<?php
if (! defined('ABSPATH')) {
    exit;
}

class Turn14_Rest_Client
{
    public static $_instance;

    const API_CLIENT = 'turn14_api_client_id';
    const API_SECRET = 'turn14_api_secret';
    const BASE_URL = 'https://apitest.turn14.com';
    const TOKEN_RESOURCE = '/v1/token';

    private $token;

    public static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function __construct()
    {
        $this->authenticate();
    }
    
    public function authenticate()
    {
        $api_client = get_option(self::API_CLIENT);
        $api_secret = get_option(self::API_SECRET);
        
        $pload = array(
            'grant_type'=>'client_credentials',
            'client_id'=>$api_client,
            'client_secret'=>$api_secret
        );

        $response_body = wp_remote_retrieve_body(
            wp_remote_post(
                self::BASE_URL . self::TOKEN_RESOURCE,
                array(
                    'body' => $pload
                )
            )
        );

        $response_body = json_decode( $response_body, true);
        $this->$token = $response_body['access_token'];
    }
}
