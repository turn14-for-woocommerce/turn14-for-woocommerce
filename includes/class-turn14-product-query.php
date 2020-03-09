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
    private $db;
    private $all_products_query;
    private $products_by_id_query;
    private $image_by_file_name_query;
    
    /**
     * Default Constructor
     */
    public function __construct()
    {
        $this->db = $GLOBALS['wpdb'];
        $this->all_products_query = "SELECT DISTINCT post_id FROM {$this->db->postmeta}"
        . " WHERE meta_key = '_product_attributes'"
        . " AND meta_value LIKE '%turn14_id%'";
        $this->products_by_id_query = "SELECT DISTINCT post_id FROM {$this->db->postmeta}"
        . " WHERE meta_key = '_product_attributes'"
        . " AND meta_value LIKE '%turn14_id%'"
        . " AND meta_value LIKE '%%%s%%'";
        $this->image_by_file_name_query = "SELECT DISTINCT post_id FROM {$this->db->postmeta}"
        . " WHERE meta_value LIKE '%%%s%%'";
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
                $this->products_by_id_query,
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
            $this->db->prepare(
                $this->all_products_query
            )
        );

        return $products;
    }

    /**
     * Gets product(post) id based on the turn14 id
     *
     * @param int turn14 id of the product(post)
     *
     * @return int product(post) id
     */
    public function get_image_id_by_file_name($filename)
    {
        $image = $this->db->get_results(
            $this->db->prepare(
                $this->image_by_file_name_query,
                $filename
            )
        )[0];

        return $image->post_id;
    }
}
