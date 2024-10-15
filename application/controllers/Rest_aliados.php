<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Restserver\Libraries\REST_Controller;

require(APPPATH . 'libraries/Rest_Controller.php');
require(APPPATH . 'libraries/Format.php');


class Rest_aliados extends REST_Controller
{


	public function __construct()
	{
		parent::__construct();
		header('Access-Control-Allow-Origin: *');
		header("Access-Control-Allow-Headers: X-API-KEY, Origin, authorization, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
		header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
	}

	function GuardarPlanes_post()
	{
		$this->load->model('planes_model', 'pla');
		$data = json_decode($this->post('data'));
	
			$this->pla->update_by(array(
				'pla_fk_aliado' => $data->id_edit
			), array(
				'plan_mes' => $data->mensual,
				'pla_mes_benef1' => $data->mes_ben1,
				'pla_mes_benef2' => $data->mes_ben2,
				'pla_mes_benef3' => $data->mes_ben3,
				'plan_semestre' => $data->semestral,
				'pla_sem_benef1' => $data->sem_ben1,
				'pla_sem_benef2' => $data->sem_ben2,
				'pla_sem_benef3' => $data->sem_ben3,
				'plan_year' => $data->anual,
				'pla_anu_benef1' => $data->anu_ben1,
				'pla_anu_benef2' => $data->anu_ben2,
				'pla_anu_benef3' => $data->anu_ben3,
				'plan_bienvenida' => $data->bienvenida
			));
			$resp['bienvenida'] = $data->bienvenida;
			$resp['mes'] = $data->mensual;
			$resp['mes_ben1'] = $data->mes_ben1;
			$resp['mes_ben2'] = $data->mes_ben2;
			$resp['mes_ben3'] = $data->mes_ben3;
			$resp['semestre'] = $data->semestral;
			$resp['sem_ben1'] = $data->sem_ben1;
			$resp['sem_ben2'] = $data->sem_ben2;
			$resp['sem_ben3'] = $data->sem_ben3;
			$resp['year'] = $data->anual;
			$resp['anu_ben1'] = $data->mes_ben1;
			$resp['anu_ben2'] = $data->mes_ben2;
			$resp['anu_ben3'] = $data->mes_ben3;
			$resp['sucess'] = true;
			$resp['mensaje'] = 'Planes configurados correctamente;';		
		$this->response($resp);
	}



	/**
	 * POST
	 * {
	 *"id_aliado":1,
	 *}

	 */

	function traerId_post()
	{
		$this->load->model('planes_model', 'pla');
		$id = $this->post('id_aliado');
		
		$planes = $this->pla->get_by(array(
			'pla_fk_aliado' => $id
		));	
	
		$data['sucess'] = true;
		$data['bienvenida'] = $planes->plan_bienvenida;
		$data['mes'] = $planes->plan_mes;
		$data['mes_ben1'] = $planes->pla_mes_benef1;
		$data['mes_ben2'] = $planes->pla_mes_benef2;
		$data['mes_ben3'] = $planes->pla_mes_benef3;
		$data['semestre'] = $planes->plan_semestre;
		$data['sem_ben1'] = $planes->pla_sem_benef1;
		$data['sem_ben2'] = $planes->pla_sem_benef2;
		$data['sem_ben3'] = $planes->pla_sem_benef3;
		$data['year'] = $planes->plan_year;
		$data['anu_ben1'] = $planes->pla_anu_benef1;
		$data['anu_ben2'] = $planes->pla_anu_benef2;
		$data['anu_ben3'] = $planes->pla_anu_benef3;
		
		$this->response($data);
	}


	function listarAliados_get()
	{
		$this->load->model('usuarios_model', 'usu');
		$pag = $this->get('pagina');

		if (empty($pag)) {
			$pag = 1;
		}
		$ini = ($pag - 1) * 12;
		$cantdat = $this->usu->count_by(array(
			'usu_perfil' => 4,
			'usu_estado' => 1
		));
		$cantdat = ceil($cantdat / 12);
		$aliados = $this->usu->limit(12, $ini)->get_many_by(array(
			'usu_perfil' => 4,
			'usu_estado' => 1
		));
		$res['lista'] = $aliados;
		$res['ok'] = true;
		$res['pag_actual'] = $pag;
		$res['cant_paginas'] = $cantdat;
		$this->response($res);
	}

	function AliadoId_post()
	{
		$this->load->model('usuarios_model', 'usu');
		$id = $this->post('id');
		$usuario = $this->usu->AliadoId($id);
		$res['usuario'] = $usuario;
		$this->response($res);
	}

	function programarClase_post()
	{
		$this->load->model('ClasesAlid_model', 'cla');
		$clase = $this->post();
		$fecha = $clase['fechapro'];


		if ($fecha >= date('Y-m-d')) {
			$this->cla->insert(array(
				'clasA_nombre' => $clase['nombre'],
				'claA_descrip' => $clase['descrip'],
				'claA_fk_aliado' => $clase['idAliad'],
				'claA_fecha_proga' => $clase['fechapro'],
				'claA_hora_proga' => $clase['horapro'],
				'claA_fecha_creacion' => date('Y-m-d H:i:s'),
				'calA_estado' => 0
			));
			$data['sucess']  = true;
			$data['mensaje'] = 'Clase programada exitosamente';
		} else {
			$data['sucess']  = false;
			$data['mensaje'] = 'La fecha de programación es inferior a la actual';
		}
		$this->response($data);
	}

	function listarClasesAlid_post()
	{
		$this->load->model('ClasesAlid_model', 'clas');
		$pag=$this->post('pagina');
		
		$idAlid = $this->post('id');
		if(empty($pag)){
			$pag=1;
	 	}
		
		$ini=($pag-1)*20;
	
		$cantdat = $this->clas->count_by(array(
			'claA_fk_aliado' => $idAlid,
			'claA_fecha_proga >=' => date('Y-m-d')
		));
		$cantdat=ceil($cantdat/20);
		$result = $this->clas->limit(20,$ini)->get_many_by(array(
			'claA_fk_aliado' => $idAlid,
			'claA_fecha_proga >=' => date('Y-m-d')
		));  

		
	
	
		$resp['data'] = $result;
		$resp['sucess'] = true;
		$resp['cant_pag']=$cantdat;
		$this->response($resp);
	}

	function ClasesAlidFront_post()
	{
		$this->load->model('ClasesAlid_model', 'clas');
		$idAlid = $this->post('id');

		$cantdat = $this->clas->count_by(array(
			'claA_fk_aliado' => $idAlid,
			'claA_fecha_proga >=' => date('Y-m-d')
		));

		$result =  $this->clas->order_by('claA_fecha_proga', 'DESC')->get_many_by(array(
			'claA_fk_aliado' => $idAlid,
			'claA_fecha_proga >=' => date('Y-m-d')
		));
		$result = array_chunk($result, 5);
		$resp['data'] = $result;
		$resp['sucess'] = true;
		$this->response($resp);
	}


	function ClaseId_post()
	{
		$this->load->model('ClasesAlid_model', 'cla');
		$id = $this->post('id');
		$data['data'] = true;
		$data['data'] = $this->cla->get_by(array(
			'claA_id' => $id
		));
		$this->response($data);
	}

	function editarClase_post()
	{
		$this->load->model('ClasesAlid_model', 'cla');
		$clase = $this->post('data');
		
		
		$id = $this->post('id_edit');
		$fecha = $clase['fechapro'];
		if ($fecha >= date('Y-m-d')) {

			$this->cla->update_by(array(
				'claA_id' => $id
			), array(
				'clasA_nombre' =>  $clase['nombre'],
				'claA_descrip' =>  $clase['descrip'],
				'claA_fk_aliado' =>  $clase['idAliad'],
				'claA_fecha_proga' =>  $clase['fechapro'],
				'claA_hora_proga' =>  $clase['horapro'],
				'calA_estado' => 0
			));
			$data['sucess']  = true;
			$data['mensaje'] = 'Clase editada exitosamente';
		} else {
			$data['sucess']  = false;
			$data['mensaje'] = 'La fecha de programación es inferior a la actual';
		}
		$this->response($data);
	}

	function GuardarVideo_post()
	{
		$this->load->model('VideosAlid_model', 'vid');
		$this->load->library('upload');
		$video = json_decode($this->post('data'));
		$aux= explode('/',$video->link);
	
		if(count($aux)==6){
			$aux[6] = 'preview';
		}
		$aux=implode("/", $aux);
	
		$this->vid->insert(array(
			'vid_titulo' => $video->titulo,
			'vid_descripcion' => $video->descrip,
			'vid_fk_aliado' => $video->idAliad,
			'vid_fecha_creacion' => date('Y-m-d H:i:s'),
			'vid_estado' => $video->estado,
			'vid_ruta' => $aux
		));
		$data['sucess'] = true;
		$data['mensaje'] = 'Video cargado exitosamente';
		$this->response($data);
	}


	function listarVidAll_post()
	{
		$this->load->model('VideosAlid_model', 'vid');
		$idAlid = $this->post('id_aliado');
		$videos = $this->vid->get_many_by(array(
			'vid_fk_aliado' => $idAlid
		));
	
		$data['sucess'] = true;
		$data['lista'] = $videos;
		$this->response($data);
	}

	function listarVid_post()
	{
		$this->load->model('VideosAlid_model', 'vid');
		$idAlid = $this->post('id_aliado');
		$videos = $this->vid->limit(30)->get_many_by(array(
			'vid_fk_aliado' => $idAlid,
			'vid_estado' => 1
		));
		$data['sucess'] = true;
		$data['lista'] = $videos;
		$this->response($data);
	}


	function VideoxId_post()
	{
		$this->load->model('VideosAlid_model', 'vid');
		$id = $this->post('id');
		$data['data'] = $this->vid->get_by(array(
			'vid_id' => $id
		));
		
		
		$txt = $data['data']->vid_ruta;
		$srr = explode('/', $txt);
		
		if(count($srr)>3){
			$data['name_arch'] = $srr[3];
		}else{
			$data['name_arch'] = 'no aplica';
		}
		
		$this->response($data);
	}

	function editarVideo_post()
	{
		$this->load->model('VideosAlid_model', 'vid');
		$video = $this->post('data');
		$id = $this->post('id_edit');
		$aux= explode('/',$video['link']);
		if(count($aux)==6){
			$aux[6] = 'preview';
		}
		
	
		
		$aux=implode("/", $aux);
		
		$this->vid->update_by(array(
			'vid_id' => $id
		), array(
			'vid_titulo' => $video['titulo'],
			'vid_descripcion' => $video['descrip'],
			'vid_fecha_creacion' => date('Y-m-d H:i:s'),
			'vid_estado' => $video['estado'],
			'vid_ruta' => $aux
		));

		$data['sucess']  = true;
		$data['mensaje'] = 'Video editado exitosamente';
		$this->response($data);
	}

	function deleteVideo_post()
	{
		$this->load->model('VideosAlid_model', 'vid');
		$this->load->helper("file");
		$id = $this->post('id');
		$video = $this->vid->get_by(array(
			'vid_id' => $id
		));
		$path = $video->vid_ruta;

	
			$mensaje = 'Video eliminado exitosamente.';
			$this->vid->delete_by(array(
				'vid_id' => $id
			));
			$list_videos = $this->vid->get_many_by(array(
				'vid_fk_aliado' => $video->vid_fk_aliado
			));
			$data['lista'] = $list_videos;
			$data['sucess'] = true;
		
		$data['mensaje'] = $mensaje;
		$data['id'] = $id;
		$this->response($data);
	}

	function ListarAlumnos_post(){
		$this->load->model('usuarios_model', 'usu');
		$this->load->model('Rel_Alum_Instructor_model', 'rel_al');
		
		$pag=$this->post('pagina');
		$idAlid = $this->post('user');
		if(empty($pag)){
			$pag=1;
		   }
		$ini=($pag-1)*12;
		$cantdat=count($this->rel_al->ListadoAlumnos($idAlid));
		$cantdat=ceil($cantdat/12);
		$result=$this->rel_al->limit(15,$ini)->order_by('rel_alum_fecha_inscripcion','ASC')->ListadoAlumnos($idAlid);
		$resp['ok']=true;
		$resp['alumnos']=$result;
		$resp['pag_actual']=$pag;
		$resp['cant_pag']=$cantdat;
		$this->response($resp);
	}

	function InscribirAlumno_post()
	{
		$this->load->model('usuarios_model', 'usu');
		$this->load->model('Rel_Alum_Instructor_model', 'rel_al');
	
		$data = $this->post('data');
		$idAlid = $this->post('user');
		$meses = $data['meses'];
		$alumno  = $this->usu->get_by(array(
			'usu_email' => $data['correo'],
			'usu_perfil' => 2
		));
		$date = date('Y-m-d');
		$mod_date = date("d-m-Y",strtotime($date."+ $meses month"));
		$fecha_final = date("Y-m-d", strtotime($mod_date));		
		
		if ($alumno) {
			//validamos que no haya sido registrado
			$inscripcion = $this->rel_al->get_by(array(
				'rel_alum_fk_alumno' => $alumno->usu_id,
				'rel_alum_fk_aliado' => $idAlid
			));
		
			
			if ($inscripcion) {
			
				$fechaactual = date('Y-m-d');
				if ($fechaactual >= $inscripcion->rel_alum_fecha_inscripcion & $fechaactual <= $inscripcion->rel_alum_fecha_fin) {
					$mensaje = 'El alumno ya se encuentra registrado y activo.';
					$alumno = $this->rel_al->AlumnoInscrito($alumno->usu_id,$idAlid);
					$data['sucess'] = false;
					$data['alumno'] = $alumno;
					$data['mensaje'] = $mensaje;
					
				} else {
					 $this->rel_al->update_by(array(
						'rel_alum_fk_alumno' => $alumno->usu_id,
						'rel_alum_fk_aliado' => $idAlid
					 ),array(
						'rel_alum_fecha_inscripcion' => date('Y-m-d'),
						'rel_alum_fecha_fin' => $fecha_final
					 ));
					 $alumno = $this->rel_al->AlumnoInscrito($alumno->usu_id,$idAlid);
					 $mensaje = "El alumno ya se encontraba registrado, Se ha renovado el mes a partir del dia de hoy.";
					 $data['sucess'] = true;
					 $data['alumno'] = $alumno;
					 $data['mensaje'] = $mensaje;
				}
				
			} else {
				$this->rel_al->insert(array(
					'rel_alum_fk_alumno' => $alumno->usu_id,
					'rel_alum_fk_aliado' => $idAlid,
					'rel_alum_fecha_inscripcion' => date('Y-m-d'),
					'rel_alum_fecha_fin' => $fecha_final
				));
				$alumno = $this->rel_al->AlumnoInscrito($alumno->usu_id,$idAlid);
				$mensaje="Alumno inscrito correctamente";
				$data['sucess'] = true;
				$data['alumno'] = $alumno;
				$data['mensaje'] = $mensaje;
			}
		} else {
			$mensaje = 'Correo no registrado.';
			$data['sucess'] = false;
			$data['alumno'] = '';
			$data['mensaje'] = $mensaje;
		}
		$this->response($data);
	}

	function EliminarAlumno_post(){
		$this->load->model('Rel_Alum_Instructor_model', 'rel_al');
		$id_reg = $this->post('id_reg');
		$alumno = $this->post('alumno');
		$aliado = $this->post('aliado');
		
		$this->rel_al->delete_by(array(
			'rel_alum_id'=>$id_reg,
			'rel_alum_fk_alumno'=>$alumno
		));
	

		$data['sucess'] = false;
		$data['data'] = $this->rel_al->limit(15,1)->order_by('rel_alum_fecha_inscripcion','ASC')->ListadoAlumnos($aliado);
		$this->response($data);
	}
	function VerifiAlumno_post(){
		$this->load->model('Rel_Alum_Instructor_model', 'rel_al');
		$aliado = $this->post('user');
		$alumno = $this->post('alumno');
		$date = date('Y-m-d');
		
		$plan = $this->rel_al->get_by(array(
			'rel_alum_fk_alumno'=>$alumno,
			'rel_alum_fk_aliado'=>$aliado,
			'rel_alum_fecha_fin >=' => $date
		));
		
	if($plan){
		$data['sucess'] = true;
		$data['fecha_fin'] = $plan->rel_alum_fecha_fin;
	}else{
		$data['sucess'] = false;
		$data['fecha_fin'] = '';
	}
	$this->response($data);	 
	}	

	function EstadoClase_post(){
		$this->load->model('ClasesAlid_model', 'cla');
		$id = $this->post('id');
		$estado = $this->post('estado');
		$this->cla->update_by(array(
			'claA_id'=>$id),array(
			'calA_estado'=>$estado	
		));
		$clase = $this->cla->get_by(array(
			'claA_id'=>$id
		));
		$data['clase'] = $clase;
		$data['sucess'] = true;
		$this->response($data);
	}

	
	 function ampliarSubsripcion_post(){
		$this->load->model('Rel_Alum_Instructor_model', 'rel_al');
		$id_alumno = $this->post('id_alum');
		$id_aliado = $this->post('idAliado');
		$fecha_fin = $this->post('fecha_fin');
		$meses = $this->post('meses');

		$mod_date = date("d-m-Y",strtotime($fecha_fin."+ $meses month"));
		$fecha_final = date("Y-m-d", strtotime($mod_date));		
		
		$this->rel_al->update_by(array(
			'rel_alum_fk_aliado'=>$id_aliado,
			'rel_alum_fk_alumno'=>$id_alumno
		),array(
			'rel_alum_fecha_fin' => $fecha_final
		));

		
		$data['sucess'] = true;
		$this->response($data);
	 }

	 public function listadoalumnos_post(){
		$this->load->model('Rel_Alum_Instructor_model', 'rel_al');
		$fechas  = $this->post('fechas');
		$id_aliado  = $this->post('id_instructor');
		$fecha_inicial = $fechas['fecha_inicio'];
		$fecha_fin = $fechas['fecha_fin'];

		if($fecha_fin <= $fecha_inicial){
			$data['sucess'] = false;
			$data['mensaje'] = 'La fecha final debe ser mayor a la inicial.';
		}else{
			$alumnos = $this->rel_al->AlumnosActivos($fecha_inicial,$fecha_fin,$id_aliado); 
			if(sizeof($alumnos)>0){
			  $data['sucess'] = true;
			  $data['alumnos'] = $alumnos;
			  $data['cantidad'] = sizeof($alumnos); 
			}else{
			  $data['sucess'] = false;
			  $data['mensaje'] = 'No hay alumnos incritos en este mes';
			}
		}
		$this->response($data);
	 }

	 function ExamenesAlidFront_post()
	{
		$this->load->model('examenes_model', 'exa');
		$idAlid = $this->post('id');

		$cantdat = $this->clas->count_by(array(
			'claA_fk_aliado' => $idAlid,
			'claA_fecha_proga >=' => date('Y-m-d')
		));

		$result =  $this->clas->order_by('claA_fecha_proga', 'DESC')->get_many_by(array(
			'claA_fk_aliado' => $idAlid,
			'claA_fecha_proga >=' => date('Y-m-d')
		));
		$result = array_chunk($result, 7);
		$resp['data'] = $result;
		$resp['sucess'] = true;
		$this->response($resp);
	}

	function CertificacionAliado_post(){
		$this->load->model('Certificaciones_alid_model', 'cert');

		$id = $this->post('idaliados');
		$result = $this->cert->get_many_by(array(
			'fk_instructor' => $id
		)); 

		$resp['data'] = $result;
		$resp['sucess'] = true;
		$this->response($resp);

		
	}

	function crearCertificacion_post(){
		$this->load->model('Certificaciones_alid_model', 'cert');
		$this->load->library('upload');

		$data=json_decode($this->post('data'));
		$id=json_decode($this->post('idcerti'));
		$idAliado = json_decode($this->post('idAliado'));
		$fechaActual = date('Y-m-d');

		if($id==0){
		
			$idinsert = $this->cert->insert(array(
				'fk_instructor'=>$idAliado,
				'descripcion'=>$data->descripcion,
				'fecha_carga' => $fechaActual,
				'estado'=>1
			));

	
		$resp['mensaje']='Certificado cargado exitosamente';


	
		}else{
			$this->cert->update_by(array(
				'id'=>$id,
			),array(
				'descripcion'=>$data->descripcion,
				'fecha_carga' => $fechaActual
			));

			$idinsert = $id;
			$resp['mensaje']='Certificado editado exitosamente';
		}
		//carga de archivos
		if(!empty($_FILES)){

			foreach($_FILES as $k=>$values){
				
				$carpeta = 'imagenes/aliados/'.$idAliado.'/certificaciones/'.$idinsert;
				
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
			
				$resp['imagenes'.$k] = true;
				$this->cert->update_by(array('id'=>$idinsert),array('documento'=>$carpeta.'/'.$fil->file_name));
			}

			}
		}
		$resp['data']=$this->cert->get_many_by(array(
			"fk_instructor"=>$idAliado)
		);
		$resp['ok']=true;


		$this->response($resp);	
	}

	function certiId_post(){
		$this->load->model('Certificaciones_alid_model', 'cert');
		$IdCert = $this->post('idCerti');
		
		$certificacion = $this->cert->get_by(array(
			'id'=>$IdCert 
		));

		$resp['ok']=true;
		$resp['certificacion']=$certificacion;
		$this->response($resp);	

	}

	function DeleteCert_post(){
		$this->load->model('Certificaciones_alid_model', 'cert');
		$IdCert = $this->post('idCerti');
		$IdAlid = $this->post('idAliado');
		
		$certificacion = $this->cert->delete_by(array(
			'id'=>$IdCert 
		));

		$resp['data']=$this->cert->get_many_by(array(
			"fk_instructor"=>$IdAlid)
		);

		$resp['ok']=true;
		$resp['mensaje']='Certificación eliminada exitosamente.';
	
		$this->response($resp);	
	}

		//funcion trae las carreras 
		function listar_carreras_post(){
			$this->load->model('Carreras_model','car');
			$carreras = $this->car->get_all();
			$resp['carreras'] = $carreras;
			$resp['success'] = true;
			$this->response($resp);	
		}

		function listar_carreras_acti_post(){
			$this->load->model('Carreras_model','car');
			$this->load->model('Rel_carreras_aliado_model','rel');

			$id_aliado = $this->post('id_aliado');
			$rel_carreras = $rel_carreras = $this->rel->get_many_by(array(
				'fk_instructor' => $id_aliado
			));
			$not_in = '';

			foreach ($rel_carreras as  $value) {
				$not_in .= $value->fk_carrera.',';
			}

			echo '<pre>';
			print_r($not_in);
			exit;
			
			
			

			$carreras = $this->car->get_many_by(array(
				'estado'=>1
			));
			$resp['carreras'] = $carreras;
			$resp['success'] = true;
			$this->response($resp);	
		}

		function EliminarCarrera_post(){
			$this->load->model('Carreras_model','car');
			$idCarrera = $this->post('idCarrera');
			$this->car->delete_by(array(
				'id'=>$idCarrera
			));

			$carreras = $this->car->get_all();
			$resp['carreras'] = $carreras;

			$resp['mensaje'] = 'Carrera eliminada exitosamente.';
			$resp['carreras'] = $carreras;
			$resp['success'] = true;
			$this->response($resp);	
			
		}

		function guardarCarrera_post(){
			$this->load->model('Carreras_model','car');
			$data=json_decode($this->post('data'));
			$user=json_decode($this->post('user'));

			$this->car->insert(array(
				'nombre'=>$data->nombre,
				'fecha_creacion'=> date('Y-m-d H:i:s'),
				'fk_usuario'=>$user,
				'estado'=>$data->estado,
				'puntaje'=>$data->puntaje
			));

			$carreras = $this->car->get_all();
			$resp['carreras'] = $carreras;

			$resp['mensaje'] = 'Carrera creada exitosamente.';
			$resp['carreras'] = $carreras;
			$resp['success'] = true;
			$this->response($resp);	
			
		}

		function InfoCarrera_post(){
			$this->load->model('Carreras_model','car');
			$idCarrera = $this->post('idCarrera');
			$carrera = $this->car->get_by(array(
				'id'=> $idCarrera
			));

			$resp['carrera'] = $carrera;
			$resp['success'] = true;
			$this->response($resp);	
		}

		function EditarCarrera_post(){
			$this->load->model('Carreras_model','car');
			$data=json_decode($this->post('data'));
			$user=json_decode($this->post('user'));
			$idCarrera=json_decode($this->post('idCarrera'));

			$this->car->update_by(array(
				'id'=>$idCarrera
			),array(
				'nombre'=>$data->nombre,
				'fecha_creacion'=> date('Y-m-d H:i:s'),
				'fk_usuario'=>$user,
				'estado'=>$data->estado,
				'puntaje'=>$data->puntaje
			));

			$carrera = $this->car->get_by(array(
				'id'=> $idCarrera
			));

			
			$resp['mensaje'] = 'Carrera editada exitosamente.';
			$resp['carrera'] = $carrera;
			$resp['success'] = true;
			$this->response($resp);	

			
		}
		


	}
