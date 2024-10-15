<?php

defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
require(APPPATH.'libraries/Rest_Controller.php');
require(APPPATH.'libraries/Format.php');

class Rest_cursos extends Rest_Controller{
	private $campos=array(
		'cur_nombre'=>'nombre',
		'cur_fk_empresa'=>'empresa',
	);
	function __construct() {
		parent::__construct();
		header('Access-Control-Allow-Origin: *');
		header("Access-Control-Allow-Headers: X-API-KEY, Origin, authorization, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
		header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    }
	/**
	 * POST
	 * {
			*"cur_descripcion":"aerobicos para toda la familia",
			*"cur_nombre":"aerobicos",
			*"cur_fk_empresa":"1",
			*"cur_fk_instructor":"1",
			*"*cur_foto1":"img1",
			*"cur_foto2":"img1",
			*"cur_foto3":"img1",
			*"cur_foto4":"img1"
			*"cur_estado":"1"
		*}

	 */

	function crear_post(){
		$this->load->model('cursos_model','cur');
		$this->load->library('upload');
		$data=json_decode($this->post('data'));
	
		$id=$this->cur->insert($data);
		//carga de archivos
		if(count($_FILES)>0){
			foreach($_FILES as $k=>$values){
				$carpeta = 'imagenes/cursos/'.$id;
				if (!file_exists($carpeta)) {
					mkdir($carpeta, 0777, true);
				}
			$mi_archivo = $values['name'];
			$config['upload_path'] = $carpeta;
			$config['file_name'] =$mi_archivo;
			$config['allowed_types'] = "*";
			$config['max_size'] = "50000";
			$config['max_width'] = "2000";
			$config['max_height'] = "2000";
			$fil=$this->upload->initialize($config,false);
			if (!$this->upload->do_upload($k)) {
				//*** ocurrio un error
				$resp['imagenes'.$k] = 'Error al cargar la foto'.$k;
			}else{
				$resp['imagenes'.$k] = true;
				$this->cur->update_by(array('cur_id'=>$id),array('cur_foto'.$k=>$carpeta.'/'.$fil->file_name));
			}
			}
		}	
		$resp['data']=$this->cur->allporId($id);
		$resp['ok']=true;
		
		$this->response($resp);
	}

	/**
	 *{
	*"id_edit": "1",
	*"data": {
		*"cur_nombre":"PILATES uno",
		*"cur_descripcion":"aerobicos para toda la familia",
		*"cur_fk_empresa":"6",
	  *"cur_estado":1
	 *}
	*}

	 */
	function editar_post(){
		$this->load->model('cursos_model','cur');
		$this->load->library('upload');
		$data=json_decode($this->post('data'));
		$data->uploadError='';
		$id=$data->id_edit;
		if(count($_FILES)>0){
			foreach($_FILES as $k=>$values){
				$carpeta = 'imagenes/cursos/'.$id;
				if (!file_exists($carpeta)) {
					mkdir($carpeta, 0777, true);
				}
			$mi_archivo = $values['name'];
			$config['upload_path'] = $carpeta;
			$config['file_name'] =$mi_archivo;
			$config['allowed_types'] = "*";
			$config['overwrite']=true;
			$config['max_size'] = "50000";
			$config['max_width'] = "2000";
			$config['max_height'] = "2000";
			$fil=$this->upload->initialize($config,false);
			if (!$this->upload->do_upload($k)) {
				//*** ocurrio un error
				$resp['imagenes'.$k] = true;
			}else{
				$resp['imagenes'.$k] = true;
				$this->cur->update_by(array('cur_id'=>$id),array('cur_foto'.$k=>$carpeta.'/'.$fil->file_name));
			}
			}
		}
		$this->cur->update_by(array('cur_id'=>$id),$data->data);	
		$resp['data']=$this->cur->allporId($id);
		$resp['ok']=true;
		$this->response($resp);
	 }


	 /**
	 * GET $pagina:number
	 */

	 function cursosempresa_post(){
		 
		$this->load->model("cursos_model","cur");
		$empresa=$this->post('empresa');
		$cantdat=$this->cur->count_all();
		/*
		$pag=$this->get('pagina');
		if(empty($pag)){
			$pag=1;
		}
		$pag=($pag-1)*12;
		
		$cantdat1=ceil($cantdat/12);
		$data=$this->cur->limit(12,$pag)->cursosempresa($empresa);
		*/

		$data=$this->cur->cursosempresa($empresa);
		
		$res['lista']=$data;
		$res['ok']=true;
		//$res['pag_actual']=$pag;
		$res['cantidad']=$cantdat;
		//$res['cant_paginas']=$cantdat1;
		$this->response($res);		
	 }


	 function updateOrdenCurso_post (){
		$this->load->model("cursos_model","cur");
		$fk_empresa = $this->post('fk_empresa');
		$orden = $this->post('orden');
		$id_curso = $this->post('cur_id');
		
		if ($orden > 0) {
			$valid_orden = $this->cur->count_by(array(
				'cur_fk_empresa'=>$fk_empresa,
				'cur_orden'=>$orden
			));
		}else{
			$valid_orden = 0;
		}
		
		
		if($valid_orden>0){
			$res['success']=false;

		}else{

			if($orden == 0){
				$orden = '999';
			}
			$this->cur->update_by(array(
				'cur_id' => $id_curso
			),array(
				'cur_orden' => $orden
			));
			

			$res['success']=true;
	    		
		}
		$data=$this->cur->cursosempresa($fk_empresa);
		$res['data']=$data;

		$this->response($res);	

	
	 }


	 /**
	 * GET $pagina:number
	 */

	
	function eliminarCurso_post(){
		$this->load->model('cursos_model','cur');
		$curso = $this->post('curso');
		$empresa = $this->post('empresa');
		
		$this->cur->delete_by(array(
			'cur_id'=>$curso
		));
		$data=$this->cur->limit(12,1)->cursosempresa($empresa);

		$res['lista']=$data;
		$res['ok']=true;
		$this->response($res);		
	}

	function cursosall_get(){
		$this->load->model("cursos_model","cur");
		$data=$this->cur->allcursos();
		
		$res['lista']=$data;
		$res['ok']=true;
		$this->response($res);	

	 }
	
	 function cursosallAmin_get(){
		$this->load->model("cursos_model","cur");
		$data=$this->cur->allcursos();

		
		$res['lista']=$data;
		$res['ok']=true;
		$this->response($res);	

	 }


	 /**
	 * POST
	 	* {
		*"cur_id": 1
	 	*}
	 */
	function traerId_post(){
		$this->load->model('cursos_model','cur');
		$id=$this->post('cur_id');	
		$info=$this->cur->allporId($id);
		$resp['ok']=true;
		$resp['data']=$info;
		$this->response($resp);
	 }

	/**
	 * GET $pagina:number
	 */
	function listar_get(){
		$this->load->model('cursos_model','cur');
		$pag=$this->get('pagina');
		if(empty($pag)){
			$pag=1;
		}
		$pag=($pag-1)*12;
		$cantdat=$this->cur->count_all();
		$cantdat=ceil($cantdat/12);
		$data=$this->cur->limit(12,$pag)->get_all();
		$res['lista']=$data;
		$res['ok']=true;
		$res['pag_actual']=$pag;
		$res['cant_paginas']=$cantdat;
		$this->response($res);		
	}
	/**
	 * GET $pagina:number
	 */

	 function listaractivos_get(){
		$this->load->model('cursos_model','cur');
		$pag=$this->get('pagina');
		if(empty($pag)){
			$pag=1;
		   }
		$ini=($pag-1)*12;
		$cantdat=$this->cur->count_by(array(
			'cur_estado'=>'1'
		));
		$cantdat=ceil($cantdat/12);
		$result=$this->cur->limit(12,$ini)->get_many_by(array(
			'cur_estado'=>1
		));	
		$resp['ok']=true;
		$resp['lista']=$result;
		$resp['pag_actual']=$pag;
		$resp['cant_pag']=$cantdat;
		$this->response($resp);
	 }

	 

	  /**
	 * POST
	 * {
			*"id_edit":1,
	   *}

	 */

	 function activar_post(){
	    //desactivar curso
		$this->load->model('cursos_model','cur');
		$id=$this->post('id');
		
		$this->cur->update_by(array(
			'cur_id'=>$id
		),array(
			'cur_estado'=>'1'
		));

		$resp['ok']=true;
		$this->response($resp);

	}
	
	 /**
		 * POST
	 	* {
			*"id_edit":1,
	    *}

	 */
	 function desactivar_post(){
	    //desactivar curso
		$this->load->model('cursos_model','cur');
		$id=$this->post('id');
		$resp=array();
		$this->cur->update_by(array(
			'cur_id'=>$id
		),array(
			'cur_estado'=>'0'
		));
		$resp['ok']=true;
		$this->response($resp);
	}	

	/**
	 * GET
	 * {
	 * 	"nombre":"aerobicos",
	 *  "empresa":"1"
	 * }
	 */
	function filtrar_get(){
		$this->load->model('cursos_model', 'cur');
		$data=array();
		$where=array();
		$param=$this->get();
		foreach($param as $w => $val){
			$flag=array_search($w,$this->campos);
			if($flag!==FALSE){
			  $where[$flag.' LIKE'] = '%' .$val.'%';	
			}
		}
	
		$sd = $this->cur->filtrar($where);
		if(count($sd)>0){
		   $resp['ok'] = true;
           $resp['lista'] = $sd;
		}else{
		   $resp['ok'] = false;
           $resp['lista'] = $sd;
		}	
		$this->response($resp);
	}
}
