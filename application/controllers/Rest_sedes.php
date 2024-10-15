<?php

defined('BASEPATH') or exit('No direct script access allowed');

use Restserver\Libraries\REST_Controller;

require(APPPATH . 'libraries/Rest_Controller.php');
require(APPPATH . 'libraries/Format.php');

class Rest_sedes extends REST_Controller
{

	public function __construct()
	{
		parent::__construct();
		header('Access-Control-Allow-Origin: *');
		header("Access-Control-Allow-Headers: X-API-KEY, Origin, authorization, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
		header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
	}


	public function crear_post()
	{
		$this->load->model('sedes_model', 'sed');
		$this->load->library('upload');
		$data = json_decode($this->post('data'));
		$id = json_decode($this->post('id_sede')); 
		$exist = 0;
		
		if($id==0) {
			$exist = $this->sed->count_by(array(
				"sed_email" => $data->email
			));
			
			if ($exist == 0) {

				$id = $this->sed->insert(array(
				'sed_nombre' => $data->nombre,
				'sed_email' => $data->email,
				'sed_telefono' => $data->telefono,
				'sed_facebook' => $data->facebook,
				'sed_instagram' => $data->instagram,
				'sed_direccion' => $data->direccion,
				'sed_mapa' => $data->mapa,
				'sed_ruta' => $data->ruta,
				'sed_pais' => $data->pais,
				'sed_ciudad' => $data->ciudad,
				'sed_estado' => $data->estado,


			));

			$resp['data'] = $this->sed->get_by(array("sed_id" => $id));;
			$resp['ok'] = true;
			$resp['mensaje'] = 'Sede creada exitosamente.';
			
			} else {
				
				$resp['data'] = '';
				$resp['ok'] = false;
				$resp['mensaje'] = 'Ya se encuentra una sede registrada con ese correo electronico.';
			}
	
		}else{

			$this->sed->update_by(array('sed_id' => $id), array(
				'sed_nombre' => $data->nombre,
				'sed_email' => $data->email,
				'sed_telefono' => $data->telefono,
				'sed_facebook' => $data->facebook,
				'sed_instagram' => $data->instagram,
				'sed_direccion' => $data->direccion,
				'sed_mapa' => $data->mapa,
				'sed_ruta' => $data->ruta,
				'sed_pais' => $data->pais,
				'sed_ciudad' => $data->ciudad,
				'sed_estado' => $data->estado,
			));

			$resp['data'] = $this->sed->get_by(array("sed_id" => $id));
			$resp['ok'] = true;
			$resp['mensaje'] = 'Sede editada exitosamente';

			
		}

			//carga de archivos
			if (!empty($_FILES) and $resp['ok']) {

				foreach ($_FILES as $k => $values) {
					$carpeta = 'imagenes/sedes/' . $id;
					if (!file_exists($carpeta)) {
						mkdir($carpeta, 0777, true);
					}
					$mi_archivo = $values['name'];
					$config['upload_path'] = $carpeta;
					$config['file_name'] = $mi_archivo;
					$config['allowed_types'] = "*";
			 								$fil = $this->upload->initialize($config, false);
					if (!$this->upload->do_upload($k)) {
						//*** ocurrio un error
						$resp['imagenes' . $k] = 'Error al cargar la foto' . $k;
					} else {
					          	$this->sed->update_by(array('sed_id' => $id), array('sed_foto' . $k => $carpeta . '/' . $fil->file_name));
						$resp['imagenes' . $k] = true;
					}
				}
			}

		$this->response($resp);
	}

	function deleteSede_post(){
		$this->load->model('sedes_model', 'sed');
		$id = $this->post('id');
		$this->sed->delete_by(array(
			'sed_id' => $id
		));

		$sedes = $this->sed->get_many_by(array(
			'sed_id' => $id
		));

		$resp['ok'] = true;
		$resp['sedes'] = $sedes;
		$this->response($resp);

	}
	



	function listar_get()
	{
		$this->load->model('gimnasios_model', 'gim');
		$this->load->model('likes_model', 'lik');
		$pag = $this->get('pagina');
		if (empty($pag)) {
			$pag = 1;
		}
		$ini = ($pag - 1) * 20;
		$cantdat = count($this->gim->get_all());
		$cantdat = ceil($cantdat / 20);
		$data = $this->gim->limit(20, $ini)->order_by('gim_likes', 'DESC')->get_all();
		foreach ($data as $dat) {
			$instructores = $this->gim->instructoresporgim($dat->gim_id);
			$likes = $this->lik->likeporgimnasio($dat->gim_id);
			$dat->instructores = $instructores;
			$dat->likes = $likes;
		}
		$resp['lista'] = $data;
		$resp['ok'] = true;
		$resp['pag_actual'] = $pag;
		$resp['cant_pag'] = $cantdat;
		$this->response($resp);
	}



	function SedesAllF_get()
	{
		$this->load->model('sedes_model', 'sed');
		$sedes = $this->sed->get_many_by(array(
			'sed_estado' => 1
		));


		$resp['lista'] = $sedes;
		$resp['ok'] = true;
		$this->response($resp);
	}


	function desactivar_post()
	{
		//desactivar gimnasio
		$this->load->model('gimnasios_model', 'gim');
		$id = $this->post('id');
		$resp = array();
		$this->gim->update_by(array(
			'gim_id' => $id
		), array(
			'gim_estado' => 0
		));
		$resp['ok'] = true;
		$this->response($resp);
	}


	function activar_post()
	{
		//desactivar gimnasio
		$this->load->model('gimnasios_model', 'gim');
		$id = $this->post('id');
		$resp = array();
		$this->gim->update_by(array(
			'gim_id' => $id
		), array(
			'gim_estado' => 1
		));
		$resp['ok'] = true;
		$this->response($resp);
	}


	function SedeId_post()
	{
		//trae gimnasio por id
		$this->load->model('sedes_model', 'sed');

		$id = $this->post('id_sede');
		$sede = $this->sed->get_by(array(
			'sed_id' => $id

		));

		$resp['sede'] = $sede;
		$resp['ok'] = true;
		$this->response($resp);
	}



	/**
	 * POST
	 * {
	 *"gimnasio":2,
	 *"usuario": 2
	 *}
	 */

	function likes_post()
	{
		$this->load->model('likes_model', 'lik');
		$this->load->model('gimnasios_model', 'gim');

		$exist = $this->lik->count_by(array(
			'like_fk_idactor' => $this->post('gimnasio'),
			'like_fk_usuario' => $this->post('usuario'),
			'like_tipo' => 2
		));


		$cant_lik = $this->gim->cant_like($this->post('gimnasio'));

		if ($exist == 0) {
			$this->lik->insert(array(
				'like_fk_idactor' => $this->post('gimnasio'),
				'like_fk_usuario' => $this->post('usuario'),
				'like_tipo' => 2
			));
			$cant_lik = $cant_lik->gim_likes + 1;
			$this->gim->update_by(array('gim_id' => $this->post('gimnasio')), array(
				'gim_likes' => $cant_lik
			));
			$resp['ok'] = true;
		} else {
			$this->lik->delete_by(array(
				'like_fk_idactor' => $this->post('gimnasio'),
				'like_fk_usuario' => $this->post('usuario'),
				'like_tipo' => 2
			));
			$cant_lik = $cant_lik->gim_likes - 1;
			$this->gim->update_by(array('gim_id' => $this->post('gimnasio')), array(
				'gim_likes' => $cant_lik
			));
			$resp['ok'] = false;
		}
		$tot_lik = $this->gim->cant_like($this->post('gimnasio'));
		$resp['total_likes'] = $tot_lik;
		$this->response($resp);
	}


	/**
	 * GET
	 * {
	 *"nombre":"tea",
	 *"pais":"COL"
	 *}
	 */

	function filtrar_get()
	{
		$this->load->model('gimnasios_model', 'gim');
		$this->load->model('likes_model', 'lik');
		$data = array();
		$where = array();
		$param = $this->get();
		foreach ($param as $w => $val) {
			$flag = array_search($w, $this->campos);
			if ($flag !== FALSE) {
				$where[$flag . ' LIKE'] = '%' . $val . '%';
			}
		}
		$sd = $this->gim->filtrar($where);
		foreach ($sd as $k) {
			$instructores = $this->gim->instructoresporgim($k->gim_id);
			$k->instructores = $instructores;
			$k->likes = $this->lik->likeporgimnasio($k->gim_id);
		}
		if (!empty($sd)) {
			$resp['ok'] = true;
			$resp['lista'] = $sd;
		} else {
			$resp['ok'] = false;
			$resp['lista'] = $sd;
		}
		$this->response($resp);
	}
}
