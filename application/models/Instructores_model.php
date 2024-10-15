<?php
class Instructores_model extends MY_Model {
	
	protected $_table = 't_instructores';
	function __construct(){
        // Call the Model constructor
        parent::__construct();
	}
	
	function infoId($profe){
		$this->db->select('*');
		$this->db->join('t_empresas', 't_empresas.emp_id = t_instructores.ins_fk_empresa'); 
		$this->db->where('t_instructores.ins_id',$profe); 
		$result = $this->db->get();
		return $result->row();
	}

	function traerporcliente($empresa){
		$this->db->select('*');
		$this->db->from('t_instructores');
		$this->db->join('t_empresas', 't_empresas.emp_id = t_instructores.ins_fk_empresa');
		$this->db->where(array(
			't_instructores.ins_fk_empresa'=>$empresa,
			't_instructores.ins_fk_perfil'=>1
		)); 
		$result = $this->db->get();
		return $result->result();
	}

	function traerporperfil($perfil){
		$this->db->select('*');
		$this->db->from('t_instructores');
		$this->db->join('t_empresas', 't_empresas.emp_id = t_instructores.ins_fk_empresa');
		$this->db->where('t_instructores.ins_fk_perfil',$perfil); 
		$result = $this->db->get();
		return $result->result();
	}

	function cursosintructor($instructor){
		$this->db->select('*');
		$this->db->from('rel_intruc_cursos');
		$this->db->join('t_cursos', 't_cursos.cur_id = rel_intruc_cursos.rel_fk_curso');
		$this->db->join('t_empresas', 't_empresas.emp_id = rel_intruc_cursos.rel_fk_empresa');
		$this->db->where(array(
			'rel_intruc_cursos.rel_fk_instructor'=>$instructor
		)); 
		$result = $this->db->get();
		return $result->result();
	}
   
	function filtrar($where){
		$this->db->select(" t_instructores.* , CONCAT(ins_nombre, ' ', ins_apellido) AS name_completo");
		$this->db->from('t_instructores');
		$this->db->join('t_empresas', 't_empresas.emp_id = t_instructores.ins_fk_empresa');
		$this->db->where($where); 
		$result = $this->db->get();
		return $result->result();
	}

	function cant_like($id_ins){
		$this->db->select("ins_likes");
		$this->db->from('t_instructores');
		$this->db->where(array(
			'ins_id'=>$id_ins
		)); 
		$result = $this->db->get();
		return $result->row();
	}

	function gimnasiosinstructor($id_ins){
		$this->db->select("t_gimnasios.*");
		$this->db->from('rel_gimnasio_instructores');
		$this->db->join('t_gimnasios', 't_gimnasios.gim_id = rel_gimnasio_instructores.rel_fk_gimnasio');
		$this->db->join('t_instructores', 't_instructores.ins_id = rel_gimnasio_instructores.rel_fk_instructor');
		$this->db->where(array(
			't_instructores.ins_id'=>$id_ins
		)); 
		$result = $this->db->get();
		return $result->result();
	}
	
}
