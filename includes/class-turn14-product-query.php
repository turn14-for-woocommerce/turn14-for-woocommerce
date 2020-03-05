<?php
/**
 * Turn14 Product Query Class
 *
 * @author Sam Hall https://github.com/hallsamuel90
 */

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Class Turn14 Product Query for custom queries of Turn14 products
 */
class Turn14_Product_Query
{
    const PRODUCT_BY_ID_QUERY = "SELECT DISTINCT post_id FROM `wp_postmeta`"
    . " WHERE meta_key = '_product_attributes'"
    . " AND meta_value LIKE '%turn14_id%'"
    . " AND meta_value LIKE '%%%s%%'";

    const ALL_PRODUCTS_QUERY = "SELECT DISTINCT post_id FROM `wp_postmeta`"
    . " WHERE meta_key = '_product_attributes'"
    . " AND meta_value LIKE '%turn14_id%'";

    private $db;
    
    /**
     * Default Constructor
     */
    public function __construct()
    {
        $this->db = $GLOBALS['wpdb'];
    }

    /**
     * Gets product(post) id based on the turn14 id 
     * 
     * @param int turn14 id of the product(post)
     * 
     * @return int product(post) id
     */
    public function get_product_id_by_turn14_id($turn14_id)
    {
        $turn14_id = '"' . $turn14_id . '"';
        $product = $this->db->get_results(
            $this->db->prepare(
                self::PRODUCT_BY_ID_QUERY,
                $turn14_id
            )
        )[0];

        return $product->post_id;
    }

    /**
     * Gets all turn14 product(post) ids  
     * 
     * @return array product(post) ids
     */
    public function get_all_turn14_products()
    {
        $products = $this->db->get_results(
            $this->db->prepare(self::ALL_PRODUCTS_QUERY)
        );

        return $products;
    }
}
