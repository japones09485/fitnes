<?php
class Empresas_socios_model extends MY_Model {
	
	protected $_table = 'empresas_ws';
	function __construct(){
        // Call the Model constructor
        parent::__construct();
	}

	function empresasxusu($usu){
		$this->db->select('*');
		$this->db->from('t_usuarios');
		$this->db->join('t_socios', 't_usuarios.usu_fk_socio = t_socios.soc_id');
		$this->db->join('empresas_ws', 'empresas_ws.imps_fk_socio = t_socios.soc_id');
		$this->db->where('t_socios`.`soc_id',$usu); 
		$result = $this->db->get();
	    return $result->result();
	}

	function traerempresasXcodigo($codigo){
		$this->db->select('*');
		$this->db->from('t_socios');
		$this->db->join('empresas_ws', 'empresas_ws.imps_fk_socio = t_socios.soc_id');
		$this->db->where('t_socios`.`soc_codigo',$codigo); 
		$result = $this->db->get();
	    return $result->result();
	}


	
}
