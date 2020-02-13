<?php
if (!defined('ABSPATH')) {
    exit;
}
/**
 * Turn14 Admin panel
 */
class Admin
{
    const PAGE_TITLE = 'Turn14 Dashboard';
    const PANEL_TITLE = 'Turn14';
    const SLUG = 'turn14-dashboard';

    private $controller;

    public function __construct()
    {
        $this->$controller = new Admin_Controller();
        $this->register_admin();
    }
    
    /**
     * Register Admin
     */
    public function register_admin()
    {
        add_action('admin_menu', array($this, 'register_admin_menu_page'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_enqueue_scripts', array( $this, 'register_scripts' ));
    }

    /**
     * Register Settings
     */
    public function register_settings()
    {
        $options = Dashboard_Settings::dashboard_settings_group();
        foreach ($options as $key=>$value) {
            add_option($key, '');
            register_setting('turn14_settings', $key, 'sanitize');
        }
    }

    /**
     * Add panel to side bar
     */
    public function register_admin_menu_page()
    {
        add_menu_page(self::PAGE_TITLE, self::PANEL_TITLE, 'manage_options', self::SLUG, array($this, 'dashboard_view'));
    }

    public function register_scripts($hook)
    {
        if (strpos($hook, 'turn14-dashboard') !== false) {
            wp_enqueue_script('admin-dashboard', plugins_url('../assets/js/admin-dashboard.js', __FILE__));
            wp_localize_script('admin-dashboard', 'admin_dashboard', array( 'ajax' => admin_url('admin-ajax.php')));
        }
    }

    /**
     * Layout Dashboard tabs
     */
    public static function dashboard_view()
    {
        $tabs = array(
            'dashboard' => 'Admin_Dashboard',
            'settings' => 'Admin_Settings'
        );

        $tab = (!empty($_GET['tab']) ? $_GET['tab'] : 'dashboard');
        if (!empty($tabs[$tab])) {
            call_user_func(array($tabs[$tab], 'view'));
        }
    }
}
