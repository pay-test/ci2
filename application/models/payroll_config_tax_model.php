<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payroll_config_tax_model extends CI_Model {

	function get_umk($sess_id)
	{
		return $this->db->select('payroll_umk.id as id, session_id, payroll_umk_city.title as city, value')
				 ->from('payroll_umk_city')
				 ->join('payroll_umk', 'payroll_umk.umk_city_id = payroll_umk_city.id', 'left')
				 ->where('session_id', $sess_id)
				 ->get();
	}
}
