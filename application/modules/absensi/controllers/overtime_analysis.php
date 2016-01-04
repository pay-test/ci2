<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Overtime_analysis extends MX_Controller {
	
	var $filename = "overtime_analysis";
    var $tabel = "kg_overtime";
    var $id_primary = "id";
    var $title = "Overtime Analysis";
	public $data;
	function __construct()
	{
		parent::__construct();
	}
	
	function index($user="0",$tgl="0000-00-00",$dep="0",$status="0")
	{
		//Set Global
        permission();
        $data['path_file'] = $this->filename;
        $data['main_content'] = $data['path_file'];
        $data['filename'] = $this->filename;
        $data['title'] = $this->title;
        //if($user == 0 && $tgl == "0000-00-00" && !$this->uri->segment(5)) $tgl = date("Y-m-d");
        $data['tgl'] = $tgl;
        $exp = explode("~", $tgl);
        if($exp[0] != "0000-00-00")
        {
            $data['start_date'] = $exp[0];
            if(!isset($exp[1])) $data['end_date'] = $exp[0];
            else if(!$exp[1]) $data['end_date'] = $exp[0];
            else $data['end_date'] = $exp[1];
        }
        else $data['start_date']=$data['end_date']="";
        
        $path_paging = base_url().$this->filename."/main/".$user."/".$tgl."/".$dep."/".$status;
        $uri_segment = 7;
        $pg = $this->uri->segment($uri_segment);
        $per_page=15;
        //End Global
		$this->_render_page('overtime_analysis', $data);
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

                if(in_array($view, array('overtime_analysis/index')))
                {
                    $this->template->set_layout('default');
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