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

    function list_attendance($user="0",$tgl="0000-00-00",$dep="0",$shift="0",$status="0")
    {
        $data['user'] = $user;
        //$data['path_file'] = $this->filename.'_user_migrasi';
        $data['path_file'] = $this->filename.'_user';
        $data['main_content'] = $data['path_file'];
        $data['filename'] = $this->filename;
        $data['title'] = $this->title;
        //if($user == 0 && $tgl == "0000-00-00" && !$this->uri->segment(5)) $tgl = date("Y-m-d");
        $data['tgl'] = $tgl;
        $exp = explode("~", $tgl);
        $data['tgl_today'] = $exp[0];
        if($exp[0] != "0000-00-00")
        {
          $data['start_date'] = $exp[0];
          if(!isset($exp[1])) $data['end_date'] = $exp[0];
          else if(!$exp[1]) $data['end_date'] = $exp[0];
          else $data['end_date'] = $exp[1];
          if($exp[1]) $data['period'] = GetMonth(substr($exp[1],5,2))." ".substr($exp[1],0,4);
          else $data['period'] = "";
        } else {
        	$dt = "Dec 2015";//date("M Y");
        	$period = GetPeriod($dt);
        	$data['start_date']=substr($period,0,10);
        	$data['end_date']=substr($period,11,10);
        	$data['period']=$dt;
        }
        $data['shift'] = $shift;
        
        
        /* Mazhters */
        if($exp[0]==date("Y-m-d")) $tabel_view="kg_view_kehadiran_rutin";
        else $tabel_view="kg_view_kehadiran_mcci";
        /* End Mazhters */
        
        $path_paging = base_url().$this->filename."/main/".$user."/".$tgl."/".$dep."/".$shift."/".$status;
        $uri_segment = 8;
        $pg = $this->uri->segment($uri_segment);
        /*riza*/
        //$filter = array($tabel_view.".tahun"=> "order/asc", $tabel_view.".bulan"=> "order/asc", $tabel_view.".tanggal"=> "order/asc");
        $filter=array();
        $per_page=1000;
        //End Global
        
        $filter_where_in=array();
        
        $exp = explode("-",$exp[0]);
        if(!isset($exp[0])) $exp[0] = "";
        if(!isset($exp[1])) $exp[1] = "";
        if(!isset($exp[2])) $exp[2] = "";
        
        //if($exp[2] > 0) $filter = array("tahun"=> "order/asc", "bulan"=> "order/asc", "tanggal"=> "order/asc");
        //else $filter = array("id_employee"=> "order/asc", "tahun"=> "order/asc", "bulan"=> "order/asc", "tanggal"=> "order/asc");
        
        if($status)
        {
          if($status == "jh") $filter['terlambat !='] = "where/1";
          $filter[$status] = "where/1";
        }
        
        $kondisi="";
        if($status == "alpa2")
        {
            $tgl_alpa=array($exp[2], $exp_alpa[2]);
            $filter_where_in['tanggal'] = $tgl_alpa;
            
            $bln_alpa=array($exp[1], $exp_alpa[1]);
            $filter_where_in['bulan'] = $bln_alpa;
            
            $thn_alpa=array($exp[0], $exp_alpa[0]);
            $filter_where_in['tahun'] = $thn_alpa;
        }
        else
        {
            if($data['start_date'] == $data['end_date'])
            {
                if($exp[0] > 0)
                {
                    $filter[$tabel_view.'.tahun'] = "where/".$exp[0];$data['title'] = $this->title." ( ".$exp[0]." )";
                    if($exp[1] > 0)
                    {
                        $filter[$tabel_view.'.bulan'] = "where/".$exp[1];$data['title'] = $this->title." ( ".GetMonth(intval($exp[1]))." ".$exp[0]." )";
                        if($exp[2] > 0){$filter[$tabel_view.'.tanggal'] = "where/".$exp[2];$data['title'] = $this->title." ( ".$exp[2]." ".GetMonth(intval($exp[1]))." ".$exp[0]." )";}
                    }
                }
                else if($exp[1] > 0)
                {
                    $exp[0] = date("Y");
                    $filter['tahun'] = "where/".$exp[0];
                    $filter['bulan'] = "where/".$exp[1];$data['title'] = $this->title." ( ".GetMonth(intval($exp[1]))." ".$exp[0]." )";
                    if($exp[2] > 0){$filter['tanggal'] = "where/".$exp[2];$data['title'] = $this->title." ( ".$exp[2]." ".GetMonth(intval($exp[1]))." ".$exp[0]." )";}
                }
                else if($exp[2] > 0)
                {
                    $exp[0] = date("Y");$exp[1]=date("m");
                    $filter['tahun'] = "where/".$exp[0];
                    $filter['bulan'] = "where/".$exp[1];
                    $filter['tanggal'] = "where/".$exp[2];$data['title'] = $this->title." ( ".$exp[2]." ".GetMonth(intval($exp[1]))." ".$exp[0]." )";
                }
            }
        }
        
        if($dep) {
        	$filter['id_department'] = "where/".$dep;
        }
        
        if($user)
        {
            $temp_user=$user;
            $ex = explode("-",$user);
            $user=array();
            foreach($ex as $r)
            {
                $user[] = $r;
            }
            $filter_where_in['id_employee'] = $user;
            $user=$temp_user;
        }

        $data['spic'] = $user;
        $data['sdep'] = $dep;
        
        
        //Grup Admin
        $id_grup = $this->session->userdata("webmaster_grup");
        $where = "";
        if($id_grup == 4) $filter['nik'] = "where/-";
        $filter[$tabel_view.'.person_nm'] = "order/asc";

        //if(($exp[2] > 0 && ($data['start_date'] == $data['end_date'])) || $user)
        if($user) {
            $data['flag_tgl']=1;
            $data['grid'] = array("Nama Karyawan","Tanggal","Hadir","OFF","Cuti","Ijin","PHL","Sakit","Alpa","PG","Terlambat","Scan Masuk", "Scan Pulang", "Keterangan");
            $data['list'] = array("id_employee","tgl","jh","off","cuti","ijin","phl","sakit","alpa","potong_gaji","terlambat","scan_masuk", "scan_pulang", "keterangan");
            if($data['start_date'])
            {
                $filter['date_full >='] = "where/".$data['start_date'];
                $filter['date_full <='] = "where/".$data['end_date'];
            }
            
            if($shift) {
                //if($shift == "p") $where_shift = " (kg_jadwal_shift.tgl_".intval($exp[2])."='".$shift."' || kg_jadwal_shift.tgl_".intval($exp[2])."='ns')";
                if($shift == "p") $where_shift = " (kg_jadwal_shift.tgl_".intval($exp[2])."!='s' && kg_jadwal_shift.tgl_".intval($exp[2])."!='m')";
                else $where_shift = " kg_jadwal_shift.tgl_".intval($exp[2])."='".$shift."' ";
                $data['query_all'] = GetJoin($tabel_view, "jadwal_shift", "kg_jadwal_shift.id_employee=".$tabel_view.".id_employee and kg_jadwal_shift.bulan='".$exp[1]."' and kg_jadwal_shift.tahun='".$exp[0]."' and $where_shift", "inner", "*", $filter, $filter_where_in);
                $filter['limit'] = $pg."/".$per_page;                   
                $data['query_list'] = GetJoin($tabel_view, "jadwal_shift", "kg_jadwal_shift.id_employee=".$tabel_view.".id_employee and kg_jadwal_shift.bulan='".$exp[1]."' and kg_jadwal_shift.tahun='".$exp[0]."' and $where_shift", "inner", "*,".$tabel_view.".id as id_k", $filter, $filter_where_in);

            } else {
                $data['query_all'] = GetAll($tabel_view, $filter, $filter_where_in);
                $filter['limit'] = $pg."/".$per_page;
                $data['query_list'] = GetAll($tabel_view, $filter, $filter_where_in);
            }
        } else {
            if($data['start_date']) {
	            $filter['date_full >='] = "where/".$data['start_date'];
	            $filter['date_full <='] = "where/".$data['end_date'];
            }
            $data['flag_tgl']=0;
            $data['grid'] = array("Employee","JHK","Hadir","OFF","Cuti","Ijin","Sakit","Alpa");
            $data['list'] = array("person_nm","jhk","jh","off","cuti","ijin","sakit","alpa");
            
            $filter['id_employee'] = "group";
            $filter['person_nm'] = "group";
            $select = "id, person_nm, id_employee as a_id, SUM(jhk) as jhk, SUM(sakit) as sakit,
            SUM(cuti) as cuti,SUM(ijin) as ijin,SUM(phl) as phl,SUM(alpa) as alpa,SUM(off) as off,
            SUM(potong_gaji) as potong_gaji, SUM(pc) as pc, SUM(jh) as jh";
            //$data['query_all'] = GetAllSelect($tabel_view, $select, $filter);
            //lastq();
            //$filter['limit'] = $pg."/".$per_page;
            //$data['query_list'] = GetJoin("employee","attendance","attendance.id_employee=employee.id ".$kondisi, "left", $select, $filter, $filter_where_in);
            $data['query_list'] = GetAllSelect($tabel_view, $select, $filter);
            //die($this->db->last_query());
        }
        
        if(!$this->uri->segment(3) && !$this->uri->segment(4) && !$this->uri->segment(5)) $data['dis_tgl'] = "display:none;";
        else $data['dis_tgl'] = "display:''";
        
        //Page
        /*$pagination = Page($data['query_all']->num_rows(),$per_page,$pg,$path_paging,$uri_segment);
        if(!$pagination) $pagination = "<strong>1</strong>";
        $data['pagination'] = $pagination;*/
        //End Page

        $this->load->view('attendance', $data);
    }

    function detail($user="0",$tgl="0000-00-00",$dep="0",$shift="0",$status="0")
    {
        $data['user'] = $user;
        //$data['path_file'] = $this->filename.'_user_migrasi';
        $data['path_file'] = $this->filename.'_user';
        $data['main_content'] = $data['path_file'];
        $data['filename'] = $this->filename;
        $data['title'] = $this->title;
        //if($user == 0 && $tgl == "0000-00-00" && !$this->uri->segment(5)) $tgl = date("Y-m-d");
        $data['tgl'] = $tgl;
        $exp = explode("~", $tgl);
        $data['tgl_today'] = $exp[0];
        if($exp[0] != "0000-00-00")
        {
            $data['start_date'] = $exp[0];
            if(!isset($exp[1])) $data['end_date'] = $exp[0];
            else if(!$exp[1]) $data['end_date'] = $exp[0];
            else $data['end_date'] = $exp[1];
        }
        else $data['start_date']=$data['end_date']="";
        $data['shift'] = $shift;
        
        /* Mazhters */
        if($exp[0]==date("Y-m-d")) $tabel_view="kg_view_kehadiran_rutin";
        else $tabel_view="kg_view_kehadiran_mcci";
        /* End Mazhters */
        
        $path_paging = base_url().$this->filename."/main/".$user."/".$tgl."/".$dep."/".$shift."/".$status;
        $uri_segment = 8;
        $pg = $this->uri->segment($uri_segment);
        /*riza*/
        $filter = array($tabel_view.".tahun"=> "order/asc", $tabel_view.".bulan"=> "order/asc", $tabel_view.".tanggal"=> "order/asc");
        $per_page=500;
        //End Global
        
        $filter_where_in=array();
        $data['opt_pic'] = GetOptPIC();
        $data['opt_pic'][''] = "";
        $data['opt_dep'] = GetOptDepartment();
        //$data['opt_dep'][''] = "";
        $data['opt_tgl'] = GetOptDate();
        $data['opt_bln'] = GetOptMonth();
        $data['opt_thn'] = GetOptYear();
        
        $exp = explode("-",$exp[0]);
        if(!isset($exp[0])) $exp[0] = "";
        if(!isset($exp[1])) $exp[1] = "";
        if(!isset($exp[2])) $exp[2] = "";
        
        //if($exp[2] > 0) $filter = array("tahun"=> "order/asc", "bulan"=> "order/asc", "tanggal"=> "order/asc");
        //else $filter = array("id_employee"=> "order/asc", "tahun"=> "order/asc", "bulan"=> "order/asc", "tanggal"=> "order/asc");
        
        if($status)
        {
            /*if($status == "alpa2")
            {
                $kemarin = date("Y-m-d", mktime(0, 0, 0, $exp[1], $exp[2]-1, $exp[0]));
                $exp_alpa = explode("-", $kemarin);
                $filter["alpa"] = "where/1";
            }
            else */
            if($status == "jh") $filter['terlambat !='] = "where/1";
            $filter[$status] = "where/1";
        }
        
        $kondisi="";
        if($status == "alpa2")
        {
            $tgl_alpa=array($exp[2], $exp_alpa[2]);
            $filter_where_in['tanggal'] = $tgl_alpa;
            
            $bln_alpa=array($exp[1], $exp_alpa[1]);
            $filter_where_in['bulan'] = $bln_alpa;
            
            $thn_alpa=array($exp[0], $exp_alpa[0]);
            $filter_where_in['tahun'] = $thn_alpa;
        }
        else
        {
            if($data['start_date'] == $data['end_date'])
            {
                if($exp[0] > 0)
                {
                    $filter[$tabel_view.'.tahun'] = "where/".$exp[0];$data['title'] = $this->title." ( ".$exp[0]." )";
                    if($exp[1] > 0)
                    {
                        $filter[$tabel_view.'.bulan'] = "where/".$exp[1];$data['title'] = $this->title." ( ".GetMonth(intval($exp[1]))." ".$exp[0]." )";
                        if($exp[2] > 0){$filter[$tabel_view.'.tanggal'] = "where/".$exp[2];$data['title'] = $this->title." ( ".$exp[2]." ".GetMonth(intval($exp[1]))." ".$exp[0]." )";}
                    }
                }
                else if($exp[1] > 0)
                {
                    $exp[0] = date("Y");
                    $filter['tahun'] = "where/".$exp[0];
                    $filter['bulan'] = "where/".$exp[1];$data['title'] = $this->title." ( ".GetMonth(intval($exp[1]))." ".$exp[0]." )";
                    if($exp[2] > 0){$filter['tanggal'] = "where/".$exp[2];$data['title'] = $this->title." ( ".$exp[2]." ".GetMonth(intval($exp[1]))." ".$exp[0]." )";}
                }
                else if($exp[2] > 0)
                {
                    $exp[0] = date("Y");$exp[1]=date("m");
                    $filter['tahun'] = "where/".$exp[0];
                    $filter['bulan'] = "where/".$exp[1];
                    $filter['tanggal'] = "where/".$exp[2];$data['title'] = $this->title." ( ".$exp[2]." ".GetMonth(intval($exp[1]))." ".$exp[0]." )";
                }
            }
        }
        
        if($dep)
        {
            /*$ex = explode("-",$dep);
            $dep=array();
            foreach($ex as $r)
            {
                $dep[] = $r;
            }
            $filter_where_in['id_department'] = $dep;*/
            $filter['id_department'] = "where/".$dep;
        }
        //else $dep=array();
        
        if($user)
        {
            $temp_user=$user;
            $ex = explode("-",$user);
            $user=array();
            foreach($ex as $r)
            {
                $user[] = $r;
            }
            $filter_where_in['id_employee'] = $user;
            $user=$temp_user;
        }
        //else $user=array();

        $data['spic'] = $user;
        $data['sdep'] = $dep;
        /*$data['stgl'] = $exp[2];
        $data['sbln'] = $exp[1];
        $data['sthn'] = $exp[0];*/
        
        //Grup Admin
        $id_grup = $this->session->userdata("webmaster_grup");
        $where = "";
        if($id_grup == 4) $filter['nik'] = "where/-";
        //else if($id_grup == 3) $filter['urut_position <'] = "where/40";
        //else if($id_grup == 2) $filter['urut_position >='] = "where/40";
        //End
        //$filter['urut_position'] = "order/asc";
        $filter[$tabel_view.'.id_employee'] = "order/asc";

        //if(count($user) > 0 || $exp[2] > 0)
        if(($exp[2] > 0 && ($data['start_date'] == $data['end_date'])) || $user)
        {
        	$data['emp'] = GetAll("hris_persons", array("person_id"=> "where/".$user));
          $data['flag_tgl']=1;
          $data['grid'] = array("Date","Hadir","OFF","Cuti","Ijin","Sick","Alpa","Late","In","Out","Shift","Description");
          $data['list'] = array("tgl","jh","off","cuti","ijin","sakit","alpa","terlambat","scan_masuk", "scan_pulang", "shift", "keterangan");
          if($data['start_date'])
          {
              $filter['date_full >='] = "where/".$data['start_date'];
              $filter['date_full <='] = "where/".$data['end_date'];
          }
          
          if($shift) {
              //if($shift == "p") $where_shift = " (kg_jadwal_shift.tgl_".intval($exp[2])."='".$shift."' || kg_jadwal_shift.tgl_".intval($exp[2])."='ns')";
              if($shift == "p") $where_shift = " (kg_jadwal_shift.tgl_".intval($exp[2])."!='s' && kg_jadwal_shift.tgl_".intval($exp[2])."!='m')";
              else $where_shift = " kg_jadwal_shift.tgl_".intval($exp[2])."='".$shift."' ";
              $data['query_all'] = GetJoin($tabel_view, "jadwal_shift", "kg_jadwal_shift.id_employee=".$tabel_view.".id_employee and kg_jadwal_shift.bulan='".$exp[1]."' and kg_jadwal_shift.tahun='".$exp[0]."' and $where_shift", "inner", "*", $filter, $filter_where_in);
              $filter['limit'] = $pg."/".$per_page;                   
              $data['query_list'] = GetJoin($tabel_view, "jadwal_shift", "kg_jadwal_shift.id_employee=".$tabel_view.".id_employee and kg_jadwal_shift.bulan='".$exp[1]."' and kg_jadwal_shift.tahun='".$exp[0]."' and $where_shift", "inner", "*,".$tabel_view.".id as id_k", $filter, $filter_where_in);
          } else {
              $data['query_all'] = GetAll($tabel_view, $filter, $filter_where_in);
              $filter['limit'] = $pg."/".$per_page;
              $data['query_list'] = GetAll($tabel_view, $filter, $filter_where_in);
          }
        }
        else
        {
            if($data['start_date'])
            {
                $filter['date_full >='] = "where/".$data['start_date'];
                $filter['date_full <='] = "where/".$data['end_date'];
            }
            $data['flag_tgl']=0;
            $data['grid'] = array("Nama Karyawan","JHK","Hadir","OFF","Cuti","PHL","Ijin","Sakit","Alpa","Potong Gaji","Pulang Cepat");
            $data['list'] = array("person_nm","jhk","jh","off","cuti","phl","ijin","sakit","alpa","potong_gaji","pc");
            /*if($exp[1] > 0) $filter['group'] = array("employee.id","bulan","tahun");
            elseif($exp[0] > 0) $filter['group'] = array("employee.id","tahun");
            else */
            //$filter['group'] = array("employee.id","tahun");
            $filter['id_employee'] = "group";
            //else $filter['group'] = array("employee.id");
            /*$select = "employee.id as a_id,employee.name, kg_attendance.id, SUM(kg_attendance.jhk) as jhk, SUM(kg_attendance.sakit) as sakit,
            SUM(kg_attendance.cuti) as cuti,SUM(kg_attendance.ijin) as ijin,,SUM(kg_attendance.alpa) as alpa,SUM(kg_attendance.off) as off,
            SUM(kg_attendance.potong_gaji) as potong_gaji, SUM(kg_attendance.pc) as pc,SUM(kg_attendance.jh) as jh";
            $data['query_all'] = GetJoin("employee","attendance","attendance.id_employee=employee.id ".$kondisi, "left", $select, $filter, $filter_where_in);*/
            $select = "id, person_nm, id_employee as a_id, SUM(jhk) as jhk, SUM(sakit) as sakit,
            SUM(cuti) as cuti,SUM(ijin) as ijin,SUM(phl) as phl,SUM(alpa) as alpa,SUM(off) as off,
            SUM(potong_gaji) as potong_gaji, SUM(pc) as pc, SUM(jh) as jh";
            $data['query_all'] = GetAllSelect($tabel_view, $select, $filter);
            //lastq();
            $filter['limit'] = $pg."/".$per_page;
            //$data['query_list'] = GetJoin("employee","attendance","attendance.id_employee=employee.id ".$kondisi, "left", $select, $filter, $filter_where_in);
            $data['query_list'] = GetAllSelect($tabel_view, $select, $filter);
            //die($this->db->last_query());
        }
        
        if(!$this->uri->segment(3) && !$this->uri->segment(4) && !$this->uri->segment(5)) $data['dis_tgl'] = "display:none;";
        else $data['dis_tgl'] = "display:''";
        
        //Page
        $pagination = Page($data['query_all']->num_rows(),$per_page,$pg,$path_paging,$uri_segment);
        if(!$pagination) $pagination = "<strong>1</strong>";
        $data['pagination'] = $pagination;
        //End Page
        
        
        $this->_render_page($this->filename.'_detail', $data);
    }

    function overtime($user="0",$tgl="0000-00-00",$dep="0",$status="0")
    {
        //Set Global
        permission();
        $data['filename'] = 'overtime';
        $data['title'] = 'Overtime';
        $data['path_file'] = $this->filename;
        //if($user == 0 && $tgl == "0000-00-00" && !$this->uri->segment(5)) $tgl = date("Y-m-d");
        $data['tgl'] = $tgl;
        $exp = explode("~", $tgl);
        if($exp[0] != "0000-00-00")
        {
          $data['start_date'] = $exp[0];
          if(!isset($exp[1])) $data['end_date'] = $exp[0];
          else if(!$exp[1]) $data['end_date'] = $exp[0];
          else $data['end_date'] = $exp[1];
          if($exp[1]) $data['period'] = GetMonth(substr($exp[1],5,2))." ".substr($exp[1],0,4);
          else $data['period'] = "";
        } else {
        	$dt = "Dec 2015";//date("M Y");
        	$period = GetPeriod($dt);
        	$data['start_date']=substr($period,0,10);
        	$data['end_date']=substr($period,11,10);
        	$data['period']=$dt;
        }
        
        $path_paging = base_url().$this->filename."/main/".$user."/".$tgl."/".$dep."/".$status;
        $uri_segment = 7;
        $pg = $this->uri->segment($uri_segment);
        $per_page=15;
        //End Global
        
        $filter_where_in=array();
        $exp = explode("-",$exp[0]);
        if(!isset($exp[0])) $exp[0] = "";
        if(!isset($exp[1])) $exp[1] = "";
        if(!isset($exp[2])) $exp[2] = "";
        
        //if($exp[2] > 0) $filter = array("tahun"=> "order/asc", "bulan"=> "order/asc", "tanggal"=> "order/asc");
        //else $filter = array("id_employee"=> "order/asc", "tahun"=> "order/asc", "bulan"=> "order/asc", "tanggal"=> "order/asc");
        
        //Grup Admin
        $id_grup = $this->session->userdata("webmaster_grup");
        
        if($status)
        {
            /*if($status == "alpa2")
            {
                $kemarin = date("Y-m-d", mktime(0, 0, 0, $exp[1], $exp[2]-1, $exp[0]));
                $exp_alpa = explode("-", $kemarin);
                $filter["alpa"] = "where/1";
            }
            else */
            if($status == "jh") $filter['terlambat !='] = "where/1";
            $filter[$status] = "where/1";
        }
        
        $kondisi="";
        if($status == "alpa2")
        {
            $tgl_alpa=array($exp[2], $exp_alpa[2]);
            $filter_where_in['tanggal'] = $tgl_alpa;
            
            $bln_alpa=array($exp[1], $exp_alpa[1]);
            $filter_where_in['bulan'] = $bln_alpa;
            
            $thn_alpa=array($exp[0], $exp_alpa[0]);
            $filter_where_in['tahun'] = $thn_alpa;
        }
        else
        {
            if($data['start_date'] == $data['end_date'])
            {
                if($exp[0] > 0)
                {
                    $filter['tahun'] = "where/".$exp[0];$data['title'] = $this->title." ( ".$exp[0]." )";
                    if($exp[1] > 0)
                    {
                        $filter['bulan'] = "where/".$exp[1];$data['title'] = $this->title." ( ".GetMonth(intval($exp[1]))." ".$exp[0]." )";
                        if($exp[2] > 0){$filter['tanggal'] = "where/".$exp[2];$data['title'] = $this->title." ( ".$exp[2]." ".GetMonth(intval($exp[1]))." ".$exp[0]." )";}
                    }
                }
                else if($exp[1] > 0)
                {
                    $exp[0] = date("Y");
                    $filter['tahun'] = "where/".$exp[0];
                    $filter['bulan'] = "where/".$exp[1];$data['title'] = $this->title." ( ".GetMonth(intval($exp[1]))." ".$exp[0]." )";
                    if($exp[2] > 0){$filter['tanggal'] = "where/".$exp[2];$data['title'] = $this->title." ( ".$exp[2]." ".GetMonth(intval($exp[1]))." ".$exp[0]." )";}
                }
                else if($exp[2] > 0)
                {
                    $exp[0] = date("Y");$exp[1]=date("m");
                    $filter['tahun'] = "where/".$exp[0];
                    $filter['bulan'] = "where/".$exp[1];
                    $filter['tanggal'] = "where/".$exp[2];$data['title'] = $this->title." ( ".$exp[2]." ".GetMonth(intval($exp[1]))." ".$exp[0]." )";
                }
            }
        }
        
        if($dep)
        {
            /*$ex = explode("-",$dep);
            $dep=array();
            foreach($ex as $r)
            {
                $dep[] = $r;
            }
            $filter_where_in['id_department'] = $dep;*/
            $filter['id_department'] = "where/".$dep;
        }
        //else $dep=array();
        
        if($user)
        {
            $temp_user=$user;
            $ex = explode("-",$user);
            $user=array();
            foreach($ex as $r)
            {
                $user[] = $r;
            }
            $filter_where_in['id_employee'] = $user;
            $user=$temp_user;
            if($data['start_date'])
            {
                $filter['date_full >='] = "where/".$data['start_date'];
                $filter['date_full <='] = "where/".$data['end_date'];
            }
        }
        //else $user=array();

        $data['spic'] = $user;
        $data['sdep'] = $dep;
        /*$data['stgl'] = $exp[2];
        $data['sbln'] = $exp[1];
        $data['sthn'] = $exp[0];*/
        
        $filter['person_nm'] = "order/asc";
        $filter['lembur'] = "where/1";
        //if(count($user) > 0 || $exp[2] > 0)
        if(($exp[2] > 0 && ($data['start_date'] == $data['end_date'])) || $user)
        {
            $data['flag_tgl']=1;
            $data['grid'] = array("Nama Karyawan","Tanggal","OT. Incidental","Kelebihan Jam Kerja","Tunjangan Hari Kerja","Alasan Lembur","Scan Masuk","Scan Pulang","Keterangan");
            $data['list'] = array("id_employee","tgl","ot_incidental","ot_allow_shift","ot_cont_allow","alasan_lembur","scan_masuk","scan_pulang","keterangan");
            if($status == "alpa2")
            {
                $sql = "select * from kg_view_kehadiran_mcci where tanggal='".$exp[2]."' AND bulan='".$exp[1]."' AND tahun='".$exp[0]."' AND alpa='1'
                AND id_employee in (select id_employee from kg_view_kehadiran where tanggal='".$exp_alpa[2]."' AND bulan='".$exp_alpa[1]."' AND tahun='".$exp_alpa[0]."' AND alpa='1')";
                $data['query_all'] = $this->db->query($sql);
                if(!$pg) $pg=0;
                $data['query_list'] = $this->db->query($sql." LIMIT $pg, $per_page");
                
            }
            else
            {
                $data['query_all'] = GetAll("kg_view_kehadiran", $filter, $filter_where_in);
                $filter['limit'] = $pg."/".$per_page;
                $data['query_list'] = GetAll("kg_view_kehadiran", $filter, $filter_where_in);
                //lastq();
            }
        }
        else
        {
            /*$temp = $filter['id_employee'];
            unset($filter['id_employee']);
            $filter['id_employee'] = $temp;
            $temp = $filter['tanggal'];
            unset($filter['tanggal']);
            $filter['tanggal'] = $temp;
            $temp = $filter['bulan'];
            unset($filter['bulan']);
            $filter['bulan'] = $temp;
            $temp = $filter['tahun'];
            unset($filter['tahun']);
            if($temp == "order/asc") $temp = "where/".date("Y");
            $filter['tahun'] = $temp;*/
            if($data['start_date'])
            {
                $filter['date_full >='] = "where/".$data['start_date'];
                $filter['date_full <='] = "where/".$data['end_date'];
            }
            $data['flag_tgl']=0;
            $data['grid'] = array("Name", "Hour SUM");
            $data['list'] = array("person_nm", "acc_ot_incidental");
            /*if($exp[1] > 0) $filter['group'] = array("employee.id","bulan","tahun");
            elseif($exp[0] > 0) $filter['group'] = array("employee.id","tahun");
            else */
            //$filter['group'] = array("employee.id","tahun");
            $filter['person_nm'] = "group";
            //else $filter['group'] = array("employee.id");
            /*$select = "employee.id as a_id,employee.name, kg_kehadirandetil.id, SUM(kg_kehadirandetil.jhk) as jhk, SUM(kg_kehadirandetil.sakit) as sakit,
            SUM(kg_kehadirandetil.cuti) as cuti,SUM(kg_kehadirandetil.ijin) as ijin,,SUM(kg_kehadirandetil.alpa) as alpa,SUM(kg_kehadirandetil.off) as off,
            SUM(kg_kehadirandetil.potong_gaji) as potong_gaji, SUM(kg_kehadirandetil.pc) as pc,SUM(kg_kehadirandetil.jh) as jh";
            $data['query_all'] = GetJoin("employee","kehadirandetil","kehadirandetil.id_employee=employee.id ".$kondisi, "left", $select, $filter, $filter_where_in);*/
            $select = "id, person_nm, id_employee as a_id, SUM(jhk) as jhk, SUM(ot_incidental) as ot_incidental,SUM(acc_ot_incidental) as acc_ot_incidental,
            SUM(ot_allow_shift) as ot_allow_shift,SUM(ot_cont_allow) as ot_cont_allow";
            $data['query_all'] = GetAllSelect("kg_view_kehadiran_mcci", $select, $filter);
            //lastq();
            $filter['limit'] = $pg."/".$per_page;
            //$data['query_list'] = GetJoin("employee","kehadirandetil","kehadirandetil.id_employee=employee.id ".$kondisi, "left", $select, $filter, $filter_where_in);
            $data['query_list'] = GetAllSelect("kg_view_kehadiran_mcci", $select, $filter);
            unset($filter['person_nm']);
            unset($filter['limit']);
            $query_detail = GetAllSelect("kg_view_kehadiran_mcci", "*", $filter);
            foreach($query_detail->result_array() as $r) {
            	$data['query_detail'][$r['id_employee']][] = $r;
            }
            //print_mz($data['query_detail']);
            //die($this->db->last_query());
        }
        
        if(!$this->uri->segment(3) && !$this->uri->segment(4) && !$this->uri->segment(5)) $data['dis_tgl'] = "display:none;";
        else $data['dis_tgl'] = "display:''";
        
        //Page
        $pagination = Page($data['query_all']->num_rows(),$per_page,$pg,$path_paging,$uri_segment);
        if(!$pagination) $pagination = "<strong>1</strong>";
        $data['pagination'] = $pagination;
        //End Page
        $this->load->view('overtime', $data);
    }

    function shift($dep=0,$period=0)
    {
        $this->data['title'] = $this->title;
        permission();
        $data['path_file'] = $this->filename;
        permissionkaryawan($this->session->userdata('webmaster_id'), $data['path_file']);
        $data['main_content'] = $data['path_file'];
        $data['filename'] = $this->filename;
        $data['title'] = $this->title;
        
        $path_paging = base_url().$this->filename."/main/".$dep."/".$period;
        $uri_segment = 5;
        $pg = $this->uri->segment($uri_segment);
        $per_page=15;
        //End Global
        
        //$data['opt_pic'] = GetOptPIC();
        $data['opt_pic'][''] = "";
        //$data['opt_dep'] = GetOptDepartment();
        //$data['opt_dep'][''] = "";
               
        $filter = array("bulan"=> "order/asc", "tahun"=> "order/asc", "person_nm"=> "order/asc");
        //$filter = array("tahun"=> "order/asc", "bulan"=> "order/asc", "person_nm"=> "order/asc");
        $filter_where_in = array();
        if($dep && $dep!="-") {
            $exp = explode("-",$dep);
            $dep=array();
            foreach($exp as $r)
            {
                $dep[] = $r;
            }
            $filter_where_in['id_department'] = $dep;
        } else $dep=array();
        
        if($period) {
        	$period=urldecode($period);
        } else {
        	$period=date("M Y");
        }
        $filter['bulan'] = "where/".GetMonthIndex(substr($period,0,3));
        $filter['tahun'] = "where/".substr($period,4,4);
        
        $data['dep']=$dep;
        $data['period']=$period;
        
        $data['grid'] = array("Name","Bulan","Shift 1","Shift 2","Shift 3","Reguler","Off");
        $data['query_all'] = GetAll('kg_view_jadwal_shift', $filter, $filter_where_in);
        $filter['limit'] = $pg."/".$per_page;
        $data['query_list'] = GetAll('kg_view_jadwal_shift', $filter, $filter_where_in);
        $data['list'] = array("person_nm","bulan","jum_p","jum_s","jum_m","jum_ns","jum_off");
        //lastq();
        //Page
        $pagination = Page($data['query_all']->num_rows(),$per_page,$pg,$path_paging,$uri_segment);
        if(!$pagination) $pagination = "<strong>1</strong>";
        $data['pagination'] = $pagination;
        //End Page
        
        $this->load->view('shift', $data);
    }
    
    function shift_detail($id=0)
		{
			//Set Global
			permission();
			$data = GetHeaderFooter();
			$data['path_file'] = $this->filename;
			$cont=$data['path_file'].'_form';
			if($id>0) $cont.='_old';
			$data['main_content'] = $cont;
			$data['filename'] = $this->filename;
			$data['title'] = $this->title;
			
			if($id > 0) $data['val_button'] = lang("edit");
			else $data['val_button'] = lang("add");
			//End Global
			
			$q = GetAll("kg_jadwal_shift", array("id"=> "where/".$id));
			$r = $q->result_array();
			if($q->num_rows() > 0) 
			{
				$data['val'] = $r[0];
				$data['selbul']= $r[0]['bulan'].'-'.$r[0]['tahun'];
				$data['tgl']=$r[0];
				/*$exp = explode(",", str_replace("-","",$r[0]['lembur_putus']));
				foreach($exp as $x) {
					$data['pts'][$x] = $x;
				}
				$exp = explode(",", str_replace("-","",$r[0]['lembur_separo']));
				foreach($exp as $x) {
					$data['separo'][$x] = $x;
				}*/
				
				$data['person_nm'] = GetValue("person_nm", "hris_persons", array("person_id"=> "where/".$r[0]['id_employee']));
				$data['group'] = GetValue("employee_grup", "kg_hris_employee", array("person_id"=> "where/".$r[0]['id_employee']));
				$data['periode'] = GetMonth(intval($r[0]['bulan']))."%20".$r[0]['tahun'];
			} else { 
				$data['val'] = array();$data['selbul']="";
				$data['tgl']=$data['pts']=array();
				$data['person_nm'] = $data['group'] = "";
			}
			
			$this->load->view('shift_detail', $data);
		}
		
		function shift_update()
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
					$data[$r['Field']] = $this->input->post($r['Field']);
				}
				else if($r['Field'] == "tgl_".$jml_hari)
				{
					$data[$r['Field']] = $this->input->post($r['Field']);
				}
			}
			//print_mz($data);
			$hitung=array_count_values($data);
			//print_mz($hitung);
			if(!isset($hitung['1'])) $hitung['1']=0;
			if(!isset($hitung['2'])) $hitung['2']=0;
			if(!isset($hitung['3'])) $hitung['3']=0;
			if(!isset($hitung['ns'])) $hitung['ns']=0;
			if(!isset($hitung['off'])) $hitung['off']=0;	
			
			$data['jum_p']=$hitung['1'];
			$data['jum_s']=$hitung['2'];
			$data['jum_m']=$hitung['3'];
			$data['jum_ns']=$hitung['ns'];
			$data['jum_off']=$hitung['off'];
			//$data['bulan']=$period[0];
			//$data['tahun']=$period[1];
			$data['modify_date'] = date("Y-m-d H:i:s");
			
			/*$lembur_putus="";
			$pts = $this->input->post("pts");
			if(is_array($pts)) {
				foreach($pts as $val)
				{
					if($val) $lembur_putus .= "-".$val."-,";
				}
			}
			$lembur_putus = substr($lembur_putus, 0, -1);
			$data['lembur_putus'] = $lembur_putus;
			
			$lembur_separo="";
			$separo = $this->input->post("separo");
			if(is_array($separo)) {
				foreach($separo as $val)
				{
					if($val) $lembur_separo .= "-".$val."-,";
				}
			}
			$lembur_separo = substr($lembur_separo, 0, -1);
			$data['lembur_separo'] = $lembur_separo;*/
			//die($lembur_putus."S");
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
			
			//if($this->input->post("stay")) redirect($this->filename.'/detail/'.$id);
			//else redirect($this->filename);
		}

    function edit($id)
    {
    	permission();
      $q = GetAll("kg_kehadirandetil", array("id"=> "where/".$id));
			$r = $q->result_array();
			$data['val'] = $r[0];
			$data['tgl'] = $r[0]['tahun']."-".$r[0]['bulan']."-".$r[0]['tanggal'];
			$data['absensi'] = array(array("jh","Hadir"),
														array("off","Off"),
														array("cuti","Cuti"),
														array("ijin","Ijin"),
														array("sakit","Sakit"),
														array("alpa","Alpa"));
														//print_mz($data['absensi']);
      for($i=0;$i<=20;$i++) {
				$opt[$i] = $i;
			}
			$data['opt_lembur'] = $opt;
      $this->load->view('attendance_edit', $data);
      //$this->_render_page($this->filename.'_edit',$data);
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

                if(in_array($view, array('index', 'attendance_edit')))
                {
                    $this->template->set_layout('default');

                    $this->template->add_css('assets/plugins/bootstrap-select2/select2.css');
                    $this->template->add_css('assets/plugins/bootstrap-datepicker/css/datepicker.css');
                    $this->template->add_css('assets/plugins/boostrap-clockpicker/bootstrap-clockpicker.min.css');

                    $this->template->add_js('assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
                    $this->template->add_js('assets/plugins/boostrap-clockpicker/bootstrap-clockpicker.min.js');
                    
                    $this->template->add_js('modules/js/'.$this->filename.'.js');
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
    
    function update()
		{
			$webmaster_id = permission();
			$id = $this->input->post('id');
			$data['id_employee'] = $this->input->post('id_employee');
			$data['jhk'] = 1;//$this->input->post('jhk');
			$data['sakit']=$data['cuti']=$data['ijin']=$data['alpa']=$data['off']=$data['potong_gaji']=$data['pc']=$data['jh']=$data['hr']=$data['lembur']=0;
			$data['terlambat']=$data['opname']=$data['opname_istirahat']=$data['kecelakaan_kerja']=0;
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
			$data['plg_cepat'] = $this->input->post("plg_cepat");
			$data['ot_incidental'] = $this->input->post("ot_incidental");
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
			
			$data['keterangan'] = $this->input->post('keterangan');
			
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
				$id = $this->db->insert_id();
				//Admin Log
				//$logs = $this->db->last_query();
				//$this->model_admin_all->LogActivities($webmaster_id,$this->tabel,$this->db->insert_id(),$logs,lang($this->filename),$data[$this->title_table],$this->filename,"Add");
				
				//$this->session->set_flashdata("message", lang('add')." ".$this->title." ".lang('msg_sukses'));
			}
			
		}
		
		function get_period($bln)
		{
			echo GetPeriod(urldecode($bln));
		}
		
		public function ajax_list()
    {
        $list = $this->payroll->get_datatables();//lastq();//print_mz($list);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $payroll) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $payroll->name;
            $row[] = $payroll->username;

             $row[] = '<a class="btn btn-sm btn-primary" href="javascript:void(0);" username="Edit" onclick="edit_user('."'".$payroll->id."'".')"><i class="glyphicon glyphicon-pencil"></i> Edit</a>
                  <a class="btn btn-sm btn-danger" href="javascript:void(0)" username="Hapus" onclick="delete_user('."'".$payroll->id."'".')"><i class="glyphicon glyphicon-trash"></i> Delete</a>';
        

            $data[] = $row;
        }

        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->payroll->count_all(),
                        "recordsFiltered" => $this->payroll->count_filtered(),
                        "data" => $data,
                );
        //output to json format
        echo json_encode($output);
    }
}
?>