<?php

defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
require(APPPATH.'libraries/Rest_Controller.php');
require(APPPATH.'libraries/Format.php');

class Rest_productos extends REST_Controller
{
	private $campos=array(
		'pro_nombre'=>'nombre'
	);

    public function __construct()
    {
        parent::__construct();
        header('Access-Control-Allow-Origin: *');
		header("Access-Control-Allow-Headers: X-API-KEY, Origin, authorization, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
		
	}
	/*
	{
		"nombre" : "Pesas",
		"descripcion" : "Pieza de metal de peso conocido que se usa para determinar lo que pesa un objeto, con la que se equilibra en una balanza.",
		"referencia" : "#125478",
		"cantidad" : 100,
		"usuario": 1,
		"foto1" : "",
		"foto2" : "",
		"foto3" : "",
		"estado" : "1",
		"precio" : 20
	}
	*/

	

	
	public function crear_post(){

		$this->load->model('productos_model','pro_m');
		$this->load->library('upload');
		$data = json_decode ($this->post('data'));
		
		$id = $this->pro_m->insert(array(
			"pro_nombre" => $data->nombre,
			"pro_descripcion" => $data->descripcion,
			"pro_referencia" => $data->referencia,
			"pro_cantidad" => $data->cantidad,
			"pro_fechaCreacion" => date( 'Y-m-d H:i:s' ),
			"pro_fk_usuario" => $data->usuario,
			"pro_estado" => 1,
			"pro_precio" => $data->precio  
		));
		//carga de archivos
	 	if(!empty($_FILES)){
			
			foreach($_FILES as $k=>$values){
				$carpeta = 'imagenes/productos/'.$id;
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
				$this->pro_m->update_by(array('pro_id'=>$id),array('pro_foto'.$k=>$carpeta.'/'.$fil->file_name));
				$resp['imagenes'.$k] = true;
			}
			}
		}
	$resp['resp'] = $this->pro_m->get_by(array('pro_id' => $id)); 
	$resp['ok'] = true;
	$this->response($resp);
}


/*{ *id_edit":"18",	
	info:{
	"nombre" : "Pesas",
	"descripcion" : "Pieza de metal de peso conocido que se usa para determinar lo que pesa un objeto, con la que se equilibra en una balanza.",
	"referencia" : "#125478",
	"cantidad" : 100,
	"usuario": 1,
	"foto1" : "",
	"foto2" : "",
	"foto3" : "",
	"estado" : "1"	,
	"precio" : 25
	}
}
*/

public function editarProducto_post() {
	$this->load->model('productos_model','pro_m');
	$this->load->library('upload');
	$data  = json_decode($this->post('data'));
	
	$inf = $data->data;
	$id = $data->id_edit;

	if(count($_FILES)>0){
		foreach($_FILES as $k=>$values){
			$carpeta = 'imagenes/productos/'.$id;
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
			$this->pro_m->update_by(array('pro_id'=>$id),array('pro_foto'.$k=>$carpeta.'/'.$fil->file_name));
			$resp['imagenes'.$k] = true;	
		}
		}
	}

	$this->pro_m->update_by(array('pro_id'=>$id),array(
		"pro_nombre" => $inf->nombre,
		"pro_descripcion" => $inf->descripcion,
		"pro_referencia" => $inf->referencia,
		"pro_cantidad" => $inf->cantidad,
		"pro_precio" => $inf->precio,
		"pro_fechaCreacion" => date( 'Y-m-d H:i:s' ),
		"pro_fk_usuario" => $inf->usuario
	));

	$resp['data']=$this->pro_m->get_by(array('pro_id'=>$id));
	$resp['ok']=true;
	$this->response($resp);
}

/*
	{
		
trae todos los productos
	
	  GET $pagina:number
	 
	}
*/
function listarActivos_get() {
	$this->load->model('productos_model','pro_m');
	$pag=$this->get('pagina');
	
	if(empty($pag)){
		$pag=1;
	   }
	$ini=($pag-1)*20;
	$cantdat=count($this->pro_m->get_All());	
	$cantdat=ceil($cantdat/20);
	$data=$this->pro_m->limit(20,$ini)->get_All();
	$resp['lista']=$data; 
	$resp['ok']=true;
	$resp['pag_actual']=$pag;
	$resp['cant_pag']=$cantdat;
	$this->response($resp);
}


	function listarAll_get() {
		$this->load->model('productos_model','pro_m');
		$pag=$this->get('pagina');
		if(empty($pag)){
			$pag=1;
		   }
		$ini=($pag-1)*20;
		$cantdat=count($this->pro_m->get_all());
		$cantdat=ceil($cantdat/20);
		$data=$this->pro_m->limit(20,$ini)->get_All();
		$resp['lista']=$data; 
		$resp['ok']=true;
		$resp['pag_actual']=$pag;
		$resp['cant_pag']=$cantdat;
		$this->response($resp);
	}

	function misProductos_post(){
		 
		$this->load->model('productos_model','pro_m');
		$usuario=$this->post('usuario');
		$pag=$this->get('pagina');
		if(empty($pag)){
			$pag=1;
		}
		$ini=($pag-1)*20;
		$cantdat=$this->pro_m->count_by(array(
			'pro_fk_usuario' => $usuario
		));
		
		$cantdat=ceil($cantdat/20);
		$data=$this->pro_m->limit(20,$ini)->get_many_by(array(
			'pro_fk_usuario' => $usuario
		));
		$resp['lista']=$data; 
		$resp['ok']=true;
		$resp['pag_actual']=$pag;
		$resp['cant_pag']=$cantdat;
		$this->response($resp);
	 }

/**
	 * POST
	 * {
			*"id_prod":7,
	   *}

*/
function eliminarProducto_post() {
	$this->load->model('productos_model','pro_m');
	$id_prod = $this->post('id_prod');
	$this->pro_m->delete_by(array('pro_id' => $id_prod));	
	$resp['ok']  = true;
	$this->response($resp);
}


function traerId_post(){
	$this->load->model('productos_model','pro_m');
	$id = $this->post('id');
	$producto = $this->pro_m->traerId($id);
	$data['ok'] = true;
	$data['producto'] = $producto;	
	$this->response($data);
}


	 /**
	* GET
	* {
		*"nombre":"tea"
	*}
	*/

	function filtrar_get(){
		$this->load->model('productos_model','pro_m');
		$param=$this->get();
		
		foreach($param as $w => $val){
			$flag=array_search($w,$this->campos);
			if($flag!==FALSE){
			  $where[$flag.' LIKE'] = '%' .$val.'%';
			}
		}
		$sd = $this->pro_m->filtrar($where);
	
		if(!empty($sd)){
		   $resp['ok'] = true;
           $resp['lista'] = $sd;
		}else{
		   $resp['ok'] = false;
           $resp['lista'] = $sd;
		}
		$this->response($resp);
	}

	function procesarProductos_post(){
		$this->load->model('usuarios_model', 'usu');
		$productos = $this->post();
		$resp = array();
		$newped = array();
		$datosenvio = array();
		$totproductos = array();

		foreach($productos as $k => $value){
			
			$newped['name_vendedor'] = $value['producto']['usu_nombres'].' '.$value['producto']['usu_apellidos'];
			$newped['id_pro'] = $value['producto']['pro_id'];
			$newped['name_pro'] = $value['producto']['pro_nombre'];
			$newped['precio'] = $value['producto']['pro_precio'];
			$newped['cantidad'] = $value['cantidad'];
			$newped['referencia'] = $value['producto']['pro_referencia'];
			$newped['id_aliado'] = $value['producto']['usu_id'];
			$newped['id_localstorage'] = $k;
			$newped['valor_total'] = $value['cantidad'] * $value['producto']['pro_precio'];
			$totproductos[$value['producto']['usu_id']] []= $newped;
			$newped['id_localstorage'] = $k;
		} 
		
		foreach($totproductos as $r => $val){ 
			$valtot=0;
			$descripcion = '';
			$usuario = $this->usu->get_by(array(
				'usu_id' => $r));
				
			foreach($val as $k => $v){
				$valne = $v['cantidad'] * $v['precio'];
				$valtot = $valne + $valtot;
				$ref =  $v['referencia'];	
				$descripcion .=  $v['name_pro'].'('.$v['cantidad'].')'.'-';						
			}
			
			$referenciafinal = $ref.rand(1, 10000);
			$firmapath= $this->generar_firma($usuario->usu_apikey,$usuario->usu_merchantid,$referenciafinal,$valtot);
			
			$datosenvio[$r][0]['valor_total'] = $valtot;	
			$datosenvio[$r][0]['referencia'] = $referenciafinal;
			$datosenvio[$r][0]['merchantId'] = $usuario->usu_merchantid;
			$datosenvio[$r][0]['firma'] = $firmapath;
			$datosenvio[$r][0]['descripcion'] = $descripcion;
			
		}
		
		
		$resp['productos'] = $totproductos;
		$resp['datosenvio'] = $datosenvio;
		
		$this->response($resp);
	 }

	 function generar_firma($api_key,$merchan_id, $referencia,$valor){
		$firma = $api_key.'~'.$merchan_id.'~'.$referencia.'~'.$valor.'~USD';
		$firma = md5($firma);
		return $firma;
	 }
	 

}
