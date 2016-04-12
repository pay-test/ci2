<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class attendance extends MX_Controller {
	
	var $title = "Attendance";
  var $filename = "attendance";
	
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
  
  function get_period($bln)
	{
		echo GetPeriod(urldecode($bln));
	}
  
  /* Attendance */
  function list_attendance($period=NULL)
  {
  	permission();
    $data['path_file'] = $this->filename;

		if($this->input->post("start_att") || $period) {
			if($period) {
				$exp = explode("~", $period);
				$start_date = $exp[0];
				$end_date = $exp[1];
			} else {
				$start_date = $this->input->post("start_att");
				$end_date = $this->input->post("end_att");
			}
			$data['start_date'] = $start_date;
      if(!$end_date) $data['end_date'] = $data['start_date'];
      //else if(!$exp[1]) $data['end_date'] = $exp[0];
      else $data['end_date'] = $end_date;
      
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

  	$this->load->view('attendance', $data);
  }
  
  function detail_attendance($id_emp=NULL, $period=NULL)
  {
  	permission();
  	$data['emp'] = GetAll("kg_view_employee", array("person_id"=> "where/".$id_emp));
    $data['id_emp'] = $id_emp;
		$data['period'] = $period;
    //$data['detail'] = $this->att->get_by_id($id_emp, $param)->result_array();

  	$this->load->view('attendance_detail', $data);
  }
  
  function edit_attendance($id, $period=NULL)
  {
  	permission();
    $q = GetAll("kg_kehadirandetil", array("id"=> "where/".$id));
		$r = $q->result_array();
		$data['val'] = $r[0];
		$data['tgl'] = $r[0]['tahun']."-".$r[0]['bulan']."-".$r[0]['tanggal'];
		$data['absensi'] = array(array("jh","ATTEND"),
													array("off","OFF"),
													array("cuti","CUTI"),
													array("ijin","IJIN"),
													array("sakit","SICK"),
													array("alpa","ABSENCE"));
													//print_mz($data['absensi']);
    for($i=0;$i<=20;$i++) {
			$opt[$i] = $i;
		}
		$data['opt_lembur'] = $opt;
		$data['param_btn'] = $r[0]['id_employee']."/".$period;
    $this->load->view('attendance_edit', $data);
    //$this->_render_page($this->filename.'_edit',$data);
  }
    
	function ajax_list_att($tgl=NULL, $regs=NULL, $div=NULL, $sec=NULL, $pos=NULL, $grade=NULL)
  {
  	permission();
  	$this->load->model('attendance_model','att');
  	$param = array("tgl"=> $tgl, "regs"=> $regs, "divisi"=> $div, "section"=> $sec, "position"=> $pos, "grade"=> $grade);
	  $list = $this->att->get_datatables($param);
	  $data = array();
	  $no = $_POST['start'];
	  foreach ($list->result() as $r) {
	    $no++;
	    $edit = '<a class="btn btn-sm btn-primary" href="javascript:void(0);" onclick="detailAtt('."'".$r->a_id."'".')"><i class="glyphicon glyphicon-info-sign"></i> Detail</a>';
	    $data[] = array($no, $r->ext_id, $r->person_nm, $r->jhk, $r->jh, $r->late, $r->off, $r->cuti, $r->ijin, $r->sakit, $r->alpa, $edit);
	  }
	
	  $output = array(
	                  "draw" => $_POST['draw'],
	                  "recordsTotal" => $this->att->count_all($param),
	                  "recordsFiltered" => $this->att->count_all($param),
	                  "data" => $data
	                 );
	  //output to json format
	  echo json_encode($output);
  }
  
  function ajax_list_detail_att($tgl=NULL, $id_emp=NULL)
  {
  	$person_id=permission();
  	$this->load->model('attendance_model','att');
  	$param = array("tgl"=> $tgl, "id_emp"=> $id_emp, "detailz"=> 1);
	  $list = $this->att->get_datatables($param);
	  $data = array();
	  $no = $_POST['start'];
	  foreach ($list->result() as $r) {
	    $no++;
	    $shift = GetValue("tgl_".intval($r->tanggal), "kg_jadwal_shift", array("id_employee"=> "where/".$id_emp, "bulan"=> "where/".$r->bulan, "tahun"=> "where/".$r->tahun));
	    if($person_id > 1 && $person_id == $r->person_id) $edit = '';
	    else $edit = '<a class="btn btn-sm btn-primary" href="javascript:void(0);" onclick="editAtt('."'".$r->id."'".', '."'".$tgl."'".')"><i class="glyphicon glyphicon-pencil"></i> Edit</a>';
	    $data[] = array($no, GetDayName($r->date_full).", ".FormatTanggalShort($r->date_full), $r->jh, $r->off, $r->cuti, $r->ijin, $r->sakit, $r->alpa, $r->scan_masuk, $r->scan_pulang, strtoupper($shift), $edit);
	  }
	
	  $output = array(
	                  "draw" => $_POST['draw'],
	                  "recordsTotal" => $this->att->count_all($param),
	                  "recordsFiltered" => $this->att->count_all($param),
	                  "data" => $data
	                 );
	  //output to json format
	  echo json_encode($output);
  }
  
  
  
  /* Shift */
  function list_shift($periode=NULL)
  {
  	permission();
  	$data['path_file'] = $this->filename;

		if($this->input->post('period') || $periode) {
    	$period=$periode ? urldecode($periode) : urldecode($this->input->post('period'));
    } else {
    	$period="Dec 2015";//date("M Y");
    }
	  $data['period']=$period;
	  
	  $data['opt_divisi'] = GetOptDivision();
	  $data['opt_regs'] = GetOptShiftReguler();
	 	$data['opt_grade'] = GetOptGrade();
	  $data['opt_pos'] = GetOptPositionLevel();
	  $data['s_regs'] = $this->input->post("s_regs") ? $this->input->post("s_regs") : "0";
	  $data['s_div'] = $this->input->post("s_div") ? $this->input->post("s_div") : "0";
	  $data['s_sec'] = $this->input->post("s_sec") ? $this->input->post("s_sec") : "0";
	  $data['s_pos'] = $this->input->post("s_pos") ? $this->input->post("s_pos") : "0";
	  $data['s_grade'] = $this->input->post("s_grade") ? $this->input->post("s_grade") : "0";

  	$this->load->view('shift', $data);
  }
  
  function detail_shift($id_emp=NULL, $tgl=NULL)
  {
  	permission();
  	$data['shift'] = GetAll("kg_view_shift", array("id_employee"=> "where/".$id_emp, "tahun"=> "where/".substr($tgl,0,4), "bulan"=> "where/".substr($tgl,5,2)));
  	$data['shift_2'] = GetAll("kg_view_shift", array("id_employee"=> "where/".$id_emp, "tahun"=> "where/".substr($tgl,11,4), "bulan"=> "where/".substr($tgl,16,2)));
  	//print_mz($data['shift_2']);
  	$data['emp'] = GetAll("kg_view_employee", array("person_id"=> "where/".$id_emp));
    $data['period'] = GetMonth(intval(substr($tgl,16,2))).' '.substr($tgl,11,4);
  	$data['tgl'] = $tgl;
  	
  	$shift_ril = GetAll("kg_jadwal_shift_ril", array("id_employee"=> "where/".$id_emp, "tahun"=> "where/".substr($tgl,0,4), "bulan"=> "where/".substr($tgl,5,2)));
  	foreach($shift_ril->result_array() as $r) {
  		$data['shift_ril'] = $r;
  	}
  	
  	$shift_ril = GetAll("kg_jadwal_shift_ril", array("id_employee"=> "where/".$id_emp, "tahun"=> "where/".substr($tgl,11,4), "bulan"=> "where/".substr($tgl,16,2)));
  	foreach($shift_ril->result_array() as $r) {
  		$data['shift_ril_2'] = $r;
  	}
  	
  	$this->load->view('shift_detail', $data);
  }
    
	function ajax_list_shift($period=NULL, $regs=NULL, $div=NULL, $sec=NULL, $pos=NULL, $grade=NULL)
  {
  	permission();
  	$this->load->model('shift_model','shift');
  	$period=urldecode($period);
  	$bulan = GetMonthIndex(substr($period,0,3));
    $tahun = substr($period,4,4);
    $param = array("bulan"=> $bulan, "tahun"=> $tahun, "regs"=> $regs, "divisi"=> $div, "section"=> $sec, "position"=> $pos, "grade"=> $grade);
	  $list = $this->shift->get_datatables($param);
	  $data = array();
	  $no = $_POST['start'];
	  foreach ($list->result() as $r) {
	    $no++;
	    $edit = '<a class="btn btn-sm btn-primary" href="javascript:void(0);" onclick="detailShift('."'".$r->id_employee."'".', '."'".GetPeriod($period)."'".')"><i class="glyphicon glyphicon-info-sign"></i> Detail</a>';
	    $data[] = array($no, $r->ext_id, $r->person_nm, GetMonth(intval($r->bulan)).' '.$r->tahun, $r->jum_p, $r->jum_s, $r->jum_m, $r->jum_ns, $r->jum_off, $edit);
	  }
	
	  $output = array(
	                  "draw" => $_POST['draw'],
	                  "recordsTotal" => $this->shift->count_all($param),
	                  "recordsFiltered" => $this->shift->count_all($param),
	                  "data" => $data
	                 );
	  //output to json format
	  echo json_encode($output);
  }
  
  function update_att()
	{
		$webmaster_id = permission();
		$id = $this->input->post('id');
		$data['id_employee'] = $this->input->post('id_employee');
		$data['jhk'] = 1;//$this->input->post('jhk');
		$data['no_slide'] = 0; //Sudah Absen
		$data['sakit']=$data['cuti']=$data['ijin']=$data['alpa']=$data['off']=0;
		$data['pg']=$data['pc']=$data['jh']=$data['hr']=$data['lembur']=$data['late']=0;
		$absen = $this->input->post('absen');
		if($absen) {
		 $data[$absen] = 1;
		 if($absen=="terlambat") $data["jh"] = 1;
		} else $data["jh"] = 1;
		$tgl = $this->input->post('tgl');
		$exp = explode("-", $tgl);
		$data['tanggal'] = $exp[2];
		$data['bulan'] = $exp[1];
		$data['tahun'] = $exp[0];
		
		$data['scan_masuk'] = $this->input->post("scan_masuk");
		$data['scan_pulang'] = $this->input->post("scan_pulang");
		//$data['terlambat'] = $this->input->post("terlambat");
		/*$data['ot_incidental'] = $this->input->post("ot_incidental");
		$data['acc_ot_incidental'] = $this->input->post("acc_ot_incidental");
		$data['ot_allow_shift'] = $this->input->post("ot_allow_shift");
		$data['acc_allow_shift'] = $this->input->post("acc_allow_shift");
		$data['ot_cont_allow'] = $this->input->post("ot_cont_allow");
		$data['acc_ot_cont_allow'] = $this->input->post("acc_ot_cont_allow");
		if($data['ot_incidental'] || $data['ot_allow_shift'] || $data['ot_cont_allow'])
		{
			$data['lembur'] = 1;
			$data['alasan_lembur'] = $this->input->post("alasan_lembur");
		}
		else
		{
			$data['lembur'] = $data['alasan_lembur'] = 0;
		}
		
		$data['keterangan'] = $this->input->post('keterangan');*/
		
		$data['modify_date'] = date("Y-m-d H:i:s");
		//print_mz($data);
		if($id > 0)
		{
			$data['modify_user_id'] = $webmaster_id;
			$this->db->where("id", $id);
			$this->db->update("kg_kehadirandetil", $data);
			
			//Admin Log
			//$logs = $this->db->last_query();
			//$this->model_admin_all->LogActivities($webmaster_id,$this->tabel,$this->db->insert_id(),$logs,lang($this->filename),$data[$this->title_table],$this->filename,"Add");
			
			//$this->session->set_flashdata("message", lang('edit')." ".$this->title." ".lang('msg_sukses'));
		}
		else
		{
			$data['create_user_id'] = $webmaster_id;
			$data['create_date'] = $data['modify_date'];
			$this->db->insert("kg_kehadirandetil", $data);
			//$id = $this->db->insert_id();
			//Admin Log
			//$logs = $this->db->last_query();
			//$this->model_admin_all->LogActivities($webmaster_id,$this->tabel,$this->db->insert_id(),$logs,lang($this->filename),$data[$this->title_table],$this->filename,"Add");
			
			//$this->session->set_flashdata("message", lang('add')." ".$this->title." ".lang('msg_sukses'));
		}
		
	}
	
	function update_shift()
	{
		$webmaster_id = permission();
		$id = $this->input->post('id');
		$GetColumns = GetColumns("kg_jadwal_shift");
		$bulan=$this->input->post("bulan");
		$tahun=$this->input->post("tahun");
		$jml_hari = GetJumHari($bulan, $tahun);
		$flag=0;
		foreach($GetColumns as $r)
		{
			if($r['Field'] == "tgl_".$jml_hari) $flag=1;
			
			if(!$flag)
			{
				$data[$r['Field']] = strtolower($this->input->post($r['Field']));
			}
			else if($r['Field'] == "tgl_".$jml_hari)
			{
				$data[$r['Field']] = strtolower($this->input->post($r['Field']));
			}
		}
		//print_mz($data);
		$hitung=array_count_values($data);
		//print_mz($hitung);
		if(!isset($hitung['1'])) $hitung['1']=0;
		if(!isset($hitung['2'])) $hitung['2']=0;
		if(!isset($hitung['3'])) $hitung['3']=0;
		if(!isset($hitung['reg'])) $hitung['reg']=0;
		if(!isset($hitung['off'])) $hitung['off']=0;	
		
		$data['jum_p']=$hitung['1'];
		$data['jum_s']=$hitung['2'];
		$data['jum_m']=$hitung['3'];
		$data['jum_ns']=$hitung['reg'];
		$data['jum_off']=$hitung['off'];
		//$data['bulan']=$period[0];
		//$data['tahun']=$period[1];
		$data['modify_date'] = date("Y-m-d H:i:s");
		
		//print_mz($data);
		if($id > 0)
		{
			$data['modify_user_id'] = $webmaster_id;
			$this->db->where("id", $id);
			$this->db->update("kg_jadwal_shift", $data);
			
			//Admin Log
			//$logs = $this->db->last_query();
			//$this->model_admin_all->LogActivities($webmaster_id,$this->tabel,$this->db->insert_id(),$logs,lang($this->filename),$data[$this->title_table],$this->filename,"Add");
			
			//$this->session->set_flashdata("message", lang('edit')." ".$this->title." ".lang('msg_sukses'));
		}
		else
		{
			$data['create_user_id'] = $webmaster_id;
			$data['create_date'] = $data['modify_date'];
			//print_mz($data);
			$this->db->insert("kg_jadwal_shift", $data);
			$id = $this->db->insert_id();
			//Admin Log
			//$logs = $this->db->last_query();
			//$this->model_admin_all->LogActivities($webmaster_id,$this->tabel,$this->db->insert_id(),$logs,lang($this->filename),$data[$this->title_table],$this->filename,"Add");
			
			//$this->session->set_flashdata("message", lang('add')." ".$this->title." ".lang('msg_sukses'));
		}
		
		$this->detail_shift($id);
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
	  $data['s_regs'] = $this->input->post("s_regs") ? $this->input->post("s_regs") : "shift";
	  $data['s_div'] = $this->input->post("s_div");
	  $data['s_sec'] = $this->input->post("s_sec");

  	$this->load->view('overtime', $data);
  }
  
  function detail_ovt($id_emp=NULL, $period=NULL)
  {
  	permission();
  	$data['emp'] = GetAll("kg_view_employee", array("person_id"=> "where/".$id_emp));
    $data['id_emp'] = $id_emp;
		$data['period'] = $period;
    //$data['detail'] = $this->att->get_by_id($id_emp, $param)->result_array();

  	$this->load->view('overtime_detail', $data);
  }
  
  function ajax_list_ovt($tgl=NULL, $regs=NULL, $div=NULL, $sec=NULL)
  {
  	permission();
  	$this->load->model('overtime_model','ovt');
  	$param = array("tgl"=> $tgl, "regs"=> $regs, "divisi"=> $div, "section"=> $sec);
	  $list = $this->ovt->get_datatables($param);
	  $data = array();
	  $no = $_POST['start'];
	  foreach ($list->result() as $r) {
	    $no++;
	    $edit = '<a class="btn btn-sm btn-primary" href="javascript:void(0);" onclick="detailOvertime('."'".$r->id_employee."'".')"><i class="glyphicon glyphicon-info-sign"></i> Detail</a>';
	    $data[] = array($no, $r->ext_id, $r->person_nm, GetMonth(intval(substr($tgl,16,2))).' '.substr($tgl,11,4), $r->ovt_hour_sum, $edit);
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
  	$person_id = permission();
  	$this->load->model('overtime_model','ovt');
  	$param = array("tgl"=> $tgl, "id_emp"=> $id_emp, "detailz"=> 1);
	  $list = $this->ovt->get_datatables($param);
	  $data = array();
	  $no = $_POST['start'];
	  foreach ($list->result() as $r) {
	    $no++;
	    $data[] = array($no, GetDayName($r->date_full).", ".FormatTanggalShort($r->date_full), $r->ovt_hour_sum, $r->ovt_flag, $r->ovt_reason, $r->ovt_detail_reason);
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
}
?>