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
    public function __construct()
    {
        parent::__construct(array(
            'singular' => 'singular_form',
            'plural' => 'plural_form',
            'ajax' => true
        ));
        $this->prepare_items();
    }

    /** 
     * Prepare the items for the table to process
     */
    public function prepare_items()
    {
        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();

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
        // $this->process_bulk_action();
        $this->items = $data;
    }

    /**
     * Retrieve records data from the database
     */
    public static function get_table_data()
    {
        
        return array(
            array(
                'brand_id' => '243',
                'brand_name' => 'ARB',
                'active' => 'true',
                'last_updated' => '2020/04/12'
            ),
            array(
                'brand_id' => '43',
                'brand_name' => 'Desert Racing Designs',
                'active' => 'true',
                'last_updated' => '2020/04/11'
            )
        );
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
        _e('No brands found.', 'bx');
    }

    /** 
     * Returns the count of records in the database. 
     * @return null|string 
     */
    public static function record_count()
    {
        // global $wpdb;
        // $sql = "SELECT COUNT(*) FROM custom_records";
        return '1';
    }

    /** 
    * Render the bulk edit checkbox 
    * * @param array $item 
    * * @return string 
    */
    function column_cb($item)
    {
        return sprintf('<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['brand_id']);
    }

    /** 
    * Render the brand name
    * * @param array $item 
    * * @return string 
    */
    function column_brand_name($item)
    {
        $actions = array(
            'activate'      => sprintf('<a href="?page=%s&action=%s&book=%s">Activate</a>',$_REQUEST['page'],'activate',$item['brand_id']),
            'deactivate'    => sprintf('<a href="?page=%s&action=%s&book=%s">Deactivate</a>',$_REQUEST['page'],'deactivate',$item['brand_id']),
        );
        return sprintf('%s %s', $item['brand_name'], $this->row_actions($actions));
    }

    /** 
    * Render active
    * * @param array $item 
    * * @return string 
    */
    function column_active($item)
    {
        return sprintf($item['active']);
    }

    /** 
    * Render last updated
    * * @param array $item 
    * * @return string 
    */
    function column_last_updated($item)
    {
        return sprintf($item['last_updated']);
    }

    /** 
     * Returns an associative array containing the bulk action 
     * @return array 
     */
    public function get_bulk_actions()
    {
        $actions = array(
            'bulk-activate' => 'Activate',
            'buld-deactivate' => 'Deactivate'
        );
        return $actions;
    }
}