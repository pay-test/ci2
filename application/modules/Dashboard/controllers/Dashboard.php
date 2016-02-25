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
    $nik=$hadir=$off=$alpa=$where=$rasio=array();
    $bawahan = CekBawahan($webmaster_id);
    //supaya User Login di Bar pertama
    $bawahan[] = "-1";
    sort($bawahan);
    foreach($bawahan as $b) {
    	if($b=="-1") $b=$webmaster_id;
    	$ext_id = GetValue("ext_id", "hris_persons", array("person_id"=> "where/".$b));
    	$person_nm = GetValue("person_nm", "hris_persons", array("person_id"=> "where/".$b));
    	//$nik[] = "'".$ext_id."-".$person_nm."'";
    	$nik[] = "'".$ext_id."'";
    	$where[] = "'".$b."'";
    	$rasio[$b] = "{y: ".str_replace("%","",GetOTRasio($b, "2015-12-15")).", id_emp: '".$b."'}";
    }
    $wherez = join($where,",");
    $q = $this->db->query("select id_employee,ext_id,SUM(sakit) as sakit,SUM(cuti) as cuti,SUM(ijin) as ijin, SUM(alpa) as alpa,SUM(off) as off, SUM(jh) as jh
    from kg_view_attendance where id_employee in (".$wherez.") group by id_employee");
    foreach($q->result_array() as $r) {
			$id_emp = $r['id_employee'];
    	if($id_emp==$webmaster_id) $id_emp = "-1";
    	$hadir[$id_emp] = "{y: ".$r['jh'].", id_emp: '".$r['id_employee']."'}";
    	$off[$id_emp] = "{y: ".$r['off'].", id_emp: '".$r['id_employee']."'}";
    	$alpa[$id_emp] = "{y: ".$r['alpa'].", id_emp: '".$r['id_employee']."'}";
    }
    ksort($hadir);
    ksort($off);
    ksort($alpa);
    
    $this->data['nik'] = join($nik, ',');
    $this->data['hadir'] = join($hadir, ',');
    $this->data['off'] = join($off, ',');
    $this->data['alpa'] = join($alpa, ',');
    $this->data['rasio'] = join($rasio, ',');
		$this->_render_page($this->filename.'/index', $this->data);
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
  	$param = array("tgl"=> $tgl, "id_emp"=> $id_emp, "detailz"=> 1);
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

              if(in_array($view, array('dashboard/index')))
              {
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