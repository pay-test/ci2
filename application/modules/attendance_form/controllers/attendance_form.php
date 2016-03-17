<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class attendance_form extends MX_Controller {
	
	var $title = "Attendance";
  var $filename = "attendance_form";
	var $period = "Dec 2015";
	public $data;
	function __construct()
	{
		parent::__construct();
    $this->lang->load('attendance');
	}
	
	function index()
	{
    $data['title'] = $this->title;
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
  
  /* Overtime */
  function overtime($flag=NULL)
  {
  	$webmaster_id=permission();
  	$data['path_file'] = $this->filename;
  	$data['flag'] = $flag;
		
  	$this->_render_page('index', $data);
  }
  
  function form_overtime($flag=NULL)
  {
  	$webmaster_id = permission();
  	$data['path_file'] = $this->filename;
  	$data['flag'] = $data['id'] = $flag;
  	
  	$data['tgl'] = "";
  	$data['time_in']=$data['time_out']=$data['scan_in']=$data['scan_out']=$data['start_ovt']=$data['end_ovt']="";
  	$data['ovt_flag']=$data['ovt_reason']=$data['ovt_detail_reason']=$data['ovt_feedback']=$data['ovt_status']="";
  	$data['id_emp']=$data['ot_rasio']=$data['actual_hours']=$data['cal_hours']=$data['revisi']="";
  	$data['opt_reason'] = GetOptReason();
  	$data['opt_ovt_flag'] = array(array("J","J"),	array("O","O"), array("T","T"),	array("N.A.","N.A."));
  	$data['opt_status'] = GetOptStatusForm();
  	
		if($flag) {
			$q = GetAll("kg_view_overtime", array("id"=> "where/".$flag));
			foreach($q->result_array() as $r) {
				if(in_array($r['create_user_id'], CekBawahan($webmaster_id)) || $r['create_user_id']==$webmaster_id) {
					$data['employee_nm'] = $r['ext_id']." - ".$r['person_nm'];
					$data['id_emp'] = $r['id_employee'];
					$data['tgl'] = $r['date_full'];
					$data['time_in'] = $r['time_in'];
					$data['time_out'] = $r['time_out'];
					$data['scan_in'] = $r['scan_masuk'];
					$data['scan_out'] = $r['scan_pulang'];
					$data['start_ovt'] = $r['start_ovt_ref'] ? $r['start_ovt_ref'] : $r['start_ovt'];
					$data['end_ovt'] = $r['end_ovt_ref'] ? $r['end_ovt_ref'] : $r['end_ovt'];
					$data['ovt_flag'] = $r['ovt_flag'];
					$data['ovt_reason'] = $r['id_reason'];
					$data['ovt_detail_reason'] = $r['ovt_detail_reason'];
					$data['ovt_feedback'] = $r['ovt_feedback'];
					$data['ovt_status'] = $r['ovt_status'];
					
					if($r['create_user_id']==$webmaster_id) {
						$data['flag']="";
						$this->db->where("id", $flag);
						$this->db->update("kg_overtime", array("is_read"=> 1));
					}
					
					//Cek Revisi
					if(($r['start_ovt'] != $r['start_ovt_ref'] || $r['end_ovt'] != $r['end_ovt_ref']) && $r['ovt_status'] == "Approve") $revisi=1;
					else $revisi=0;
					$data['revisi'] = $revisi;
					
					//Detail OT
					$data['ot_rasio']=GetOTRasio($r['id_employee'], $r['date_full']);
					$sum_cal = GetOTCal($r['id_employee'], $r['date_full']);
					$exp = explode("~", $sum_cal);
					$data['actual_hours'] = $exp[0];
					$data['cal_hours'] = $exp[1];
					
				} else $data['flag'] = "";
			}
		}

  	$this->load->view('form_overtime', $data);
  }
  
  function history_overtime()
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
	    $dt = $this->period;//date("M Y");
	  	$period = GetPeriod($dt);
	  	$data['start_date']=substr($period,0,10);
	  	$data['end_date']=substr($period,11,10);
	  	$data['period']=$dt;
	  }
  	
  	$this->load->view('history_overtime', $data);
  }
    
	function ajax_list_ovt($tgl=NULL)
  {
  	permission();
  	$this->load->model('overtime_model','ovt');
  	if($this->session->userdata('person_id') > 1) $person_id=$this->session->userdata('person_id');
  	else $person_id=0;
  	$param = array("tgl"=> $tgl, "history"=> 1, "person_id"=> $person_id);
	  $list = $this->ovt->get_datatables($param);
	  $data = array();
	  $no = $_POST['start'];
	  foreach ($list->result() as $r) {
	    $no++;
	    if($r->ovt_status=="Waiting") $status = '<a class="error" href="javascript:void(0);" onclick="detailOvt('."'".$r->id."'".')">Waiting</a>';
	    else $status=$r->ovt_status;
	    $data[] = array($no, GetDayName($r->date_full).", ".FormatTanggalShort($r->date_full), $r->ovt_hour_sum, $r->ovt_reason, $r->ovt_detail_reason);
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
  
  function update_overtime()
	{
		$webmaster_id = permission();
		$id = $this->input->post('id');
		
		//Cek Scan
		$scan_in=$this->input->post('scan_in');
		$scan_out=$this->input->post('scan_out');
		$date_full=$this->input->post('date_full');
		$id_kehadirandetil = GetValue("id", "kg_view_attendance", array("id_employee"=> "where/".$webmaster_id, "date_full"=> "where/".$date_full));
		
		if(($scan_in > 0 && $scan_out > 0) || $id) {
			$data['time_in'] = $this->input->post('time_in');
			$data['time_out'] = $this->input->post('time_out');
			$data['start_ovt'] = $this->input->post('start_ovt');
			$data['end_ovt'] = $this->input->post('end_ovt');
			$data['ovt_hour_sum'] = GetHourSum($this->input->post('start_ovt'), $this->input->post('end_ovt'));
			//$data['ovt_flag'] = $this->input->post('ovt_flag');
			$data['id_reason'] = $this->input->post('ovt_reason');
			$data['ovt_detail_reason'] = $this->input->post('ovt_detail_reason');
			$data['ovt_feedback'] = $this->input->post('ovt_feedback');
			//print_mz($data);
			
			$data['modify_date'] = date("Y-m-d H:i:s");
			//print_mz($data);
			if($id > 0)
			{
				$cek_status = GetValue("ovt_status", "kg_overtime", array("id"=> "where/".$id));
				//if($cek_status=="Waiting") {
					if($this->input->post('ovt_status')) $data['ovt_status'] = $this->input->post('ovt_status');
					$data['modify_user_id'] = $webmaster_id;
					
					if($data['ovt_status']=="Approve") {
						unset($data['start_ovt']);
						unset($data['end_ovt']);
						$data['start_ovt_ref'] = $this->input->post('start_ovt');
						$data['end_ovt_ref'] = $this->input->post('end_ovt');
						$id_emp = $this->input->post("id_emp");
						$exp = explode("-", $date_full);
						$cek_jadwal = GetValue("tgl_".intval($exp[2]), "kg_jadwal_shift", array("id_employee"=> "where/".$id_emp, "bulan"=> "where/".$exp[1], "tahun"=> "where/".$exp[0]));
			    	if($cek_jadwal==3) {
			    		if(CekLemburAuto($id_emp, $date_full)) $cek_jadwal=33;
			    	}
			    	
			    	$data['ovt_hour_cal'] = GetAccOvt($data['ovt_hour_sum'], $cek_jadwal);
			    	
			    	$exp = explode("-", $date_full);
			    	//Cek Closing Period
			    	$tgl_closing = GetConfigDirect('close_period');
			    	$start_period = GetConfigDirect('att_start_period');
			    	if(intval($exp[2]) > $tgl_closing) $closing = date("Y-m-d", mktime(0, 0, 0, $exp[1]+1, $tgl_closing, $exp[0]));
			    	else $closing = $exp[0]."-".$exp[1]."-".$tgl_closing;
			    	
			    	if(date("Y-m-d") > $closing) {
			    		$data['date_temp'] = str_replace("-".$tgl_closing, "-".$start_period, $closing);
			    	}
			    	
					}
					//print_mz($data);
					$this->db->where("id", $id);
					$this->db->update("kg_overtime", $data);
					//Admin Log
					//$logs = $this->db->last_query();
					//$this->model_admin_all->LogActivities($webmaster_id,$this->tabel,$this->db->insert_id(),$logs,lang($this->filename),$data[$this->title_table],$this->filename,"Add");
					
					//$this->session->set_flashdata("message", lang('edit')." ".$this->title." ".lang('msg_sukses'));
					$this->session->set_userdata("message", "Submitted");
				//}
			}
			else
			{
				$data['id_kehadirandetil'] = $id_kehadirandetil;
				$data['create_user_id'] = $webmaster_id;
				$data['create_date'] = $data['modify_date'];
				//print_mz($data);
				$this->db->insert("kg_overtime", $data);
				$id = $this->db->insert_id();
				//Admin Log
				//$logs = $this->db->last_query();
				//$this->model_admin_all->LogActivities($webmaster_id,$this->tabel,$this->db->insert_id(),$logs,lang($this->filename),$data[$this->title_table],$this->filename,"Add");
				
				$this->session->set_userdata("message", "Your request has been submitted");
			}
		} else $this->session->set_userdata("message", "Failed");
		
		//die($this->session->flashdata('message')."S");
		redirect(site_url('attendance_form/overtime/'.$id));
	}
	
	function approval_overtime()
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
	    $dt = $this->period;//date("M Y");
	  	$period = GetPeriod($dt);
	  	$data['start_date']=substr($period,0,10);
	  	$data['end_date']=substr($period,11,10);
	  	$data['period']=$dt;
	  }

  	$this->load->view('approval_overtime', $data);
  }
    
	function ajax_list_ovt_app($tgl=NULL)
  {
  	$webmaster_id=permission();
  	$this->load->model('overtime_model','ovt');
  	if($this->session->userdata('person_id') > 1) $person_id=$this->session->userdata('person_id');
  	else $person_id=0;
  	$param = array("tgl"=> $tgl, "approve"=> 1, "person_id"=> $person_id);
	  $list = $this->ovt->get_datatables($param);
	  $data = array();
	  $no = $_POST['start'];
	  foreach ($list->result() as $r) {
	    $no++;
	    if($r->ovt_status=="Waiting") {
	    	$cls="blue";
	    	if($r->id_employee!=$webmaster_id) $r->ovt_status="Waiting Your Approval";
	    }
	    else if($r->ovt_status=="Approve") $cls="green";
	    else if($r->ovt_status=="Reject") $cls="red";
	    $status = '<a class="'.$cls.'" href="javascript:void(0);" onclick="detailOvt('."'".$r->id."'".')">'.$r->ovt_status.'</a>';
	    $data[] = array($no, GetDayName($r->date_full).", ".FormatTanggalShort($r->date_full), $r->start_ovt." - ".$r->end_ovt, $r->start_ovt_ref." - ".$r->end_ovt_ref, $r->ovt_hour_sum, $r->ovt_reason, $r->ovt_feedback, $status);
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
	
	function get_period($bln)
	{
		echo GetPeriod(urldecode($bln));
	}
  
  function get_current_schedule($tgl)
	{
		$webmaster_id = permission();
		$exp = explode("-", $tgl);
		$cek_jadwal = GetValue("tgl_".intval($exp[2]), "kg_jadwal_shift", array("id_employee"=> "where/".$webmaster_id, "bulan"=> "where/".$exp[1], "tahun"=> "where/".$exp[0]));
		if($cek_jadwal == "reg") {echo GetConfigDirect('reguler_start')."~".GetConfigDirect('reguler_end');}
		else if($cek_jadwal == "off") echo "00:00~00:00";
		else {
			$time = GetConfigDirect('shift_'.$cek_jadwal);
			if($cek_jadwal == 3) $time .= "~".GetConfigDirect('shift_1');
			else $time .= "~".GetConfigDirect('shift_'.($cek_jadwal+1));
			echo $time;
		}
		
		//Scan in out
		$q = GetAll("kg_view_attendance", array("id_employee"=> "where/".$webmaster_id, "date_full"=> "where/".$tgl));
		if($q->num_rows() > 0) {
			foreach($q->result_array() as $r) {
				if($r['scan_masuk']=="-") $r['scan_masuk']="--:--";
				if($r['scan_pulang']=="-") $r['scan_pulang']="--:--";
				echo "~".$r['scan_masuk']."~".$r['scan_pulang'];
			}
		} else echo "~--:--~--:--";
	}
  
  
}
?>