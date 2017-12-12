<?php 
class Login_Model extends CI_Model
{
   protected $table = "";
   
   function __construct()
   {
     parent::__construct();
   }
   
   public function login($email, $password)
   {
      $user = $this->get_by_email($email);   
      if(empty($user) && count($user) == 0){
        $user = $this->get_by_user($email);
      }
      $pass = md5($password);
      if(!empty($user)&& $user['password'] == $pass){
        $this->session->set_userdata('user_data', $user);
        return true;
      }
      return false;
   }
   
   public function logout()
   {
        $this->session->sess_destroy();
   }
   
   public function get_by_email($email)
   {
        $this->db->select("*");
        $this->db->from("admin_users");
        $this->db->where(array('email' => $email));
        return $this->db->get()->row_array();
   }
   
   public function get_by_user($email)
   {
        $this->db->select("*");
        $this->db->from("user");
        $this->db->where(array('email' => $email));
        $result = $this->db->get()->row_array();
        if(empty($result)){
            $this->db->from("user");
            $this->db->where(array('default_id' => $email));
            $result = $this->db->get()->row_array();
        }
        return $result;
   }
    
}

?>