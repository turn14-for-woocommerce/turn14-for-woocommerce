<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Configures custom fields for the WC REST API.
 *
 * @author Sam Hall <hallsamuel90@gmail.com>
 */
class Rest_Config
{
    private $db;

    /**
     * 
     */
    public function __construct($db){
        $this->db = $db;
    }

    /**
     *
     */
    public function register_rest_attributes()
    {
        add_action('rest_api_init', array($this, 'register_brand_id'));
        add_action('rest_api_init', array($this, 'register_ymm_fitment'));
    }

    /**
     * Registers the brand_id field.
     */
    public function register_brand_id()
    {
        register_rest_field('product',
            'brand_id',
            array(
                'get_callback' => array($this, 'get_brand_id'),
                'update_callback' => array($this, 'set_brand_id'),
                'schema' => null,
            )
        );
    }

    public function get_brand_id($object, $field_name)
    {
        return get_post_meta( $object[ 'id' ], $field_name, true );
    }

    public function set_brand_id($value, $object, $field_name)
    {
        return update_post_meta($object->id, $field_name, $value);
    }

    /**
     * Registers the ymm_fitment field.
     */
    public function register_ymm_fitment()
    {
        register_rest_field('product',
            'ymm_fitment',
            array(
                'get_callback' => array($this, 'get_ymm_fitment'),
                'update_callback' => array($this, 'set_ymm_fitment'),
                'schema' => null,
            )
        );
    }

    public function get_ymm_fitment($object, $field_name, $request)
    {
        return $this->db->getProductRestrictionText($object['id']);
    }

    public function set_ymm_fitment($value, $object, $field_name)
    {
        return $this->db->saveProductRestrictionText($object->id, $value);
    }
}
