<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class App_Controller extends CI_Controller
{
    public $logged_in                  = FALSE;
    public $error_message              =    '';
    public $data                       =    array();
    public $role                       =    0;
    public $init_scripts               = array();
    public $criteria                   = array(); 
    
   
    public $settings   = array();
    public $interests  = array();
    public $options    = array();
    
    public function __construct()
    {
        parent::__construct(); 
   
        //$this->role = get_user_role();
        
        $this->init();
        
        $this->load->library("form_validation");

        $this->load->library("layout");
     
   }


    public function init()
    {
       $seg1 = $this->uri->segment(1,'');

        $this->load->config('layout');
        

        switch ($seg1) 
        {
            case 'admin':

                $layout = $this->config->item('admin', 'layout');

                if( !$layout )
                            die('Layout not found.');

                $this->layout->initialize($layout);

                if(!is_logged_in())
                {
                
                    $seg2 = $this->uri->segment(2,'');
                    if($seg1 === 'admin' && $seg2 !== 'login'){
                         redirect('admin/login');
                    }
                }
                elseif(is_logged_in() && get_user_role())
                {
                    
                    if(get_user_role() != '1')
                    {
                       // echo "test";
                       // redirect('home');
                    }
                    else
                    {
                        $seg2 = $this->uri->segment(2,'');
                        if(empty($seg2)){
                            
                            //redirect('admin/contact_form');
                        }
                    }
                }


                break;
            
            default:
                $layout = $this->config->item('frontend', 'layout');

                if( !$layout )
                            die('Layout not found.');
                        
                $this->layout->initialize($layout);

              //  $this->load_settings_data();

                break;
        }

        
        
    }


    public function index()
    {
       
    }
    
    public function _ajax_output($data, $json_format = FALSE)
    {
        if(is_array($data) && $json_format)
            echo json_encode($data);
        else 
            echo $data;
        
        exit();
    }
    
    public function load_settings_data()
    {
        $this->load->model('settings_model');
        
        $result = $this->settings_model->get_settings();

        $this->settings = array();
        foreach ($result as $row) 
        {
            $this->settings[$row['type']][$row['name']] = $row['value'];
        }

        $result = $this->settings_model->get_interest_list(array('i.status' => 1));
        $this->interests = array();
        $this->options  = array();
        foreach ($result as $row) 
        {
            $this->interests[$row['int_id']] = $row;
            if($row['option_id'])
            {
                $this->options[$row['int_id']][$row['option_id']] = $row['option_name'];
            }
            
        }

        return $this->settings;


    }
  
}

?>
