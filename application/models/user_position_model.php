<?php

class User_Position_Model extends CI_Model {
    
    
    function __construct()
    {
        parent::__construct();
        $this->_table = 'user_position';
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
    
    function check_unique($where)
    {
        $this->db->select();
        $this->db->from($this->_table);
        $this->db->where($where);
        $result = $this->db->get()->row_array();
        return $result;
    }
    
    function lists($where)
    {
        return $this->db->get_where($this->_table,$where)->result_array();
    }
    
    function get_group_users($table,$where)
    {
        $this->db->select("*");
        $this->db->from($table);
        $this->db->where($where);
        $result = $this->db->get()->result_array();
        return $result;
    }
    function get_position($group)
    {
        $this->db->select('user.id,user.default_id,user.display_name,user.is_show,user.show_personal,user.is_contact,user.satiation_id,user.is_overwrite,"'.$group['join_key'].'" as group_join_key,"'.$group['description'].'" as description,user_groups.group_id,user_groups.is_joined,user.phonenumber,user.android_id,user.device_id,user.email,user.profile_image,user_position.lat,user_position.lon,user_groups.status,user_groups.last_seen_time,user.is_tracked,user_groups.is_view,user_position.date_updated,user_position.accuracy,user_position.altitude,user_position.speed,user_position.bearing,user_position.date_updated as lastseen,user_groups.is_visible',false);
        $this->db->from("user_groups");
        $this->db->where("user_groups.group_id",$group['id']);
        $this->db->where('user_groups.blocked',0);  
        $this->db->join('user_position','user_groups.user_id=user_position.user_id');
        $this->db->join('user','user.id=user_position.user_id');
       // $this->db->join('user_plan','user_plan.plan_id=user.plan_id');
       // $whr = '';
     //   if($group['type'] == 'private')
       //    $whr = "AND `user_groups`.`status` = if(user.id!=".$group['user_id'].",1,if(user.id=".$group['user_id'].",1,0))";
           
     //  return  $this->db->query("SELECT user.id, user.default_id, user.display_name, user.is_show, user.show_personal, user.is_contact, user.satiation_id, user.is_overwrite, '".$group['join_key']."' as group_join_key, '".$group['join_key']."' as description, user_groups.group_id, user_groups.is_joined, user.phonenumber, user.android_id, user.device_id, user.email, user.profile_image, user_position.lat, user_position.lon, user_groups.status, user_groups.last_seen_time, user.is_tracked, user_groups.is_view, user_position.date_updated, user_position.accuracy, user_position.altitude, user_position.speed, user_position.bearing, user_position.date_updated as lastseen, user_groups.is_visible FROM (`user_groups`) JOIN `user_position` ON `user_groups`.`user_id`=`user_position`.`user_id` JOIN `user` ON `user`.`id`=`user_position`.`user_id` WHERE `user_groups`.`group_id` = '9' AND `user_groups`.`blocked` = 0 $whr")->result_array();
        return $this->db->get()->result_array();
    }
    
    
    function get_group_active_user_position($group,$user_id)
    {
        $this->db->select('user.id,user.default_id,user.is_show,user.show_personal,user.is_contact,user.display_name,user.satiation_id,user.is_overwrite,"'.$group['join_key'].'" as group_join_key,"'.$group['description'].'" as description,user_groups.group_id,user_groups.is_joined,user.phonenumber,user.email,user.profile_image,user.android_id,user.device_id,user_position.lat,user_position.lon,user_groups.status,user_position.date_updated,user_groups.last_seen_time,user.is_tracked,user_groups.is_view,user_position.accuracy,user_position.altitude,user_position.speed,user_position.bearing,user_groups.is_visible',false);
        $this->db->from("user_groups");
        $this->db->where('user_groups.group_id',$group['id']);
        $this->db->where('user_groups.blocked',0);
        //$this->db->where('user_groups.user_id !=',$user_id);
       // $this->db->where('user_groups.status',1);
        $this->db->join('user_position','user_groups.user_id=user_position.user_id');
        $this->db->join('user','user.id=user_position.user_id');
        //$this->db->join('user_plan','user_plan.plan_id=user.plan_id');
        return $result = $this->db->get()->result_array(); 
    }
    
    function delete_guest_maps(){

       // $this->db->select('id,plan_id,date_created');
        //$this->db->where('login_type',"website");
        //$result = $this->db->get('user')->result_array();
        
        $result = $this->db->query("select id,plan_id,date_created from user where login_type='guest'")->result_array();
        
        $user_ids = array();
        
        if(count($result)>0)
        {
            foreach($result as $res){

                $timediff = strtotime(date('Y-m-d H:i:s')) - $res['date_created'];

                if($timediff > 86400){ 
                    
                    //temporary group delete
                    $this->db->where('user_id',$res['id']);
                    $this->db->delete('groups');

                    //temporary user group delete
                    $this->db->where('user_id',$res['id']);
                    $this->db->delete('user_groups');

                    //temporary user position delete
                    $this->db->where('user_id',$res['id']);
                    $this->db->delete('user_position');

                    //temporary user delete
                    $this->db->where('id',$res['id']);
                    $this->db->delete('user');
                    $user_ids[]= $res['id'];
                }
            }
        }
        
        return $user_ids;
    }

    function update_positions($user_id,$positions)
    {
        $pos = $this->delete_and_get_position_histories($user_id);

        foreach($positions as $val){
            $pos[] = $val;
        }
        $pos = json_encode($pos);

        $this->db->where('user_id',$user_id);
        $this->db->update($this->_table,array('trigger_positions'=> $pos));

        return $this->db->affected_rows();
    }

    function get_trigger_positions($users,$time_limit='')
    {
        $res = array();
       
        foreach($users as $key => $id){

            $pos = $this->delete_and_get_position_histories($id,$time_limit);

            //udate latest one
            if(!empty($pos)){
                $this->db->where('user_id',$id);
                $this->db->update($this->_table,array('trigger_positions'=> json_encode($pos)));

                if(!empty($time_limit) && ($time_limit == 1)){

                    $lim=array();

                    foreach($pos as $val){
                       
                        $timediff = strtotime(date('Y-m-d H:i:s')) - ($val['update_time']/1000);

                        if($timediff <= 600)
                            array_push($lim,$val);
                    }

                    $pos = $lim;

                }
            }    

            $res[$id] = $pos;

        }

        return $res;
    }
    
    function delete_and_get_position_histories($user_id,$time_limit='')
    {
        $this->db->select('user_id,trigger_positions');
        $this->db->where('user_id',$user_id);
        $pos = $this->db->get($this->_table)->row_array();

        $res=array();

        if(is_array($pos) && $pos['trigger_positions']!=''){

            $trigger_pos = json_decode($pos['trigger_positions'],TRUE);

            foreach($trigger_pos as $val){
                
                if($time_limit == 2) {
                    if($val['flag']==0)
                      continue;
                }
                
                $timediff = strtotime(date('Y-m-d H:i:s')) - ($val['update_time']/1000);

                if($timediff <= 86400)
                    //$val['update_time'] = date("Y-m-d H:i:s", $val['update_time']);
                    array_push($res,$val);
            }

        }             

        return $res;
    }
    
}
?>
