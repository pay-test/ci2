<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Monthly_income_model extends CI_Model {

	var $table = 'hris_employee';
	var $table_join1 = 'hris_users';
	var $table_join2 = 'hris_persons';
	var $table_join3 = 'payroll_group';
	var $table_join4 = 'payroll_monthly_income';
	var $table_join5 = 'payroll_monthly_income_component';
	var $table_join6 = 'payroll_period';
	var $table_join7 = 'payroll_master';
	var $column = array('user_nm','person_nm'); //set column field database for order and search
	//var $order = array('employee_id' => 'asc'); // default order 

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

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

	public function get_by_id($id, $period_id)
	{
		$this->db->select(
			$this->table.'.employee_id as employee_id,
			'.$this->table_join1.'.user_nm as user_nm,
			'.$this->table_join2.'.person_nm as person_nm,
			'.$this->table_join4.'.payroll_group_id as group_id,
			'.$this->table_join4.'.payroll_currency_id,
			'.$this->table_join4.'.is_expatriate,
			'.$this->table_join4.'.payroll_ptkp_id,
			'.$this->table_join4.'.payroll_tax_method_id,
			'.$this->table_join3.'.title as group_title,
			');

		$this->db->from($this->table);
		$this->db->join($this->table_join1, $this->table_join1.'.person_id = '.$this->table.'.employee_id', 'left');
		$this->db->join($this->table_join2, $this->table_join2.'.person_id = '.$this->table.'.employee_id', 'left');
		$this->db->join($this->table_join4, $this->table_join4.'.employee_id = '.$this->table.'.employee_id', 'left');
		$this->db->join($this->table_join3, $this->table_join4.'.payroll_group_id = '.$this->table_join3.'.job_class_id', 'left');
		//$this->db->join($this->table_join5, $this->table_join4.'.id = '.$this->table_join5.'.payroll_monthly_income_id', 'left');
		//$this->db->join($this->table_join6, $this->table_join4.'.payroll_period_id = '.$this->table_join6.'.id', 'left');
		$this->db->where('user_nm REGEXP "^[0-9]"', NULL, FALSE);
		$this->db->where('hris_employee.status_cd', 'normal');
		$this->db->where($this->table.'.employee_id', $id);
		$this->db->where($this->table_join4.'.payroll_period_id', $period_id);
		$query = $this->db->get();

		return $query->row();
	}

	function get_master($id){
		/*var $table = 'hris_employee';
		var $table_join1 = 'hris_users';
		var $table_join2 = 'hris_persons';
		var $table_join3 = 'payroll_group';
		var $table_join4 = 'payroll_monthly_income';
		var $table_join5 = 'payroll_monthly_income_component';
		var $table_join6 = 'payroll_period';
		var $table_join7 = 'payroll_master';
		*/
		$this->db->select(
			$this->table.'.employee_id as employee_id,
			'.$this->table_join1.'.user_nm as user_nm,
			'.$this->table_join2.'.person_nm as person_nm,
			'.$this->table_join7.'.payroll_group_id as group_id,
			'.$this->table_join3.'.title as group_title,
			');

		$this->db->from($this->table);
		$this->db->join($this->table_join1, $this->table_join1.'.person_id = '.$this->table.'.employee_id', 'left');
		$this->db->join($this->table_join2, $this->table_join2.'.person_id = '.$this->table.'.employee_id', 'left');
		$this->db->join($this->table_join7, $this->table_join7.'.employee_id = '.$this->table.'.employee_id', 'left');
		$this->db->join($this->table_join3, $this->table_join7.'.payroll_group_id = '.$this->table_join3.'.job_class_id', 'left');
		$this->db->where('user_nm REGEXP "^[0-9]"', NULL, FALSE);
		$this->db->where('hris_employee.status_cd', 'normal');
		$this->db->where('payroll_master.employee_id', $id);
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

	public function get_monthly_component($monthly_id)
	{
		return $this->db->select('payroll_monthly_income_component.id as id,
								  payroll_component.title as component, 
							      payroll_component.code as code, 
							      payroll_component_type.title as component_type, 
							      payroll_monthly_income_component.value as value,
							      payroll_monthly_income_component.payroll_component_id as component_id'
						  		)
				 ->from('payroll_monthly_income_component')
				 ->join('payroll_component', 'payroll_component.id = payroll_monthly_income_component.payroll_component_id')
				 ->join('payroll_component_type', 'payroll_component.component_type_id = payroll_component_type.id')
				 ->where('payroll_monthly_income_component.payroll_monthly_income_id', $monthly_id)
				 ->where('payroll_monthly_income_component.is_deleted', 0)
				 ->order_by('payroll_monthly_income_component.value', 'desc')
				 ->order_by('payroll_component.component_type_id', 'asc')
				 ->get();
	}

	function get_master_payroll_component($master_payroll_id)
	{
		return $this->db->select('payroll_master_component.id as id,
								  payroll_component.title as component, 
							      payroll_component.code as code, 
							      payroll_component_type.title as component_type,
							      payroll_master_component.value as value,
							      payroll_master_component.payroll_component_id as component_id'
						  		)
				 ->from('payroll_master_component')
				 ->join('payroll_component', 'payroll_component.id = payroll_master_component.payroll_component_id')
				 ->join('payroll_component_type', 'payroll_component.component_type_id = payroll_component_type.id')
				 ->where('payroll_master_component.payroll_master_id', $master_payroll_id)
				 ->where('payroll_master_component.is_deleted', 0)
				 ->order_by('payroll_monthly_income_component.value', 'desc')
				 ->order_by('payroll_component.component_type_id', 'asc')
				 ->get();
	}

	public function get_group()
	{	
		$this->db->where($this->table_join3.'.is_deleted',0);
		$this->db->order_by($this->table_join3.'.title','asc');
		return $this->db->get($this->table_join3);
	}

	public function get_employee_detail($employee_id) {
		$this->db->select(
			$this->table.'.employee_id as employee_id,
			'.$this->table_join1.'.user_nm as user_nm,
			'.$this->table_join2.'.person_nm as person_nm,
			hris_jobs.job_nm as job_nm,
			hris_jobs.job_abbr as job_abbr,
			hris_orgs.org_nm as org_nm
			');

		$this->db->from($this->table);
		$this->db->join('hris_persons', 'hris_persons.person_id = '.$this->table.'.person_id', 'left');
		$this->db->join('hris_users', 'hris_users'.'.person_id = '.$this->table.'.employee_id', 'left');
		$this->db->join('hris_employee_job', 'hris_employee_job.employee_id = '.$this->table.'.employee_id', 'left');
		$this->db->join('hris_jobs', 'hris_jobs.job_id = hris_employee_job.job_id', 'left');
		$this->db->join('hris_orgs', 'hris_orgs.org_id = hris_jobs.org_id', 'left');
		$this->db->where('hris_employee.employee_id', $employee_id);

		return $this->db->get();
	}


	public function get_ptkp()
	{	
		$this->db->where('payroll_ptkp'.'.is_deleted',0);
		$this->db->order_by('payroll_ptkp'.'.id','asc');
		return $this->db->get('payroll_ptkp');
	}

	public function get_currency()
	{	
		$this->db->where('payroll_currency'.'.is_deleted',0);
		$this->db->order_by('payroll_currency'.'.id','asc');
		return $this->db->get('payroll_currency');
	}

	public function get_tax_method()
	{	
		return $this->db->get('payroll_tax_method');
	}
}
