<?php
if (! defined('ABSPATH')) {
    exit;
}

class Turn14_Settings
{
    public static $_instance;
    public static $dashboard_settings;

    public static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public static function dashboard_settings()
    {
        if (is_null(self::$dashboard_settings)) {
            self::$dashboard_settings = array(
                'turn14_api_client_id'=>'Client ID',
                'turn14_api_secret'=>'Client Secret'
                );
        }
        return self::$dashboard_settings;
    }
}
