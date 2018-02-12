<?php
safe_include("models/app_model.php");
class Promo_Model extends App_model {
    
    
    function __construct() 
    {
        parent::__construct();
        $this->_table = 'promo';
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
                case 'code':
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
   
   function check_unique($where)
   {
     $this->db->select("*");
     $this->db->from($this->_table);
     $this->db->where($where);
     return $this->db->get()->row_array();
   }
   
   function get_all_promos()
   {
        //$this->db->select("*");
        //$this->db->from($this->_table);
        //return $this->db->get()->result_array();
     $this->db->select("p.*,pl.planname");
     $this->db->from("promo p");
     $this->db->join("plans pl","pl.id=p.plan_id");
     return $this->db->get()->result_array();
   }
   
   function get_user_assigned_promos($user_id)
   {
     $this->db->select("up.*,p.code,p.expire as expire_date");
     $this->db->from("user_promos up");
     $this->db->join("promo p","p.id=up.promo_id");
     $this->db->where("up.user_id",$user_id);
     return $this->db->get()->result_array();
   }
}
?>
