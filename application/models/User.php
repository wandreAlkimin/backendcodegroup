<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '/Traits/ModelCrudTrait.php';

class User extends CI_Model {
	use ModelCrudTrait;

	protected $table = 'users';

	public function __construct() {
		$this->load->database();
	}

	public function updateFotoPerfil($userId, $filePath)
	{
		$data = [
			'perfil_img' => $filePath
		];

		$this->db->where('id', $userId);
		return $this->db->update('users', $data);
	}

	public function getUsersDateRange($startData, $endData) {
		$startData = $startData."-01-01";
		$endData = $endData."-12-31";
		$this->db->where('dt_nascimento >=', $startData);
		$this->db->where('dt_nascimento <=', $endData);
		$query = $this->db->get('users');
		return $query->result();
	}
}
