<?php
class Infraction_model extends CI_Model {
		
	private $_tabla = 'infraction';


	public function getInfraction($codeInfraction)
    {
		$codeInfraction = strtoupper($codeInfraction);
        $query = $this->db->where('code', $codeInfraction)->get($this->_tabla);
        $rs = $query->result();
        if (!isset($rs[0])) {
            return false;
        } else {
            return $rs[0];
        }
    }

}
