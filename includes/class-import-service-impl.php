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
 * Class Interface Import Service Implementation for Turn14 into WooCommerce
 */
class Import_Service_Impl implements Import_Service
{
    private $turn14_product_query;
    private $turn14_rest_client;

    /**
     * Default Constructor
     */
    public function __construct()
    {
        $this->turn14_product_query = new Turn14_Product_Query();
        $this->turn14_rest_client = new Turn14_Rest_Client();
    }

    public function import_products($turn14_items)
    {
        if ($turn14_items !== null) {
            foreach ($turn14_items as $item) {
                if (!post_exists($item['attributes']['product_name'])) {
                    $post_id = $this->import_product($item);
                    
                    $media = $this->turn14_rest_client->get_item_media($item['id'])[0];
                    if ($media != null){
                        $this->import_media($post_id, $media);
                    }

                    $pricing = $this->turn14_rest_client->get_item_pricing($item['id']);
                    if($pricing != null){
                        $this->import_pricing($post_id, $pricing);
                    }

                    $inventory = $this->turn14_rest_client->get_item_inventory($item['id']);
                    if($inventory != null){
                        $this->import_inventory($post_id, $inventory);
                    }
                }
            }
        }
    }

    public function import_products_media($turn14_media)
    {
        if ($turn14_media !== null) {
            foreach ($turn14_media as $media) {
                $product_id =  $this->turn14_product_query->get_product_id_by_turn14_id($media['id']);
                if ($product_id != null) {
                    $this->import_media($media, $product_id);
                }
            }
        }
    }

    public function import_products_pricing($turn14_pricing)
    {
        if ($turn14_pricing !== null) {
            foreach ($turn14_pricing as $pricing) {
                $product_id =  $this->turn14_product_query->get_product_id_by_turn14_id($pricing['id']);
                if ($product_id != null) {
                    $this->import_pricing($product_id, $pricing);
                }
            }
        }
    }

    public function import_products_inventory($turn14_inventory)
    {
        if ($turn14_inventory !== null) {
            foreach ($turn14_inventory as $inventory) {
                $product_id =  $this->turn14_product_query->get_product_id_by_turn14_id($inventory['id']);
                if ($product_id != null) {
                    $this->import_inventory($product_id, $inventory);
                }
            }
        }
    }

    public function delete_products_all()
    {
        while (true) {
            $products = $this->turn14_product_query->get_all_turn14_products();
            if (!empty($products)) {
                foreach ($products as $product) {
                    $post_id = $product->post_id;
                    wp_delete_post($post_id, true);
                }
            } else {
                break;
            }
        }
    }

    /**
     * Helper function for importing a single product
     * 
     * @param array product
     */
    private function import_product($turn14_product)
    {
        $product_id = $turn14_product['id'];
        $turn14_product = $turn14_product['attributes'];

        $post_id = Import_Util::import_post($turn14_product);

        $thumbnail = $turn14_product['thumbnail'];
        if ($thumbnail != null){
            Import_Util::import_image($post_id, $thumbnail, true);
        }
        
        update_post_meta($post_id, '_sku', $turn14_product['mfr_part_number']);

        // dimensions, first box only
        $dimensions = $turn14_product['dimensions'][0];
        Import_Util::import_dimensions($post_id, $dimensions);

        // set attributes
        Import_Util::import_attributes($post_id, $product_id);

        // set categories
        Import_Util::import_categories($post_id, $turn14_product);

        wp_set_object_terms($post_id, 'simple', 'product_type');
        update_post_meta($post_id, '_visibility', 'visible');

        return $post_id;
    }

    /**
     * Helper function for importing a single media
     * 
     * @param array media
     */
    private function import_media($post_id, $media)
    {
        // update descriptions
        Import_Util::import_descriptions($post_id, $media);

        // update images
        Import_Util::import_images($post_id, $media);
    }

    /**
     * Helper function for importing a single pricing
     * 
     * @param array pricing
     */
    private function import_pricing($post_id, $pricing)
    {
        Import_Util::import_pricing($post_id, $pricing);
    }

    /**
     * Helper function for importing a single inventory
     * 
     * @param array inventory
     */
    private function import_inventory($post_id, $inventory)
    {
        Import_Util::import_inventory($post_id, $inventory);
    }
}
