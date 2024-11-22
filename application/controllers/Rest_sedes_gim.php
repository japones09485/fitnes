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
				'sed_precio_trimestre'=>$data->precio_t,
				'sed_link_trimestre'=>$data->link_tri,
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
		'sed_precio_trimestre'=>$inf->precio_t,
		'sed_link_trimestre'=>$inf->link_tri,
		'sed_precio_semestre'=>$inf->precio_sm,
		'sed_link_semestre'=>$inf->link_sem
    ));

	
    $resp['data'] = $this->sed->get_by(array('sed_id' => $id));
    $resp['ok'] = true;

    $this->response($resp);
}


//trae todos los gimnasios (admin)
	/**
	 * GET $pagina:number
	 */
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
	$data=$this->gim->order_by('gim_likes','DESC')->get_many_by(array());

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
