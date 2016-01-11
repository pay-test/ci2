<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Monthly_income extends MX_Controller {
	
	var $title = "payroll";
    var $page_title = "Monthly-Income";
	var $filename = "monthly_income";
	public $data;
	function __construct()
	{
		parent::__construct();
        $this->load->model('monthly_income_model','payroll');
        $this->load->model('payroll_setup_model','setup');
        //$this->load->model('all_model','all_model');
	}
	
	function index()
	{
        $this->data['title'] = ucfirst($this->title);
		$this->data['page_title'] = $this->page_title;

        permission();

         $year_now = date('Y');
        $this->data['period'] = $this->setup->render_periode($year_now);
        $this->data['options_group'] = options_row('payroll', 'get_group','id','title', '-- Choose Payroll Group --');
		$this->_render_page($this->filename, $this->data);
	}

    public function ajax_list()
    {
        $list = $this->payroll->get_datatables();//lastq();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $payroll) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $payroll->user_nm;
            $row[] = $payroll->person_nm;


            //add html for action
            $row[] = '<a class="btn btn-sm btn-primary" href="javascript:void(0);" title="Edit" onclick="edit_user('."'".$payroll->employee_id."'".')"><i class="glyphicon glyphicon-pencil"></i></a>';
        
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

    public function ajax_edit($id, $period_id)
    {
        $data = $this->payroll->get_by_id($id, $period_id);//print_mz($data); // if 0000-00-00 set tu empty for datepicker compatibility
        $monthly_income_id = getValue('id', 'payroll_monthly_income', array('employee_id'=>'where/'.$id, 'payroll_period_id'=>'where/'.$period_id));
        $data2 = $this->payroll->get_monthly_component($monthly_income_id)->result_array();
        echo json_encode(array('data1'=>$data, 'data2'=>$data2));
    }

    public function ajax_update()
    {
        //print_mz($this->input->post('monthly_component_id'));
        //$this->_validate();
        $employee_id = $this->input->post('employee_id');
        $period_id = $this->input->post('period_id');
        $num_rows = getAll('payroll_monthly_income', array('employee_id'=>'where/'.$employee_id, 'payroll_period_id'=>'where/'.$period_id))->num_rows();
        $monthly_income_id = getValue('id', 'payroll_monthly_income', array('employee_id'=>'where/'.$employee_id, 'payroll_period_id'=>'where/'.$period_id));
        $old_group = getValue('payroll_group_id', 'payroll_monthly_income', array('employee_id'=>'where/'.$employee_id, 'payroll_period_id'=>'where/'.$period_id));
        $group_id = $this->input->post('group_id');
        if($old_group != $group_id)$this->db->where('payroll_monthly_income_id', $monthly_income_id)->update('payroll_monthly_income_component', array('is_deleted' => 1));
        $data = array(
                'employee_id' => $employee_id,
                'payroll_group_id' => $group_id,
                'payroll_period_id' => $period_id,
            );
        if($num_rows>0)$this->db->where('employee_id', $employee_id)->where('payroll_period_id', $period_id)->update('payroll_monthly_income', $data);
            else $this->db->insert('payroll_monthly_income', $data);
            //print_r($this->db->last_query());
        $monthly_income_id = ($num_rows>0) ? $monthly_income_id : $this->db->insert_id();
        $monthly_group = getAll('payroll_monthly_income', array('employee_id'=>'where/'.$employee_id, 'payroll_group_id'=>'where/'.$old_group))->num_rows();//print_mz($monthly_group);
        $component_num_rows = getAll('payroll_monthly_income_component', array('payroll_monthly_income_id'=>'where/'.$monthly_income_id))->num_rows;
        $component = array('monthly_component_id' => $this->input->post('monthly_component_id'),
                           'component_id' => $this->input->post('component_id'),
                           'value' => $this->input->post('value'),
                    );
        //print_mz($component);
        
        for($i=0;$i<sizeof($component['component_id']);$i++):
            $data2 = array(
                    'payroll_monthly_income_id'=>$monthly_income_id,
                    'payroll_component_id' =>$component['component_id'][$i],
                    'value' =>$component['value'][$i],
                );
            if($old_group == $group_id)$this->db->where('id', $component['monthly_component_id'][$i])->update('payroll_monthly_income_component', $data2);
                else $this->db->insert('payroll_monthly_income_component', $data2);
                //rint_r($this->db->last_query());
        endfor;
        echo json_encode(array("status" => TRUE));
    }

    public function ajax_delete($id)
    {
        $this->payroll->delete_by_id($id);
        echo json_encode(array("status" => TRUE));
    }

    public function get_component_table()
    {
        $id = $this->input->post('id');

        $q = $this->data['component'] = $this->payroll->get_component($id)->result();//lastq();print_mz($q);
        $this->load->view('monthly_income/component_table', $this->data);
    }

    public function get_periode_status()
    {
        $id = $this->input->post('id');

        $status = getValue('status','payroll_period', array('id'=>'where/'.$id));
        $status = ($status == 1) ? 'Period Closed' : 'Period Open';

        echo $status;
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