<?php
if (! defined('ABSPATH')) {
    exit;
}

class Turn14_Dashboard
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

    public function register_admin_menu_page()
    {
        add_menu_page(self::PAGE_TITLE, self::PANEL_TITLE, 'manage_options', self::SLUG, array($this, 'dashboard_view'));
    }

    public function dashboard_view()
    {
        echo "<h1>Hello World</h1>";
    }
}
