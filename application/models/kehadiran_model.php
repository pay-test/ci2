<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kehadiran_model extends CI_Model {

	var $table = 'kg_view_kehadiran_mcci';
	var $column = array("person_nm","jhk","jh","off","cuti","phl","ijin","sakit","alpa","potong_gaji","pc"); //set column field database for order and search
	//var $order = array('tahun' => 'asc','bulan'=>'asc', 'tanggal'=>'asc', 'id_employee'=>'asc'); // default order 

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	private function _get_datatables_query()
	{
		$this->db->select('id, person_nm, id_employee as a_id, SUM(jhk) as jhk, SUM(sakit) as sakit, SUM(cuti) as cuti, SUM(ijin) as ijin, SUM(phl) as phl, SUM(alpa) as alpa, SUM(off) as off, SUM(potong_gaji) as potong_gaji, SUM(pc) as pc, SUM(jh) as jh');
		$this->db->from($this->table);
		$this->db->group_by('id_employee');
		$i = 0;
	
		foreach ($this->column as $item) 
		{
			if($_POST['search']['value'])
			{
				($i===0) ? $this->db->like($item, $_POST['search']['value']) : $this->db->or_like($item, $_POST['search']['value']);
			}
				
			$column[$i] = $item;
			$i++;
		}
		
		if(isset($_POST['order']))
		{
			$this->db->order_by($column[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} 
		else
		{
			//$order = $this->order;
			//$this->db->order_by(key($order), $order[key($order)]);
			$this->db->order_by('tahun', 'asc');
			$this->db->order_by('bulan', 'asc');
			$this->db->order_by('tanggal', 'asc');
			$this->db->order_by('id_employee', 'asc');
		}
	}

	function get_datatables()
	{
		$this->_get_datatables_query();
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered()
	{
		$this->_get_datatables_query();
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all()
	{
		$this->db->from($this->table);
		return $this->db->count_all_results();
	}

	public function get_by_id($id)
	{
		$this->db->from($this->table);
		$this->db->where('id',$id);
		$query = $this->db->get();

		return $query->row();
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
