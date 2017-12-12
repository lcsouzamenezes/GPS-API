<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

abstract class App_model extends CI_Model
{
	protected $db;
	protected $_CI;
	protected $_table;
	protected $_debug = FALSE;
	protected $escape = TRUE; //Set this as TRUE if tilt symbol needs to be added with table fields.
	protected $_fields = '';

	public function __construct()
	{
		parent::__construct();
		$this->_CI = get_instance();
		if (!$this->_table) {
			$this->_table = $this->getTableName();
		}

		$this->db = $this->_CI->db;

	}

	function listing()
	{
	   
		$this->_fields = 'SQL_CALC_FOUND_ROWS '.$this->_fields;
		$this->escape = FALSE;
		$this->db->select($this->_fields, $this->escape);
		 
		$this->db->limit($this->listing->_get_per_page(), $this->listing->_get_offset());
		$this->db->order_by($this->listing->_get_order() , $this->listing->_get_direction());
		 
		$list = $this->db->get($this->_table)->result_array();
	 
		$count = $this->db->query("select FOUND_ROWS() as count")->row()->count;
		 
		return array(
    	            'list'  => $list,
    	            'count' => $count
		);
		 
	}

	public function get_where($where = array(), $fields = '*',$table = NULL, $order_by = NULL)
	{
		if(!is_array($where)) return FALSE;
		 
		$this->db->select($fields);
		 
		foreach ($where as $f => $v)
		{
			if(is_array($v))
			$this->db->where_in($f, $v);
			else
			$this->db->where($f, $v);
		}
		 
		if( !is_null($order_by) )
		$this->db->order_by($order_by);

		$table = ($table)?$table:$this->_table;
		 
		return $this->db->get($table);
	}


	public function insert($data, $table = NULL)
	{
		$table = ($table)?$table:$this->_table;

		$this->db->insert($table, $data);

		if ($this->_debug){
			log_message('debug', $this->db->last_query());
		}

		return $this->get_last_id();
	}

	public function update($where = array(), $data,$table = NULL)
	{
		if(!is_array($where)) return FALSE;

		$table = ($table)?$table:$this->_table;

		foreach ($where as $f => $v)
		{
			if(is_array($v))
			$this->db->where_in($f, $v);
			else
			$this->db->where($f, $v);
		}
		 

		$this->db->update($table, $data);

		if ($this->_debug){
			log_message('debug', $this->db->last_query());
		}

		return $this->db->affected_rows();
	}


	public function delete($where = array(),$table = NULL)
	{
		if(!is_array($where)) return FALSE;
		 
		$table = ($table)?$table:$this->_table;

		foreach ($where as $f => $v)
		{
			if(is_array($v))
			$this->db->where_in($f, $v);
			else
			$this->db->where($f, $v);
		}

		return $this->db->delete($table);
	}



	function getTableName()
	{
		$class = strtolower(get_class($this));
		return substr($class, 0, strlen($class) - 6);
	}

	public function get_last_id()
    {
        return $this->db->insert_id();
    }
 

	protected function _before_save($data) {
	}

	protected function _after_save($last_id) {
	}

}

?>
