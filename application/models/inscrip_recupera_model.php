<?php
class Inscrip_recupera_model extends MY_Model {
	
	protected $_table = 'alum_examen_recupe';
	function __construct(){
        // Call the Model constructor
        parent::__construct();
	}

	
	function alum_recuperacion($id_examen){
		$this->db->select("rec.id_recupera AS id,exa.nombre as nombre_examen,concat(alum.usu_nombres,' ',alum.usu_apellidos) AS alumno , concat(aliado.usu_nombres,' ',aliado.usu_apellidos) AS aliado , alum.usu_email AS email , rec.fecha_inscrip AS fecha");
		$this->db->from('alum_examen_recupe as rec');
		$this->db->join('t_usuarios AS alum', 'alum.usu_id = rec.fk_alumno');
		$this->db->join('t_usuarios as aliado', 'aliado.usu_id = rec.fk_aliado ');
		$this->db->join('t_examenes AS exa', 'exa.id_examen =  rec.fk_examen');
		$this->db->where(array(
			'rec.fk_examen'=>$id_examen,
		
		)); 
		$result = $this->db->get();
		return $result->result();
	}
	
}
