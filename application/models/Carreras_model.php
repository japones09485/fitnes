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
		$result = $this->db->get();
		return $result->result();
    }
	
	
}
