<?php
safe_include("models/app_model.php");
class Plantype_Model extends App_model {
    
    
    function __construct() 
    {
        parent::__construct();
        $this->_table = 'plan_types';
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
                case 'name':
                    $this->db->like($key, $value);
                break;
                case 'cost':
                    $this->db->like($key, $value);
                break;
                case 'type':
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
  
   function get_all_types()
   {
     $this->db->select("t.*,p.planname");
     $this->db->from("plan_types t");
     $this->db->join("plans p","p.id=t.plan_id");
      return $this->db->get()->result_array();
   }
}
?>
