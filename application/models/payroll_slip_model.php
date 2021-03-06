<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payroll_slip_model extends CI_Model {

	var $table = 'hris_employee';
	var $table_join1 = 'hris_users';
	var $table_join2 = 'hris_persons';
	var $table_join3 = 'payroll_group';
	var $table_join4 = 'payroll_monthly_income';
	var $table_join5 = 'payroll_monthly_component';
	var $column = array('user_nm','person_nm'); //set column field database for order and search
	//var $order = array('employee_id' => 'asc'); // default order 

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	//custom model

	function render_periode($year_now = 0) {
		$this->db->from('payroll_period');
		$this->db->where('year', $year_now);
		$result = $this->db->get();

		return $result;
	}

	function get_period($period = 0) {
		$this->db->from('payroll_period');
		$this->db->where('id', $period);
		$result = $this->db->get();

		return $result;
	}

	//e.o. custom model

	private function _get_datatables_query()
	{
		$this->db->select(
			$this->table.'.employee_id as employee_id,
			'.$this->table_join1.'.user_nm as user_nm,
			'.$this->table_join2.'.person_nm as person_nm,
			');

		$this->db->from($this->table);
		$this->db->join($this->table_join1, $this->table_join1.'.person_id = '.$this->table.'.employee_id', 'left');
		$this->db->join($this->table_join2, $this->table_join2.'.person_id = '.$this->table.'.employee_id', 'left');
		$this->db->where('user_nm REGEXP "^[0-9]"', NULL, FALSE);
		$this->db->where('hris_employee.status_cd', 'normal');

		$i = 0;
	
		foreach ($this->column as $item) // loop column 
		{
			if($_POST['search']['value'])
			{
				if($item == 'user_nm'){
					$item = $this->table_join1.'.user_nm';
				}elseif($item == 'person_nm'){
					$item = $this->table_join2.'.person_nm';
				}

				($i===0) ? $this->db->like($item, $_POST['search']['value']) : $this->db->or_like($item, $_POST['search']['value']);
			}
				
			$column[$i] = $item;
			$i++;
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
		$this->db->select(
			$this->table.'.employee_id as employee_id,
			'.$this->table_join1.'.user_nm as user_nm,
			'.$this->table_join2.'.person_nm as person_nm,
			');

		$this->db->from($this->table);
		$this->db->join($this->table_join1, $this->table_join1.'.person_id = '.$this->table.'.employee_id', 'left');
		$this->db->join($this->table_join2, $this->table_join2.'.person_id = '.$this->table.'.employee_id', 'left');
		$this->db->where('user_nm REGEXP "^[0-9]"', NULL, FALSE);
		$this->db->where('hris_employee.status_cd', 'normal');
		return $this->db->count_all_results();
	}

	public function get_by_id($id)
	{
		$this->db->select(
			$this->table.'.employee_id as employee_id,
			'.$this->table_join1.'.user_nm as user_nm,
			'.$this->table_join2.'.person_nm as person_nm,
			'.$this->table_join4.'.payroll_group_id as group_id,
			');

		$this->db->from($this->table);
		$this->db->join($this->table_join1, $this->table_join1.'.person_id = '.$this->table.'.employee_id', 'left');
		$this->db->join($this->table_join2, $this->table_join2.'.person_id = '.$this->table.'.employee_id', 'left');
		$this->db->join($this->table_join4, $this->table_join4.'.employee_id = '.$this->table.'.employee_id', 'left');
		$this->db->where('user_nm REGEXP "^[0-9]"', NULL, FALSE);
		$this->db->where('hris_employee.status_cd', 'normal');
		$this->db->where($this->table.'.employee_id', $id);
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

	public function get_component($id)
	{
		return $this->db->select('payroll_component.title as component, 
							      payroll_component.code as code, 
							      payroll_group_component.payroll_component_id as component_id'
						  		)
				 ->from('payroll_group_component')
				 ->join('payroll_component', 'payroll_component.id = payroll_group_component.payroll_component_id')
				 ->where('payroll_group_component.payroll_group_id', $id)
				 ->get();
	}

	public function get_monthly_component($id, $monthly_id)
	{
		return $this->db->select('payroll_monthly_income_component.id as id,
								  payroll_component.title as component, 
							      payroll_component.code as code, 
							      payroll_monthly_income_component.value as value,
							      payroll_monthly_income_component.payroll_component_id as component_id'
						  		)
				 ->from('payroll_monthly_income_component')
				 ->join('payroll_component', 'payroll_component.id = payroll_monthly_income_component.payroll_component_id')
				 ->where('payroll_monthly_income_component.payroll_monthly_income_id', $monthly_id)
				 ->where('payroll_monthly_income_component.is_deleted', 0)
				 ->get();
	}

	public function get_group()
	{	
		$this->db->where($this->table_join3.'.is_deleted',0);
		$this->db->order_by($this->table_join3.'.title','asc');
		return $this->db->get($this->table_join3);
	}
}
