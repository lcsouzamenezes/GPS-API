<?php if(!defined("BASEPATH")) exit("No direct script access allowed");
safe_include("controllers/admin/admin_controller.php");

class Plan extends Admin_controller {
    
    protected $_plan_validation_rules = array( array('field' => 'planname', 'label' => 'Plan Name', 'rules' => 'trim|required'),
                                               array('field' => 'validity', 'label' => 'Validity', 'rules' => 'trim|required')
                                       );
    
    protected $_plan_access_validation_rules = array(array('field' => "free_joinable_hmgps_live_maps0", "label" => "Free Joinable Hmgps live Maps", "rules" => "required"));
    
    function __construct()
    {
       parent::__construct(); 
       $this->load->model(array('plan_model','plantype_model'));
    }
    
    function index()
    {
        
         $this->layout->add_javascripts(array('js/listing', 'js/rwd-table','js/scripts'));  
         
         $this->layout->add_stylesheets(array("css/main","css/theme","css/MoneAdmin","plugins/Font-Awesome/css/font-awesome"));

        $this->load->library('listing');

        $this->simple_search_fields      = array('planname' => 'Plan Name','validity' => 'Validity');
        $this->_narrow_search_conditions = array("");
        
        $str = '<a href="'.site_url('admin/plan/add/{id}').'" class="table-link">
                    <span class="fa-stack">
                        <i class="icon-edit"></i>
                        <i class="fa fa-pencil fa-stack-1x fa-inverse"></i>
                    </span>
                </a>';
 
        $this->listing->initialize(array('listing_action' => $str));

        $listing = $this->listing->get_listings('plan_model', 'listing');

        if($this->input->is_ajax_request())
            $this->_ajax_output(array('listing' => $listing), TRUE);
        
        $this->data['bulk_actions'] = array('' => 'select', 'delete' => 'Delete');
        $this->data['simple_search_fields'] = $this->simple_search_fields;
        $this->data['search_conditions'] = $this->session->userdata($this->namespace.'_search_conditions');
        $this->data['per_page'] = $this->listing->_get_per_page();
        $this->data['per_page_options'] = array_combine($this->listing->_get_per_page_options(), $this->listing->_get_per_page_options());
        
        $this->data['search_bar'] = $this->load->view('admin/listing/search_bar', $this->data, TRUE);        
        
        $this->data['listing']    = $listing;
        
        $this->data['grid'] = $this->load->view('admin/listing/view', $this->data, TRUE);
        
      //  $this->data['user_data'] = $this->session->userdata('admin_user_data');
        
        $this->layout->view("admin/plan/list");
        
        
    }
    
    
    //ADD OR EDIT PLANS
    function add($edit_id='')
    {
        $this->layout->add_javascripts(array('js/jquery-ui.min','js/date_custom','js/custom'));
        $this->layout->add_stylesheets(array("css/main","css/theme","css/MoneAdmin","plugins/Font-Awesome/css/font-awesome","css/jquery-ui"));
        
        if(is_logged_in()) {
            
            $edit_id = (isset($_POST['edit_id']))?$_POST['edit_id']:$edit_id;
            
            $this->form_validation->set_rules($this->_plan_validation_rules);
       
            if($this->form_validation->run()) {  
                
                $form = $this->input->post();
                
                $ins_data                = array();
                $ins_data['planname']    = $_POST['planname'];
                $ins_data['validity']    = $_POST['validity'];
               
                //add or update promo
                if(!empty($edit_id)) {
                    $update_menu  = $this->plan_model->update($ins_data,array("id" => $edit_id)); 
                }
                else
                {
                    $add_menu     = $this->plan_model->insert($ins_data);
                }
                
                redirect("admin/plan");       
            }
            
            if($edit_id){
                
                $edit_data = $this->plan_model->check_unique(array("id" => $edit_id));
                if(!count($edit_data)) {
                    $this->service_message->set_flash_message('record_not_found_error');
                    redirect('admin/plan/list');
                }
                
                $this->data['title']          = "EDIT PLAN";
                $this->data['form_data']      = $edit_data;  
            }
            else if($this->input->post()) {
                $this->data['title']           = "ADD PLAN";
                $this->data['form_data']       = $_POST;
                $this->data['form_data']['id'] = $edit_id != ''?$edit_id:'';
            }
            else
            {
                $this->data['title']     = "ADD PLAN";
                $this->data['form_data'] = array("planname" => "","validity" => "");
            }
            $this->layout->view('admin/plan/add',$this->data);    
        }
        else
        {
            redirect("admim/login");
        } 
    }
    
    function access()
    {
        
        if(is_logged_in()) {
        
            $this->layout->add_stylesheets(array("css/main","css/theme","css/MoneAdmin","plugins/Font-Awesome/css/font-awesome","css/jquery-ui"));
         
         
           
       
            $plans = $this->plantype_model->get_all_types();
           
             //access fields names
            $access_fields_names = array( "free_joinable_hmgps_live_maps" => "Free Joinable HMGPS live Maps", 
                                          "access_maps_with_search_site" => "Access Maps with Search Site for Free App" , 
                                          "mutable_user" => "Mutable User Support", 
                                          "permanent_hmgps_userids_map_channels" => "Permanent HMGPS User Ids Map Channels", 
                                          "number_of_joined_user_allowed_on_one_map" => "Number Of Joined Users allowed On 1 Map", 
                                          "password_protection_for_maps" => "Password Protection for Maps", 
                                          "allow_or_deni_access" =>  "Allow Or Deni Access", 
                                          "no_ads_on_app" => "No Ads On App", 
                                          "no_ads_on_web" => "No Ads On Webpages"
                                       ); 
            
            $this->form_validation->set_rules($this->_plan_access_validation_rules);
             
            if($this->form_validation->run()) { 
                
               $this->db->query("truncate table plan_access");
                
               $form = $this->input->post();
                
               $plancount = count($plans);
               $ins_data  = array();
               
               for($j = 0; $j<$plancount; $j++) {
                    
                    $ins_data['free_joinable_hmgps_live_maps']            = ($form['free_joinable_hmgps_live_maps'.$j] == 'on')?1:0;
                    $ins_data['access_maps_with_search_site']             = ($form['access_maps_with_search_site'.$j]=='on')?1:0;
                    $ins_data['mutable_user']                             = ($form['mutable_user'.$j] == 'on')?1:0;
                    $ins_data['permanent_hmgps_userids_map_channels']     = ($form['permanent_hmgps_userids_map_channels'.$j]=='on')?1:0;
                    $ins_data['number_of_joined_user_allowed_on_one_map'] = $form['number_of_joined_user_allowed_on_one_map'.$j];
                    $ins_data['password_protection_for_maps']             = ($form['password_protection_for_maps'.$j]=='on')?1:0;
                    $ins_data['allow_or_deni_access']                     = ($form['allow_or_deni_access'.$j] == 'on')?1:0;
                    $ins_data['no_ads_on_app']                            = ($form['no_ads_on_app'.$j]=='on')?1:0;
                    $ins_data['no_ads_on_web']                            = ($form['no_ads_on_web'.$j] == 'on')?1:0;
                    
                    $this->plan_model->plan_access_insert($ins_data);
               } 
            
            }
        
           
            
            $this->data['field_name'] = $access_fields_names;
            
            //get all plans
            $all_plans = $this->plan_model->get_all_plans();
            
            
            //get plan access
            $plan_access = $this->plan_model->get_access();
            $this->data['plan_access'] = $plan_access;
            
          
            $planaccess = array();
            for($i=0;$i<=count($plan_access);$i++) {
                 
                if(isset($plan_access[$i]['free_joinable_hmgps_live_maps'])){
                  $planaccess['free_joinable_hmgps_live_maps'][$i] = $plan_access[$i]['free_joinable_hmgps_live_maps']; 
                }
                if(isset($plan_access[$i]['access_maps_with_search_site'])){
                  $planaccess['access_maps_with_search_site'][$i] = $plan_access[$i]['access_maps_with_search_site']; 
                }
                if(isset($plan_access[$i]['mutable_user'])){
                  $planaccess['mutable_user'][$i] = $plan_access[$i]['mutable_user']; 
                }
                if(isset($plan_access[$i]['permanent_hmgps_userids_map_channels'])){
                  $planaccess['permanent_hmgps_userids_map_channels'][$i] = ($i == 0 )?'na':$plan_access[$i]['permanent_hmgps_userids_map_channels']; 
                }
                if(isset($plan_access[$i]['number_of_joined_user_allowed_on_one_map'])){
                  $planaccess['number_of_joined_user_allowed_on_one_map'][$i] = $plan_access[$i]['number_of_joined_user_allowed_on_one_map']; 
                }
                if(isset($plan_access[$i]['password_protection_for_maps'])){
                  $planaccess['password_protection_for_maps'][$i] = ($i == 0 || $i ==1 )?'na':$plan_access[$i]['password_protection_for_maps']; 
                }
                if(isset($plan_access[$i]['allow_or_deni_access'])){
                  $planaccess['allow_or_deni_access'][$i] = ($i == 0 || $i ==1 )?'na':$plan_access[$i]['allow_or_deni_access']; 
                }
                if(isset($plan_access[$i]['no_ads_on_app'])){
                    
                  $planaccess['no_ads_on_app'][$i] = ($i == 0 || $i ==1 )?'na':$plan_access[$i]['no_ads_on_app']; 
                }
                if(isset($plan_access[$i]['no_ads_on_web'])){
                  $planaccess['no_ads_on_web'][$i] = ($i == 0 || $i ==1 || $i ==2 )?'na':$plan_access[$i]['no_ads_on_web']; 
                }
            }
            
            $this->data['fields']     = $access_fields_names;
            $this->data['planaccess'] = $planaccess;
            $this->layout->view("admin/planaccess/update",$this->data);
     }
     else
     {
       redirect("admin/login");
     } 
  } 
}    