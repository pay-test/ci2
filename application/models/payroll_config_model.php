<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payroll_config_model extends CI_Model {

	function get_matrix_table($sess_id, $org_id)
	{
		$this->db->select('m.id as id,
						   c.job_class_nm as job_class,
						   c.job_level as job_level,
						   j.title as job_value,
						   value,
						   value_max,
						   value_min
						 ')
				 ->from('payroll_job_value_matrix as m')
				 ->join('hris_job_class as c', 'c.job_class_id = m.job_class_id')
				 ->join('payroll_job_value as j', 'j.id = m.job_value_id')
				 ->where('session_id', $sess_id)
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
