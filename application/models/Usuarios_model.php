<?php
class Usuarios_model extends MY_Model {
	
	protected $_table = 't_usuarios';
	function __construct(){
        // Call the Model constructor
        parent::__construct();
    }

    function filtrar($where){
		$this->db->select('*');
		$this->db->from('t_usuarios');
		$this->db->where($where); 
		$result = $this->db->get();
		return $result->result();
	}

	function AliadoId($id){
		$this->db->select("*");
		$this->db->from('t_usuarios');
		$this->db->join('t_planes', 't_usuarios.usu_id = t_planes.pla_fk_aliado');
		$this->db->where(array(
			't_usuarios.usu_id'=>$id
		)); 
		$result = $this->db->get();
		return $result->row();
	}

	function countusuarioWs(){
		$this->db->select('COUNT(*) AS `numrows`');
		$this->db->from('t_usuarios');
		$this->db->where(array(
			'usu_perfil'=>4
		));
		$this->db->or_where(array(
			'usu_perfil'=>6
		));
		$result = $this->db->get();
		return $result->row();
	}

	function GETusuarioAdmin(){
		$this->db->select('*');
		$this->db->from('t_usuarios');
		$this->db->where(array(
			'usu_perfil <='=>2
		));
		$this->db->or_where(array(
			'usu_perfil'=>6
		));
		$result = $this->db->get();  
	    return $result->result();
	}



	function GETuserscur($curso){
		$this->db->select('*');
		$this->db->from('t_usuarios');
		$this->db->where(array(
			'usu_perfil <='=>5,
			'fk_curso'=>$curso,
			
		));
		$result = $this->db->get();  
	    return $result->result();
	}
	

	function countusuarioAdmin(){
		$this->db->select('COUNT(*) AS `numrows`');
		$this->db->from('t_usuarios');
		$this->db->where(array(
			'usu_perfil <='=>2,
			'usu_estado !='=> 0
		));
		$this->db->or_where(array(
			'usu_perfil'=>6,
			'usu_estado !='=> 0
		));
		$result = $this->db->get();
		return $result->row();
	}


	function GETusuarioWs(){
		$this->db->select('*');
		$this->db->from('t_usuarios');
		$this->db->where(array(
			'usu_perfil'=>4
		));
		$this->db->or_where(array(
			'usu_perfil'=>6
		));
		$result = $this->db->get();
	    return $result->result();
	}

	function AliadoIXempesaws($id){
		$this->db->select("*");
		$this->db->from('t_usuarios');
		$this->db->join('empresas_ws', 'empresas_ws.id_emps = t_usuarios.usu_fk_empresa_socio');
		$this->db->join('t_socios', 't_socios.soc_id = empresas_ws.imps_fk_socio');
		$this->db->where(array(
			't_usuarios.usu_fk_empresa_socio'=>$id
		)); 
		$result = $this->db->get();
	    return $result->result();
	}
	


}
