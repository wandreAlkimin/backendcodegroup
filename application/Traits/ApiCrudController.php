<?php

defined('BASEPATH') OR exit('No direct script access allowed');

trait ApiCrudController
{
	public $CI;
	public $model;
	public $validation_rules = [];

	public function __construct()
	{
		$this->CI =& get_instance();
	}

	protected function getModel()
	{
		return $this->model;
	}

	protected function getValidationRules()
	{
		return $this->validation_rules;
	}

	public function successResponse($data = null, $message = null, $status = 200)
	{
		$response = [
			'data' => $data,
			'message' => $message,
			'result' => true,
		];

		return $this->output
			->set_content_type('application/json')
			->set_status_header($status)
			->set_output(json_encode($response));
	}

	public function errorResponse($message, $erroInterno, $erroValidacao = null, $status = 200)
	{
		$response = [
			'data' => false,
			'message' => $message,
			'result' => false,
			'erroInterno' => $erroInterno,
			'erroValidacao' => $erroValidacao
		];

		return $this->output
			->set_content_type('application/json')
			->set_status_header($status)
			->set_output(json_encode($response));
	}

	public function search()
	{
		try {
			$model = $this->getModel();
			$query = $model->search();

			$resultados = $this->aplicarFiltros($query, $this->input->get());

			if (empty($resultados)) {
				return $this->errorResponse('Nenhum item encontrado', 'NenhumEncontrado', null);
			}

			return $this->successResponse($resultados, 'Resultado recuperado com sucesso');
		} catch (Exception $e) {
			log_message('error', 'Ocorreu um erro ao recuperar os registros: ' . $e->getMessage());
			return $this->errorResponse('Ocorreu um erro ao recuperar os registros.', $e->getMessage());
		}
	}

	protected function aplicarFiltros($query, $params)
	{
		$pagina = isset($params['page']) ? $params['page'] : 1;
		$registrosPorPagina = isset($params['limit']) ? $params['limit'] : 5;

		foreach ($params as $campo => $valor) {
			if ($valor !== null && $valor !== 'null' && $campo !== 'page' && $campo !== 'limit') {
				$query->or_like($campo, $valor);
			}
		}

		$total = $query->count_all_results('', false);
		$query->limit($registrosPorPagina, ($pagina - 1) * $registrosPorPagina);
		$resultados = $query->get()->result();

		return [
			'data' => $resultados,
			'total' => $total,
			'per_page' => $registrosPorPagina,
			'current_page' => $pagina,
			'last_page' => ceil($total / $registrosPorPagina)
		];
	}

	public function index()
	{
		try {
			$model = $this->getModel();
			$items = $model->getAll();
			return $this->successResponse($items);
		} catch (Exception $e) {
			log_message('error', 'Erro interno ao listar todos os itens: ' . $e->getMessage());
			return $this->errorResponse('Erro interno ao listar todos os itens.', $e->getMessage(), null, 500);
		}
	}

	public function show($id)
	{
		try {
			$model = $this->getModel();
			$item = $model->getId($id);

			if (!$item) {
				return $this->errorResponse('Item não encontrado', 'NãoEncontrado', null, 200);
			}

			return $this->successResponse($item);
		} catch (Exception $e) {
			log_message('error', 'Erro interno ao exibir um item: ' . $e->getMessage());
			return $this->errorResponse('Erro interno ao exibir um item.', $e->getMessage(), null, 500);
		}
	}

	public function store()
	{
		try {
			$data = json_decode($this->input->raw_input_stream, true);


			$model = $this->getModel();


			$this->load->library('form_validation');
			$this->form_validation->set_data($data);
			$this->form_validation->set_rules($this->getValidationRules());

			if ($this->form_validation->run() === FALSE) {
				return $this->errorResponse('Erro de validação', $this->form_validation->error_array(), $this->form_validation->error_array(), 200);
			}

			$item = $model->insert($data);

			return $this->successResponse($item, 'Item criado com sucesso', 201);
		} catch (Exception $e) {
			log_message('error', 'Erro interno ao criar um novo item: ' . $e->getMessage());
			return $this->errorResponse('Erro interno ao criar um novo item.', $e->getMessage(), null, 500);
		}
	}

	public function update($id)
	{
		try {

			$data = json_decode($this->input->raw_input_stream, true);

			$model = $this->getModel();

			$this->load->library('form_validation');
			$this->form_validation->set_data($data);
			$this->form_validation->set_rules($this->getValidationRules());

			if ($this->form_validation->run() === FALSE) {
				return $this->errorResponse('Erro de validação', $this->form_validation->error_array(), $this->form_validation->error_array());
			}

			$item = $model->getId($id);

			if (!$item) {
				return $this->errorResponse('Item não encontrado', 'NãoEncontrado', null, 200);
			}

			$model->update($id, $data);

			return $this->successResponse($model->getId($id), 'Item atualizado com sucesso');
		} catch (Exception $e) {
			log_message('error', 'Erro interno ao atualizar um item: ' . $e->getMessage());
			return $this->errorResponse('Erro interno ao atualizar um item.', $e->getMessage(), null, 500);
		}
	}

	public function destroy($id)
	{
		try {
			$model = $this->getModel();
			$item = $model->getId($id);

			if (!$item) {
				return $this->errorResponse('Item não encontrado', 'NãoEncontrado', null, 200);
			}

			$model->delete($id);

			return $this->successResponse(null, 'Item excluído com sucesso');
		} catch (Exception $e) {
			log_message('error', 'Erro interno ao excluir um item: ' . $e->getMessage());
			return $this->errorResponse('Erro interno ao excluir um item.', $e->getMessage(), null, 500);
		}
	}
}
