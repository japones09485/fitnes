<?php

defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
require(APPPATH.'libraries/Rest_Controller.php');
require(APPPATH.'libraries/Format.php');

class Rest_ins_sedes extends REST_Controller
{
	
    public function __construct()
    {
        parent::__construct();
        header('Access-Control-Allow-Origin: *');
		header("Access-Control-Allow-Headers: X-API-KEY, Origin, authorization, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
		header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
	}



	public function createIns_post(){
		
		$this->load->model('Instructores_sedes','ins');
		$data = json_decode($this->post('data'));
		$sede = json_decode($this->post('sede'));
		
		$validIns = $this->ins->get_ins_with_instructor($sede, $data);
		
		if(count($validIns) >0 ){
			$resp['success']= false;
			$resp['mensaje']= 'Ya se encuentra un instructor asignado con estos parametros.';
		
		}else{
			$this->ins->insert(array(
				"fk_instructor"=>$data->instructor,
				"fk_sede"=>$sede,
				"tipo"=>$data->tipo
			));
		
			$resp['success']= true;
			$resp['mensaje']= 'Instructor registrado exitosamente.';
		}
		$validIns = $this->ins->get_instructorSede($sede);
		$resp['instructoresSede']= $validIns;

		$this->response($resp);

	
	}
  
	public function listarInsS_post() {
		$this->load->model('Instructores_sedes','ins');
		$sede= $this->post('idSede');
		$validIns = $this->ins->get_instructorSede($sede);
		$resp['instructoresSede']= $validIns;
		$resp['success']= true;
		
		$this->response($resp);
			
	}


	function DeleteInsSede_post(){
		$this->load->model('Instructores_sedes','ins');
		$id= $this->post('id');
		$sede= $this->post('sede');

		$this->ins->delete_by(array(
			'id'=>$id
		));

		$validIns = $this->ins->get_instructorSede($sede);
		
		$resp['instructoresSede']= $validIns;
		$resp['mensaje']= 'Instructor eliminado exitosamente.';

		
		$this->response($resp);
	}
	
}	
