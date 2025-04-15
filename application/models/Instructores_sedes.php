<?php
class Instructores_sedes extends MY_Model {
	
	protected $_table = 't_instructores_sede';
	function __construct(){
        // Call the Model constructor
        parent::__construct();
	}

	public function get_ins_with_instructor($sede, $data)
	{
		
		$this->db->select('*');
		$this->db->from('t_instructores_sede');
		$this->db->join('t_instructores', 't_instructores_sede.fk_instructor = t_instructores.ins_id');
		$this->db->where('t_instructores_sede.fk_sede', $sede);
		$this->db->where('t_instructores_sede.fk_instructor', $data->instructor);
		$this->db->where('t_instructores_sede.tipo', $data->tipo);
		
		$query = $this->db->get();
		return $query->result(); // Devuelve los resultados como un array de objetos
	}

	public function get_instructorSede($sede)
	{
	
		$this->db->select('*, CONCAT(t_instructores.ins_nombre, " ", t_instructores.ins_apellido) AS nombre_completo');
		$this->db->from('t_instructores_sede');
		$this->db->join('t_instructores', 't_instructores_sede.fk_instructor = t_instructores.ins_id');
		$this->db->where('t_instructores_sede.fk_sede', $sede);
		
		$query = $this->db->get();
		return $query->result(); // Devuelve los resultados como un array de objetos
	}
}
