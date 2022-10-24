<?php
class Pagos_model extends MY_Model {
	
	protected $_table = 't_pagos';
	function __construct(){
        // Call the Model constructor
        parent::__construct();
	}

	function all_pagos($id_pag){
		$this->db->select("*");
		$this->db->from('t_pagos');
		$this->db->join('t_usuarios', 't_usuarios.usu_id = t_pagos.pag_fk_usuario');
		$this->db->where(array(
			'pag_id'=>$id_pag
		)); 
		$result = $this->db->get();
		return $result->row();
	}

}
