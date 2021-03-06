<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller {
    
    function __construct(){
        parent::__construct();
        $this->load->model("user_model");
    }

    function index()
    {
      //print_r($_SERVER);
      echo phpinfo();
           
    }

    function changepassword() 
    {
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('new_password', 'Password', 'required|matches[confirm_password]');
        $this->form_validation->set_rules('confirm_password', 'Password Confirmation', 'required');
        
        if ($this->form_validation->run() == true){
           // echo "success"; exit;
            
		    $password  = (isset($_POST['new_password']) && !empty($_POST['new_password']))?$_POST['new_password']:"";
            $cpassword = (isset($_POST['confirm_password']) && !empty($_POST['confirm_password']))?$_POST['confirm_password']:"";
            //$user_id   = (isset($_GET['id']) && !empty($_GET['id']))?base64_decode($_GET['id']):"";
            $user_id   = (isset($_GET['id']) && !empty($_GET['id']))?$_GET['id']:"";
            $password  = md5($password);
        
            if(!empty($user_id)) {
               
                $ins_data = array();
                $ins_data['password'] = $password;
                
                $this->user_model->update("user",$ins_data,array("id" => $user_id));
            }
            
            $this->data['return_message'] = 'Your password has been updated. You can login with new password.';
            
		 }
		
        $time    = (isset($_GET['expire_time']) && !empty($_GET['expire_time']))?$_GET['expire_time']:"";
        $date    = strtotime(date("Y-m-d h:i:s"));
        $diff    = round((abs($date-$time)/3600),2);
       
        if($diff >= 1) {
           $this->data['return_message'] = "This link has been expired.so give request again you getting new email."; 
        } 
        
        $this->data['user_id'] = $user_id;
        $this->load->view("change_password",$this->data);
    }
    
    function delete_guest_maps()
    {
      $this->load->model("user_position_model");
      $ids = $this->user_position_model->delete_guest_maps();
      $this->db->insert('cron_test',array('data'=> json_encode($ids)));

    } 

    function test()
    {
        echo date('Y-m-d H:i:s')."=====".strtotime(date('Y-m-d H:i:s'))."<br/>";

        $timestamp = time()+date("Z");
        echo date("Y-m-d H:i:s",$timestamp);
    }


    function location($key = ""){

        $this->load->model("group_model");
        
        if(empty($key)) 
        {
            show_404();die;
        }
        
        $result = $this->group_model->check_unique(array("join_key" => $key));
        if($result['location_type'] == 'static'){
          $lon = $result['lon'];
          $lat = $result['lat'];
        }
        else
        {
            $res = $this->group_model->get_user_position($key);
            
            $lon = $res['lon'];
            $lat = $res['lat'];
        }
        
        if(empty($result)) {
             show_404();die;
        }
        
        
        //$url =  "http://maps.google.com/maps?daddr=$lat,$lon";
        $url = "http://maps.google.com/maps?z=12&t=m&q=loc:$lat+$lon";
        redirect($url);die;
    }
    
}

/* End of file user.php */
/* Location: ./application/controllers/user.php */