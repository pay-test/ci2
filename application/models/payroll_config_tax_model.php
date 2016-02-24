<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payroll_config_tax_model extends CI_Model {

	function get_matrix_table_management($sess_id, $org_id)
	{
		$this->db->select('m.id as id,
						   c.job_class_nm as job_class,
						   j.title as job_value,
						   value,
						   value_max,
						   value_min
						 ')
				 ->from('payroll_job_value_matrix as m')
				 ->join('hris_job_class as c', 'c.job_class_id = m.job_class_id')
				 ->join('payroll_job_value as j', 'j.id = m.job_value_id')
				 ->where('session_id', $sess_id)
				 ->where('c.job_level', 'management')
				 ->where('org_id', $org_id);
		return $q = $this->db->get();
	}

	function get_matrix_table_nonmanagement($sess_id, $org_id)
	{
		$this->db->select('m.id as id,
						   c.job_class_nm as job_class,
						   j.title as job_value,
						   value,
						   value_max,
						   value_min
						 ')
				 ->from('payroll_job_value_matrix as m')
				 ->join('hris_job_class as c', 'c.job_class_id = m.job_class_id')
				 ->join('payroll_job_value as j', 'j.id = m.job_value_id')
				 ->where('session_id', $sess_id)
				 ->where('c.job_level', 'nonmanagement')
				 ->where('org_id', $org_id);
		return $q = $this->db->get();
	}

	function get_com_table($sess_id)
	{
		$this->db->select('m.id as id,
						   c.job_class_nm as job_class,
						   var,
						   fix
						 ')
				 ->from('payroll_compensation_mix as m')
				 ->join('hris_job_class as c', 'c.job_class_id = m.job_class_id')
				 ->where('session_id', $sess_id);
		return $q = $this->db->get();
	}
}
