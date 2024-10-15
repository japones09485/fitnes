<?php

defined('BASEPATH') or exit('No direct script access allowed');

use Restserver\Libraries\REST_Controller;

require(APPPATH . 'libraries/Rest_Controller.php');
require(APPPATH . 'libraries/Format.php');

class Rest_trabajos extends REST_Controller
{
	private $campos = array(
		'nombre' => 'nombre'
	);

	public function __construct()
	{
		parent::__construct();
		header('Access-Control-Allow-Origin: *');
		header("Access-Control-Allow-Headers: X-API-KEY, Origin, authorization, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
	}

	function listarTrabajos_post(){
		$this->load->model('Trabajos_model', 'tra');
		
		$usuario = $this->post('id_empresa');
		$tipo_doc = $this->post('tipo_doc');

	
		$trabajos = $this->tra->get_many_by(array(
			'usuario_creacion'=>$usuario,
			'tipo_doc'=>$tipo_doc
		));

		$data['trabajos'] = $trabajos;
		if(count($trabajos)>0){
			$data['success'] = true;
			$data['mensaje'] = 'ok';

		}else{
			$data['success'] = false;
			$data['mensaje'] = 'No existen resultados disponibles';
		}

		$this->response($data);		
	}

	function List_trabajosAct_post(){
		$this->load->model('Trabajos_model', 'tra');
		
		$usuario = $this->post('id_empresa');
		$tipo_doc = $this->post('tipo_doc');

	
		$trabajos = $this->tra->get_many_by(array(
			'usuario_creacion'=>$usuario,
			'estado'=>1
			//'tipo_doc'=>$tipo_doc
			
		));

		
		if(count($trabajos)>0){
			$trabajos = array_chunk($trabajos,5);

			$data['trabajos'] = $trabajos;
			$data['success'] = true;
			$data['mensaje'] = 'ok';

		}else{
			$data['trabajos'] = '';
			$data['success'] = false;
			$data['mensaje'] = 'No existen resultados disponibles';
		}
		
		$this->response($data);		
	}
	

	function TrabajoId_post()
	{
		$this->load->model('Trabajos_model', 'tra');
		$id = $this->post('id');
		
		
		$trabajo = $this->tra->get_by(array(
			'id'=>$id
		));
		
		$data['ok'] = true;
		$data['trabajo'] = $trabajo;
		$this->response($data);
	}


	public function crearTrabajo_post()
	{

		$this->load->model('Trabajos_model', 'tra');
		$this->load->library('upload');

		$data1 = json_decode($this->post('data'));		
		$id_empresa =$data1->id_empresa;
		$tipo_doc = json_decode($this->post('tipo_doc'));

		$id = $this->tra->insert(array(
			"nombre" => $data1->data->nombre,
			"descripcion" =>  $data1->data->descrip,
			"fecha_creacion" =>  date('Y-m-d H:i:s'),
			"usuario_creacion" =>  $id_empresa,
			"fecha_inicio" =>  $data1->data->fechaini,
			"fecha_fin" => $data1->data->fechafin,
			'estado' => 1,
			'tipo_doc' => $tipo_doc
			
		));

				//carga de archivos
				if (!empty($_FILES)) {
					
					$carpeta = 'trabajos/' . $id;
					
					foreach ($_FILES as $k => $values) {
		
						if (!file_exists($carpeta)) {
							mkdir($carpeta, 0777, true);
						}
						$mi_archivo = $values['name'];
						$config['upload_path'] = $carpeta;
						$config['file_name'] = $mi_archivo;
						$config['overwrite'] = true;
						$config['allowed_types'] = "*";
						$fil = $this->upload->initialize($config, false);
						if (!$this->upload->do_upload($k)) {
							//*** ocurrio un error
							$resp['imagenes' . $k] = 'Error al cargar la foto' . $k;
						} else {
		
							$this->tra->update_by(array('id' => $id), array('ruta_arch' => $carpeta . '/' . $fil->file_name));
							$resp['imagenes' . $k] = true;
						}
					}
				}
		$resp['data'] = $this->tra->get_by(array("id" => $id));;
		$resp['ok'] = true;	
		$this->response($resp);
	}


	public function editarTrabajo_post()
	{
		$this->load->model('Trabajos_model', 'tra');
		$this->load->library('upload');
	
		$data1 = json_decode($this->post('data'));		
		$id_empresa =$data1->id_empresa;
		$tipo_doc = json_decode($this->post('tipo_doc'));
		$id = $data1->id_trabajo;
		

		$this->tra->update_by(array('id' => $id), array(
			"nombre" => $data1->data->nombre,
			"descripcion" =>  $data1->data->descrip,
			"fecha_creacion" =>  date('Y-m-d H:i:s'),
			"usuario_creacion" =>  $id_empresa,
			"fecha_inicio" =>  $data1->data->fechaini,
			"fecha_fin" => $data1->data->fechafin,
			'estado' => 1,
			'tipo_doc' => $tipo_doc
		));

		
			
				//carga de archivos
				if (!empty($_FILES)) {
					
					$carpeta = 'trabajos/' . $id;
					
					foreach ($_FILES as $k => $values) {
		
						if (!file_exists($carpeta)) {
							mkdir($carpeta, 0777, true);
						}
						$mi_archivo = $values['name'];
						$config['upload_path'] = $carpeta;
						$config['file_name'] = $mi_archivo;
						$config['overwrite'] = true;
						$config['allowed_types'] = "*";
						$fil = $this->upload->initialize($config, false);
						if (!$this->upload->do_upload($k)) {
							//*** ocurrio un error
							$resp['imagenes' . $k] = 'Error al cargar la foto' . $k;
						} else {
		
							$this->tra->update_by(array('id' => $id), array('ruta_arch' => $carpeta . '/' . $fil->file_name));
							$resp['imagenes' . $k] = true;
						}
					}
				}
		

		$resp['resp'] = $this->tra->get_by(array('id' => $id));
		$resp['mensaje'] = 'Examen editado exitosamente';
		$resp['ok'] = true;

	
		$this->response($resp);
	}

	function eliminarTrabajo_post(){
		$this->load->model('Trabajos_model', 'tra');
		$id = $this->post('id');
		$usuario = $this->post('id_empresa');
		
		$trabajo = $this->tra->delete_by(array(
			'id'=>$id
		));

		$trabajos = $this->tra->get_many_by(array(
			'usuario_creacion'=>$usuario,
			//'tipo_doc'=>$tipo_doc
			
		));

		$data['trabajos'] = $trabajos;
		$data['ok'] = true;
		$this->response($data);	
	}
	
	function RespuestasTrabajos_post(){
		$this->load->model('Trabajos_alumnos_model', 'resp');
		$respuestas = $this->resp->RespuestasTrabajo($this->post('id_trabajo'));
		if($respuestas){
			$resp['success']=true;
			$resp['respuestas']=$respuestas;
		}else{
			$resp['success']=false;
			$resp['respuestas']=$respuestas;

		}
	
		$this->response($resp);
	}

	function GuardarRespuesta_post(){

		$this->load->model('Trabajos_alumnos_model', 'resp');
		$this->load->library('upload');

		$data1 = json_decode($this->post('data'));		
		$id_trabajo =$data1->id_trabajo;
		$usuario_cre =$data1->usuario_cre;
		$alumno =$data1->alumno;

		$aux = $this->resp->get_by(array(
			"fk_trabajo" =>  $id_trabajo,
			"fk_alumno" =>  $alumno,
			"usuario_creacion" =>  $usuario_cre
		));
		
		
		if($aux){
			$this->resp->delete_by(array(
				"id" =>  $aux->id	
			));

			$rutaDelete = 'Respuestas_trabajos/' . $id_trabajo.'/'.$aux->id;
			$this->rmDir_rf($rutaDelete);
			
		}
	
		$id = $this->resp->insert(array(
			"comentario" => $data1->data->comentarios,
			"link_respuesta" => $data1->data->link,
			"fk_trabajo" =>  $id_trabajo,
			"fk_alumno" =>  $alumno,
			"usuario_creacion" =>  $usuario_cre,
			"fecha" => date('Y-m-d H:i:s')
		));

				//carga de archivos
				if (!empty($_FILES)) {
					
					$carpeta = 'Respuestas_trabajos/' . $id_trabajo.'/'.$id;
					
					
					foreach ($_FILES as $k => $values) {
		
						if (!file_exists($carpeta)) {
							mkdir($carpeta, 0777, true);
						}
						$mi_archivo = $values['name'];
						$config['upload_path'] = $carpeta;
						$config['file_name'] = $mi_archivo;
						$config['overwrite'] = true;
						$config['allowed_types'] = "*";
						$fil = $this->upload->initialize($config, false);
						if (!$this->upload->do_upload($k)) {
							//*** ocurrio un error
							$resp['imagenes' . $k] = 'Error al cargar la foto' . $k;
						} else {
		
							$this->resp->update_by(array('id' => $id), array('ruta_arch' => $carpeta . '/' . $fil->file_name));
							$resp['imagenes' . $k] = true;
						}
					}
				}
		$resp['success'] = true;	
		$this->response($resp);
		
	}

	function rmDir_rf($carpeta)
    {
      foreach(glob($carpeta . "/*") as $archivos_carpeta){             
        if (is_dir($archivos_carpeta)){
          rmDir_rf($archivos_carpeta);
        } else {
        unlink($archivos_carpeta);
        }
      }
      rmdir($carpeta);
     }

}
