<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Location extends CI_Controller {
    
    function __construct(){
        parent::__construct();
    }

    function redirect_to_map($key = ""){
        
        if(empty($key)) 
        {
            show_404();die;
        }
        
       
        $result = $this->db->get_where('location',array('user_key' => $key))->row_array();
        
        if(empty($result)) {
             show_404();die;
        }
        
        $lon = $result['lon'];
        $lat = $result['lat'];
        
        $url =  "http://maps.google.com/maps?daddr=$lat,$lon";
        redirect($url);die;
    }
}

/* End of file location.php */
/* Location: ./application/controllers/location.php */