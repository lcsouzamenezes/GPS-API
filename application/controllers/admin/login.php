<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

safe_include("controllers/admin/admin_controller.php");
class Login extends Admin_Controller 
{
    
    protected $_login_validation_rules =    array (
                                                   // array('field' => 'email', 'label' => 'Email', 'rules' => 'trim|required|valid_email'),
                                                    array('field' => 'password', 'label' => 'Password', 'rules' => 'trim|required')
                                                  );
    function __construct()
    {
        parent::__construct();    
        
        $this->load->library('form_validation');
        $this->load->model('login_model');
        
    }  
    public function index()
    {
        $this->login();
    }
    
    public function login()
    {
       $this->layout->add_stylesheets(array('css/login','plugins/magic/magic','plugins/bootstrap/css/style'));
       $this->layout->add_javascripts('js/login');
       $this->form_validation->set_rules($this->_login_validation_rules);
       
        if($this->form_validation->run()){
            $form = $this->input->post();
             
            if($this->login_model->login($form['email'], $form['password'])){
                $userdata = $this->session->userdata("user_data");
                
                if($userdata['role'] == 'admin'){
                   // print_r($userdata); exit;
                    redirect("admin/home");
                }
                else
                {
                   redirect("admin/profile"); 
                }
            }  
        }
        $this->layout->view("admin/login/login");
    }
    
    public function logout()
	{
		$this->session->sess_destroy();
		$this->session->sess_create();
		$this->service_message->set_flash_message('logout_success');
		redirect('admin/login');
	}
    
}


?>