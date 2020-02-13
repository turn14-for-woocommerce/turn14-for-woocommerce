<?php
if (! defined('ABSPATH')) {
    exit;
}

/**
 *
 */
class Admin_Controller
{
    public function __construct()
    {
        add_action( 'wp_ajax_import_all_products', array($this, 'import_all_products' )); 
        add_action( 'wp_ajax_nopriv_import_all_products', array($this, 'import_all_products' ));
    }

    /**
     *
     */
    public function import_all_products()
    {
        wp_send_json_success(array(
            'test'=>'TESTING'
        ));
    }
}
