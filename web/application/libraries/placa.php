<?php
class Placa
{
	private $_key = "2fd321efa3362e525a659f80d8e569a91b0e5975";
	private $_guid = "";
	private $_url = "http://api.lima.datosabiertos.pe/datastreams/invoke/";
	private $_pathAnpr = "";
	private $CI;
	
	public function __construct() {
		$this->CI =& get_instance();
		$this->CI->load->model('Service_model');
	}
	
	public function getData($placa) {
		$placa = strtoupper($placa);
		$evalModel = $this->CI->Service_model->getDataVehicle($placa);
		if ($evalModel) {
			return $evalModel;
		} else {
			return array();
		}
		$this->_guid = "DIREC-DE-VEHIC-2013";
		$response = file_get_contents($this->_url.$this->_guid."?auth_key=".$this->_key."&output=json_array&filter0=column0[contains]".$placa);
		return @json_decode($response, true);
	}
	
	public function getSanciones($placa) {
		$placa = strtoupper($placa);
		$evalModel = $this->CI->Service_model->getVehiclesInfraction($placa);
		if ($evalModel) {
			return $evalModel;
		} else {
			return array();
		}
		$this->_guid = "DIREC-DE-SANCI-2007-2010";
		$response = file_get_contents($this->_url.$this->_guid."?auth_key=".$this->_key."&output=json_array&filter0=column0[contains]".$placa);
		return @json_decode($response, true);
	}
	
	public function getDataByImage($path) {
		$result = shell_exec("java -jar javaanpr.jar -recognize -i $path");
		return trim($result, "\t\n\r\0\x0B");
	}
}
