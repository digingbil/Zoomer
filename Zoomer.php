
<?php 
/**
 * @version     0.0.1
 * @package     Zoomer.php
 * @copyright   Copyright (C) 2021 Zoran Tanevski. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Zoran Tanevski <zoran@tanevski.com> - http://tanevski.com
 */

 class Zoomer {

 	const ZOOM_JWTK_KEY = 'YOUR_JWT_TOKEN_HERE';
 	const ZOOM_MEETING_ID = 43453485345345;

 	private $users = [];
 	private $is_wp = false;
 	private $is_joomla = false;
 	private $headers = [];

 	public function __construct(){
 			
 		$this->users = $this->getUsers();
 		$this->is_wp = $this->isItWordPress();
 		$this->is_joomla = $this->isItJoomla();

 		$this->headers = [
 			'content-type' => 'application/json',
 			'authorization' => 'Bearer '.self::ZOOM_JWTK_KEY,
            'Accept' => 'application/json'
 		];

 		if( $this->is_wp ) {

 			define('WP_ROOT', __DIR__);
 			require_once WP_ROOT . '/wp-load.php';

 		} elseif ( $this->is_joomla ) {

 			define('_JEXEC', 1);
 			define('JPATH_BASE', __DIR__);
 			require_once JPATH_BASE . '/includes/defines.php';
 			require_once JPATH_BASE . '/includes/framework.php';

 		}

 	}

 	public function getUsers() {

 		// Get them from DB, CSV or anywhere else.
 		// Here <<<< !!!just for testing purposes!!! >>>> I'm adding some fake data

 		$users = [

 			[
 				'email' => 'someuser@someemail.com',
 				'first_name' => 'John',
 				'last_name' => 'Doe',
 				'country' => 'MK'
 			],
 			[
 				'email' => 'someuser2@someemail2.com',
 				'first_name' => 'Super',
 				'last_name' => 'Man',
 				'country' => 'US'
 			],

 		];

 		return $users;
 		
 	}
 	

 	public function register() {

 		foreach ($this->users as $user) {
 			
 			if( $this->is_wp ) {

 				$this->postItWp( $user );

 			} elseif ( $this->is_joomla ) {

 				$this->postItJoomla( $user );

 			} else {
 				$this->postIt( $user );
 			}

 		}
 		
 	}
 	
 	public function postItWp( $user ) {

 		$url = 'https://api.zoom.us/v2/meetings/'.self::ZOOM_MEETING_ID.'/registrants';
 		$timeout = 30;

 		$body = [
	        'email' => $user['email'],
	        'first_name' => $user['first_name'],
	        'last_name' => $user['last_name'],
	        'country' => $user['country']
	    ];
	    $json_body = json_encode($body);
	    $http = new WP_Http();

        $response = $http->post( $url, [ 'timeout' => $timeout, 'headers' => $this->headers, 'body' => $json_body ] );

        if( is_wp_error($response) || $response['response']['code'] >= 400 || strtolower( $response['response']['message']) != 'created') {
                return false; // Log or output something before instead
            } else {
               return true;
            }

 	}

 	public function postItJoomla( $user ) {

 		$url = 'https://api.zoom.us/v2/meetings/'.self::ZOOM_MEETING_ID.'/registrants';
 		$http = JHttpFactory::getHttp();

 		$body = [
	        'email' => $user['email'],
	        'first_name' => $user['first_name'],
	        'last_name' => $user['last_name'],
	        'country' => $user['country']
	    ];
	    $json_body = json_encode($body);

 		$response = $http->post( $url, $json_body, $this->headers );

 		$body = json_decode($response->body);
 		
 		if(!$body->error){
			return true;
		}
 		return false;

 	}

 	public function postIt( $user ) {

 		$url = 'https://api.zoom.us/v2/meetings/'.self::ZOOM_MEETING_ID.'/registrants';

 		$body = [
	        'email' => $user['email'],
	        'first_name' => $user['first_name'],
	        'last_name' => $user['last_name'],
	        'country' => $user['country']
	    ];

		$ch = curl_init();
 		curl_setopt_array( $ch, [
            CURLOPT_URL => $url,
            CURLOPT_POST => TRUE,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($body),
            CURLOPT_HTTPHEADER => $this->headers
        ]);


        $response = curl_exec( $ch );
        $err = curl_error( $ch );
        curl_close( $ch );
        if (! $response ) {
            return false; // Log or output something before instead
        }

        return json_decode( $response );

 	}


 	public function isItJoomla() {
 			if (

 				is_dir( __DIR__.'/modules') &&
 				is_dir( __DIR__.'/plugins') &&
 				is_dir( __DIR__.'/components') &&
 				is_dir( __DIR__.'/components/com_content') &&
 				is_dir( __DIR__.'/administrator') &&
 				is_dir( __DIR__.'/administrator/components') &&
 				is_dir( __DIR__.'/administrator/components/com_content') &&
 				is_dir( __DIR__.'/templates' ) &&
 				file_exists( __DIR__.'/configuration.php' )

 			) {
 				return true;
 			}

 			return false;
 		}

 		public function isItWordPress() {
 			
 			if (

 				is_dir( __DIR__.'/wp-content') &&
 				is_dir( __DIR__.'/wp-admin') &&
 				is_dir( __DIR__.'/wp-includes') &&
 				is_dir( __DIR__.'/wp-content/plugins') &&
 				is_dir( __DIR__.'/wp-content/themes') &&
 				file_exists( __DIR__.'/wp-login.php' ) && 
 				file_exists( __DIR__.'/wp-load.php' ) && 
 				file_exists( __DIR__.'/wp-config.php' )

 			) {
 				return true;
 			}

 			return false;

 		}
 	

}


//Run it
$zoomer = new Zoomer();
$zoomer->register();