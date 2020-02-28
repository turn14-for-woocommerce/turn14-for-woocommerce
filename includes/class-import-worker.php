<?php
/**
 * Import Worker Class
 *
 * @author Sam Hall https://github.com/hallsamuel90
 */

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Class Import Worker completes import jobs
 */
class Import_Worker
{
    private $turn14_rest_client;
    private $import_service;
    private $admin_emailer;

    /**
     * Default Constructor
     */
    public function __construct()
    {
        $this->turn14_rest_client = new Turn14_Rest_Client();
        $this->import_service = new Import_Service_Impl();
        $this->admin_emailer = new Admin_Emailer();
        
        if (! is_admin()) {
            require_once(ABSPATH . 'wp-admin/includes/post.php');
        }
        
        add_action('worker_import_products_hook', array($this, 'import_products'));
        add_action('worker_import_media_hook', array($this, 'import_media'));
        add_action('worker_import_pricing_hook', array($this, 'import_pricings'));
        add_action('worker_import_inventory_hook', array($this, 'import_inventory'));
        add_action('worker_update_hook', array($this, 'update_products'));
        add_action('worker_delete_all_hook', array($this, 'delete_all_products'));
    }

    /**
     * Job function for importing products into WooCommerce
     * 
     * @param int page number of API
     */
    public function import_products($page_number)
    {
        error_log('Importing Turn14 Items page ' . $page_number);
        set_time_limit(0);

        $turn14_items = $this->turn14_rest_client->get_items($page_number);
        if ($page_number == 1 && $turn14_items != null) {
            $total_pages = $turn14_items['meta']['total_pages'];
            for ($i = 2; $i <= $total_pages; $i++) {
                wp_schedule_single_event(time(), 'worker_import_products_hook', array('page_number' => $i));
                spawn_cron();
            }
        }
        $turn14_items = $turn14_items['data'];
        if (!empty($turn14_items)) {
            $this->import_service->import_products($turn14_items);
        }
    }

    /**
     * Job function for importing media into WooCommerce
     * 
     * @param int page number of API
     */
    public function import_media($page_number)
    {
        error_log('Importing Turn14 Media page ' . $page_number);
        set_time_limit(0);

        while (true) {
            $turn14_media = $this->turn14_rest_client->get_media($page_number);
            if ($page_number == 1 && $turn14_media != null) {
                $total_pages = $turn14_media['meta']['total_pages'];
                for ($i = 2; $i <= $total_pages; $i++) {
                    wp_schedule_single_event(time(), 'worker_import_media_hook', array('page_number' => $i));
                    spawn_cron();
                }
            }
            $turn14_media = $turn14_media['data'];
            if (!empty($turn14_media)) {
                $this->import_service->import_products_media($turn14_media);
            } else {
                break;
            }
        }
    }

    /**
     * Job function for importing pricings into WooCommerce
     * 
     * @param int page number of API
     */
    public function import_pricings($page_number)
    {
        error_log('Importing Turn14 Pricing page ' . $page_number);
        set_time_limit(0);

        while (true) {
            $turn14_pricing = $this->turn14_rest_client->get_pricing($page_number);
            if ($page_number == 1 && $turn14_pricing != null) {
                $total_pages = $turn14_pricing['meta']['total_pages'];
                for ($i = 2; $i <= $total_pages; $i++) {
                    wp_schedule_single_event(time(), 'worker_import_pricing_hook', array('page_number' => $i));
                    spawn_cron();
                }
            }
            $turn14_pricing = $turn14_pricing['data'];
            if (!empty($turn14_pricing)) {
                $this->import_service->import_products_pricing($turn14_pricing);
            } else {
                break;
            }
        }
    }

    /**
     * Job function for importing inventory into WooCommerce
     * 
     * @param int page number of API
     */
    public function import_inventory($page_number)
    {
        error_log('Importing Turn14 Pricing page ' . $page_number);
        set_time_limit(0);

        while (true) {
            $turn14_inventory = $this->turn14_rest_client->get_inventory($page_number);
            if ($page_number == 1 && $turn14_inventory != null) {
                $total_pages = $turn14_inventory['meta']['total_pages'];
                for ($i = 2; $i <= $total_pages; $i++) {
                    wp_schedule_single_event(time(), 'worker_import_inventory_hook', array('page_number' => $i));
                    spawn_cron();
                }
            }
            $turn14_inventory = $turn14_inventory['data'];
            if (!empty($turn14_inventory)) {
                $this->import_service->import_products_inventory($turn14_inventory);
            } else {
                break;
            }
        }
    }

    /**
     * Job function for updating products
     */
    public function update_products()
    {
    }

    /**
     * Job function for deleting all Turn14 products
     */
    public function delete_all_products()
    {
        error_log('Deleting all Turn14 products');
        set_time_limit(0);
        $this->import_service->delete_products_all();
    }
}
