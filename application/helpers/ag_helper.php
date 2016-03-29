<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
if (!function_exists('sessNow')){
	function sessNow()
	{
		$CI =& get_instance();
		$y = date('Y');
		$start_ses = $y."-04-01 00:00:00";
        $session = (date('Y-m-d H:i:s') < $start_ses) ? $y-1 : $y;//print_mz($session); 
		
		return $session;
	}
}
if (!function_exists('permission')){
	function permission()
	{
		$CI =& get_instance();
		if(!$CI->session->userdata("person_id")){
			redirect("login");
		}
		
		return $CI->session->userdata("person_id");
	}
}

if(!function_exists('sessId')){
	function sessId()
	{
		$CI =& get_instance();
		$sess_id = $CI->session->userdata("person_id");
		if(!empty($sess_id)){
			return $sess_id;
		}
	}
}

if (!function_exists('getPersonIdFromNik')){
function getPersonIdFromNik($nik)
{
	$f = getValue('person_id', 'hris_persons', array('ext_id'=>'where/'.$nik));
	return $f;
}
}

function getMonthNumber($month)
{
	$bln = array('Jan'=>1,'Feb'=>2,'Mar'=>3,'Apr'=>4,'May'=>5,'Jun'=>6,'Jul'=>7,'Aug'=>8,'Sep'=>9,'Oct'=>10,'Nov'=>11,'Dec'=>12);
	return $bln[$month];
}

if ( ! function_exists('options_row'))
	{
		function options_row($model=NULL,$function=NULL,$id_field=NULL,$title_field=NULL,$default=NULL)
		{
			$CI =& get_instance();
			$query = get_query_view($model, $function, '' ,'','');
			if($default) $data['options_row'][''] = $default;
			
			foreach($query['result_array'] as $row)
			{
				$data['options_row'][$row[$id_field]] = $row[$title_field];
			}
			return $data['options_row'];
		}
	}


if ( ! function_exists('get_query_view'))
	{
		function get_query_view($model, $function, $function_count=NULL,$limit=NULL, $uri_segment=NULL)
		{
			$CI =& get_instance();
			if($uri_segment != NULL)
				$offset = $CI->uri->segment($uri_segment);
			else
				$offset = 0;
			
			$data['query'] = $q_ = $CI->$model->$function($limit,$offset);
			$data['result_array'] = $q_->result_array();
			if($function_count != '')
				$data['num_rows'] = $CI->$model->$function_count();
			else
				$data['num_rows'] = $q_->num_rows();
			return $data;
		}
	}

	if (!function_exists('print_ag')){	
	function print_ag($data)
	{
		echo "<pre>";
		print_r($data);
		echo "</pre>";
	}
}

if (!function_exists('get_total_hadir_satu_periode')){	
	function get_total_hadir_satu_periode($emp_id, $period_id)
	{
		$CI =& get_instance();
		$filter_period = array('id'=>'where/'.$period_id);
		$month = getValue('month', 'payroll_period', $filter_period);
		$year = getValue('year', 'payroll_period', $filter_period);
		$date_end = $year.'-'.$month.'-'.'15';
		$date_start = date("Y-m-d", strtotime( date( $date_end, strtotime( date("Y-m-d") ) ) . "-1 month" ) );
		$date_start = date("Y-m-d", strtotime( date( $date_start, strtotime( date("Y-m-d") ) ) . "+1 day" ) );
		$q = "select jh from kg_view_attendance WHERE (date_full BETWEEN '2015-09-29' AND '2016-01-30') and id_employee = $emp_id and jh=1";
		$q = $CI->db->query($q)->num_rows();
		return $q;
	}
}

if(!function_exists('dateNow')){
	function dateNow()
	{
		return date('Y-m-d H:i:s');
	}
}
