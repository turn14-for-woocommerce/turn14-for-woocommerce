<?php
/**
 * Admin Controller Class for handling ajax requests 
 * 
 * @author Sam Hall https://github.com/hallsamuel90
 */

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Class Admin Controller
 */
class Admin_Controller
{
    private $worker;

    /**
     * Default Constructor
     */
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
     * Handler for ajax request which kicks off the import job
     */
    public function import_all_products()
    {
        // wp_unschedule_hook('worker_import_products_hook');
        
        wp_schedule_single_event(time(), 'worker_import_products_hook', array('page_number' => 1));
        spawn_cron();
        wp_send_json_success(
            array(
            'msg' => 'Importing all products in the background. This may take a while... We will email you when we are finished!'
            )
        );
    }

    /**
     * Handler for ajax request which kicks off the update job
     */
    public function update_products()
    {
        if (!wp_next_scheduled('worker_update_products_hook', array('page_number' => 1))) {
            $time = strtotime('today');
            $time = $time + 18000; // midnight eastern
            wp_schedule_event($time, 'daily', 'worker_update_products_hook', array('page_number' => 1));
        }
    }

    /**
     * Handler for ajax request which kicks off the delete all job
     */
    public function delete_all_products()
    {
        wp_schedule_single_event(time(), 'worker_delete_all_hook');
        spawn_cron();
        wp_send_json_success(
            array(
            'msg' => 'Deleting all products in the background. This may take a few minutes... We will email you when we are finished!')
        );
    }
}
