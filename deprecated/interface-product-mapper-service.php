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
interface Product_Mapper_Service
{
    public function map_product($item_attributes);
}