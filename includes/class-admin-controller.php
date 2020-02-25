<?php
if (! defined('ABSPATH')) {
    exit;
}

/**
 *
 */
class Admin_Controller
{
    private $worker;

    public function __construct()
    {
        add_action('wp_ajax_import_all_products', array($this, 'import_all_products' ));
        add_action('wp_ajax_nopriv_import_all_products', array($this, 'import_all_products' ));

        add_action('wp_ajax_delete_all_products', array($this, 'delete_all_products' ));
        add_action('wp_ajax_nopriv_delete_all_products', array($this, 'delete_all_products' ));

        $this->worker = new Import_Worker();
        $this->update_products();
    }

    /**
     * Method retrieves products from Turn14 and imports them into WooCommerce
     */
    public function import_all_products()
    {
        wp_schedule_single_event(time(), 'worker_import_all_hook');
        spawn_cron();
        wp_send_json_success(
            array(
            'msg' => 'Importing all products in the background. This may take a few minutes...')
        );
    }

    /**
     *
     */
    public function update_products()
    {
        if (!wp_next_scheduled('worker_update_hook')) {
            $time = strtotime('today');
            $time = $time + 18000; // midnight eastern
            wp_schedule_event($time, 'daily', 'worker_update_hook');
        }
    }

    /**
     *
     */
    public function delete_all_products()
    {
        wp_schedule_single_event(time(), 'worker_delete_all_hook');
        spawn_cron();
        wp_send_json_success(
            array(
            'msg' => 'Deleting all products in the background')
        );
    }
}
