<?php
if (! defined('ABSPATH')) {
    exit;
}

/**
 * 
 */
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

    /**
     * 
     */
    public static function view()
    {
        self::instance()->render_settings();
    }

    /**
     * 
     */
    public function render_settings()
    {
        self::instance()->add_settings();
        Dashboard_View_Config::load_template('header', array('tabs' => Dashboard_View_Config::dashboard_tabs()));
        Dashboard_View_Config::load_template('api-settings-group', Dashboard_Settings::dashboard_settings_group());
        Dashboard_View_Config::load_template('footer');
    }

    /**
     * 
     */
    public function add_settings()
    {
        add_settings_section(
            'turn14_api_credentials', // ID
            'API Credentials', // Title
            array( $this, 'empty_callback' ), // Callback
            'turn14_dashboard' // Page
        );
        
        foreach (Dashboard_Settings::dashboard_settings_group() as $key=>$value) {
            $args = array('key'=>$key, 'value'=>$value);
            add_settings_field(
                $key, // ID
                $value, // Title
                array( $this, 'input_field_callback' ), // Callback
                'turn14_dashboard', // Page
                'turn14_api_credentials', // Section
                $args
            );
        }
    }

    /**
     * 
     */
    public function empty_callback()
    {
        echo '';
    }

    /**
     * 
     */
    public function input_field_callback($args)
    {
        $value = get_option($args['key']);
        echo '<input type="text" name="' . $args['key'] . '" id="' . $args['key'] . '" value="' . $value . '" >';
    }
}
