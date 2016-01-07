<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
if (!function_exists('permission')){
	function permission()
	{
		$CI =& get_instance();
		if(!$CI->session->userdata("user_id")){
			redirect("login");
		}
		
		return $CI->session->userdata("user_id");
	}
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