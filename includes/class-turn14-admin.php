<?php
if (!defined('ABSPATH')) {
    exit;
}

class Turn14_Admin
{
    const PAGE_TITLE = 'Turn14 Dashboard';
    const PANEL_TITLE = 'Turn14';
    const SLUG = 'turn14-dashboard';

    public static function init()
    {
        $admin = new self;
        $admin->register_admin();
    }
    
    /**
     * Register Admin
     */
    public function register_admin()
    {
        add_action('admin_menu', array($this, 'register_admin_menu_page'));
    }

    /**
     * Add panel to side bar
     */
    public function register_admin_menu_page()
    {
        add_menu_page(self::PAGE_TITLE, self::PANEL_TITLE, 'manage_options', self::SLUG, array($this, 'dashboard_view'));
    }

    /**
     * Layout Dashboard tabs
     */
    public static function dashboard_view()
    {
        $tabs = array(
            'dashboard' => 'Turn14_Admin_Dashboard',
            'settings' => 'Turn14_Admin_Settings'
        );

        $tab = (!empty($_GET['tab']) ? $_GET['tab'] : 'dashboard');
        if (!empty($tabs[$tab])) {
            call_user_func(array($tabs[$tab], 'view'));
        }
    }

    public static function get_tabs()
    {
        $tabs = array(
            array('name' => __('Dashboard', 'turn14'), 'tab_url' => 'dashboard'),
            array('name' => __('Settings', 'turn14'), 'tab_url' => 'settings')
        );

        return $tabs;
    }

    public static function load_template($name, $variables = array())
    {
        if (!empty($variables)) {
            extract($variables);
        }

        $filename = plugin_dir_path(__FILE__) . 'templates/' . $name . '.php';
        if (file_exists($filename)) {
            include($filename);
        }
    }
}
