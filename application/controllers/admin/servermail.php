<?php if(!defined("BASEPATH")) exit("No direct script access allowed");
safe_include("controllers/admin/admin_controller.php");
class Servermail extends Admin_controller {
    
    function __construct()
    {
        parent::__construct();
    }
    public function index() 
    {
         $this->layout->add_stylesheets(array('css/main','css/theme','css/MoneAdmin','plugins/Font-Awesome/css/font-awesome','css/layout2','plugins/flot/examples/examples','plugins/timeline/timeline'));
            
            $user = 'contact@heresmygps.com';
            $pass = 'Iz123!@#';
            
            // Connect to the imap email inbox belonging to $user
            $con = imap_open("{mail.heresmygps.com:143/imap}INBOX", $user, $pass) or die('Cannot connect to Gmail: '.print_r(imap_errors()));
            
            $emails = imap_search($con,'ALL');
            
            if($emails) {
            	$output = '';
            	//rsort($emails);
                //print_r($emails);
            	foreach($emails as $email_number) {
            		$overview[] = imap_fetch_overview($con,$email_number,0);
            		$message[]  = imap_fetchbody($con,$email_number,2);
            		
            		/* output the email header information */
            	//	$output.= '<div class="toggler '.($overview[0]->seen ? 'read' : 'unread').'">';
//            		$output.= '<span class="subject">'.$overview[0]->subject.'</span> ';
//            		$output.= '<span class="from">'.$overview[0]->from.'</span>';
//            		$output.= '<span class="date">on '.$overview[0]->date.'</span>';
//            		$output.= '</div>';
//            		
//            		/* output the email body */
//            		$output.= '<div class="body">'.html_entity_decode(htmlspecialchars($message)).'</div>';   
            	}
            	$this->data['overview'] = $overview;
                $this->data['message']  = $message;
                
            } 
            $this->layout->view("admin/mail",$this->data);
            imap_close($con);
      }

}
?>