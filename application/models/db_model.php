<?php

class Db_model extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
    
	// load a single record based on the parameters
	function load($table, $select, $join, $where, $or_where, $like, $or_like)
	{
		if($select)
		{
			$this->db->select($select);
		}
		
		if($join)
		{
			foreach($join as $key=>$value)
			{
				$this->db->join("$key", "$value", 'left');
			}
		}
		
		if($where)
		{
			foreach($where as $key=>$value)
			{
				$this->db->where("$key", "$value");
			}
		}
		
		if($or_where)
		{
			foreach($or_where as $key=>$value)
			{
				$this->db->or_where("$key", "$value");
			}
		}
		
		if($like)
		{
			foreach($like as $key=>$value)
			{
				$this->db->like("$key", "$value");
			}
		}
		
		if($or_like)
		{
			foreach($or_like as $key=>$value)
			{
				$this->db->or_like("$key", "$value");
			}
		}
				
		return $this->db->get($table)->row_array();
	}
	
	function loadall1($table, $select, $join, $where, $or_where, $like, $or_like, $limit=20, $offset=0, $groupby, $orderby, $where_in_q="")
	{
		if($select)
		{
			$this->db->select($select, false);
		}
			
		if($join)
		{
			foreach($join as $key=>$value)
			{
				$this->db->join("$key", "$value", 'left');
			}
		}
		
		if($where_in_q)
		{
			$this->db->where("$where_in_q");
		}

		if($where)
		{
			foreach($where as $key=>$value)
			{					
				if(($key == 'CreatedDate') && ($value))
					$this->db->where("DATE_FORMAT(ADDTIME(CreatedDate, '" . IN_TIME_DIFF_MYSQL . "'), '%d/%m/%Y') = '$value'");					
				else if($value != "")
				{
					$this->db->where("$key", "$value");
				}
			}
		}
		
		if($or_where)
		{
			foreach($or_where as $key=>$value)
			{
				$this->db->or_where("$key", "$value");
			}
		}
		
		if($like)
		{
			foreach($like as $key=>$value)
			{
				if($value)
					$this->db->like("$key", "$value");
			}
		}
		
		if($or_like)
		{
			foreach($or_like as $key=>$value)
			{
				if($value)
					$this->db->or_like("$key", "$value");
			}
		}
				
		if($groupby)
			$this->db->group_by($groupby);
		
		if($orderby)
			$this->db->order_by($orderby);
			
		$this->db->limit($limit,$offset);
		return $this->db->get($table)->result();
	}
	
	// load multiple records based on the parameters
	function loadall($table, $select, $join, $where, $or_where, $like, $or_like, $limit, $offset=0, $groupby, $orderby, $where_in_q="")
	{
		if($select)
		{
			$this->db->select($select, false);
		}
			
		if($join)
		{
			foreach($join as $key=>$value)
			{
				$this->db->join("$key", "$value", 'left');
			}
		}
		
		if($where_in_q)
		{
			$this->db->where("$where_in_q");
		}

		if($where)
		{
			foreach($where as $key=>$value)
			{					
				if($value != "")
				{
					$this->db->where("$key", "$value");
				}
			}
		}
		
		if($or_where)
		{
			foreach($or_where as $key=>$value)
			{
				$this->db->or_where("$key", "$value");
			}
		}
		
		if($like)
		{
			foreach($like as $key=>$value)
			{
				if($value)
					$this->db->like("$key", "$value");
			}
		}
		
		if($or_like)
		{
			foreach($or_like as $key=>$value)
			{
				if($value)
					$this->db->or_like("$key", "$value");
			}
		}
				
		if($groupby)
			$this->db->group_by($groupby);
		
		if($orderby)
			$this->db->order_by($orderby);
		
		if($limit)	
			$this->db->limit($limit,$offset);
		return $this->db->get($table)->result_array();
	}
	
	// update records based on the parameters
	function update($table, $upd_arr, $where, $where_in_q="")
	{
		if($where)
		{
			foreach($where as $key=>$value)
			{
				$this->db->where("$key", "$value");
			}
		}
		
		if($where_in_q)
		{
			$this->db->where("$where_in_q");
		}
		
		return $this->db->update($table, $upd_arr);
	}
	
	// insert a record based on the parameters
	function insert($table, $ins_arr)
	{		
		return $this->db->insert($table, $ins_arr);
	}
	
	// get the count of records based on the parameters
	function get_count($table, $join, $where, $or_where, $like, $or_like, $groupby, $where_in = "", $where_in_col = "", $where_in_q="")
	{
		if($join)
		{
			foreach($join as $key=>$value)
			{
				$this->db->join("$key", "$value", 'left');
			}
		}
		
		if($where_in_q)
		{
			$this->db->where("$where_in_q");
		}
		
		if($where)
		{
			foreach($where as $key=>$value)
			{
				if($value != "")
					$this->db->where("$key", "$value");
			}
		}
		
		/*if($where_in_q)
		{
			$this->db->where("$where_in_q");
		}*/
		
		if($or_where)
		{
			foreach($or_where as $key=>$value)
			{
				$this->db->or_where("$key", "$value");
			}
		}
		
		if($like)
		{
			foreach($like as $key=>$value)
			{
				if($value)
					$this->db->like("$key", "$value");
			}
		}
		
		if($or_like)
		{
			foreach($or_like as $key=>$value)
			{
				if($value)
					$this->db->or_like("$key", "$value");
			}
		}
		
		if($where_in)
		{
			$this->db->where_in("$where_in_col", "$where_in");
		}
		
		if($groupby)
			$this->db->group_by($groupby);

		return $this->db->get($table)->num_rows();
	}
	
	// delete records based on the parameters
	function delete($table, $where, $or_where, $like, $or_like, $where_in, $where_in_col)
	{
		if($where)
		{
			foreach($where as $key=>$value)
			{
				$this->db->where("$key", "$value");
			}
		}
		
		if($or_where)
		{
			foreach($or_where as $key=>$value)
			{
				$this->db->or_where("$key", "$value");
			}
		}
		
		if($like)
		{
			foreach($like as $key=>$value)
			{
				if($value)
					$this->db->like("$key", "$value");
			}
		}
		
		if($or_like)
		{
			foreach($or_like as $key=>$value)
			{
				if($value)
					$this->db->or_like("$key", "$value");
			}
		}
		
		if($where_in)
		{
			$this->db->where_in("$where_in_col", $where_in);
		}
		
		return $this->db->delete("$table");
	}
	

	
		//send mail based on the parameters  
	function send_mail($subject, $to, $content, $from='arun.izaptech@gmail.com')
	{		

  
        $this->config->load('email_config');
        
        $this->load->library('email', $this->config->item('email'));
        $this->email->set_newline("\r\n");
	
		$this->email->from("$from",'IZAP');
		$this->email->subject($subject);
		$this->email->to($to);					
	    $this->email->message($content);
	 	$this->email->send();
	}
	
    
    function get_lastIndex($date)
    {
       $prv_date  = date("Y-m-d", (strtotime($date) - (3600*24)));
       
         $lines = array();      
         $lines = @file(BASEPATH_CUSTOM."log/".$date.".txt");
         
       if(empty($lines))
       {
         $lines = @file(BASEPATH_CUSTOM."log/".$prv_date.".txt");
       }
      
       if(empty($lines))
       {
        return '0';
       }
       else
       {
         $last_line = $lines[count($lines)-1];       
         $x = explode(">",$last_line);        
         $val = str_replace('</td',"",$x[2]);
         return $val;
       }   
    }
    
    
    function get_user_details($where = array(),$select = ' * ') {
        
        $this->db->select($select,false);
        
        $this->db->from('user');
        
        if( !empty($where) ) {
            $this->db->where($where);
        }
        
        $this->db->join('user_plan','user_plan.plan_id = user.plan_id');
        
        return $this->db->get()->row_array();
    }
    
}
?>