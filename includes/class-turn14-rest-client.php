<?php
if (! defined('ABSPATH')) {
    exit;
}

/**
 * 
 */
class Turn14_Rest_Client
{
    public static $_instance;

    const API_CLIENT = 'turn14_api_client_id';
    const API_SECRET = 'turn14_api_secret';
    const BASE_URL = 'https://apitest.turn14.com';
    const TOKEN_RESOURCE = '/v1/token';
    const ITEMS_RESOURCE = '/v1/items?page=';
    const MEDIA_RESOURCE = '/v1/items/data?page=';
    const ITEM_MEDIA_RESOURCE = '/v1/items/data/';
    const PRICING_RESOURCE = '/v1/pricing?page=';
    const ITEM_PRICING_RESOURCE = '/v1/pricing/';
    const INVENTORY_RESOURCE = '/v1/inventory?page=';
    const ITEM_INVENTORY_RESOURCE = '/v1/inventory/';

    private $token;

    public function __construct()
    {
        add_filter( 'https_local_ssl_verify', '__return_false' );
        add_filter( 'https_ssl_verify', '__return_false' );
        $this->health_check();
    }

    /**
     * 
     */
    public function get_items($page_number)
    {
        $auth_header = array(
            'Authorization'=> 'Bearer ' . $this->token
        );
        $url = self::BASE_URL . self::ITEMS_RESOURCE . $page_number;
        $response_body = wp_remote_retrieve_body(
            wp_remote_get(
                self::BASE_URL . self::ITEMS_RESOURCE . $page_number,
                array(
                    'headers' => $auth_header,
                    'timeout' => 10
                )
            )
        );

        $response_body = json_decode( $response_body, true);
        return $response_body['data'];
    }

    /**
     * 
     */
    public function get_media($page_number)
    {
        $auth_header = array(
            'Authorization'=> 'Bearer ' . $this->token
        );

        $response_body = wp_remote_retrieve_body(
            wp_remote_get(
                self::BASE_URL . self::MEDIA_RESOURCE . $page_number,
                array(
                    'headers' => $auth_header,
                    'timeout' => 10
                )
            )
        );

        $response_body = json_decode( $response_body, true);
        return $response_body['data'];
    }

    /**
     * 
     */
    public function get_item_media($item_id)
    {
        $auth_header = array(
            'Authorization'=> 'Bearer ' . $this->token
        );

        $response_body = wp_remote_retrieve_body(
            wp_remote_get(
                self::BASE_URL . self::ITEM_MEDIA_RESOURCE . $item_id,
                array(
                    'headers' => $auth_header,
                    'timeout' => 10
                )
            )
        );

        $response_body = json_decode( $response_body, true);
        return $response_body['data'];
    }

    /**
     * 
     */
    public function get_pricing($page_number)
    {
        $auth_header = array(
            'Authorization'=> 'Bearer ' . $this->token
        );

        $response_body = wp_remote_retrieve_body(
            wp_remote_get(
                self::BASE_URL . self::PRICING_RESOURCE . $page_number,
                array(
                    'headers' => $auth_header,
                    'timeout' => 10
                )
            )
        );

        $response_body = json_decode( $response_body, true);
        return $response_body['data'];
    }

    /**
     * 
     */
    public function get_item_pricing($item_id)
    {
        $auth_header = array(
            'Authorization'=> 'Bearer ' . $this->token
        );

        $response_body = wp_remote_retrieve_body(
            wp_remote_get(
                self::BASE_URL . self::ITEM_PRICING_RESOURCE . $item_id,
                array(
                    'headers' => $auth_header,
                    'timeout' => 10
                )
            )
        );

        $response_body = json_decode( $response_body, true);
        return $response_body['data'];
    }

    /**
     * 
     */
    public function get_inventory($page_number)
    {
        $auth_header = array(
            'Authorization'=> 'Bearer ' . $this->token
        );

        $response_body = wp_remote_retrieve_body(
            wp_remote_get(
                self::BASE_URL . self::INVENTORY_RESOURCE . $page_number,
                array(
                    'headers' => $auth_header,
                    'timeout' => 10
                )
            )
        );

        $response_body = json_decode( $response_body, true);
        return $response_body['data'];
    }

    /**
     * 
     */
    public function get_item_inventory($item_id)
    {
        $auth_header = array(
            'Authorization'=> 'Bearer ' . $this->token
        );

        $response_body = wp_remote_retrieve_body(
            wp_remote_get(
                self::BASE_URL . self::ITEM_INVENTORY_RESOURCE . $item_id,
                array(
                    'headers' => $auth_header,
                    'timeout' => 10
                )
            )
        );

        $response_body = json_decode( $response_body, true);
        return $response_body['data'];
    }

    /**
     * 
     */
    private function authenticate()
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
        // cache for future use and set timeout
        set_transient('turn14_api_token', $response_body['access_token'], 60*60);
    }

    /**
     * 
     */
    private function health_check(){
        
        $turn14_api_token = get_transient('turn14_api_token');

        if ($turn14_api_token != null){
            $this->token = get_transient('turn14_api_token');
        }
        else{
            $this->authenticate();
            $this->token = get_transient('turn14_api_token');
        }
    }
}
