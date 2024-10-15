<?php
class Respuestas_alum_model extends MY_Model {
	
	protected $_table = 't_respuestas_alumno';
	function __construct(){
        // Call the Model constructor
        parent::__construct();
	}

	public function result_pdf($id_presentacion) {
		// Construimos la consulta SQL
		$sql = "
			SELECT 
				CONCAT(usu.usu_nombres, ' ', usu.usu_apellidos) AS nombre, 
				exa.nombre AS nombre_examen,
				ra.fk_pregunta,
				p.enunciado AS enunciado_pregunta,
				tp.descripcion AS tipo_pregunta,
				r.texto_respuesta AS respuesta_elegida,
				CASE 
					WHEN ra.fk_tipo_pregunta = 1 THEN rc.texto_respuesta
					WHEN ra.fk_tipo_pregunta = 2 THEN ra.respuesta
				END AS respuesta_correcta,
				CASE 
					WHEN ra.result_respuesta = 1 THEN 'CORRECTO'
					WHEN ra.result_respuesta = 0 THEN 'INCORRECTO'
				END AS resultado
			FROM  
				t_respuestas_alumno AS ra
			INNER JOIN 
				t_preguntas AS p ON p.id_pregunta = ra.fk_pregunta
			INNER JOIN 
				t_examenes AS exa ON exa.id_examen = ra.fk_examen
			INNER JOIN 
				t_respuestas AS r ON ra.fk_pregunta = r.fk_pregunta AND ra.respuesta = r.orden
			INNER JOIN 
				t_respuestas AS rc ON ra.fk_pregunta = rc.fk_pregunta AND ra.respuesta_true = rc.orden
			INNER JOIN 
				tipo_pregunta AS tp ON tp.id = ra.fk_tipo_pregunta
			INNER JOIN 
				t_usuarios AS usu ON ra.fk_alumno = usu.usu_id
			WHERE 
				ra.fk_presentacion = $id_presentacion
		";
		
		// Ejecutamos la consulta
		$query = $this->db->query($sql, array($id_presentacion));
	
		// Retornamos los resultados
		return $query->result();
	}
	
	

}
