<?php
/**
 * 
 */
if (! defined('ABSPATH')) {
    exit;
}

/**
 * 
 */
class Dashboard_View_Config
{
    /**
     * 
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
     * 
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
     * 
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
