<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Kehadiran extends MX_Controller {
	
	var $title = "kehadiran";
	var $filename = "kehadiran";
	public $data;
	function __construct()
	{
		parent::__construct();
        $this->load->model('users_model','users');
	}
	
	function index()
	{
		$this->data['title'] = $this->title;
        permission();
		$this->_render_page($this->filename, $this->data);
	}

    public function ajax_list()
    {
        $list = $this->users->get_datatables();//lastq();//print_mz($list);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $users) {
            $no++;
            $row = array();
            $row[] = $users->employee_id;

            $data[] = $row;
        }

        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->users->count_all(),
                        "recordsFiltered" => $this->users->count_filtered(),
                        "data" => $data,
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

                if(in_array($view, array($this->filename)))
                {
                    $this->template->set_layout('default');
                    $this->template->add_css('assets/plugins/data-tables/datatables.min.css');
                    $this->template->add_js('assets/plugins/data-tables/jquery.dataTables.min.js');
                    $this->template->add_js('modules/js/absensi/'.$this->filename.'.js');
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