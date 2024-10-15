<?php
class Rel_Alum_Instructor_model extends MY_Model {
	
	protected $_table = 'rel_alumnos_aliado ';
	function __construct(){
        // Call the Model constructor
        parent::__construct();
	}

	function AlumnoInscrito($alumno, $aliado){
		$this->db->select("usu_id as id,usu_nombres as nombres,usu_apellidos as apellidos,usu_email as email,usu_pais as pais,usu_facebook as facebook,usu_instagram as instagram,rel_alum_fecha_inscripcion as fecha_inicio,rel_alum_fecha_fin as fecha_fin");
		$this->db->from('t_usuarios');
		$this->db->join('rel_alumnos_aliado', 't_usuarios.usu_id = rel_alumnos_aliado.rel_alum_fk_alumno');
		$this->db->where(array(
			't_usuarios.usu_id'=>$alumno,
			'rel_alumnos_aliado.rel_alum_fk_aliado'=>$aliado
		)); 
		$result = $this->db->get();
		return $result->row();
	}

	
	function ListadoAlumnos($aliado){
		$this->db->select("rel_alum_id as id_reg,rel_alum_fk_alumno as alumno,rel_alum_fk_aliado as aliado,usu_id as id,usu_nombres as nombres,usu_apellidos as apellidos,usu_email as email,usu_pais as pais,usu_facebook as facebook,usu_instagram as instagram,rel_alum_fecha_inscripcion as fecha_inicio,rel_alum_fecha_fin as fecha_fin");
		$this->db->from('t_usuarios');
		$this->db->join('rel_alumnos_aliado', 't_usuarios.usu_id = rel_alumnos_aliado.rel_alum_fk_alumno');
		$this->db->where(array(
			'rel_alumnos_aliado.rel_alum_fk_aliado'=>$aliado
		)); 
		$result = $this->db->get();
		return $result->result();
	}

	function AlumnosActivos($fecha_inicial, $fecha_fin, $aliado){
		$this->db->select("usu_nombres as nombres,usu_apellidos as apellidos,usu_email as email,rel_alum_fecha_inscripcion as fch_ini, rel_alum_fecha_fin as fch_fin");
		$this->db->from('t_usuarios');
		$this->db->join('rel_alumnos_aliado', 't_usuarios.usu_id = rel_alumnos_aliado.rel_alum_fk_alumno');
		$this->db->where(array(
			'rel_alumnos_aliado.rel_alum_fk_aliado'=>$aliado,
			'rel_alumnos_aliado.rel_alum_fecha_fin >=' =>$fecha_inicial,
			'rel_alumnos_aliado.rel_alum_fecha_fin <=' => $fecha_fin
		)); 
		$result = $this->db->get();
		return $result->result();
	}

	function DatosCsv($socio,$empresa,$fchIni,$fchFin){
		$query = "SELECT so.soc_nombre AS Socio , em.imps_nombre AS Empresa , CONCAT(usu.usu_nombres,' ',usu.usu_apellidos) AS Aliado , CONCAT(dal.usu_nombres ,' ', dal.usu_apellidos) AS Alumno , dal.usu_email AS Correo , alum.rel_alum_fecha_inscripcion AS Fecha_Inicio , alum.rel_alum_fecha_fin as Fecha_Fin
		FROM t_socios  AS so
		JOIN empresas_ws AS em ON so.soc_id = em.imps_fk_socio
		JOIN t_usuarios AS usu ON em.id_emps = usu.usu_fk_empresa_socio
		JOIN rel_alumnos_aliado AS alum ON usu.usu_id = alum.rel_alum_fk_aliado
		JOIN t_usuarios AS dal ON alum.rel_alum_fk_alumno = dal.usu_id
		WHERE so.soc_id = ".$socio." AND em.id_emps = ".$empresa." AND (alum.rel_alum_fecha_inscripcion>= '".$fchIni."' AND alum.rel_alum_fecha_inscripcion<= '".$fchFin."' ) OR (alum.rel_alum_fecha_fin <= '".$fchFin."' AND alum.rel_alum_fecha_fin >= '".$fchIni."')";
    $resultados = $this->db->query($query);
    return $resultados->result();
	}
}
