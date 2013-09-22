<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Consult extends CI_Controller {
	
	public function index()
	{
		$this->load->library('placa');
		var_dump($this->placa->getData('004550'));
	}
	
	public function plate()
	{
		$this->load->model('Drive_model');
		$this->load->model('Infraction_model');
		$this->load->library('placa');
		
		if (isset($_POST['placa']) && $_POST['placa'] != "") {
			$placa = $_POST['placa'];
			$uploadfile = null;
		} elseif (isset($_FILES['avatar']['name']) && $_FILES['avatar']['name'] != "") {
			$extension = explode('.',$_FILES['avatar']['name']);
			$uploaddir = 'plates/';
			$file = basename($_FILES['avatar']['name']);
			$uploadfile = $uploaddir . substr(md5(uniqid(rand())),0,6) . $file;

			if (move_uploaded_file($_FILES['avatar']['tmp_name'], $uploadfile)) {
				$placa = $this->placa->getDataByImage($uploadfile);
			} else {
				$data["status"] = 'false';
				echo json_encode($data); exit;
			}
		} else {
			$data["status"] = 'false';
			echo json_encode($data); exit;
		}
		$uid = $_POST['uid'];
		$ubicacion = $_POST['ubicacion'];
		$ubicacion = explode(',', $ubicacion);
		$this->Drive_model->registerDrive(
			array(
				'user_id' => $uid,
				'num_placa' => $placa,
				'path_placa' => $uploadfile,
				'lat_placa' => $ubicacion[0],
				'long_placa' => $ubicacion[1]
			)
		);
		$dataPlaca = $this->placa->getData($placa);
		if (!isset($dataPlaca['result']) || count($dataPlaca['result']) != 2) {
			echo json_encode(array('status' => 'false', 'placa' => $placa)); 
			exit;
		}
		$sancionesPlaca = $this->placa->getSanciones($placa);
		$dataResult = array();
		$dataResult['status'] = 'true';
		$dataResult['placa'] = $dataPlaca['result'][1][0];
		$dataResult['tipoServicio'] = $dataPlaca['result'][1][1];
		$dataResult['tipoAuto'] = $dataPlaca['result'][1][2];
		$dataResult['marcaAuto'] = $dataPlaca['result'][1][5]. " (".$dataPlaca['result'][1][4].")";
		$dataResult['numeroPuertas'] = $dataPlaca['result'][1][19];
		$dataResult['serie'] = $dataPlaca['result'][1][6];
		$dataResult['anho'] = $dataPlaca['result'][1][3];
		$dataResult['capacidad'] = $dataPlaca['result'][1][15];
		$dataResult['numAsientos'] = $dataPlaca['result'][1][16];
		$dataResult['multas'] = array();
		
		if (isset($sancionesPlaca['result'])) {
			foreach ($sancionesPlaca['result'] as $i => $s) {
				if ($i > 0) {
					$dataInfraccion = $this->Infraction_model->getInfraction($s[1]);
					$dataResult['multas'][] = array(
						'descripcion' => $dataInfraccion->description, 
						'tipoInfraccion' => $dataInfraccion->level, 
						'monto' => $dataInfraccion->mount, 
						'ubicacion' => $s[15]." (".$s[16].")", 
						'nombre' => $s[10].", ".$s[11]
					);
				}
			}
		}
		echo json_encode($dataResult);
	}
}
