<?php if(!defined("BASEPATH")) exit("No direct script access allowed");
safe_include("controllers/admin/admin_controller.php");
class Profile extends Admin_controller {
    
    protected $_profile_validation_rules = array(
                                                    array('field' => 'map_avail', 'label' => 'Map Available', 'rules' => 'trim|required'),
                                                    array('field' => 'type', 'label' => 'Map Type', 'rules' => 'trim|required')
                                                    //array('field' => 'location_type', 'label' => 'Location Type', 'rules' => 'trim|required')
                                                 );
    
    protected $api_url       = 'http://heresmygps.com/service/';
	protected $service_param = array('X-APP-KEY'=>'myGPs~@!');
    
    function __construct()
    {
         parent::__construct();
         $this->load->library('Rest');
         $this->rest->initialize(
			array('server' => $this->api_url,
		        'http_auth' => 'basic'
	    	)
		);
         $this->load->model(array('promo_model','user_model','user_groups_model','user_position_model','group_model'));
         
         $this->data['userdata'] = $this->session->userdata("user_data");
    }
    
    function index()
    {
        if(is_logged_in()) {
            $this->service_message->set_flash_message("login_success");
            $this->layout->add_stylesheets(array('css/main','css/theme','css/MoneAdmin','plugins/Font-Awesome/css/font-awesome','css/layout2','plugins/flot/examples/examples','plugins/timeline/timeline'));
            $this->data['profile'] = '';
            $this->layout->view('admin/user/profile/index', $this->data); 
       } 
       else
       {
           redirect("admim/login");
       }
    }
    
    //user view
     function add($edit_id = '')
     {
        $this->layout->add_javascripts(array('js/jquery-ui.min','js/date_custom','js/custom'));
        $this->layout->add_stylesheets(array("css/main","css/theme","css/MoneAdmin","plugins/Font-Awesome/css/font-awesome","css/jquery-ui"));
        
        if(is_logged_in()) {
            
            $edit_id  = (isset($_POST['edit_id']))?$_POST['edit_id']:$edit_id;
            $this->form_validation->set_rules($this->_profile_validation_rules);
       
            if($this->form_validation->run()) {  
                    $join_key   = $this->input->post('join_key');
                    $mapavail   = $this->input->post('map_avail');
                    $type       = $this->input->post('type');
                    $loc_type   = $this->input->post('location_type');
                    $allow_deny = $this->input->post('allow_deny');
                    
                    $ins_data    = array();
                    $ins_data['join_key']      = $join_key;
                    $ins_data['map_avail']     = $mapavail;
                    $ins_data['type']          = $type;
                    $ins_data['location_type'] = $loc_type;
                    $ins_data['allow_deny']    = $allow_deny;
                
                //add or update promo
                if(!empty($edit_id)) {
                    $update_menu  = $this->group_model->update($ins_data,array("id" => $edit_id)); 
                }
                else
                {
                    $add_menu     = $this->group_model->insert($ins_data);
                } 
                 
                 redirect('admin/profile/channels');  
            }
            
            if($edit_id){
                
                $edit_data = $this->group_model->check_unique(array("id" => $edit_id));
                
                if(!count($edit_data)) {
                    $this->service_message->set_flash_message('record_not_found_error');
                    redirect('admin/profile/channels');
                }
                
                $this->data['title']           = "VIEW CHANNEL";
                $this->data['form_data']       = $edit_data;
            }
            else if($this->input->post()) {
                $this->data['title']           = "VIEW CHANNEL";
                $this->data['form_data']       = $_POST;
                $this->data['form_data']['id'] = $edit_id != ''?$edit_id:'';
            }
            else
            {
                $this->data['title']     = "VIEW CHANNEL";
                $this->data['form_data'] = array("default_id" => "","phonenumber" => "");
            }
            $this->layout->view('admin/user/profile/channel/add',$this->data);    
        }
        else
        {
            redirect("admim/login");
        }
    }
    
    
    function channels()
    {
        $this->layout->add_javascripts(array('js/listing', 'js/rwd-table','js/scripts',"js/custom"));   
        $this->layout->add_stylesheets(array("css/main","css/theme","css/MoneAdmin","plugins/Font-Awesome/css/font-awesome"));
        $this->load->library('listing');

        $this->simple_search_fields      = array('name' => 'Group Name','satiation_id' => 'Station ID','join_key' => 'Joinkey', 'display_name' => 'Display Name');   
        $this->_narrow_search_conditions = array("");
        
        $str = '<a href="'.site_url('admin/profile/add/{id}').'" class="table-link">
                    <span class="fa-stack">
                        <i class="icon-edit"></i>
                        <i class="fa fa-pencil fa-stack-1x fa-inverse"></i>
                    </span>
                </a>
                <a href="'.site_url('admin/profile/delete/{id}').'" class="table-link">
                    <span class="fa-stack">
                        <i class="icon-delete"></i>
                        <i class="fa fa-pencil fa-stack-1x fa-inverse"></i>
                    </span>
                </a>';
 
        $this->listing->initialize(array('listing_action' => $str));
        $listing = $this->listing->get_listings('group_model', 'listing');

        if($this->input->is_ajax_request())
            $this->_ajax_output(array('listing' => $listing), TRUE);
        
        $this->data['bulk_actions']         = array('' => 'select', 'delete' => 'Delete');
        $this->data['simple_search_fields'] = $this->simple_search_fields;
        $this->data['search_conditions']    = $this->session->userdata($this->namespace.'_search_conditions');
        $this->data['per_page']             = $this->listing->_get_per_page();
        $this->data['per_page_options']     = array_combine($this->listing->_get_per_page_options(), $this->listing->_get_per_page_options());
        $this->data['search_bar']           = $this->load->view('admin/listing/search_bar', $this->data, TRUE);        
        $this->data['listing']              = $listing;
        $this->data['grid']                 = $this->load->view('admin/listing/view', $this->data, TRUE);
        $this->layout->view("admin/user/profile/channel/list",$this->data);
        // $this->output->enable_profiler(TRUE);
    }
    
    function delete($id='')
    {
        $id = ($_POST['id'])?$_POST['id']:$id;
        if(!empty($id)) {
            $this->db->query('delete from groups where id in ('.$id.')');
            $this->service_message->set_flash_message('record_delete_success');
        }
    }  
}  