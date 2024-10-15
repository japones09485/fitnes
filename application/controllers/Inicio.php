<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Inicio extends CI_Controller {

    public function __construct() {
        parent::__construct();
       
    }

	function verifycodigo($codigo){
		$this->load->model('usuarios_model', 'usu');
		$exist=$this->usu->count_by(array(
			'usu_cod_verificacion'=>$codigo,
			'usu_estado_verificacion'=>1
		));
		if($exist==0){
			$this->usu->update_by(array(
				'usu_cod_verificacion'=>$codigo
			),array(
				'usu_estado_verificacion'=>1
			));
		}
		
		header('Location: https://cityfitnessworld.com');
	}

	function restab_contra($codigo){
		$this->load->model('usuarios_model', 'usu');
		$usuario=$this->usu->count_by(array(
			'usu_cod_contrasena'=>$codigo
		));
		if($usuario>0){
			$this->usu->update_by(array(
					'usu_cod_contrasena'=>$codigo),array(
					'usu_cod_contrasena'=>0	
					));
		}
		header('Location: https://cityfitnessworld.com');

	}   
}

?>
