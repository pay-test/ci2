<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Payroll_component extends MX_Controller {
	
	var $title = "payroll";
    var $page_title = "Component";
	var $filename = "payroll_component";
	public $data;
	function __construct()
	{
		parent::__construct();
        $this->load->model('payroll_component_model','payroll');
        $this->load->model('all_model','all_model');
	}
	
	function index()
	{
        $this->data['title'] = ucfirst($this->title);
		$this->data['page_title'] = $this->page_title;
        $this->data['session'] = getAll('hris_global_sess', array('id'=>'order/desc'));
        $this->data['component_type'] = $this->payroll->get_component_type();
        $this->data['tax_component'] = $this->payroll->get_tax_component();
        $this->data['component_job_value'] = getAll('payroll_component_job_value');
        $this->data['job_value'] = getAll('payroll_job_value');
        permission();
		$this->_render_page($this->filename, $this->data);
	}

    public function ajax_list($session_id)
    {
        $list = $this->payroll->get_datatables();//lastq();//print_mz($list);
        //print_mz($session_id);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $p_comp) {
            //get component type
            $component_type = $this->all_model->GetValue('title','payroll_component_type','id = '.$p_comp->component_type_id);
            //get tax component
            $tax_component = $this->all_model->getValue('title','payroll_tax_component','id = '.$p_comp->tax_component_id);
            if (!$tax_component) {
                $tax_component = "";
            }
            //status attribute
            if ($p_comp->is_annualized == 1) {
                $is_annualized = "Annualized";
            }else{
                $is_annualized = "Not Annualized";
            }
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $p_comp->title;
            $row[] = $p_comp->code;
            $row[] = $component_type;
            $row[] = $is_annualized;
            $row[] = $tax_component;
            $url = base_url('payroll/payroll_component/edit/'.$p_comp->id.'/'.$session_id);
             $row[] = '<a class="btn btn-sm btn-primary" href="'.$url.'" title="Edit"><i class="fa fa-pencil"></i></a>
                  <a class="btn btn-sm btn-danger" href="javascript:void(0)" username="Hapus" onclick="delete_user('."'".$p_comp->id."'".')"><i class="fa fa-trash"></i></a>';

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

    public function ajax_edit($id, $session)
    {
        /*
        $data = $this->payroll->get_by_id($id);
        $data2 = $this->payroll->get_component_value($id, $session);
        echo json_encode(array('data'=>$data, 'data2'=>$data2));
        */
        $this->_render_page('payroll/payroll_component/edit', $this->data);
    }

    public function edit($id, $session_id)
    {
        $this->data['title'] = ucfirst($this->title);
        $this->data['page_title'] = $this->page_title;
        $this->data['component_type'] = $this->payroll->get_component_type();
        $this->data['tax_component'] = $this->payroll->get_tax_component();
        $this->data['component_job_value'] = getAll('payroll_component_job_value');
        $this->data['job_value'] = getAll('payroll_job_value');
        $this->data['session_id'] = $session_id;
        $this->data['data'] = $data = $this->payroll->get_by_id($id);//print_ag($data);
        $filter = array('payroll_component_id'=>'where/'.$id, 'session_id'=>'where/'.$session_id);
        $component_session_num_rows = getAll('payroll_component_session', $filter)->num_rows();//print_mz($component_session_id);
        if($component_session_num_rows < 1) $this->db->insert('payroll_component_session', array('session_id'=>$session_id, 'payroll_component_id'=>$id));
         $this->data['component_session_id'] = $component_session_id = getValue('id', 'payroll_component_session', $filter);//print_mz($component_session_id);
        $filter2 =  array('payroll_component_session_id'=>'where/'.$component_session_id, 'from'=>'order/desc');
        $this->data['data2'] = $data2 = getAll('payroll_component_value',$filter2);//lastq();print_mz($data2->result());
        permission();
        $this->_render_page('payroll_component/edit', $this->data);
    }

    function edit_component($id)
    {
        permission();
        $data = array(
            'title' => $this->input->post('title'),
            'code' => $this->input->post('code'),
            'component_type_id' => $this->input->post('component_type_id'),
            'is_annualized' => $this->input->post('is_annualized'),
            'tax_component_id' => $this->input->post('tax_component_id'),
            'edited_by' => GetUserID(),
            'edited_on' => date('Y-m-d H:i:s')
        );
        $this->payroll->update(array('id' => $id), $data);
        echo json_encode(array('status'=>true));
    }

    public function ajax_add()
    {
        $this->_validate();
        $data = array(
                'title' => $this->input->post('title'),
                'code' => $this->input->post('code'),
                'component_type_id' => $this->input->post('component_type_id'),
                'is_annualized' => $this->input->post('is_annualized'),
                'tax_component_id' => $this->input->post('tax_component_id'),
                'formula' => $this->input->post('formula'),
                'is_condition' => $this->input->post('is_condition'),
                'min' => str_replace(',', '', $this->input->post('min')),
                'max' => str_replace(',', '', $this->input->post('max')),
                'session_id' => $this->input->post('session'),
                'created_by' => GetUserID(),
                'created_on' => date('Y-m-d H:i:s')
            );
        $insert = $this->payroll->save($data);
        echo json_encode(array("status" => TRUE));
    }

    public function ajax_update()
    {
        //$i = $this->input->post('checkbox1');print_r($i);print_r($this->input->post('job_value_id'));print_mz($this->input->post('value'));
        $this->_validate();
        $session_id = $this->input->post('session');
        $id = $this->input->post('id');
        $data = array(
                'title' => $this->input->post('title'),
                'code' => $this->input->post('code'),
                'component_type_id' => $this->input->post('component_type_id'),
                'is_annualized' => $this->input->post('is_annualized'),
                'tax_component_id' => $this->input->post('tax_component_id'),
                'edited_by' => GetUserID(),
                'edited_on' => date('Y-m-d H:i:s')
            );
        $this->payroll->update(array('id' => $this->input->post('id')), $data);
        $num_rows = getAll('payroll_component_value', array('payroll_component_id'=>'where/'.$id, 'session_id'=>'where/'.$session_id))->num_rows();
        $data2 = array(
            'payroll_component_id'=>$id,
                'formula' => strtoupper($this->input->post('formula')),
                'is_condition' => $this->input->post('is_condition'),
                'min' => str_replace(',', '', $this->input->post('min')),
                'max' => str_replace(',', '', $this->input->post('max')),
                'session_id' => $this->input->post('session'),
                );
        if($num_rows>0){
            $this->db->where('payroll_component_id', $id)->where('session_id', $session_id)->update('payroll_component_value', $data2);
        }else{
            $this->db->insert('payroll_component_value', $data2);
        }
        echo json_encode(array("status" => TRUE));
    }

    function add_formula()
    {
        permission();

        $data = array(
            'payroll_component_session_id' => $this->input->post('component_session_id'), 
            'from' => $this->input->post('from'), 
            'to' => $this->input->post('to'), 
            'formula' => strtoupper($this->input->post('formula')), 
            'is_condition' => $this->input->post('is_condition'), 
            'max' => str_replace(',', '', $this->input->post('max')), 
            'min' => str_replace(',', '', $this->input->post('min')), 
            );
        $this->db->insert('payroll_component_value', $data);

        echo json_encode(array('status'=>true));
    }

    function edit_formula($id)
    {
        permission();

        $data = array(
            'payroll_component_session_id' => $this->input->post('component_session_id'.$id), 
            'from' => $this->input->post('from'.$id), 
            'to' => $this->input->post('to'.$id), 
            'formula' => strtoupper($this->input->post('formula'.$id)),
            'is_condition' => $this->input->post('is_condition'.$id), 
            'max' => str_replace(',', '', $this->input->post('max'.$id)), 
            'min' => str_replace(',', '', $this->input->post('min'.$id)), 
            );
        $this->db->where('id', $id)->update('payroll_component_value', $data);
        echo json_encode(array('status'=>true, 'data'=>$data));
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
 
        if($this->input->post('title') == '')
        {
            $data['inputerror'][] = 'title';
            $data['error_string'][] = 'Name is required';
            $data['status'] = FALSE;
        }
 
        if($this->input->post('code') == '')
        {
            $data['inputerror'][] = 'code';
            $data['error_string'][] = 'Code is required';
            $data['status'] = FALSE;
        }
 
        if($data['status'] === FALSE)
        {
            echo json_encode($data);
            exit();
        }
    }

    function render_job_value($component_id = 0) {
        $filter = array('payroll_group_id' => 'where/'.$group_id,'is_deleted' => 'where/0');
        $output = $this->all_model->GetAll('payroll_group_component',$filter);
    
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
                    $this->template->add_css('assets/plugins/data-tables/DT_bootstrap.min.css');
                    $this->template->add_css('assets/plugins/bootstrap-select2/select2.css');

                    $this->template->add_css('assets/plugins/bootstrap-datepicker/css/datepicker.css');
                    $this->template->add_js('assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
                    $this->template->add_js('assets/plugins/data-tables/jquery.dataTables.min.js');
                    $this->template->add_css('assets/plugins/jquery-datatable/css/jquery.dataTables.css');
                    $this->template->add_js('assets/plugins/data-tables/jquery.dataTables.min.js');
                    $this->template->add_js('assets/plugins/jquery-datatable/extra/js/dataTables.tableTools.min.js');
                    $this->template->add_js('assets/plugins/datatables-responsive/js/datatables.responsive.js');
                    $this->template->add_js('assets/plugins/jquery-maskmoney/jquery.maskMoney.js');
                    $this->template->add_js('modules/js/'.$this->title.'/'.$this->filename.'.js');
                }elseif(in_array($view, array($this->filename.'/edit')))
                {
                    $this->template->set_layout('default');
                    $this->template->add_css('assets/plugins/data-tables/DT_bootstrap.min.css');
                    $this->template->add_css('assets/plugins/bootstrap-select2/select2.css');
                    $this->template->add_css('assets/plugins/bootstrap-datepicker/css/datepicker.css');
                    $this->template->add_js('assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
                    $this->template->add_js('assets/plugins/data-tables/jquery.dataTables.min.js');
                    $this->template->add_css('assets/plugins/jquery-datatable/css/jquery.dataTables.css');
                    $this->template->add_js('assets/plugins/data-tables/jquery.dataTables.min.js');
                    $this->template->add_js('assets/plugins/jquery-datatable/extra/js/dataTables.tableTools.min.js');
                    $this->template->add_js('assets/plugins/datatables-responsive/js/datatables.responsive.js');
                    $this->template->add_js('assets/plugins/jquery-maskmoney/jquery.maskMoney.js');
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