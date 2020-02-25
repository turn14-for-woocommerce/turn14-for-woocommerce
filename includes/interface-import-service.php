<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Interface for Import Services
 */
interface Import_Service
{
    public function import_product($turn14_product);

    public function import_media($media, $post_id);

    public function import_pricing($post_id, $pricing);

    public function import_inventory($post_id, $inventory);

}