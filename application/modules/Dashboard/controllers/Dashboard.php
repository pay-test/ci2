<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class dashboard extends MX_Controller {
	
	var $title = "Dashboard";
	var $filename = "dashboard";
	public $data;
	function __construct()
	{
		parent::__construct();
		//$this->load->model("model_admin_all");
	}
	
	function index()
	{
		$this->data['title'] = $this->title;
    $webmaster_id = permission();
    if($webmaster_id > 1) {
	    $nik=$hadir=$off=$alpa=$where=$rasio=array();
	    $bawahan = CekBawahanByGrade($webmaster_id);
	    //supaya User Login di Bar pertama
	    $bawahan[] = array("grade"=> 100, "id_emp"=> "-1", "color"=> "000000");
	    
	    if(count($bawahan) == 1 && $webmaster_id != 1) $this->data['flagz'] = 1;
	    else $this->data['flagz'] = 0;
	    //die($data['flagz']."D");
	    rsort($bawahan);
	    //print_mz($bawahan);
	    $avg_rasio=0;$legend=$downline=array();
	    foreach($bawahan as $val) {
	    	$b=$val['id_emp'];
	    	if($b=="-1") $b=$webmaster_id;
	    	else $downline[] = $b;
	    	$ext_id = GetValue("ext_id", "hris_persons", array("person_id"=> "where/".$b));
	    	$person_nm = GetValue("person_nm", "hris_persons", array("person_id"=> "where/".$b));
	    	//$nik[] = "'".$ext_id."-".$person_nm."'";
	    	//$nik[] = "'".$ext_id."'";
	    	$new_nm="";
	    	$exp = explode(" ", $person_nm);
	    	foreach($exp as $e) {
	    		if(strlen($new_nm." ".$e) > 12) break;
	    		else $new_nm .= $e." ";
	    	}
	    	$nik[] = "'".$new_nm."'";
	    	$where[] = "'".$b."'";
	    	//$val_rasio = str_replace("%","",GetOTRasio($b, "2015-12-15"));
	    	//if($val_rasio > 0) $avg_rasio += $val_rasio;
	    	//$rasio[$b] = "{titlez: '".$person_nm."', y: ".str_replace("%","",GetOTRasio($b, "2015-12-15")).", id_emp: '".$b."', color: '#".$val['color']."'}";
	    	//if($val['grade'] != 100) $legend[$val['grade']] = "<label class='legend_grafik' style='background:#".$val['color'].";'>&nbsp;</label><label class='title_legend_grafik'>GRADE ".$val['grade']."</label>";
	
	    }
	    $this->data['width_legend'] = count($legend) * 10.5;
	    $this->data['legend'] = join('',$legend);
	    //$this->data['avg_rasio'] = Decimal($avg_rasio / count($bawahan));
	    //$this->data['limit_ot'] = GetConfigDirect("limit_ot_rasio_grafik");
	    //print_mz($nik);
	    $wherez = join($where,",");
	    $q = $this->db->query("select person_nm,id_employee,ext_id,SUM(sakit) as sakit,SUM(cuti) as cuti,SUM(ijin) as ijin, SUM(alpa) as alpa,SUM(off) as off, SUM(jh) as jh
	    from kg_view_attendance where id_employee in (".$wherez.") group by id_employee order by grade_job_class desc, id_employee desc");
	    foreach($q->result_array() as $r) {
				$id_emp = $r['id_employee'];
	    	if($id_emp==$webmaster_id) $id_emp = "-1";
	    	$hadir[$id_emp] = "{titlez: '".$r['person_nm']."', y: ".$r['jh'].", id_emp: '".$r['id_employee']."'}";
	    	$off[$id_emp] = "{titlez: '".$r['person_nm']."', y: ".$r['off'].", id_emp: '".$r['id_employee']."'}";
	    	$alpa[$id_emp] = "{titlez: '".$r['person_nm']."', y: ".$r['alpa'].", id_emp: '".$r['id_employee']."'}";
	    }
	    //ksort($hadir);
	    //ksort($off);
	    //ksort($alpa);
	    
	    $this->data['nik'] = join($nik, ',');
	    $this->data['hadir'] = join($hadir, ',');
	    $this->data['off'] = join($off, ',');
	    $this->data['alpa'] = join($alpa, ',');
	    $this->data['rasio'] = join($rasio, ',');
		}
		$this->_render_page($this->filename.'/index', $this->data);
	}
	
	function index_slide($tgl=NULL)
	{
		if(!$tgl) $tgl=$this->input->post("date_slide");
		if(!$tgl) $tgl=date("Y-m-d");
		$this->data['title'] = $this->title;
    $webmaster_id = permission();
    if($webmaster_id > 1) {
	    $nik=$hadir=$off=$alpa=$where=$rasio=array();
	    $bawahan = CekBawahanByGrade($webmaster_id);
	    //supaya User Login di Bar pertama
	    $bawahan[] = array("grade"=> 100, "id_emp"=> "-1", "color"=> "000000");
	    
	    if(count($bawahan) == 1 && $webmaster_id != 1) $this->data['flagz'] = 1;
	    else $this->data['flagz'] = 0;
	    //die($data['flagz']."D");
	    rsort($bawahan);
	    //print_mz($bawahan);
	    $avg_rasio=0;$legend=$downline=array();
	    foreach($bawahan as $val) {
	    	$b=$val['id_emp'];
	    	if($b=="-1") $b=$webmaster_id;
	    	else $downline[] = $b;
	    	$ext_id = GetValue("ext_id", "hris_persons", array("person_id"=> "where/".$b));
	    	$person_nm = GetValue("person_nm", "hris_persons", array("person_id"=> "where/".$b));
	    	//$nik[] = "'".$ext_id."-".$person_nm."'";
	    	//$nik[] = "'".$ext_id."'";
	    	$new_nm="";
	    	$exp = explode(" ", $person_nm);
	    	foreach($exp as $e) {
	    		if(strlen($new_nm." ".$e) > 12) break;
	    		else $new_nm .= $e." ";
	    	}
	    	$nik[] = "'".$new_nm."'";
	    	$where[] = "'".$b."'";
	    	//$val_rasio = str_replace("%","",GetOTRasio($b, "2015-12-15"));
	    	//if($val_rasio > 0) $avg_rasio += $val_rasio;
	    	//$rasio[$b] = "{titlez: '".$person_nm."', y: ".str_replace("%","",GetOTRasio($b, "2015-12-15")).", id_emp: '".$b."', color: '#".$val['color']."'}";
	    	//if($val['grade'] != 100) $legend[$val['grade']] = "<label class='legend_grafik' style='background:#".$val['color'].";'>&nbsp;</label><label class='title_legend_grafik'>GRADE ".$val['grade']."</label>";
	
	    }
	    $this->data['width_legend'] = count($legend) * 10.5;
	    $this->data['legend'] = join('',$legend);
	    //$this->data['avg_rasio'] = Decimal($avg_rasio / count($bawahan));
	    //$this->data['limit_ot'] = GetConfigDirect("limit_ot_rasio_grafik");
	    //print_mz($nik);
	    $wherez = join($where,",");
	    $q = $this->db->query("select person_nm,id_employee,ext_id,SUM(sakit) as sakit,SUM(cuti) as cuti,SUM(ijin) as ijin, SUM(alpa) as alpa,SUM(off) as off, SUM(jh) as jh
	    from kg_view_attendance where id_employee in (".$wherez.") group by id_employee");
	    foreach($q->result_array() as $r) {
				$id_emp = $r['id_employee'];
	    	if($id_emp==$webmaster_id) $id_emp = "-1";
	    	$hadir[$id_emp] = "{titlez: '".$r['person_nm']."', y: ".$r['jh'].", id_emp: '".$r['id_employee']."'}";
	    	$off[$id_emp] = "{titlez: '".$r['person_nm']."', y: ".$r['off'].", id_emp: '".$r['id_employee']."'}";
	    	$alpa[$id_emp] = "{titlez: '".$r['person_nm']."', y: ".$r['alpa'].", id_emp: '".$r['id_employee']."'}";
	    }
	    ksort($hadir);
	    ksort($off);
	    ksort($alpa);
	    
	    $this->data['nik'] = join($nik, ',');
	    $this->data['hadir'] = join($hadir, ',');
	    $this->data['off'] = join($off, ',');
	    $this->data['alpa'] = join($alpa, ',');
	    $this->data['rasio'] = join($rasio, ',');
	    
	    //Pie Slide no Slide
	    $slide=$no_slide=0;
	    $this->data['chart_ada']=0;
	    $list_data_tabel = "<table class='table-detailOT'><tr><th colspan='5' class='center' style='border-bottom:1px solid #fff;'>List of Employee (Slide & no Slide)</th></tr>";
	    $list_data_tabel .= "<tr><th class='center'>No</th><th>NIK</th><th>Nama</th><th class='center'>Actual In</th><th class='center'>Actual Out</th></tr>";
	    $q = GetAll("kg_view_attendance", array("date_full"=> "where/".$tgl, "grade_job_class"=> "order/desc", "id_employee "=> "order/desc"), array("id_employee"=> $downline));
	    if($q->num_rows() > 0) {
		    $this->data['chart_ada']=1;
		    foreach($q->result_array() as $key=> $r) {
		    	if($r['no_slide']) $no_slide++;
		    	else $slide++;
		    	$list_data_tabel .= "<tr><td class='center'>".++$key."</td><td>".$r['ext_id']."</td><td>".$r['person_nm']."</td><td class='center'>".$r['scan_masuk']."</td><td class='center'>".$r['scan_pulang']."</td></tr>";
		    }
		  } else $list_data_tabel .= "<tr><td class='center' colspan='5'>No Data</td></tr>";
	    $list_data_tabel .= "</table>";
	    $this->data['list_data_tabel']=$list_data_tabel;
	    $this->data['chart_pie'] = "{name: 'Slide', y: ".$slide.", no_slide: 0, color: '#008000'}, {name: 'No Slide', y: ".$no_slide.", no_slide: 1, color: '#fd0000'}";
			$this->data['tgl'] = $tgl;
		}
		$this->_render_page($this->filename.'/index_slide', $this->data);
	}
	
	function overtime()
	{
		$this->data['title'] = $this->title;
    $webmaster_id = permission();
    $nik=$hadir=$off=$alpa=$where=$rasio=array();
    $bawahan = CekBawahanByGrade($webmaster_id);
    //supaya User Login di Bar pertama
    $bawahan[] = array("grade"=> 100, "id_emp"=> "-1", "color"=> "000000");
    
    if(count($bawahan) == 1 && $webmaster_id != 1) $this->data['flagz'] = 1;
    else $this->data['flagz'] = 0;
    //die($data['flagz']."D");
    rsort($bawahan);
    //print_mz($bawahan);
    $avg_rasio=0;$legend=array();
    foreach($bawahan as $val) {
    	$b=$val['id_emp'];
    	if($b=="-1") $b=$webmaster_id;
    	$ext_id = GetValue("ext_id", "hris_persons", array("person_id"=> "where/".$b));
    	$person_nm = GetValue("person_nm", "hris_persons", array("person_id"=> "where/".$b));
    	//$nik[] = "'".$ext_id."-".$person_nm."'";
    	//$nik[] = "'".$ext_id."'";
    	$new_nm="";
    	$exp = explode(" ", $person_nm);
    	foreach($exp as $e) {
    		if(strlen($new_nm." ".$e) > 12) break;
    		else $new_nm .= $e." ";
    	}
    	$nik[] = "'".$new_nm."'";
    	$where[] = "'".$b."'";
    	$val_rasio = str_replace("%","",GetOTRasio($b, "2015-12-15"));
    	if($val_rasio > 0) $avg_rasio += $val_rasio;
    	$rasio[$b] = "{titlez: '".$person_nm."', y: ".str_replace("%","",GetOTRasio($b, "2015-12-15")).", id_emp: '".$b."', color: '#".$val['color']."'}";
    	if($val['grade'] != 100) $legend[$val['grade']] = "<label class='legend_grafik' style='background:#".$val['color'].";'>&nbsp;</label><label class='title_legend_grafik'>GRADE ".$val['grade']."</label>";
    }
    $this->data['avg_rasio'] = Decimal($avg_rasio / count($bawahan));
    $this->data['limit_ot'] = GetConfigDirect("limit_ot_rasio_grafik");
    
    if(count($legend) <= 2) $this->data['width_legend'] = 35;
    else $this->data['width_legend'] = count($legend) * 10.5;
    $legend['target'] = "<div class='clearfix'></div><label class='legend_grafik' style='width:30px;border-top:2px dashed #ff0000;height:3px;margin-top:8px;'>&nbsp;</label><label class='title_legend_grafik' style='width:100px;'>TARGET (".$this->data['limit_ot']."%)</label>";
    $legend['average'] = "<label class='legend_grafik' style='width:30px;border-top:2px dashed #008000;height:3px;margin-top:8px;'>&nbsp;</label><label class='title_legend_grafik' style='width:110px;'>AVERAGE (".$this->data['avg_rasio']."%)</label>";
    $this->data['legend'] = join('',$legend);
    
    //print_mz($nik);
    $wherez = join($where,",");
    $q = $this->db->query("select person_nm,id_employee,ext_id,SUM(sakit) as sakit,SUM(cuti) as cuti,SUM(ijin) as ijin, SUM(alpa) as alpa,SUM(off) as off, SUM(jh) as jh
    from kg_view_attendance where id_employee in (".$wherez.") group by id_employee");
    foreach($q->result_array() as $r) {
			$id_emp = $r['id_employee'];
    	if($id_emp==$webmaster_id) $id_emp = "-1";
    	$hadir[$id_emp] = "{titlez: '".$r['person_nm']."', y: ".$r['jh'].", id_emp: '".$r['id_employee']."'}";
    	$off[$id_emp] = "{titlez: '".$r['person_nm']."', y: ".$r['off'].", id_emp: '".$r['id_employee']."'}";
    	$alpa[$id_emp] = "{titlez: '".$r['person_nm']."', y: ".$r['alpa'].", id_emp: '".$r['id_employee']."'}";
    }
    ksort($hadir);
    ksort($off);
    ksort($alpa);
    
    $this->data['nik'] = join($nik, ',');
    $this->data['hadir'] = join($hadir, ',');
    $this->data['off'] = join($off, ',');
    $this->data['alpa'] = join($alpa, ',');
    $this->data['rasio'] = join($rasio, ',');
    
    $this->_render_page($this->filename.'/index_ot', $this->data);
	}
	
	
	
	function list_data($id_emp=NULL, $period=NULL)
  {
  	$period="2015-11-16~2015-12-15";
  	permission();
  	$data['emp'] = GetAll("kg_view_employee", array("person_id"=> "where/".$id_emp));
    $data['id_emp'] = $id_emp;
		$data['period'] = $period;
		$data['date'] = substr($period,11,10);
		$exp = explode("-", $data['date']);
		$data['last_date'] = date("Y-m-d", mktime(0, 0, 0, $exp[1], $exp[0], $exp[2]));
		$data['ot_rasio'] = GetOTRasio($id_emp, $data['date']);
		$data['ot_rasio_last'] = GetOTRasio($id_emp, $data['last_date']);
		$data['ot_rasio_selisih'] = str_replace("%","",$data['ot_rasio']) - str_replace("%","",$data['ot_rasio_last']);
		if(str_replace("%","",$data['ot_rasio']) > str_replace("%","",$data['ot_rasio_last'])) $data['high_low'] = "higher";
		else $data['high_low'] = "lower";
    //$data['detail'] = $this->att->get_by_id($id_emp, $param)->result_array();

  	$this->load->view('list_data', $data);
  }
  
  function ajax_list_detail_att($tgl=NULL, $id_emp=NULL)
  {
  	permission();
  	$this->load->model('attendance_model','att');
  	$param = array("tgl"=> $tgl, "id_emp"=> $id_emp, "detailz_grafik"=> 1);
	  $list = $this->att->get_datatables($param);
	  $data = array();
	  $no = $_POST['start'];
	  foreach ($list->result() as $r) {
	    $no++;
	    $shift = GetValue("tgl_".intval($r->tanggal), "kg_jadwal_shift", array("id_employee"=> "where/".$id_emp, "bulan"=> "where/".$r->bulan, "tahun"=> "where/".$r->tahun));
	    $edit = '<a class="btn btn-sm btn-primary" href="javascript:void(0);" onclick="editAtt('."'".$r->id."'".', '."'".$tgl."'".')"><i class="glyphicon glyphicon-pencil"></i> Edit</a>';
	    $data[] = array($no, GetDayName($r->date_full).", ".FormatTanggalShort($r->date_full), $r->jh, $r->off, $r->alpa, $r->scan_masuk, $r->scan_pulang, strtoupper($shift));
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

	function _render_page($view, $data=null, $render=false)
  {
      // $this->viewdata = (empty($data)) ? $this->data: $data;
      // $view_html = $this->load->view($view, $this->viewdata, $render);
      // if (!$render) return $view_html;
      $data = (empty($data)) ? $this->data : $data;
      if ( ! $render)
      {
          $this->load->library('template');

              if(in_array($view, array('dashboard/index', 'dashboard/index_slide', 'dashboard/index_ot')))
              {
              	$this->template->add_css('assets/plugins/bootstrap-datepicker/css/datepicker.css');				
					      $this->template->add_js('assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');

                $this->template->set_layout('default');
                $this->template->add_js('assets/plugins/highcharts/highcharts.js');
                $this->template->add_js('assets/plugins/highcharts/modules/exporting.js');
              }

          if ( ! empty($data['title']))
          {
              $this->template->set_title($data['title']);
          }

          $this->template->load_view($view, $data);
      }
      else
      {
          return $this->load->view($view, $data, TRUE);
      }
  }
}
?>