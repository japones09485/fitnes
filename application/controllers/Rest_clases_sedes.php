<?php

defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
require(APPPATH.'libraries/Rest_Controller.php');
require(APPPATH.'libraries/Format.php');

class Rest_clases_sedes extends REST_Controller
{
	
    public function __construct()
    {
        parent::__construct();
        header('Access-Control-Allow-Origin: *');
		header("Access-Control-Allow-Headers: X-API-KEY, Origin, authorization, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
		header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
	}

	function listar_post(){
		$this->load->model('Clases_sedes_model','cla');
		
		$fkGim = $this->post('fk_gim');
		$idSede = $this->post('idSede');
	
		$data=$this->cla->order_by('clas_dia','ASC')->order_by('hora_inicio', 'ASC')->get_many_by(array(
			'cla_fk_gimnasio'=>$fkGim,
			'clas_fk_sede'=>$idSede 
		));

		$ordenArray = array();
		$diaAc ='';
		
		$resp['lista']=$data;
		$this->response($resp);
	} 

	public function createClaseSed_post(){
		$this->load->model('Clases_sedes_model','cla');
		$data = json_decode($this->post('data'));
		$gim = json_decode($this->post('gim'));
		$sede = json_decode($this->post('sede'));
		$user = $this->post('user');
		

		$this->cla->insert(array(
			"clas_nombre"=>$data->nombre,
			"clas_descripcion"=>$data->descripcion,
			"cla_fk_gimnasio"=>$gim,
			"clas_fk_sede"=>$sede,
			"clas_fk_instructor"=>$data->instructor,
			"hora_inicio"=>$data->HoraInicio,
			"hora_fin"=>$data->HoraFin,
			"clas_dia"=>$data->dia,
			"estado"=>$data->estado
			
		));

		$data=$this->cla->order_by('clas_dia','ASC')->get_many_by(array(
			'cla_fk_gimnasio'=>$gim,
			'clas_fk_sede'=>$sede 
		));

		$resp['lista']=$data;
		$this->response($resp);

	
	}
  
  public function EditClase_post() {
    $this->load->model('Clases_sedes_model','cla');
		$data = json_decode($this->post('data'));
		$gim = json_decode($this->post('gim'));
		$sede = json_decode($this->post('sede'));
		$idClase = json_decode($this->post('idClase'));
		$user = $this->post('user');

		$this->cla->update_by(array(
			"clas_id"=>$idClase
		),array(
			"clas_nombre"=>$data->nombre,
			"clas_descripcion"=>$data->descripcion,
			"cla_fk_gimnasio"=>$gim,
			"clas_fk_sede"=>$sede,
			"clas_fk_instructor"=>$data->instructor,
			"hora_inicio"=>$data->HoraInicio,
			"hora_fin"=>$data->HoraFin,
			"clas_dia"=>$data->dia,
			"estado"=>$data->estado
		));
		
		$data=$this->cla->order_by('clas_dia','ASC')->get_many_by(array(
			'cla_fk_gimnasio'=>$gim,
			'clas_fk_sede'=>$sede 
		));

		$resp['lista']=$data;
		$this->response($resp);
	}


	
	function DeleteClase_post(){
		$this->load->model('Clases_sedes_model','cla');
		$idClase = $this->post('idClase');
		$idSede = $this->post('idSede');

		$this->cla->delete_by(array(
			"clas_id"=>$idClase
		));

		$data=$this->cla->get_many_by(array(
			'clas_fk_sede'=>$idSede 
		));

		$resp['lista']=$data;
		$this->response($resp);
		
	}
	
}	
