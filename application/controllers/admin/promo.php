<?php if(!defined("BASEPATH")) exit("No direct script access allowed");
safe_include("controllers/admin/admin_controller.php");
class Promo extends Admin_controller {
    
    protected $_promo_validation_rules  = array(array('field' => 'expire','label' => 'Expire Date', 'rules' => 'trim|required'),
                                                array('field' => 'participant_count','label' => 'Participant Count', 'rules' => 'trim|required'),
                                                array('field' => 'number_of_time_to_use','label' => 'Time To Use', 'rules' => 'trim|required')
                                             //   array('field' => 'number_of_user_to_use','label' => 'User To Use', 'rules' => 'trim|required')
                                                );
    function __construct()
    {
        parent::__construct();
        
        $this->load->model(array('promo_model','user_model','plan_model'));
        
        
    }
    
    function index()
    {
        
         $this->layout->add_javascripts(array('js/listing', 'js/rwd-table','js/scripts','js/function','js/custom'));  
         
         $this->layout->add_stylesheets(array("css/main","css/theme","css/MoneAdmin","plugins/Font-Awesome/css/font-awesome"));

        $this->load->library('listing');

        $this->simple_search_fields = array('code' => 'Promo Code');
         
        $this->_narrow_search_conditions = array("");
        
        $str = '<a href="'.site_url('admin/promo/add/{id}').'" class="table-link">
                    <span class="fa-stack">
                        <i class="icon-edit"></i>  
                    </span>
                </a>';
 
        $this->listing->initialize(array('listing_action' => $str));

        $listing = $this->listing->get_listings('promo_model', 'listing');

        if($this->input->is_ajax_request())
            $this->_ajax_output(array('listing' => $listing), TRUE);
        
        $this->data['bulk_actions'] = array('' => 'select', 'delete' => 'Delete');
        $this->data['simple_search_fields'] = $this->simple_search_fields;
        $this->data['search_conditions'] = $this->session->userdata($this->namespace.'_search_conditions');
        $this->data['per_page'] = $this->listing->_get_per_page();
        $this->data['per_page_options'] = array_combine($this->listing->_get_per_page_options(), $this->listing->_get_per_page_options());
        
        $this->data['search_bar'] = $this->load->view('admin/listing/search_bar', $this->data, TRUE);        
        
        $this->data['listing'] = $listing;
        
        $this->data['grid'] = $this->load->view('admin/listing/view', $this->data, TRUE);
         
        $this->layout->view("admin/promo/list");
        
    }
    
    function add($edit_id='')
    {
        $this->layout->add_javascripts(array('js/jquery-ui.min','js/date_custom','js/custom'));
        $this->layout->add_stylesheets(array("css/main","css/theme","css/MoneAdmin","plugins/Font-Awesome/css/font-awesome","css/jquery-ui"));
        if(is_logged_in()) {
            
            $edit_id = (isset($_POST['edit_id']))?$_POST['edit_id']:$edit_id;
            
            $this->form_validation->set_rules($this->_promo_validation_rules);
            
            $this->form_validation->set_rules('number_of_user_to_use', 'Number of Users Allowed', 'trim|required|callback_promo_use_time_check['.$_POST['number_of_time_to_use'].']');
            
            $this->form_validation->set_rules('code','Promo Code', 'trim|required|callback_promo_unique_check['.$edit_id.']');
            
            $this->form_validation->set_rules('plan_id','Select Plan', 'trim|required|callback_promo_plan_check');
            
            if($this->form_validation->run()) {  
                
                $form = $this->input->post();
                
                $ins_data                              = array();
                $ins_data['code']                      = $_POST['code'];
                $ins_data['plan_id']                   = $_POST['plan_id'];
                $ins_data['expire']                    = $_POST['expire'];
                $ins_data['participant_count']         = $_POST['participant_count'];
                $ins_data['number_of_time_to_use']     = $_POST['number_of_time_to_use'];
                $ins_data['number_of_user_to_use']     = $_POST['number_of_user_to_use'];
                $ins_data['purchased_from']            = $_POST['purchased_from']; 
               
                //add or update promo
                if(!empty($edit_id)) {
                    $update_menu  = $this->promo_model->update($ins_data,array("id" => $edit_id)); 
                }
                else
                {
                    $add_menu     = $this->promo_model->insert($ins_data);
                } 
                
                redirect("admin/promo");   
            }
            
            if($edit_id){
                
                $edit_data = $this->promo_model->check_unique(array("id" => $edit_id));
                if(!isset($edit_data)) {
                    $this->service_message->set_flash_message('record_not_found_error');
                    redirect('admin/promo/list');
                }
                
                $this->data['title']          = "EDIT PROMO";
                $this->data['form_data']      = $edit_data;  
            }
            else if($this->input->post()) {
                $this->data['title']           = "ADD PROMO";
                $this->data['form_data']       = $_POST;
                $this->data['form_data']['id'] = $edit_id != ''?$edit_id:'';
            }
            else
            {
                $this->data['title']     = "ADD PROMO";
                $this->data['form_data'] = array("code" => "","expire" => "");
            }
            $this->data['plans'] = $this->plan_model->get_all_plans();
            $this->layout->view('admin/promo/add',$this->data);    
        }
        else
        {
            redirect("admim/login");
        }
    }
    
    function promo_code_generation()
    {
        
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';

        $length = 7;
        
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
        echo $string; 
    }
    
    //delete promos
    function delete()
    {
        $id = ($_POST['id'])?$_POST['id']:"";
        if(!empty($id)) {
            
            $this->db->query('delete from promo where id in ('.$id.')');
            $this->service_message->set_flash_message('record_delete_success');
            return true;    
        }
    }
    
    //promo assign
    function assign()
    {
        
        $this->layout->add_javascripts(array('js/jquery-ui.min','js/date_custom','js/custom'));
        $this->layout->add_stylesheets(array("css/main","css/theme","css/MoneAdmin","plugins/Font-Awesome/css/font-awesome","css/jquery-ui"));
        
        
        //all promos
        $this->data['promos'] = $this->promo_model->get_all_promos();
        
        //all users
        $this->data['users']  = $this->user_model->get_all_users();
        
        $this->data['title']  = "ASSIGN PROMO TO USER";
        
        $this->layout->view('admin/promo/assign',$this->data); 
    }
    
    
     //promo code unique check    
     function promo_unique_check($str,$id) 
     {
        
        $promo = $this->db->query("select * from promo where code='".$str."' and id !='".$id."'")->row_array();
    
        if(count($promo)) {
             $this->form_validation->set_message('promo_unique_check', 'Promo Code Already Exists');
             return FALSE; 
        }
       	return TRUE;
    } 
    
    //promo code unique check    
     function promo_use_time_check($str,$number_of_time_use) 
     {
    
        if($number_of_time_use < $str ) {
             $this->form_validation->set_message('promo_use_time_check', "Please enter minimum value of number of time to use");
             return FALSE; 
        }
       	return TRUE;
    } 
    
    
    //promo plan check
    function promo_plan_check($str)
    {
        if($str == 'select'){
           $this->form_validation->set_message('promo_plan_check', "Please Select plan");
             return FALSE;  
        }
        return true;
    }
 }
 
    