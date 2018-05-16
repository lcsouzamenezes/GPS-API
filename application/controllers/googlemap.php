<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Googlemap extends CI_Controller {
    
    function __construct(){
        parent::__construct();
        $this->load->model("group_model");
    }

    function index($key = ""){
        
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

/* End of file location.php */
/* Location: ./application/controllers/location.php */