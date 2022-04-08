<?php
/**
 * Plugin Name: WP Cloudflare Images Uploader
 * Plugin URI: https://github.com/softlibrary/wc-subscription
 * Description: Upload your media direct to Cloudflare Images
 * Version: 0.1.0
 * Requires at least: 5.0
 * Requires PHP: 8.0
 * Tested up to: 8.1
 * Author: softlibrary
 * Author URI: https://softlibrary.org
 * Text Domain: wp-subscription
 * Copyright 2021  softlibrary  (email: contact@softlibrary.org)
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

// Disable direct view to file (include online)
if (!defined('ABSPATH')) {
  die('Do not open this file directly.');
}

register_activation_hook(__FILE__, 'activate');

function activate()
{
  add_option('wp_cloudflare_images_uploader_enable', false);
  add_option('wp_cloudflare_images_uploader_account_id', false);
  add_option('wp_cloudflare_images_uploader_token', false);
}

class WP_CLOUDFLARE_IMAGES_UPLOADER
{

  public function __construct()
  {
    add_action('admin_init', array($this, 'admin_init'));
    add_filter('wp_handle_upload', array($this, 'wp_handle_upload'), 'upload');
  }

  public function wp_handle_upload($file)
  {
    if (get_option('wp_cloudflare_images_uploader_enable'))
    {

      $ch = curl_init('https://api.cloudflare.com/client/v4/accounts/' . get_option('wp_cloudflare_images_uploader_account_id') . '/images/v2/direct_upload');

      curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Bearer ' . get_option('wp_cloudflare_images_uploader_token')));
      curl_setopt($ch, CURLOPT_POSTFIELDS, array(
        'requireSignedURLs' => true,
        'metadata' => '{"key":"value"}'
      ));

      $result = curl_exec($ch);
      return $result;
      curl_close($ch);
    }
    
    return $file;
  }

  public static function init()
  {
    new self();
  }

  public function admin_init()
  {

    register_setting('media', 'wp_cloudflare_images_uploader_enable', array(
      'type' => 'boolean'
    ));
    register_setting('media', 'wp_cloudflare_images_uploader_account_id', array(
      'type' => 'string'
    ));
    register_setting('media', 'wp_cloudflare_images_uploader_token', array(
      'type' => 'string'
    ));

    add_settings_section(
      'wp_cloudflare_images_uploader_setting_section',
      'Connect to Cloudflare pages',
      'wp_cloudflare_images_uploader_setting_section_callback_function',
      'media'
    );
    function wp_cloudflare_images_uploader_setting_section_callback_function( $arg )
    {
      $option = get_option('wp_cloudflare_images_uploader_enable', '');
      ?>

      <label for="<?php $arg['id'] ?>'_enable">
      <input id="<?php $arg['id'] ?>'_enable" name="wp_cloudflare_images_uploader_enable" type="checkbox" value="1" <?php checked($option); ?> /> Enable Cloudflare Images Uploader
      </label>
      <?php
    }

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
      'Token',
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
  
}

WP_CLOUDFLARE_IMAGES_UPLOADER::init();