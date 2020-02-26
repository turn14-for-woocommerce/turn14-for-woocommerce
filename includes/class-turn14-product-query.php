<?php
if (! defined('ABSPATH')) {
    exit;
}

/**
 *
 */
class Turn14_Product_Query
{
    /**
     *
     */
    public static function get_product_by_turn14_id($turn14_id)
    {
        // SELECT DISTINCT post_id, meta_value FROM `wp_posts` INNER JOIN `wp_postmeta` 
        // WHERE meta_key = '_product_attributes' 
        // AND post_type = 'product'
        // AND meta_value LIKE '%turn14_id%'
        $args = array(
            array(
                'taxonomy' => 'pa_turn14_id',
                'field' => 'slug',
                'terms' => $turn14_id,
                'operator' => 'IN'
            )
        );

        $products = wc_get_products($args);

        // should only be one
        return $products[0];
    }

    /**
     *
     */
    public static function get_all_turn14_products()
    {
        return  wc_get_products(array(
            array(
                'limit' => -1,
                'taxonomy' => 'pa_turn14_id',
                'operator' => 'EXISTS'
            )
        ));
    }
}
