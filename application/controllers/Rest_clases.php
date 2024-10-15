<?php

defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
require(APPPATH.'libraries/Rest_Controller.php');
require(APPPATH.'libraries/Format.php');

class Rest_clases extends Rest_Controller{
	private $campos=array(
		'clas_nombre'=>'nombre',
		'clas_fk_instructor'=>'empresa',
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
	*"clas_nombre":"teatro",
	*"clas_descripcion":"iouwfujefjijowfjowiefj",
	*"clas_fecha_inicio":"2020-05-30",
	*"clas_fk_curso":"1",
	*"clas_tipo":"2",
	*"clas_enlace":"zoom.com"
	*"clas_estado": 1
	*}

*/

function crear_post(){
	$this->load->model('clases_model','clas');
	$data=$this->post();
	$id=$this->clas->insert($data);
	$data=$this->clas->infoId($id);
	$resp['ok']=true;
	$resp['data']=$this->clas->infoId($id);
	$this->response($resp);
}

/**
 * POST
 * {
 * "clase":2,
 * "instructor":1
 * }
 */

function clasporinstructor_post(){
	$this->load->model('clases_model','clas');
	$instructor=$this->post('instructor');
	$info=$this->clas->clasesporinstructor($instructor);
	$resp['ok']=true;
	$resp['data']=$info;
	$this->response($resp);

}

    /**
	 * POST
	 	* {
		*"id": 1
	 	*}
	 */
	function traerporid_post(){
		$this->load->model('clases_model','clas');
		$id=$this->post('clas_id');
		$info=$this->clas->infoId($id);
		$resp['ok']=true;
		$resp['data']=$info;
		$this->response($resp);
	 }


	 /**
	 * POST
	 	* {
		*"id": 1
	 	*}
	 */

	 function traerporcurso_post(){
		$this->load->model('clases_model','clas');
		$data=$this->post();
		$info=$this->clas->traerporcurso($data['id']);
		$resp['ok']=true;
		$resp['data']=$info;
		$this->response($resp);
	 }



/**
	 * GET $pagina:number
	 */
	function listar_get(){
		$this->load->model('clases_model','clas');
		$pag=$this->get('pagina');
		if(empty($pag)){
			$pag=1;
		   }
		   $ini=($pag-1)*12;
		   $cantdat=!empty($this->clas->allclases());
		   $cantdat=ceil($cantdat/12);
		   $data=$this->clas->limit(12,$ini)->order_by('clas_id','ASC')->allclases();
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
		$this->load->model('clases_model','clas');
		$pag=$this->get('pagina');
		if(empty($pag)){
			$pag=1;
		   }
		$ini=($pag-1)*12;
		$cantdat=!empty($this->clas->all_activos());
		$cantdat=ceil($cantdat/12);
		$result=$this->clas->order_by('clas_id','ASC')->limit(12,$ini)->all_activos();
		$resp['ok']=true;
		$resp['lista']=$result;
		$resp['pag_actual']=$pag;
		$resp['cant_pag']=$cantdat;
		$this->response($resp);
	 }

	 /**
	 * POST
	 * {
	 * "id_edit" : 1
	 * {
	*"clas_nombre":"teatro",
	*"clas_descripcion":"iouwfujefjijowfjowiefj",
	*"clas_fecha_inicio":"2020-05-30",
	*"clas_fk_curso":"1",
	*"clas_tipo":"2",
	*"clas_enlace":"zoom.com"
	*"clas_estado": 1
	*}
	*}
	*/

	function cursosClases_post(){
		$this->load->model('clases_model','clas');
		$this->load->model('cursos_model','cur');
		$this->load->model('usuarios_model', 'usu');

		$idCurso = $this->post('idCurso');

		$curso = $this->cur->get_by(array(
			'cur_id'=>$idCurso
		));
		
		$clases = $this->usu->get_many_by(array(
			'fk_curso' =>$idCurso,
			'usu_perfil' => 5,
			'usu_estado' => 1
		));

		$resp['curso']=$curso;
		$resp['clases']=$clases;
		$this->response($resp);

	}

	function editar_post(){
		$this->load->model('clases_model','clas');
		$data=$this->post();
		$this->clas->update_by(array('clas_id'=>$data['id_edit']),$data['data']);
		$resp['ok']=true;
		$resp['data']=$this->clas->infoId($data['id_edit']);
		$this->response($resp);
	 }


	   /**
	 * POSTf
	 * {
		*"id":"1",

		*}

	 */
		function eliminar_post(){
		$this->load->model('clases_model','clas');
		$id=$this->post('id');
		$this->clas->delete_by(array('clas_id'=>$id));
		$resp['ok']=true;
		$this->response($resp);
		}
	  /**
	 * POST
	 * {
		*"emp_id":"1",

		*}

	 */

	function activar_post(){
		$this->load->model('clases_model','clas');
	   $id=$this->post();
	   $this->clas->update_by(array(
		  'clas_id'=>$id['id_edit']
	  ),array(
		  'clas_estado'=>'1'
	  ));
	  $resp['ok']=true;
	  $this->response($resp);
   }
	/**
	* POST
	* {
	   *"emp_id":"1",
	   *}
	*/
	function desactivar_post(){
	   $this->load->model('clases_model','clas');
	   $id=$this->post();
	   $this->clas->update_by(array(
		  'clas_id'=>$id['id_edit']
	  ),array(
		  'clas_estado'=>'0'
	  ));
	  $resp['ok']=true;
	  $this->response($resp);
   }

   /**
	* GET
	* {
		*"nombre":"tea",
		*"capacitador":"1"
	*}
	*/
	function filtrar_get(){
		$this->load->model('clases_model', 'clas');
		$data=array();
		$where=array();
		$param=$this->get();
		foreach($param as $w => $val){
			$flag=array_search($w,$this->campos);
			if($flag!==FALSE){
			  $where[$flag.' LIKE'] = '%' .$val.'%';
			}
		}
		$sd = $this->clas->filtrar($where);
		if(!empty($sd)){
		   $resp['ok'] = true;
           $resp['lista'] = $sd;
		}else{
		   $resp['ok'] = false;
           $resp['lista'] = $sd;
		}
		$this->response($resp);
	}

}
