<?php

class Favourite_Model extends CI_Model {
    
    
    function __construct()
    {
        parent::__construct();
        $this->_table = 'favourite_users';
    }
   	
	function insert($ins_arr)
	{		
		 $this->db->insert($this->_table, $ins_arr);
         return $this->db->insert_id(); 
	}
	
    function update($ins_data,$where)
    {
        $this->db->where($where);
        $this->db->update($this->_table,$ins_data);
        return $this->db->affected_rows();
    }
     function delete($where)
    {
        $this->db->where($where);
        $this->db->delete($this->_table);

    }
    function check_unique($where)
    {
        $this->db->select();
        $this->db->from($this->_table);
        $this->db->where($where);
        $result = $this->db->get()->row_array();
        return $result;
    }
    
    
}
?>
