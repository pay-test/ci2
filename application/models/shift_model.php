<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class shift_model extends CI_Model {

	var $table = 'kg_view_shift';
	var $column = array('id','ext_id','person_nm','bulan','jum_p','jum_s','jum_m','jum_ns','jum_off'); //set column field database for order and search
	var $column_detail = array('id','date_full','jh','off','cuti','ijin','sakit','alpa','scan_masuk','scan_pulang'); //set column field database for order and search
	
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	//custom model
	private function _get_datatables_query($param=NULL)
	{
		$where = "bulan = '".$param['bulan']."' AND tahun = '".$param['tahun']."' ";		
		if(isset($param['divisi']) && $param['divisi'] > 0) {
			$id_sec = "(";
			$q = GetAll("hris_orgs", array("parent_id"=> "where/".$param['divisi']));
			foreach($q->result_array() as $r) {
				$id_sec .= "'".$r['org_id']."',"; 
			}
			$id_sec = substr($id_sec,0,-1).")";
			$where .= "AND org_id in ".$id_sec." ";
		}
		if(isset($param['section']) && $param['section'] > 0) $where .= "AND org_id='".$param['section']."' ";
		if(isset($param['position']) && $param['position'] > 0) $where .= "AND job_class_id='".$param['position']."' ";
		if(isset($param['grade']) && $param['grade'] > 0) $where .= "AND grade_job_class='".$param['grade']."' ";
		
		if(isset($param['regs'])) {
			if($param['regs']=="reguler") $where .= "AND group_shift='N.A.' ";//$this->db->where("group_shift", "N.A.");
			else if($param['regs']=="shift") $where .= "AND group_shift in ('A', 'B', 'C', 'D') ";//$this->db->where_in("group_shift", array("A","B","C","D"));
		}
		
		$person_id = permission();
		if($person_id != 1) {
			$bawahan = CekBawahan($person_id);
			$id_bawahan = "('".$person_id."',";
			foreach($bawahan as $r) {
				$id_bawahan .= "'".$r."',"; 
			}
			$id_bawahan = substr($id_bawahan,0,-1).")";
			$where .= "AND person_id in ".$id_bawahan." ";
		}
		
		//Query
		$this->db->select("*");
		$this->db->from($this->table);
		
		$i = 0;
	  $like="";
		foreach ($this->column as $item) 
		{
			if(isset($_POST['search']['value']))
			{
				//($i===0) ? $this->db->like($item, $_POST['search']['value']) : $this->db->or_like($item, $_POST['search']['value']);
				$like .= ($i===0) ? " AND (".$item." LIKE '%".$_POST['search']['value']."%' " : " OR ".$item." LIKE '%".$_POST['search']['value']."%' ";
			}
				
			$column[$i] = $item;
			$i++;
		}
		if($like != "") {
			$like .= ")";
			$this->db->where($where.$like);
		} else $this->db->where($where);
		
		if(isset($_POST['order'])) // here order processing
		{
			$this->db->order_by($column[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} 
		else if(isset($this->order))
		{
			$order = $this->order;
			$this->db->order_by(key($order), $order[key($order)]);
		}
		
		$this->db->order_by("person_nm", "asc");
	}
	
	function get_datatables($param=NULL)
	{
		$this->_get_datatables_query($param);
		if(!isset($param['report'])) {
			if($_POST['length'] != -1) $this->db->limit($_POST['length'], $_POST['start']);
		}
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

	function get_by_id($id, $param=NULL)
	{
		$this->db->from($this->table);
		$this->db->where('id_employee',$id);
		$exp = explode("~", $param['tgl']);
		$this->db->where("date_full >=", $exp[0]);
		$this->db->where("date_full <=", $exp[1]);
		$query = $this->db->get();

		return $query;
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
