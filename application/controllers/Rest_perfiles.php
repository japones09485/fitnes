<?php

defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
require(APPPATH.'libraries/Rest_Controller.php');
require(APPPATH.'libraries/Format.php');

class Rest_perfiles extends REST_Controller
{
	
	public function __construct()
    {
        parent::__construct();
        header('Access-Control-Allow-Origin: *');
		header("Access-Control-Allow-Headers: X-API-KEY, Origin, authorization, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
		header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
	}

	function getPerfiles_get(){
		$this->load->model('Perfiles_model','per');
		$perfiles = $this->per->get_many_by(array(
			'id !='=>2
		));
		$resp['perfiles']=$perfiles;
		$this->response($resp);
	}


	
}	
