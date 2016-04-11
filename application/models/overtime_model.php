<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class overtime_model extends CI_Model {

	var $table = 'kg_view_overtime';
	var $column = array('id','ext_id','person_nm','bulan','ovt_hour_sum','ovt_hour_cal'); //set column field database for order and search
	var $column_detail = array('id','date_full','ovt_hour_sum','ovt_hour_cal','ovt_reason','ovt_detail_reason'); //set column field database for order and search
	var $column_history = array('id','date_full','ovt_hour_sum', 'ovt_reason');
	var $column_approve = array('id','date_full','start_ovt','start_ovt_ref','ovt_hour_sum', 'ovt_reason','ovt_feedback','ovt_status');
	
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	//custom model
	private function _get_datatables_query($param=NULL)
	{
		//Param
		$exp = explode("~", $param['tgl']);
		$where = "((date_full >= '".$exp[0]."' AND date_full <= '".$exp[1]."' AND date_temp='0000-00-00') || (date_temp >= '".$exp[0]."' AND date_temp <= '".$exp[1]."')) AND ovt_status='Approve' ";
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
		if(isset($param['rekap']) && $param['rekap'] == "full") $select = "*";
		else $select = "*, SUM(ovt_hour_sum) as ovt_hour_sum, SUM(ovt_hour_cal) as ovt_hour_cal";
		$this->db->select($select);
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
		
		if(isset($param['rekap']) && $param['rekap'] == "full") {$s=1;}
		else $this->db->group_by(array("id_employee", "person_nm"));
		
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
	
	private function _get_datatables_query_detail($param=NULL)
	{
		//Parameter
		$exp = explode("~", $param['tgl']);
		$where = "date_full >= '".$exp[0]."' AND date_full <= '".$exp[1]."' AND ovt_status='Approve' ";
		if(isset($param['id_emp'])) $where .= "AND id_employee='".$param['id_emp']."' ";
		
		//Query
		$this->db->select("*");
		$this->db->from($this->table);

		$i = 0;
		$like="";
		foreach ($this->column_detail as $item) 
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
	}
	
	private function _get_datatables_query_history($param=NULL)
	{
		//Paramter
		$exp = explode("~", $param['tgl']);
		$where = "date_full >= '".$exp[0]."' AND date_full <= '".$exp[1]."' AND ovt_status='Approve' ";
		
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
		
		$this->db->order_by("date_full", "asc");
	}
	
	private function _get_datatables_query_approve($param=NULL)
	{
		//Paramter
		$exp = explode("~", $param['tgl']);
		$where = "date_full >= '".$exp[0]."' AND date_full <= '".$exp[1]."' ";
		
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
		
		$this->db->order_by("date_full", "asc");
	}
	
	function get_datatables($param=NULL)
	{
		if(isset($param['detailz'])) $this->_get_datatables_query_detail($param);
		else if(isset($param['history'])) $this->_get_datatables_query_history($param);
		else if(isset($param['approve'])) $this->_get_datatables_query_approve($param);
		else $this->_get_datatables_query($param);
		if(!isset($param['rekap'])) {
			if($_POST['length'] != -1) $this->db->limit($_POST['length'], $_POST['start']);
		}
		$this->db->limit(20, 0);
		$query = $this->db->get();
		//lastq();
		return $query;
	}

	function count_filtered($param=NULL)
	{
		if(isset($param['detailz'])) $this->_get_datatables_query_detail($param);
		else if(isset($param['history'])) $this->_get_datatables_query_history($param);
		else if(isset($param['approve'])) $this->_get_datatables_query_approve($param);
		else $this->_get_datatables_query($param);
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all($param=NULL)
	{
		if(isset($param['detailz'])) $this->_get_datatables_query_detail($param);
		else if(isset($param['history'])) $this->_get_datatables_query_history($param);
		else if(isset($param['approve'])) $this->_get_datatables_query_approve($param);
		else $this->_get_datatables_query($param);
		$query = $this->db->get();
		//lastq();
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
