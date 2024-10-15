<?php
class Likes_model extends MY_Model {
	
	protected $_table = 't_likes';
	function __construct(){
        // Call the Model constructor
        parent::__construct();
	}

	function instructoresporgim($gimnasio){
		$this->db->select('*');
		$this->db->from('t_instructores');
		$this->db->join('rel_gimnasio_instructores', 'rel_gimnasio_instructores.rel_fk_instructor = t_instructores.ins_id');
		$this->db->where(array(
			'rel_like_intructores.rel_fk_gimnasio'=>$gimnasio,
		
		)); 
		$result = $this->db->get();
		return $result->result();
	}

	
	function likeporinstructor($instructor){
		$this->db->select('*');
		$this->db->from('t_likes');
		$this->db->join('t_usuarios', 't_usuarios.usu_id = t_likes.like_fk_usuario');
		$this->db->where(array(
			't_likes.like_fk_idactor'=>$instructor,
			't_likes.like_tipo'=>1
		
		)); 
		$result = $this->db->get();
		return $result->result();
	}

	function likeporgimnasio($gimnasio){
		$this->db->select('*');
		$this->db->from('t_likes');
		$this->db->join('t_usuarios', 't_usuarios.usu_id = t_likes.like_fk_usuario');
		$this->db->where(array(
			't_likes.like_fk_idactor'=>$gimnasio,
			't_likes.like_tipo'=>2
		
		)); 
		$result = $this->db->get();
		return $result->result();
	}
	
}
