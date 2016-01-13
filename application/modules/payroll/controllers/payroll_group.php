<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Payroll_group extends MX_Controller {
    
    var $title = "payroll";
    var $page_title = "Group";
    var $filename = "payroll_group";
    public $data;
    function __construct()
    {
        parent::__construct();
        $this->load->model('payroll_group_model','payroll');
        $this->load->model('payroll_component_model','component');
        $this->load->model('all_model','all_model');
    }
    
    function index()
    {
        $this->data['title'] = ucfirst($this->title);
        $this->data['page_title'] = $this->page_title;

        $filter = array('is_deleted' => 'where/0');
        $this->data['p_component'] = $list_component =$this->all_model->GetAll('payroll_component',$filter);

        $filter = array('status_cd' => 'where/normal');
        $this->data['job_class'] = $this->all_model->GetAll('hris_job_class',$filter,'job_class_level');
        //lastq();

        permission();
        $this->_render_page($this->filename, $this->data);
    }

    function render_group_component($group_id = 0) {
        $filter = array('payroll_group_id' => 'where/'.$group_id,'is_deleted' => 'where/0');
        $output = $this->all_model->GetAll('payroll_group_component',$filter);
    
        echo json_encode($output);
    }

    public function ajax_list()
    {
        $list = $this->payroll->get_datatables();//lastq();//print_mz($list);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $group) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $group->title;
            $row[] = $group->code;
             $row[] = '<a class="btn btn-sm btn-primary" href="javascript:void(0);" username="Edit" onclick="edit_user('."'".$group->id."'".')"><i class="glyphicon glyphicon-pencil"></i> </a>
                  <a class="btn btn-sm btn-danger" href="javascript:void(0)" username="Hapus" onclick="delete_user('."'".$group->id."'".')"><i class="glyphicon glyphicon-trash"></i></a>';
        

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
        $job_class_id = $this->input->post('job_class_id');
        $job_class_nm = $this->all_model->GetValue('job_class_nm','hris_job_class','job_class_id = '.$job_class_id);
        $job_class_abbr = $this->all_model->GetValue('job_class_abbr','hris_job_class','job_class_id = '.$job_class_id);
        //lastq();
        $data = array(
                'job_class_id' => $job_class_id,
                'title' => $job_class_nm,
                'code' => $job_class_abbr,
                'created_by' => GetUserID(),
                'created_on' => date('Y-m-d H:i:s')
            );
        $insert = $this->payroll->save($data);

        $group_id = $insert; //last insert id
        $array_pcomp = $this->input->post('p_component');
        $array_thp = $this->input->post('is_thp');

        //update group component
        if ($array_pcomp) {
            $this->all_model->DeleteWhere('payroll_group_component',array('payroll_group_id' => $group_id));
            foreach ($array_pcomp as $pcomp) {
                $data2 = array(
                    'payroll_group_id' => $group_id,
                    'payroll_component_id' => $pcomp
                 );
                $this->all_model->Insert('payroll_group_component',$data2);
            }
        }
        //set is thp
        if ($array_thp) {
            foreach ($array_thp as $thp) {
                $data3 = array('is_thp' => 1);
                $this->all_model->Update('payroll_group_component',$data3,array('payroll_component_id' => $thp));
            }
        }

        echo json_encode(array("status" => TRUE));
    }

    public function ajax_update()
    {
        $this->_validate();
        $group_id = $this->input->post('id');
        $job_class_id = $this->input->post('job_class_id');
        $job_class_nm = $this->all_model->GetValue('job_class_nm','hris_job_class','job_class_id = '.$job_class_id);
        $job_class_abbr = $this->all_model->GetValue('job_class_abbr','hris_job_class','job_class_id = '.$job_class_id);
        $array_pcomp = $this->input->post('p_component');
        $array_thp = $this->input->post('is_thp');

        $data = array(
                'job_class_id' => $job_class_id,
                'title' => $job_class_nm,
                'code' => $job_class_abbr,
                'edited_by' => GetUserID(),
                'edited_on' => date('Y-m-d H:i:s')
            );

        //update group component
        if ($array_pcomp) {
            $this->all_model->DeleteWhere('payroll_group_component',array('payroll_group_id' => $group_id));
            foreach ($array_pcomp as $pcomp) {
                $data2 = array(
                    'payroll_group_id' => $group_id,
                    'payroll_component_id' => $pcomp
                 );
                $this->all_model->Insert('payroll_group_component',$data2);
            }
        }
        //set is thp
        if ($array_thp) {
            foreach ($array_thp as $thp) {
                $data3 = array('is_thp' => 1);
                $this->all_model->Update('payroll_group_component',$data3,array('payroll_component_id' => $thp));
            }
        }
        
        $this->payroll->update(array('id' => $group_id), $data);
        echo json_encode(array("status" => TRUE,"datas" => $array_pcomp));
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
 
        if($this->input->post('job_class_id') == '')
        {
            $data['inputerror'][] = 'title';
            $data['error_string'][] = 'Name is required';
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
                     $this->template->add_css('modules/css/'.$this->title.'/'.$this->filename.'.css');

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

    //group components
    public function ajax_component_list($group_id = 0)
    {
        $list = $this->component->get_datatables();//lastq();//print_mz($list);

        $group_id = $this->input->post('group_id');
        
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $p_comp) {
            $filter = array('payroll_group_id' => 'where/'.$group_id, 'payroll_component_id' => 'where/'.$p_comp->id);
            $list_comp_group = $this->all_model->GetRow('payroll_group_component',$filter);
            //lastq();
            if ($list_comp_group) {
                $checked_1 = "checked";
            }else{
                $checked_1 = "";
            }

            if ($list_comp_group['is_thp'] == 1) {
                $checked_2 = "checked";
            }else{
                $checked_2 = "";
            }

            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $p_comp->title;
            $row[] = $p_comp->code;
            $row[] = '<input type="checkbox" value="'.$p_comp->id.'" name="p_component[]" '.$checked_1.'>';
            $row[] = '<input type="checkbox" value="'.$p_comp->id.'" name="is_thp[]" '.$checked_2.'>';

            $data[] = $row;
        }

        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->component->count_all(),
                        "recordsFiltered" => $this->component->count_filtered(),
                        "data" => $data,
                );
        //output to json format
        echo json_encode($output);
    }
}
?>