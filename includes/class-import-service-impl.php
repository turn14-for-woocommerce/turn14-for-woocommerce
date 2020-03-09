<?php
/**
 * Import Service Interface Implementation Class
 *
 * @author Sam Hall https://github.com/hallsamuel90
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class Import Service Implementation for Turn14 into WooCommerce
 */
class Import_Service_Impl implements Import_Service
{
    private $turn14_product_query;
    private $turn14_rest_client;
    private $product_mapper;

    /**
     * Default Constructor
     */
    public function __construct()
    {
        $this->turn14_product_query = new Turn14_Product_Query();
        $this->turn14_rest_client = new Turn14_Rest_Client();
        $this->product_mapper = new Product_Mapper_Service_Impl($this->turn14_rest_client);
    }

    public function import_products($turn14_items)
    {
        if ($turn14_items !== null) {
            foreach ($turn14_items as $item) {
                if ($item != null) {
                    $product = $this->product_mapper->map_product($item);
                    $product->save();
                }
            }
        }
    }

    public function delete_products_all()
    {
        $products = $this->turn14_product_query->get_all_turn14_products();
        
        if (!empty($products)) {
            foreach ($products as $product) {
                $post_id = $product->post_id;
                wp_delete_post($post_id, true);
            }
        }
    }
}
