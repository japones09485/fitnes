<?php

defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
require(APPPATH.'libraries/Rest_Controller.php');
require(APPPATH.'libraries/Format.php');

class Rest_pagos extends Rest_Controller{
	
	function __construct() {
		parent::__construct();
		header('Access-Control-Allow-Origin: *');
		header("Access-Control-Allow-Headers: X-API-KEY, Origin, authorization, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
		header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
	}

/**
 * POST
 * {
 * 	"usuario":1,
 *  "clase":2,
 *  "valor":150000,
 *  "ref_pago":1168165165
 *  "estado": 1
 * }
 */
function guardar_post(){
	$this->load->model('pagos_model','pag');
	$data=$this->post();
	
	$id=$this->pag->insert(array(
		'pag_fk_usuario'=> $data['usuario'],
		'pag_fecha'=> date( 'Y-m-d H:i:s' ),
		'pag_fk_clase'=> $data['clase'],
		'pag_valor'=> $data['valor'],
		'pag_ref_transaccion'=> $data['ref_pago'],
		'pag_estado' => $data['estado']
		
	));
	$resp['pago']=$this->pag->get_by(array('pag_id'=>$id));
	$resp['ok']=true;
	$this->response($resp);
}

//listar todos los pagos

	/**
	 * GET $pagina:number
	 */

function listar_post(){
	$this->load->model('pagos_model','pag');
	
	$pag=$this->post('pagina');
		if(empty($pag)){
			$pag=1;
		   }

	$ini=($pag-1)*20;
	$cantdat=count($this->pag->get_all());
	$cantdat=ceil($cantdat/20);
	$datos=$this->pag->limit(20,$ini)->get_all();
	$pagos=array();
	
	foreach($datos as $pago){
	
		$pagos[]=$this->pag->all_pagos($pago->pag_id);
		
		
	}
	
	$resp['lista']=$pagos;
	$resp['ok']=true;
	$resp['pagActual']=$pag;
	$resp['cantPag']=$cantdat;
	$this->response($resp);

}
}
