<?php
safe_include("models/app_model.php");
class Plan_Model extends App_model {
    
    
    function __construct() 
    {
        parent::__construct();
        $this->_table = 'plans';
    }
   
   
   function listing()
   {
      $this->_fields = "*,id as id";
       
        foreach ($this->criteria as $key => $value) 
        {
            if( !is_array($value) && strcmp($value, '') === 0 )
                continue;

            switch ($key)
            {
                case 'planname':
                    $this->db->like($key, $value);
                break;
                case 'validity':
                    $this->db->like($key, $value);
                break;
            }
        }
        
        return parent::listing();
    }
    
   function insert($ins_data)
   {
     $this->db->insert($this->_table,$ins_data);
     return $this->db->insert_id();
   } 
    
   function update($update_data,$where)
   {
     $this->db->where($where);
     $this->db->update($this->_table,$update_data);
   }
   
   
  function get_pro_plans($where)
  {
    $this->db->select("p.*,t.*,p.id as plan_id,t.id as plan_type_id");
    $this->db->from("plans p");
    $this->db->join("plan_types t","t.plan_id=p.id");
    $this->db->where(array("t.type =" => "app"));
    if(!empty($where)){
      $this->db->where(array("p.id =" => $where));  
    }
    return $this->db->get()->result_array();
  }
  
  function get_all_plans()
  {
    $this->db->select("*");
    $this->db->from($this->_table);
    return $this->db->get()->result_array();
  }
    
   function get_plans()
   {
      $this->db->select('*');
      $this->db->from('user_plan');
      return $this->db->get()->result_array();
   } 
    
   function get_plan_details($where)
   {
	  $this->db->select('*');
      $this->db->from('user_plan');
      $this->db->where($where);
      $query = $this->db->get()->row_array();
      return $query;
   }
   
   function check_unique($where)
   {
     $this->db->select("*");
     $this->db->from($this->_table);
     $this->db->where($where);
     return $this->db->get()->row_array();
   }
   
   function get_access()
   {
     $this->db->select("*");
     $this->db->from("plan_access");
     return $this->db->get()->result_array();
   }
   
   function plan_access_insert($ins_data)
   {
     $this->db->insert("plan_access",$ins_data);
     return $this->db->insert_id();
   }
   
   /*  Get plan all details */
   
   function get_plan_all_datas($where)
   {
	    $this->db->select("p.*,t.*,p.id as plan_id,t.id as plan_type_id");
		$this->db->from("plans p");
		$this->db->join("plan_types t","t.plan_id=p.id");
		//$this->db->join("plan_access a","t.type=a.type");
		$this->db->where($where);
		return $this->db->get()->result_array();  
   }
   
   function get_plan_access($where)
   {
        $this->db->select("*");
        $this->db->from("plan_access");
        $this->db->where($where);
        return $this->db->get()->row_array();
   }
   
   function check_user_plan_exist($where)
   {
		$this->db->select("*");
		$this->db->from("user_selected_plan");
		$this->db->where($where);
		return $this->db->get()->row_array();
   }
   
    function plan_update($table_name,$data,$where)
    { 
        $this->db->where($where);
        return $this->db->update($table_name,$data);
    }
    
     function block_unblock_update($table_name,$data,$where)
    { 
        $this->db->where($where);
        return $this->db->update($table_name,$data);
    }
    
    
    function get_blocked_user($group_id)
    {
		$this->db->select("g.user_id as guser,ug.user_id as uguser,g.join_key as jkey,g.description,g.type,g.location_type,g.id as group_id");
        $this->db->join("groups g","g.id=ug.group_id");
        $this->db->where("ug.group_id",$group_id);
        return $this->db->get("user_groups ug")->result_array();
	}
    
   
   
}
?>

