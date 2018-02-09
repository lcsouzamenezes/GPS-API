<?php
require APPPATH.'/libraries/REST_Controller.php';

class Service extends REST_Controller
{
       protected $profile_url = 'http://heresmygps.com/assets/uploads/profile/resize';
 
        function __construct()
        {
            // Construct our parent class
            parent::__construct();
            
            $this->load->model(array("user_model","plan_model","group_model","user_position_model","user_groups_model","user_plans_model","promo_model"));
            
            $key  = $this->get('X-APP-KEY'); 
            $user = $this->get('login_uid');

            if($key!='' && $user!='')
            {
                $user_device = $this->db->query("select * from user where id='".$user."'")->row_array();

                if(is_array($user_device) && $user_device['device_id']!=''){

                   echo $res = $this->db->query("select * from user where device_id='".$key."' AND id='".$user."'")->num_rows();

                    if($res==0){
                        return $this->response(array('status' => 'logout','msg' => 'You are logged in to other device','error_code' => 1), 404);
                    }    

                }    
            }
        }
       
        function register_app_get() {
            
            //check for required values
            $app_id = $this->get('app_id');
        if(empty($app_id)){
          return $this->response(array('status' => 'error','msg' => 'Required fields missing in your request','error_code' => 1), 404);
        }
            $this->load->config("rest");
            
            $key_table = $this->config->item('rest_keys_table');
            
            $result =  $this->db->get_where($key_table,array('key' => $app_id))->num_rows();
                
            if(empty($result)){
             $this->db->insert($key_table,array('key' => $app_id,'custom_key'=>1,'date_created' => strtotime(date('Y-m-d'))));
                 
                 if($this->db->insert_id()) {
                     return $this->response(array('status' =>'success','api_key' => $app_id), 200);
                 }
                 else
                 {
                     return $this->response(array('status' => "error",'msg' => 'Unknown Error Occurred!! Try Again...','error_code' => 2), 404);
                 }
        }
            else
            {
                  return $this->response(array('status' =>'success','api_key' => $app_id), 200);    
            }
            
        }
        
        function user_register_get() {
             
            $default_id     = $this->get('default');
            $phonenumber    = $this->get('phonenumber');
            $display_name   = $this->get('display_name');
            $email          = $this->get('email');
            $profile_image  = $this->get('profile_image');
            $user_plan      = $this->get('user_plan');
            $password       = $this->get('password');
            $android_id     = $this->get('android_id');
            $device_id      = $this->get('device_id');
            $android_id     = (!empty($android_id))?$android_id:"";
            $device_id      = (!empty($device_id))?$device_id:"";
            $password       = ($password!='')?md5($password):'';
            $logintype      = $this->get('login_type');
            $pass_protect   = $this->get('password_protect');
            $allow_deny     = $this->get('allow_deny');
            $upd_type       = $this->get('updated_type');
            $upd_phone      = $this->get('updated_phonenumber');
            
            $display_group_name = $default_id;
            $phone_group_name   = $phonenumber;
            
            if($logintype == 'hmgps'){
                $cresult = $this->user_model->check_unique(array("phonenumber" => $phonenumber,"password" => ""));
                if(count($cresult)){
                    $this->user_model->delete("user",array("id" => $cresult['id']));
                    $this->user_model->delete("user_position",array("user_id" => $cresult['id']));
                    $this->user_model->delete("groups",array("user_id" => $cresult['id']));
                    $this->user_model->delete("user_groups",array("user_id" => $cresult['id']));
                }
            }  
                       
            //get plan id
            $plan    = $this->plan_model->get_plan_details(array("plan_type"=>$user_plan));
            $plan_id = $plan['plan_id'];
            
            $source_image   = "var/www/html/heresmygps/assets/uploads/profile/".$profile_image;
            $image_crop_url = "var/www/html/heresmygps/assets/uploads/profile/resize";
            
            //check if phonenumber already exist or not for guest user
            //$result = $this->user_model->check_unique(array("phonenumber" => $phonenumber));
//            
//            if(count($result)) {
//                
//                $plan_id = (empty($result['plan_id']))?1:$result['plan_id'];
//                
//                 //As a guset user again register with FREE(HMGPS user) version
//                if($plan_id==2){
//
//                    if($email) {
//                      //Email check
//                      $email_res = $this->user_model->check_unique(array("email" => $email));
//                      if(!empty($email_res)) {
//                        return $this->response(array('status' => "error",'msg' => 'Email already exist.','error_code' => 112), 404);
//                      }
//                    }
//                   
//                    if($default_id){
//                       //default ID check
//                       $default_res = $this->user_model->check_unique(array("default_id" => $default_id));
//                        if(!empty($default_res)) {
//                            return $this->response(array('status' => "error",'msg' => 'Default ID already exist.','error_code' => 103), 404);
//                        }
//                    }
//                     if($display_group_name){
//                       //default ID group check
//                        $default_group = $this->group_model->check_unique(array("join_key" => $display_group_name));
//                        if(!empty($default_group)) {
//                            return $this->response(array('status' => "error",'msg' => 'Default ID Group already exists','error_code' => 103), 404);
//                        }
//                    }
//                    
//                    $ins_data = array();
//                    $ins_data['default_id']     = (!empty($default_id))?$default_id:$phonenumber;
//                    $ins_data['display_name']   = (!empty($display_name))?$display_name:$phonenumber;
//                    $ins_data['password']       = $password;
//                    $ins_data['phonenumber']    = $phonenumber;
//                    $ins_data['email']          = $email;
//                    $ins_data['profile_image']  = $profile_image;
//                  //  $ins_data['plan_id']        = $plan_id;
//                    $ins_data['date_updated']   = strtotime(date('Y-m-d H:i:s'));
//                    $ins_data['android_id']     = $android_id;
//                    $ins_data['device_id']      = $device_id;
//                    $ins_data['logged_in']      = 0;
//                    $ins_data['is_show']        = 1;
//                    $ins_data['show_personal']  = 1;
//                    $ins_data['is_contact']     = 1;
//                    $ins_data['satiation_id']   = (!empty($default_id))?$default_id:$phonenumber;
//                    $ins_data['updated_type']   =  $upd_type;
//                    $ins_data['updated_phonenumber']   =  $upd_phone;
//                    $this->user_model->update('user',$ins_data,array('id' => $result['id']));
//                        
//                    if(!empty($profile_image)) {
//                        $image_name = $result['id'].".jpg";
//                       // image_crop($source_image,$image_crop_url,$image_name);
//                    }
//                        
//                    //create group by through default id
//                    if(!empty($default_id)) {
//                        $default_group = create_group($default_id,$result['id'],'default',$pass_protect,$allow_deny);
//                    }
//                    $file_exists  = "./assets/uploads/profile/resize/large_".$result['id'].".jpg";
//                    $profileImage = (file_exists($file_exists))?site_url()."assets/uploads/profile/resize/large_".$result['id'].".jpg":'http://heresmygps.com/assets/images/no_image.png';
//
//                    $this->user_model->update("user",array('logged_in'=>1),array('id' => $result['id'])); 
//                    
//                    //check if user selected plan or not
//                    $plan_data = get_plan_data($result['id']); 
//                    
//                    return $this->response(array('status' =>'success','request_type' => 'create_account','user_id' => $result['id'], 'default_id' => $default_id, 'phonenumber' => $phonenumber, 'display_name' => $default_id, 'email' => $email, 'profile_image' => $profileImage,'plan_selected' => (count($plan_data)>0)?"yes":"no"), 200);
//                
//                }
//
//                 //update logged in status to 1
//                 $ins_data = array();
//
//                if($this->get('X-APP-KEY')!=$result['device_id']){
//
//                    $ins_data['device_id'] = $this->get('X-APP-KEY');
//                }
//
//                 $ins_data['logged_in'] = 1;
//                 $this->user_model->update("user",$ins_data,array('id' => $result['id']));  
//                 
//                  //check if user selected plan or not
//                  $plan_data = get_plan_data($result['id']); 
//                  
//                return $this->response(array('status' =>'success','request_type' => 'guest_login','user_id' => $result['id'], 'default_id' => $result['default_id'], 'phonenumber' => $result['phonenumber'], 'android_id' => $result['android_id'], 'display_name' => $result['display_name'], 'plan_selected' => (count($plan_data)>0)?"yes":"no"), 200);
//               
//            }
//            else
//            {
//              
                if($email) {
                  //Email check
                  $result = $this->user_model->check_unique(array("email" => $email));
                  if(!empty($result)) {
                    return $this->response(array('status' => "error",'msg' => 'Email already exist.','error_code' => 112), 404);
                  }
               }
               
               if($default_id){
                   //default ID check
                   $result = $this->user_model->check_unique(array("default_id" => $default_id));
                    if(!empty($result)) {
                        return $this->response(array('status' => "error",'msg' => 'Default ID already exist.','error_code' => 103), 404);
                    }
                }
               
                 if($phonenumber && ($logintype =='hmgps')){
                    //phone number check
                   $result = $this->user_model->check_unique(array("phonenumber" => $phonenumber));
                   
                    if(!empty($result)) {
                        return $this->response(array('status' => "error",'msg' => 'Phonenumber already exist.','error_code' => 106), 404);
                        }
                    }
                    
                    if($display_group_name){
                       //default ID group check
                        $default_group = $this->group_model->check_unique(array("join_key" => $display_group_name));
                        if(!empty($default_group)) {
                            return $this->response(array('status' => "error",'msg' => 'Default ID Group already exists','error_code' => 103), 404);
                        }
                    }
                    
                     if($phone_group_name && ($logintype =='hmgps') ){
                       //phonenumber group check
                       $phone_group = $this->group_model->check_unique(array("join_key" => $phone_group_name));
                        if(!empty($phone_group)) {
                            return $this->response(array('status' => "error",'msg' => 'Phonenumber Group already exists.','error_code' => 103), 404);
                        }
                    }
                    

                    $ins_data = array();
                    $ins_data['default_id']     = (!empty($default_id))?$default_id:$phonenumber;
                    $ins_data['display_name']   = (!empty($display_name))?$display_name:$phonenumber;
                    $ins_data['password']       = $password;
                    $ins_data['phonenumber']    = $phonenumber;
                    $ins_data['email']          = $email;
                    $ins_data['profile_image']  = $profile_image;
                   // $ins_data['plan_id']        = $plan_id;
                    $ins_data['is_tracked']     = 1;
                    $ins_data['date_created']   = strtotime(date('Y-m-d H:i:s'));
                    $ins_data['android_id']     = $android_id;
                    $ins_data['device_id']      = $device_id;
                    $ins_data['logged_in']      = 0;
                    $ins_data['is_show']        = 1;
                    $ins_data['show_personal']  = 1;
                    $ins_data['is_contact']     = 1;
                    $ins_data['login_type']     = (!empty($logintype))?$logintype:'app';
                    $ins_data['satiation_id']   = (!empty($default_id))?$default_id:$phonenumber;
                    $ins_data['updated_type']   =  $upd_type;
                    $ins_data['updated_phonenumber']   =  $upd_phone;
                    $user_id = $this->user_model->insert('user',$ins_data);
                    
                    $plan_id = (empty($result['plan_id']))?1:$result['plan_id'];
                   
                    if(!empty($profile_image)) {
                         $image_name = $user_id.".jpg";
                       // image_crop($source_image,$image_crop_url,$image_name);
                    }
                        
                    //create group by through default id, phonenumber
                    if(!empty($default_id)) {
                        $default_group = create_group($default_id,$user_id,'default',$pass_protect,$allow_deny);
                     }
                     if(!empty($phonenumber) && ($logintype == 'hmgps')) {
                        $phone_group    = create_group($phonenumber,$user_id,'phonenumber',$pass_protect,$allow_deny);
                     }
                     
                     $file_exists  = "./assets/uploads/profile/resize/large_".$user_id.".jpg";
                     
                     $profileImage = (file_exists($file_exists))?site_url()."assets/uploads/profile/resize/large_".$user_id.".jpg":'http://heresmygps.com/assets/images/no_image.png';
                     
                      //check if user selected plan or not
                     $plan_data = get_plan_data($user_id);
                     
                    if($user_id) {
                        $this->user_model->update("user",array('logged_in'=>1),array('id' => $user_id)); 
                        
                        return $this->response(array('status' =>'success','request_type' => 'create_account','user_id' => $user_id, 'default_id' => $default_id, 'phonenumber' => $phonenumber, 'display_name' => $default_id, 'email' => $email, 'profile_image' => $profileImage, 'plan_selected' => (count($plan_data)>0)?"yes":"no"), 200);
                    }
                    else
                    {
                        return $this->response(array('status' => "error",'request_type' => 'create_account','msg' => 'Unknown Error Occurred!! Try Again...','error_code' => 2), 404);
                    }
                
              //  }
        }
           
        
        function login_get() {
            
            $val     = $this->get('val');
            $pass    = $this->get('password');
            //check for required values
        if($val=='' || $pass == ''){
          return $this->response(array('status' => 'error','msg' => 'Required fields missing in your request','error_code' => 1), 404);
        }
            
            
            $pass = md5($pass);
            $result = $this->db->query("select * from user where default_id='".$val."' or phonenumber='".$val."' or email='".$val."'")->row_array();  
            
            /*
            if($result['logged_in'] == 1) {
                return $this->response(array('status' => "error",'msg' => 'You are alreadly logged in another device.','error_code' => 6), 404);
            }
            */
            
            if(!count($result)) {
                return $this->response(array('status' => "error",'msg' => 'Invalid Username.','error_code' => 4), 404);    
            }
            else
            {
                $file_exists = "./assets/uploads/profile/resize/large_".$result['id'].".jpg";
                
                if(isset($result['password']) && ($result['password'] == $pass)) {
                     $default_id        = (!empty($result['default_id']))?$result['default_id']:"";
                     $phonenumber       = (!empty($result['phonenumber']))?$result['phonenumber']:"";
                      $profile_image     = (file_exists($file_exists))?site_url()."assets/uploads/profile/resize/large_".$result['id'].".jpg":'http://heresmygps.com/assets/images/no_image.png';
                   //  $profile_image     =  site_url()."assets/uploads/profile/resize/large_".$result['id'].".jpg";
                     $email             = (!empty($result['email']))?$result['email']:"";
                     $android_id        = (!empty($result['android_id']))?$result['android_id']:"";
                     $device_id         = (!empty($result['device_id']))?$result['device_id']:"";
                     $plan              = $this->plan_model->get_plan_details(array("plan_id" => $result['plan_id']));
                     $plan              = $plan['plan_type'];
                     $plan_id           = $result['plan_id'];
                     
                     //update logged in status to 1
                     $ins_data = array();

                     //update device id while login to another device
                     if($result['device_id'] != $this->get('X-APP-KEY')){
                        $ins_data['device_id'] = $this->get('X-APP-KEY');
                        $device_id = $this->get('X-APP-KEY');
                      }  
                     
                     $ins_data['logged_in'] = 1;
                     $this->user_model->update("user",$ins_data,array('id' => $result['id']));    
                     
                     //check if user selected plan or not
                     $plan_data = get_plan_data($result['id']);
                          
                    return $this->response(array('status' =>'success','request_type' => 'login','default_id' => $default_id,'display_name' => $result['display_name'],'phonenumber' => $phonenumber,'user_id' => $result['id'],'email' => $email,'profile_image' => $profile_image,'android_id' => $android_id,'device_id' => $device_id, 'plan' => $plan_id, 'display_name' => $result['display_name'], 'is_show' => $result['is_show'], 'is_contact' => $result['is_contact'], 'plan_selected' => (count($plan_data)>0)?"yes":"no"), 200);
                }
                else
                {
                    return $this->response(array('status' => "error",'request_type' => 'login', 'msg' => 'Login faild.','error_code' => 108), 404);
                }
            }
        }
        
        
        function social_login_get(){
            
            //check for required values
            $email     = $this->get('email');
            $user_plan = $this->get('user_plan');
            
        if(!$this->get('email') || !$this->get('profile_image')) {
          return $this->response(array('status' => 'error','msg' => 'Required fields missing in your request','error_code' => 1), 404);
        }
                        
            $where = array("email" => $email);
            $result = $this->db_model->get_user_details($where);
             
             $file_exists       = "./assets/uploads/profile/resize/large_".$result['id'].".jpg";
             $default_id        = (!empty($result['default_id']))?$result['default_id']:"";
             $phonenumber       = (!empty($result['phonenumber']))?$result['phonenumber']:"";
             $profile_image     = (empty($result['profile_image']) && (file_exists($file_exists)))?$this->profile_url."large_".$result['id'].".jpg":'http://heresmygps.com/assets/images/no_image.png';
             $email             = (!empty($result['email']))?$result['email']:"";
             $android_id        = (!empty($result['android_id']))?$result['android_id']:"";
             $device_id         = (!empty($result['device_id']))?$result['device_id']:"";
             $user_id           = (!empty($result['id']))?$result['id']:""; 
           
            if(!empty($result)) {  
                
                /*
                 if($result['logged_in'] == 1) {
                     return $this->response(array('status' => "error",'msg' => 'You are alreadly logged in another device.','error_code' => 6), 404);
                 }
                */ 
          //return $this->response(array('status' =>'success',$this->_prepare_user_details($result)), 200);
                 
                 
                //update logged in status to 1
                 $ins_data = array();

                 //update device id while login to another device
                 if($result['device_id']!=$this->get('X-APP-KEY')){

                    $ins_data['device_id'] = $this->get('X-APP-KEY');
                    $device_id = $this->get('X-APP-KEY');
                  }
                    
                 $ins_data['logged_in'] = 1;
                 $this->user_model->update("user",$ins_data,array('id' => $result['id']));
                 
                 $plan_id = (empty($result['plan_id']))?1:$result['plan_id'];
                 
                 //check if user selected plan or not
                  $plan_data = get_plan_data($result['id']); 
                     
           return $this->response(array('status' =>'success','default_id' => $default_id,'phonenumber' => $phonenumber,'user_id' => $result['id'],'email' => $email,'profile_image' => $profile_image,'android_id' => $android_id,'device_id' => $device_id,'display_name' => $result['display_name'],'is_show' => $result['is_show'],'is_contact' => $result['is_contact'],'plan_selected' => (count($plan_data)>0)?"yes":"no"), 200);
            }
            else
            {
                
            
                //get plan id
                $plan    = $this->plan_model->get_plan_details(array("plan_type"=>$user_plan));
                $plan_id = $plan['plan_id'];
                
                $default_id = "";
                
                $profile_image = $this->get('profile_image');
                
              if($this->get('email')){
                   
                   $result = $this->user_model->check_unique(array("email" => $email));
                    if(empty($result)) {
                       $email = $this->get('email');
                    }
                    else
                    {
                         return $this->response(array('status' => "error", 'request_type' => 'social_login','msg' => 'Email Already exist.','error_code' => 112), 404);
                    }
                }
                if($this->get('default')) {
                   //default ID check
                    $result = $this->user_model->check_unique(array("default_id" => str_to_lower($this->get('default'))));
                    if(empty($result)) {
                       $default_id = $this->get('default');
                    }
                    else
                    {
                         return $this->response(array('status' => "error", 'request_type' => 'social_login','msg' => 'Default Id Already exist.','error_code' => 112), 404);
                    }
                 }
                 
                 $ins_data = array('default_id' => $default_id,'display_name' => $default_id,'phonenumber' => $phonenumber,'email' => $email, 'profile_image' => $profile_image, 'plan_id' => $plan_id);
                 $ins_data['device_id'] = $this->get('X-APP-KEY');
                 $ins_data['date_created']   = strtotime(date('Y-m-d H:i:s'));  
                 $ins_data['login_type']     = 'social'; 
                 $ins_data['is_show']        = 1;
                 $ins_data['is_contact']     = 1;
                 $ins_data['satiation_id']   = (!empty($default_id))?$default_id:$phonenumber;
                 $user_id = $this->user_model->insert("user",$ins_data);
                 
                 $file    = FCPATH."assets/uploads/profile/social.jpg";
                 $current = file_get_contents($profile_image);
              
                 file_put_contents($file, $current, FILE_APPEND);
                 
                 $image_crop_url = "var/www/html/heresmygps/assets/uploads/profile/resize";
                 $source_image   = "var/www/html/heresmygps/assets/uploads/profile/social.jpg";
                 $image_name     = $user_id.".jpg";
                // image_crop($source_image,$image_crop_url,$image_name);
                 
                 //create group
                 $pass_protect = $this->get('password_protect');
                 $allow_deny   = $this->get('allow_deny');
                 
                 create_group($default_id,$user_id,'default',$pass_protect,$allow_deny);
                  
                 if(!$user_id) {
                    return $this->response(array('status' => "error",'request_type' => 'social_login','msg' => 'Unknown Error Occurred!! Try Again...','error_code' => 2), 404);
                 }
                 
                 $plan_id = (empty($result['plan_id']))?1:$result['plan_id'];
                 
                 //check if user selected plan or not
                  $plan_data = get_plan_data($user_id); 
                 
                 $pimage = $this->profile_url."/large_$user_id.jpg";
                 return $this->response(array('status' =>'success','request_type' => 'social_login','default_id' => $default_id,'display_name' => $default_id,
                 'phonenumber' => $phonenumber,'user_id' => $user_id, 'profile_image' => $pimage, 'email' => $email,'is_show' => $result['is_show'],'is_contact' => $result['is_contact'],'plan_selected' => (count($plan_data)>0)?"yes":"no"), 200);
            }
            
        }
        
         //forgot password
        
        function forgot_password_get(){
            
            if(!$this->get('email') && !$this->get('appkey')) {
                return $this->response(array('status' => 'error', 'msg' => 'Required fields missing in your request', 'error_code' => 1), 404);
            }
            
            $email  = $this->get('email');
            $appkey = $this->get("appkey");
            
                $result = $this->db->query("select * from user where email='".$email."'")->row_array();
                if(count($result)) {
                    
                      
                     
            $config['protocol']  = 'sendmail';
            $config['mailpath'] = '/usr/sbin/sendmail';
            $config['charset'] = 'iso-8859-1';
            $config['wordwrap'] = TRUE;
            $config['mailtype'] = "html";

                     
                   $this->load->library('email',$config);
                    
                    // $default      = md5($result['default_id']);
                     //$user_id      = base64_encode($result['id']);
                     $user_id = $result['id'];
                     $current_time = strtotime(date("Y-m-d H:i:s"));
                     $this->data['fpwd_url'] = site_url()."user/changepassword?id=$user_id&expire_time=$current_time";
                     $this->data['username'] = $result['default_id'];
                     $message = $this->load->view("email_template/send_email_template",$this->data,true);
                   //   $message  = "<html>";
                     // $message .= "<body>";
                   //   $message .= "<p>Hi $username,</p><br/>";
                   //   $message .= "<p>Please click below link to reset your password.</p><br/>";
                   //   $message .= "<p><a href='".$fpwd_url."' title='Reset Your Password'>Click Here</a></p><br/><br/>";
                   //   $message .= "<p>Thanks,<p>";
                   //   $message .= "<p><a href='http://911gps.me'>911gps.me</a></p>"; 
                   //   $message .= "</body></html>";

          // $this->email->set_newline("\r\n"); 
          // $this->email->set_header('MIME-Version', '1.0; charset=utf-8'); 
          // $this->email->set_header('Content-type', 'text/html'); 
                  //print_r($message); exit;

                     $this->email->clear(TRUE);
                     $this->email->set_newline("\r\n");   
                     $this->email->from('contact@heresmygps.com','Contact');
                     $this->email->to($email);
                     $this->email->subject('Forgot Password');
                     $this->email->message($message);
                     $this->email->send();
                    
                    return $this->response(array('status' =>'success','request_type' => 'forgot_password','email' => $email,'user_id' => $result['id']), 200);
                }
                else
                {
                    return $this->response(array('status' => "error",'request_type' => 'forgot_password','msg' => 'Invalid Mail ID.','error_code' => 11), 404);
                }
        }
        
       
         function _prepare_user_details($user_data = array(),$user_id = 0) {
            
            $result = array();
            $result['user']['profile'] = $result['user']['account'] = array();
            $user_profile_fields = array('default_id','id','phonenumber','email','profile_image','android_id','device_id');
            $user_account_fields = array('plan_name');
            
            if(!empty($user_data)) {
                
                foreach($user_profile_fields as $field) {
                    $result['user']['profile'][$field] = (isset($user_profile_fields[$field]))?$user_profile_fields[$field]:"";
                }
                
                foreach($user_account_fields as $field) {
                    $result['user']['account'][$field] = (isset($user_account_fields[$field]))?$user_account_fields[$field]:"";
                }
            }
            else if(empty($user_data) && $user_id ) {
                
                $this->_prepare_user_details( $this->db_model->get_user_details(array('id' => $user_id),implode(',',$user_profile_fields)) );
            }
            
            return $result;
        }
        
        
      
        function user_profile_save_get() {
            
               //check for required values
        if(!$this->get('phonenumber')) {
          return $this->response(array('status' => 'error','msg' => 'Required fields missing in your request','error_code' => 1), 404);
        }
            $default_id   = $this->get('default');
            $phonenumber  = $this->get('phonenumber');
            $user_id      = $this->get('user_id');
            $email        = $this->get('email');
            $password     = $this->get('password');
            $profile_image= $this->get('profile_image');
            $display_name = $this->get('display_name');
            $image_type   = $this->get('image_type');

            $source_image   = "var/www/html/heresmygps/assets/uploads/profile/".$profile_image;
            $image_crop_url = "var/www/html/heresmygps/assets/uploads/profile/resize";
                        
            $ins_data = array();
            
            if(!empty($email)) {
              //Email check
               if(!empty($user_id))
                $this->db->where('id != ',$user_id);
              $this->db->where('lower(email)',str_to_lower($email));
              $res_email = $this->db->get('user')->row_array();
            
              $ins_data['email']        = (!empty($email))?$email:$res_email['email'];
                
              if(count($res_email)) {
                return $this->response(array('status' => "error",'request_type' => 'user_profile','msg' => 'Email already exist.','error_code' => 112), 404);
              }
           }
           
           if(!empty($default_id)){
               //default ID check
               if($user_id)
                    $this->db->where('id != ',$user_id);
                    
               $this->db->where('lower(default_id)',str_to_lower($default_id));
               $res_default =  $this->db->get('user')->row_array();
               
                $ins_data['default_id']   = (!empty($default_id))?$default_id:$res_default['default_id'];
                
                if(count($res_default)) {
                    return $this->response(array('status' => "error",'request_type' => 'user_profile','msg' => 'Default ID already exist.','error_code' => 103), 404);
                }
            }
            
            if(!empty($phonenumber)){
                //phone number check
                 if($user_id)
                    $this->db->where('id != ',$user_id);
                
                $this->db->where('phonenumber',$phonenumber);    
                $res_phonenumber =  $this->db->get('user')->row_array();
                
                $ins_data['phonenumber']  = (!empty($phonenumber))?$phonenumber:$res_phonenumber['phonenumber'];
                
                if(count($res_phonenumber)) {
                    return $this->response(array('status' => "error",'request_type' => 'user_profile','msg' => 'Phonenumber already exist.','error_code' => 106), 404);
                }
            }
             
             $ins_data['date_updated'] = strtotime(date('Y-m-d H:i:s'));
             
             if(!empty($password)) {
                $ins_data['password'] = md5($password);
             }
             
             if(!empty($profile_image) && !empty($image_type) && ($image_type == 'new')) {
                 $ins_data['profile_image'] = $profile_image;
                 $image_name = $user_id.".jpg";
                // image_crop($source_image,$image_crop_url,$image_name);
             }

             if(!empty($display_name))
                $ins_data['display_name'] = $this->get('display_name');
             
             $file_exists = "./assets/uploads/profile/resize/thumb_".$user_id.".jpg";
             
            $profileImage = (file_exists($file_exists))?site_url()."assets/uploads/profile/resize/thumb_".$user_id.".jpg":'http://heresmygps.com/assets/images/no_image.png';

            if($user_id){
                $this->user_model->update('user',$ins_data, array('id' => $user_id));

                $user_data = $this->user_model->check_unique(array("id" => $user_id));
            
                if(!empty($default_id) && !empty($phonenumber)) {
                    //update user groups by through default id and phonenumber
                    user_group_update($user_data,$default_id,$phonenumber);
                }

                return $this->response(array('status' =>'success','request_type' => 'user_profile','user_id' => $user_id, 'default_id' => $default_id, 'phonenumber' => $phonenumber,'email' => $email,'profile_image'=>$profileImage,'display_name'=>$user_data['display_name']), 200);
            }
            else
            {
                return $this->response(array('status' => "error",'request_type' => 'user_profile','msg' => 'Unknown Error Occurred!! Try Again...','error_code' => 2), 404);
            }
        }
        
        
        
        function user_gcm_update_post() {
            
               //check for required values
        if((!$this->post('user_id')) && (!$this->post('gcm_id') )) 
        {
          return $this->response(array('status' => 'error','msg' => 'Required fields missing in your request','error_code' => 1), 404);
        }
            
            $user_id = $this->post('user_id');
            $gcm_id  = $this->post('gcm_id');
            
             
            if($user_id){
                 $ins_data                 = array();
                 $ins_data['gcm_id']       = $gcm_id;
                 $ins_data['date_updated'] = strtotime(date('Y-m-d H:i:s'));
                 
                 $this->user_model->update('user',$ins_data,array('id' => $user_id));
            }
            
            if($user_id) {
                return $this->response(array('status' =>'success','user_id' => $user_id), 200);
            }
            else
            {
                return $this->response(array('status' => "error",'msg' => 'Unknown Error Occurred!! Try Again...','error_code' => 2), 404);
            }
        }
        
        
    //user position update
    function user_position_save_get()
    {    
      //check for required values
    if(!$this->get('user_id') || !$this->get('lat') || !$this->get('lon')) {
      return $this->response(array('status' => 'error','msg' => 'Required fields missing in your request','error_code' => 1), 404);
    }
     
        $user_id      = $this->get('user_id');
        $sensor_speed = $this->get("sensor_speed");
        
        $result = $this->db->get_where('user_position',array('user_id' => $user_id))->num_rows();
               
        $ins_data = array();
        $ins_data['user_id']        = $user_id;
        $ins_data['lat']            = ($this->get('lat')=='noloc')?0:$this->get('lat');
        $ins_data['lon']            = ($this->get('lon')=='noloc')?0:$this->get('lon');
        $ins_data['altitude']       = $this->get('altitude');
        $ins_data['speed']          = $this->get('speed');
        $ins_data['bearing']        = $this->get('bearing');
        $ins_data['accuracy']       = $this->get('accuracy');
        $ins_data['manual_address'] = $this->get('manual_address');
        $addr_type                  = $this->get('address_type');
        $ins_data['address_type']   = (empty($addr_type))?"current":$addr_type;

        if($result > 0) {
           // $ins_data['date_updated'] = strtotime(date('Y-m-d H:i:s'));
          //  $update = $this->user_position_model->update($ins_data,array('user_id' => $user_id));
            
            $check_lat_lon = $this->user_position_model->check_unique(array("user_id" => $user_id));
            
            $latt = $this->get('lat');
            $lon  = $this->get('lon');
            
            //Check if lat lon already 0 or greater than 0
            if(($latt != 0 && $lon != 0) && (($check_lat_lon['lat'] != 0 && $check_lat_lon['lon'] != 0) || ($check_lat_lon['lat'] == 0 && $check_lat_lon['lon'] == 0))) {
                $ins_data['date_updated'] = strtotime(date('Y-m-d H:i:s'));
                $update = $this->user_position_model->update($ins_data,array('user_id' => $user_id));
            }
            
            //update map last seen time
            $up_data = array();
            $up_data['last_seen_time'] = date("Y-m-d H:i:s");
            $this->user_groups_model->update($up_data,array("user_id" => $user_id,"status" => 1));
            
        }
        else
        {
            $ins_data['date_created'] = strtotime(date('Y-m-d H:i:s'));
            $ins_data['date_updated'] = strtotime(date('Y-m-d H:i:s'));
            $this->user_position_model->insert($ins_data);

            if($this->get('lat') == 'noloc')
              $this->user_groups_model->update(array('is_visible'=>1),array("user_id" => $user_id));
        }
             
         $track = $this->user_model->check_unique(array("id" => $user_id)); 
        // print_r($track);
        // exit;
          if($track['is_tracked'] == 1){    
            $this->push_group_user_data_to_gcm("",$user_id,$sensor_speed);
         }
        return $this->response(array('status' =>'success', 'request_type' => 'user_position_update'), 200);
    }
        
        
    //create group
     function group_save_get() 
     {
           
            //check for required values
        if(!$this->get('name') || !$this->get('type')|| !$this->get('user_id') || !$this->get('join_key') || !$this->get('location_type'))
        {
          return $this->response(array('status' => 'error','msg' => 'Required fields missing in your request','error_code' => 1), 404);
        }
                        
            $group_id     = $this->get('group_id');
            $join_key     = $this->get('join_key');
            $user_id      = $this->get('user_id');
            $map_avail    = $this->get('map_avail');
            $pass_protect = $this->get('password_protect');
            $allow_deny   = $this->get('allow_deny');
            $password     = $this->get('password');
            
            $where = array();
            $where['join_key'] = str_to_lower($join_key);
         
            if(!empty($group_id))
             $where['id'] = str_to_lower($group_id);
             
            $result = $this->group_model->check_unique($where);
            
            if(count($result)) {
                return $this->response(array('status' => "error",'msg' => 'Join key already exist.','error_code' => 109), 404);
            }
          
            $description = $this->get('description');
            $name        = $this->get('name');
            
            $ins_data = array();
            $ins_data['name']            = $name;
            $ins_data['type']            = $this->get('type');
            $ins_data['join_key']        = $join_key;
            $ins_data['description']     = (!empty($description))?$description:$name;
            $ins_data['location_type']   = $this->get('location_type');
            $ins_data['user_id']         = $this->get('user_id');   
            $ins_data['is_available']    = $this->get('is_available');
            $ins_data['password_protect']= $pass_protect;
            $ins_data['allow_deny']      = $this->get('allow_deny');
            $ins_data['password']        = $password;   

            if($map_avail!='')
              $ins_data['map_avail']     = $map_avail;
            
            
            if($ins_data['location_type'] == 'static'){
                $ins_data['lat'] = $this->get('lat');
                $ins_data['lon'] = $this->get('lon');
            }
            
            if($group_id) { 
                $ins_data['date_updated'] = strtotime(date('Y-m-d H:i:s'));
                $this->group_model->update($ins_data,array('id' => $group_id));
            }else
            {
                $ins_data['status']    = 1;
                $ins_data['date_created'] = strtotime(date('Y-m-d H:i:s'));
                $group_id =  $this->group_model->insert($ins_data);
                
                //join group
                $res = $this->user_groups_model->check_unique(array('user_id' => $user_id, 'group_id' => $group_id));
                $cnt    = count($res);
                
                if(empty($cnt)) {
                    $ins_data = array();
                    $ins_data['user_id']  = $user_id;
                    $ins_data['group_id'] = $group_id; 
                    $ins_data['status']   = 1; 
                    $ins_data['is_joined'] = 1; 
                    $ins_data['last_seen_time'] = date('Y-m-d H:i:s');
                    $ins_data['user_active_time'] = date('Y-m-d H:i:s');
                    $add_group            = $this->user_groups_model->insert($ins_data);
                }
            }
            
              if($group_id) {
                  
                    return $this->response(array('status' =>'success','request_type' => 'group_creation','group_id' => $group_id,'join_key' => $join_key), 200);
              }
              else
              {
                    return $this->response(array('status' => "error",'request_type' => 'group_creation','msg' => 'Unknown Error Occurred!! Try Again...','error_code' => 2), 404);
              }   
              
        }
        
        function group_list_get()
        {
                        
            //check for required values
        if(!$this->get('user_id')){
          return $this->response(array('status' => 'error','msg' => 'Required fields missing in your request','error_code' => 1), 404);
        }
            
            $user_groups = $this->group_model->lists($this->get('user_id'));
            
            foreach($user_groups as $ukey => $uvalue) {
                $count  = $this->user_groups_model->get_groups_member_count(array("group_id" => $uvalue['id']));
                $user_groups[$ukey]['members_count'] = $count;
            }
            
            
            return $this->response(array('status' =>'success','request_type' => 'group_list','list' => $user_groups), 200); 
       }
      
      
      //group update
      function group_update_get() 
      {
         if((!$this->get('group_id'))) 
            return $this->response(array('status' => 'error','msg' => 'Required fields missing in your request','error_code' => 1), 404);
         
         //|| (!$this->get('available')) || (!$this->get('type')) || (!$this->get('location_type'))
         
         $available        = $this->get('available');
         $type             = $this->get('type');
         $location_type    = $this->get('location_type');
         $group_id         = $this->get('group_id');

         $display_name     = $this->get('display_name');
         $channel_id       = $this->get('channel_id');
         $password_protect = $this->get('password_protect');
         $allow_deny       = $this->get('allow_deny');
         $password         = $this->get('password');
         
         
         $ins_data = array();
         
         $ins_data['password_protect']= $password_protect;
         $ins_data['allow_deny']      = $allow_deny;
         $ins_data['password']        = $password; 

         if($type)
            $ins_data['type']= $type;

         if($available)
            $ins_data['is_available'] = $available;

         if($location_type)
              $ins_data['location_type']= $location_type;

         if($display_name)
              $ins_data['description']= $display_name;


         if($channel_id)  
              $ins_data['join_key']= $channel_id;
              
          
             

         if(empty($ins_data))   
              return $this->response(array('status' => 'error','msg' => 'Required fields missing in your request','error_code' => 1), 404);


         $this->group_model->update($ins_data,array("id" => $group_id));
         
         return $this->response(array("status" => 'success', 'request_type' => 'group_update'), 200);
      }
      
      
      //join group
      function join_group_get($user_id='',$group_id ='') 
      { 
        //check for required values
    if(!$this->get('join_key') || !$this->get('user_id')){
      return $this->response(array('status' => 'error','msg' => 'Required fields missing in your request','error_code' => 1), 404);
    }
                
        $join_key = $this->get('join_key');
        $user_id  = $this->get('user_id');
        $sensor_speed  = $this->get('sensor_speed');
        $result   = $this->group_model->check_unique(array("join_key" => $join_key));
             
            
        if(empty($result)) {
             return $this->response(array('status' => "error",'msg' => "Group Doesn't exists.",'error_code' => 110), 404);
        }
            
            $this->send_notification_group_owner_get($join_key,$user_id,'online',FALSE);
            
        //We can join only public map
        if($result['type'] == 'private') {
           return $this->response(array('status' => 'error', 'msg' => 'You dont have access to this map.', 'error_code' => 3), 404);
         }  
             
             
             $owner_status = $this->group_model->get_owner_status($join_key);
            
             if($owner_status['track']==0) {
                
                return $this->response(array('status' => 'error', 'join_key' => $join_key ,'msg' => 'User is not online', 'error_code' => 5), 404);
             }
             
            
            $res = $this->user_groups_model->check_unique(array('user_id' => $user_id, 'group_id' => $result['id']));
            $cnt = count($res);
            
            if(empty($cnt)) {
                $ins_data = array();
                $ins_data['user_id']   = $user_id;
                $ins_data['group_id']  = $result['id']; 
                $ins_data['status']    = 1;
                $ins_data['is_joined'] = 1; 
                $ins_data['is_view']   = 1;
                $ins_data['is_visible']= 1;
                $add_group             = $this->user_groups_model->insert($ins_data);
            }
            else
            {
                $ins_data['is_joined'] = 1; 
                $ins_data['is_view']   = 1;
                $ins_data['is_visible']= 1;
                $up_group              = $this->user_groups_model->update($ins_data,array("user_id" => $user_id));
            }
             $this->search_map_get($join_key,$user_id,'join');
        
       
        //echo $add_group; exit;
       // return $this->response(array('status' =>'success', 'join_key' => $join_key, 'group_name' => $result['name']), 200);  
    }
    
    
    //group leave update
    function group_leave_get()
    {
        //check for required values
    if(!$this->get('group_id')|| !$this->get('user_id')){
      return $this->response(array('status' => 'error','msg' => 'Required fields missing in your request','error_code' => 1), 404);
    }  
        
        $user_id   = $this->get("user_id");
        $join_key  = $this->get("group_id"); 
        
        $result = $this->group_model->check_unique(array("join_key" => $join_key));
        
        if(!count($result)) {
            return $this->response(array('status' => "error",'msg' => 'Invalid Group.','error_code' => 101), 404);
        }
            
        $ins_data  = array();
        $ins_data['status']  = 0;
        $ins_data['user_leave_time'] = date("Y-m-d H:i:s");
        $this->user_groups_model->update($ins_data,array("user_id" => $user_id, 'group_id' => $result['id']));
        
        if(($result['is_available'] !=1) && ($user_id == $result['user_id'])) {
            $this->group_model->delete(array("id" => $result['id']));
            $this->user_groups_model->delete(array("group_id" => $result['id']));
            
        }
        
          return $this->response(array('status' =>'success', 'request_type' => 'group_inactive','join_key' => $join_key, 'group_status' => 0, 'is_availabel' => $result['is_available']), 200);  
    }
    
    //set user favourite map
    function favourite_group_get()
    {
      
        //check for required values
    if(!$this->get('group_id')|| !$this->get('user_id')){
      return $this->response(array('status' => 'error','msg' => 'Required fields missing in your request','error_code' => 1), 404);
    }  
        
        $user_id   = $this->get("user_id");
        $join_key  = $this->get("group_id"); 
        $favourite = $this->get("favourite");
        
        $result = $this->group_model->check_unique(array("join_key" => $join_key));
        
        if(!count($result)) {
            return $this->response(array('status' => "error",'msg' => 'Invalid Group','error_code' => 101), 404);
        }
            
        $ins_data  = array();
        $ins_data['is_favourite'] = $favourite;
        $this->user_groups_model->update($ins_data,array("user_id" => $user_id, 'group_id' => $result['id']));
        
        return $this->response(array('status' =>'success', 'request_type' => 'set_group_favourite','join_key' => $join_key, 'favourite' => $favourite), 200);  
    }
    
    //get favourites
    function favourite_groups_get()
    {
         //check for required values
    if(!$this->get('user_id')){
      return $this->response(array('status' => 'error','msg' => 'Required fields missing in your request','error_code' => 1), 404);
    }  
        
        $user_id   = $this->get("user_id");  
         
        $favourite_groups    = $this->user_groups_model->get_favourite_groups($user_id);

        $image_crop_url = FCPATH."assets/uploads/profile/resize";
        
        foreach($favourite_groups as $fkey => $fvalue) {
            $count  = $this->user_groups_model->get_groups_member_count(array("group_id" => $fvalue['id']));
            $favourite_groups[$fkey]['members_count'] = $count;

            if($fvalue['profile_image']!='' && $fvalue['user_type']=='admin'){
                $source_image   = "var/www/html/heresmygps/assets/uploads/profile/".$fvalue['profile_image'];                

                if($jvalue['login_type']=='social')
                {
                     $file    = "var/www/html/heresmygps/assets/uploads/profile/social.jpg";
                     $current = file_get_contents($jvalue['profile_image']);
                  
                     file_put_contents($file, $current, FILE_APPEND);
                     
                     $source_image   = "var/www/html/heresmygps/assets/uploads/profile/social.jpg";
                }   

                $image_name = $fvalue['user_id'].".jpg";
              //  image_crop($source_image,$image_crop_url,$image_name);

                $favourite_groups[$fkey]['profile_image'] = site_url()."assets/uploads/profile/resize/large_".$fvalue['user_id'].".jpg";
            }
            else
            {
                $favourite_groups[$fkey]['profile_image'] = '';
            }
        }
            
        return $this->response(array('status' =>'success','request_type' => 'favourite_list','list' => $favourite_groups), 200);
    }
    
    
    //get joined groups
    function joined_groups_get()
    {
       //check for required values
    if(!$this->get('user_id')){
      return $this->response(array('status' => 'error','msg' => 'Required fields missing in your request','error_code' => 1), 404);
    }  
        
        $user_id   = $this->get("user_id");  
         
        $joined_groups    = $this->user_groups_model->get_joined_groups($user_id);

        $image_crop_url = FCPATH."assets/uploads/profile/resize";
       
        foreach($joined_groups as $jkey => $jvalue) {
            $count  = $this->user_groups_model->get_groups_member_count(array("group_id" => $jvalue['id']));
            $joined_groups[$jkey]['members_count'] = $count;

            if($jvalue['profile_image']!='' && $jvalue['user_type']=='admin'){

                $source_image   = "var/www/html/heresmygps/assets/uploads/profile/".$jvalue['profile_image'];

                if($jvalue['login_type']=='social')
                {
                     $file    = "var/www/html/heresmygps/assets/uploads/profile/social.jpg";
                     $current = file_get_contents($jvalue['profile_image']);
                  
                     file_put_contents($file, $current, FILE_APPEND);
                     
                     $source_image   = "var/www/html/heresmygps/assets/uploads/profile/social.jpg";
                }              

                $image_name = $jvalue['user_id'].".jpg";
                //image_crop($source_image,$image_crop_url,$image_name);

                $joined_groups[$jkey]['profile_image'] = $this->profile_url."/large_".$jvalue['user_id'].".jpg";
            }
            else
            {
                $joined_groups[$jkey]['profile_image'] = '';
            }
        }
        return $this->response(array('status' =>'success','request_type' => 'joined_list','list' => $joined_groups), 200);
    }
   
   
   //delete groups
   function delete_groups_get()
   {
     //check for required values
    if(!$this->get('group_id')){
      return $this->response(array('status' => 'error','msg' => 'Required fields missing in your request','error_code' => 1), 404);
    }  
        
        $group_id   = $this->get("group_id");
        
        $this->user_groups_model->delete(array("group_id" => $group_id));
        $this->group_model->delete(array("id" => $group_id));
        
       return $this->response(array('status' =>'success','request_type' => 'delete_map'), 200); 
   }
   
   //update user tracker on or off
   function update_user_tracker_status_get()
   {

       //check for required values
    if((!$this->get('tracker')) && (!$this->get('user_id'))){
      return $this->response(array('status' => 'error','msg' => 'Required fields missing in your request','error_code' => 1), 404);
    } 
                
        $user_id                = $this->get("user_id");
        $tracker                = $this->get("tracker");
        $ins_data               = array();
        $ins_data['is_tracked'] = $tracker;
        $this->user_model->update('user',$ins_data,array("id" => $user_id));
      //   $this->db->query("update user set is_tracked='".$tracker."' where id='".$user_id."'");    

        $active_group = $this->user_groups_model->get_user_active_group($user_id);
        
        $resp = array('status' =>'success','request_type' => 'user_tracker_update', 'tracker' => $tracker, 'active_group' => $active_group['join_key'], 'type' => $active_group['type'], 'is_view' => $active_group['is_view']);
        
        $userdet = '';
        
        if($tracker==1)
          $userdet = $this->push_group_user_data_to_gcm("",$user_id,'',TRUE);

        $resp['members'] = $userdet;

        return $this->response($resp, 200);     
   } 
    
    //user current group active 
    function user_current_group_active_get() {
        if(!$this->get('group_id') || !$this->get('user_id')){
      return $this->response(array('status' => 'error','msg' => 'Required fields missing in your request','error_code' => 1), 404);
    }  
        
         $user_id   = $this->get("user_id");
         $join_key  = $this->get("group_id"); 
         
        // $result = '';
         $result = $this->group_model->get_group_data(array("g.join_key" => $join_key));

         $groups = $this->user_groups_model->get_user_groups($user_id);
        
        $ins_data  = array();
        $admin_id  = '';
        $is_view = '';
        
        if(count($groups)) {
            
            foreach($groups as $gkey => $gvalue) {
               
             //   $ins_data['status']  = ($gvalue['group_id']==$result['id'])?1:0;
                if($gvalue['group_id'] == $result['id']) {
                    $favourite   =  $gvalue['favourite'];
                    $is_view     =  $gvalue['is_view'];  
                    $is_visible  =  $gvalue['is_visible'];
                }
                else
                {
                    $group_id    = $gvalue['group_id'];
                   // $favourite   = $gvalue['favourite'];
                  //  $is_visible  = $gvalue['is_visible'];
                }

            }

            $ins_data['user_leave_time']  = date("Y-m-d H:i:s");
            $ins_data['status']           = 0;        

         $this->user_groups_model->update($ins_data,array("user_id" => $user_id, 'group_id !=' => $result['id']));

            $ins_data['user_active_time'] = date("Y-m-d H:i:s");
            $ins_data['status']           = 1;

        $this->user_groups_model->update($ins_data,array("user_id" => $user_id, 'group_id' => $result['id']));
                  
            return $this->response(array('status' =>'success','request_type' => 'current_group_active','join_key' => $join_key,'lat' => $result['lat'],'lon' => $result['lon'],'description' => $result['description'],'location_type' => $result['location_type'],'admin_id'=> $result['user_id'], 'type' => $result['type'],'group_id' => $result['id'],'password' => $result['password'],'password_protect'=> $result['password_protect'],'allow_deny' => $result['allow_deny'], "is_view" => $is_view, "is_visible" => $is_visible,'is_favourite' => $favourite), 200);
        } 
        else
        {
            return $this->response(array('status' =>'error', 'request_type' => 'current_group_active', 'msg' => 'No Active Groups for this user', 'error_code' => 1), 404);
        }
          
  }
    
    //share map 
    function check_group_get()
    {
        //check for required values
    if(!$this->get('join_key')){
      return $this->response(array('status' => 'error','msg' => 'Required fields missing in your request','error_code' => 1), 404);
    }
        
        $join_key = str_to_lower($this->get('join_key'));
        $result   = $this->group_model->check_unique(array("join_key" => $join_key));
        
        if(count($result)) {
           return $this->response(array('status' => "success",'join_key' => $join_key), 404);
        }
        else
        {
            $ins_data = array();
            $ins_data['name']            = $join_key;
            $ins_data['type']            = 'private';
            $ins_data['join_key']        = $join_key;
            $ins_data['description']     = $join_key;
            $ins_data['location_type']   = 'mobile';
            $ins_data['user_id']         = $this->get('user_id');  
            $ins_data['date_created']    = strtotime(date('Y-m-d H:i:s'));
            $group_id =  $this->group_model->insert($ins_data); 
            return $this->response(array('status' =>'success','join_key' => $join_key, 'request_type' => 'group_creation','group_id' => $group_id), 200);
        }
            
    }  
     
    //random number generate for unique group id           
    function random_number_generation_get()
    { 
        $rand = gen_random_string(6);

        return $this->response(array('status' =>'success','request_type' => 'random_id_generate','random_id' => $rand), 200);     
    }
    
    //group hide or show
    function group_status_get()
    {
         if(!$this->get('group_id') && !$this->get('user_id') && !$this->get('view')){
      return $this->response(array('status' => 'error','msg' => 'Required fields missing in your request','error_code' => 1), 404);
    }  
        
         $user_id   = $this->get("user_id");
         $join_key  = $this->get("group_id");
         $view      = $this->get("view"); 
         
         $result = $this->group_model->check_unique(array("join_key" => $join_key));
       
         $ins_data = array();
         $ins_data['is_view'] = $view;
             
         $affected_rows = $this->user_groups_model->update($ins_data,array("user_id" => $user_id, 'group_id' => $result['id']));
        
         if(!empty($affected_rows)) {
            return $this->response(array('status' =>'success', 'request_type' => 'group_view_status','user_id' => $user_id, 'group_id' => $result['id'], 'view' => $ins_data['is_view']), 200);
         }
         else
         {
            return $this->response(array('status' =>'success', 'request_type' => 'group_view_status','msg' => "Already hided this group.", 'view' => $ins_data['is_view']), 202);
         }
    }
    
   function push_group_user_data_to_gcm($group_id = "",$user_id = "",$sensor,$response_flag=FALSE) { 
            
            
            if(empty($user_id) && empty($group_id)){
                return false;
            }
        
           if($user_id) {                 

                $userdata = $this->user_model->check_unique(array("id" => $user_id)); 

                $user_groups = $this->group_model->get_group_users("user_groups",array("user_id" => $user_id,'status'=>1));
                $filtered_user_ids = $filtered_group_ids = array();
                
                if(!empty($user_groups)){
                    foreach($user_groups as $row) {
                        $filtered_group_ids[] = $row['group_id'];
                    }
                }  
                
                if(!empty($filtered_group_ids)) {
                   $group_details =  $this->group_model->filter_groups($filtered_group_ids);
                }
                else
                {
                    return false;
                }
            }
            else
            {
                return false;
            }
            $user = array(); 
            
            $this->load->library("FCM");

            $res     = $this->user_groups_model->get_user_active_group($user_id);
            $type    = (isset($res['type']))?$res['type']:'';
            $is_view = (isset($res['is_view']))?$res['is_view']:'';

            $grp_lat        = (isset($res['lat']))?$res['lat']:'';
            $grp_lon        = (isset($res['lon']))?$res['lon']:'';
            $grp_descript   = (isset($res['description']))?$res['description']:'';
            $grp_loctype    = (isset($res['location_type']))?$res['location_type']:'';
            $grp_datecreate = (isset($res['date_created']))?$res['date_created']:'';
            
            //print_r($group_details); 
            if(!empty($group_details)){
                
                foreach($group_details as $group) {
                    
                   $user_details = $this->user_groups_model->get_user_gcm($group['id']);
                    if(!empty($user_details)) {
                            $user_position = $this->user_position_model->get_group_active_user_position($group,$user_id);
                            
                          //  echo "<pre>";
                          //  print_r($user_position); exit;
                            $i = 0;
                            if(count($user_position)) {
                            
                            foreach($user_position as $pkey=>$pvalue) {   
                              
                               $usertype = ($pvalue['id']==$group['user_id'])?'admin':'member'; 
                                
                                if($pvalue['status'] == 0 && $usertype== 'member') {
                                   continue;
                                }
                               
                                if($usertype== 'admin' && $pvalue['show_personal'] == 0 && $group['id']!=$pvalue['group_id']){
                                    continue;
                                }
                               
                                if(!empty($pvalue['last_seen_time']) && ($pvalue['show_personal'] == 0)) {
                   
                                    $lastseen = date('d-m-Y H:i',strtotime($pvalue['last_seen_time']));
                                    
                                    //24 hours last update time check
                                    $twenty_four_time = date('d-m-Y H:i',strtotime('-24 hour'));
                                    
                                    if((strtotime($twenty_four_time) > strtotime($lastseen)) && ($usertype== 'member')){
                                        continue;
                                    }
                                    
                                    $currtime = date('d-m-Y H:i',strtotime('-1 hour'));
                                
                                    $lastup   = 1;
                                    
                                    if(strtotime($currtime) <= strtotime($lastseen)){
                                       $lastup   = 1; 
                                    }
                                    
                                    if((strtotime($currtime) > strtotime($lastseen)) && (strtotime($twenty_four_time) < strtotime($lastseen))) {  
                                        if($usertype == 'member') {
                                           continue;
                                        }
                                        
                                      $lastup = 0;
                                    }   
                                }
                                
                                $lat = $pvalue['lat'];
                                $lon = $pvalue['lon'];
                                
                                //check user group status active or inactive
                                $user_position['lat']   = $lat;
                                $user_info              = $this->_prepare_user_details($user_position);
                                
                                if($user_info['user']['profile']) {
                                    $user[$i]['user']['profile']['id']              = $pvalue['id'];
                                    $user[$i]['user']['profile']['default_id']      = $pvalue['default_id'];
                                    $user[$i]['user']['profile']['display_name']    = $pvalue['display_name'];
                                    $user[$i]['user']['profile']['phonenumber']     = $pvalue['phonenumber'];
                                    $user[$i]['user']['profile']['email']           = $pvalue['email'];
                                    $user[$i]['user']['profile']['android_id']      = $pvalue['android_id'];
                                    $user[$i]['user']['profile']['device_id']       = $pvalue['device_id'];
                                    $user[$i]['user']['profile']['profile_image']   = $this->profile_url."/thumb_".$pvalue['id'].".jpg";
                                    $user[$i]['user']['profile']['flag']            = $pvalue['is_tracked'];
                                    $user[$i]['user']['profile']['user_type']       = $usertype;
                                    $user[$i]['user']['profile']['satiation_id']    = $pvalue['satiation_id'];
                                    $user[$i]['user']['profile']['is_overwrite']    = $pvalue['is_overwrite'];
                                    $user[$i]['user']['profile']['is_show']         = $pvalue['is_show'];
                                    $user[$i]['user']['profile']['is_contact']      = $pvalue['is_contact'];
                                }
                                if($user_info['user']['account']) {
                                    $user[$i]['user']['account']['plan_name'] = (isset($pvalue['plan_name']) && !empty($pvalue['plan_name']))?$pvalue['plan_name']:"";
                                } 
                               $user[$i]['user']['position']['lat'] = $lat;
                               $user[$i]['user']['position']['lon'] = $lon; 
                               $user[$i]['user']['position']['altitude'] = $pvalue['altitude'];
                               $user[$i]['user']['position']['speed']    = $pvalue['speed'];
                               $user[$i]['user']['position']['sensor_speed']   = (empty($sensor))?"0.0":$sensor; 
                               $user[$i]['user']['position']['bearing']  = $pvalue['bearing']; 
                               $user[$i]['user']['position']['accuracy'] = $pvalue['accuracy'];
                               if($pvalue['status'] == 0) {
                                 //$user[$i]['user']['position']['updated_time'] = date("Y-m-d h:i:s", $pvalue['date_updated']);
                                 $user[$i]['user']['position']['updated_time'] = $pvalue['date_updated'];
                               }
                               $user[$i]['user']['group']['id']                = $pvalue['group_id'];
                               $user[$i]['user']['group']['join_key']          = $pvalue['group_join_key'];
                               $user[$i]['user']['group']['description']       = $pvalue['description'];
                               $user[$i]['user']['group']['status']            = $pvalue['status'];
                               $user[$i]['user']['group']['visible']           = $pvalue['is_joined'];
                               $user[$i]['user']['group']['view']              = $pvalue['is_view'];
                               $user[$i]['user']['group']['invisible']         = $pvalue['is_visible'];
                               if($lat==0)
                                  $user[$i]['user']['group']['invisible']      = 1;
                                
                               $user[$i]['user']['group']['type']              = $group['type'];
                              // $user[$i]['user']['group']['last_seen_time']    = strtotime($pvalue['last_seen_time']);
                                $user[$i]['user']['group']['last_seen_time']   = $pvalue['date_updated'];
                               $user[$i]['user']['group']['is_online']         = $lastup;
                               $user[$i]['user']['group']['share_link']        = site_url()."search/".$pvalue['group_join_key'];
                               //print_r($user);exit;
                               $user[$i]['user']['static_map']['user_id']      = $pvalue['id'];

                              // $user[$i]['user']['static_map']['maps']   = $static_maps;

                               $i++;                                   
                            } 
                           // print_r($user_details);
                          foreach($user_details as $ugcm){
                           
                             $gcm_id = $ugcm['gcm_id'];
                            
                            if($ugcm['is_tracked'] == 0) {
                                continue;
                            }
                            
                        
                             $gcm_data = array();
                             $gcm_data['members'] = $user;                            
                             $gcm_data['default_id'] = $userdata['default_id'];
                             $gcm_data['join_key']   = $ugcm['join_key'];
                             $gcm_data['type']       = $type;
                             $gcm_data['is_view']    = $is_view;
                             $gcm_data['time']       = strtotime(date('Y-m-d H:i:s'));
                             $gcm_data['description']= $grp_descript;
                             $gcm_data['location_type'] = $grp_loctype;
                             $gcm_data['lat']        = $grp_lat;
                             $gcm_data['lon']        = $grp_lon;
                             $gcm_data['date_created']=$grp_datecreate;
                             $gcm_data['method']     = 'search_user';
                              $gcm_data['msg']        = '';
                           //  print_r($gcm_data);
                            // $this->gcm->send_notification(array($gcm_id),array("hmg" => $gcm_data));
 
                        }
                        
                      }  
                      
                    }
                }
                
                $static_maps = $this->user_model->get_static_maps("",$res['id']); 
                $static_maps = (count($static_maps)>0)?$static_maps:array();
                             
                //while tracker is on retuen to members liat only
                if($response_flag)
                  return $user;

                return $this->response(array('status' =>'success','user_id' => $user_id,'type'=>$type,'description'=>$grp_descript,'location_type'=>$grp_loctype,'lat'=>$grp_lat,'lon'=>$grp_lon,'date_created'=>$grp_datecreate,'is_view'=>$is_view,'members'=>$user, "static_maps" => $static_maps), 200);
            }
            else
            {
                return $this->response(array('status' => "error",'msg' => "Group doesn't exist.",'error_code' => 1), 404);
            }

        }
        
        
   function search_map_get($join_key = '', $user_id ='', $type='')
   {
   
        //check for required values
    if(!$this->get('join_key') && !$this->get('user_id') && !$this->get('allowed')) {
      return $this->response(array('status' => 'error','msg' => 'Required fields missing in your request','error_code' => 1), 404);
    } 
                
        $join_key  = (!empty($join_key))?$join_key:$this->get("join_key");
        $user_id   = (!empty($user_id))?$user_id:$this->get("user_id"); 
        $allowed   = $this->get('allowed');
        $sensor    = $this->get("sensor_speed");
        $password  = $this->get('password');
        $type      = (empty($type))?$this->get('type'):$type;
        
        $result    = $this->group_model->check_unique(array("join_key" => $join_key));
    
        $plan_data = get_plan_data($result['user_id']);
        $user_data = $this->user_model->check_unique(array("id" => $user_id));
        
         //check if user has already joined or not
        $user_al  = $this->user_groups_model->check_unique(array("user_id" => $user_id, "group_id" => $result['id']));
        $uct      = count($user_al);
          
        if(empty($result)) {
           return $this->response(array('status' => "error",'msg' => "Group doesn't exists.", 'join_key' => $join_key, 'error_code' => 110), 404); 
        }
        
        $res = $this->user_groups_model->check_unique(array('user_id' => $user_id, 'group_id' => $result['id']));
        $cnt = count($res);
        
        if(!empty($type) && ($type != 'authenticated_success')){
           if(empty($cnt)){     
                if(($result['password_protect']==1) && ($result['user_id']!=$user_id)){
                    return $this->response(array('status' => 'error','msg' => 'This map has been password protected so please enter password','request_type' => 'password_protect','join_key' => $join_key, 'error_code' => 113), 404);
                }
                if(($result['allow_deny']==1) && ($result['user_id']!=$user_id)){
                    return $this->response(array('status' => 'error','msg' => 'This map has been protected. Do you want to send join request?','request_type' => 'allow_deny', 'join_key' => $join_key, 'error_code' => 107), 404);
                }
            }
        }
        
        $grp_lat        = (isset($result['lat']))?$result['lat']:'';
        $grp_lon        = (isset($result['lon']))?$result['lon']:'';
        $grp_descript   = (isset($result['description']))?$result['description']:'';
        $grp_loctype    = (isset($result['location_type']))?$result['location_type']:'';
        $grp_datecreate = (isset($result['date_created']))?$result['date_created']:'';
        $grp_dateupdate = (isset($result['date_updated']))?$result['date_updated']:'';

        //Search  group owner active or not, also check owner track is on or off
        $owner_status = $this->group_model->get_owner_status($join_key);
       
        if(($owner_status['id'] != $user_id)) {

                if((($owner_status['active']!=1 || $owner_status['track']!=1 ) && $result['type'] == 'private')){
                  $this->user_model->update("user",array("is_tracked" => 1),array("id" => $owner_status['id']));
                }
                else if($result['type'] == 'public' && $owner_status['track']!=1)
                {
                    return $this->response(array('status' => 'error', 'join_key' => $join_key ,'msg' => 'User is not online', 'join_key' => $join_key, 'error_code' => 5), 404);
                }
         }
         
       
         if(count($result)){
            
            if(!empty($user_id) && ($allowed== 'no') && empty($cnt)) {
                $this->send_notification_group_owner_get($join_key,$user_id,'online',FALSE);
            }
        // echo $cnt; exit;
            if(empty($cnt) && (int)$user_id) {
                
                  $ins_data = array();
                  $ins_data['user_id']   = $user_id;
                  $ins_data['group_id']  = $result['id']; 
                  $ins_data['status']    = 1; 
                  $ins_data['is_joined'] = 1; 
                  $ins_data['is_view']   = ($result['type']=='private')?1:0;
                 // $ins_data['is_visible']= ($result['type']=='private')?1:0;
                  $ins_data['last_seen_time']   = date('Y-m-d H:i:s');
                  
                  $add_group             = $this->user_groups_model->insert($ins_data);
                  
                  //increase members joined count
                 if($user_data['login_type'] != 'website'){
                   
                 } 
              }
            //get user data
            $userdata = $this->user_model->check_unique(array("id" => $user_id));

          //  $this->load->library("GCM");
            $group_id = $result['id'];
            
             if(!empty($user_id)) {
                $ins_data = array();
                $ins_data['status'] = 1;
                $activate = $this->user_groups_model->update($ins_data,array("group_id" => $group_id, 'user_id' => $user_id));
             }
            //get active group user details
            $user_details = array();
            
            
            $user_details  = $this->user_groups_model->get_user_gcm($group_id);
            $user_position = $user = array();
        
            if(count($user_details)){

                $admin_id='';
                $lastseenup='';
                                    
                $position = $this->user_position_model->get_position($result);
                 
                     $i = 0;
                     if(count($position)) {
                        
                         foreach($position as $pkey => $pvalue)
                         {
                                $usertype = ($pvalue['id']==$result['user_id'])?'admin':'member'; 
                                
                                if($pvalue['status'] == 0 && $usertype== 'member' && $result['type'] == 'private') {
                                   continue;
                                }
                               
                                if($usertype == 'admin' && $pvalue['show_personal'] == 0 && ($group_id!=$pvalue['group_id'])){
                                    continue;
                                }
                                
                                if(!empty($pvalue['last_seen_time']) && ($pvalue['show_personal'] == 0)) {
                   
                                    $lastseen = date('d-m-Y H:i',strtotime($pvalue['last_seen_time']));
                                    
                                    //24 hours last update time check
                                    $twenty_four_time = date('d-m-Y H:i',strtotime('-24 hour'));
                                    
                                   if((strtotime($twenty_four_time) > strtotime($lastseen)) && ($usertype== 'member')){
                                        continue;
                                    }
                                    
                                    $currtime = date('d-m-Y H:i',strtotime('-1 hour'));
                                
                                    $lastup   = 1;
                                    
                                    if(strtotime($currtime) <= strtotime($lastseen)){
                                       $lastup   = 1; 
                                    }
                                    
                                    if((strtotime($currtime) > strtotime($lastseen)) && (strtotime($twenty_four_time) < strtotime($lastseen))) {  
                                   // if((strtotime($currtime) >= strtotime($lastseen))) {
                                        if($usertype == 'member') {
                                           continue;
                                        }
                                        
                                      $lastup = 0;
                                    
                                    }   
                                }
                                
                                $lat = $pvalue['lat'];
                                $lon = $pvalue['lon'];
                                
                                
                                //check user group status active or inactive
                                $user_position['lat']         = $lat;
                        
                                $user_info = $this->_prepare_user_details($user_position);
                                
                                if($user_info['user']['profile']) {
                                    $user[$i]['user']['profile']['id']              = $pvalue['id'];
                                    $user[$i]['user']['profile']['default_id']      = $pvalue['default_id'];
                                    $user[$i]['user']['profile']['display_name']    = $pvalue['display_name'];
                                    $user[$i]['user']['profile']['phonenumber']     = $pvalue['phonenumber'];
                                    $user[$i]['user']['profile']['email']           = $pvalue['email'];
                                    $user[$i]['user']['profile']['android_id']      = $pvalue['android_id'];
                                    $user[$i]['user']['profile']['device_id']       = $pvalue['device_id'];
                                    $user[$i]['user']['profile']['profile_image']   = $this->profile_url.'/thumb_'.$pvalue['id'].".jpg";
                                    $user[$i]['user']['profile']['uimage']          = 'https://heresmygps.com/assets/uploads/profile/resize'.'/thumb_'.$pvalue['id'].".jpg";
                                    $user[$i]['user']['profile']['flag']            = $pvalue['is_tracked'];
                                    $user[$i]['user']['profile']['user_type']       = $usertype;
                                    $user[$i]['user']['profile']['satiation_id']    = $pvalue['satiation_id'];
                                    $user[$i]['user']['profile']['is_overwrite']    = $pvalue['is_overwrite'];
                                    $user[$i]['user']['profile']['is_show']         = $pvalue['is_show'];
                                    $user[$i]['user']['profile']['is_contact']      = $pvalue['is_contact'];
                                }
                                if($user_info['user']['account']) {
                                    $user[$i]['user']['account']['plan_name'] = $pvalue['plan_name'];
                                } 
                               $user[$i]['user']['position']['lat']          = $lat;
                               $user[$i]['user']['position']['lon']          = $lon;
                               $user[$i]['user']['position']['altitude']     = $pvalue['altitude'];
                               $user[$i]['user']['position']['speed']        = $pvalue['speed']; 
                               $user[$i]['user']['position']['sensor_speed'] = (empty($sensor))?"0.0":$sensor; 
                               $user[$i]['user']['position']['bearing']      = $pvalue['bearing']; 
                               $user[$i]['user']['position']['accuracy']     = $pvalue['accuracy'];
                               $user[$i]['user']['group']['id']              = $pvalue['group_id'];
                               $user[$i]['user']['group']['description']     = $pvalue['description'];
                               $user[$i]['user']['group']['join_key']        = $pvalue['group_join_key'];
                               $user[$i]['user']['group']['status']          = $pvalue['status'];
                               $user[$i]['user']['group']['visible']         = $pvalue['is_joined'];
                               $user[$i]['user']['group']['view']            = $pvalue['is_view'];

                               $user[$i]['user']['group']['invisible']       = $pvalue['is_visible'];
                               if($lat==0)
                                  $user[$i]['user']['group']['invisible']       = 1;

                               $user[$i]['user']['group']['type']            = $result['type'];
                              // $user[$i]['user']['group']['last_seen_time']  = strtotime($pvalue['last_seen_time']);
                               $user[$i]['user']['group']['last_seen_time']  = (!empty($pvalue['lastseen']))?$pvalue['lastseen']:date("Y-m-d H:i");
                               $user[$i]['user']['group']['is_online']       = $lastup;
                               $user[$i]['user']['group']['share_link']      = site_url()."search/".$pvalue['group_join_key'];
                               
                               
                               $user[$i]['user']['static_map']['user_id'] = $pvalue['id'];

                              
                              // print_($user);

                               if($result['user_id'] == $pvalue['id']){
                                    $lastseenup = $pvalue['date_updated'];
                                    $admin_id = $pvalue['id'];
                                }    

                            $i++; 
                           // sleep(1);                              
                         }

                    // foreach($user_details as $ukey => $uvalue) {
                          
                         //  $gcm_id   = $uvalue['gcm_id'];
                           
                            //if($uvalue['is_tracked'] == 0) {
//                              continue;
//                            }  
                         
                       //  if(!empty($user_id) && !empty($gcm_id)) {  
                           // $gcm_data = array();
//                            $gcm_data['members'] = $user;
//                            $gcm_data['default_id'] = (       isset($userdata['default_id']))?$userdata['default_id']:$uvalue['default_id'];
//                            $gcm_data['join_key']   = $uvalue['join_key'];
//                            $gcm_data['time']       = strtotime(date('Y-m-d H:i:s'));
//                            $gcm_data['type']       = $result['type'];
//                            $gcm_data['is_view']    = $res['is_view'];
//                            $gcm_data['description']= $grp_descript;
//                            $gcm_data['location_type']=$grp_loctype;
//                            $gcm_data['lat']        = $grp_lat;
//                            $gcm_data['lon']        = $grp_lon;
//                            $gcm_data['date_created']=$grp_datecreate;
//                            $gcm_data['method']     = 'search_user';
//                            $gcm_data['msg']        = '';
                            //$this->gcm->send_notification(array($gcm_id),array("hmg" => $gcm_data));  
                      //  }
                   // }    

                }
                
                $static_maps  = $this->user_model->get_static_maps('',$result['id']);
                $static_maps  = (count($static_maps)>0)?$static_maps:array();

                //send position update notification
                if((int)$admin_id && !empty($lastseenup)){

                    $lastseen = date('d-m-Y H:i',$lastseenup);

                    $currtime = date('d-m-Y H:i',strtotime('-30 minutes'));

                    if(strtotime($currtime) <= strtotime($lastseen)){

                        //$this->force_update_get($admin_id, $join_key, FALSE);
                    }
                }                

                return $this->response(array('status' =>'success','join_key' => $join_key,'user_id'=>$user_id,'type'=>$result['type'],'description'=>$grp_descript,'location_type'=>$grp_loctype,'lat'=>$grp_lat,'lon'=>$grp_lon,'date_created'=>$grp_datecreate,'is_view'=>$res['is_view'],'date_update' => $grp_dateupdate ,'members' => $user, 'static_maps' => $static_maps), 200);
            }
            else
            {
               return $this->response(array('status' => "error",'msg' => 'No one user active in this group.','error_code' => 1), 404);
            }
            
        }
        else
        {
            return $this->response(array('status' => "error",'msg' => "Group doesn't exist.",'error_code' => 1), 404);
        }
               
    }
    
    
   function send_notification_group_owner_get($join_key,$user_id,$user_status='',$response_flag=TRUE)
   {
    
        //check for required values
    if(!$this->get('join_key') && !$this->get('user_id')) {
      return $this->response(array('status' => 'error','msg' => 'Required fields missing in your request','error_code' => 1), 404);
    }
      
        $user_id  = $this->get('user_id');
        $join_key = $this->get('join_key');
        
                
        $result    = $this->group_model->get_group_owner_gcm($join_key);
        
        $user_data = $this->user_model->check_unique(array("id" => $user_id));
        
        $this->load->library("FCM");
        
        $gcm_id   = $result['gcm_id'];

        $gcm_data = array();
        $gcm_data['user_status']    = (!empty($user_status))?$user_status:'offline';
        $gcm_data['join_key']       = $join_key;
        $gcm_data['default_id']     = (!empty($user_data['default_id']))?$user_data['default_id']:$user_data['phonenumber'];
        $gcm_data['method']         = 'join_request';
        $gcm_data['msg']            = $gcm_data['default_id'].' has joined in your Channel ID '.$join_key;
        
        $res = $this->user_groups_model->check_unique(array('user_id' => $user_id, 'group_id' => $result['group_id']));

        if($result['user_id'] != $user_id || !count($res)){
            //insert notification to DB
            $this->insert_notification($result['user_id'],$join_key,$gcm_data,$user_id);

            $this->fcm->send_notification(array($gcm_id),array("hmg" => $gcm_data));
        }

        if($response_flag)
            return $this->response(array('status' => 'success', 'join_key' => $join_key), 200);
   }
   
   
   // Delete all Group members 
   function delete_group_members_get()
   {
    
        //check for required values
    if(!$this->get('join_key') && !$this->get('user_id')) {
      return $this->response(array('status' => 'error','msg' => 'Required fields missing in your request','error_code' => 1), 404);
    }
        
        $this->load->library("FCM");
        
        $user_id  = $this->get('user_id');
        $join_key = $this->get('join_key');
                
        $result = $this->group_model->check_unique(array("user_id" => $user_id, "join_key" => $join_key));
        
        
        if(count($result)) {
            
            $members = $this->user_groups_model->get_groups_member($result['id'], $result['user_id']);
            
            $ins_data = array();
            $ins_data['status'] = 1;
            
            foreach($members as $mkey => $mvalue) {
               
                if(!empty($mvalue['userId'])) {
                    
                    $this->user_groups_model->delete(array("group_id" => $result['id'], "user_id" => $mvalue['userId']));
                    
                    $status = $this->user_groups_model->check_unique(array("user_id" => $mvalue['userId'], 'status' => 1));
                  
                    if(!count($status)) {
                    
                        //make the user default map is active
                        $get_group_name = $this->user_model->get_defult_group($mvalue['userId']);
                        
                        $this->group_model->update($ins_data,array("id" => $get_group_name['id']));
                        
                        $this->user_groups_model->update($ins_data,array("group_id" => $get_group_name['id'],"user_id" => $mvalue['userId']));
                    }
                    
                }
                
                if($mvalue['login_type'] == 'app') {  
                    //get user plan
                   // $plan_data     = get_plan_data($user_id);
                   // $members_count = $plan_data['members_count'] + 1;
                   // update_plan_data($user_id,$members_count,'map'); 
                }
                
                $gcm_id   = $mvalue['gcm_id'];
                $gcm_data = array();
                
                $msg = "Your are disconnected from this group (".$join_key.")";
                $gcm_data['default_id'] = $mvalue['default_id'];
                $gcm_data['join_key']   = $join_key;
                $gcm_data['method']     = 'disconnect';
                $gcm_data['msg']        = $msg;
                
                //insert notification to DB
                $this->insert_notification($result['user_id'],$join_key,$gcm_data,$user_id);

                $this->fcm->send_notification(array($gcm_id),array("hmg" => $gcm_data)); 
             
            }
             
            
            return $this->response(array("status" => 'success', 'group_id' => $result['id'], 'join_key' => $result['join_key']),200);
        }
        else
        {
            return $this->response(array('status' => "error",'msg' => "Group doesn't exist.",'error_code' => 1), 404);
        }
   }
   
   function group_members_get()
   {
        //check for required values
    if(!$this->get('join_key')) {
      return $this->response(array('status' => 'error','msg' => 'Required fields missing in your request','error_code' => 1), 404);
    }                
        $join_key = $this->get("join_key");
        
        //group details
        $group_data    = $this->group_model->check_unique(array("join_key" => $join_key));
        
        $group_members = $this->group_model->get_group_members($group_data);
       // echo "<pre>";
      //  print_r($group_members);
        $users = array();
        
        if(count($group_members)) {
            $i = 0;
            foreach($group_members as $gkey => $gvalue)
            {
                $usertype = ($gvalue['id']==$group_data['user_id'])?'admin':'member'; 
                
                if($gvalue['is_tracked'] == 0){
                    continue;
                }
                                              
                if($gvalue['status'] == 0 && $usertype== 'member') {
                   continue;
                }
               
                if($usertype == 'admin' && $gvalue['show_personal'] == 0 && $group['id']!=$gvalue['gid']){
                    continue;
                }
               
                if(!empty($gvalue['last_seen_time']) && ($gvalue['show_personal'] == 0)) {
   
                    $lastseen = date('d-m-Y H:i',strtotime($gvalue['last_seen_time']));
                    
                    //24 hours last update time check
                    $twenty_four_time = date('d-m-Y H:i',strtotime('-24 hour'));
                    
                    if((strtotime($twenty_four_time) > strtotime($lastseen)) && ($usertype== 'member')){
                        continue;
                    }
                    
                    $currtime = date('d-m-Y H:i',strtotime('-1 hour'));
                    $lastup   = 1;
                    
                    if(strtotime($currtime) <= strtotime($lastseen)){
                       $lastup   = 1; 
                    }
                    
                    if((strtotime($currtime) > strtotime($lastseen)) && (strtotime($twenty_four_time) < strtotime($lastseen))) {  
                        if($usertype == 'member') {
                           continue;
                        }

                      $lastup = 0;
                    }   
                }     
                                
               // if(!empty($gvalue['last_seen_time'])) {
//                   
//                   $lastseen = date('d-m-Y H:i',strtotime($gvalue['last_seen_time']));
//                   $currtime = date('d-m-Y H:i',strtotime('-1 hour'));
//                
//                    $lastup = 1;
//                    
//                    if(strtotime($currtime) >= strtotime($lastseen)) {  
//                        
//                        if($gvalue['user_type'] == 'member') {
//                           continue;
//                        }
//                        
//                      $lastup = 0;
//                    
//                    }   
//                }   
                
//                
                $users[$i]['user_id']         = $gvalue['id'];
                $users[$i]['default_id']      = $gvalue['default_id'];
                $users[$i]['display_name']    = $gvalue['display_name'];
                $users[$i]['satiation_id']    = $gvalue['satiation_id'];
                $users[$i]['is_overwrite']    = $gvalue['is_overwrite'];
                $users[$i]['is_show']         = $gvalue['is_show'];
                $users[$i]['is_contact']      = $gvalue['is_contact'];
                $users[$i]['phonenumber']     = $gvalue['phonenumber'];
                $users[$i]['profile_image']   = $this->profile_url."/thumb_".$gvalue['id'].".jpg";
                $users[$i]['track']           = $gvalue['is_tracked'];
                $users[$i]['join_key']        = $group_data['join_key'];
                $users[$i]['description']     = $group_data['description'];
                $users[$i]['group_id']        = $gvalue['gid'];
                $users[$i]['lat']             = $gvalue['lat'];
                $users[$i]['lon']             = $gvalue['lon'];
                $users[$i]['accuracy']        = $gvalue['accuracy'];
                $users[$i]['user_type']       = $usertype;
                $users[$i]['email']           = $gvalue['email'];
                $users[$i]['blocked']         = $gvalue['blocked'];
                $users[$i]['is_online']       = $lastup;
                $users[$i]['share_link']      = site_url()."search/".$group_data['join_key'];
                $users[$i]['invisible']       = (($gvalue['lat'] == 0 && $gvalue['lon'] == 0) || ($gvalue['invisible']==1))?1:0;
                $users[$i]['is_view']         = $gvalue['is_view'];
                $users[$i]['last_seen_time']  = strtotime($gvalue['last_seen_time']);
                
                
                //$users[$i]['static_map']['maps']   = $static_maps;
            
                $i++;
            }
            
            $static_maps = $this->user_model->get_static_maps('',$group_data['id']);
            
            $static_maps  = (count($static_maps)>0)?$static_maps:array();
            
            return $this->response(array("status" => 'success','members' => $users, 'static_maps' => $static_maps),200);
        }
        else
        {
            return $this->response(array('status' => "error",'msg' => "Member doesn't exist.",'error_code' => 102), 404);
        }    
   }
   
    //Delete one member of the group 
   function delete_member_get()
   {
       //check for required values
    if(!$this->get('group_id') && !$this->get('user_id')) {
      return $this->response(array('status' => 'error','msg' => 'Required fields missing in your request','error_code' => 1), 404);
    }
                
        $this->load->library("FCM");
        
        $group_id  = $this->get('group_id');
        $user_id   = $this->get('user_id');
        
        $this->user_groups_model->delete(array('group_id' => $group_id, 'user_id' => $user_id));
        
        //check if member active group exist or not
        $status = $this->user_groups_model->check_unique(array("user_id" => $user_id, 'status' => 1));
        
        $ins_data = array();
        $ins_data['status'] = 1;
            
            
        if(!count($status)) {
           //make the user default map is active
            $get_group_name = $this->user_model->get_defult_group($user_id);
            
            $this->group_model->update($ins_data,array("id" => $get_group_name['id']));
            
            $this->user_groups_model->update($ins_data,array("group_id" => $get_group_name['id'],"user_id" => $user_id));
         }
         
         //send notification to delete member
         $user_data = $this->user_model->check_unique(array("id" => $user_id));
            
        if(count($user_data)) {
                $gcm_id     = $user_data['gcm_id'];
                $group_name = $this->group_model->check_unique(array("id" => $group_id));
                $msg = "Your are disconnected from this group (".$group_name['join_key'].")";

                $gcm_data = array();
                $gcm_data['default_id'] = $user_data['default_id'];               
                $gcm_data['join_key']   = $group_name['join_key'];                  
                $gcm_data['method']     = 'disconnect';              
                $gcm_data['msg']        = $msg;
                
                //insert notification to DB
                $this->insert_notification($user_id,$group_name['join_key'],$gcm_data,$user_id);

                $this->fcm->send_notification(array($gcm_id),array("hmg" => $gcm_data));
        }
        if($user_data['login_type'] == 'app') {
            
            //get group data
            $group_data   = $this->db->query("select * from groups where id='".$group_id."'")->row_array();
            //get user plan
         //   $plan_data = get_plan_data($group_data['user_id']);
          //  $members_count = $plan_data['members_count'] + 1;
            //update_plan_data($group_data['user_id'],$members_count,'map');
        }
        
        return $this->response(array("status" => 'success'),200);    
   } 
   
   //Get Particular user information like position,profile,account and group
   
   function get_user_information_get()
   {
    
         //check for required values
    if(!$this->get('join_key') && !$this->get('user_id')) {
      return $this->response(array('status' => 'error','msg' => 'Required fields missing in your request','error_code' => 1), 404);
    }
                
        $join_key = $this->get("join_key");
        $user_id  = $this->get("user_id");
        
        $user_info = $this->user_groups_model->get_user_info($join_key,$user_id);
        
        if(count($user_info)) {
           return $this->response(array("status" => 'success','user' => $user_info),200);     
        }
        else
        {
            return $this->response(array('status' => "error",'msg' => 'Invalid Details','error_code' => 101), 404);
        }
   }

   function get_channel_byuser_get()
   {
       //check for required values
        if(!$this->get('user_id')) {
          return $this->response(array('status' => 'error','msg' => 'Required fields missing in your request','error_code' => 1), 404);
        }

        $join_key  = $this->get("join_key");        
        $user_id   = $this->get("user_id");
        $user_info = $this->user_groups_model->get_active_group($user_id);

        $active_group = $this->user_groups_model->get_user_active_group($user_id);
        $joined_group = (count($active_group))?$active_group['join_key']:'';
        
        if(count($user_info)) {
           return $this->response(array("status" => 'success','user' => $user_info,'joined_group'=>$joined_group),200);     
        }
        else
        {
            return $this->response(array('status' => "error",'msg' => 'Invalid Details','error_code' => 101), 404);
        }
   }
   
   //Update user display name
   function update_display_name_get()
   {
         //check for required values
    if(!$this->get('user_id')) {
      return $this->response(array('status' => 'error','msg' => 'Required fields missing in your request','error_code' => 1), 404);
    }
                
         $user_id      = $this->get("user_id");
         $display_name = $this->get("display_name");
         $default_id   = $this->get("channel_id"); 
         
         $ins_data = array();
         $ins_data['display_name'] = $display_name;

         if($default_id!='')
            $ins_data['default_id'] = $default_id;
         
         $this->user_model->update("user",$ins_data,array("id" => $user_id));
         
        return $this->response(array("status" => 'success','user_id' => $user_id),200);   
   }

   function update_groupanduser_get(){

      if(!$this->get('user_id') && !$this->get('group_id'))
        return $this->response(array('status' => 'error','msg' => 'Required fields missing in your request','error_code' => 1), 404);
      
      $user_id      = $this->get("user_id");
      $display_name = $this->get("display_name");
      $channel_id   = $this->get("channel_id"); 
      $group_id     = $this->get('group_id');

  
        //update group info
        $group_data = array();
        $group_data['description'] = $display_name;  
        $group_data['join_key']    = $channel_id;
        create_group($channel_id, $user_id,'default',0,0);
        $this->group_model->update($group_data,array('id'=>$group_id));

        //update user info
        $ins_data = array();
        $ins_data['display_name'] = $display_name;  
        $ins_data['default_id']   = $channel_id;
        $this->user_model->update("user",$ins_data,array("id" => $user_id));

        return $this->response(array("status" => 'success','user_id' => $user_id),200);   
   }
   
   //username send to user email
   function username_recovery_get()
   {
    
         //check for required values
    if(!$this->get('email')) {
      return $this->response(array('status' => 'error','msg' => 'Required fields missing in your request','error_code' => 1), 404);
    }
                
        $email = $this->get("email");
        
        $result = $this->user_model->check_unique(array("email" => $email));
        
         if(count($result)) {
            
             $username = $result['default_id'];
             $message  = "<p>Hi $username,</p><br /><br /><br />";
             $message .= "Please see below your username. <br /><br />";
             $message .= "<b>Username</b> :".$username;
             $message .= "<br /><br /><br /><br /><br />";
             $message .= "<p>Thanks,<p>";
             $message .= "<p><a href='http://heresmygps.com/'>Heresmygps.com</a></p>"; 
              
             $config['charset'] = 'iso-8859-1';
             $config['wordwrap'] = TRUE;
             $config['mailtype'] = 'html';
             $this->load->library('email'); 
             $this->email->set_newline("\r\n");
             $this->email->initialize($config);
             
             $this->email->from('contact@heresmygps.com','Contact');
             $this->email->to($email);
             $this->email->subject('Username Recovery');
             $this->email->message($message);
             $this->email->send();
            
            return $this->response(array('status' =>'success','request_type' => 'username_recovery','email' => $email,'user_id' => $result['id']), 200);        
        }
        else
        {
            return $this->response(array('status' => "error",'request_type' => 'username_recovery','msg' => 'Invalid Mail ID.','error_code' => 11), 404);
        }
   }
   
   //Logout service
   function logout_get()
   {
     
       //check for required values
    if(!$this->get('user_id')) {
      return $this->response(array('status' => 'error','msg' => 'Required fields missing in your request','error_code' => 1), 404);
    }
                
        $user_id = $this->get('user_id');
        
        $ins_data =array();
        $ins_data['logged_in'] = 0;
        
        $this->user_model->update('user',$ins_data,array("id" => $user_id));
        
        return $this->response(array("status" => 'success', 'user_id' => $user_id), 200);
        
   }

   //Force update
   
   function force_update_get($user_id='',$join_key='',$response_flag=TRUE)
   {
        $user_id = $this->get('user_id');
        $join_key = $this->get('join_key');

       
        if(!(int)$user_id && empty($join_key))
            return $this->response(array('status' => 'error','msg' => "Required fields missing in your request",'error_code' => 1), 404);

        $groups = $this->group_model->check_unique(array("join_key" => $join_key));
        
        if(!count($groups))
           return $this->response(array('status' => "error",'msg' => "Group doesn't exists.",'error_code' => 110), 404); 
        

        $res = $this->user_groups_model->check_unique(array('user_id' => $user_id, 'group_id' => $groups['id'], 'status'=>'1'));
        
        if(!count($res))
            return $this->response(array('status' => 'error','msg' => "User doesn't exist",'error_code' => 1), 404);
        
        $gcmdata    = $this->group_model->get_single_user_gcm($user_id);

        $user_data = $this->user_model->check_unique(array("id" => $user_id));
        
        $this->load->library("FCM");
        
        $gcm_id   = $gcmdata['gcm_id'];
       
        $gcm_data = array();
        $gcm_data['user_id']        = $user_id;
        $gcm_data['join_key']       = $join_key;
        $gcm_data['default_id']     = (!empty($user_data['default_id']))?$user_data['default_id']:$user_data['phonenumber'];
        $gcm_data['method']         = 'force_update';
        $gcm_data['msg']            = "Sends Force update position to ". $gcm_data['default_id']." and group of ".$join_key;
             
        //insert notification to DB
        $this->insert_notification($user_id,$join_key,$gcm_data);
                
        $this->fcm->send_notification(array($gcm_id),array("hmg" => $gcm_data)); 

        if($response_flag)
            return $this->response(array("status" => 'success', 'user_id' => $user_id, 'join_key' => $join_key), 200);
              
   }

   function send_offline_join_request_get($join_key,$user_id,$type='admin')
   {
    
        //check for required values
        if(!$this->get('join_key') && !$this->get('user_id')) {
            return $this->response(array('status' => 'error','msg' => 'Required fields missing in your request','error_code' => 1), 404);
        }
      
        $user_id  = $this->get('user_id');
        $join_key = $this->get('join_key');
        $type     = $this->get('type');
        
                
        if($type=='admin')
            $result    = $this->group_model->get_group_owner_gcm($join_key);
        else
            $result    = $this->group_model->get_single_user_gcm($user_id);

        $user_data = $this->user_model->check_unique(array("id" => $user_id));
        
        $this->load->library("FCM");
        
        $gcm_id   = $result['gcm_id'];

        $gcm_data = array();
        $gcm_data['user_status']    = (!empty($user_status))?$user_status:'offline';
        $gcm_data['join_key']       = $join_key;
        $gcm_data['user_id']        = $user_id;
        $gcm_data['type']           = $type;
        $gcm_data['default_id']     = (!empty($user_data['default_id']))?$user_data['default_id']:$user_data['phonenumber'];
        $gcm_data['method']         = 'offline_request';

        $msg = ($type=='admin')?$gcm_data['default_id'].' wants to join in your Channel ID '.$join_key : 'Your request has been accepted to Channel ID '.$join_key;

        $gcm_data['msg']            =  $msg;
        
        //insert notification to DB
        $this->insert_notification($user_id,$join_key,$gcm_data);

        $this->fcm->send_notification(array($gcm_id),array("hmg" => $gcm_data));

        return $this->response(array('status' => 'success', 'join_key' => $join_key,'user_id'=> $user_id), 200);
   }

   function insert_notification($user_id='',$join_key='',$message, $notification_staus='',$from_id='')
   {
        if(!(int)$user_id)
            return false;

        $ins_data = array();
        $ins_data['user_id']     = $user_id;
        $ins_data['from_id']     = $from_id;
        $ins_data['join_key']    = $join_key;
        $ins_data['message']     = json_encode($message);
        $ins_data['is_viewed']   = 0;
        $ins_data['date_created']= date('Y-m-d H:i:s');
        $ins_data['notification_status'] = json_encode($notification_staus);

        $this->user_model->insert_notification($ins_data);
       // echo $this->db->last_query();die;
   }

   function user_notifications_get($user_id)
   {
        $user_id  = $this->get('user_id');
        $join_key = $this->get('join_key');
        $join_key = (empty($join_key))?"":$this->get('join_key');

        if(!(int)$user_id)
            return $this->response(array('status' => 'error','msg' => 'Required fields missing in your request','error_code' => 1), 404);

        $res   = $this->user_model->get_user_notifications($user_id,$join_key);
        
        $where = (!empty($join_key))?"user_id ='".$user_id."' and join_key='".$join_key."'":"user_id='".$user_id."'";

        $notification_count = $this->db->query("select count(*) as cnt from user_notifications where $where and is_viewed='0'")->row_array();          

       // $group_message_count= $this->db->query("select count(*) as cnt from group_message where $where and is_viewed='0'")->row_array();

        //$cnt = $group_message_count['cnt'] + $notification_count['cnt'];
        $cnt = $notification_count['cnt'];

        if(empty($res))
            return $this->response(array('status' => 'error','msg' => 'No messages received!','error_code' => 101), 404);
        else    
            return $this->response(array('status' => 'success', 'message_list' => $res,'notification_count' => $cnt,'user_id'=> $user_id), 200);
   }

   function update_notification_view_get($msg_id)
   {
        $msg_id  = $this->get('msg_id');
        $type    = $this->get('type');
        $join_key= $this->get('join_key');

        $field   = (!empty($type) && ($type == 'user'))?"user_id":"id";

        $res     = $this->user_model->update('user_notifications',array('is_viewed'=>1),
                                         array("$field" => $msg_id));

       // $res     = $this->user_model->update('group_message',array('is_viewed'=>1),
                                //         array("join_key" => $join_key));

        return $this->response(array('status' => 'success', 'msg' => 'View status updated'), 200);
   }
   
   

   function update_user_block_status_get()
   {
      $user_id  = $this->get('user_id');
      $group_id = $this->get('group_id');
      $blocked  = $this->get('blocked');

      if(!$this->get('user_id') && !$this->get('group_id')) 
            return $this->response(array('status' => 'error','msg' => 'Required fields missing in your request','error_code' => 1), 404);
        
       $this->user_groups_model->update(array('blocked'=>$blocked),array('user_id'=>$user_id,'group_id'=>$group_id)); 
       
       return $this->response(array('status' => 'success','blocked'=>$blocked, 'user_id'=>$user_id, 'group_id'=>$group_id, 'msg' => 'View status updated'), 200); 
   }

   function user_exist_check_get()
   {
      $status='success';

      if(!$this->get('user_id'))
        $status='error';

      $user_id  = $this->get('user_id');

      $res = $this->user_model->check_unique(array('id'=>$user_id));
      
      if(count($res)==0)
        $status='error';

      return $this->response(array('status' => $status,'request_type'=>'user_check'), 200);
       
   }

   function update_trigger_positions_get()
   {
      if(!$this->get('user_id') && $this->get('positions')!='') 
            return $this->response(array('status' => 'error','msg' => 'Required fields missing in your request','error_code' => 1), 404);
      
      $user_id  = $this->get('user_id');
      $positions = $this->get('positions');  

      $positions = json_decode($positions,TRUE);

      $res = $this->user_position_model->update_positions($user_id,$positions);

      return $this->response(array('status' => 'success','request_type'=>'position_update'), 200);
   }
   
   function get_trigger_positions_get()
   {
     if($this->get('user_id')=='') 
            return $this->response(array('status' => 'error','msg' => 'Required fields missing in your request','error_code' => 1), 404);
      
     $users  = json_decode($this->get('user_id'),TRUE);
    
     $time_limit = $this->get('time_limit');

      $res = $this->user_position_model->get_trigger_positions($users,$time_limit);

      return $this->response(array('status' => 'success','user_id'=>$users,'positions'=>$res), 200);
   }

   function create_user_static_map_get()
   {
      if(!$this->get('user_id') && !$this->get('group_id') && !$this->get('map_name') && !$this->get('lat') && !$this->get('lon'))
          return $this->response(array('status' => 'error','msg' => 'Required fields missing in your request','error_code' => 1), 404);

        $group_id     = $this->get('group_id');
        //get group data
        $group_data   = $this->db->query("select * from groups where id='".$group_id."'")->row_array();
        $user_id      = $this->get('user_id');
        
        //get user plan
        $plan_data = get_plan_data($group_data['user_id']);
       
        
        //check if 
        $st_grp_ct  = $this->user_model->check_user_static_groupct($group_id);
        
       
       if(($st_grp_ct == $plan_data['clues_count']) && ($plan_data['clues_count'] !=0)){
            return $this->response(array('status' => 'error','user_id'=>$user_id,'msg' => 'Your Free plan has been expired You need to upgrade pro plan!','error_code' => 1), 404);
       }
       
       
       $map_name     = $this->get('map_name');
       $lat          = $this->get('lat');
       $lon          = $this->get('lon');
       $clue_image   = $this->get('clue_image');
       $notes        = $this->get('notes');

       $source_image   = "var/www/html/heresmygps/assets/uploads/profile/".$clue_image;
       $image_crop_url = "var/www/html/heresmygps/assets/uploads/profile/resize";
        
        
       $res = $this->user_model->check_user_static_group($user_id,$group_id,$map_name);

       if($res>0)
          return $this->response(array('status' => 'error','user_id'=>$user_id,'msg' => 'The static map already exists from this group user!','error_code' => 1), 404);
         
        $ins_data = array();
        $ins_data['user_id']     = $user_id;
        $ins_data['group_id']    = $group_id;
        $ins_data['map_name']    = $map_name;
        $ins_data['clue_image']  = $clue_image;
        $ins_data['lat']         = $lat;
        $ins_data['lon']         = $lon;
        $ins_data['notes']       = $notes;
        $ins_data['status']      = 1;
        $ins_data['created_time']= strtotime(date('Y-m-d H:i:s'));
        
        $static_map_id = $this->user_model->insert('user_static_maps',$ins_data);
        
        if(!empty($clue_image)) {
            $imag_name = "static_".$group_id."_".$user_id."_".$static_map_id.".jpg";
            //image_crop($source_image,$image_crop_url,$imag_name);
        }

        return $this->response(array('status' => 'success','user_id'=>$user_id,'group_id' => $group_id,'map_name'=>$map_name, 'static_map_id' => $static_map_id), 200);

   }

   function delete_user_static_map_get()
   {
      if(!$this->get('user_id') && !$this->get('group_id') && !$this->get('map_name'))
          return $this->response(array('status' => 'error','msg' => 'Required fields missing in your request','error_code' => 1), 404);

      $user_id      = $this->get('user_id');
      $group_id     = $this->get('group_id');
      $map_name     = $this->get('map_name');  

      $where = array('user_id'=>$user_id,'group_id'=>$group_id,'map_name'=>$map_name);
      $this->user_model->delete('user_static_maps',$where);

      return $this->response(array('status' => 'success','user_id'=>$user_id,'group_id' => $group_id,'map_name'=>$map_name), 200);
   }   

    function rand_str_get($length = 8)
    {

        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890';

        // Length of character list
        $chars_length = (strlen($chars) - 1);

        // Start our string
        $string = $chars{rand(0, $chars_length)};
       
        // Generate random string
        for ($i = 1; $i < $length; $i = strlen($string))
        {
            // Grab a random character from our list
            $r = $chars{rand(0, $chars_length)};
           
            // Make sure the same two characters don't appear next to each other
            if ($r != $string{$i - 1}) $string .=  $r;
        }
       
        // Return the string
        return $string;
    }
    
    //check if user joined or not searched map
    
    function check_user_joined_map_get()
    {
        if(!$this->get('user_id') && !$this->get('join_key'))
          return $this->response(array('status' => 'error','msg' => 'Required fields missing in your request','error_code' => 1), 404);
        
        $user_id  = $this->get('user_id'); 
        $join_key = $this->get('join_key');
        
        //get group details
        $group = $this->group_model->check_unique(array('join_key' => $join_key));
        
        if(!count($group)) {
             return $this->response(array('status' => "error",'msg' => "Group doesn't exists.",'error_code' => 110), 404);
        }
        
        $result = $this->user_groups_model->check_unique(array('user_id' => $user_id, 'group_id' => $group['id']));
        
        if(count($result))
            return $this->response(array("status" => 'success','group_id' => $group['id'], 'is_joined' => 1), 200);
        else
           return $this->response(array('status' => 'success', 'group_id' => $group['id'], 'is_joined' => 0), 200);  
    }
   
   //set favourite channel ids
    function favourite_channel_get()
    {
        //check for required values
    if(!$this->get('group_id')){
      return $this->response(array('status' => 'error','msg' => 'Required fields missing in your request','error_code' => 1), 404);
    }  
        
        $join_key  = $this->get("group_id"); 
        
        $result = $this->group_model->check_unique(array("join_key" => $join_key));
        
        if(!count($result)) {
            return $this->response(array('status' => "error",'msg' => 'Invalid Group','error_code' => 101), 404);
        }
            
        $ins_data  = array();
        $ins_data['is_favourite'] = 1;
        $this->group_model->update($ins_data,array('group_id' => $result['id']));
        
        return $this->response(array('status' =>'success', 'request_type' => 'set_channel_favourite','join_key' => $join_key, 'favourite' => 1), 200);  
    }
    
    //set flag channel ids
    function flag_channel_get()
    {
        //check for required values
    if(!$this->get('group_id')){
      return $this->response(array('status' => 'error','msg' => 'Required fields missing in your request','error_code' => 1), 404);
    }  
        
        $join_key  = $this->get("group_id"); 
        
        $result = $this->group_model->check_unique(array("join_key" => $join_key));
        
        if(!count($result)) {
            return $this->response(array('status' => "error",'msg' => 'Invalid Group','error_code' => 101), 404);
        }
            
        $ins_data  = array();
        $ins_data['is_flag'] = 1;
        $this->group_model->update($ins_data,array('group_id' => $result['id']));
        
        return $this->response(array('status' =>'success', 'request_type' => 'set_channel_flag','join_key' => $join_key, 'flag' => 1), 200);  
    }
    
    //manage channel ids
    function manage_channel_ids_get()
    {
       
       //check for required values
      if(!$this->get('user_id')){
        return $this->response(array('status' => 'error','msg' => 'Required fields missing in your request','error_code' => 1), 404);
      } 
        
        $user_id = $this->get('user_id');
        
        $this->load->model(array("favourite_model","flag_model"));
        
        //user own channels
        $own_channels = $this->group_model->get_own_channel($user_id);

        //user joined channels
        $joined_channels = $this->user_groups_model->get_joined_channels($user_id);
      
        foreach($joined_channels as $jkey => $jvalue) {
            if(!in_array($jvalue['join_key'], array_column($own_channels, 'join_key'))) {
                array_push($own_channels,$joined_channels[$jkey]);
            }
        } 

        if(!count($own_channels)) {
            return $this->response(array('status' => "error",'msg' => 'No Channels Found!','error_code' => 102), 404);
        }
           
        $manage_channels = array();    
        
        foreach($own_channels as $ckey => $cvalue) {
            
           $manage_channels[$ckey]['channel_id']     = $cvalue['join_key'];
           $manage_channels[$ckey]['group_id']       = $cvalue['gid'];
           $manage_channels[$ckey]['admin_id']       = $cvalue['admin_id'];
           $manage_channels[$ckey]['type']           = $cvalue['type'];
           $manage_channels[$ckey]['is_favourite']   = $cvalue['is_favourite'];
           $manage_channels[$ckey]['last_seen_time'] = strtotime($cvalue['last_seen_time']);
           
           $members = $this->group_model->get_channel_members($cvalue['id'],0);
           
           if(count($members) > 0) {
           
        for($i=0; $i<count($members); $i++) {
            $group     = $this->group_model->check_unique(array("id" => $cvalue['gid']));
          //favourite and flag values
          $favourite = $this->favourite_model->check_unique(array("favourite_user_id" => $members[$i]['uid'], "group_id" => $cvalue['gid']));
          $flag      = $this->flag_model->check_unique(array("flag_user_id" => $members[$i]['uid'], "group_id" => $cvalue['gid']));
          
          $manage_channels[$ckey]['members'][$i]['default_id']    = $members[$i]['default_id'];
          $manage_channels[$ckey]['members'][$i]['phonenumber']   = $members[$i]['phonenumber'];
          $manage_channels[$ckey]['members'][$i]['display_name']  = $members[$i]['display_name'];
          $manage_channels[$ckey]['members'][$i]['email']         = $members[$i]['email'];
          $manage_channels[$ckey]['members'][$i]['profile_image'] = $this->profile_url."/thumb_".$members[$i]['uid'].".jpg";
          $manage_channels[$ckey]['members'][$i]['default_id']    = $members[$i]['default_id'];
          $manage_channels[$ckey]['members'][$i]['flag']          = ((!empty($flag)) && (count($flag)>0))?1:0;
          $manage_channels[$ckey]['members'][$i]['block']         = $members[$i]['blocked'];
          $manage_channels[$ckey]['members'][$i]['favourite']     = (isset($favourite['favourite']) && !empty($favourite['favourite']))?$favourite['favourite']:0;
          $manage_channels[$ckey]['members'][$i]['last_seen_time']= $members[$i]['last_seen_time'];
          $manage_channels[$ckey]['members'][$i]['user_id']       = $members[$i]['uid'];
          $manage_channels[$ckey]['members'][$i]['satiation_id']  = $members[$i]['satiation_id'];
          $manage_channels[$ckey]['members'][$i]['is_overwrite']  = $members[$i]['is_overwrite'];
                   // $manage_channels[$ckey]['members'][$i]['is_favourite']  = $group['is_favourite'];
                  //  $manage_channels[$ckey]['members'][$i]['type']          = $group['type']; 
        } 
            
      }else {
        $manage_channels[$ckey]['members']= array();
      }
       
        }
        return $this->response(array('status' =>'success', 'request_type' => 'manage_channels','channels' => $manage_channels), 200);  
    }
    
    //group member favourite
    function favourite_user_get()
    {
      //check for required values
    if((!$this->get('user_id')) && (!$this->get('favourite_user_id')) && (!$this->get('group_id'))){
      return $this->response(array('status' => 'error','msg' => 'Required fields missing in your request','error_code' => 1), 404);
    }   
        
        $user_id           = $this->get("user_id");
        $favourite_user_id = $this->get("favourite_user_id");
        $group_id          = $this->get("group_id");
        $favourite         = $this->get("favourite");
        
        $this->load->model("favourite_model");
        
        $result = $this->favourite_model->check_unique(array("favourite_user_id" => $favourite_user_id, "group_id" => $group_id, "user_id" => $user_id));
        
        $ins_data = array();
            
        if(!count($result)) {
            $ins_data['favourite_user_id'] = $favourite_user_id;
            $ins_data['user_id']           = $user_id;
            $ins_data['group_id']          = $group_id;
            $ins_data['favourite']         = $favourite; 
            $favourite_insert_id = $this->favourite_model->insert($ins_data);
            return $this->response(array('status' =>'success', 'request_type' => 'favourite_user', 'favourite_id' => $favourite_insert_id), 200);
        }
        else
        {
            $ins_data['favourite']         = $favourite; 
            $favourite_insert_id = $this->favourite_model->update($ins_data,array("user_id" => $user_id, "favourite_user_id" => $favourite_user_id, "group_id" => $group_id ));
            return $this->response(array('status' =>'success', 'request_type' => 'unfavourite_user'), 200);
        }
    }
    
    //group member flag
    function flag_user_get()
    {
      //check for required values
    if((!$this->get('user_id')) && (!$this->get('flag_user_id')) && (!$this->get('group_id')) && (!$this->get('flag_message'))){
      return $this->response(array('status' => 'error','msg' => 'Required fields missing in your request','error_code' => 1), 404);
    }   
        
        $user_id           = $this->get("user_id");
        $flag_user_id      = $this->get("flag_user_id");
        $group_id          = $this->get("group_id");
        $flag              = $this->get("flag_message");
        
        
        $this->load->model("flag_model");
        
       // $result = $this->flag_model->check_unique(array("user_id" => $user_id,"flag_user_id" => $flag_user_id, "group_id" => $group_id));
        
        //if(!count($result)) {
            $ins_data = array();
            $ins_data['flag_user_id']      = $flag_user_id;
            $ins_data['user_id']           = $user_id;
            $ins_data['group_id']          = $group_id;
            $ins_data['flag_message']      = $flag ;
            $flag_insert_id = $this->flag_model->insert($ins_data);
            return $this->response(array('status' =>'success', 'request_type' => 'flag_user'), 200);
       // }
      //  else
      //  {
        //    return $this->response(array('status' =>'error', 'request_type' => 'flag_user', 'msg' => 'Already this user flagged for this group', 'error_code' => 7), 404);
      //  }
    }
    
    //get blocked members
    function blocked_members_get()
    {
        //check for required values
    if(!$this->get('user_id')){
      return $this->response(array('status' => 'error','msg' => 'Required fields missing in your request','error_code' => 1), 404);
    } 
        
        $user_id  = $this->get("user_id");
        
        $this->load->model("group_model");
        
        //user own channels
        $own_channels = $this->group_model->get_own_channel($user_id);
        
        $blocked_members = array();
        foreach($own_channels as $ckey => $cvalue) {
            
           $blocked_members[$ckey]['channel_id']     = $cvalue['join_key'];
           $blocked_members[$ckey]['group_id']       = $cvalue['gid'];
           $blocked_members[$ckey]['admin_id']       = $cvalue['admin_id'];
           $blocked_members[$ckey]['type']           = $cvalue['type'];
           $blocked_members[$ckey]['is_favourite']   = $cvalue['is_favourite'];
           $blocked_members[$ckey]['last_seen_time'] = strtotime($cvalue['last_seen_time']);
           
           $members = $this->group_model->get_channel_members($cvalue['id'],1);
            if(!count($members)){
                $blocked_members[$ckey]['members'] = array();
            }
        
            for($i=0; $i<count($members); $i++) {
                $blocked_members[$ckey]['members'][$i]['default_id']    = $members[$i]['default_id'];
                $blocked_members[$ckey]['members'][$i]['phonenumber']   = $members[$i]['phonenumber'];
                $blocked_members[$ckey]['members'][$i]['display_name']  = $members[$i]['display_name'];
                $blocked_members[$ckey]['members'][$i]['email']         = $members[$i]['email'];
                $blocked_members[$ckey]['members'][$i]['profile_image'] = $this->profile_url."/thumb_".$members[$i]['uid'].".jpg";
                $blocked_members[$ckey]['members'][$i]['default_id']    = $members[$i]['default_id'];
                $blocked_members[$ckey]['members'][$i]['flag']          = $members[$i]['is_tracked'];
                $blocked_members[$ckey]['members'][$i]['block']         = $members[$i]['blocked'];
                $blocked_members[$ckey]['members'][$i]['favourite']     = $members[$i]['is_favourite'];
                $blocked_members[$ckey]['members'][$i]['last_seen_time']= $members[$i]['last_seen_time'];
                $blocked_members[$ckey]['members'][$i]['user_id']       = $members[$i]['uid'];  
            }  
        }
       
        
       
        if(count($blocked_members) > 0){
            return $this->response(array('status' =>'success', 'request_type' => 'blocked_members_lists', 'channels' => $blocked_members), 200);
        }
        else
        {
            return $this->response(array('status' =>'error', 'request_type' => 'blocked_members_lists', 'msg' => 'No Members Found', 'error_code' => 102), 404);
        }
    }
    
    //get member flag
    function flag_messages_get()
    {
      //check for required values
    if((!$this->get('user_id')) && (!$this->get('flag_user_id')) && (!$this->get('group_id'))){
      return $this->response(array('status' => 'error','msg' => 'Required fields missing in your request','error_code' => 1), 404);
    }   
        
        $this->load->model("flag_model");
        
        $user_id           = $this->get("user_id");
        $flag_user_id      = $this->get("flag_user_id");
        $group_id          = $this->get("group_id");
        
        $flags = $this->flag_model->get_flags(array("user_id" => $user_id, "flag_user_id" => $flag_user_id, "group_id" => $group_id));
        
        if(count($flags) > 0){
            return $this->response(array('status' =>'success', 'request_type' => 'get_flags', 'flags' => $flags), 200);
        }
        else
        {
            return $this->response(array('status' =>'error', 'request_type' => 'get_flags', 'msg' => 'No Flags Found', 'error_code' => 102), 404);
        }
    }
    
    //delete flag
    function delete_flag_get()
    {
      //check for required values
    if((!$this->get('flag_id'))){
      return $this->response(array('status' => 'error','msg' => 'Required fields missing in your request','error_code' => 1), 404);
    }   
        
        $this->load->model("flag_model");
        
        $flag_id   = $this->get("flag_id");
        $flags     = $this->flag_model->delete(array("id" => $flag_id));
        
        return $this->response(array('status' =>'success', 'request_type' => 'delete_flag'), 200); 
    }
    
    //get user own groups
    function user_own_channels_get()
    {
         //check for required values
    if((!$this->get('user_id'))){
      return $this->response(array('status' => 'error','msg' => 'Required fields missing in your request','error_code' => 1), 404);
    }   
        
        $this->load->model("group_model");
        
        $user_id     = $this->get("user_id");
        $groups      = $this->group_model->get_user_own_channels(array("g.user_id" => $user_id));
        //echo $this->db->last_query(); exit;
        $channels    = array();
        foreach($groups as $gkey => $gvalue){
            $channels[$gkey]['join_key']         = $gvalue['join_key'];
            $channels[$gkey]['name']             = $gvalue['name'];
            $channels[$gkey]['password_protect'] = $gvalue['password_protect'];
            $channels[$gkey]['password']         = $gvalue['password'];
            $channels[$gkey]['allow_deny']       = $gvalue['allow_deny'];
            $channels[$gkey]['group_description']= $gvalue['description'];
            $channels[$gkey]['type']             = $gvalue['type'];
            $channels[$gkey]['lat']              = $gvalue['lat'];
            $channels[$gkey]['lon']              = $gvalue['lon'];
            $channels[$gkey]['status']           = $gvalue['status'];
            $channels[$gkey]['date_created']     = $gvalue['date_created'];
            $channels[$gkey]['date_updated']     = $gvalue['date_updated'];
            $channels[$gkey]['map_avail']        = $gvalue['map_avail'];
            $channels[$gkey]['id']               = $gvalue['gid'];
            $channels[$gkey]['user_id']          = $gvalue['user_id'];
            $channels[$gkey]['is_available']     = $gvalue['is_available'];
            $channels[$gkey]['is_favourite']     = $gvalue['is_favourite'];
            $channels[$gkey]['is_flag']          = $gvalue['is_flag'];
            $channels[$gkey]['location_type']    = $gvalue['location_type'];
            $channels[$gkey]['display_name']     = $gvalue['display_name'];
            $channels[$gkey]['satiation_id']     = $gvalue['satiation_id'];
            $channels[$gkey]['is_overwrite']     = $gvalue['is_overwrite'];
            $channels[$gkey]['profile_image']    = $this->profile_url."/thumb_static_".$gvalue['gid']."_".$gvalue['uid'].".jpg";
            
        }
        
        if(count($groups) > 0){
            return $this->response(array('status' =>'success', 'request_type' => 'user_own_groups', 'channels' => $channels), 200);
        }
        else
        {
            return $this->response(array('status' =>'error', 'request_type' => 'user_own_groups', 'msg' => 'No Channels Found', 'error_code' => 102), 404);
        } 
    }
    
    //update user satiation_id
    function user_update_satiation_id_get()
    {
         //check for required values
    if((!$this->get('user_id')) && (!$this->get('satiation_id'))){
      return $this->response(array('status' => 'error','msg' => 'Required fields missing in your request','error_code' => 1), 404);
    }   
        
        $this->load->model("user_model");
        
        $user_id     = $this->get("user_id");
        $sid         = $this->get("satiation_id");
        $groups      = $this->user_model->update("user",array("satiation_id" => $sid),array("id" => $user_id));
        
        return $this->response(array('status' =>'success', 'request_type' => 'user_update_satiation_id'), 200);   
    }
    
    //update user display name
    function user_update_display_name_get()
    {
         //check for required values
    if((!$this->get('user_id')) && (!$this->get('display_name'))){
      return $this->response(array('status' => 'error','msg' => 'Required fields missing in your request','error_code' => 1), 404);
    }   
        
        $this->load->model("user_model");
        
        $user_id     = $this->get("user_id");
        $display_name= $this->get("display_name");
        $groups      = $this->user_model->update("user",array("display_name" => $display_name),array("id" => $user_id));
        
        return $this->response(array('status' =>'success', 'request_type' => 'user_update_display_name'), 200);   
    }
    
    //get user own groups
    function user_update_is_overwrite_get()
    {
         //check for required values
    if((!$this->get('user_id')) && (!$this->get('is_overwrite'))){
      return $this->response(array('status' => 'error','msg' => 'Required fields missing in your request','error_code' => 1), 404);
    }   
        
        $this->load->model("user_model");
        
        $user_id     = $this->get("user_id");
        $sid         = $this->get("is_overwrite");
        $groups      = $this->user_model->update("user",array("is_overwrite" => $sid),array("id" => $user_id));
        
        return $this->response(array('status' =>'success', 'request_type' => 'user_update_is_overwrite'), 200);
         
    }
    
     //get user satiation -d
    function get_user_satiation_id_get()
    {
         //check for required values
    if(!$this->get('user_id')){
      return $this->response(array('status' => 'error','msg' => 'Required fields missing in your request','error_code' => 1), 404);
    }   
        
        $this->load->model(array("user_model","group_model"));
        $user_id     = $this->get("user_id");
        $satiationid = $this->user_model->check_unique(array("id" => $user_id));
        $gp_data     = $this->group_model->check_unique(array("join_key" => $satiationid['satiation_id']));
        
        $satiationid['password_protect'] = $gp_data['password_protect'];
        $satiationid['allow_deny']       = $gp_data['allow_deny'];
        $satiationid['group_password']   = $gp_data['password'];
        if(count($satiationid)) {
            return $this->response(array('status' =>'success', 'request_type' => 'get_user_satiation_id', 'user' => $satiationid), 200);
        }
        else
        {
            return $this->response(array('status' =>'error', 'request_type' => 'get_user_satiation_id', 'msg' => 'No User Found', 'error_code' => 102), 404);
        }   
    }
    
    
    
    /*  Get particular Plan details by passing plan_id */
    
    
   function plan_details_get()
   {
     if(!$this->get('plan_id')){
      return $this->response(array('status' => 'error','msg' => 'Required fields missing in your request','error_code' => 1), 404);
    }  
    
        $access_arr = array("free_joinable_hmgps_live_maps","access_maps_with_search_site","mutable_user","permanent_hmgps_userids_map_channels","number_of_joined_user_allowed_on_one_map","password_protection_for_maps","allow_or_deni_access","no_ads_on_app","no_ads_on_web");
        
    $plan_id = $this->get('plan_id');
    
        $plan_details =  $this->plan_model->get_plan_all_datas(array('t.type =' => 'app','p.id' => $plan_id));
        
        $plan_details_arr['detail'] = (array)$plan_details;
        $plan_access = $this->plan_model->get_plan_access(array("type" => "app", "plan_id" => $plan_id));
          
        $plan_acc = array();
        
         for($i=0; $i<count($access_arr); $i++)
         {
            $plan_acc[$i]['access_key']   = $access_arr[$i];
            $plan_acc[$i]['is_available'] = (isset($plan_access[$access_arr[$i]]) && !empty($plan_access[$access_arr[$i]]))?$plan_access[$access_arr[$i]]:0;
         } 
              
        $plan_details_arr['access'] = $plan_acc;
      
        return $this->response(array("status" => 'success', 'plans' => $plan_details_arr), 200);
   }
    
    /*  Plan select */
    
    function active_plan_get()
    { 
    if((!$this->get('user_id'))){
      return $this->response(array('status' => 'error','msg' => 'Required fields missing in your request','error_code' => 1), 404);
    }
    
    $plan_id   = $this->get('plan_id');
    $user_id   = $this->get('user_id');
      //  $type    = $this->get("type");
        $promo_id  = $this->get("promo_id");
        $pstatus   = $this->get("plan_status");
        
         $ins_data = array();
         
         $promo_members_count = ''; $plan_members_count = '';
         
         if(!empty($promo_id)){
            $plan_data    = $this->db->query("select * from promo where code='".$promo_id."'")->row_array();
            $ins_data['promo_id']      = $promo_id;
         }
         
         
         $plan_id = (empty($plan_id))?$plan_data['plan_id']:$plan_id;
         
         $allowed_info = $this->db->query("select * from plan_access where plan_id='".$plan_id."'")->row_array();
       
       //if(empty($promo_id)) {
        $ins_data['plan_id']       = $plan_id;
        $ins_data['user_id']       = $user_id;
        $ins_data['plan_status']   = $pstatus;
        $ins_data['members_count'] = (isset($allowed_info['number_of_joined_user_allowed_on_one_map']) && !empty($allowed_info['number_of_joined_user_allowed_on_one_map']))?$allowed_info['number_of_joined_user_allowed_on_one_map']:0;
        $ins_data['created_date']  = strtotime(date('Y-m-d'));
      // } 
       // print_r($ins_data);
        //$plan_data['participant_count'] = (isset($plan_data['participant_count']) && !empty($plan_data['participant_count']))?$plan_data['participant_count']:0; 
           
    $check = $this->plan_model->check_user_plan_exist(array("user_id" => $user_id));
        
    if(count($check)>0) {
       $plan_update = $this->plan_model->plan_update("user_selected_plan",$ins_data,array("user_id" => $user_id));
       //return $this->response(array("status" => 'success'), 200); 
    }
    else 
        {
      $this->db->insert("user_selected_plan",$ins_data);
      //return $this->response(array("status" => 'success'), 200);
    } 
        $this->user_current_plan_get($user_id);
  }
  
  /*  Block =5,Unblock=6  */
    function block_unblock_get()
    { 
    if((!$this->get('user_id')) || (!$this->get('val')) || (!$this->get('group_id'))){
      return $this->response(array('status' => 'error','msg' => 'Required fields missing in your request','error_code' => 1), 404);
    }
    
    $user_id = $this->get('user_id');
    $val     = $this->get('val');
        $group_id= $this->get('group_id');
      
    $plan_update = $this->plan_model->block_unblock_update("user_groups",array("blocked" => $val),array("user_id" => $user_id, "group_id" => $group_id));
    return $this->response(array("status" => 'success'), 200);  
  }
  
   
    //get user current plan
    function user_current_plan_get($user_id='')
    {
        if(!$this->get('user_id')){
      return $this->response(array('status' => 'error','msg' => 'Required fields missing in your request','error_code' => 1), 404);
    }
        
        $user_id   = $this->get("user_id");
        $plan_data = get_plan_data($user_id);
       
        $plan_data['detail'] = $this->user_plans_model->user_plan_data($plan_data['plan_id']);
        if(count($plan_data)>0){
            return $this->response(array("status" => 'success','request_type' => 'user_current_plan','plan_data' => $plan_data), 200);
        }
        else
        {
            return $this->response(array('status' =>'error', 'request_type' => 'user_current_plan', 'msg' => 'No Plan Found', 'error_code' => 102), 404);
        }
    } 
    
     //pro plan service
   function pro_plans_get()
   {  
        $plans =  $this->plan_model->get_pro_plans();
        return $this->response(array("status" => 'success', 'plans' => $plans), 200);
   }
   
   
    function get_promocodes_get()
    {
        $promos =  $this->promo_model->get_all_promos();
        if(count($promos) > 0){
            return $this->response(array("status" => 'success', 'promos' => $promos, 'request_type' => 'get_promo_codes'), 200);
        }
        else
        {
            return $this->response(array("status" => 'error', 'msg' => 'No User Found', 'request_type' => 'get_promo_codes'), 404);
        }
    }
    
    function user_is_show_update_get()
    { 
    if((!$this->get('user_id')) && (!$this->get('is_show'))){
      return $this->response(array('status' => 'error','msg' => 'Required fields missing in your request','error_code' => 1), 404);
    }
    
    $user_id = $this->get('user_id');
        $is_show = $this->get('is_show');
      
    $update  = $this->user_model->update("user",array("is_show" => $is_show),array("id" => $user_id));
    return $this->response(array("status" => 'success'), 200);  
  }
  
    function user_is_contact_update_get()
    { 
    if((!$this->get('user_id')) && (!$this->get('is_contact'))){
      return $this->response(array('status' => 'error','msg' => 'Required fields missing in your request','error_code' => 1), 404);
    }
    
    $user_id   = $this->get('user_id');
        $is_contact= $this->get('is_contact');
      
    $update    = $this->user_model->update("user",array("is_contact" => $is_contact),array("id" => $user_id));
    return $this->response(array("status" => 'success'), 200);  
  }
    
    function user_update_allow_deny_and_password_protect_get()
    { 
    if((!$this->get('user_id')) && (!$this->get('field_name'))){
      return $this->response(array('status' => 'error','msg' => 'Required fields missing in your request','error_code' => 1), 404);
    }
    
    $user_id          = $this->get('user_id');
        $password_protect = $this->get('password_protect');
        $field_name       = $this->get('field_name');
        $allow_deny       = $this->get('allow_deny');
        
        $update_data = array();
        if(!empty($field_name) && ($field_name=='password')){
            $update_data['password_protect'] = $password_protect;
        }
        else
        {
            $update_data['allow_deny'] = $allow_deny;
        }
      
    $update    = $this->user_model->update("groups",$update_data,array("id" => $user_id));
    return $this->response(array("status" => 'success'), 200);  
  }
        
    function allow_to_join_map_get($user_id='', $group_id='')
    {
        if((!$this->get('user_id')) && (!$this->get('group_id'))){
      return $this->response(array('status' => 'error','msg' => 'Required fields missing in your request','error_code' => 1), 404);
    }
        
        $user_id   = (!empty($user_id))?$user_id:$this->get('user_id');
        $group_id  = (!empty($group_id))?$group_id:$this->get('group_id');
        
        $result    = $this->group_model->check_unique(array("id" => $group_id));
        
        //check already user joined or not
        $res = $this->user_groups_model->check_unique(array('user_id' => $user_id, 'group_id' => $result['id']));
        $cnt = count($res);
       
        if(empty($cnt) && (int)$user_id) { 
            $ins_data = array();
            $ins_data['user_id']        = $user_id;
            $ins_data['group_id']       = $group_id; 
            $ins_data['status']         = 1; 
            $ins_data['is_joined']      = 1; 
            $ins_data['is_view']        = ($result['type']=='private')?1:0;
            $ins_data['last_seen_time'] = date('Y-m-d H:i:s');
            $add_group                  = $this->user_groups_model->insert($ins_data);   
            //return $this->response(array("status" => 'success'), 200);
       }           
    }
    
    
    function group_admin_update_password_get()
    {
        if((!$this->get('join_key')) && (!$this->get('password'))){
      return $this->response(array('status' => 'error','msg' => 'Required fields missing in your request','error_code' => 1), 404);
    }
        
        $join_key   = $this->get('join_key');
        $password   = $this->get('password');
        
        $ins_data   = array();
        $ins_data['password'] = $password;
        
        $this->group_model->update($ins_data,array("join_key" => $join_key));
        
        return $this->response(array("status" => 'success'), 200);   
    }
    
    function check_group_password_get()
    {
       //echo $this->get('join_key').$this->get('password').$this->get('user_id'); exit;
     // return $this->response(array('status' => 'error','jkey' => $this->get('join_key'),'pass' => $this->get('password'),'id' => $this->get('user_id'),'msg' => 'Required fields missing in your request','error_code' => 1), 404);
        if((!$this->get('join_key')) && (!$this->get('password')) && (!$this->get('user_id'))){
           return $this->response(array('status' => 'error','msg' => 'Required fields missing in your request','error_code' => 1), 404);
         }
        
        $join_key   = $this->get('join_key');
        $password   = $this->get('password');
        $user_id    = $this->get('user_id');
        
        $ins_data   = array();
        $ins_data['password'] = $password;
        
       // $result     = $this->group_model->check_unique(array("join_key" => $join_key, "password" => $password));
        $result     = $this->db->query("select * from groups where 1=1 and join_key='".$join_key."' and password='".$password."'")->row_array();
       
        if(count($result)){
           $this->allow_to_join_map_get($user_id,$result['id']); 
           $this->search_map_get($join_key,$user_id,'authenticated_success');
           //return $this->response(array("status" => 'success', 'request_type' => 'check_group_password'), 200);  
        }
        else
        {
           return $this->response(array("status" => 'error', 'msg' => 'Incorrect Password', 'request_type' => 'check_group_password', 'error_code' => 105), 404);
        }   
    }
    
    function user_is_show_personal_locator_get()
    { 
    if((!$this->get('user_id')) && (!$this->get('show_personal'))){
      return $this->response(array('status' => 'error','msg' => 'Required fields missing in your request','error_code' => 1), 404);
    }
    
    $user_id          = $this->get('user_id');
        $show_personal    = $this->get('show_personal');
      
    $update    = $this->user_model->update("user",array("show_personal" => $show_personal),array("id" => $user_id));
    return $this->response(array("status" => 'success'), 200);  
  }
    
    //remove only one user from that group
    function remove_one_member_from_group_get()
    { 
    if((!$this->get('user_id')) && (!$this->get('group_id'))){
      return $this->response(array('status' => 'error','msg' => 'Required fields missing in your request','error_code' => 1), 404);
    }
    
    $user_id   = $this->get('user_id');
        $group_id  = $this->get('group_id');    
        
        $gr_dt = $this->group_model->check_unique(array("join_key" => $group_id));    
            
    $this->user_groups_model->delete(array("user_id" => $user_id,"group_id" => $gr_dt['id']));
        
        $user  = $this->user_model->check_unique(array("id" => $user_id));
        
        //set default id map to active 
        $grp_data = $this->db->query("select g.id from groups g  
                                      inner join user u on u.id=g.user_id where g.join_key='".$user['default_id']."'")->row_array();
        
        $up_data = array();
        $up_data['status'] = 1;
        
        $this->user_groups_model->update($up_data,array("user_id" => $user_id, "group_id" => $grp_data['id'] ));
    return $this->response(array("status" => 'success'), 200);  
  }
    
    //remove user from all groups
    function remove_user_from_all_groups_get()
    { 
    if((!$this->get('user_id'))){
      return $this->response(array('status' => 'error','msg' => 'Required fields missing in your request','error_code' => 1), 404);
    }
    
        $user_id   = $this->get('user_id'); 
        $groups    = $this->group_model->get_own_channel($user_id);
        
        $ids     = '';
        foreach($groups as $gkey => $gvalue)
        {
           $ids .= $gvalue['id'].",";
        }
        
        //echo $ids;
        
        $ids = rtrim($ids,",");
        
        $this->db->query("delete from user_groups where user_id='".$user_id."' and group_id not in ($ids)");
        
        $this->user_groups_model->update(array("status" => 0),array("user_id" => $user_id));
        
        $user  = $this->user_model->check_unique(array("id" => $user_id));
        
        //set default id map to active 
        $grp_data = $this->db->query("select g.id from groups g  
                                      inner join user u on u.id=g.user_id where g.join_key='".$user['default_id']."'")->row_array();
        
        $up_data = array();
        $up_data['status'] = 1;
        $this->user_groups_model->update($up_data,array("user_id" => $user_id, "group_id" => $grp_data['id']));
         
    return $this->response(array("status" => 'success'), 200);  
  }
    
    //admin remove all members from group
    function admin_remove_all_members_from_group_get()
    { 
    if((!$this->get('user_id')) && (!$this->get('join_key'))){
      return $this->response(array('status' => 'error','msg' => 'Required fields missing in your request','error_code' => 1), 404);
    }
    
    $user_id   = $this->get('user_id');
        $join_key  = $this->get('join_key');
      
        $result    = $this->group_model->check_unique(array("join_key" => $join_key));
            
    $this->db->query("delete from user_groups where group_id='".$result['id']."' and user_id not in ($user_id)");
        
    return $this->response(array("status" => 'success'), 200);  
  }
    
    //clear all clues
    function admin_remove_all_clues_from_group_get()
    { 
    if((!$this->get('user_id')) && (!$this->get('join_key'))){
      return $this->response(array('status' => 'error','msg' => 'Required fields missing in your request','error_code' => 1), 404);
    }
    
    $user_id   = $this->get('user_id');
        $join_key  = $this->get('join_key');
      
        $result    = $this->group_model->check_unique(array("join_key" => $join_key, 'user_id' => $user_id));
            
    $this->db->query("delete from user_static_maps where group_id='".$result['id']."'");
        
    return $this->response(array("status" => 'success'), 200);  
  }
    
    //clear both members & clues
    function remove_both_clues_and_members_get()
    { 
    if((!$this->get('join_key'))){
      return $this->response(array('status' => 'error','msg' => 'Required fields missing in your request','error_code' => 1), 404);
    }
    
    //$user_id   = $this->get('user_id');
        $join_key  = $this->get('join_key');
      
        $result    = $this->group_model->check_unique(array("join_key" => $join_key));
            
    $this->db->query("delete from user_static_maps where group_id='".$result['id']."'");
        
        $user_id   = $result['user_id'];
         
        $this->db->query("delete from user_groups where group_id='".$result['id']."' and user_id not in ($user_id)");
        
    return $this->response(array("status" => 'success'), 200);  
  }
    
    //allow_deny option is enabled means need to send notification to owner of the group
    function allowdeny_send_notification_get()
    {
        if((!$this->get('join_key')) && (!$this->get('user_id'))){
      return $this->response(array('status' => 'error','msg' => 'Required fields missing in your request','error_code' => 1), 404);
    }
        
        $join_key   = $this->get('join_key');
        $user_id    = $this->get('user_id');
        
        $this->load->library("FCM");
        
        $result     = $this->group_model->check_unique(array("join_key" => $join_key));
        $admin      = $this->user_model->check_unique(array("id" => $result['user_id']));
      
        //requested userdata
        $user    = $this->user_model->check_unique(array("id" => $user_id));
        
        if(count($admin)){
            $gcm_id = $admin['gcm_id'];
            if(!empty($user_id) && !empty($gcm_id)) {  

                $gcm_data = array();
                $gcm_data['user_status']    = (!empty($user_status))?$user_status:'offline';
                $gcm_data['join_key']       = $join_key;
                $gcm_data['user_id']        = $user_id;
                $gcm_data['admin_id']       = $admin['id'];
                $gcm_data['type']           = "user";
                $gcm_data['default_id']     = (!empty($user['default_id']))?$user['default_id']:$user['phonenumber'];
                $gcm_data['method']         = 'allow_deny_send_notification';
        
                $gcm_data['msg']    = ucfirst($user['default_id']).' sent join request to your channel '. ucfirst($join_key);
                
                $notification_staus =  $this->fcm->send_notification(array($gcm_id),array("hmg" => $gcm_data),'');
                
                $this->insert_notification($result['user_id'],$join_key,$gcm_data,$notification_staus,$user_id);
                //$this->user_notifications_get($user_id);  
            }
           return $this->response(array("status" => 'success','join_key' => $join_key, 'msg' => $gcm_data['msg'] ,'request_type' => 'allow_deny_send_notification'), 200);  
        }
        else
        {
           return $this->response(array("status" => 'error', 'msg' => "Group doesn't exists", 'request_type' => 'allow_deny_send_notification', 'error_code' => 110), 404);
        }   
    }
    
    //user join request map
    function user_join_request_map_get()
    {
        if((!$this->get('join_key')) && (!$this->get('user_id'))){
      return $this->response(array('status' => 'error','msg' => 'Required fields missing in your request','error_code' => 1), 404);
    }
        
        $join_key   = $this->get('join_key');
        $user_id    = $this->get('user_id');
        $cfrom      = $this->get('cfrom');
        
        $cfrom      = (empty($cfrom))?'app':$cfrom;
        
       
         $this->load->library("FCM");
        $result     = $this->group_model->check_unique(array("join_key" => $join_key));
        $admin      = $this->user_model->check_unique(array("id" => $user_id));
      
        //requested userdata
        $user       = $this->user_model->check_unique(array("id" => $result['user_id']));
        
        if(count($admin)){
           $gcm_id = $admin['gcm_id'];
            $this->allow_to_join_map_get($user_id,$result['id']);
            if(!empty($user_id)) {  
               // if($cfrom == 'app') {
                $gcm_data = array();
                $gcm_data['user_status']    = (!empty($user_status))?$user_status:'offline';
                $gcm_data['join_key']       = $join_key;
                $gcm_data['user_id']        = $user_id;
                $gcm_data['admin_id']       = $result['user_id'];
                $gcm_data['type']           = "user";
                $gcm_data['default_id']     = (!empty($admin['default_id']))?$admin['default_id']:$admin['phonenumber'];
                $gcm_data['method']         = 'join_request';
        
                $msg         = ucfirst($user['default_id']).' accepted your join request now you can search that group '. ucfirst($join_key);
                $gcm_data['msg']     = $msg;
                $this->insert_notification($user_id,$join_key,$gcm_data,$user_id);
                $this->fcm->send_notification(array($gcm_id),array("hmg" => $gcm_data));
              // }    
            }
           return $this->response(array("status" => 'success','join_key' => $join_key, 'msg' => $msg , 'request_type' => 'join_request'), 200);  
        }
        else
        {
           return $this->response(array("status" => 'error', 'msg' => "Group doesn't exists", 'request_type' => 'join_request', 'error_code' => 110), 404);
        }   
    }
    
    //get user current active group
    function get_user_current_group_active_get() {
        if(!$this->get('user_id')){
      return $this->response(array('status' => 'error','msg' => 'Required fields missing in your request','error_code' => 1), 404);
    }  
        
         $user_id   = $this->get("user_id");
         $groups    = $this->user_groups_model->get_user_active_group($user_id);
       //print_r($groups);
        if(count($groups)) {  
            return $this->response(array('status' =>'success', 'request_type' => 'current_group_active','group' => $groups), 200);
        } 
        else
        {
            return $this->response(array('status' =>'error', 'request_type' => 'current_group_active', 'msg' => 'No Active Groups for this user', 'error_code' => 1), 404);
        }   
  }
  
    //get user current active group
    function update_guest_user_displayname_and_channel_get() {
        if(!$this->get('user_id') && !$this->get('displayname') && !$this->get('join_key')){
      return $this->response(array('status' => 'error','msg' => 'Required fields missing in your request','error_code' => 1), 404);
    }  
        
         $user_id   = $this->get("user_id");
         $name      = $this->get("displayname");
         $joinkey   = $this->get("join_key");
         $userdata  = $this->user_model->check_unique(array("id" => $user_id));
       
        if(count($userdata)) {  
            $update    = $this->user_model->update("user",array("display_name" => $name),array("id" => $user_id));
            $gp_up     = $this->group_model->update(array('description' => $name),array('user_id' => $user_id,'join_key' => $joinkey));
            return $this->response(array('status' =>'success', 'request_type' => 'guest_displayname_update'), 200);
        } 
        else
        {
            return $this->response(array('status' =>'error', 'request_type' => 'guest_displayname_update', 'msg' => 'No User found', 'error_code' => 1), 404);
        }   
   }


   //get user current active group
    function updateMapID_get() 
    {
      $channelID    = $this->get("channel_id");
      $userID       = $this->get("user_id");

      $userdata  = $this->group_model->check_unique( array("join_key" => $channelID) );
      
      if( !count($userdata) ) 
      {
        //update groups table
        $update_data = array(
            "join_key"    => $channelID,
            "name"        => $channelID,
            "description" => $channelID
          );

        $this->group_model->update( $update_data, array("user_id" => $userID) );

        //update user table
        $update_data = array('default_id' => $channelID);
        $this->user_model->update( 'user', $update_data, array("id" => $userID) );

        return $this->response(array('status' =>'success', 'msg' => 'Updated Successfully.'), 200);
      }
      else
      {
        return $this->response(array('status' =>'error', 'request_type' => 'guest_displayname_update', 'msg' => 'Guest Map ID is already exists.', 'error_code' => 1), 404);
      }      
           
   }

   //check if user sent join request
   function userJoinRequest_get()
   {
        $user_id  = $this->get('user_id');
        $join_key = $this->get('join_key');
       
        if(!(int)$user_id)
            return $this->response(array('status' => 'error','msg' => 'Required fields missing in your request','error_code' => 1), 404);


        $res = $this->user_model->user_join_request_check($user_id,$join_key);
       
        if(empty($res))
            return $this->response(array('status' => 'error','msg' => 'No messages received!','error_code' => 101), 404);
        else    
            return $this->response(array('status' => 'success', 'message_list' => $res,'user_id'=> $user_id), 200);
   }

   function testMail_get() 
   {
     
/*mail('developerid000@gmail.com','TEst','TEst'); exit;
      $gcm_id = "fPGfkO7r7iU:APA91bH6rzP4y1cPJWLQUhYR1ZhtoxOO63JNSJmvIbEWvEsmGH0qUID3WrLsxJcwgETXUHFhcERsM9ZQsImXzzj7ptlImPbE65cozi5EQ_iuMbld3WW-jAyddBlSm4qQbCjFFPMlyE0r";
      
      $gcm_data = array('msg' => "Hi Parimala!..", 'arg1' => '1111', 'arg2' => '22222'); 
      $data = array('hmg' => $gcm_data);        

      $this->load->library("FCM");

      $this->fcm->send_notification( array($gcm_id), $data );

      die('KKKKKKKKK');*/

      $this->load->library('email');

      $config = array();
      $config['protocol'] = 'sendmail';
      $config['charset']  = 'UTF-8';
      $config['wordwrap'] = TRUE;
      $config['mailtype'] = 'html';
      $config['newline']  = "\r\n";

      $this->email->initialize($config);

     /* $email = $this->get('email');

      $message  = "<html>";
       $message .= "<body>";
       $message .= "<p>Hi $username,</p><br/>";
       $message .= "<p>Please click below link to reset your password.</p><br/>";
       //$message .= "<p><a href='".$fpwd_url."' title='Reset Your Password'>Click Here</a></p><br/><br/>";
       $message .= "<p>Thanks,<p>";
       $message .= "<p><a href='http://911gps.me'>911gps.me</a></p>"; 
       $message .= "</body></html>";
*/

       $this->email->from('contact@heresmygps.com','Contact');
         $this->email->to("developerid000@gmail.com");
         $this->email->subject('Forgot Password');
         $this->email->message("TEsdsssssssssssssss");
         $this->email->send();
   }

   function testFCM_get()
    {

        $this->nxs_cURLTest("http://www.nextscripts.com/", "HTTPS to NXS", "Social Networks");
        $this->nxs_cURLTest("http://www.google.com/intl/en/contact/", "HTTP to Google", "Mountain View, CA");
        die('FFF');

        $msg = array(
            'body'  => "Hi sakdskjdkjsahd",
            'title' => "HMGPS"
            //'icon'    => 'myicon',/*Default Icon*/
            //'sound' => 'mySound'/*Default sound*/   
        );

        $fields = array
            (
                'to'        => "fSP8YDXMz1E:APA91bEsXtdIUlR5WiHMZXYGCjz50Ub5XrnVxgAz6zQ_CvrRfdxPYLpjD_MfLnh1ehnUuXIEKDJJn1tbvzD39zB9W0OR_lwbMkBhdIFSz1Plb6vw5vrvzGciZIXHHxuhcd9NGeKXuQ7R",
                'notification'  => $msg
            );

        $headers = array(
            'Authorization: key='.'AIzaSyCapGarv6tix5SkskMJXz-_343fr7EQngo',
            'Content-Type: application/json'
        );

        $ch = curl_init(); 

        // set url 
        curl_setopt($ch, CURLOPT_URL, 'https://healiohealth.com/checkout'); 

        //return the transfer as a string 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        // $output contains the output string 
        $output = curl_exec($ch); 
        die($output);
        // close curl resource to free up system resources 
        curl_close($ch); 

        die;
        // Open connection
        $ch = curl_init();
        curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt( $ch,CURLOPT_POST, true );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        //curl_setopt($ch, CURLOPT_TIMEOUT, 0);
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ,JSON_UNESCAPED_SLASHES ) );
        $result = curl_exec($ch );
       // echo "<pre>";
          print_r($result); exit;
        return $result;
     
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
        curl_close( $ch );

    }

    function nxs_cURLTest($url, $msg, $testText){  
      $ch = curl_init(); 
      curl_setopt($ch, CURLOPT_URL, $url); 
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
      curl_setopt($ch, CURLOPT_TIMEOUT, 10); 
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
      $response = curl_exec($ch); 
      $errmsg = curl_error($ch); 
      $cInfo = curl_getinfo($ch); 
      curl_close($ch); 
      echo "Testing ... ".$url." - ".$cInfo['url']."<br />";
      if (stripos($response, $testText)!==false) 
        echo "....".$msg." - OK<br />"; 
      else 
      { 
        echo "....<b style='color:red;'>".$msg." - Problem</b><br /><pre>"; 
        print_r($errmsg); 
        print_r($cInfo); 
        print_r(htmlentities($response)); 
        echo "</pre>There is a problem with cURL. You need to contact your server admin or hosting provider.";
      }
    }

    function group_info_get()
    {
      

        $channelID    = $this->get("channel_id");
        $userID       = $this->get("user_id");

        $group_data = $this->db->query("select * from groups where join_key='".$channelID."'")->row_array();

        $user_group = $this->db->query("select ug.* from user_groups ug where ug.user_id='".$userID."' and ug.group_id='".$group_data['id']."'")->row_array();

        
        if( count($user_group) ) 
        {
          return $this->response(array('status' =>'success', 'data' => $user_group, 'group_data' => $group_data), 200);
        }
        else
        {
          return $this->response(array('status' =>'error', 'msg' => 'Guest Map ID does not exists.', 'error_code' => 1), 404);
        }
    }

    function update_user_info_get()
    {

        $user_id        = $this->get("user_id");
        $display_name   = $this->get("display_name");
        $update_type    = $this->get("update_type");
        $phonenumber    = $this->get("phone_number");

        $where = array('id' => $user_id);

        $data = array(
            'display_name' => $display_name, 
            'updated_type' => $update_type
          );

        if( $phonenumber ) $data['phonenumber'] = $phonenumber;

        $this->db->where($where);
        $this->db->update('user', $data );

        return $this->response(array('status' =>'success', 'msg' => 'Updated successfully.'), 200);
    }
    
    function group_dt_get()
    {
        $joinKey    = $this->get("join_key");
        
        $group_data = $this->db->query("select * from groups where join_key='".$joinKey."'")->row_array();
        
        if( count($group_data) ) 
        {
          return $this->response(array('status' =>'success', 'data' => $group_data), 200);
        }
        else
        {
          return $this->response(array('status' =>'error', 'msg' => 'Group does not exists.', 'error_code' => 1), 404);
        }
    }
   
    function sendMessageToGroupMembers_get()
    {

      $joinKey       = $this->get("join_key");
      $message       = $this->get("message");
      $sender        = $this->get("sender_id");

      $group_data    = $this->db->query("select * from groups where join_key='".$joinKey."'")->row_array();
      $user_details  = $this->user_groups_model->get_user_gcm($group_data['id']);
      $senderData    = $this->db->query("select * from user where id='".$sender."'")->row_array();

      $this->load->library("FCM");
      
      $gcm_data = array()          ;
      $gcm_data['msg']    = $message;
      $gcm_data['sender'] = $senderData['display_name'];
      $gcm_data['method'] = 'send_message_to_group_members';
      foreach($user_details as $ukey => $uvalue) {
            $gcm_id   = $uvalue['gcm_id'];
            if(!empty($gcm_id)) {  
              
              $this->fcm->send_notification(array($gcm_id),array("hmg" => $gcm_data)); 
              $this->insert_notification($uvalue['id'],$joinKey,$gcm_data,$sender); 
            }
        } 
        return $this->response(array('status' =>'success','request_type' => 'send_message_to_group_members'), 200);
    }


    function sendMessageToUser_get()
    {

      $userId        = $this->get("user_id");
      $message       = $this->get("message");
      $sender        = $this->get("sender_id");    
      $userData      = $this->db->query("select * from user where id='".$userId."'")->row_array();
      $senderData    = $this->db->query("select * from user where id='".$sender."'")->row_array();
      
      $this->load->library("FCM");
      
      $gcm_data = array()          ;
      $gcm_data['msg']    = $message;
      $gcm_data['sender'] = $senderData['display_name'];
      $gcm_data['method'] = 'send_message_to_user';
      $gcm_id   = $userData['gcm_id'];
      if(!empty($gcm_id)) {  
        
        $this->fcm->send_notification(array($gcm_id),array("hmg" => $gcm_data));  
        $this->insert_notification($userId,'',$gcm_data,$sender);
      }
      
      return $this->response(array('status' =>'success','request_type' => 'send_message_to_user'), 200);  
    }


    // function send_message_to_group_get()
    // {


    //   $user_id  = $this->get('user_id');
    //   $join_key = $this->get('join_key');
    //   $message  = $this->get('message');
       
    //     if(!(int)$user_id && empty($join_key) && empty($message)) {
    //         return $this->response(array('status' => 'error','msg' => 'Required fields missing in your request','error_code' => 1), 404);
    //     }    

    //     $ins_data = array(); 
    //     $ins_data['user_id']      = $user_id;
    //     $ins_data['join_key']     = $join_key;
    //     $ins_data['date_created'] = date("Y-m-d H:i:s");
    //     $ins_data['is_viewed']    = 0;
    //     $message                  = array("user_id" => $user_id, "join_key" => $join_key, "msg" => $message, 'date_created' => strtotime(date("Y-m-d H:i:s")));
    //     $ins_data['message']      = json_encode($message);

    //     $message_id               = $this->group_model->send_message_to_group($ins_data); 

    //     if($message_id)
    //       return $this->response(array('status' =>'success', 'msg' => 'Message sent successfully.'), 200);
    //     else
    //       return $this->response(array('status' =>'error', 'msg' => "Message doesn't sent"), 404);   
    // }
   
}
?>

