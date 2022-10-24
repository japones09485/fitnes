<?php
class Gimnasios_model extends MY_Model {
	
	protected $_table = 't_gimnasios ';
	function __construct(){
        // Call the Model constructor
        parent::__construct();
	}
	
	function instructoresporgim($gimnasio){
		$this->db->select('*');
		$this->db->from('rel_gimnasio_instructores');
		$this->db->join('t_gimnasios', 't_gimnasios.gim_id = rel_gimnasio_instructores.rel_fk_gimnasio');
		$this->db->join('t_instructores','t_instructores.ins_id=rel_gimnasio_instructores.rel_fk_instructor');
		$this->db->where(array(
			'rel_gimnasio_instructores.rel_fk_gimnasio'=>$gimnasio,
		)); 
		$result = $this->db->get();
		return $result->result();
	}

	function filtrar($where){
		$this->db->select("*");
		$this->db->from('t_gimnasios');
		$this->db->join('rel_gimnasio_instructores', 'rel_gimnasio_instructores.rel_fk_gimnasio = t_gimnasios.gim_id');
		$this->db->where($where); 
		$result = $this->db->get();
		return $result->result();
	}

	function cant_like($id_gim){
		$this->db->select("gim_likes");
		$this->db->from('t_gimnasios');
		$this->db->where(array(
			'gim_id'=>$id_gim
		)); 
		$result = $this->db->get();
		return $result->row();
	}


}
