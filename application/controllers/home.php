<?php if(!defined("BASEPATH")) exit("No direct script access allowed");

class Home extends CI_Controller {
    function __construct()
    {
        parent::__construct();   
    }
    public function index()
    {
        //echo CI_VERSION;
        $this->data['title'] = "Here's My GPS | Location Tracking GPS Apps | Holden Global Technologies";
        $this->data['meta_description'] = "Here's My GPS App is the best family and friends location survival app safe your besties by tracking their vehicles or fleet's exact locations through GPS map. Great location tracking GPS product.";
        $this->data['meta_keywords'] = "Here's My GPS App, Location Tracking GPS Apps, Location Tracking GPS Product, Best GPS Family Locator App, GPS Fleet Tracking, GPS Vehicle Tracking, GPS Map Channel, GPS Location Survival App, GPS Family and Friends Tracker, Holden Global Technologies";
         $this->load->view("_partials/header",$this->data);
        $this->load->view("home");
        $this->load->view("_partials/footer");
    }
    public function contact()
    {
        $this->data['title'] = "Here's My GPS | Location Tracking GPS Apps | Holden Global Technologies";
        $this->data['meta_description'] = "Here's My GPS App is the best family and friends location survival app safe your besties by tracking their vehicles or fleet's exact locations through GPS map. Great location tracking GPS product.";
        $this->data['meta_keywords'] = "Here's My GPS App, Location Tracking GPS Apps, Location Tracking GPS Product, Best GPS Family Locator App, GPS Fleet Tracking, GPS Vehicle Tracking, GPS Map Channel, GPS Location Survival App, GPS Family and Friends Tracker, Holden Global Technologies";
        
        $this->load->view("_partials/header",$this->data);
        $this->load->view("contact");
        $this->load->view("_partials/footer");
    }
    public function howitworks()
    {
        $this->data['title'] = "GPS Team Tracking System | GPS to UTM Converter | Search and Rescue GPS App";
        $this->data['meta_description'] = "Holden Global Technologies offers GPS dispatch and coordination app, which works as a SAR GPS Team Deployment App and Search and Rescue GPS App. Try now.";
        $this->data['meta_keywords'] = "SAR GPS Team Deployment App, GPS Dispatch and Coordination App, GPS to UTM Converter, GPS Search and Rescue, GPS Team Tracking System, Search and Rescue GPS App";
        
         $this->load->view("_partials/header",$this->data);
        $this->load->view("how_it_work");
        $this->load->view("_partials/footer");
    }
    
    public function news()
    {
        $this->data['title'] = "Here's My GPS | Location Tracking GPS Apps | Holden Global Technologies";
        $this->data['meta_description'] = "Here's My GPS App is the best family and friends location survival app safe your besties by tracking their vehicles or fleet's exact locations through GPS map. Great location tracking GPS product.";
        $this->data['meta_keywords'] = "Here's My GPS App, Location Tracking GPS Apps, Location Tracking GPS Product, Best GPS Family Locator App, GPS Fleet Tracking, GPS Vehicle Tracking, GPS Map Channel, GPS Location Survival App, GPS Family and Friends Tracker, Holden Global Technologies";
        
        $this->load->view("_partials/header",$this->data);
        $this->load->view("news");
        $this->load->view("_partials/footer");
    }
    
    public function about()
    {
        $this->data['title'] = "Real Time GPS Tracking App | GPS Live Map Link | GPS Password Protected Map";
        $this->data['meta_description'] = "Here's My GPS app is one of the latest GPS drive tracking systems, offer GPS Walkie Talkie Map Channel, GPS Target Tracking, Device Tracking and 911 GPS Alternative Map Channel etc. ";
        $this->data['meta_keywords'] = "GPS Walkie Talkie Map Channel, GPS Live Map Link, GPS Target Tracking, GPS Device Tracking, GPS Password Protected Map, 911 GPS Alternative Map Channel, Real Time GPS Tracking App";
        
        $this->load->view("_partials/header",$this->data);
        $this->load->view("about");
        $this->load->view("_partials/footer");
    }
    
     public function privacypolicy()
    {
        $this->data['title'] = "Here's My GPS | Location Tracking GPS Apps | Holden Global Technologies";
        $this->data['meta_description'] = "Here's My GPS App is the best family and friends location survival app safe your besties by tracking their vehicles or fleet's exact locations through GPS map. Great location tracking GPS product.";
        $this->data['meta_keywords'] = "";
        
        $this->load->view("_partials/header",$this->data);
        $this->load->view("terms-condition");
        $this->load->view("_partials/footer");
    }
    
   public function notification()
   {
      $this->load->library('FCM');
      $this->fcm->send_notification();
   }
      
}  
       