<?php
class Service_model extends CI_Model {

	public function getDataVehicle($placa) {
		$placa = strtoupper($placa);
        $query = $this->db->where('placa', $placa)->get('vehicles');
        $rs = $query->result_array();
        if (!isset($rs[0])) {
            return false;
        } else {
			$result['result'][0] = array();
			$result['result'][1] = array();
			$keys = array_keys($rs[0]);
			foreach ($keys as $k) {
				$result['result'][1][] = $rs[0][$k];
			}
            return $result;
        }
	}
	
	public function getVehiclesInfraction($placa) {
		$placa = strtoupper($placa);
        $query = $this->db->where('placa', $placa)->get('vehicle_infraction');
        $rs = $query->result_array();
        if (!isset($rs[0])) {
            return false;
        } else {
            $result['result'][0] = array();
			$result['result'][1] = array();
			$keys = array_keys($rs[0]);
			foreach ($keys as $k) {
				$result['result'][1][] = $rs[0][$k];
			}
            return $result;
        }
	}
}
