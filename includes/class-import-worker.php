<?php
if (! defined('ABSPATH')) {
    exit;
}

/**
 *
 */
class Import_Worker
{
    private $turn14_rest_client;
    private $import_service;

    public function __construct()
    {
        $this->turn14_rest_client = new Turn14_Rest_Client();
        $this->import_service = new Import_Service_Impl();
        
        if (! is_admin()) {
            require_once(ABSPATH . 'wp-admin/includes/post.php');
        }
        
        add_action('worker_import_all_hook', array($this, 'import_all_products'));
        add_action('worker_update_hook', array($this, 'update_products'));
        add_action('worker_delete_all_hook', array($this, 'delete_all_products'));
    }

    /**
     * Method retrieves products from Turn14 and imports them into WooCommerce
     */
    public function import_all_products()
    {
        // $current_page = (!empty($_POST['current_page'])) ? $_POST['current_page']:1;

        $turn14_items = $this->turn14_rest_client->get_items('1');
        
        if ($turn14_items !== null) {
            foreach ($turn14_items as $item) {
                if (!post_exists($item['attributes']['product_name'])) {
                    $post_id = $this->import_service->import_product($item);
                }
            }
        }

        // media
        $turn14_media = $this->turn14_rest_client->get_media('1');
        
        if ($turn14_media !== null) {
            foreach ($turn14_media as $media) {
                $product =  wc_get_products(array(
                    array(
                        'taxonomy' => 'pa_turn14_id',
                        'field' => 'slug',
                        'terms' => $media['id'],
                        'operator' => 'IN'
                    )
                ))[0];
                
                if ($product != null) {
                    $product_id = $product->get_id();
                    $this->import_service->import_media($media, $product_id);
                }
            }
        }
        

        // pricing
        $turn14_pricing = $this->turn14_rest_client->get_pricing('1');
        if ($turn14_pricing !== null) {
            foreach ($turn14_pricing as $pricing) {
                $product =  wc_get_products(array(
                array(
                    'taxonomy' => 'pa_turn14_id',
                    'field' => 'slug',
                    'terms' => $pricing['id'],
                    'operator' => 'IN'
                )
                ))[0];

                if ($product != null) {
                    $product_id = $product->get_id();
                    $this->import_service->import_pricing($product_id, $pricing);
                }
            }
        }

        // inventory
        $turn14_inventory = $this->turn14_rest_client->get_inventory($item['id']);
        if ($turn14_inventory !== null) {
            foreach ($turn14_inventory as $inventory) {
                $product =  wc_get_products(array(
                array(
                    'taxonomy' => 'pa_turn14_id',
                    'field' => 'slug',
                    'terms' => $inventory['id'],
                    'operator' => 'IN'
                )
                ))[0];

                if ($product != null) {
                    $product_id = $product->get_id();
                    $this->import_service->import_inventory($product_id, $inventory);
                }
            }
        }
    }

    /**
     *
     */
    public function update_products()
    {
    }

    /**
     *
     */
    public function delete_all_products()
    {
        $query = new WC_Product_Query();
        $query->set('turn14_id', true);
        $products = $query->get_products();

        foreach ($products as $product) {
            $post_id = $product->get_id();
            wp_delete_post($post_id, true);
        }
    }
}
