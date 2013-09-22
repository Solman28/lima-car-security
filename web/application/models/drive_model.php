<?php
class Drive_model extends CI_Model {
		
	private $_tabla = 'drive';

	public function registerDrive($data) {
		$this->db->insert(
            $this->_tabla,
            array(
                'user_id' => $data['user_id'],
                'num_placa' => $data['num_placa'],
                'path_placa' => $data['path_placa'],
                'lat_placa' => $data['lat_placa'],
                'long_placa' => $data['long_placa'],
                'date' => date('Y-m-d H:i:s')
            )
        );
        return $this->db->insert_id();
	}
	
	public function getDriver($idUser) {
		$query = $this->db->where('user_id', $idUser)
			->join('vehicles', 'vehicles.placa = drive.num_placa', 'left')
			->order_by('id', 'desc')
			->get($this->_tabla);
        return $query->result();
	}
	
	public function getNewDrivers($idUser, $lastId) {
		$query = $this->db->where('user_id', $idUser)
			->where('id >', $lastId)
			->join('vehicles', 'vehicles.placa = drive.num_placa', 'left')
			->order_by('id', 'desc')
			->get($this->_tabla);
        return $query->result();
	}
}
