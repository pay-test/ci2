<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payroll_setup_model extends CI_Model {

	var $table = 'payroll_period';
	var $table_join1 = 'payroll_monthly_income';
	var $table_join2 = 'payroll_monthly_income_component';
	var $table_join3 = 'payroll_component';

	var $column = array('title','code','component_type_id','tax_component_id'); //set column field database for order and search
	var $order = array('id' => 'asc'); // default order 

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	//custom model

	function get_monthly_income($monthly_id) {

		$this->db->select(
			$this->table_join1.'.employee_id as employee_id,
			'.$this->table_join2.'.value as value,
			'.$this->table_join3.'.id as component_id,
			'.$this->table_join3.'.component_type_id as component_type_id,
			');

		$this->db->from($this->table_join1);
		$this->db->join($this->table_join2, $this->table_join1.'.id = '.$this->table_join2.'.payroll_monthly_income_id', 'left');
		$this->db->join($this->table_join3, $this->table_join2.'.payroll_component_id = '.$this->table_join3.'.id', 'left');
		$this->db->where('payroll_monthly_income_id', $monthly_id);

		$result = $this->db->get();

		return $result;
	}

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

	function get_component_type() {
		$this->db->from('payroll_component_type');
		$this->db->where('is_deleted', 0);
		$result = $this->db->get();

		return $result;
	}

	function get_tax_component() {
		$this->db->from('payroll_tax_component');
		$this->db->where('is_deleted', 0);
		$result = $this->db->get();

		return $result;
	}

	//e.o. custom model

	private function _get_datatables_query()
	{
		
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

	public function get_employee_detail($employee_id = 0) {
		$this->db->select('a.employee_id,f.person_nm,c.job_id,c.job_nm,d.job_class_id,d.job_class_cd,d.gradeval_top,d.job_level,e.org_id,e.org_cd,e.org_nm');
		$this->db->from('hris_employee AS a');
		$this->db->join('hris_employee_job AS b', 'b.employee_id = a.employee_id', 'left');
		$this->db->join('hris_jobs AS c', 'c.job_id = b.job_id', 'left');
		$this->db->join('hris_job_class AS d', 'd.job_class_id = c.job_class_id', 'left');
		$this->db->join('hris_orgs AS e', 'e.org_id = c.org_id', 'left');
		$this->db->join('hris_persons AS f', 'f.person_id = a.person_id', 'left');
		$this->db->where('a.status_cd', 'normal');
		$this->db->where('a.employee_id', $employee_id);
		$query = $this->db->get();

		return $query;

	}
}
