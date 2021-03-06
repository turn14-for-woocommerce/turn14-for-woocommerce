<?php
/**
 * Turn14 Client Class
 *
 * @author Sam Hall https://github.com/hallsamuel90
 */

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Class Turn14 Client for communication with Turn14 API
 */
class Turn14_Client
{
    const API_CLIENT = 'turn14_api_client_id';
    const API_SECRET = 'turn14_api_secret';
    const BASE_URL = 'https://apitest.turn14.com';
    const TOKEN_RESOURCE = '/v1/token';
    const ITEMS_RESOURCE = '/v1/items?page=';
    const UPDATED_ITEMS_RESOURCE = '/v1/items/updates?page=';
    const MEDIA_RESOURCE = '/v1/items/data?page=';
    const ITEM_MEDIA_RESOURCE = '/v1/items/data/';
    const PRICING_RESOURCE = '/v1/pricing?page=';
    const ITEM_PRICING_RESOURCE = '/v1/pricing/';
    const INVENTORY_RESOURCE = '/v1/inventory?page=';
    const ITEM_INVENTORY_RESOURCE = '/v1/inventory/';

    private $token;

    /**
     * Default Constuctor
     */
    public function __construct()
    {
        add_filter('https_local_ssl_verify', '__return_false');
        add_filter('https_ssl_verify', '__return_false');
        $this->health_check();
    }

    /**
     * Fetches Turn14 items from the API
     *
     * @param int page number to be fetched
     *
     * @return array of fetched items (products)
     */
    public function get_items($page_number)
    {
        $auth_header = array(
            'Authorization'=> 'Bearer ' . $this->token
        );
        $url = self::BASE_URL . self::ITEMS_RESOURCE . $page_number;
        $response = wp_remote_get(
            self::BASE_URL . self::ITEMS_RESOURCE . $page_number,
            array(
                'timeout' => 10,
                'headers' => $auth_header
            )
        );

        $response_code = $response['response']['code'];
        if ($response_code == 200) {
            $response_body = wp_remote_retrieve_body($response);
            return json_decode($response_body, true);
        } elseif ($response_code == 401) {
            $this->authenticate();
            $this->get_items($page_number);
        } elseif ($response_code == 429) {
            sleep(1);
            $this->get_items($page_number);
        } else {
            error_log('We are having issues retrieving products from the Turn14 API. Responded with ' . $response_code);
            return null;
        }
    }

    /**
    * Fetches daily updated Turn14 items from the API
    *
    * @param int page number to be fetched
    *
    * @return array of fetched items (products)
    */
    public function get_updated_items($page_number)
    {
        $auth_header = array(
            'Authorization'=> 'Bearer ' . $this->token
        );
        $url = self::BASE_URL . self::UPDATED_ITEMS_RESOURCE . $page_number .'&days=1';
        $response = wp_remote_get(
            $url,
            array(
                'timeout' => 10,
                'headers' => $auth_header
            )
        );

        $response_code = $response['response']['code'];
        if ($response_code == 200) {
            $response_body = wp_remote_retrieve_body($response);
            return json_decode($response_body, true);
        } elseif ($response_code == 401) {
            $this->authenticate();
            $this->get_updated_items($page_number);
        } elseif ($response_code == 429) {
            sleep(1);
            $this->get_updated_items($page_number);
        } else {
            error_log('We are having issues retrieving products from the Turn14 API. Responded with ' . $response_code);
            return null;
        }
    }

    /**
     * Fetches Turn14 media from the API
     *
     * @param int page number to be fetched
     *
     * @return array of fetched media
     */
    public function get_media($page_number)
    {
        $auth_header = array(
            'Authorization'=> 'Bearer ' . $this->token
        );

        $response = wp_remote_get(
            self::BASE_URL . self::MEDIA_RESOURCE . $page_number,
            array(
                'headers' => $auth_header,
                'timeout' => 10
            )
        );

        $response_code = $response['response']['code'];
        if ($response_code == 200) {
            $response_body = wp_remote_retrieve_body($response);
            return json_decode($response_body, true);
        } elseif ($response_code == 401) {
            $this->authenticate();
            $this->get_media($page_number);
        } elseif ($response_code == 429) {
            sleep(1);
            $this->get_media($page_number);
        } else {
            error_log('We are having issues retrieving media from the Turn14 API. Responded with ' . $response_code);
            return null;
        }
    }

    /**
     * Fetches Turn14 media from the API for particular item
     *
     * @param int id of item associated to media
     *
     * @return array of fetched media
     */
    public function get_item_media($item_id)
    {
        $auth_header = array(
            'Authorization'=> 'Bearer ' . $this->token
        );

        $response = wp_remote_get(
            self::BASE_URL . self::ITEM_MEDIA_RESOURCE . $item_id,
            array(
                'headers' => $auth_header,
                'timeout' => 10
            )
        );

        $response_code = $response['response']['code'];
        if ($response_code == 200) {
            $response_body = json_decode(wp_remote_retrieve_body($response), true);
            return $response_body['data'];
        } elseif ($response_code == 401) {
            $this->authenticate();
            $this->get_item_media($item_id);
        } elseif ($response_code == 429) {
            sleep(1);
            $this->get_item_media($item_id);
        } else {
            error_log('We are having issues retrieving products from the Turn14 API. Responded with ' . $response_code);
            return null;
        }
    }

    /**
     * Fetches Turn14 pricing from the API
     *
     * @param int page number to be fetched
     *
     * @return array of fetched pricing
     */
    public function get_pricing($page_number)
    {
        $auth_header = array(
            'Authorization'=> 'Bearer ' . $this->token
        );

        $response = wp_remote_get(
            self::BASE_URL . self::PRICING_RESOURCE . $page_number,
            array(
                'headers' => $auth_header,
                'timeout' => 10
            )
        );

        $response_code = $response['response']['code'];
        if ($response_code == 200) {
            $response_body = wp_remote_retrieve_body($response);
            return json_decode($response_body, true);
        } elseif ($response_code == 401) {
            $this->authenticate();
            $this->get_pricing($page_number);
        } elseif ($response_code == 429) {
            sleep(1);
            $this->get_pricing($page_number);
        } else {
            error_log('We are having issues retrieving pricings from the Turn14 API. Responded with ' . $response_code);
            return null;
        }
    }

    /**
     * Fetches Turn14 pricing from the API for particular item
     *
     * @param int id of item associated to pricing
     *
     * @return array of fetched item pricing
     */
    public function get_item_pricing($item_id)
    {
        $auth_header = array(
            'Authorization'=> 'Bearer ' . $this->token
        );

        $response = wp_remote_get(
            self::BASE_URL . self::ITEM_PRICING_RESOURCE . $item_id,
            array(
                'headers' => $auth_header,
                'timeout' => 10
            )
        );

        $response_code = $response['response']['code'];
        if ($response_code == 200) {
            $response_body = json_decode(wp_remote_retrieve_body($response), true);
            return $response_body['data'];
        } elseif ($response_code == 401) {
            $this->authenticate();
            $this->get_item_pricing($item_id);
        } elseif ($response_code == 429) {
            sleep(1);
            $this->get_item_pricing($item_id);
        } else {
            error_log('We are having issues retrieving item pricing from the Turn14 API. Responded with ' . $response_code);
            return null;
        }
    }

    /**
     * Fetches Turn14 inventory from the API
     *
     * @param int page number to be fetched
     *
     * @return array of fetched inventory
     */
    public function get_inventory($page_number)
    {
        $auth_header = array(
            'Authorization'=> 'Bearer ' . $this->token
        );

        $response = wp_remote_get(
            self::BASE_URL . self::INVENTORY_RESOURCE . $page_number,
            array(
                'headers' => $auth_header,
                'timeout' => 10
            )
        );

        $response_code = $response['response']['code'];
        if ($response_code == 200) {
            $response_body = wp_remote_retrieve_body($response);
            return json_decode($response_body, true);
        } elseif ($response_code == 401) {
            $this->authenticate();
            $this->get_inventory($page_number);
        } elseif ($response_code == 429) {
            sleep(1);
            $this->get_inventory($page_number);
        } else {
            error_log('We are having issues retrieving inventory from the Turn14 API. Responded with ' . $response_code);
            return null;
        }
    }

    /**
     * Fetches Turn14 inventory from the API for particular item
     *
     * @param int id of item associated to inventory
     *
     * @return array of fetched inventory
     */
    public function get_item_inventory($item_id)
    {
        $auth_header = array(
            'Authorization'=> 'Bearer ' . $this->token
        );

        $response = wp_remote_get(
            self::BASE_URL . self::ITEM_INVENTORY_RESOURCE . $item_id,
            array(
                'headers' => $auth_header,
                'timeout' => 10
            )
        );

        $response_code = $response['response']['code'];
        if ($response_code == 200) {
            $response_body = json_decode(wp_remote_retrieve_body($response), true);
            return $response_body['data'];
        } elseif ($response_code == 401) {
            $this->authenticate();
            $this->get_item_inventory($item_id);
        } elseif ($response_code == 429) {
            sleep(1);
            $this->get_item_inventory($item_id);
        } else {
            error_log('We are having issues retrieving item inventory from the Turn14 API. Responded with ' . $response_code);
            return null;
        }
    }

    /**
     * Authenticates against the Turn14 API based on credentials entered in the Settings tab
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

        $response_body = json_decode($response_body, true);
        // cache for future use and set timeout
        set_transient('turn14_api_token', $response_body['access_token'], 60*60);
        $this->token = get_transient('turn14_api_token');
    }

    /**
     * Checks to see if token has expired and reauthenticates if need be
     */
    private function health_check()
    {
        $turn14_api_token = get_transient('turn14_api_token');
        if ($turn14_api_token != null) {
            $this->token = get_transient('turn14_api_token');
        } else {
            $this->authenticate();
        }
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
        $pload = array(
            'grant_type'=>'client_credentials',
            'client_id'=>$client_id,
            'client_secret'=>$client_secret
        );

        $response = wp_remote_post(
            self::BASE_URL . self::TOKEN_RESOURCE,
            array(
                'body' => $pload
            )
        );

        $response_code = $response['response']['code'];
        if ($response_code == 200) {
           return true;
        }
        return false;
    }
}
