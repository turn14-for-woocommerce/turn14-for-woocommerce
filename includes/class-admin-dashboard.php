<?php
/**
 * Admin Dashboard Class initializes dashboard tab 
 * 
 * @author Sam Hall https://github.com/hallsamuel90
 */

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Class Admin Dashboard
 */
class Admin_Dashboard
{
    public static $_instance;

    /**
     * Static instance
     */
    public static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * Default Constructor
     */
    public function __construct()
    {
        
    }

    /**
     * Render the view
     */
    public static function view()
    {
        self::instance()->render_dashboard();
    }

    /**
     * Render the dashboard
     */
    private function render_dashboard()
    {
        Dashboard_View_Config::load_template('header', array('tabs' => Dashboard_View_Config::dashboard_tabs()));
        // Dashboard_View_Config::load_template('import-all-products');
        // Dashboard_View_Config::load_template('brands-table');
        $brands_table = new Brands_Table();
        $brands_table->display();
        Dashboard_View_Config::load_template('footer');
    }
}
