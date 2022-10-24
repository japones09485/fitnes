<?php

defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
require(APPPATH.'libraries/Rest_Controller.php');
require(APPPATH.'libraries/Format.php');

class Rest_gimnasios extends REST_Controller
{
	private $campos=array(
		'gim_nombre'=>'nombre',
		'gim_pais'=>'pais',
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
	*{
	*"nombre": "bodytech bosa",
	*"nit": "11223344",
	*"email": "bodytech@gmail.com",
  	*"pais": "COL",
	*"ciudad":"BOGOTA",
	*"facebook":"www.facebook.com",
	*"instagram":"www.instagram.com",
	*"telefono": "32011161561",
	*"descripcion": "Gimnasios a nivel nacional",
	*"mapa":"120.565416651165",
	*"ruta": "455.266",
	*"estado": "1"
	*}
*/

	public function crear_post(){
		$this->load->model('gimnasios_model','gim');
		$this->load->library('upload');
		$data=json_decode($this->post('data'));
		$exist=$this->gim->count_by(array(
			"gim_nit"=>$data->nit
		));
		
		if($exist==0){
			$id=$this->gim->insert(array(
				'gim_nombre'=>$data->nombre,
				'gim_nit'=>$data->nit,
				'gim_email'=>$data->email,
				'gim_pais'=>$data->pais,
				'gim_ciudad'=>$data->ciudad,
				'gim_telefono'=>$data->telefono,
				'gim_facebook'=>$data->facebook,
				'gim_instagram'=>$data->instagram,
				'gim_descripcion'=>$data->descripcion,
				'gim_mapa'=>$data->mapa,
				'gim_ruta'=>$data->ruta,
				'gim_estado'=>1
			));
		//carga de archivos
		if(!empty($_FILES)){
			
			foreach($_FILES as $k=>$values){
				$carpeta = 'imagenes/gimnasios/'.$id;
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
				$this->gim->update_by(array('gim_id'=>$id),array('gim_foto'.$k=>$carpeta.'/'.$fil->file_name));
				$resp['imagenes'.$k] = true;
			}
			}
		}	
		$resp['data']=$this->gim->get_by(array("gim_id"=>$id));;
		$resp['ok']=true;
		}else{
			$resp['data']='';
			$resp['ok']=false;
		}
		$this->response($resp);
	}
  /**
	 * POST
	*{
	*id_edit":"18",	
	*"data":{	
	*"nombre": "JAPOTECH BOSA YORK",
	*"nit": "110223344",
	*"email": "bodytech@gmail.com",
    *"pais": "COL",
	*"ciudad":"BOGOTA",
	*"telefono": "32011161561",
	*"descripcion": "Gimnasios a nivel nacional",
    *"mapa":"120.565416651165",
	*"ruta": "455.266",
	*
 	*
   */
	public function editar_post(){
		$this->load->model('gimnasios_model','gim');
		$this->load->library('upload');
		$data = $inf = json_decode($this->post('data'));
		$id=$data->id_edit;
		if(count($_FILES)>0){
			foreach($_FILES as $k=>$values){
				$carpeta = 'imagenes/gimnasios/'.$id;
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
				$this->gim->update_by(array('gim_id'=>$id),array('gim_foto'.$k=>$carpeta.'/'.$fil->file_name));
				$resp['imagenes'.$k] = true;	
			}
			}
		}
		$this->gim->update_by(array('gim_id'=>$id),array(
				'gim_nombre'=>$inf->nombre,
				'gim_nit'=>$inf->nit,
				'gim_email'=>$inf->email,
				'gim_pais'=>$inf->pais,
				'gim_ciudad'=>$inf->ciudad,
				'gim_telefono'=>$inf->telefono,
				'gim_facebook'=>$inf->facebook,
				'gim_instagram'=>$inf->instagram,
				'gim_descripcion'=>$inf->descripcion,
				'gim_mapa'=>$inf->mapa,
				'gim_ruta'=>$inf->ruta,
				'gim_estado'=>$inf->estado
		));

		$resp['data']=$this->gim->get_by(array('gim_id'=>$id));
		$resp['ok']=true;
		$this->response($resp);
	}

//trae todos los gimnasios (admin)
	/**
	 * GET $pagina:number
	 */
	function listar_get(){
		$this->load->model('gimnasios_model','gim');
		$this->load->model('likes_model','lik');
		$pag=$this->get('pagina');
		if(empty($pag)){
			$pag=1;
		   }
		$ini=($pag-1)*20;
		$cantdat=count($this->gim->get_all());
		$cantdat=ceil($cantdat/20);
		$data=$this->gim->limit(20,$ini)->order_by('gim_likes','DESC')->get_all();
		foreach($data as $dat){
			$instructores=$this->gim->instructoresporgim($dat->gim_id);
			$likes=$this->lik->likeporgimnasio($dat->gim_id);
			$dat->instructores=$instructores;
			$dat->likes=$likes;
		}
		$resp['lista']=$data; 
		$resp['ok']=true;
		$resp['pag_actual']=$pag;
		$resp['cant_pag']=$cantdat;
		$this->response($resp);
	} 

     //trae gimnasios activos
	/**
	 * POST 
	 * {
	 * "pagina":1,
	 * "usuario":2
	 * }
	 */

function listaractivos_post(){
	$this->load->model('gimnasios_model','gim');
	$this->load->model('likes_model','lik');
	$pag=$this->post('pagina');
	$usuario=$this->post('usuario');
	if(empty($pag)){
		$pag=1;
	   }
	$ini=($pag-1)*20;
	$data=$this->gim->order_by('gim_likes','DESC')->get_many_by(array(
		'gim_estado'=>1
	));

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

/**
	 * POST
	 * {
			*"id":7,
	   *}

	 */
	function desactivar_post(){
	    //desactivar gimnasio
		$this->load->model('gimnasios_model','gim');
		$id=$this->post('id');
		$resp=array();
		$this->gim->update_by(array(
			'gim_id'=>$id
		),array(
			'gim_estado'=> 0
		));
		$resp['ok']=true;
		$this->response($resp);
	}

	 /**
	 * POST
	 * {
			*"id_edit":7,
	   *}

	 */

	function activar_post(){
	   //desactivar gimnasio
		$this->load->model('gimnasios_model','gim');
		$id=$this->post('id');
		$resp=array();
		$this->gim->update_by(array(
			'gim_id'=>$id
		),array(
			'gim_estado'=> 1
		));
		$resp['ok']=true;
		$this->response($resp);

	}

	/**
	 * POST
	 * {
			*"id":1,
	   *}

	 */

	function traerid_post(){
		//trae gimnasio por id
		$this->load->model('gimnasios_model','gim');
		$this->load->model('likes_model','lik');
		$id=$this->post('id');
		$gimnasio=$this->gim->get_by(array(
			'gim_id'=>$id

		));
		$instructores=$this->gim->instructoresporgim($id);
		$likes=$this->lik->likeporgimnasio($id);
		$gimnasio->instructores=$instructores;
		$gimnasio->likes=$likes;

		$resp['data']=$gimnasio;
		$resp['ok']=true;
		$this->response($resp);

	}



	 /**
	* POST
	* {
		*"gimnasio":2,
		*"usuario": 2
	*}
	*/

	function likes_post(){
		$this->load->model('likes_model','lik');
		$this->load->model('gimnasios_model','gim');
		
		$exist=$this->lik->count_by(array(
			'like_fk_idactor'=>$this->post('gimnasio'),
			'like_fk_usuario'=>$this->post('usuario'),
			'like_tipo'=>2
		));
		

		$cant_lik=$this->gim->cant_like($this->post('gimnasio'));
	
		if($exist==0){
			$this->lik->insert(array(
				'like_fk_idactor'=>$this->post('gimnasio'),
				'like_fk_usuario'=>$this->post('usuario'),
				'like_tipo'=>2
			));
			$cant_lik=$cant_lik->gim_likes+1;
			$this->gim->update_by(array('gim_id'=>$this->post('gimnasio')),array(
				'gim_likes'=>$cant_lik
			));
			$resp['ok']=true;
		}else{
			$this->lik->delete_by(array(
				'like_fk_idactor'=>$this->post('gimnasio'),
				'like_fk_usuario'=>$this->post('usuario'),
				'like_tipo'=>2
			));
			$cant_lik=$cant_lik->gim_likes-1;
			$this->gim->update_by(array('gim_id'=>$this->post('gimnasio')),array(
				'gim_likes'=>$cant_lik
			));
			$resp['ok']=false;
		}
		$tot_lik=$this->gim->cant_like($this->post('gimnasio'));
		$resp['total_likes']=$tot_lik;
		$this->response($resp);
		
	}

	
	 /**
	* GET
	* {
		*"nombre":"tea",
		*"pais":"COL"
	*}
	*/

	function filtrar_get(){
		$this->load->model('gimnasios_model','gim');
		$this->load->model('likes_model','lik');
		$data=array();
		$where=array();
		$param=$this->get();
		foreach($param as $w => $val){
			$flag=array_search($w,$this->campos);
			if($flag!==FALSE){
			  $where[$flag.' LIKE'] = '%' .$val.'%';
			}
		}
		$sd = $this->gim->filtrar($where);
		foreach($sd as $k){
			$instructores=$this->gim->instructoresporgim($k->gim_id);
			$k->instructores=$instructores;
			$k->likes=$this->lik->likeporgimnasio($k->gim_id);
			
		}		
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
