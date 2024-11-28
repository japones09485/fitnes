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
	
		$data=$this->cla->get_many_by(array(
			'cla_fk_gimnasio'=>$fkGim,
			'clas_fk_sede'=>$idSede 
		));

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
			"fecha_inicio"=>$data->fInicial,
			"fecha_fin"=>$data->fFinal
		));

		$data=$this->cla->get_many_by(array(
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
			"fecha_inicio"=>$data->fInicial,
			"fecha_fin"=>$data->fFinal
		));
		
		$data=$this->cla->get_many_by(array(
			'cla_fk_gimnasio'=>$gim,
			'clas_fk_sede'=>$sede 
		));

		$resp['lista']=$data;
		$this->response($resp);
	}


	function listaractivos_post(){
		$this->load->model('gimnasios_model','gim');
		$this->load->model('likes_model','lik');
		$pag=$this->post('pagina');
		$usuario=$this->post('usuario');
		if(empty($pag)){
			$pag=1;
		}
		$ini=($pag-1)*20;
		$data=$this->gim->order_by('gim_likes','DESC')->get_many_by(array());

		$cantdat=count($data);
		$cantdat=ceil($cantdat/20);

		foreach($data as $dat){
			$instructores=$this->gim->instructoresporgim($dat->gim_id);
			$likes=$this->lik->likeporgimnasio($dat->gim_id);
			if($usuario>0){
			$verifi_like=$this->lik->count_by(array('like_fk_usuario'=>$usuario , 'like_fk_idactor'=>$dat->gim_id , 'like_tipo'=>2));
			if($verifi_like==0){
				$like_usu=false;
			}else{
				$like_usu=true;
			}
			$dat->verlike=$like_usu;
			}
			$dat->instructores=$instructores;
			$dat->likes=$likes;
		}
		$resp['lista']=$data; 
		$resp['ok']=true;
		$resp['pag_actual']=$pag;
		$resp['cant_pag']=$cantdat;
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
