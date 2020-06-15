<?php
/**
 * Brands Table Class for displaying brand data
 * 
 * @author Sam Hall https://github.com/hallsamuel90
 */

if (!class_exists('WP_List_Table')) {
    require_once (ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
 
}
 
/** 
 * Create a new table class that will extend the WP_List_Table 
 */
class Brands_Table extends WP_List_Table
{
    private $service_client;

    public function __construct()
    {
        $this->service_client = new Service_Client();
        $this->prepare_items();
        parent::__construct(array(
            'singular' => 'brand',
            'plural' => 'brands',
            'ajax' => true
        ));
        
    }

    /** 
     * Prepare the items for the table to process
     */
    public function prepare_items()
    {
        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();
        $this->process_bulk_action();
        $data = $this->get_table_data();
        usort( $data, array( &$this, 'sort_data' ) );

        $perPage = 20;
        $currentPage = $this->get_pagenum();
        $totalItems = count($data);

        $this->set_pagination_args( array(
            'total_items' => $totalItems,
            'per_page'    => $perPage
        ) );

        $data = array_slice($data,(($currentPage-1)*$perPage),$perPage);

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->items = $data;
    }

    /**
     * Fetch brands from the brand service
     * 
     * @param boolean pull fresh or accept cached data, defaults to false
     */
    public function get_table_data($fresh = false)
    {
        $user_id = get_transient('user_id');
        if ($user_id == null){
            $user_id = $this->service_client->login(home_url(), get_option('turn14_settings')['turn14_api_secret']);
            set_transient('user_id', $user_id, 60*60);
        }
        $brands = get_transient('brands');
        if($brands == null || $fresh){
            $brands = $this->service_client->get_brands($user_id);
            set_transient('brands', $brands, 60*60);
        }
        // TODO error handling
        return $brands;
    }

    /** 
    * Override the parent columns method. Defines the columns to use in your listing table 
    * @return Array 
    */
    function get_columns()
    {
        $columns = array(
            'cb' => '<input type="checkbox" />',
            'brand_name' => 'Brand Name',
            'active' => 'Active', 
            'first_published' => 'First Published',
            'last_updated' => 'Last Updated',
        );
        return $columns;
    }

    /**
     * Get hidden columns 
     */
    public function get_hidden_columns()
    {
            // Setup Hidden columns and return them
            return array();
    }

    /**
     * Make columns sortable
     */
    public function get_sortable_columns()
    {
        // TODO
        // $sortable_columns = array(
        //     'cb' => array('cb', false) ,
        //     'brand_name' => array('brand_name', true) ,
        //     'active' => array('active', true) ,
        //     'last_updated' => array('last_updated', true) 
        // );
        return array();
    }

    /** 
     *Text displayed when no record data is available 
     */
    public function no_items()
    {
        _e('No brand data could be displayed. Make sure you have set your API keys in the Settings tab!', 'bx');
    }

    /** 
    * Render the bulk edit checkbox 
    * * @param array $item 
    * * @return string 
    */
    function column_cb($item)
    {
        return sprintf('<input type="checkbox" name="bulk-action[]" value="%s" />', $item['_id']);
    }

    /** 
    * Render the brand name
    * * @param array $item 
    * * @return string 
    */
    function column_brand_name($item)
    {
        $actions = array(
            'activate'      => sprintf('<a href="?page=%s&action=%s&id=%s">Activate</a>',$_REQUEST['page'],'activateBrand',$item['_id']),
            'deactivate'    => sprintf('<a class=deactivate href="?page=%s&action=%s&id=%s">Deactivate</a>',$_REQUEST['page'],'deactivateBrand',$item['_id']),
        );
        return sprintf('<p class=row-title >%s</p> %s', $item['brandName'], $this->row_actions($actions));
    }

    /** 
    * Render active
    * * @param array $item 
    * * @return string 
    */
    function column_active($item)
    {
        // return sprintf($item['active']);
        return sprintf( $item['active'] ? 'true' : 'false');
    }

    /** 
    * Render first imported
    * * @param array $item 
    * * @return string 
    */
    function column_first_published($item)
    {
        $date = date('d-M-Y', strtotime($item['firstPublished']));
        return sprintf($date);
    }

    /** 
    * Render last updated
    * * @param array $item 
    * * @return string 
    */
    function column_last_updated($item)
    {
        $date = date('d-M-Y', strtotime($item['lastUpdated']));
        return sprintf($date);
    }

    /** 
     * Returns an associative array containing the bulk action 
     * @return array 
     */
    public function get_bulk_actions()
    {
        $actions = array(
            'bulk-activate' => 'Activate',
            'bulk-deactivate' => 'Deactivate'
        );
        return $actions;
    }

    public function process_bulk_action(){
        $action = $this->current_action();
        if('activateBrand' === $this->current_action()){
            $id = $_REQUEST['id'];
            $this->service_client->activate($id, true);
            $brands = $this->get_table_data(true);
        } else if('deactivateBrand' === $this->current_action()){
            $id = $_REQUEST['id'];
            $this->service_client->activate($id, false);
            $brands = $this->get_table_data(true);
        }
    }
}