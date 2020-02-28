<?php
/**
 * Dashboard Settings Class settings group for plugin
 * 
 * @author Sam Hall https://github.com/hallsamuel90
 */

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Class Dashboard Settings
 */
class Dashboard_Settings
{
    /**
     * Plugin settings group
     */
    public static function dashboard_settings_group()
    {
        return array(
            'turn14_api_client_id'=>'Client ID',
            'turn14_api_secret'=>'Client Secret'
        );
    }
}
