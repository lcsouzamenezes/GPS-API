<?php
safe_include("models/app_model.php");
class User_plans_model extends App_model {
    
    function __construct() 
    {
        parent::__construct();
        $this->_table = 'user_selected_plan';
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
   
   
   function check_unique($where)
   {
     $this->db->select("*");
     $this->db->from($this->_table);
     $this->db->where($where);
     return $this->db->get()->row_array();
   }
   
   function user_plan_data($plan_id)
   {
     $this->db->select("p.planname as pname,pt.name,pt.description,pt.type,pt.participant_count,pt.cost");
     $this->db->from("plans p");
     $this->db->join("plan_types pt","pt.plan_id=p.id");
     $this->db->join("user_selected_plan us","us.plan_id=pt.plan_id");
     $this->db->where("us.plan_id",$plan_id);
     $this->db->where("pt.type","app");
     $this->db->group_by("pt.plan_id");
     return $this->db->get()->result_array();
     
   }
}
?>

