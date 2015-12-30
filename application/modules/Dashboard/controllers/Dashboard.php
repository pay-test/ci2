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
        permission();
		$this->_render_page($this->filename.'/index', $this->data);
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