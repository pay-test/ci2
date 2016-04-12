<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class config_email_alert_model extends CI_Model {

	var $table = 'kg_config_email_alert';
	var $column = array('id','title','ket','days'); //set column field database for order and search
	
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	//custom model
	private function _get_datatables_query($param=NULL)
	{
		$this->db->select("*");
		$this->db->from($this->table);
		
		$i = 0;
	
		foreach ($this->column as $item) // loop column 
		{
			foreach ($this->column as $item) 
			{
				if($_POST['search']['value'])
				{
					($i===0) ? $this->db->like($item, $_POST['search']['value']) : $this->db->or_like($item, $_POST['search']['value']);
				}
					
				$column[$i] = $item;
				$i++;
			}
		}
		
		if(isset($_POST['order'])) // here order processing
		{
			$this->db->order_by($column[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} 
		else if(isset($this->order))
		{
			$order = $this->order;
			$this->db->order_by(key($order), $order[key($order)]);
		}
		
		$this->db->order_by("title", "asc");
	}
	
	function get_datatables($param=NULL)
	{
		$this->_get_datatables_query($param);
		if($_POST['length'] != -1) $this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		//lastq();
		return $query;
	}

	function count_filtered($param=NULL)
	{
		if(isset($param['detailz'])) $this->_get_datatables_query_detail($param);
		else $this->_get_datatables_query($param);
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all($param=NULL)
	{
		if(isset($param['detailz'])) $this->_get_datatables_query_detail($param);
		else $this->_get_datatables_query($param);
		$query = $this->db->get();	
		return $query->num_rows();
	}

	public function save($data)
	{
		$this->db->insert($this->table, $data);
		return $this->db->insert_id();
	}

	public function update($where, $data)
	{
		$this->db->update($this->table, $data, $where);
		return $this->db->affected_rows();
	}

	public function delete_by_id($id)
	{
		$this->db->where('id', $id);
		$this->db->delete($this->table);
	}
}
