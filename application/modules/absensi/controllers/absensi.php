<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Absensi extends MX_Controller {
	
	var $title = "Absensi";
	var $filename = "absensi";
	public $data;
	function __construct()
	{
		parent::__construct();
        $this->load->helper('mz');
		//$this->load->model("model_admin_all");
        $this->load->model('users_model','users');
	}
	
	function index()
	{
		$this->data['title'] = $this->title;
        permission();
		redirect('absensi/kehadiran', 'refresh');
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

                if(in_array($view, array('absensi/index')))
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