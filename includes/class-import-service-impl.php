<?php
if (!defined('ABSPATH')) {
    exit;
}
/**
 *
 */
class Import_Service_Impl implements Import_Service
{
    const SIMPLE = 'simple';

    /**
     *
     */
    public function __construct()
    {
    }

    /**
     *
     */
    public function import_product($turn14_product)
    {
        $product_id = $turn14_product['id'];
        $turn14_product = $turn14_product['attributes'];

        $post_id = Import_Util::import_post($turn14_product);

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
     *
     */
    public function import_media($media, $post_id)
    {
        // update descriptions
        Import_Util::import_descriptions($post_id, $media);

        // update images
        Import_Util::import_images($post_id, $media);
    }

    /**
     *
     */
    public function import_pricing($post_id, $pricing)
    {
        Import_Util::import_pricing($post_id, $pricing);
    }

    /**
     *
     */
    public function import_inventory($post_id, $inventory)
    {
        Import_Util::import_inventory($post_id, $inventory);
    }
}
