 <?php
class Clases_model extends MY_Model {
	
	protected $_table = 't_clases';
	function __construct(){
        // Call the Model constructor
        parent::__construct();
	}
	
	function infoId($curso){
		$this->db->select('*');
		$this->db->from('t_clases');
		$this->db->join('t_cursos', 't_cursos.cur_id = t_clases.clas_fk_curso');
		$this->db->join('t_instructores', 't_clases.clas_fk_instructor = t_instructores.ins_id', 'left');
		$this->db->where('t_clases.clas_id',$curso); 
		$result = $this->db->get();
		return $result->row();
	}

	function allclases(){
		$this->db->select('*');
		$this->db->from('t_clases');
		$this->db->join('t_cursos', 't_cursos.cur_id = t_clases.clas_fk_curso');
		$this->db->join('t_instructores', 't_clases.clas_fk_instructor = t_instructores.ins_id', 'left');
		$result = $this->db->get();
		return $result->result();
	}

	function all_activos(){
		$this->db->select('*');
		$this->db->from('t_clases');
		$this->db->join('t_cursos', 't_cursos.cur_id = t_clases.clas_fk_curso');	
		$this->db->join('t_instructores', 't_clases.clas_fk_instructor = t_instructores.ins_id', 'left');
		$result = $this->db->get();
		return $result->result();
	}

	function traerporcurso($curso){
		$this->db->select('*');
		$this->db->from('t_clases');
		$this->db->join('t_cursos', 't_cursos.cur_id = t_clases.clas_fk_curso');	
		$this->db->join('t_instructores', 't_clases.clas_fk_instructor = t_instructores.ins_id', 'left');
		$this->db->where(array('clas_fk_curso'=>$curso,
								'clas_estado !='=>2,
								'clas_fecha_inicio >='=>date('y-m-d')					
	     ));
		$result = $this->db->get();
		return $result->result();
	}

	function clasesporinstructor($instructore){
		$this->db->select('*');
		$this->db->from('rel_intruc_cursos');
		$this->db->join('t_cursos', 't_cursos.cur_id = rel_intruc_cursos.rel_fk_curso');
		$this->db->join('t_instructores', 't_instructores.ins_id = rel_intruc_cursos.rel_fk_instructor');
		$this->db->where(array(
			'rel_fk_instructor'=>$instructore
		));	
		$result = $this->db->get();
		return $result->result();
	}

	function filtrar($where){
		$this->db->select('*');
		$this->db->from('t_clases');
		$this->db->join('t_cursos', 't_cursos.cur_id = t_clases.clas_fk_curso');
		$this->db->join('t_instructores', 't_clases.clas_fk_instructor = t_instructores.ins_id', 'left');
		$this->db->where($where); 
		$result = $this->db->get();
		return $result->result();
	}

}
