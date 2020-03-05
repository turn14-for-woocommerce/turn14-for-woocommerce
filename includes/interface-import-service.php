<?php
/**
 * Import Service Interface
 * 
 * @author Sam Hall https://github.com/hallsamuel90
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Interface Import Services for Turn14 into WooCommerce
 */
interface Import_Service
{
    /**
     * Imports full Turn14 products into WooCommerce
     * 
     * @param array of turn14 products
     * 
     */
    public function import_products($turn14_items);

    /**
     * Imports product media into WooCommerce
     * 
     * @param array of turn14 products media
     * 
     */
    public function import_products_media($turn14_media);

    /**
     * Imports products pricing into WooCommerce
     * 
     * @param array of turn14 products pricing
     * 
     */
    public function import_products_pricing($turn14_pricing);

    /**
     * Imports full Turn14 products into WooCommerce
     * 
     * @param array of turn14 products
     * 
     */
    public function import_products_inventory($turn14_inventory);

    /**
     * Imports full Turn14 products into WooCommerce
     * 
     * @param array of turn14 products
     * 
     */
    public function delete_products_all();

}