
<?php

defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
require(APPPATH.'libraries/Rest_Controller.php');
require(APPPATH.'libraries/Format.php');


class Rest_empresas extends Rest_Controller{
	private $campos=array(
		'emp_nombre'=>'nombre',
		'emp_pais'=>'pais',
	);

	public function __construct()
    {
        parent::__construct();
        header('Access-Control-Allow-Origin: *');
		header("Access-Control-Allow-Headers: X-API-KEY, Origin, authorization, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
		header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
	}
    /**
	 * POST
	* {
	*"emp_nombre": "bodytech",
	*"emp_nit": "1122334455",
	*"emp_descripcion": "Entrenamiento personal",
	*"emp_telefono": "32011161561",
	*"emp_email": "´ruebas@gmail.com",
	*"emp_pais":"COL",
	*"emp_foto1": "",
	*"emp_foto2": ",
	*"emp_foto3": "",
	*"emp_vision":"Entrenamiento personal especializado",
	*"emp_mision":"Entrenar de manera sana a las personas",
	*"emp_estado":1
	*}

	 */
	function crear_post(){
		$this->load->model('empresas_model','emp');
		$this->load->library('upload');
		$data=json_decode($this->post('data'));
	
		$exist=$this->emp->count_by(array(
			"emp_nit"=>$data->emp_nit
		));
		
		if($exist==0){
			$id=$this->emp->insert($data);
		//carga de archivos
		if(!empty($_FILES)){
			foreach($_FILES as $k=>$values){
				$carpeta = 'imagenes/empresas/'.$id;
				if (!file_exists($carpeta)) {
					mkdir($carpeta, 0777, true);
				}
			$mi_archivo = $values['name']; 
			$config['upload_path'] = $carpeta;
			$config['file_name'] =$mi_archivo;
			$config['allowed_types'] = "*";
			$fil=$this->upload->initialize($config,false);
			if (!$this->upload->do_upload($k)) {
				//*** ocurrio un error
				$resp['imagenes'.$k] = 'Error al cargar la foto'.$k;
			}else{
				$this->emp->update_by(array('emp_id'=>$id),array('emp_foto'.$k=>$carpeta.'/'.$fil->file_name));
				$resp['imagenes'.$k] = true;
			}
			}
		}	
		$resp['data']=$this->emp->get_by(array("emp_iD"=>$id));;
		$resp['ok']=true;
		}else{
			$resp['data']='';
			$resp['ok']=false;
		}
		$this->response($resp);
	}
	

	  /**
	 * POST
	 * {    
	 * {
		*"id_edit":"1",	
		*"data":
			*"emp_nombre": "bodytech",
			*"emp_nit": "1122334455",
			*"emp_descripcion": "Entrenamiento personal",
			*"emp_telefono": "32011161561",
			*"emp_email": "´ruebas@gmail.com",
			*"emp_pais":"COL",
			*"emp_foto1": "",
			*"emp_foto2": ",
			*"emp_foto3": "",
			*"emp_vision":"Entrenamiento personal especializado",
			*"emp_mision":"Entrenar de manera sana a las personas",
			*"emp_estado":1
		*}
	 *}

	 */
	function editar_post(){
		$this->load->model('empresas_model','emp');
		$this->load->library('upload');
		$data=json_decode($this->post('data'));
		$data->uploadError='';
		$id=$data->id_edit;
		if(!empty($_FILES)){
			foreach($_FILES as $k=>$values){
			$carpeta = 'imagenes/empresas/'.$id;
			if (!file_exists($carpeta)) {
					mkdir($carpeta, 0777, true);
			}
			$mi_archivo = $values['name'];
			$config['upload_path'] = $carpeta;
			$config['file_name'] =$mi_archivo;
			$config['allowed_types'] = "*";
			$config['overwrite']=true;
			$fil=$this->upload->initialize($config,false);
			if (!$this->upload->do_upload($k)) {
				//*** ocurrio un error
				$resp['imagenes'.$k] = 'Error al cargar la foto'.$k;
			}else{
				$this->emp->update_by(array('emp_id'=>$id),array('emp_foto'.$k=>$carpeta.'/'.$fil->file_name));
				$resp['imagenes'.$k] = true;
			}
			}
		}
	
		$this->emp->update_by(array("emp_id"=>$id),$data->data);
		$resp['data']=$this->emp->get_by(array("emp_id"=>$id));;
		$resp['ok']=true;
		$this->response($resp);
	 }


	/**
	 * POST
	 	* {
		*"emp_id": 1
	 	*}
	 */
	function traerId_post(){
		$this->load->model('empresas_model','emp');
		$data=$this->post();
		$info=$this->emp->get_by($data);
		$resp['ok']=true;
		$resp['data']=$info;
		$this->response($resp);
	 }

	 
	/**
	 * GET $pagina:number
	 */

	function listar_get(){
		$this->load->model('empresas_model','emp');
		$pag=$this->get('pagina');

		if(empty($pag)){
		 $pag=1;
		}
		
		$ini=($pag-1)*12;
		$cantdat=$this->emp->count_all();
		$cantdat=ceil($cantdat/12);
		$data=$this->emp->limit(12,$ini)->order_by('emp_id','ASC')->get_many_by(array(
			'emp_estado !=' => 2
		));
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
		$this->load->model('empresas_model','emp');
		$pag=$this->get('pagina');
		$ini=($pag-1)*12;
		$cantdat=$this->emp->count_by(array(
			'emp_estado'=>'1'
		));
		$cantdat=ceil($cantdat/12);
		$result=$this->emp->limit(12,$ini)->order_by('emp_id','ASC')->get_many_by(array(
			'emp_estado'=>1
		));	
		$resp['ok']=true;
		$resp['lista']=$result;
		$resp['pag_actual']=$pag;
		$resp['cant_pag']=$cantdat;
		$this->response($resp);
	 }

	 function eliminarCliente_post(){
		$this->load->model('empresas_model','emp');
		$this->load->model('cursos_model','cur');

		$empresa=$this->post('cliente');
		
		$this->cur->delete_by(array(
			'cur_fk_empresa'=>$empresa
		));

		$this->emp->delete_by(array(
			'emp_id' => $empresa
		));	

		
		$result=$this->emp->limit(12,1)->order_by('emp_id','ASC')->get_many_by(array(
			'emp_estado !='=>2
		));	
		$resp['ok']=true;
		$resp['lista']=$result;
		$this->response($resp);

	 }

	 /**
	 * POST
	 * {
		*"emp_id":"1",
	
		*}

	 */

	 function activar_post(){
		 $this->load->model('empresas_model','emp');
		 $id=$this->post();
		 $this->emp->update_by(array(
			'emp_id'=>$id['id']
		),array(
			'emp_estado'=>'1'
		));

		$resp['ok']=true;
		$this->response($resp);
	 }

//lleva todas las empresas
	 function getall_get()
    {
        $this->load->model('empresas_model', 'emp');
        $list = $this->emp->order_by('emp_id','ASC')->get_all();
        $resp['ok'] = true;
        $resp['lista'] = $list;
        $this->response($resp);
	}
	
	/**
	 * GET
	 * {
	 *	"nombre":"juan"
	 *  "pais" : "COL"

	 * }
	 */
	function filtrar_get(){
		$this->load->model('empresas_model', 'emp');
		$data=array();
		$where=array();
		$param=$this->get();
		foreach($param as $w => $val){
			$flag=array_search($w,$this->campos);
			if($flag!==FALSE){
			  $where[$flag.' LIKE'] = '%' .$val.'%';	
			}
		}
		$sd = $this->emp->get_many_by($where);
		if(!empty($sd)){
		   $resp['ok'] = true;
           $resp['lista'] = $sd;
		}else{
		   $resp['ok'] = false;
           $resp['lista'] = $sd;
		}	
		
		$this->response($resp);
	}

	 /**
	 * POST
	 * {
		*"emp_id":"1",
	
		*}

	 */


	 //mail contacto
	function mail_contacto_post(){
		$this->load->model('clases_model','clas');
		$nombre=$this->post('nombre');
		$mensaje=$this->post('mensaje');
		$email=$this->post('email');
		$this->email->from('contacto@cityfitnessworld.com');
		$this->email->to('contacto@cityfitnessworld.com');
		$this->email->subject('Mensaje de contacto-'.$nombre.'-'.$email);
		$this->email->message($mensaje);
		if($this->email->send()){
			$resp['ok']=TRUE;
		}else{
			$resp['ok']=FALSE;
		}
		$this->response($resp);
		
		
	   }
	   

	 function desactivar_post(){
		$this->load->model('empresas_model','emp');
		$id=$this->post();
		$this->emp->update_by(array(
		   'emp_id'=>$id['id']
	   ),array(
		   'emp_estado'=>'0'
	   ));

	   $resp['ok']=true;
	   $this->response($resp);
	}

	public function empresasAliadas_get(){
		$this->load->model('usuarios_model', 'usu'); 
		$pag = $this->get('pagina');
	
		if (empty($pag)) {
			$pag = 1;
		}
		$ini = ($pag - 1) * 12;
		$cantdat = $this->usu->count_by(array(
			'usu_perfil' => 5,
			'usu_estado' => 1
		));
		$cantdat = ceil($cantdat / 12);
		$aliados = $this->usu->limit(12, $ini)->get_many_by(array(
			'usu_perfil' => 5,
			'usu_estado' => 1
		));
		$res['lista'] = $aliados;
		$res['ok'] = true;
		$res['pag_actual'] = $pag;
		$res['cant_paginas'] = $cantdat;
		$this->response($res);
		

	 }
}
