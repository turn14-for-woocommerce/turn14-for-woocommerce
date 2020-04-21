<?php
/**
 * Admin Settings Class initializes settings tab 
 * 
 * @author Sam Hall https://github.com/hallsamuel90
 */

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Class Admin Settings
 */
class Admin_Settings
{
    public static $_instance;

    /**
     * Static instance
     */
    public static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Default Constructor
     */
    public function __construct()
    {
    }

    /**
     * Render the view
     */
    public static function view()
    {
        self::instance()->render_settings();
    }

    /**
     * Render the settings
     */
    public function render_settings()
    {
        self::instance()->add_settings();
        Dashboard_View_Config::load_template('header', array('tabs' => Dashboard_View_Config::dashboard_tabs()));
        Dashboard_View_Config::load_template('api-settings-group', Dashboard_Settings::dashboard_settings_group());
        Dashboard_View_Config::load_template('footer');
    }

    /**
     * Registers settings
     */
    public static function register_settings()
    {
        $options = Dashboard_Settings::dashboard_settings_group();
        register_setting('turn14_for_woocommerce', 'turn14_settings', array(
            'sanitize_callback' => array(self::instance(), 'validation_callback'))
        );
    }

    /**
     * Setup settings form
     */
    public function add_settings()
    {
        add_settings_section(
            'turn14_api_credentials', // ID
            __('API Credentials', 'wordpress'), // Title
            array( $this, 'empty_callback' ), // Callback
            'turn14_for_woocommerce' // Page
        );
        
        foreach (Dashboard_Settings::dashboard_settings_group() as $key=>$value) {
            $args = array('key'=>$key, 'value'=>$value);
            add_settings_field(
                $key, // ID
                __($value, 'wordpress'), // Title
                array( $this, 'input_field_callback' ), // Callback
                'turn14_for_woocommerce', // Page
                'turn14_api_credentials', // Section
                $args
            );
        }
    }

    /**
     * Empty callback
     */
    public function empty_callback()
    {
        echo '';
    }

    /**
     * Input callback 
     * 
     * @param array args
     */
    public function input_field_callback($input)
    {
        $options = get_option('turn14_settings');
        $field = $input['key'];
        if ($options != ""){
            if (array_key_exists($field, $options)){
                ?>
                <input type='text' name='turn14_settings[<?php echo $field ?>]' value='<?php echo $options[$field]; ?>'>
                <?php
            } else{
                ?>
                <input type='text' name='turn14_settings[<?php echo $field ?>]' value=''>
                <?php
            }
        } else{
            ?>
            <input type='text' name='turn14_settings[<?php echo $field ?>]' value=''>
            <?php
        }
    }

    /**
     * Validation callback checks to see if api keys are valid. If keys are valid, the site is
     * registered with the backend service and finally saved.
     * 
     * @param array $input
     */
    public function validation_callback($input)
    {
        $turn14_client = new Turn14_Rest_Client();
        $turn14_valid = $turn14_client->verify($input['turn14_api_client_id'], $input['turn14_api_secret']);
        if (!$turn14_valid){
            add_settings_error(
                'turn14_settings_error',
                esc_attr('settings_updated'),
                '🔥 Invalid Turn14 Client ID and/or Client Secret'
            );
            return;
        }
        $wc_client = new WC_Client();
        $wc_valid = $wc_client->verify($input['wc_api_client_id'], $input['wc_api_secret']);
        if (!$wc_valid){
            add_settings_error(
                'turn14_settings_error',
                esc_attr('settings_updated'),
                '🔥 Invalid WC Client ID and/or Client Secret'
            );
            return;
        }
        // send to users service for registration
        $url = home_url();
        $email = get_bloginfo('admin_email');

        return $input;
    }

}
