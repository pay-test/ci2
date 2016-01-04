<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Shift_schedule extends MX_Controller {
	
	var $filename = "jdwlshift";
    var $id_primary = "id";
    var $title = "Jadwal Shift";
    var $tabel = "jadwal_shift";
	public $data;
	function __construct()
	{
		parent::__construct();
	}
	
	function index($dep=0,$period=0)
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
        $data['opt_dep'] = GetOptDepartment();
        //$data['opt_dep'][''] = "";
        
        
        
        $filter = array("id_employee"=> "order/asc");
        $filter_where_in = array();
        /*if($user)
        {
            $exp = explode("-",$user);
            $user=array();
            foreach($exp as $r)
            {
                $user[] = $r;
            }
            $filter_where_in['id'] = $user;
        }
        else $user=array();*/
        
        if($dep && $dep!="-")
        {
            $exp = explode("-",$dep);
            $dep=array();
            foreach($exp as $r)
            {
                $dep[] = $r;
            }
            $filter_where_in['id_department'] = $dep;
        }
        else $dep=array();
        if($period)
        {
            $filter['bulan'] = "where/".substr($period,0,2);
            $filter['tahun'] = "where/".substr($period,3,4);
            $period=$period;
        }
        else $period=NULL;
        
        $data['dep']=$dep;
        $data['period']=$period;
        
        $data['grid'] = array("Nama","Departemen","Bulan","Shift Pagi","Shift Sore","Shift Malam","Non Shift","Off");
        $data['query_all'] = GetAll('kg_view_jadwal_shift', $filter, $filter_where_in);
        $filter['limit'] = $pg."/".$per_page;
        $data['query_list'] = GetAll('kg_view_jadwal_shift', $filter, $filter_where_in);
        $data['list'] = array("employee","title","bulan","jum_p","jum_s","jum_m","jum_ns","jum_off");
        //lastq();
        //Page
        $pagination = Page($data['query_all']->num_rows(),$per_page,$pg,$path_paging,$uri_segment);
        if(!$pagination) $pagination = "<strong>1</strong>";
        $data['pagination'] = $pagination;
        //End Page
        if($this->input->post('export')){
        /*  $data['grid_export'] = array("Nama","No Employee", "Jabatan", "Departement", "Date Of Joint", "Status", "Jenis Kelamin","Tanggal Lahir", "Keluarga", "Riwayat Pendidikan", "Riwayat Kerja", "Riwayat Training", "Riwayat Medis");
            $data['query_list_export'] = GetAll($this->tabel, array("id !="=> "where/1"));
            $data['list_export'] = array("name","nip","id_jabatan","id_kedeputian","tgl_aktif","status_pernikahan","gender","ttl","keluarga","pendidikan","riwayatkerja","training","medis");
            
            $data['main_content'] = "hris/personal_export";
            $html = $this->load->view("template_export",$data);
            to_excel($html,str_replace(" ","_",$this->title));*/
        }else{
		$this->_render_page('shift_schedule', $data);
        }
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

                if(in_array($view, array('shift_schedule/index')))
                {
                    $this->template->set_layout('default');
                    $this->template->add_css('assets/plugins/data-tables/DT_bootstrap.min.css');
                    $this->template->add_js('assets/plugins/data-tables/jquery.dataTables.min.js');
                    $this->template->add_js('modules/js/absensi/index.js');
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