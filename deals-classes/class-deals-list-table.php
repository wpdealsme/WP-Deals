<?php

//secure included files
deals_secure();

/**
 * Create managable table data, extend
 * from default Wordpress class WP_List_Table
 * 
 * @author Tokokoo
 * @package Deals_List_Table 
 */
class Deals_List_Table extends WP_List_Table {
    
    public $data = array();
    public $data_per_page = '20';    
    public $column_default_callback = null;
    public $column_cb_callback = null;
    public $bulk_action_callback = null;
    public $prepare_item_callback = null;
    public $extra_table_nav_callback = null;
    public $register_columns = array();
    public $register_sortable_columns = array();
    public $register_bulk_actions = array();
    
    /**
     * Construct method
     * 
     * @access public
     * @param array $args
     * @return void
     */
    public function __construct($args){
        global $status, $page,$wp_roles;
                
        //Set parent defaults
        parent::__construct( array(
            'singular'  => $args['singular_name'],     //singular name of the listed records
            'plural'    => $args['plural_name'],    //plural name of the listed records
            'ajax'      => $args['ajax_enabled']        //does this table support ajax?
        ) );                
        
    } 
    
    /**
     * Set column default
     *
     * @access public
     * @param array $item
     * @param array $column_name
     * @return callback
     */
    public function column_default($item, $column_name) {        
        return $this->_set_table_callback('column_default_callback', array($item,$column_name));        
    }
    
    /**
     * Set column checkbox
     * 
     * @access public
     * @param array $item
     * @return callback
     */
    public function column_cb($item) {           
        return $this->_set_table_callback('column_cb_callback', array($item));        
    }
    
    /**
     * Get registered columns
     * 
     * @return array
     */
    public function get_columns() {
        return $this->register_columns;
    }
    
    /**
     * Get registered sortable columns
     * 
     * @return array
     */
    public function get_sortable_columns() {
        return $this->register_sortable_columns;
    }
    
    /**
     * Get registered bulk actions
     * 
     * @return array
     */
    public function get_bulk_actions() {
        return $this->register_bulk_actions;
    }
    
    public function process_bulk_action() {        
        return $this->_set_table_callback('bulk_action_callback');
    }
    
    /**
     * Prepare data table
     * 
     * @return void
     */
    public function prepare_items() {
        $this->_set_table_callback('prepare_item_callback',array($this));
    }
    
    /**
     * Set callback to give extra tablenav
     * 
     * @param string $which
     * @return void
     */
    public function extra_tablenav($which) {        
        $this->_set_table_callback('extra_table_nav_callback',array($which));        
    }
    
    /**
     * Get all registered callback
     * 
     * @param string $type
     * @param array $args [optional]
     * @return void
     */
    private function _set_table_callback($type,$args=array()) {
        
        $callback = $this->{$type};
        if( isset($callback) && !is_null($callback) ) {
            
            if( function_exists($callback) ) {
                if(!empty($args)) {
                    call_user_func_array($callback,$args);
                }else{
                    call_user_func($callback);
                }
            }
            
        }
        
    }
    
}