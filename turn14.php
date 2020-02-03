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

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! defined( 'PF_PLUGIN_FILE' ) ) {
    define( 'PF_PLUGIN_FILE', __FILE__ );
}

class Turn14_Base {

    const VERSION = '2.1.9';
	

    /**
     * Construct the plugin.
     */
    public function __construct() {
    
    }

    /**
     * Initialize the plugin.
     */
    public function init() {

       
    }

    
}

new Turn14_Base();    //let's go