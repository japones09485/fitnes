<?php
class Trabajos_alumnos_model extends MY_Model {
	
	protected $_table = 'rel_trabajos_alumnos';
	function __construct(){
        // Call the Model constructor
        parent::__construct();
	}

	function RespuestasTrabajo($id){
		$this->db->select("t_trabajos.id as id_trabajo , t_trabajos.nombre as nombre_trabajo,t_usuarios.usu_nombres as alumno,rel_trabajos_alumnos.comentario as comentario ,rel_trabajos_alumnos.link_respuesta as link, rel_trabajos_alumnos.fecha as fecha, rel_trabajos_alumnos.ruta_arch as ruta");
		$this->db->from('rel_trabajos_alumnos');
		$this->db->join('t_trabajos', 't_trabajos.id = rel_trabajos_alumnos.fk_trabajo');
		$this->db->join('t_usuarios', 't_usuarios.usu_id = rel_trabajos_alumnos.fk_alumno');
		$this->db->where(array(
			'rel_trabajos_alumnos.fk_trabajo'=>$id
		)); 
		$result = $this->db->get();
		return $result->result();	
	}

	
}
