<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require 'application/libraries/facebook.php';

class Welcome extends CI_Controller {
	
	private $_appId = "409929075775547";
	private $_secret = "33a9d118b1d98959fcfba7bc7481d425";
	private $_facebook;
	private $_user;
	
	public function __construct() {
		$this->_facebook = new Facebook(array(
			'appId'  => $this->_appId,
			'secret' => $this->_secret
		));
		parent::__construct();
		$this->_user = $this->session->userdata('user');
	}
	
	public function index()
	{
		if ($this->_user) {
			redirect(base_url()."dashboard");
		}
		$user = $this->_facebook->getUser();
		if ($user) {
			try {
				$user_profile = $this->_facebook->api('/me');
				$this->session->set_userdata(
					'user', 
					array(
						'id' => $user_profile['id'], 
						'name' => $user_profile['name']
					)
				);
				redirect(base_url()."dashboard");
			} catch (FacebookApiException $e) {
				$view['loginUrl'] = $this->_facebook->getLoginUrl();
			}
		} else {
			$view['loginUrl'] = $this->_facebook->getLoginUrl();
		}
		$view['user'] = $user;
		$this->load->view('welcome_message', $view);
	}
	
	public function dashboard()
	{
		if (!$this->_user) {
			redirect(base_url());
		}
		$this->load->model('Drive_model');
		$drivers = $this->Drive_model->getDriver($this->_user['id']);
		$positions = array();
		foreach ($drivers as $d) {
			$display = "";
			$display .= "<b>Marca:</b> ".$d->nombre_marca."<br />";
			$display .= "<b>Model:</b> ".$d->nombre_modelo."<br />";
			$display .= "<b>Placa:</b> ".$d->num_placa."<br />";
			$positions[] = array($display, $d->lat_placa, $d->long_placa);
		}
		$view['user'] = $this->_user;
		$view['drivers'] = $drivers;
		$view['positions'] = json_encode($positions);
		$this->load->view('dashboard', $view);
	}
	
	public function logout()
	{
		$this->session->sess_destroy();
		$this->_facebook->destroySession();
        redirect(base_url());
	}
	
	public function get_new_drives()
	{
		$this->load->model('Drive_model');
		$lastId = $_GET['lastId'];
		$newsDriver = $this->Drive_model->getNewDrivers($this->_user['id'], $lastId);
		echo json_encode($newsDriver); exit;
	}
}
