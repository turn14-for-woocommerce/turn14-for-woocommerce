<?php
if (! defined('ABSPATH')) {
    exit;
}

/**
 * 
 */
class Turn14_Admin_Dashboard
{
    public static $_instance;

    public static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function __construct()
    {
        
    }

    /**
     * 
     */
    public static function view()
    {
        self::instance()->render_dashboard();
    }

    /**
     * 
     */
    private function render_dashboard()
    {
        Dashboard_View_Config::load_template('header', array('tabs' => Dashboard_View_Config::dashboard_tabs()));
        Dashboard_View_Config::load_template('import-all-products');
        Dashboard_View_Config::load_template('footer');
    }
}
