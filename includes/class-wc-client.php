<?php
/**
 * WC Client Class
 *
 * @author Sam Hall https://github.com/hallsamuel90
 */

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Class WC Client for communication with WooCommerce API
 */
class WC_Client
{
    private $base_url;
    const COUPONS_RESOURCE = '/wp-json/wc/v3/coupons';

    /**
     * 
     */
    public function __construct()
    {
        $this->base_url = home_url();
    }

    /**
     * Verifies credentials
     *
     * @param string client id
     * @param string client secret
     *
     * @return boolean true if valid
     */
    public function verify($client_id, $client_secret)
    {
        $auth_header = array(
            'Authorization'=> 'Basic ' . base64_encode($client_id . ':' . $client_secret)
        );

        $response = wp_remote_get(
            $this->base_url . self::COUPONS_RESOURCE,
            array(
                'headers' => $auth_header,
                'timeout' => 10
            )
        );

        $response_code = $response['response']['code'];
        if ($response_code == 200) {
           return true;
        }
        return false;
    }
}