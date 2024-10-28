<?php

defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
require(APPPATH.'libraries/Rest_Controller.php');
require(APPPATH.'libraries/Format.php');

class Rest_instructores extends Rest_Controller{
	private $campos=array(
		'ins_nombre'=>'nombre',
		'ins_fk_pais'=>'pais',
		'ins_fk_perfil'=>'perfil'
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
	  *{
	*"ins_identificacion":"1125564565",
	*"ins_nombre":"japones andre",
	*"ins_apellido":"Manrique",
	*"ins_fk_empresa":1,
	*"ins_descrp_profesional":"descripcion profesional",
	*"ins_especialidades":"Especialidades",
	*"ins_cursos":"Fitness",
	*"ins_correo":"fitnes@gmail.com",
	*"ins_facebook":"dacebook.com",
	*"ins_instagram":"instagram.com",
	*"ins_estado":1,
	*"ins_telefono":"116161165",
	*"ins_fk_pais":"COL",
	*"ins_ciudad":"Bogota"
	*"cursos":[
				*{
					*"cur_id": "1",
					*"cur_nombre": "aerobicos iniciales",
					*"cur_descripcion": "aerobicos para toda la familia",
					*"cur_fk_empresa": "1",
					*"cur_foto1": "img1",
					*"cur_foto2": "img1",
					*"cur_foto3": "img1",
					*"cur_foto4": "img1",
					*"cur_estado": "1"
				*},
				*{
					*"cur_id": "2",
					*"cur_nombre": "aerobicos iniciales",
					*"cur_descripcion": "aerobicos para toda la familia",
					*"cur_fk_empresa": "1",
					*"cur_foto1": "img1",
					*"cur_foto2": "img1",
					*"cur_foto3": "img1",
					*"cur_foto4": "img1",
					*"cur_estado": "1"
				*},
	   *]
	*"gimnasios":[
		*{
			*"gim_id": "1",
			*"gim_nombre": "bodytech",
			*"gim_nit": "1122334455",
			*"gim_email": "bodytech@gmail.com",
			*"gim_telefono": "32011161561",
			*"gim_descripcion": "´Gimnasios a nivel nacional",
			*"gim_latitud":"120.56",
			*"gim_longitud": "455.266",	
			*"gim_estado": ",
			*"gim_foto1": "imagenes/gimnasios/12/4.jpg",
			*"gim_foto2":"imagenes/gimnasios/12/1.jpg",
			*"gim_foto3":"imagenes/gimnasios/12/1.jpg"
		*},
		*{
			*"gim_id": "2",
			*"gim_nombre": "bodytech",
			*"gim_nit": "80147277",
			*"gim_email": "bodytech@gmail.com",
			*"gim_telefono": "32011161561",
			*"gim_descripcion": "´Gimnasios a nivel nacional",
			*"gim_latitud":"120.56",
			*"gim_longitud": "455.266",	
			*"gim_estado": ",
			*"gim_foto1": "imagenes/gimnasios/12/4.jpg",
			*"gim_foto2":"imagenes/gimnasios/12/1.jpg",
			*"gim_foto3":"imagenes/gimnasios/12/1.jpg"
		*}
	   *]
	*}

	  *}

	 */


	function crear_post(){
		$this->load->model('instructores_model','ins');
		$this->load->model('rel_ins_cursos_model','rel_cur');
		$this->load->model('rel_gimnasio_instructores_model','rel_gim');
		$this->load->library('upload');
		$data=json_decode($this->post('data'));
		$cursos=$data->cursos;
		$gimnasios=$data->gimnasios;
		$exist=$this->ins->count_by(array(
			"ins_correo"=>$data->ins_correo
		));
 
		if($exist==0){

		
		 $id=$this->ins->insert($data);
		//carga de archivos
		if(!empty($_FILES)){
			foreach($_FILES as $k=>$values){
				$carpeta = 'imagenes/instructores/'.$id;
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
				$this->ins->update_by(array('ins_id'=>$id),array('ins_foto'.$k=>$carpeta.'/'.$fil->file_name));
			}
			}
		}

			//guardamos cursos
			if(!empty($cursos)){
				foreach($cursos as $cur){
					$this->rel_cur->insert(array(
						'rel_fk_instructor'=>$id,
						'rel_fk_curso'=>$cur->cur_id,
						'rel_fk_empresa'=>$cur->cur_fk_empresa
					));
				}
			}

			//guardamos gimnasios
			if(!empty($gimnasios)){
				foreach($gimnasios as $gim){
					$this->rel_gim->insert(array(
						'rel_fk_gimnasio'=>$gim->gim_id,
						'rel_fk_instructor'=>$id,
					));
				}
			}

		$resp['data']=$this->ins->get_by(array("ins_id"=>$id));;
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
		*"id_edit":"1",

		*}

	 */

	function editar_post(){
		$this->load->model('instructores_model','ins');
		$this->load->model('rel_ins_cursos_model','rel_cur');
		$this->load->model('rel_gimnasio_instructores_model','rel_gim');
		$this->load->library('upload');
		$data=json_decode($this->post('data'));
		$instructor=$data->data;
		$cursos=$instructor->cursos;
		$gimnasios=$instructor->gimnasios;
		$id=$data->id_edit;
		if(!empty($_FILES)){
			foreach($_FILES as $k=>$values){
			$carpeta = 'imagenes/instructores/'.$id;
			$carpeta = 'imagenes/instructores/'.$id;
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
				$resp['imagenes'.$k] = true;
				$this->ins->update_by(array('ins_id'=>$id),array('ins_foto'.$k=>$carpeta.'/'.$fil->file_name));
			}
			}
		}

		//cargamos cursos
		if(!empty($cursos)){
			$this->rel_cur->delete_by(array(
				'rel_fk_instructor'=>$id,
			));
			foreach($cursos as $cur){
				$this->rel_cur->insert(array(
					'rel_fk_instructor'=>$id,
					'rel_fk_curso'=>$cur->cur_id,
					'rel_fk_empresa'=>$cur->cur_fk_empresa
				));
			}
		}

		//guardamos gimnasios
		if(!empty($gimnasios)){
			$this->rel_gim->delete_by(array(
				'rel_fk_instructor'=>$id,
			));

			foreach($gimnasios as $gim){
				$this->rel_gim->insert(array(
					'rel_fk_gimnasio'=>$gim->gim_id,
					'rel_fk_instructor'=>$id,
				));
			}
		}

		$this->ins->update_by(array("ins_id"=>$id),$data->data);
		$resp['data']=$this->ins->get_by(array("ins_id"=>$id));
		$resp['ok']=true;
		$this->response($resp);
	}

	 /**
	 * GET $pagina:number
	 */
	function traerporcliente_get(){
		$this->load->model('instructores_model','ins');
		$empresa=$this->get('empresa');
		$data=$this->ins->traerporcliente($empresa);
		$resp['lista']=$data;
		$resp['ok']=true;
		$this->response($resp);
	}

	/**
	 * GET $pagina:number
	 */
	function listar_get(){
		
		//listado de todos instructores
		$this->load->model('instructores_model','ins');
		$this->load->model('likes_model','lik');
		$pag=$this->get('pagina');
		if(empty($pag)){
			$pag=1;
		   }
		$ini=($pag-1)*15;
		$cantdat=$this->ins->count_by(array(
			'ins_fk_perfil'=>0
		));
		$cantdat=ceil($cantdat/15);
		$data=$this->ins->limit(15,$ini)->order_by('ins_nombre','ASC')->get_many_by(array(
			'ins_estado'=>1
		));
		foreach($data as $dat){
			
			$cursos=$this->ins->cursosintructor($dat->ins_id);
			$likes=$this->lik->likeporinstructor($dat->ins_id);
			$gimnasios=$this->ins->gimnasiosinstructor($dat->ins_id);
			$dat->cursos=$cursos;
			$dat->likes=$likes;
			$dat->gimnasios=$gimnasios;
		}

		$resp['lista']=$data; 
		$resp['ok']=true;
		$resp['pag_actual']=$pag;
		$resp['cant_pag']=$cantdat;
		$this->response($resp);

	}
	

	function eliminar_post(){
		//listado de todos instructores
		$this->load->model('instructores_model','ins');
		$this->load->model('likes_model','lik');

		//eliminamos intructores
		$pag=$this->post('pagina'); 
		$id=$this->post('idAliado');

		$this->ins->delete_by(array(
			'ins_id' => $id
		));
	
		if(empty($pag)){
			$pag=1;
		   }
		$ini=($pag-1)*15;
		$cantdat=$this->ins->count_by(array(
			'ins_fk_perfil'=>0
		));
		$cantdat=ceil($cantdat/15);
		$data=$this->ins->limit(15,$ini)->order_by('ins_nombre','ASC')->get_many_by(array(
			'ins_estado'=>1
		));
		foreach($data as $dat){
			
			$cursos=$this->ins->cursosintructor($dat->ins_id);
			$likes=$this->lik->likeporinstructor($dat->ins_id);
			$gimnasios=$this->ins->gimnasiosinstructor($dat->ins_id);
			$dat->cursos=$cursos;
			$dat->likes=$likes;
			$dat->gimnasios=$gimnasios;
		}

		$resp['lista']=$data; 
		$resp['ok']=true;
		$resp['pag_actual']=$pag;
		$resp['cant_pag']=$cantdat;
		$resp['mensaje']='Instructor eliminado exitosamente';
		$this->response($resp);
	}

	/**
	 * POST 
	 * {
	 * "pagina":1,
	 * "usuario":2
	 * }
	 */

	function listaractivos_post(){
		//listado de todos instructores activos
		$this->load->model('instructores_model','ins');
		$this->load->model('likes_model','lik'); 
		$this->load->model('Carreras_model','car');
		$pag=$this->post('pagina');
		$usuario=$this->post('usuario');

		
		
		if(empty($pag)){
			$pag=1;
		   }
		$ini=($pag-1)*20;
		$cantdat=$this->ins->count_by(array(
			'ins_estado'=>1,
			'ins_fk_perfil'=>0
		));
		$cantdat=ceil($cantdat/20);
		$data=$this->ins->limit(20,$ini)->order_by('fk_puntaje_carrera','DESC')->order_by('ins_likes', 'DESC')->get_many_by(array(
			'ins_estado'=>1,
			'ins_fk_perfil'=>0
		));
		
		foreach($data as $dat){
			$cursos=$this->ins->cursosintructor($dat->ins_id);
			$carreras=$this->car->carrerasAliado($dat->ins_id);
			//$likes=$this->lik->likeporinstructor($dat->ins_id);
			$gimnasios=$this->ins->gimnasiosinstructor($dat->ins_id);

			$nivelAlto = $this->car->getNivelAlto($dat->ins_id);
		

			if($usuario>0){
				$verifi_like=$this->lik->count_by(array('like_fk_usuario'=>$usuario , 'like_fk_idactor'=>$dat->ins_id , 'like_tipo'=>1));
			if($verifi_like==0){
				$like_usu=false;
			}else{
				$like_usu=true;
			}
			$dat->verlike=$like_usu;
			}

			$dat->cursos=$cursos;
			$dat->carreras=$carreras;
			$dat->gimnasios=$gimnasios;
			$dat->nivelAlto=$nivelAlto;
				
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
			*"id":1,
	   *}

	 */
	function desactivar_post(){
	    //desactivar usuario
		$this->load->model('instructores_model','ins');
		$id=$this->post('id');
		$resp=array();
		$this->ins->update_by(array(
			'ins_id'=>$id
		),array(
			'ins_estado'=> 0
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

	function activar_post(){
	    //desactivar usuario
		$this->load->model('instructores_model','ins');
		$id=$this->post('id');
		$this->ins->update_by(array(
			'ins_id'=>$id
		),array(
			'ins_estado'=> 1
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

	function traerId_post(){
		//trae instructores por id
		$this->load->model('instructores_model','ins');
		$this->load->model('Certificaciones_alid_model', 'cert');

		$id=$this->post('id');

		$instructor=$this->ins->get_by(array(
			'ins_id'=>$id

		));

		$certificaciones = $this->cert->get_many_by(array(
			"fk_instructor"=>$id)
		);

		

		$cursos=$this->ins->cursosintructor($instructor->ins_id);
		$gimnasios=$this->ins->gimnasiosinstructor($instructor->ins_id);
		$instructor->gimnasios=$gimnasios;
		$instructor->cursos=$cursos;
		$resp['data']=$instructor;
		$resp['certificaciones']=$certificaciones;
		$resp['ok']=true;

		$this->response($resp);

	}

	 /**
	 * POST
	 * {
		*"perfil":"1",

		*}

	 */

	 function traerporperfil_post(){
		$this->load->model('instructores_model','ins');
		$perfil=$this->post('perfil');
		$instructor=$this->ins->traerporperfil($perfil);
	
		foreach($instructor as $ist){
		
			$cursos=$this->ins->cursosintructor($ist->ins_id);
			$ist->cursos=$cursos;
		}
		$resp['data']=$instructor;
		$resp['ok']=true;
		$this->response($resp);

	 }

	  /**
	 * POST
	 * {
		*"instructor":"2",
		*"curso":4

		*}

	 */

	 function eliminarcurso_post(){
		 $this->load->model('rel_ins_cursos_model','rel_ins');
		 $instructor=$this->post('instructor');
		 $curso=$this->post('curso');

		 $this->rel_ins->delete_by(array(
			'rel_fk_instructor'=>$instructor,
			'rel_fk_curso'=>$curso
		 ));
		 $resp['ok']=true;
		 $this->response($resp);
	 }

	 /**
	 * POST
	 * {
		*"instructor":"2"

		*}

	 */
	 function cursosinstructor_post(){
		$this->load->model('rel_ins_cursos_model','rel_ins');
		$this->load->model("cursos_model","cur");
		$instructor=$this->post('instructor');
		$ins_cursos=$this->rel_ins->get_many_by(array(
			'rel_fk_instructor'=>$instructor
		));
		$cursos=array();
		if(!empty($ins_cursos)){
			foreach($ins_cursos as $cur){
				$cursos[]=$this->cur->get_by(array(
					'cur_id'=>$cur->rel_fk_curso
				));
			}
		$resp['cursos']=$cursos;
		$resp['ok']=true;

		}else{
		$resp['cursos']='';
		$resp['ok']=false;
		}
		$this->response($resp);
	 }

	  /**
	 * POST
	 * {
		*"instructor":"2"

		*}

	 */
	function giminstructor_post(){
		$this->load->model("cursos_model","cur");
		$this->load->model("gimnasios_model","gim");
		$this->load->model('rel_gimnasio_instructores_model','rel_gim');
		$instructor=$this->post('instructor');
		$gim_ins=$this->rel_gim->get_many_by(array(
			'rel_fk_instructor'=>$instructor
		));
		$gimnasios=array();
		if(!empty($gim_ins)){
			foreach($gim_ins as $gim){
				$gimnasios[]=$this->gim->get_by(array(
					'gim_id'=>$gim->rel_fk_gimnasio
				));
			}

		$resp['gimnasios']=$gimnasios;
		$resp['ok']=true;

		}else{
		$resp['gimnasios']='';
		$resp['ok']=false;
		}
		$this->response($resp);
	 }



	 /*
	function filtrar_get(){
		$this->load->model('instructores_model', 'ins');
		$data=array();
		$where=array();
		$param=$this->get();
		
		foreach($param as $w => $val){
			$flag=array_search($w,$this->campos);
			if($flag!==FALSE){
			  $where[$flag.' LIKE'] = '%' .$val.'%';
			}
		}
		$sd = $this->ins->filtrar($where);
		
		if(!empty($sd)){
			foreach($sd as $s){
				$cursos=$this->ins->cursosintructor($s->ins_id);
				$s->cursos=$cursos;
			}
		   $resp['ok'] = true;
           $resp['lista'] = $sd;
		}else{
		   $resp['ok'] = false;
           $resp['lista'] = $sd;
		}

		$this->response($resp);
	}
		*/


		function filtrar_get(){
			$this->load->model('instructores_model', 'ins');
			$data=array();
			$where=array();
			$param=$this->get();
			
			foreach($param as $w => $val){
			
				  $where['CONCAT(ins_nombre , ins_apellido) LIKE'] = '%' .$val.'%';
				  
			}

			
			$sd = $this->ins->filtrar($where);
				
			if(!empty($sd)){
				foreach($sd as $s){
					$cursos=$this->ins->cursosintructor($s->ins_id);
					$s->cursos=$cursos;
				}
			   $resp['ok'] = true;
			   $resp['lista'] = $sd;
			}else{
			   $resp['ok'] = false;
			   $resp['lista'] = $sd;
			}
	
			$this->response($resp);
		}

	//validar creacion de instructores
	private function validar($iden){
		//valida si existe intructor con la misma cedula
		$this->load->model('instructores_model','ins');
		$result=$this->ins->count_by(array(
			'ins_identificacion'=>$iden
		));


		 if($result==0){
			return true;
		 }else{
			return true;
		 }
	}


	function InstructorEmail_post(){

		$this->load->model('instructores_model','ins');
		$email = $this->post('email');

		

		$ContIns = $this->ins->count_by(
			array(
				'ins_correo' => $email,
				'ins_estado'=>1
			)
		);

		if($ContIns > 0){
			
			$instructor = $this->ins->get_many_by(
				array(
					'ins_correo' => $email,
					'ins_estado'=>1
				)
			);

			$data['lista'] = $instructor;
			$data['success'] = true;

		}else{
			$data['lista'] = '';
			$data['success'] = false;
		}
		
	
		$this->response($data);

	}

	 /**
	* POST
	* {
		*"instructor":2,
		*"usuario": 2
	*}
	*/

	function likes_post(){
		$this->load->model('likes_model','lik');
		$this->load->model('instructores_model','ins');
		
		$exist=$this->lik->count_by(array(
			'like_fk_idactor'=>$this->post('instructor'),
			'like_fk_usuario'=>$this->post('usuario'),
			'like_tipo'=>1
		));
		 $total_like=$this->ins->cant_like($this->post('instructor'));
		if($exist==0){
			$this->lik->insert(array(
				'like_fk_idactor'=>$this->post('instructor'),
				'like_fk_usuario'=>$this->post('usuario'),
				'like_tipo'=>1
			));
			$total_like=$total_like->ins_likes+1;
			$this->ins->update_by(array('ins_id'=>$this->post('instructor')),array(
				'ins_likes'=>$total_like
			));
			$resp['ok']=true;
		}else{
			$this->lik->delete_by(array(
				'like_fk_idactor'=>$this->post('instructor'),
				'like_fk_usuario'=>$this->post('usuario'),
				'like_tipo'=>1
			));
			$total_like=$total_like->ins_likes-1;
			$this->ins->update_by(array('ins_id'=>$this->post('instructor')),array(
				'ins_likes'=>$total_like
			));
			$resp['ok']=false;
		}
		$total=$this->ins->cant_like($this->post('instructor'));
		$resp['total_likes']=$total->ins_likes;
		$this->response($resp);
		
	}



}
