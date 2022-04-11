<?php

// Check that the file is not accessed directly.
if (!defined('ABSPATH')) {
  die('We\'re sorry, but you can not directly access this file.');
}

/**
 * Class WP Cloudflare Images Uploader.
 */
class WP_Cloudflare_Images_Uploader
{

  /**
   * WP Class WP Cloudflare Images Uploader constructor.
   *
   * @uses Health_Check::init()
   *
   * @return void
   */
  public function __construct()
  {
    $this->init();
  }

  /**
   * Plugin initiation.
   *
   * A helper function, called by `WP_Cloudflare_Images_Uploader::__construct()` to initiate actions, hooks and other features needed.
   *
   * @uses add_action()
   * @uses add_filter()
   *
   * @return void
   */
  private function init()
  {
    add_action('admin_init', array($this, 'admin_init'));

    add_filter('plugin_action_links_'. plugin_basename(WP_CLOUDFLARE_IMAGES_UPLOADER_CHECK_PLUGIN_FILE), array($this, 'plugin_action_link'));
    add_filter('wp_handle_upload_prefilter', array($this, 'wp_handle_upload_prefilter'), 'upload');
  }

  public function admin_init()
  {
    register_setting('media', 'wp_cloudflare_images_uploader_account_id', array(
      'type' => 'string'
    ));
    register_setting('media', 'wp_cloudflare_images_uploader_token', array(
      'type' => 'string'
    ));

    add_settings_section(
      'wp_cloudflare_images_uploader_setting_section',
      'Connect to Cloudflare Images',
      '',
      'media'
    );

    add_settings_field(
      'wp_cloudflare_images_uploader_setting_field_account_id',
      'Account ID',
      'wp_cloudflare_images_uploader_setting_field_account_id_callback_function',
      'media',
      'wp_cloudflare_images_uploader_setting_section'
    );
    function wp_cloudflare_images_uploader_setting_field_account_id_callback_function()
    {
      $option = get_option('wp_cloudflare_images_uploader_account_id', '');
?>
      <input style="width: 350px;" type="text" name="wp_cloudflare_images_uploader_account_id" value="<?php echo $option; ?>" />
    <?php
    }

    add_settings_field(
      'wp_cloudflare_images_uploader_setting_field_token',
      'Account API Token',
      'wp_cloudflare_images_uploader_setting_field_token_callback_function',
      'media',
      'wp_cloudflare_images_uploader_setting_section'
    );
    function wp_cloudflare_images_uploader_setting_field_token_callback_function()
    {
      $option = get_option('wp_cloudflare_images_uploader_token', '');
    ?>
      <input style="width: 350px;" type="password" name="wp_cloudflare_images_uploader_token" value="<?php echo $option; ?>" />
<?php
    }
  }

  /**
   * Add settings link to plugin actions
   *
   * @param  array  $plugin_actions
   * @param  string $plugin_file
   * @since  1.0
   * @return array
   */
  public function plugin_action_link($actions)
  {

    $link = '<a href="' . admin_url('/options-media.php') . '">Settings</a>';
    array_unshift($actions, $link);

    return $actions;
  }

  public function wp_handle_upload_prefilter($file)
  {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, 'https://api.cloudflare.com/client/v4/accounts/' . get_option('wp_cloudflare_images_uploader_account_id') .  '/images/v1');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    $args['file'] = new CURLFile($file['tmp_name'], $file['type'], $file['name']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $args);

    $headers = array();
    $headers[] = 'Authorization: Bearer ' . get_option('wp_cloudflare_images_uploader_token');
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($ch);

    if (curl_errno($ch)) {
      // echo 'Error:' . curl_error($ch);
      return curl_error($ch);
    }
    curl_close($ch);

    $result_decode = json_decode($result, true)['result'];

    $attachment_id = wp_insert_attachment(array(
      'guid' => $result_decode['variants'][0],
      'post_mime_type' => $file['type'],
      'post_title' => sanitize_title(pathinfo($file['name'])['filename']),
    ));

    $attachment_metadata = array(
      'width' => 200,
      'height' => 400,
      'file' => wp_basename($file['name']),
      'sizes' => $file['size']
    );
    $attachment_metadata['sizes'] = array('full' => $attachment_metadata);

    wp_update_attachment_metadata($attachment_id, $attachment_metadata);

    // return wp_send_json(array(
    //   'success' => true,
    //   'id' => $attachment_id,
    // ));
    return wp_send_json(array(
      'success' => true,
      'data' => array(
        'id' => $attachment_id,
        'title' => sanitize_title(pathinfo($file['name'])['filename']),
        'filename' => wp_basename($file['name']),
        'url' => $result_decode['variants'][0],
        'alt' => '',
        'author' => get_current_user_id(),
        'description' => '',
        'caption' => '',
        'name' => sanitize_title(pathinfo($file['name'])['filename']),
        'status' => 'inherit',
        'uploadedTo' => 0,
        'date' => time() . 000,
        'modified' => time() . 000,
        'menuOrder' => 0,
        'mime' => $file['type'],
        'type' => 'image',
        'subtype' => '',
        'icon' => '',
        'dateFormatted' => date('M d Y'),
        'editLink' => get_admin_url() . 'post.php?post=' . $attachment_id . '&action=edit',
        'meta' => false,
        'authorName' => get_userdata(get_current_user_id())->display_name,
        'authorLink' => get_author_posts_url(get_current_user_id()),
        'filesizeInBytes' => $file['size'],
        'filesizeHumanReadable' => size_format($file['size'], 2),
        'context' => '',
        'width' => 200,
        'height' => 400,
        'orientation' => 'landscape',
      )
    ));
  }
}
