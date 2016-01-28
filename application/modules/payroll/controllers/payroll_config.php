<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class payroll_config extends MX_Controller {
    
    var $title = "payroll";
    var $filename = "payroll_config";
    public $data;
    function __construct()
    {
        parent::__construct();
        $this->load->model('payroll_config_model','payroll');
    }
    
    function index()
    {
        $this->data['title'] = $this->title;
        $filter_org = array('org_class_id'=>'where/4', 'status_cd'=>'where/normal');
        $this->data['session'] = getAll('hris_global_sess');
        $this->data['org'] = getAll('hris_orgs', $filter_org);//print_mz($this->data['org']->num_rows());
        
        permission();
        $this->_render_page($this->filename, $this->data);
    }

    public function matrix_list()
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

    function edit($type){
        $id = $this->input->post('id');
        $value = str_replace(',', '', $this->input->post('value'.$type));

        $this->db->where('id', $id)->update('payroll_job_value_matrix', array('value'.$type=>$value));
        lastq();
    }

    public function ajax_edit($id)
    {
        $data = $this->payroll->get_by_id($id); // if 0000-00-00 set tu empty for datepicker compatibility
        echo json_encode($data);
    }

    public function ajax_add()
    {
        //$this->_validate();
        $data = array(
                'name' => $this->input->post('name'),
                'username' => $this->input->post('username'),
            );
        $insert = $this->payroll->save($data);
        echo json_encode(array("status" => TRUE));
    }

    public function ajax_update()
    {
        //$this->_validate();
        $data = array(
                'name' => $this->input->post('name'),
                'username' => $this->input->post('username'),
            );
        $this->payroll->update(array('id' => $this->input->post('id')), $data);
        echo json_encode(array("status" => TRUE));
    }

    public function ajax_delete($id)
    {
        $this->payroll->delete_by_id($id);
        echo json_encode(array("status" => TRUE));
    }

    //FOR JS FUNCTION
    function get_table_matrix($sess_id, $org_id)
    {

        $filter = array(
                        'session_id'=>'where/'.$sess_id,
                        'org_id' => 'where/'.$org_id
                        );
        //$data['matrix'] = getAll('payroll_job_value_matrix', $filter);
        $data['matrix'] = $this->payroll->get_matrix_table($sess_id, $org_id);

        $this->load->view('payroll_config/matrix_table', $data);
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
                    $this->template->add_css('assets/plugins/bootstrap-select2/select2.css');
                    $this->template->add_css('assets/plugins/data-tables/datatables.min.css');
                    $this->template->add_js('assets/plugins/data-tables/jquery.dataTables.min.js');
                    $this->template->add_js('modules/js/'.$this->title.'/'.$this->filename.'.js');
                }

            if ( ! empty($data['username']))
            {
                $this->template->set_title($data['username']);
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