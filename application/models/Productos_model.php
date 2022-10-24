<?php
class Productos_model extends MY_Model {
	
	protected $_table = 't_productos';
	function __construct(){
        // Call the Model constructor
        parent::__construct();
	}

	function get_Activos(){
		$this->db->select('*');
		$this->db->from('t_productos');
		$this->db->join('t_usuarios', 't_productos.pro_fk_usuario = t_usuarios.usu_id');
		$this->db->where(array(
			't_productos.pro_estado'=>1,
		)); 
		$result = $this->db->get();
		return $result->result();	
	}

	function get_All(){
		$this->db->select('*');
		$this->db->from('t_productos');
		$this->db->join('t_usuarios', 't_productos.pro_fk_usuario = t_usuarios.usu_id');
		$result = $this->db->get();
		return $result->result();	
	}

	function traerId($id){
		$this->db->select('*');
		$this->db->from('t_productos');
		$this->db->join('t_usuarios', 't_productos.pro_fk_usuario = t_usuarios.usu_id');
		$this->db->where(array(
			't_productos.pro_id'=>$id,
		)); 
		$result = $this->db->get();
		return $result->row();
	}

	function misProductos($id){
		$this->db->select('*');
		$this->db->from('t_productos');
		$this->db->join('t_usuarios', 't_productos.pro_fk_usuario = t_usuarios.usu_id');
		$this->db->where(array(
			't_productos.pro_fk_usuario'=>$id,
		)); 
		$result = $this->db->get();
		return $result->row();
	}

	function filtrar($where){
		$this->db->select('*');
		$this->db->from('t_productos');
		$this->db->join('t_usuarios', 't_productos.pro_fk_usuario = t_usuarios.usu_id');
		$this->db->where($where); 
		$result = $this->db->get();
		return $result->result();
	}

	

}
