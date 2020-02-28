<?php
/**
 * Dashboard View Config configures the dashboard admin panel 
 * 
 * @author Sam Hall https://github.com/hallsamuel90
 */

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Class Dashboard View Config
 */
class Dashboard_View_Config
{
    /**
     * Configures the dashboard admin tabs
     */
    public static function dashboard_tabs()
    {
        $tabs = array(
            array('name' => __('Dashboard', 'turn14'), 'tab_url' => 'dashboard'),
            array('name' => __('Settings', 'turn14'), 'tab_url' => 'settings')
        );

        return $tabs;
    }
    
    /**
     * Loads HTML templates
     * 
     * @param string name of template to load
     * @param array args
     */
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

    /**
     * Sanitizes input
     * 
     * @param string input to be sanatized
     */
    public function sanitize($input)
    {
        $new_input = null;
        if (isset($input)) {
            $new_input = sanitize_text_field($input);
        }

        return $new_input;
    }
}
