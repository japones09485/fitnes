<?php
class Examenes_model extends MY_Model {
	
	protected $_table = 't_examenes';
	function __construct(){
        // Call the Model constructor
        parent::__construct();
	}

	function get_Activos(){
		$this->db->select('*');
		$this->db->from('t_examenes');
		$this->db->join('t_usuarios', 't_examenes.usuario_creacion = t_usuarios.usu_id');
		$this->db->where(array(
			't_examenes.id_examen'=>1,
		)); 
		$result = $this->db->get();
		return $result->result();	
	}

	function get_All(){
		$this->db->select('*');
		$this->db->from('t_examenes');
		$this->db->join('t_usuarios', 't_examenes.usuario_creacion = t_usuarios.usu_id');
		$result = $this->db->get();
		return $result->result();	
	}

	function traerId($id){
		$this->db->select('*');
		$this->db->from('t_examenes');
		$this->db->join('t_usuarios', 't_examenes.usuario_creacion = t_usuarios.usu_id');
		$this->db->where(array(
			't_examenes.id_examen'=>$id,
		)); 
		$result = $this->db->get();
		return $result->row();
	}

	function resultados_examenes($id_examen){
		$this->db->select('t_examenes.id_examen,t_presentacion_examen.id_presentacion as presentacion,CONCAT(t_usuarios.usu_nombres,t_usuarios.usu_apellidos) AS alumno,t_presentacion_examen.estado AS estado,t_presentacion_examen.fecha_inicio AS fecha_inicio,t_presentacion_examen.fecha_finalizacion AS fecha_final,t_presentacion_examen.fecha_fin AS fecha_plazo,t_presentacion_examen.fecha_caducado AS fecha_caducidad');
		$this->db->from('t_examenes');
		$this->db->join('t_presentacion_examen', ' t_examenes.id_examen = t_presentacion_examen.fk_examen');
		$this->db->join('t_usuarios', ' t_usuarios.usu_id = t_presentacion_examen.fk_alumno');
		$this->db->where(array(
			't_examenes.id_examen'=>$id_examen,
			
		)); 

		$result = $this->db->get();
		return $result->result();
	}

	function filtrar($where){
		$this->db->select('*');
		$this->db->from('t_examenes');
		$this->db->join('t_usuarios', 't_examenes.usuario_creacion = t_usuarios.usu_id');
		$this->db->where($where); 
		$result = $this->db->get();
		return $result->result();
	}

	function info_examen($id_pres){
		$this->db->select("CONCAT (t_usuarios.usu_nombres,' ',t_usuarios.usu_apellidos) AS alumno , t_examenes.nombre AS examen,t_presentacion_examen.fecha_inicio AS inicio , t_presentacion_examen.fecha_fin AS fecha_limite, t_presentacion_examen.fecha_finalizacion AS fecha_finalizacion, t_resultados_examen.resul_nota AS nota , result_cuantitativa AS cuantitativa");
		$this->db->from('t_presentacion_examen');
		$this->db->join('t_examenes', 't_examenes.id_examen = t_presentacion_examen.fk_examen');
		$this->db->join('t_resultados_examen', 't_resultados_examen.resul_fk_presen = t_presentacion_examen.id_presentacion');
		$this->db->join('t_usuarios', 't_usuarios.usu_id = t_presentacion_examen.fk_alumno');
		$this->db->where(array(
			't_presentacion_examen.id_presentacion' => $id_pres
		)); 
		$result = $this->db->get();
		return $result->result();
	}

	function preg_presen($id_pres){
		$this->db->select('t_preguntas.*');
		$this->db->from('t_presentacion_examen');
		$this->db->join('t_preguntas', 't_preguntas.fk_examen = t_presentacion_examen.fk_examen');
		$this->db->where(array(
			't_presentacion_examen.id_presentacion' => $id_pres
		)); 
		$result = $this->db->get();
		return $result->result();
	}

	

}
