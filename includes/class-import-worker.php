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
        $current_page = 376;

        while (true) {
            $turn14_items = $this->turn14_rest_client->get_items($current_page);
            if (!empty($turn14_items)) {
                $this->import_products_posts($turn14_items);
            } else {
                break;
            }
            $current_page = $current_page + 1;
        }

        $current_page = 752;

        // media
        while (true) {
            $turn14_media = $this->turn14_rest_client->get_media($current_page);
            if (!empty($turn14_media)) {
                $this->import_products_media($turn14_media);
            } else {
                break;
            }
            $current_page = $current_page + 1;
        }
        
        $current_page = 1;

        // pricing
        while (true) {
            $turn14_pricing = $this->turn14_rest_client->get_pricing($current_page);
            if (!empty($turn14_pricing)) {
                $this->import_products_pricing($turn14_pricing);
            } else {
                break;
            }
            $current_page = $current_page + 1;
        }
        
        $current_page = 1;

        // inventory
        while (true) {
            $turn14_inventory = $this->turn14_rest_client->get_inventory($item['id']);
            if (!empty($turn14_inventory)) {
                $this->import_products_inventory($turn14_inventory);
            } else {
                break;
            }
            $current_page = $current_page + 1;
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
        while (true) {
            $products = Turn14_Product_Query::get_all_turn14_products();
            if (!empty($products)) {
                foreach ($products as $product) {
                    $post_id = $product->get_id();
                    wp_delete_post($post_id, true);
                }
            } else {
                break;
            }
        }

        //send email
    }

    /**
     *
     */
    private function import_products_posts($turn14_items)
    {
        if ($turn14_items !== null) {
            foreach ($turn14_items as $item) {
                if (!post_exists($item['attributes']['product_name'])) {
                    $post_id = $this->import_service->import_product($item);
                }
            }
        }
    }

    /**
     *
     */
    private function import_products_media($turn14_media)
    {
        if ($turn14_media !== null) {
            foreach ($turn14_media as $media) {
                $product =  Turn14_Product_Query::get_product_by_turn14_id($media['id']);
                if ($product != null) {
                    $product_id = $product->get_id();
                    $this->import_service->import_media($media, $product_id);
                }
            }
        }
    }

    /**
     *
     */
    private function import_products_pricing($turn14_pricing)
    {
        if ($turn14_pricing !== null) {
            foreach ($turn14_pricing as $pricing) {
                $product =  Turn14_Product_Query::get_product_by_turn14_id($pricing['id']);
                if ($product != null) {
                    $product_id = $product->get_id();
                    $this->import_service->import_pricing($product_id, $pricing);
                }
            }
        }
    }

    /**
     *
     */
    private function import_products_inventory($turn14_inventory)
    {
        if ($turn14_inventory !== null) {
            foreach ($turn14_inventory as $inventory) {
                $product =  Turn14_Product_Query::get_product_by_turn14_id($inventory['id']);
                if ($product != null) {
                    $product_id = $product->get_id();
                    $this->import_service->import_inventory($product_id, $inventory);
                }
            }
        }
    }
}
