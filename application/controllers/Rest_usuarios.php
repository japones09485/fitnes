<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
require(APPPATH.'libraries/Rest_Controller.php');
require(APPPATH.'libraries/Format.php');


class Rest_usuarios extends REST_Controller
{
	private $campos=array(
		'usu_nombres'=>'nombre',
		'usu_apellidos' => 'apellidos'
	);

    public function __construct()
    {
        parent::__construct();
		header('Access-Control-Allow-Origin: *');
		header("Access-Control-Allow-Headers: X-API-KEY, Origin, authorization, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
		header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    }

	
/* 

1 Administrativo
3 Ventas
4 Instructores
5 Curso
6 Socios

*/

	 
	function restab_contra_post(){
		$this->load->model('usuarios_model', 'usu');
		$email=$this->post('email');
		$exist=$this->usu->count_by(array('usu_email'=>$email));
	
		if($exist>0){
			$this->email_registro($email,2);
			$resp['ok']=true;
		}else{
			$resp['ok']=false;
		}
		
		$this->response($resp);	
	}

	/**
	 * POST
	 * {
		*"nombres":"Carlos Andres",
		*"apellidos":"Campiño Lopez",
		*"email":"lealdesarrollo@gmail.com",
		*"contrasena":"123456789",
		*"pais":"COL"
	*}
	**/

	public function registrarse_post(){
		$this->load->model('usuarios_model', 'usu');
		$data=$this->post();
		$exist=$this->validarregistro($data['email']);
		
		if($exist==true){
			$passw=md5($data['contrasena']);
			$data['password']=$passw;
			$id=$this->usu->insert(array(
				'usu_nombres'=>$data['nombres'],
				'usu_apellidos'=>$data['apellidos'],
				'usu_email'=>$data['email'],
				'usu_textoclaro'=>$data['contrasena'],
				'usu_password'=>$passw,
				'usu_perfil'=>2,
				'usu_pais'=>$data['pais'],
				'usu_estado'=>1,
				'usu_cod_verificacion'=>md5($data['email']),
				'usu_estado_verificacion'=>0,
			));
			$this->email_registro($data['email'],1);
			$resp['ok'] = true;
			$resp['lista'] = $this->usu->get_by(array(
				'usu_id'=>$id
			));
		}else{
			$resp['ok'] = false;
			$resp['lista'] ='';
		}
		 $this->response($resp);
	}

	public function validarregistro($correo){	
		$this->load->model('usuarios_model', 'usu');
		$exist=$this->usu->count_by(array(
			'usu_email'=>$correo
		));
		if($exist==0){
			return true;
		}else{
			return false;
		}
	}

	//email pára valñidar el registro

	function email_registro($email,$tipo){
		$this->load->model('usuarios_model', 'usu');
		$codigo=md5($email);

		if($tipo==1){
			$this->usu->update_by(array(
				'usu_email'=>$email
			),array(
				'usu_cod_verificacion'=>$codigo,
			));
			$cabecera='Bienvenido a City Fitness World para validar su cuenta por favor dar click al siguiente en enlace';
			$body='https://cityfitnessworld.com/fitnes/inicio/verifycodigo/'.$codigo;
		}else{
			$cod_contrasena=md5($email.rand());
			
			$this->usu->update_by(array(
					'usu_email'=>$email),array(
					'usu_cod_contrasena'=>$cod_contrasena
			));
			$cabecera='Bienvenido a City Fitness World para reestablecer su contraseña por favor dar click al siguiente en enlace';
			$body='https://cityfitnessworld.com/fitnes/inicio/restab_contra/'.$cod_contrasena;
		}
		
		
		$usuario=$this->usu->get_by(array('usu_email'=>$email));
		
		$config['protocol'] = 'sendmail';
		$config['mailtype'] = 'html';
		$config['charset']  = 'utf-8';
		$config['newline']  = "\r\n";
		
        $this->email->clear(TRUE);
		$this->email->initialize($config);
		$this->email->set_mailtype("html");
		$this->email->from('cityfitnessworld.contacto@cityfitnessworld.com','City Fitness World');
		$this->email->to($email);
		$this->email->subject($cabecera);
		$this->email->message($body);
		if($this->email->send()){
			return TRUE;
		}else{
			return FALSE;
		}
	}

	function crear_post(){
		$this->load->model('usuarios_model', 'usu');
		$this->load->library('upload');
		$info = json_decode ($this->post('data'));
<<<<<<< HEAD
		$cursof = $this->post('data');
		$verif = $this->verify_usuario($info->email);
		
=======
		$verif = $this->verify_usuario($info->email);
>>>>>>> ab578ea9d5681b2c3400b00408c2afaa99b93d60
	
		if($verif == true){
			$id = $this->usu->insert(array(
				'usu_nombres'=>$info->nombre,
				'usu_apellidos'=>$info->apellidos,
				'usu_descrip_profesional'=>$info->descripcion,
				'usu_email'=>$info->email,
				'usu_facebook'=>$info->facebook,
				'usu_instagram'=>$info->instagram,
				'usu_telefono'=>$info->telefono,
				'usu_textoclaro'=>$info->contras,
				'usu_password'=>md5($info->contras),
				'usu_perfil'=>$info->perfil,
				'usu_pais'=>$info->pais,
				'usu_estado'=>$info->estado,
				'usu_estado_verificacion'=>1,
				'usu_epayco'=>$info->epayco,
<<<<<<< HEAD
				'usu_apikey'=>$info->apikey,
				'usu_merchantid'=>$info->merchantid,
				'link_pago_mes'=>$info->link_mes,
				'link_pago_trimestre'=>$info->link_trimestre,
				'link_pago_semestre'=>$info->link_semestre,
				'fk_curso'=>$info->cursof,
				'usu_fk_empresa_socio'=>$info->empresaws,
				'usu_videoB' => $info->videoB
			));
			
			if($info->perfil == 4 or $info->perfil == 5){
=======
				'link_pago_mes'=>$info->link_mes,
				'link_pago_trimestre'=>$info->link_trimestre,
				'link_pago_semestre'=>$info->link_semestre
			));
			
			if($info->perfil == 4){
>>>>>>> ab578ea9d5681b2c3400b00408c2afaa99b93d60
				$this->load->model('planes_model', 'pla');
				$this->pla->insert(array(
				'pla_fk_aliado' => $id,
				'plan_mes' => 0,
				'pla_mes_benef1' => 'Beneficio 1',
				'pla_mes_benef2' => 'Beneficio 2',
				'pla_mes_benef3' => 'Beneficio 3',
				'plan_semestre' => 0,
				'pla_sem_benef1' => 'Beneficio 1',
				'pla_sem_benef2' => 'Beneficio 2',
				'pla_sem_benef3' => 'Beneficio 3',
				'plan_year' => 0,
				'pla_anu_benef1' => 'Beneficio 1',
				'pla_anu_benef2' => 'Beneficio 2',
				'pla_anu_benef3' => 'Beneficio 3',
				'plan_bienvenida' => 'Pronto mas información'
				));
			}

			if(!empty($_FILES)){
				
				foreach($_FILES as $k=>$values){
					$carpeta = 'imagenes/ventasUsuarios/'.$id;
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
					$this->usu->update_by(array(
						'usu_id'=>$id
					),array(
						'usu_logo'=>$carpeta.'/'.$fil->file_name
					));
					$resp['imagenes'.$k] = true;
				}
				}
			}	
		$data['ok'] = true;
		$data['mensaje'] = 'usuario creado exitosamente';
		
		}else{
		  $data['ok'] = false;
		  $data['mensaje'] = 'El email ya se encuentra registrado';
		}
		$this->response($data);
		
	}

	function editar_post(){
		$this->load->model('usuarios_model', 'usu');
		$this->load->library('upload');
		$dat=json_decode($this->post('data'));
		$info = $dat->data;
		$id_edit = $dat->id_edit;
		
		$this->usu->update_by(array(
			'usu_id'=>$id_edit
		),array(
<<<<<<< HEAD
			 'usu_nombres'=> $info->nombre,
			 'usu_apellidos'=> $info->apellidos,
			 'usu_descrip_profesional'=> $info->descripcion,
			 'usu_email'=> $info->email,
			 'usu_telefono'=> $info->telefono,
			 'usu_facebook'=> $info->facebook,
			 'usu_instagram'=> $info->instagram,
			 'usu_textoclaro'=> $info->contras,
			 'usu_password'=> md5($info->contras),
			 'usu_perfil'=> $info->perfil,
			 'usu_pais'=> $info->pais, 
			 'usu_estado'=> $info->estado, 
			 'usu_epayco'=> $info->epayco,
			 'usu_apikey'=> $info->apikey,
			 'usu_merchantid'=> $info->merchantid,
			 'link_pago_mes'=> $info->link_mes,
			 'link_pago_trimestre'=> $info->link_trimestre,
			 'link_pago_semestre'=> $info->link_semestre,
			 'usu_videoB'=> $info->videoB
=======
			 'usu_nombres'=>$info->nombre,
			 'usu_apellidos'=>$info->apellidos,
			 'usu_descrip_profesional'=>$info->descripcion,
			 'usu_email'=>$info->email,
			 'usu_telefono'=>$info->telefono,
			 'usu_facebook'=>$info->facebook,
			 'usu_instagram'=>$info->instagram,
			 'usu_textoclaro'=>$info->contras,
			 'usu_password'=>md5($info->contras),
			 'usu_perfil'=>$info->perfil,
			 'usu_pais'=>$info->pais, 
			 'usu_estado'=>$info->estado, 
			 'usu_epayco'=>$info->epayco,
			 'link_pago_mes'=>$info->link_mes,
			 'link_pago_trimestre'=>$info->link_trimestre,
			 'link_pago_semestre'=>$info->link_semestre
>>>>>>> ab578ea9d5681b2c3400b00408c2afaa99b93d60
		));

		if(!empty($_FILES)){
				
			foreach($_FILES as $k=>$values){
				$carpeta = 'imagenes/ventasUsuarios/'.$id_edit;
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
				$this->usu->update_by(array(
					'usu_id'=>$id_edit
				),array(
					'usu_logo'=>$carpeta.'/'.$fil->file_name
				));
				$resp['imagenes'.$k] = true;
			}
			}
		}	
		$data['ok'] = true;
		$data['data'] = $this->usu->get_by(array(
			'usu_id'=>$id_edit
		));
		$this->response($data);			
	}

<<<<<<< HEAD
	function eliminarUsuario_post(){
		$this->load->model('usuarios_model','usu');
		$usuario = $this->post('usuario');
		$curso = $this->post('curso');

		$this->usu->update_by(array(
			'usu_id' => $this->post('usuario')
		),array(
			'usu_estado' => 2
		));

	

		$resp['ok'] = true;
		$resp['lista'] = $this->usu->get_many_by(array(
				'fk_curso'=>$curso,
				'usu_estado !='=> 2
		));
	
		$this->response($resp);
	}

=======
>>>>>>> ab578ea9d5681b2c3400b00408c2afaa99b93d60
	function verify_usuario($email){
		$this->load->model('usuarios_model', 'usu');
		$verif = $this->usu->count_by(array(
			'usu_email' => $email
		));
		if($verif>0){
			return false;
		}else{
			return true;
		}
	}

<<<<<<< HEAD
	function listar_post(){
=======
	function listar_get(){
>>>>>>> ab578ea9d5681b2c3400b00408c2afaa99b93d60
		
		//listado de todos instructores
		$this->load->model('usuarios_model', 'usu');
		$this->load->model('likes_model','lik');
<<<<<<< HEAD

		
	
		$pag=$this->post('pagina');
		$perfilf = $this->post('perfil');
		$cursof = $this->post('curso');
		
		if(empty($pag)){
			$pag=1;
		   }


		$ini=($pag-1)*15;
		if($this->post('perfil')==1){
			$usuarios=$this->usu->countusuarioAdmin();
			$usuarios = $usuarios->numrows;
			
		}else if($this->post('perfil')==4){
			$emp_ws = $this->post('emp_ws');
			$usuarios=$this->usu->countusuarioWs();
			$usuarios = $usuarios->numrows;
			
		}else if($this->post('perfil')==6){
			$emp_ws = $this->post('emp_ws');
			$usuarios=$this->usu->count_by(array(
				'usu_perfil <=' => 6,
				'usu_fk_empresa_socio' => $emp_ws
			));
			
		}else{
			$usuarios=$this->usu->count_by(array(
				'usu_perfil' => $this->post('perfil'),
				'fk_curso' => $cursof,
				'usu_estado !=' => 0	
			));
		}
	
		$cantusu=ceil($usuarios/15);

		if($this->post('perfil')==1){
			$data=$this->usu->limit(15,$ini)->order_by('usu_perfil','DESC')->GETusuarioAdmin();
			
		}else if($this->post('perfil')==4){
			$data=$this->usu->limit(15,$ini)->order_by('usu_perfil','ASC')->GETusuarioWs();
		}else if($this->post('perfil')==6){
			$data=$this->usu->limit(15,$ini)->order_by('usu_perfil','ASC')->get_many_by(array(
				'usu_perfil' => 4,
				'usu_fk_empresa_socio' => $emp_ws
			));
		}else{
			$data=$this->usu->limit(15,$ini)->order_by('usu_perfil','ASC')->get_many_by(array(
				'usu_perfil' => $this->post('perfil'),
				'fk_curso' => $cursof,
				'usu_estado !=' => 2	
			));
			
		}
	
=======
		$pag=$this->get('pagina');
		if(empty($pag)){
			$pag=1;
		   }
		$ini=($pag-1)*15;
		$usuarios=$this->usu->count_by(array(
			'usu_perfil !=' => 3
		));
		$cantusu=ceil($usuarios/15);
		$data=$this->usu->limit(15,$ini)->order_by('usu_telefono','DESC	')->get_many_by(array(
			//'usu_perfil !=' => 2 
		));
>>>>>>> ab578ea9d5681b2c3400b00408c2afaa99b93d60
		$resp['lista']=$data; 
		$resp['ok']=true;
		$resp['pag_actual']=$pag;
		$resp['cant_pag']=$cantusu;
<<<<<<< HEAD

=======
>>>>>>> ab578ea9d5681b2c3400b00408c2afaa99b93d60
		$this->response($resp);

	}

	function traerId_post(){
		$this->load->model('usuarios_model', 'usu');
		$id = $this->post('id');		
		$usuario = $this->usu->get_by(array(
			'usu_id' => $id
		));
		$data['ok'] = true;
		$data['usuario'] = $usuario;
		$this->response($data);
	}

	function filtrar_get(){
		$this->load->model('usuarios_model', 'usu');
		$param=$this->get();
		$where = array();	
		foreach($param as $w => $val){
			$flag=array_search($w,$this->campos);
			if($flag!==FALSE){
			  $where[$flag.' LIKE'] = '%' .$val.'%';
			}
		}
		$sd = $this->usu->filtrar($where);
	
		if(!empty($sd)){
		   $resp['ok'] = true;
           $resp['lista'] = $sd;
		}else{
		   $resp['ok'] = false;
           $resp['lista'] = $sd;
		}
		$this->response($resp);
	}

	/**
	 * POST
	 * {
	 *  "id"=>2, 
	 * "contrasena":"80147247"
	 * 
	 * }
	 */

	function olvidaste_contra_post(){
		$this->load->model('usuarios_model', 'usu');
		$email = $this->post('email');		
		$exist = $this->usu->count_by(array(
<<<<<<< HEAD
			'usu_email'=>$email,
			'usu_perfil'=> 2
		));
		
=======
			'usu_email'=>$email
		));
>>>>>>> ab578ea9d5681b2c3400b00408c2afaa99b93d60
		if($exist>0){
			$data['sucess'] = true;
			if($this->email_cambioPass($email)){
				$data['sucess'] = true;
				$data['mensaje'] = 'Hemos enviado un correo para que restablezcas tu contraseña.';
			}else{
				$data['sucess'] = false;
				$data['mensaje'] = 'Error al enviar el email intente mas tarde.' ;
			}
			
		}else{
			$data['sucess'] = false;
				$data['mensaje'] = 'El email ingresado no se encuentra registrado.' ;
		}	
		$this->response($data);
	}

	function email_cambioPass($email){
		$this->load->model('usuarios_model', 'usu');
		$usuario = $this->usu->get_by(array(
			'usu_email' => $email,
			'usu_perfil'=> 2
		));
	
<<<<<<< HEAD

		$mensaje = 'Cordial Saludo por favor dar click en el siguiente link para restablecer su contraseña, https://cityfitnessworld.com/#/resetpass/'.$usuario->usu_id.'';
		$cabecera='Bienvenido a City Fitness World para validar su cuenta por favor dar click al siguiente en enlace';
		$email  = $usuario->usu_email;
		$config['protocol'] = 'sendmail';
		$config['mailtype'] = 'html';
		$config['charset']  = 'utf-8';
		$config['newline']  = "\r\n";
		
        $this->email->clear(TRUE);
		$this->email->initialize($config);
		$this->email->set_mailtype("html");
		$this->email->from('cityfitnessworld.contacto@cityfitnessworld.com','City Fitness World');
		$this->email->to($email);
		$this->email->subject($cabecera);
=======
		$nombre = $usuario->usu_nombres;
		$mensaje = 'Cordial Saludo por favor dar click en el siguiente link para restablecer su contraseña, https://cityfitnessworld.com/#/resetpass/'.$usuario->usu_id.'';
		$email  = $usuario->usu_email;
		$this->email->from('contacto@cityfitnessworld.com');
		$this->email->to('contacto@cityfitnessworld.com');
		$this->email->subject('City Fitness World - Recupere su contraseña');
>>>>>>> ab578ea9d5681b2c3400b00408c2afaa99b93d60
		$this->email->message($mensaje);
		if($this->email->send()){
			return true;
		}else{
			return false;
		}
		
	}

	
}
