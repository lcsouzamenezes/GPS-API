<?php if(!defined("BASEPATH")) exit("No direct script access allowed");
safe_include("controllers/admin/admin_controller.php");

class Plantype extends Admin_controller {
    
    protected $_plantype_validation_rules = array(
                                                array('field' => 'name', 'label' => 'Name', 'rules'=> 'trim|required'),
                                                array('field' => 'cost', 'label' => 'Plan Cost', 'rules'=> 'trim|required|callback_plan_cost_check'),
                                                array('field' => 'type', 'label' => 'Type', 'rules'=> 'trim|required')
                                               );
    
    function __construct()
    {
       parent::__construct(); 
       $this->load->model(array("plantype_model","plan_model"));
    }
    
    function index()
    {
        
         $this->layout->add_javascripts(array('js/listing', 'js/rwd-table','js/scripts','js/function','js/custom'));  
         
         $this->layout->add_stylesheets(array("css/main","css/theme","css/MoneAdmin","plugins/Font-Awesome/css/font-awesome"));

        $this->load->library('listing');

        $this->simple_search_fields = array('name' => 'Plan Type','cost' => 'Cost','type' => 'Type');
         
        $this->_narrow_search_conditions = array("");
        
        $str = '<a href="'.site_url('admin/plantype/add/{id}').'" class="table-link">
                    <span class="fa-stack">
                        <i class="icon-edit"></i>
                        
                    </span>
                </a>';
 
        $this->listing->initialize(array('listing_action' => $str));

        $listing = $this->listing->get_listings('plantype_model', 'listing');

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
        
      //  $this->data['user_data'] = $this->session->userdata('admin_user_data');
        
        $this->layout->view("admin/plantypes/list");
         
    }
    
    function add($edit_id='')
    {
        $this->layout->add_javascripts(array('js/jquery-ui.min','js/date_custom','js/custom'));
        $this->layout->add_stylesheets(array("css/main","css/theme","css/MoneAdmin","plugins/Font-Awesome/css/font-awesome","css/jquery-ui"));
        
        if(is_logged_in()) {
            
            $edit_id = (isset($_POST['edit_id']))?$_POST['edit_id']:$edit_id;
            
            $this->form_validation->set_rules($this->_plantype_validation_rules);
       
            if($this->form_validation->run()) {  
                
                $form = $this->input->post();
                
                $ins_data               = array();
                $ins_data['name']       = $_POST['name'];
                $ins_data['plan_id']    = $_POST['plan_id'];
                $ins_data['cost']       = $_POST['cost'];
                $ins_data['type']       = $_POST['type'];
                $ins_data['description']= $_POST['description'];
                
               
                //add or update promo
                if(!empty($edit_id)) {
                    $update_menu  = $this->plantype_model->update($ins_data,array("id" => $edit_id)); 
                }
                else
                {
                    $add_menu     = $this->plantype_model->insert($ins_data);
                } 
                 
                 redirect('admin/plantype');  
            }
            
            if($edit_id){
                
                $edit_data = $this->plantype_model->check_unique(array("id" => $edit_id));
                
                if(!count($edit_data)) {
                    $this->service_message->set_flash_message('record_not_found_error');
                    redirect('admin/plantypes/list');
                }
                
                $this->data['title']          = "EDIT PLANTYPE";
                $this->data['form_data']      = $edit_data;  
            }
            else if($this->input->post()) {
                $this->data['title']           = "ADD PLANTYPE";
                $this->data['form_data']       = $_POST;
                $this->data['form_data']['id'] = $edit_id != ''?$edit_id:'';
            }
            else
            {
                $this->data['title']     = "ADD PLANTYPE";
                $this->data['form_data'] = array("name" => "","cost" => "","type" => "", "plan_id" => "","plans" => array(array()));
            }
            
            $this->data['form_data']['plans'] = $this->plan_model->get_all_plans();
            
            $this->layout->view('admin/plantypes/add',$this->data);    
        }
        else
        {
            redirect("admim/login");
        }
    }
    
    //delete plan types
    function delete()
    {
        $id = ($_POST['id'])?$_POST['id']:"";
        if(!empty($id)) {
            
            $this->db->query('delete from plan_types where id in ('.$id.')');
            $this->service_message->set_flash_message('record_delete_success');
            return true;    
        }
    }
    
    //check validation for plan cost if zero or null
    function plan_cost_check($str)
    {
        if($str == 0) {
             $this->form_validation->set_message('plan_cost_check', 'Plan Cost Should be Greater than 0');
             return FALSE; 
        }
       	return TRUE;
    }
}    