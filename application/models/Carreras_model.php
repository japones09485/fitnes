<?php
class Carreras_model extends MY_Model {
	
	protected $_table = 't_carreras';
	function __construct(){
        // Call the Model constructor
        parent::__construct();
	}

	public function carrerasDisponibles($not_in) {
        $this->db->select('*')  // Selecciona todos los campos, puedes ajustar según tu necesidad
                 ->from('t_carreras')  // Cambia por el nombre real de tu tabla
                 ->where('estado', 1)  // Condición del estado
                 ->where_not_in('id', $not_in);  // Aplica la condición NOT IN

        $query = $this->db->get();  // Ejecuta la consulta
        return $query->result();  // Devuelve los resultados como array de objetos
    }

	public function carrerasAliado($idAliado) {

		$this->db->select('*');
		$this->db->from('t_carreras');
		$this->db->join('rel_instructor_carreras', 't_carreras.id = rel_instructor_carreras.fk_carrera');
		$this->db->where(array(
			'rel_instructor_carreras.fk_aliado'=>$idAliado
			
		
		)); 
		$this->db->order_by('t_carreras.nombre', 'ASC');
		$result = $this->db->get();
		return $result->result();
    }

	public function getNivelAlto($idAliado) {
		 // Selecciona todos los campos de las tablas t_carreras y rel_instructor_carreras
		 $this->db->select('sistema, nivel');
		 $this->db->from('t_carreras');
		 $this->db->join('rel_instructor_carreras', 't_carreras.id = rel_instructor_carreras.fk_carrera');
		 
		 // Condiciones para el ID del aliado y el sistema
		 $this->db->where('rel_instructor_carreras.fk_aliado', $idAliado);
		 $this->db->where('t_carreras.sistema', 'LAQF');
		 
		 // Ordenar por el campo 'nivel' de forma descendente
		 $this->db->order_by('nivel', 'DESC');
		 
		 // Limitar la consulta a un solo resultado
		 $this->db->limit(1);
		 
		 $query = $this->db->get();
		 
		 // Verificar si la consulta obtuvo resultados
		 if ($query->num_rows() > 0) {
			 return $query->row();
		 } else {
			 // Devolver un objeto con valores null para las columnas
			 return (object) ['sistema' => null, 'nivel' => null];
		 }
	}
	
	
	
}
