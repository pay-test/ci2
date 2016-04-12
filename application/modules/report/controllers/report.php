<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class report extends MX_Controller {
	
	var $title = "Report";
  var $filename = "report";
	
	public $data;
	function __construct()
	{
		parent::__construct();
    $this->lang->load('attendance');
	}
	
	function index()
	{
		$data['title'] = $this->title;
    $data['filename'] = $this->filename;
    permission();
    $data['opt_report'] = GetOptMenuReport();
		$this->_render_page('index', $data);
	}
	
	function search($url=NULL)
	{
    $data['title'] = $this->title;
    $data['filename'] = $this->filename;
    $data['path_file'] = "report";
    $data['url'] = $url;
    permission();
		$this->load->view('search', $data);
	}

  function _render_page($view, $data=null, $render=false)
  {
    // $this->viewdata = (empty($data)) ? $this->data: $data;
    // $view_html = $this->load->view($view, $this->viewdata, $render);
    // if (!$render) return $view_html;
    $data = (empty($data)) ? $this->data : $data;
    if(!$render) {
      $this->load->library('template');
      if(in_array($view, array('index'))) {
	      $this->template->set_layout('default');
	
	      $this->template->add_css('assets/plugins/bootstrap-select2/select2.css');
	      $this->template->add_css('assets/plugins/bootstrap-datepicker/css/datepicker.css');
	      $this->template->add_css('assets/plugins/boostrap-clockpicker/bootstrap-clockpicker.min.css');
	
	      $this->template->add_js('assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
	      $this->template->add_js('assets/plugins/boostrap-clockpicker/bootstrap-clockpicker.min.js');
	      
	      $this->template->add_js('modules/js/'.$this->filename.'.js');
      }

      if(!empty($data['title'])) {
      	$this->template->set_title($data['title']);
      }

      $this->template->load_view($view, $data);
    } else {
    	return $this->load->view($view, $data, TRUE);
    }
  }
  
  function get_period($bln)
	{
		echo GetPeriod(urldecode($bln));
	}
  
  /* Attendance */
  function list_att($report=NULL, $tgl=NULL, $div=NULL, $sec=NULL, $pos=NULL, $grade=NULL)
  {
  	permission();
  	$data['path_file'] = $this->filename;

		if($this->input->post("start_att") || $tgl) {
    	if($tgl) {
    		$exp = explode("~", $tgl);
    		$data['start_date']=$exp[0];
    		$data['end_date']=$exp[1];
    	} else {
				$data['start_date'] = $this->input->post("start_att");
	      if(!$this->input->post("end_att")) $data['end_date'] = $data['start_date'];
	      //else if(!$exp[1]) $data['end_date'] = $exp[0];
	      else $data['end_date'] = $this->input->post("end_att");
	    }
      
      $data['period'] = GetMonth(substr($data['end_date'],5,2))." ".substr($data['end_date'],0,4);
    } else {
	    $dt = "Dec 2015";//date("M Y");
	  	$period = GetPeriod($dt);
	  	$data['start_date']=substr($period,0,10);
	  	$data['end_date']=substr($period,11,10);
	  	$data['period']=$dt;
	  }
	  
	  $data['jum_kolom'] = 8;
	  $tgl=$data['start_date']."~".$data['end_date'];
	  $data['div']=$this->input->post('s_div') ? $this->input->post('s_div') : $div;
	  $data['sec']=$this->input->post('s_sec') ? $this->input->post('s_sec') : $sec;
	  $data['pos']=$this->input->post('s_pos') ? $this->input->post('s_pos') : $pos;
	  $data['grade']=$this->input->post('s_grade') ? $this->input->post('s_grade') : $grade;
	  
	  $this->load->model('attendance_model','att');
  	$param = array("tgl"=> $tgl, "report"=> 1, "divisi"=> $data['div'], "section"=> $data['sec'], "position"=> $data['pos'], "grade"=> $data['grade']);
  	$list = $this->att->get_datatables($param);
	  $dataz = array();
	  $no = 0;
	  
	  foreach ($list->result() as $r) {
	    $no++;
	    $dataz[] = array($no, $r->ext_id, $r->person_nm, $r->jh, $r->off, $r->cuti, $r->ijin, $r->sakit, $r->alpa);
	  }
	  
	  $data['list'] = $dataz;
	  
	  $data['report'] = $report;
	  if(!$report) $this->load->view('r_list_att', $data);	  
	  else to_excel($this->load->view('r_list_att',$data), 'ReportAttendance');
  }
   
  /* Overtime */
  function list_ovt($report=NULL, $rekap_full=NULL, $tgl=NULL, $regs=NULL, $div=NULL, $sec=NULL, $pos=NULL, $grade=NULL)
  {
  	permission();
  	$data['path_file'] = $this->filename;

		if($this->input->post("start_att") || $tgl) {
    	if($tgl) {
    		$exp = explode("~", $tgl);
    		$data['start_date']=$exp[0];
    		$data['end_date']=$exp[1];
    	} else {
				$data['start_date'] = $this->input->post("start_att");
	      if(!$this->input->post("end_att")) $data['end_date'] = $data['start_date'];
	      //else if(!$exp[1]) $data['end_date'] = $exp[0];
	      else $data['end_date'] = $this->input->post("end_att");
	    }
      
      $data['period'] = GetMonth(substr($data['end_date'],5,2))." ".substr($data['end_date'],0,4);
    } else {
	    $dt = "Dec 2015";//date("M Y");
	  	$period = GetPeriod($dt);
	  	$data['start_date']=substr($period,0,10);
	  	$data['end_date']=substr($period,11,10);
	  	$data['period']=$dt;
	  }
	  
	  $data['jum_kolom'] = 7;
	  $tgl=$data['start_date']."~".$data['end_date'];
	 	$data['regs']=$this->input->post('s_regs') ? $this->input->post('s_regs') : $regs;
	  $data['div']=$this->input->post('s_div') ? $this->input->post('s_div') : $div;
	  $data['sec']=$this->input->post('s_sec') ? $this->input->post('s_sec') : $sec;
	  $data['pos']=$this->input->post('s_pos') ? $this->input->post('s_pos') : $pos;
	  $data['grade']=$this->input->post('s_grade') ? $this->input->post('s_grade') : $grade;
	  $data['rekap_full']=$this->input->post('s_rekap_full') ? $this->input->post('s_rekap_full') : $rekap_full;
	  
	  $this->load->model('overtime_model','ovt');
  	$param = array("rekap"=> $data['rekap_full'], "tgl"=> $tgl, "regs"=> $data['regs'], "divisi"=> $data['div'], "section"=> $data['sec'], "position"=> $data['pos'], "grade"=> $data['grade']);
  	$exp = explode("~", $tgl);
  	$list = $this->ovt->get_datatables($param);
  	
	  $dataz = array();
	  $no = $data['tot_act'] = $data['tot_cal'] = $data['tot_amt'] = 0;
	  if($data['rekap_full']=="rekap") {
		  foreach ($list->result() as $r) {
		    $no++;
		    
		    //Acc Ovt
		    $acc=0;
		    $q = GetAll("kg_view_overtime", array("id_employee"=> "where/".$r->id_employee, "date_full >="=> "where/".$exp[0], "date_full <="=> "where/".$exp[1], "date_temp"=> "where/0000-00-00"));
		    foreach($q->result_array() as $s) {
		    	if($s['job_level'] != "nonmanagement") {
		    		if($s['ovt_hour_sum'] >= 2) $acc += $s['ovt_hour_sum'];
		    	} else $acc += $s['ovt_hour_cal'];
		    }
		    
		    $q = GetAll("kg_view_overtime", array("id_employee"=> "where/".$r->id_employee, "date_temp >="=> "where/".$exp[0], "date_temp <="=> "where/".$exp[1]));
		    foreach($q->result_array() as $s) {
		    	if($s['job_level'] != "nonmanagement") {
		    		if($s['ovt_hour_sum'] >= 2) $acc += $s['ovt_hour_sum'];
		    	} else $acc += $s['ovt_hour_cal'];
		    }
		    
		    if($r->job_level != "nonmanagement") {
		    	$upah = $acc * GetConfigDirect('rest_time');
		    } else $upah = $acc * ( GetGapok($r->id_employee, $exp[0]) + GetHA($r->id_employee, $exp[0]) ) / GetConfigDirect('total_hour_ovt');
		    $ot_rasio = GetOTRasio($r->id_employee, $exp[1]);//$upah / (GetGapok($r->id_employee, $exp[0]) + GetHA($r->id_employee, $exp[0]) + $upah) * 100;
		    //$edit = '<a class="btn btn-sm btn-primary" href="javascript:void(0);" onclick="detailOvertime('."'".$r->id_employee."'".')"><i class="glyphicon glyphicon-info-sign"></i> Detail</a>';
		    //$data[] = array($no, $r->ext_id, $r->person_nm, GetMonth(intval(substr($tgl,16,2))).' '.substr($tgl,11,4), $r->ovt_hour_sum, $acc, Decimal($ot_rasio)."%", Rupiah($upah), $edit);
		    $data['tot_act'] += Decimal($r->ovt_hour_sum,1);
		    $data['tot_cal'] += Decimal($acc);
		    $data['tot_amt'] += $upah;
		    $dataz[] = array($no, $r->ext_id, $r->person_nm, GetMonth(intval(substr($tgl,16,2))).' '.substr($tgl,11,4), Decimal($r->ovt_hour_sum,1), Decimal($acc), $ot_rasio, Rupiah($upah));
		  }
		  $data['list'] = $dataz;
		  
		  $data['report'] = $report;
		  if(!$report) $this->load->view('r_list_ovt', $data);
		  else to_excel($this->load->view('r_list_ovt', $data), 'ReportOvertimeRekap');
		  
		} else {
			foreach ($list->result() as $r) {
		    $no++;
		    $data['tot_act'] += Decimal($r->ovt_hour_sum,1);
		    $data['tot_cal'] += Decimal($r->ovt_hour_cal);
		    $dataz[] = array($no, $r->ext_id, $r->person_nm, GetDayName($r->date_full).", ".FormatTanggalShort($r->date_full), Decimal($r->ovt_hour_sum,1), Decimal($r->ovt_hour_cal), $r->ovt_reason, $r->ovt_detail_reason);
		  }
		  $data['list'] = $dataz;
		  
		  $data['report'] = $report;
		  if(!$report) $this->load->view('r_list_ovt_full', $data);
		  else to_excel($this->load->view('r_list_ovt_full', $data), 'ReportOvertimeFull');
		  
		}
	  
  }
  
  function detail_ovt($id_emp=NULL, $period=NULL)
  {
  	permission();
  	$data['path_file'] = $this->filename;
  	$data['emp'] = GetAll("kg_view_employee", array("person_id"=> "where/".$id_emp));
    $data['id_emp'] = $id_emp;
		$data['period'] = $period;
    //$data['detail'] = $this->att->get_by_id($id_emp, $param)->result_array();

  	$this->load->view('overtime_detail', $data);
  }
  
  function ajax_list_ovt($tgl=NULL, $regs=NULL, $div=NULL, $sec=NULL, $pos=NULL, $grade=NULL)
  {
  	permission();
  	$this->load->model('overtime_model','ovt');
  	$param = array("tgl"=> $tgl, "regs"=> $regs, "divisi"=> $div, "section"=> $sec, "position"=> $pos, "grade"=> $grade);
  	$exp = explode("~", $tgl);
  	/*$config=array();
  	$q = GetAll("kg_config");
  	foreach($q->result_array() as $r) {
  		if(preg_match('/jam_/', $r['title'])) $config[$r['title']] = $r['value'];
  	}*/
  	//print_mz($config);
	  $list = $this->ovt->get_datatables($param);
	  $data = array();
	  $no = $_POST['start'];
	  foreach ($list->result() as $r) {
	    $no++;
	    
	    //Acc Ovt
	    $acc=0;
	    $q = GetAll("kg_view_overtime", array("id_employee"=> "where/".$r->id_employee, "date_full >="=> "where/".$exp[0], "date_full <="=> "where/".$exp[1], "date_temp"=> "where/0000-00-00"));
	    foreach($q->result_array() as $s) {
	    	if($s['job_level'] != "nonmanagement") {
	    		if($s['ovt_hour_sum'] >= 2) $acc += $s['ovt_hour_sum'];
	    	} else $acc += $s['ovt_hour_cal'];
	    }
	    
	    $q = GetAll("kg_view_overtime", array("id_employee"=> "where/".$r->id_employee, "date_temp >="=> "where/".$exp[0], "date_temp <="=> "where/".$exp[1]));
	    foreach($q->result_array() as $s) {
	    	if($s['job_level'] != "nonmanagement") {
	    		if($s['ovt_hour_sum'] >= 2) $acc += $s['ovt_hour_sum'];
	    	} else $acc += $s['ovt_hour_cal'];
	    }
	    
	    if($r->job_level != "nonmanagement") {
	    	$upah = $acc * GetConfigDirect('rest_time');
	    } else $upah = $acc * ( GetGapok($r->id_employee, $exp[0]) + GetHA($r->id_employee, $exp[0]) ) / GetConfigDirect('total_hour_ovt');
	    $ot_rasio = GetOTRasio($r->id_employee, $exp[1]);//$upah / (GetGapok($r->id_employee, $exp[0]) + GetHA($r->id_employee, $exp[0]) + $upah) * 100;
	    $edit = '<a class="btn btn-sm btn-primary" href="javascript:void(0);" onclick="detailOvertime('."'".$r->id_employee."'".')"><i class="glyphicon glyphicon-info-sign"></i> Detail</a>';
	    //$data[] = array($no, $r->ext_id, $r->person_nm, GetMonth(intval(substr($tgl,16,2))).' '.substr($tgl,11,4), $r->ovt_hour_sum, $acc, Decimal($ot_rasio)."%", Rupiah($upah), $edit);
	    $data[] = array($no, $r->ext_id, $r->person_nm, GetMonth(intval(substr($tgl,16,2))).' '.substr($tgl,11,4), Decimal($r->ovt_hour_sum,1), Decimal($acc), $ot_rasio, Rupiah($upah), $edit);
	  }
	
	  $output = array(
	                  "draw" => $_POST['draw'],
	                  "recordsTotal" => $this->ovt->count_all($param),
	                  "recordsFiltered" => $this->ovt->count_all($param),
	                  "data" => $data
	                 );
	  //output to json format
	  echo json_encode($output);
  }
  
  function ajax_list_detail_ovt($tgl=NULL, $id_emp=NULL)
  {
  	permission();
  	$this->load->model('overtime_model','ovt');
  	$param = array("tgl"=> $tgl, "id_emp"=> $id_emp, "detailz"=> 1);
	  $list = $this->ovt->get_datatables($param);
	  $data = array();
	  $no = $_POST['start'];
	  foreach ($list->result() as $r) {
	    $no++;
	    $edit = '<a class="btn btn-sm btn-primary" href="javascript:void(0);" onclick="detailAtt('."'".$r->id."'".')"><i class="glyphicon glyphicon-info-sign"></i> Detail</a>';
	    $data[] = array($no, GetDayName($r->date_full).", ".FormatTanggalShort($r->date_full), $r->ovt_hour_sum, Decimal($r->ovt_hour_cal), $r->ovt_reason, $r->ovt_detail_reason);
	  }
	
	  $output = array(
	                  "draw" => $_POST['draw'],
	                  "recordsTotal" => $this->ovt->count_all($param),
	                  "recordsFiltered" => $this->ovt->count_all($param),
	                  "data" => $data
	                 );
	  //output to json format
	  echo json_encode($output);
  }
  
  function export_ovt_full($tgl=NULL, $regs=NULL, $div=NULL, $sec=NULL, $pos=NULL, $grade=NULL)
  {
  	$this->load->model('overtime_model','ovt');
  	$param = array("tgl"=> $tgl, "rekap"=> "full", "regs"=> $regs, "divisi"=> $div, "section"=> $sec, "position"=> $pos, "grade"=> $grade);
  	$exp = explode("~", $tgl);
  	$list = $this->ovt->get_datatables($param);
	  $dataz = array();
	  $no = 0;//$_POST['start'];
	  foreach ($list->result() as $r) {
	    $no++;
	    $dataz[] = array($no, $r->ext_id, $r->person_nm, GetDayName($r->date_full).", ".FormatTanggalShort($r->date_full), Decimal($r->ovt_hour_sum,1), Decimal($r->ovt_hour_cal), $r->ovt_reason, $r->ovt_detail_reason);
	  }
	  $data['list'] = $dataz;
	  //print_mz($data['list']);
  	$html = $this->load->view('export_overtime_full',$data);
		to_excel($html, 'ReportOvertimeFull');
  }
  
  function export_ovt_rekap($tgl=NULL, $regs=NULL, $div=NULL, $sec=NULL, $pos=NULL, $grade=NULL)
  {
  	$this->load->model('overtime_model','ovt');
  	$param = array("tgl"=> $tgl, "rekap"=> "rekap", "regs"=> $regs, "divisi"=> $div, "section"=> $sec, "position"=> $pos, "grade"=> $grade);
  	$exp = explode("~", $tgl);
  	$list = $this->ovt->get_datatables($param);
	  $dataz = array();
	  $no = 0;//$_POST['start'];
	  foreach ($list->result() as $r) {
	    $no++;
	    
	    //Acc Ovt
	    $acc=0;
	    $q = GetAll("kg_view_overtime", array("id_employee"=> "where/".$r->id_employee, "date_full >="=> "where/".$exp[0], "date_full <="=> "where/".$exp[1], "date_temp"=> "where/0000-00-00"));
	    foreach($q->result_array() as $s) {
	    	if($s['job_level'] != "nonmanagement") {
	    		if($s['ovt_hour_sum'] >= 2) $acc += $s['ovt_hour_sum'];
	    	} else $acc += $s['ovt_hour_cal'];
	    }
	    
	    $q = GetAll("kg_view_overtime", array("id_employee"=> "where/".$r->id_employee, "date_temp >="=> "where/".$exp[0], "date_temp <="=> "where/".$exp[1]));
	    foreach($q->result_array() as $s) {
	    	if($s['job_level'] != "nonmanagement") {
	    		if($s['ovt_hour_sum'] >= 2) $acc += $s['ovt_hour_sum'];
	    	} else $acc += $s['ovt_hour_cal'];
	    }
	    
	    if($r->job_level != "nonmanagement") {
	    	$upah = $acc * GetConfigDirect('rest_time');
	    } else $upah = $acc * ( GetGapok($r->id_employee, $exp[0]) + GetHA($r->id_employee, $exp[0]) ) / GetConfigDirect('total_hour_ovt');
	    $ot_rasio = GetOTRasio($r->id_employee, $exp[1]);//$upah / (GetGapok($r->id_employee, $exp[0]) + GetHA($r->id_employee, $exp[0]) + $upah) * 100;
	    //$edit = '<a class="btn btn-sm btn-primary" href="javascript:void(0);" onclick="detailOvertime('."'".$r->id_employee."'".')"><i class="glyphicon glyphicon-info-sign"></i> Detail</a>';
	    $dataz[] = array($no, $r->ext_id, $r->person_nm, GetMonth(intval(substr($tgl,16,2))).' '.substr($tgl,11,4), Decimal($r->ovt_hour_sum,1), Decimal($acc), $ot_rasio, Number($upah));
	  }
	  $data['list'] = $dataz;
	  //print_mz($data['list']);
  	$html = $this->load->view('export_overtime_rekap',$data);
		to_excel($html, 'ReportOvertimeRekap');
  }
  
  
  /* Cuti */
  function list_cuti($report=NULL, $tgl=NULL, $div=NULL, $sec=NULL, $pos=NULL, $grade=NULL)
  {
  	permission();
  	$data['path_file'] = $this->filename;

    if($this->input->post("start_att") || $tgl) {
    	if($tgl) {
    		$exp = explode("~", $tgl);
    		$data['start_date']=$exp[0];
    		$data['end_date']=$exp[1];
    	} else {
				$data['start_date'] = $this->input->post("start_att");
	      if(!$this->input->post("end_att")) $data['end_date'] = $data['start_date'];
	      //else if(!$exp[1]) $data['end_date'] = $exp[0];
	      else $data['end_date'] = $this->input->post("end_att");
	    }
      
      $data['period'] = GetMonth(substr($data['end_date'],5,2))." ".substr($data['end_date'],0,4);
    } else {
    	$dt = "Dec 2015";//date("M Y");
	  	$period = GetPeriod($dt);
	  	$data['start_date']=substr($period,0,10);
	  	$data['end_date']=substr($period,11,10);
	  	$data['period']=$dt;
	  }
	  
	  $data['jum_kolom'] = 5;
	  $tgl=$data['start_date']."~".$data['end_date'];
	  $data['div']=$this->input->post('s_div') ? $this->input->post('s_div') : $div;
	  $data['sec']=$this->input->post('s_sec') ? $this->input->post('s_sec') : $sec;
	  $data['pos']=$this->input->post('s_pos') ? $this->input->post('s_pos') : $pos;
	  $data['grade']=$this->input->post('s_grade') ? $this->input->post('s_grade') : $grade;
	  
	  $this->load->model('cuti_model','cuti');
  	$param = array("tgl"=> $tgl, "divisi"=> $data['div'], "section"=> $data['sec'], "position"=> $data['pos'], "grade"=> $data['grade']);
  	$list = $this->cuti->get_datatables($param);
	  $dataz = array();
	  $no = 0;
	  
	  foreach ($list->result() as $r) {
	    $no++;
	    $sisa_cuti = GetSisaCuti($r->id_employee, $data['start_date']);
	    $terpakai = GetSum("kg_cuti", "hari_ref", array("tgl_start >="=> "where/".$data['start_date'], "tgl_start <="=> "where/".$data['end_date'], "create_user_id"=> "where/".$r->id_employee, "cuti_status"=> "where/Approve"), "value");
	    $dataz[] = array($no, $r->ext_id, $r->person_nm, $sisa_cuti, $terpakai, ($sisa_cuti - $terpakai));
	  }
	  
	  $data['list'] = $dataz;
	  
	  $data['report'] = $report;
	  if(!$report) $this->load->view('r_list_cuti', $data);	  
	  else to_excel($this->load->view('r_list_cuti',$data), 'ReportLeave');
  }
  
  /* Shift */
  function list_shift($report=NULL, $tgl=NULL, $div=NULL, $sec=NULL, $pos=NULL, $grade=NULL)
  {
  	permission();
  	$data['path_file'] = $this->filename;

    if($this->input->post("start_att") || $tgl) {
    	if($tgl) {
    		$exp = explode("~", $tgl);
    		$data['start_date']=$exp[0];
    		$data['end_date']=$exp[1];
    	} else {
				$data['start_date'] = $this->input->post("start_att");
	      if(!$this->input->post("end_att")) $data['end_date'] = $data['start_date'];
	      //else if(!$exp[1]) $data['end_date'] = $exp[0];
	      else $data['end_date'] = $this->input->post("end_att");
	    }
      
      $data['period'] = GetMonth(substr($data['end_date'],5,2))." ".substr($data['end_date'],0,4);
    } else {
    	$dt = "Dec 2015";//date("M Y");
	  	$period = GetPeriod($dt);
	  	$data['start_date']=substr($period,0,10);
	  	$data['end_date']=substr($period,11,10);
	  	$data['period']=$dt;
	  }
	  
	  $data['jum_kolom'] = 5;
	  $tgl=$data['start_date']."~".$data['end_date'];
	  $data['div']=$this->input->post('s_div') ? $this->input->post('s_div') : $div;
	  $data['sec']=$this->input->post('s_sec') ? $this->input->post('s_sec') : $sec;
	  $data['pos']=$this->input->post('s_pos') ? $this->input->post('s_pos') : $pos;
	  $data['grade']=$this->input->post('s_grade') ? $this->input->post('s_grade') : $grade;
	  
	  $this->load->model('shift_model','shift');
  	$param = array("bulan"=> substr($data['start_date'],5,2), "tahun"=> substr($data['start_date'],0,4), "report"=> 1, "divisi"=> $data['div'], "section"=> $data['sec'], "position"=> $data['pos'], "grade"=> $data['grade']);
  	$list = $this->shift->get_datatables($param);
	  $dataz = array();
	  $no = 0;
	  foreach ($list->result_array() as $r) {
	    $no++;
	    $dataz[$r['id_employee']][] = $r;
	  }
	  
	  $param = array("bulan"=> substr($data['end_date'],5,2), "tahun"=> substr($data['end_date'],0,4), "report"=> 1, "divisi"=> $data['div'], "section"=> $data['sec'], "position"=> $data['pos'], "grade"=> $data['grade']);
  	$list = $this->shift->get_datatables($param);
	  foreach ($list->result_array() as $r) {
	    $dataz[$r['id_employee']][] = $r;
	  }
	  
	  //print_mz($dataz);
	  $data['list'] = $dataz;
	  
	  $data['report'] = $report;
	  if(!$report) $this->load->view('r_list_shift', $data);	  
	  else to_excel($this->load->view('r_list_shift',$data), 'ReportSummaryOfShift');
  }
}
?>