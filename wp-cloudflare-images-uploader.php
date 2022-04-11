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

// Check that the file is not accessed directly.
if (!defined('ABSPATH')) {
	die('We\'re sorry, but you can not directly access this file.');
}

// Set the plugin file.
define('WP_CLOUDFLARE_IMAGES_UPLOADER_CHECK_PLUGIN_FILE', __FILE__);

// Include class-files used by our plugin.
require(dirname(__FILE__) . '/includes/class-wp-cloudflare-images-uploader.php');

// Initialize our plugin.
new WP_Cloudflare_Images_Uploader();
