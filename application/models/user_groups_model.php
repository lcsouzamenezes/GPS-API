<?php

class User_Groups_Model extends CI_Model {
    
    
    function __construct()
    {
        parent::__construct();
        $this->_table = 'user_groups';
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
        return $this->db->delete($this->_table);
    }
    
    function check_unique($where)
    {
        $this->db->select("*");
        $this->db->from($this->_table);
        $this->db->where($where);
        $result = $this->db->get()->row_array();
        return $result;
    }
    
    function get_members_count($join_key)
    {
        $this->db->select("*");
        $this->db->join("groups g","g.id=ug.group_id");
        $this->db->where("g.join_key",$join_key);
        return $this->db->get("user_groups ug")->result_array(); 
    }
    
    function get_user_gcm($group_id)
    {
        $this->db->select("user.id,user.gcm_id,user.is_tracked,user.default_id,groups.join_key");
        $this->db->where("user_groups.group_id",$group_id);
        $this->db->where("user_groups.status",1);
        $this->db->join("groups","groups.id=user_groups.group_id");
        $this->db->join("user","user.id=user_groups.user_id");
        return $this->db->get("user_groups")->result_array();        
    } 
    
    function get_user_groups($user_id)
    {
        $this->db->select("ug.*,g.location_type,g.lat,g.lon,g.id as gid");
        $this->db->from("user_groups ug");
        $this->db->join("groups g","g.id=ug.group_id");
        $this->db->where("ug.user_id",$user_id);
        return $this->db->get()->result_array();
    }
    
    function get_user_channels($user_id)
    {
        $this->db->select("g.user_id as guser,ug.user_id as uguser,g.join_key as jkey,g.description,g.type,g.location_type,g.id as group_id");
        $this->db->join("groups g","g.id=ug.group_id");
        $this->db->where("ug.user_id",$user_id);
        return $this->db->get("user_groups ug")->result_array();
    }
    
    function get_groups_member_count($where)
    {  
        return $this->db->get_where($this->_table,$where)->num_rows();
    }
    
    function get_favourite_groups($user_id)
    {
        $this->db->select('groups.id,groups.description,groups.join_key,groups.type,groups.name,groups.user_id,groups.location_type,user.default_id,user.profile_image,user.login_type,user_groups.is_favourite,user_groups.status,user_groups.last_seen_time,IF(groups.user_id=user_groups.user_id,"admin","member") as user_type',false);
        $this->db->where("user_groups.user_id",$user_id);
        $this->db->where("user_groups.is_favourite",1);
        $this->db->join('groups','groups.id=user_groups.group_id');
        $this->db->join('user','user.id=groups.user_id');   
        return $this->db->get('user_groups')->result_array(); 
    }
    
    function get_joined_groups($user_id)
    {
        $this->db->select('groups.id,groups.description,groups.join_key,groups.type,groups.name,groups.password_protect,groups.password,groups.lat,groups.lon,groups.allow_deny,groups.user_id,groups.location_type,user.default_id,user.profile_image,user.login_type,user_groups.is_favourite,user_groups.status,user_groups.last_seen_time,IF(groups.user_id=user_groups.user_id,"admin","member") as user_type',false);
        $this->db->where("user_groups.user_id",$user_id);
        $this->db->where("user_groups.is_joined",1);
        $this->db->join('groups','groups.id=user_groups.group_id');
        $this->db->join('user','user.id=groups.user_id');
       // $this->db->join('user_plan','user_plan.plan_id=user.plan_id');
        return $this->db->get('user_groups')->result_array(); 
    }
    
    function get_user_active_group($user_id)
    {
        $this->db->select("groups.id,groups.join_key,groups.type,user_groups.is_view,groups.description,groups.lat,groups.lon,groups.location_type,groups.date_created,groups.password_protect,groups.allow_deny,groups.password");
        $this->db->where("user_groups.user_id",$user_id);
        $this->db->where("user_groups.status",1);
        $this->db->join("groups","groups.id=user_groups.group_id");
        return $this->db->get('user_groups')->row_array(); 
    }
    
    function get_groups_member($group_id,$user_id)
    {
        $this->db->select("user_groups.*,user.id as userId,user.default_id,user.gcm_id,user.login_type");
        $this->db->where("user_groups.group_id",$group_id);
        $this->db->where("user_groups.user_id !=",$user_id);
        $this->db->join("user","user.id=user_groups.user_id");
        return $this->db->get($this->_table)->result_array();
    }
    
    function get_user_info($join_key,$user_id)
    {
        $this->db->select('user.id,user.default_id,user.display_name,user_groups.group_id,user_groups.is_joined,user.phonenumber,user.android_id,user.device_id,user.email,user.profile_image,user_position.lat,user_position.lon,user_groups.status,user_plan.plan_name,user_groups.last_seen_time,user.is_tracked,user_groups.is_view,user_position.accuracy,"'.$join_key.'" as group_name,groups.description',false);
        $this->db->where("groups.join_key",$join_key);
        $this->db->where("user_groups.user_id",$user_id);
        $this->db->join("groups","groups.id=user_groups.group_id");
        $this->db->join("user_position","user_position.user_id=user_groups.user_id");        
        $this->db->join("user","user.id=user_position.user_id");
        $this->db->join('user_plan','user_plan.plan_id=user.plan_id');
        return $this->db->get('user_groups')->result_array();
    }

    function get_active_group($user_id){
        $this->db->select('user.id as user_id,user.phonenumber,user.updated_type,user.updated_phonenumber,groups.id as group_id,groups.join_key as channel_id,groups.description as display_name,user.display_name as udispname',false);
        $this->db->join("user","user.id=groups.user_id");
        $this->db->where("user.id",$user_id);
        return $this->db->get('groups')->row_array();
    }
    
    //get participant lists
    function get_participant_lists($group_id)
    {
        $this->db->select("u.*");
        $this->db->join("user_groups ug","ug.user_id=u.id");
        $this->db->join("groups g","g.id=ug.group_id");
        $this->db->where("g.id",$group_id);
        return $this->db->get('user u')->result_array();
    }
    
    //get user joined channels
    function get_joined_channels($user_id)
    {
        $this->db->select("g.*,g.id as gid,g.join_key,g.user_id as admin_id,g.user_id,g.id as id,IF(ug.user_id=".$user_id.",'join_group','notjoin') as jointype,ug.last_seen_time",false);
        $this->db->from("user_groups ug");
        $this->db->join("groups g","g.id=ug.group_id");
        $this->db->where("ug.user_id",$user_id);
        $this->db->order_by("g.id","asc");
        return $this->db->get()->result_array();
    }
    
   
}
?>