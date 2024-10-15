<?php
class Cursos_model extends MY_Model {
	
	protected $_table = 't_cursos';
	function __construct(){
        // Call the Model constructor
        parent::__construct();
	}
	
	function cursosempresa($empresa){
		$query = "SELECT t_cursos.cur_orden,t_cursos.*
		FROM t_empresas
		JOIN t_cursos ON t_empresas.emp_id = t_cursos.cur_fk_empresa
		WHERE t_cursos.cur_fk_empresa = $empresa
		AND t_cursos.cur_estado != 2 
		ORDER BY t_cursos.cur_orden IS NULL, t_cursos.cur_orden ASC";
		$resultados = $this->db->query($query);
		return $resultados->result();
	}

	function allporId($id){
		$this->db->select('*');
		$this->db->from('t_empresas');
		$this->db->join('t_cursos', 't_empresas.emp_id = t_cursos.cur_fk_empresa');
		$this->db->where('t_cursos.cur_id',$id);
		$result = $this->db->get();
		return $result->row();
	  }

	  function filtrar($where){
		$this->db->select('*');
		$this->db->from('t_cursos');
		$this->db->join('t_empresas', 't_empresas.emp_id = t_cursos.cur_fk_empresa');
		$this->db->where($where);
		$result = $this->db->get();
		return $result->result();
	  }

	  function allcursos(){

		$query = "SELECT *
		FROM t_cursos
		ORDER BY t_cursos.cur_orden IS NULL, t_cursos.cur_orden ASC";
		$resultados = $this->db->query($query);
		return $resultados->result();

	  }
}
