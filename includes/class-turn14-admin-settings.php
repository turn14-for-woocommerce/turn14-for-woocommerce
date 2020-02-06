<?php
if (! defined('ABSPATH')) {
    exit;
}

class Turn14_Admin_Settings
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

    public static function view()
    {
        self::instance()->render_settings();
    }

    public function render_settings()
    {
        Turn14_Admin::load_template('header', array('tabs' => Turn14_Admin::get_tabs()));
        Turn14_Admin::load_template('api-settings-group');
        Turn14_Admin::load_template('footer');
    }
}