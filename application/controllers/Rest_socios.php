<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
require(APPPATH.'libraries/Rest_Controller.php');
require(APPPATH.'libraries/Format.php');


class Rest_socios extends REST_Controller
{
	
    public function __construct()
    {
        parent::__construct();
		header('Access-Control-Allow-Origin: *');
		header("Access-Control-Allow-Headers: X-API-KEY, Origin, authorization, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
		header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    }


	
	function crear_post(){
		$this->load->model('socios_model', 'soc');
		$this->load->model('usuarios_model', 'usu');
		$this->load->library('upload');
		$data=json_decode($this->post('data'));
		//generamos codigo al azar
	
		$id=$this->soc->insert(array(
			'soc_nombre'=>$data->nombre,
			'soc_descripcion'=>$data->descrip,
			'soc_color_primario'=>$data->color1,
			'soc_color_secundario'=>$data->color2,
			'soc_pais' => $data->pais,
			'soc_telefono' => $data->telefono,
			'soc_facebook' => $data->facebook,
			'soc_instagram' => $data->instagram,
			'soc_email' => $data->email,
			'soc_pagina' => $data->pagina,
			'soc_password' => $data->password
		));

			if(!empty($_FILES)){
				
				$permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';
				$codSoc = substr(str_shuffle($permitted_chars), 0, 10).'ID'.$id;
				
				foreach($_FILES as $k=>$values){
					
					$carpeta = 'imagenes/socios/'.$id;
					if (!file_exists($carpeta)) {
						mkdir($carpeta, 0777, true);
					}

				$tip = explode('.' , $values['name']);
				$mi_archivo = 'soc_ft'.$k.'.jpg';	 
				$config['upload_path'] = $carpeta;
				$config['file_name'] =$mi_archivo;
				$config['allowed_types'] = "*";
				$fil=$this->upload->initialize($config,false);
				if (!$this->upload->do_upload($k)) {
					//*** ocurrio un error
					$resp['imagenes'.$k] = 'Error al cargar la foto'.$k;
				}else{
				
					$this->soc->update_by(array('soc_id'=>$id),array('soc_ft'.$k=>$carpeta.'/'.$mi_archivo,'soc_codigo'=>$codSoc));
					$resp['imagenes'.$k] = true;
				}
				}
			}	
		//creacion de usuario
		$this->usu->insert(array(
			'usu_nombres'=>$data->nombre,
			'usu_apellidos'=>'SocioWeb',
			'usu_email'=>$data->email,
			'usu_textoclaro'=> $data->password,
			'usu_password'=> md5($data->password),
			'usu_perfil'=>6,
			'usu_pais'=>$data->pais,
			'usu_estado'=>1,
			'usu_cod_verificacion'=>md5($data->email),
			'usu_estado_verificacion'=>1,
			'usu_fk_socio' => $id
		));

		$data1['ok'] = true;
		$data1['mensaje'] = 'Socio creado exitosamente';
		$this->response($data1);
	}

	function editar_post(){
		$this->load->model('socios_model', 'soc');
		$this->load->library('upload');
		$dat=json_decode($this->post('data'));
		$info = $dat->data;
		$id_edit = $dat->id_edit;
		
		$this->soc->update_by(array(
			'soc_id'=>$id_edit
		),array(
			'soc_nombre'=>$info->nombre,
			'soc_descripcion'=>$info->descrip,
			'soc_color_primario'=>$info->color1,
			'soc_color_secundario'=>$info->color2,
			'soc_pais' => $info->pais,
			'soc_telefono' => $info->telefono,
			'soc_facebook' => $info->facebook,
			'soc_instagram' => $info->instagram,
			'soc_email' => $info->email,
			'soc_pagina' => $info->pagina
		));
		
		if(!empty($_FILES)){
				$contfls = 1;	
			foreach($_FILES as $k=>$values){
				
				$carpeta = 'imagenes/socios/'.$id_edit;
				
				if (!file_exists($carpeta)) {
					mkdir($carpeta, 0777, true);
				}
			$tip = explode('.' , $values['name']);
			$mi_archivo = 'soc_ft'.$contfls.'.jpg';	 
			$config['upload_path'] = $carpeta;
			$config['file_name'] =$mi_archivo;
			$config['allowed_types'] = "*";
			$config['overwrite'] = true;	
			$fil=$this->upload->initialize($config,false);
			if (!$this->upload->do_upload($k)) {
				//*** ocurrio un error
				$resp['imagenes'.$k] = 'Error al cargar la foto'.$k;
			}else{
				
				$this->soc->update_by(array('soc_id'=>$id_edit),array('soc_ft'.$k=>$carpeta.'/'.$mi_archivo));
				$resp['imagenes'.$k] = true;
			}
			$contfls++;
			}
		}	
		$data['ok'] = true;
		$data['data'] = $this->soc->get_by(array(
			'soc_id'=>$id_edit
		));
		$this->response($data);			
	}

	
	function listar_get(){
		$this->load->model('socios_model','soc');
		$pag=$this->get('pagina');

		if(empty($pag)){
		 $pag=1;
		}
		
		$ini=($pag-1)*12;
		$cantdat=$this->soc->count_all();
		$cantdat=ceil($cantdat/12);
		$data=$this->soc->limit(12,$ini)->order_by('soc_id','ASC')->get_all();
		$res['lista']=$data;
		$res['ok']=true;
		$res['pag_actual']=$pag;
		$res['cant_paginas']=$cantdat;
		$this->response($res);		

	}

	function traerId_post(){
		$this->load->model('socios_model','soc');
		$id = $this->post('id');		
		$socio = $this->soc->get_by(array(
			'soc_id' => $id
		));
		$data['ok'] = true;
		$data['socio'] = $socio;
		$this->response($data);
	}

	function traerxcodigo_post(){
		$this->load->model('socios_model','soc');
		$id = $this->post('codigo');		
		$socio = $this->soc->get_by(array(
			'soc_codigo' => $id
		));
		$data['ok'] = true;
		$data['socio'] = $socio;
		$this->response($data);
	}


	function EmpreporUsu_post(){
		$this->load->model('empresas_socios_model','soc');
		$empresas = $this->soc->empresasxusu($this->post('usu'));
		$data['ok'] = true;
		$data['empresas'] = $empresas;
		$this->response($data);	
	}

	function crearEmpresaWs_post(){
		$this->load->model('empresas_socios_model','soc');	
		$this->load->library('upload');
		$post=$this->post();
		$data=json_decode($post['data']);
		$socio=json_decode($post['socio']);
		
		//generamos codigo al azar
	
		$id=$this->soc->insert(array(
			'imps_fk_socio'=>$socio,
			'imps_nombre'=>$data->nombre,
			'imps_descripcion'=>$data->descripcion,
			'imps_facebook'=>$data->facebook,
			'imps_instagram' => $data->instagram,
			'imps_pagina' => $data->pagina,
		));

			if(!empty($_FILES)){
			
				foreach($_FILES as $k=>$values){
					
					$carpeta = 'imagenes/socios/empresas/'.$id;
					if (!file_exists($carpeta)) {
						mkdir($carpeta, 0777, true);
					}

				$tip = explode('.' , $values['name']);
				$mi_archivo = 'img'.$k.'.jpg';	 
				$config['upload_path'] = $carpeta;
				$config['file_name'] =$mi_archivo;
				$config['allowed_types'] = "*";
				$fil=$this->upload->initialize($config,false);
				if (!$this->upload->do_upload($k)) {
					//*** ocurrio un error
					$resp['imagenes'.$k] = 'Error al cargar la foto'.$k;
				}else{
				
					$this->soc->update_by(array('id_emps'=>$id),array('imps_foto'.$k=>$carpeta.'/'.$mi_archivo));
					$resp['imagenes'.$k] = true;
				}
				}
			}	
		

		$data1['ok'] = true;
		$data1['mensaje'] = 'Empresa creada exitosamente';
		$this->response($data1);
	}

	function TraerEmpresasId_post(){
		$this->load->model('empresas_socios_model','soc');	
		$id = $this->post('id');
		$empresa = $this->soc->get_by(array(
			'id_emps' => $id
		));
		$data['ok'] = true;
		$data['empresa'] = $empresa;
		$this->response($data);	
	}

	function editarEmpresaWs_post(){
		$this->load->model('empresas_socios_model','soc');
		$this->load->library('upload');	
		$post=$this->post();
		$info=json_decode($post['data']);
		$id_edit=json_decode($post['socio']);


		$this->soc->update_by(array(
			'id_emps' => $id_edit
		),array(
			 'imps_nombre' => $info->nombre,
			 'imps_descripcion' => $info->descripcion,
			 'imps_facebook' => $info->facebook,
			 'imps_instagram' => $info->instagram,
			 'imps_pagina' => $info->pagina

		));
		
		if(!empty($_FILES)){
			
			foreach($_FILES as $k=>$values){
			
				$carpeta = 'imagenes/socios/empresas/'.$id_edit;
				if (!file_exists($carpeta)) {
					mkdir($carpeta, 0777, true);
				}

			$tip = explode('.' , $values['name']);
			$mi_archivo = 'img'.$k.'.jpg';	 
			$config['upload_path'] = $carpeta;
			$config['file_name'] =$mi_archivo;
			$config['allowed_types'] = "*";
			$fil=$this->upload->initialize($config,false);
			if (!$this->upload->do_upload($k)) {
				//*** ocurrio un error
				$resp['imagenes'.$k] = 'Error al cargar la foto'.$k;
			}else{
			
				$this->soc->update_by(array('id_emps'=>$id_edit),array('imps_foto'.$k=>$carpeta.'/'.$mi_archivo));
				$resp['imagenes'.$k] = true;
			}
			}
		}	


		$empresa = $this->soc->get_by(array(
			'id_emps' => $id_edit
		));
		$data['ok'] = true;
		$data['empresa'] = $empresa;

		$data['ok'] = true;
		$this->response($data);	
	}

	function traerempresasXcodigo_post(){
		$this->load->model('empresas_socios_model','soc');
		$cod_empresa = $this->post();
		$empresas = $this->soc->traerempresasXcodigo($cod_empresa[0]);
		$data['ok'] = true;
		$data['empresas'] = $empresas;
		$this->response($data);	
	}	

	function traeraliadosXempresa_post(){
		$this->load->model('socios_model', 'soc');
		$this->load->model('usuarios_model', 'usu');
		$empresasocios = $this->post();
		$usuarios = $this->usu->AliadoIXempesaws($empresasocios[0]);
		$data['ok'] = true;
		$data['usuarios'] = $usuarios;
		$this->response($data);	
		
	}


	function traeraliadosXsocio_post(){
		$this->load->model('empresas_socios_model','soc');
		$cod_empresa = $this->post();
		$empresas = $this->soc->get_many_by(array(
			'imps_fk_socio' => $cod_empresa[0]
		));
		$data['ok'] = true;
		$data['empresas'] = $empresas;
		$this->response($data);	
	}	


	function generarInforme_post(){

		$this->load->model('socios_model', 'soc');
		$this->load->model('usuarios_model', 'usu');
		$this->load->model('Rel_Alum_Instructor_model', 'alum');

		
		$empresasocios = $this->post();
		
		$usuarios = $this->usu->AliadoIXempesaws($empresasocios['socio']);
		
		foreach($usuarios as $usu){
			//array con toda la información para generar el ecxel
			$info[$usu->usu_id] = $this->alum->ListadoAlumnos($usu->usu_id);
		}

		//ordenamos datos a retornar que se mostrar en el cvs
		$alum = $this->alum->DatosCsv($empresasocios['socio'],$empresasocios['empresa'],$empresasocios['fechaini'],$empresasocios['fechafn']);
		if(sizeof($alum)>0){
			$data['ok'] = true;
			$data['data'] = $alum;
		}else{
			$data['ok'] = false;
		}
		$this->response($data);	
		
	}

	

}
