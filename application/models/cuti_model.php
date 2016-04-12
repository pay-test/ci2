<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class cuti_model extends CI_Model {

	var $table = 'kg_view_cuti';
	var $column_history = array('id','tgl_start','hari_ref', 'reason');
	var $column_approve = array('id','tgl_start_ref','hari_ref', 'reason','feedback','status');
	
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	//custom model
	private function _get_datatables_query($param=NULL)
	{
		//Parameter
		$where = "id > 0 ";
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
		
		//Query
		$select = "*";
		$this->db->select($select);
		$this->db->from("kg_view_cuti_platfon");
		$this->db->where($where);
		$this->db->order_by("person_nm", "asc");
	}
	
	private function _get_datatables_query_history($param=NULL)
	{
		//Paramter
		$exp = explode("~", $param['tgl']);
		$where = "tgl_start >= '".$exp[0]."' AND tgl_start <= '".$exp[1]."' AND cuti_status='Approve' ";
		
		if(isset($param['person_id']) && $param['person_id'] > 0) $where .= "AND id_employee='".$param['person_id']."' ";
		
		//Query
		$select = "*";
		$this->db->select($select);
		$this->db->from($this->table);
		
		$i = 0;
		$like="";
		foreach ($this->column_history as $item) 
		{
			if($_POST['search']['value'])
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
		
		$this->db->order_by("tgl_start", "asc");
	}
	
	private function _get_datatables_query_approve($param=NULL)
	{
		//Paramter
		$exp = explode("~", $param['tgl']);
		$where = "tgl_start >= '".$exp[0]."' AND tgl_start <= '".$exp[1]."' ";
		
		if(isset($param['person_id']) && $param['person_id'] > 0) {
			$get_bawahan = CekBawahan($param['person_id']);
			$bawahan = join($get_bawahan, ',');
			$where .= "AND (create_user_id='".$param['person_id']."' ";
			if(count($get_bawahan) > 0) $where .= " OR create_user_id in (".$bawahan.")) ";
			else $where .= " ) ";
		}
		
		//Query
		$select = "*";
		$this->db->select($select);
		$this->db->from($this->table);
		
		$i = 0;
		$like="";
		foreach ($this->column_approve as $item) 
		{
			if($_POST['search']['value'])
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
		
		$this->db->order_by("tgl_start_ref", "asc");
	}
	
	function get_datatables($param=NULL)
	{
		if(isset($param['history'])) $this->_get_datatables_query_history($param);
		else if(isset($param['approve'])) $this->_get_datatables_query_approve($param);
		else $this->_get_datatables_query($param);
		$query = $this->db->get();
		//lastq();
		return $query;
	}

	function count_filtered($param=NULL)
	{
		if(isset($param['history'])) $this->_get_datatables_query_history($param);
		else if(isset($param['approve'])) $this->_get_datatables_query_approve($param);
		else $this->_get_datatables_query($param);
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all($param=NULL)
	{
		if(isset($param['history'])) $this->_get_datatables_query_history($param);
		else if(isset($param['approve'])) $this->_get_datatables_query_approve($param);
		else $this->_get_datatables_query($param);
		$query = $this->db->get();
		//lastq();
		return $query->num_rows();
	}
}
