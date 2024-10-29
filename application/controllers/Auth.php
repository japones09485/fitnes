<?php

defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
require(APPPATH.'libraries/Rest_Controller.php');
require(APPPATH.'libraries/Format.php');

class Auth extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();
		header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: X-API-KEY, Authorization,Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

    }

    public function jwt_login_post()
    {
        $this->load->model('Usuarios_model', 'u');
		$this->load->model('instructores_model','ins');
		
        $usuario = $this->post('usuario');
        $data = array();
        $contrasenia = $this->post('contrasenia');
        $perfil = $this->post('perfil');

		if(!$perfil){
			$perfil =2;
		}

		
		
        $cont_user = $this->u->count_by(array(
            'usu_email' => $usuario,
            'usu_estado_verificacion'=>1,
            'usu_textoclaro' => $contrasenia,
			'usu_perfil'=>$perfil
        ));

		
        if($cont_user>0){
            $user = $this->u->get_by(array(
                'usu_email' => $usuario,
                'usu_estado_verificacion'=>1,
                'usu_perfil'=>$perfil
            ));

			//validamos que exita instructor con el mismo correo
			
			$instructor = $this->ins->get_by(array(
				'ins_correo'=>$usuario
			));

          
            if($user->usu_estado == 0){
                $data['status'] = false;
                $data['mensaje'] = 'Usuario Inactivo por favor ponerse en contacto con contacto@cityfitnessworld.com';

            }else{
                $data['usu_id'] = $user->usu_id;
                $data['usu_nombres'] = $user->usu_nombres;
                $data['usu_apellidos'] = $user->usu_apellidos;
                $data['usu_email'] = $user->usu_email;
                $data['usu_perfil'] = $user->usu_perfil;
                $data['usu_email'] = $user->usu_email;
                $data['usu_pais'] = $user->usu_pais;
				$data['usu_fk_socio'] = $user->usu_fk_socio;
				$data['usu_fk_empresa_socio'] = $user->usu_fk_empresa_socio;
                $passSYSTEM = md5($contrasenia);
                $passSYSTEM = md5($contrasenia);
                if (is_object($user) and $user->usu_password === $passSYSTEM) {
                    unset($user->usu_passw);
                    $creatorJWT = new CreatorJWT();
                    $tokenData['uniqueId'] = $user->usu_id;
                    $tokenData['nombre'] = $user->usu_nombres . ' ' . $user->usu_apellidos;
                    $tokenData['perfil'] = $user->usu_perfil;
                    $tokenData['timeStamp'] = Date('Y-m-d h:i:s');
                    $jwtToken = $creatorJWT->GenerateToken($tokenData);
                    $data['status'] = true;
                    $data['mensaje'] = '';
                    $data['token'] = $jwtToken;
                    $data['user'] = $data;
					$data['instructor'] =$instructor;
                    
                } else {
                    $data['status'] = false;
                    $data['mensaje'] = 'Credenciales Incorrectas';
                }
            }

        }else{
            $data['status'] = false;
            $data['mensaje'] = 'Credenciales Incorrectas';
        }
        $this->response($data);
    }

    public function verifyToken_get()
    {
        $creatorJWT = new CreatorJWT();
        $received_Token = $this->input->request_headers();
      
        if (!isset($received_Token['X-API-KEY'])) {
            http_response_code('401');
            $this->response(array('status' => false, "message" => 'NO ESTA'));
            exit;
        }

        try {
            $rkAr = explode(' ', $received_Token['X-API-KEY']);
            $jwtData = $creatorJWT->DecodeToken($rkAr[1]);
            $this->response(array(
                'status' => true,
                'data' => $jwtData
            ));
        } catch (Exception $e) {
            http_response_code('401');
            $this->response(array('status' => false, "message" => $e->getMessage()));
            exit;
        }
    }

    public function ressetPassword_post(){
        $this->load->model('Usuarios_model', 'usu');
        $datos = $this->post(); 
        $passw=md5($datos['password']);
        $this->usu->update_by(array(
            'usu_id'=>$datos['id']
        ),array(
            'usu_textoclaro'=>$datos['password'],
            'usu_password'=>$passw,
            'usu_estado_verificacion'=>1
        ));
        $res['mensaje'] = 'Contraseña actualizada correctamente';
        $res['response'] = true;
        $this->response($res);
    }

    public function getPaises_get(){
	
		$this->load->model('Paises_model','pa');
		$paisesaux = $this->pa->get_all();
        $paises = array();
        foreach($paisesaux as $k => $value){
            $paises[$value->iso]= $value;
        }
		$data['lista'] = $paises;
        $data['ok'] = true;
        $this->response($data);
		
	}

    public function getPaisesList_get(){
	
	
		$this->load->model('Paises_model','pa');
		$paisesaux = $this->pa->get_all();
        $paises = array();
		$data['lista'] = $paisesaux;
        $data['ok'] = true;
        $this->response($data);
		
	}


    
}
