<?php
if (!defined('ABSPATH')) {
    exit;
}
/**
 * Turn14 Admin panel
 */
class Turn14_Admin
{
    const PAGE_TITLE = 'Turn14 Dashboard';
    const PANEL_TITLE = 'Turn14';
    const SLUG = 'turn14-dashboard';

    public function __construct()
    {
        $this->register_admin();
    }
    
    /**
     * Register Admin
     */
    public function register_admin()
    {
        add_action('admin_menu', array($this, 'register_admin_menu_page'));
        add_action('admin_init', array($this, 'register_settings'));
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
}
