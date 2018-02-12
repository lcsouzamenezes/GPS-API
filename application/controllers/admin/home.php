<?php if(!defined("BASEPATH")) exit("No direct script access allowed");
safe_include("controllers/admin/admin_controller.php");
class Home extends Admin_controller {
    function __construct()
    {
        parent::__construct();
         
    }

    public function index()
    {
      
        if(is_logged_in()) {
            $this->service_message->set_flash_message("login_success");
            $this->layout->add_stylesheets(array('css/main','css/theme','css/MoneAdmin','plugins/Font-Awesome/css/font-awesome','css/layout2','plugins/flot/examples/examples','plugins/timeline/timeline'));
            
            $total_users   = $this->db->query("select count(*) as cnt from user")->row_array();
            $tot_sub_users = $this->db->query("select count(*) as cnt from user_selected_plan")->row_array();
            $recently_added= $this->db->query("select default_id,date_created from user order by date_created desc limit 0,1")->row_array();
            //print_r($total_users);
            
            //foreach($total_users as $tkey=>$tvalue){
//                
//            }
            
            $this->data['total_users']    = $total_users;
            $this->data['tot_sub_users']  = $tot_sub_users;
            $this->data['recently_added'] = $recently_added;
            $this->layout->view("admin/home",$this->data);
        }
        else
        {
            
            $this->layout->view("login/login");
        }
    }
}
?>