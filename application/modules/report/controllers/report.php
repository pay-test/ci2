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
	
	function overtime()
	{
    $data['title'] = $this->title;
    $data['filename'] = $this->filename;
    permission();
		$this->_render_page('index', $data);
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
  
    
  /* Overtime */
  function list_ovt()
  {
  	permission();
  	$data['path_file'] = $this->filename;

		if($this->input->post("start_att")) {
			$data['start_date'] = $this->input->post("start_att");
      if(!$this->input->post("end_att")) $data['end_date'] = $data['start_date'];
      //else if(!$exp[1]) $data['end_date'] = $exp[0];
      else $data['end_date'] = $this->input->post("end_att");
      
      $data['period'] = GetMonth(substr($data['end_date'],5,2))." ".substr($data['end_date'],0,4);
    } else {
	    $dt = "Dec 2015";//date("M Y");
	  	$period = GetPeriod($dt);
	  	$data['start_date']=substr($period,0,10);
	  	$data['end_date']=substr($period,11,10);
	  	$data['period']=$dt;
	  }
	  
	  $data['opt_divisi'] = GetOptDivision();
	  $data['opt_regs'] = GetOptShiftReguler();
	  $data['opt_grade'] = GetOptGrade();
	  $data['opt_pos'] = GetOptPositionLevel();
	  $data['s_regs'] = $this->input->post("s_regs") ? $this->input->post("s_regs") : "0";
	  $data['s_div'] = $this->input->post("s_div") ? $this->input->post("s_div") : "0";
	  $data['s_sec'] = $this->input->post("s_sec") ? $this->input->post("s_sec") : "0";
	  $data['s_pos'] = $this->input->post("s_pos") ? $this->input->post("s_pos") : "0";
	  $data['s_grade'] = $this->input->post("s_grade") ? $this->input->post("s_grade") : "0";

  	$this->load->view('overtime', $data);
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
}
?>