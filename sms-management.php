<?php
/*
Plugin Name: Woocommerce SMS Management via cron job
Plugin URI:  http://webloungeonlinebd.com
Description: This plugin manages sms. You need to set up a cron job for table cron_sms_archive
Version:     0.1.0
Author:      Mohsin
Author URI:  http://webloungeonlinebd.com
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

defined( 'ABSPATH' ) or die( 'Hush! Stay away please!' );

/**
 *
 * Load sms Setting Under woocommerce Menu
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-settings.php';

/**
 *
 * Send Sms when woocommerce order status Change
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-sms.php';

/**
 *
 * check woocommerce plugin available or not
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/check-woocommerce.php';
