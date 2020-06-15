<?php
/**
 * Service Client Class
 *
 * @author Sam Hall https://github.com/hallsamuel90
 */

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Class Service Client for communication with backend service
 */
class Service_Client
{
    const BASE_URL = 'http://206.189.190.203';
    const REGISTER_RESOURCE = '/users/register';
    const LOGIN_RESOURCE = '/users/login';
    const BRANDS_RESOURCE = '/brands/?userId=';
    const UPDATE_BRAND_RESOURCE = '/brands';

    /**
     * Default Contructor
     */
    public function __construct()
    {
    }

    /**
     * Registers the plugin with the backend service
     *
     * @param array registrationBody
     *
     * @return string userId
     */
    public function register($registrationBody)
    {
        $json_body = wp_json_encode($registrationBody);
        $response = wp_remote_post(
            self::BASE_URL . self::REGISTER_RESOURCE,
            array(
                'timeout' => 10,
                'headers'     => [
                    'Content-Type' => 'application/json',
                ],
                'body' => $json_body
            )
        );
        if (!is_wp_error($response)){
            $response_code = $response['response']['code'];
            if ($response_code == 200) {
                $response_body = wp_remote_retrieve_body($response);
                return json_decode($response_body, true)['_id'];
            }
        }
        return null;
    }

    /**
     * Login plugin with the backend service
     *
     * @param string username
     * @param string password
     *
     * @return string userId
     */
    public function login($username, $password)
    {
        $json_body = wp_json_encode(array(
            'username' => $username,
            'password' => $password
        ));
        $response = wp_remote_post(
            self::BASE_URL . self::LOGIN_RESOURCE,
            array(
                'timeout' => 10,
                'headers'     => [
                    'Content-Type' => 'application/json',
                ],
                'body' => $json_body
            )
        );
        if (!is_wp_error($response)){
            $response_code = $response['response']['code'];
            if ($response_code == 200) {
                $response_body = wp_remote_retrieve_body($response);
                return json_decode($response_body, true)['_id'];
            }
        }
        return null;
    }

    /**
     * Fetches Brand data
     *
     * @param string user_id
     *
     * @return array of fetched brands
     */
    public function get_brands($user_id)
    {
        $response = wp_remote_get(
            self::BASE_URL . self::BRANDS_RESOURCE . $user_id,
            array(
                'timeout' => 10
            )
        );
        if (!is_wp_error($response)){
            $response_code = $response['response']['code'];
            if ($response_code == 200) {
                $response_body = wp_remote_retrieve_body($response);
                return json_decode($response_body, true);
            }
        }
    }

    /**
     * Set brand active or deactive
     *
     * @param string id of brand resource
     * @param boolean true if active, false otherwise
     *
     */
    public function activate($id, $active)
    {
        $json_body = wp_json_encode(array(
            'active' => $active
        ));
        $response = wp_remote_request(
            self::BASE_URL . self::UPDATE_BRAND_RESOURCE . '/' . $id,
            array(
                'timeout' => 10,
                'headers'     => [
                    'Content-Type' => 'application/json',
                ],
                'method' => 'PATCH',
                'body' => $json_body
            )
        );
        if (!is_wp_error($response)){
            $response_code = $response['response']['code'];
            if ($response_code == 200) {
                $response_body = wp_remote_retrieve_body($response);
                return json_decode($response_body, true)['_id'];
            }
        }
        return null;
    }
}