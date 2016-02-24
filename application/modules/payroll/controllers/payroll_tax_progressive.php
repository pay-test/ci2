<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Payroll_tax_progressive extends MX_Controller {
    
    var $title = "payroll";
    var $page_title = "Tax Progressive";
    var $filename = "payroll_tax_progressive";
    public $data;
    function __construct()
    {
        parent::__construct();
        $this->load->model('payroll_tax_progressive_model','payroll');
    }
    
    function index()
    {
        $this->data['title'] = ucfirst($this->title);
        $this->data['page_title'] = $this->page_title;
       
        permission();
        $this->_render_page($this->filename, $this->data);
    }

    public function ajax_list()
    {
        $list = $this->payroll->get_datatables();//lastq();//print_mz($list);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $tax) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $tax->value_min;
            $row[] = $tax->value_max;
            $row[] = $tax->percentage;

            $row[] = '<a class="btn btn-sm btn-primary" href="javascript:void(0);" username="Edit" onclick="edit_user('."'".$tax->id."'".')"><i class="glyphicon glyphicon-pencil"></i> </a>
                  <a class="btn btn-sm btn-danger" href="javascript:void(0)" username="Hapus" onclick="delete_user('."'".$tax->id."'".')"><i class="glyphicon glyphicon-trash"></i></a>';
        

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

    public function ajax_edit($id)
    {
        $data = $this->payroll->get_by_id($id); // if 0000-00-00 set tu empty for datepicker compatibility
        echo json_encode($data);
    }

    public function ajax_add()
    {
        $this->_validate();
         $data = array(
                'value_min' => $this->input->post('value_min'),
                'value_max' => $this->input->post('value_max'),
                'percentage' => $this->input->post('percentage'),
                'created_by' => GetUserID(),
                'created_on' => date('Y-m-d H:i:s')
            );
        $insert = $this->payroll->save($data);
        echo json_encode(array("status" => TRUE));
    }

    public function ajax_update()
    {
        $this->_validate();
         $data = array(
                'value_min' => $this->input->post('value_min'),
                'value_max' => $this->input->post('value_max'),
                'percentage' => $this->input->post('percentage'),
                'edited_by' => GetUserID(),
                'edited_on' => date('Y-m-d H:i:s')
            );
        $this->payroll->update(array('id' => $this->input->post('id')), $data);
        echo json_encode(array("status" => TRUE));
    }

    public function ajax_delete($id)
    {
        $this->payroll->delete_by_id($id);
        echo json_encode(array("status" => TRUE));
    }

    private function _validate()
    {
        $data = array();
        $data['error_string'] = array();
        $data['inputerror'] = array();
        $data['status'] = TRUE;
 
        if($this->input->post('value_min') == '')
        {
            $data['inputerror'][] = 'value_min';
            $data['error_string'][] = 'Min Values is required';
            $data['status'] = FALSE;
        }
 
        if($this->input->post('value_max') == '')
        {
            $data['inputerror'][] = 'value_max';
            $data['error_string'][] = 'Max value is required';
            $data['status'] = FALSE;
        }
 
        if($data['status'] === FALSE)
        {
            echo json_encode($data);
            exit();
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

                if(in_array($view, array($this->filename)))
                {
                    $this->template->set_layout('default');
                    $this->template->add_css('assets/plugins/data-tables/DT_bootstrap.min.css');
                    $this->template->add_css('assets/plugins/bootstrap-select2/select2.css');

                    $this->template->add_js('assets/plugins/data-tables/jquery.dataTables.min.js');
                    $this->template->add_css('assets/plugins/jquery-datatable/css/jquery.dataTables.css');
                    $this->template->add_js('assets/plugins/data-tables/jquery.dataTables.min.js');
                    $this->template->add_js('assets/plugins/jquery-datatable/extra/js/dataTables.tableTools.min.js');
                    $this->template->add_js('assets/plugins/datatables-responsive/js/datatables.responsive.js');
                    $this->template->add_js('modules/js/'.$this->title.'/'.$this->filename.'.js');
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