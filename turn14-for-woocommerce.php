<?php
/**
*Plugin Name: Turn14 Integration for WooCommerce
*Plugin URI:
*Description: Integrates the Turn14 API with WooCommerce
*Version: 0.1.0
*Author: Sam Hall
*Author URI:
*License:
*Text Domain: turn14-for-woocommerce
*WC requires at least: 3.0.0
*WC tested up to: 3.9
*/

if (! defined('ABSPATH')) {
    exit;
}

if (! defined('PF_PLUGIN_FILE')) {
    define('PF_PLUGIN_FILE', __FILE__);
}

class Turn14_For_WooCommerce
{
    const VERSION = '0.1.0';
    

    /**
     * Construct the plugin.
     */
    public function __construct()
    {
        add_action('plugins_loaded', array( $this, 'init' ));
    }

    /**
     * Initialize the plugin.
     */
    public function init()
    {

        // if (!class_exists('WC_Integration')) {
        //     return;
        // }

        //load required classes
        require_once 'includes/class-turn14-admin.php';
        require_once 'includes/class-turn14-admin-dashboard.php';
        require_once 'includes/class-turn14-admin-settings.php';
        require_once 'includes/class-turn14-settings.php';
        require_once 'includes/class-turn14-rest-client.php';
        
        Turn14_Admin::init();
    }
}

new Turn14_For_WooCommerce();    //let's go
