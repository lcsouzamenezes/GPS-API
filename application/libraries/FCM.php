<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class FCM {
    // constructor
    function __construct() {
        define( 'API_ACCESS_KEY', 'AIzaSyCapGarv6tix5SkskMJXz-_343fr7EQngo' );
    }

    /**
     * Sending Push Notification
     */
    public function send_notification($registatoin_ids='', $message='', $title = '') {

      //print_r($message); exit;
         $msg = array(
		         'body' 	=> $message,
		        'title'	=> "Here'sMygps"
             	//'icon'	=> 'myicon',/*Default Icon*/
              	//'sound' => 'mySound'/*Default sound*/
               );

    	$fields = array
			(
				'to'		=> $registatoin_ids,
				'notification'	=> $msg
			);
        $headers = array(
            'Authorization: key='.API_ACCESS_KEY,
            'Content-Type: application/json'
        );
        // Open connection
        $ch = curl_init();
		curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
		curl_setopt( $ch,CURLOPT_POST, true );
		curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
		curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        //curl_setopt($ch, CURLOPT_TIMEOUT, 0);
		curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ,JSON_UNESCAPED_SLASHES ) );
		$result = curl_exec($ch );
       // echo "<pre>";
	//	print_r($result); exit;
        return $result;
     
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
        curl_close( $ch );
    }

}
