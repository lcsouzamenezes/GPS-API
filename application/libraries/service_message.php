<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Service_message {
    
    private $_CI;
    
    private $_message  = null;
    
    private $_template = 'service_message.php';
    
    public function __construct()
    {
        $this->_CI =& get_instance();
        
        $this->_CI->load->language('service_messages');
    }
    public function set_flash_message($message_key, $replace_items = null)
    {
        $message        = $this->_prepare_message($message_key, $replace_items);
        $this->_message = $message;
    }
    private function _prepare_message($message_key, $replace_items)
    {
        $service_message = $this->_CI->lang->line($message_key);
        if($service_message){
            if($replace_items){
                if(!is_array($replace_items)){
                    $replace_items = array($replace_items);
                }
                $service_message['message'] = vsprintf($service_message['message'], $replace_items);
            }
            return $service_message;
         }
           return false;
    }
    
    public function render( $template = null )
    {
        $data = array( 'service_message' => $this->get_message());
        
        if(!$template){
            $template = $this->_template;
        }
        if(isset($data['service_message']['message'])){
            return $this->_CI->load->view($template,$data,true);
        }
        else
        {
            return false;
        }
        
    }
    
    public function get_message()
    {
        if(!empty($this->_message))
        {
            return $this->_message;
        }
        return $this->_CI->session->flashdata('service_message');
    }
    
    public function set_message($message, $replace_items = null )
    {
        $message        = $this->_prepare_message($message, $replace_items);
        $this->_message = $message;
        
    }
}
?>