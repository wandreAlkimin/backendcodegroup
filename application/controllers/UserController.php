<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '/Traits/ApiCrudController.php';

class UserController extends CI_Controller {

	use ApiCrudController;

	public function __construct() {
		parent::__construct();
		$this->load->model('User', 'user');
		$this->load->library('upload');
		$this->load->helper('url_helper');

		$this->model = $this->user;
		$this->validation_rules = [
			[
				'field' => 'nome',
				'label' => 'Nome',
				'rules' => 'required|max_length[255]'
			],
			[
				'field' => 'email',
				'label' => 'Email',
				'rules' => 'required|valid_email'
			],
			[
				'field' => 'dt_nascimento',
				'label' => 'Data de nascimento',
				'rules' => 'required'
			],
			[
				'field' => 'telefone',
				'label' => 'telefone',
				'rules' => 'required'
			],
		];

	}

	public function uploadFotoPerfil($idUser)
	{
		// Configuração do upload
		$config['upload_path'] = './assets/img';
		$config['allowed_types'] = 'jpg|jpeg|png|gif';
		$config['max_size'] = 2048; // KB
		$config['encrypt_name'] = TRUE; // Criptografar o nome do arquivo

//		TESTAR O CAMINHO DA IMAGEM
//		if (!is_dir($config['upload_path'])) {
//			echo 'O diretório de upload não existe.';
//		} elseif (!is_writable($config['upload_path'])) {
//			echo 'O diretório de upload não é gravável.';
//		} else {
//			echo 'O diretório de upload está configurado corretamente.';
//		}

		$this->upload->initialize($config);

		if ($this->upload->do_upload('perfil_img')) {
			$upload_data = $this->upload->data();
			$file_path = $upload_data['file_name'];

			// Atualizar o caminho da imagem no banco de dados
			if ($this->user->updateFotoPerfil($idUser, $file_path)) {

				$this->successResponse($file_path);

			} else {
				$message = "Erro ao atualizar a foto de perfil.";
				$erroInterno = "Erro ao atualizar a foto de perfil.";

				$this->errorResponse($message, $erroInterno);
			}
		} else {

			$message = "Erro ao atualizar a foto de perfil.";
			$this->errorResponse($message, $this->upload->display_errors());

		}
	}

	public function getUsersDataRange() {
		$startData = $this->input->get('start_data');
		$endData = $this->input->get('end_data');

		if (empty($startData) || empty($endData)) {
			$message = "Insira as datas corretamente";
		   return $this->errorResponse($message, $message);
		}

		// Verifica se os anos são números válidos e têm exatamente 4 dígitos
		if (!is_numeric($startData) || !is_numeric($endData) || strlen($startData) != 4 || strlen($endData) != 4) {
			$message = "Os anos devem ser números válidos de 4 dígitos.";
			return $this->errorResponse($message, $message);
		}

		// Verifica se o ano de início não é posterior ao ano de término
		if ($startData > $endData) {
			$message = "O ano de início não pode ser posterior ao ano de término.";
			return $this->errorResponse($message, $message);
		}

		$results = $this->user->getUsersDateRange($startData, $endData);

		if (empty($results)) {
			$message = "Nenhum registro encontrado";
			$this->errorResponse($message, $message);
		}else{
			$message = "Registros encontrados com sucesso";
			$this->successResponse($results, $message);
		}
	}
}
