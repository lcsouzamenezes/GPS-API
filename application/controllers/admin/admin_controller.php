<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

safe_include("controllers/app_controller.php");
class Admin_controller extends App_controller {

	public $namespace;
	public $_search_conditions 			= array("search_type", "search_text");
	public $_narrow_search_conditions 	= array();
	public $_session_namespace;
	public $_session_narrow_namespace;
	public $previous_url				= '';
	
	function __construct()
	{
		parent::__construct();
		
		//set name-spaces for session-storage of each controller-method pair.
		$this->namespace = strtolower($this->uri->segment(1, 'admin').'_'.$this->uri->segment(2, 'index').'_'.$this->uri->segment(3, 'index'));
		
		//echo '<pre>';print_r($this->session->all_userdata());
		//get all session keys
		$sess_keys = array_keys($this->session->all_userdata());
		
		/*unset session-data stored by using namespaces.But except the current controller-methos pair's data.
		 There are list of methods if current method is one of those, dont unset the session.*/
		$current_method = $this->uri->segment(3, 'index');
		$methods_list = array('index');// these are the methods which are having grid-view
		if(in_array($current_method, $methods_list))
		{
			$keys = array('search_conditions','search_narrow_conditions','fields','per_page','order_field','direction');
			foreach ($sess_keys as $key => $value)
			{
				foreach ($keys as $key)
				{
					$position1 = strpos($value, $key);
					$position2 = strpos($value, $this->namespace);
					if($position2 !== 0 && $position1 !== false && $position1 != 0)
					$this->session->unset_userdata($value);
				}
			}
		}

	}
	
	
	/**
	* This function returns the advance filter form.
	*@param string namespace <p>
	* It is the namespace of current grid-view page.
	* </p>
	* @return string HTML-Form.
	*/
	function get_advance_filter_form( $namespace = '' )
	{
		//load pagination config
		$this->load->config("listing", TRUE);
		 
		//get current grid's config by using namespace.
		$pagination = $this->config->item($namespace, 'listing');
		 
		//To populate the form, get the previous data if available in session.
		$this->data = $this->session->userdata($namespace.'_search_narrow_conditions');
		//print_r($this->data);die;
		//now get the form
		$form = $this->load->view($pagination['advance_search_view'], $this->data, TRUE);
		 
		if($this->input->is_ajax_request())
		$this->_ajax_output(array('advance_filter_form' => $form), TRUE);
		 
		return TRUE;
	}

	/**
     * This function sets the number of records per page for grid.
     *@param string namespace <p>
     * It is the namespace of current grid-view page.
     * </p>
     * @return void.
     */
    function set_records_per_page( $namespace = '' )
    {
    	$per_page = ((int)$this->input->post('per_page'))?$this->input->post('per_page'):20;
    	
    	$this->session->set_userdata($namespace.'_per_page', $per_page);
    	
    	if($this->input->is_ajax_request())
    		$this->_ajax_output(array('status' => 'success'), TRUE);
    	 
    	return TRUE;
    }
}
