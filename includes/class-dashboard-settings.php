<?php
if (! defined('ABSPATH')) {
    exit;
}

/**
 * 
 */
class Dashboard_Settings
{
    /**
     * 
     */
    public static function dashboard_settings_group()
    {
        return array(
            'turn14_api_client_id'=>'Client ID',
            'turn14_api_secret'=>'Client Secret'
        );
    }
}
