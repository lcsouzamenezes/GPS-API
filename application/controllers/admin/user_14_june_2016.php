<?php if(!defined("BASEPATH")) exit("No direct script access allowed");
safe_include("controllers/admin/admin_controller.php");
class User extends Admin_controller {
    
    protected $_user_validation_rules = array(array('field' => 'update_user_status', 'label' => 'Status', 'rules' => 'trim|required'));
    
    function __construct()
    {
        
         parent::__construct();
         $this->load->model(array('promo_model','user_model','user_groups_model','user_position_model'));
    }
    
    function index()
    {
        
         $this->layout->add_javascripts(array('js/listing', 'js/rwd-table','js/scripts',"js/custom"));  
         
         $this->layout->add_stylesheets(array("css/main","css/theme","css/MoneAdmin","plugins/Font-Awesome/css/font-awesome"));

        $this->load->library('listing');

        $this->simple_search_fields = array('default_id' => 'Default ID','phonenumber' => 'Phonenumber','email' => 'Email', 'display_name' => 'Display Name');
         
        $this->_narrow_search_conditions = array("");
        
            $str = '<a href="'.site_url('admin/user/add/{id}').'" class="table-link">
                    <span class="fa-stack">
                        <i class="fa fa-square fa-stack-2x"></i>
                    </span>
                   </a>
                   <button class="btn btn-info btn-action" data-toggle="modal" data-target="#participant_lists" onclick="get_participants_lists({id})"> 
                      <img src="'.base_url().'assets/admin/images/participants.png" class="img-responsive m-menu" alt="participants"> 
                    </button>
                    <button class="btn btn-info btn-action" data-toggle="modal" data-target="#uiModal" onclick="assign_user({id})">
                      <img src="'.base_url().'assets/admin/images/promo-code.png" class="img-responsive m-menu" alt="promo code"> 
                    </button>';
                    
        $this->listing->initialize(array('listing_action' => $str));

        $listing = $this->listing->get_listings('user_model', 'listing');

        if($this->input->is_ajax_request())
            $this->_ajax_output(array('listing' => $listing), TRUE);
        
        $this->data['bulk_actions'] = array('' => 'select', 'delete' => 'Delete');
        $this->data['simple_search_fields'] = $this->simple_search_fields;
        $this->data['search_conditions'] = $this->session->userdata($this->namespace.'_search_conditions');
        $this->data['per_page'] = $this->listing->_get_per_page();
        $this->data['per_page_options'] = array_combine($this->listing->_get_per_page_options(), $this->listing->_get_per_page_options());
        
        $this->data['search_bar'] = $this->load->view('admin/listing/search_bar', $this->data, TRUE);        
        
        $this->data['listing'] = $listing;
        $this->data['grid']    = $this->load->view('admin/listing/view', $this->data, TRUE);
        
         //all promos
        $this->data['promos']  = $this->promo_model->get_all_promos();

        $this->layout->view("admin/user/list",$this->data);  
    }
    
    
    //user view
     function add($edit_id = '')
     {
        $this->layout->add_javascripts(array('js/jquery-ui.min','js/date_custom','js/custom'));
        $this->layout->add_stylesheets(array("css/main","css/theme","css/MoneAdmin","plugins/Font-Awesome/css/font-awesome","css/jquery-ui"));
        
        if(is_logged_in()) {
            
            $edit_id = (isset($_POST['edit_id']))?$_POST['edit_id']:$edit_id;
            
            $this->form_validation->set_rules($this->_user_validation_rules);
       
            if($this->form_validation->run()) {  
                
                $form = $this->input->post();
                
                 $ins_data = array();
                 $ins_data['user_status'] = $form['update_user_status'];
                 
                //add or update promo
                if(!empty($edit_id)) {
                    $update_menu  = $this->user_model->update("user",$ins_data,array("id" => $edit_id)); 
                }
                else
                {
                    $add_menu     = $this->user_model->insert("user",$ins_data);
                } 
                 
                 redirect('admin/user');  
            }
            
            if($edit_id){
                
                $edit_data = $this->user_model->check_unique(array("id" => $edit_id));
                
                if(!count($edit_data)) {
                    $this->service_message->set_flash_message('record_not_found_error');
                    redirect('admin/user/list');
                }
                
                $this->data['title']                  = "VIEW USER";
                $this->data['form_data']              = $edit_data;
                $plantype                             = $this->db->query("select plan_type from user_plan where plan_id='".$edit_data['plan_id']."'")->row_array();
                $promo                                = $this->db->query("select code from promo where id='".$edit_data['last_assigned_promo']."'")->row_array();
                $position                             = $this->user_position_model->check_unique(array('user_id' => $edit_id));
                $this->data['form_data']['user_type'] = $plantype['plan_type'];  
                $this->data['form_data']['promo']     = $promo['code'];
                $this->data['form_data']['position']  = $position;
            }
            else if($this->input->post()) {
                $this->data['title']           = "VIEW USER";
                $this->data['form_data']       = $_POST;
                $this->data['form_data']['id'] = $edit_id != ''?$edit_id:'';
            }
            else
            {
                $this->data['title']     = "VIEW USER";
                $this->data['form_data'] = array("default_id" => "","phonenumber" => "");
            }
            
            $this->layout->view('admin/user/view',$this->data);    
        }
        else
        {
            redirect("admim/login");
        }
    }
    
    
    function assign_promo()
    {
        $user_id  = (isset($_POST['user_id']))?$_POST['user_id']:"";
        $promo_id = (isset($_POST['promo_id']))?$_POST['promo_id']:""; 
        
        $ins_data = array();
        $ins_data['user_id']   = $user_id;
        $ins_data['promo_id']  = $promo_id;
        
        
        $used_promo_count      = $this->db->query("select count(*) as cnt from user_promos where promo_id='".$promo_id."' and user_id='".$user_id."'")->row_array();
        $promo_count           = $this->db->query("select count(*) as cnt from user_promos where promo_id='".$promo_id."'")->row_array();
        
        $number_of_time_to_use = $this->promo_model->check_unique(array("id" => $promo_id));
        $number_of_time_to_use = $number_of_time_to_use['number_of_time_to_use'];
        $number_of_user_to_use = $number_of_time_to_use['number_of_user_to_use'];
        $used_promo_count      = $used_promo_count['cnt'];
        
        $promo_cnt = $promo_count['cnt'];
        
            if($used_promo_count < $number_of_user_to_use) {
                $this->db->query("insert into user_promos set user_id='".$user_id."', promo_id='".$promo_id."',assign_date='".date("Y-m-d")."'");
                
                $this->db->query("update user set last_assigned_promo='".$promo_id."', participant_count='".$number_of_time_to_use['participant_count']."' where id='".$user_id."'");
                
                $this->promos($user_id);
            }
            else
            {
                 if($this->input->is_ajax_request())
                    return    $this->_ajax_output(array('status' => 'success' ,'msg' => "Promo used time expired!"), TRUE);
            }
        
        exit;
    }
    
    function promos($user_id = '')
    {
        //user id
        $user_id = (isset($_POST['user_id']))?$_POST['user_id']:$user_id;
        
        $this->data['assignedpromos'] = $this->promo_model->get_user_assigned_promos($user_id);
        
        $output  = $this->load->view("admin/user/promos",$this->data,TRUE);
        
        if($this->input->is_ajax_request())
            return    $this->_ajax_output(array('status' => 'success' ,'output' => $output), TRUE);
    }
    
    function send_notification()
    {
        
        $this->load->library("GCM");
        
        $form = $this->input->post();
      
        $gcm_data = array();
        $gcm_data['title']       = $form['title'];
        $gcm_data['message']     = $form['message'];
        $gcm_data['description'] = $form['description'];
        
        //gcm_id
        $user = $this->user_model->check_unique(array("id" => $form['notify_user_id']));
        
        $this->gcm->send_notification(array($gcm_id),array("hmg" => $gcm_data));
        
        redirect("admin/user");
    }
    
    function group_participant_lists()
    {
        //user user id
        $user_id   = $_POST['user_id'];
        
        $user_data = $this->user_model->check_unique(array('id' => $user_id));
        
        $this->data['participants'] = $this->user_groups_model->get_participant_lists($user_data['default_id']);
        
        $output  = $this->load->view("admin/user/participants",$this->data,TRUE);
        
        if($this->input->is_ajax_request())
            return    $this->_ajax_output(array('status' => 'success' ,'output' => $output), TRUE);
        
    }
}   