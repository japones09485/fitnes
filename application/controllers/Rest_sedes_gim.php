<?php

defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
require(APPPATH.'libraries/Rest_Controller.php');
require(APPPATH.'libraries/Format.php');

class Rest_sedes_gim extends REST_Controller
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


	public function crear_post(){
		$this->load->model('Sedes_gim_model','sed');
		$this->load->library('upload');
		$this->load->library('image_lib');

		$data=json_decode($this->post('data'));
		$fkGim=json_decode($this->post('fkGim'));

		
		$exist=$this->sed->count_by(array(
			"sed_nit"=>$data->nit
		));

		
		
		if($exist==0){
			$id=$this->sed->insert(array(
				'sed_fk_gimnasio'=>$fkGim,
				'sed_nombre'=>$data->nombre,
				'sed_nit'=>$data->nit,
				'sed_email'=>$data->email,
				'sed_pais'=>$data->pais,
				'sed_ciudad'=>$data->ciudad,
				'sed_telefono'=>$data->telefono,
				'sed_facebook'=>$data->facebook,
				'sed_instagram'=>$data->instagram,
				'sed_descripcion'=>$data->descripcion,
				'sed_mapa'=>$data->mapa,
				'sed_ruta'=>$data->ruta,
				'sed_estado'=>1,
				'sed_servicios'=>$data->servicios,
				'sed_horarios'=>$data->horarios,
				'sed_precio_mes'=>$data->precio_m,
				'sed_link_mes'=>$data->link_mes,
				'sed_precio_ano'=>$data->precio_t,
				'sed_link_ano'=>$data->link_tri,
				'sed_precio_semestre'=>$data->precio_sm,
				'sed_link_semestre'=>$data->link_sem,

			));

		
		//carga de archivos
		if(!empty($_FILES)){
			
			foreach ($_FILES as $k => $values) {
				$carpeta = 'imagenes/Sedesgimnasios/' . $id;
				if (!file_exists($carpeta)) {
					mkdir($carpeta, 0777, true);
				}

				$mi_archivo = $values['name'];
				$config['upload_path'] = $carpeta;
				$config['file_name'] = $mi_archivo;
				$config['allowed_types'] = "*";
				$fil = $this->upload->initialize($config, false);
				
				if (!$this->upload->do_upload($k)) {
					$resp['imagenes' . $k] = 'Error al cargar la foto' . $k;
				} else {
					// Configuración para redimensionar la imagen
					$config['image_library'] = 'gd2';
					$config['source_image'] = $carpeta . '/' . $fil->file_name;
					$config['maintain_ratio'] = false;
					$config['width'] = 1080;
					$config['height'] = 1080;
					
					$this->image_lib->initialize($config);
					
					if (!$this->image_lib->resize()) {
						$resp['imagenes' . $k] = 'Error al redimensionar la imagen' . $k;
					} else {
						$this->sed->update_by(array('sed_id' => $id), array('sed_foto' . $k => $carpeta . '/' . $fil->file_name));
						$resp['imagenes' . $k] = true;
					}
					
					// Limpiar configuración para la próxima iteración
					$this->image_lib->clear();
				}
			}
		}	
		$resp['lista']=$this->sed->get_many_by(array(
			"sed_fk_gimnasio"=>$fkGim
		));

		$resp['ok']=true;
		}else{
			$resp['data']='';
			$resp['ok']=false;
		}

	
		$this->response($resp);
	}
  
  public function editar_post() {
    $this->load->model('Sedes_gim_model','sed');
	$this->load->library('upload');
	$this->load->library('image_lib');

    $data = $inf = json_decode($this->post('data'));
    $id = $data->id_edit;

	
	

    if (count($_FILES) > 0) {
        foreach ($_FILES as $k => $values) {
            $carpeta = 'imagenes/Sedesgimnasios/' . $id;
            if (!file_exists($carpeta)) {
                mkdir($carpeta, 0777, true);
            }

            $mi_archivo = $values['name'];
            $config['upload_path'] = $carpeta;
            $config['file_name'] = $mi_archivo;
            $config['allowed_types'] = "*";
            $config['overwrite'] = true;
            $config['max_size'] = "50000";
            $fil = $this->upload->initialize($config, false);

            if (!$this->upload->do_upload($k)) {
                $resp['imagenes' . $k] = 'Error al cargar la imagen ' . $k;
            } else {
                $ruta_imagen = $carpeta . '/' . $mi_archivo;
				
                // Configuración para redimensionar la imagen
                $config_resize['image_library'] = 'gd2';
                $config_resize['source_image'] = $ruta_imagen;
                $config_resize['maintain_ratio'] = false;
                $config_resize['width'] = 1080;
                $config_resize['height'] = 1080;

                $this->image_lib->initialize($config_resize);

                if (!$this->image_lib->resize()) {
                    $resp['imagenes' . $k] = 'Error al redimensionar la imagen ' . $k;
                } else {
                    // Actualizar la ruta de la imagen en la base de datos
                  

					$this->sed->update_by(array('sed_id' => $id), array('sed_foto' . $k => $carpeta . '/' . $fil->file_name));
					$resp['imagenes' . $k] = true;

                }

                // Limpiar configuración de redimensión
                $this->image_lib->clear();
            }
        }
    }




    // Actualizar los demás datos del gimnasio
    $this->sed->update_by(array('sed_id' => $id), array(
        'sed_nombre'=>$inf->nombre,
		'sed_nit'=>$inf->nit,
		'sed_email'=>$inf->email,
		'sed_pais'=>$inf->pais,
		'sed_ciudad'=>$inf->ciudad,
		'sed_telefono'=>$inf->telefono,
		'sed_facebook'=>$inf->facebook,
		'sed_instagram'=>$inf->instagram,
		'sed_descripcion'=>$inf->descripcion,
		'sed_mapa'=>$inf->mapa,
		'sed_ruta'=>$inf->ruta,
		'sed_estado'=>1,
		'sed_servicios'=>$inf->servicios,
		'sed_horarios'=>$inf->horarios,
		'sed_precio_mes'=>$inf->precio_m,
		'sed_link_mes'=>$inf->link_mes,	
		'sed_precio_ano'=>$inf->precio_t,
		'sed_link_ano'=>$inf->link_tri,
		'sed_precio_semestre'=>$inf->precio_sm,
		'sed_link_semestre'=>$inf->link_sem
    ));

	
    $resp['data'] = $this->sed->get_by(array('sed_id' => $id));
    $resp['ok'] = true;

    $this->response($resp);
}



	function listar_post(){
		$this->load->model('Sedes_gim_model','sed');
		$this->load->model('likes_model','lik');
		$fkGim = $this->post('idGim');
		$data=$this->sed->get_many_by(array(
			'sed_fk_gimnasio'=>$fkGim 
		));
		
		$resp['lista']=$data; 
		$resp['ok']=true;
		$this->response($resp);
	} 

	function DeleteSede_post(){
		$this->load->model('Sedes_gim_model','sed');
		$idsede = $this->post('idSede');
		$fkGim = $this->post('sed_fk_gimnasio');

		$this->sed->delete_by(array(
			'sed_id' => $idsede
		));

		$data=$this->sed->get_many_by(array(
			'sed_fk_gimnasio'=>$fkGim 
		));
		
		$resp['lista']=$data; 
		$resp['ok']=true;

		$resp['mensaje'] = 'Sede eliminada exitosamente.';
		$this->response($resp);
	}

	
	function SedeId_post()
	{
		//trae gimnasio por id
		$this->load->model('Sedes_gim_model','sed');

		$id = $this->post('id_sede');
		$sede = $this->sed->get_by(array(
			'sed_id' => $id

		));

		$resp['sede'] = $sede;
		$resp['ok'] = true;
		$this->response($resp);
	}


}	
