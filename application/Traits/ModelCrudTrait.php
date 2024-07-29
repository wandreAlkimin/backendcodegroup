<?php

defined('BASEPATH') OR exit('No direct script access allowed');

trait ModelCrudTrait {

	public function getAll() {
		$query = $this->db->get($this->table);
		return $query->result_array();
	}

	public function getId($id) {
		$query = $this->db->get_where($this->table, array('id' => $id));
		return $query->row_array();
	}

	public function insert($data) {
		$this->db->insert('users', $data);
		return $this->db->insert_id();
	}

	public function update($id, $data) {
		$this->db->where('id', $id);
		return $this->db->update($this->table, $data);
	}

	public function delete($id) {
		$this->db->where('id', $id);
		return $this->db->delete($this->table);
	}

	public function search() {
		$query = $this->db->select('*')->from($this->table);
		return $query;
	}
}
