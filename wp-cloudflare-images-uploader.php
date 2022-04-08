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
    
  }

  public static function init()
  {
    new self();
  }

  public function admin_init()
  {
    add_settings_section(
      'wp_cloudflare_images_uploader_setting_section',
      'Connect to Cloudflare pages',
      'wp_cloudflare_images_uploader_setting_section_callback_function',
      'media'
    );
    function wp_cloudflare_images_uploader_setting_section_callback_function( $arg )
    {
      echo '<label for="' . $arg['id'] .'_enable">';
      echo '<input id="' . $arg['id'] .'_enable" type="checkbox" value="" /> Enable Cloudflare Images Uploader';
      echo '</label>';
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
      echo '<input style="width: 350px;" type="text" />';
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
      echo '<input style="width: 350px;" type="password" />';
    }
  }
  
}

WP_CLOUDFLARE_IMAGES_UPLOADER::init();