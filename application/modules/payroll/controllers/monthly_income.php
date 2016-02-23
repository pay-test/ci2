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
        $filter = array('is_deleted' => 'where/0', 'is_active'=> 'where/1');
        $this->data['component'] = getAll('payroll_component', $filter)->result();
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
            $row[] = '<a class="btn btn-sm btn-primary" href="javascript:void(0);" title="Edit" onclick="edit_user('."'".$payroll->employee_id."'".')"><i class="fa fa-pencil"></i></a>
                <a class="btn btn-sm btn-danger" href="javascript:void(0);" title="Edit" onclick="print('."'".$payroll->employee_id."'".')"><i class="glyphicon glyphicon-print"></i></a>';
        
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
        $master_num_rows = getAll('payroll_master', array('employee_id'=>'where/'.$id))->num_rows();
        $monthly_income_num_rows = getAll('payroll_monthly_income', array('employee_id'=>'where/'.$id, 'payroll_period_id'=>'where/'.$period_id))->num_rows();//print_mz($monthly_income_num_rows);
        if($monthly_income_num_rows>0){$data = $this->payroll->get_by_id($id, $period_id);}else{$data = $this->payroll->get_master($id);}//print_r($this->db->last_query());
        $month = getValue('month', 'payroll_period', array('id'=>'where/'.$period_id));
        $sess_id = ($month<4) ?  date('Y')-1 : date('Y');//print_mz($sess_id);
        $monthly_income_id = getValue('id', 'payroll_monthly_income', array('employee_id'=>'where/'.$id, 'payroll_period_id'=>'where/'.$period_id));
        $master_payroll_id = getValue('id', 'payroll_master', array('employee_id'=>'where/'.$id, 'session_id'=>'where/'.$sess_id));//print_mz($master_payroll_id);
        if($monthly_income_num_rows>0){$data2 = $this->payroll->get_monthly_component($monthly_income_id)->result_array();}else{$data2 = $this->payroll->get_master_payroll_component($master_payroll_id)->result_array();}//print_r($this->db->last_query());
        echo json_encode(array('data1'=>$data, 'data2'=>$data2, 'master_num_rows'=>$master_num_rows));
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
        $group_id = $this->input->post('group_id');//print_mz($group_id);
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
        
        $component = array('monthly_component_id' => $this->input->post('monthly_component_id'),
                           'component_id' => $this->input->post('component_id'),
                           'value' => str_replace(',', '', $this->input->post('value')),
                    );
        //print_mz($component);
        
        for($i=0;$i<sizeof($component['component_id']);$i++):
            $component_num_rows = getAll('payroll_monthly_income_component', array('payroll_component_id'=>'where/'.$component['component_id'][$i],'payroll_monthly_income_id'=>'where/'.$monthly_income_id))->num_rows;
            $data2 = array(
                    'payroll_monthly_income_id'=>$monthly_income_id,
                    'payroll_component_id' =>$component['component_id'][$i],
                    'value' =>$component['value'][$i],
                );
            if($component_num_rows>0){$this->db->where('id', $component['monthly_component_id'][$i])->update('payroll_monthly_income_component', $data2);}
                else{$this->db->insert('payroll_monthly_income_component', $data2);}
                //print_r($this->db->last_query());
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

    function cek_period($id, $period_id)
    {
        $num_rows = getAll('payroll_monthly_income', array('employee_id'=>'where/'.$id, 'payroll_period_id'=>'where/'.$period_id))->num_rows();

        echo $num_rows;
    }

    public function print_slip($id, $period_id)
    {
        $this->data['employee_id'] = $id;
        //render employee detail
        $this->data['employee_detail'] = $this->payroll->get_employee_detail($id);
        //lastq();
        $this->data['monthly_income'] = $this->payroll->get_by_id($id, $period_id);
        $monthly_income_id = getValue('id', 'payroll_monthly_income', array('employee_id'=>'where/'.$id, 'payroll_period_id'=>'where/'.$period_id));
        
        $this->data['period'] = getValue('title', 'payroll_period', array('id'=>'where/'.$period_id));

        $table = 'payroll_monthly_income_component';
        $table_join = 'payroll_component';
        $condition = 'payroll_monthly_income_component.payroll_component_id = payroll_component.id';
        $select = "$table.value as value,$table_join.title as component,$table_join.component_type_id as type";
        $this->data['income'] = getJoin($table, $table_join, $condition,'left',$select, array('payroll_monthly_income_id'=>'where/'.$monthly_income_id, 'component_type_id'=>'where/1'))->result();
        $this->data['deduction'] = getJoin($table, $table_join, $condition,'left',$select, array('payroll_monthly_income_id'=>'where/'.$monthly_income_id, 'component_type_id'=>'where/2'))->result();
        //print_mz($this->data['component']);

        //UcapanSelamat
        $this->data['dob']=getValue('birth_dttm', 'hris_persons', array('person_id'=>'where/'.$id)); 
        $this->data['family_dob']=getAll('hris_employee_family', array('employee_id'=>'where/'.$id)); 
        $this->load->library('mpdf60/mpdf');
        $html = $this->load->view('payroll/monthly_income/payroll_slip', $this->data, true);
        $stylesheet = file_get_contents('assets/modules/css/payroll/mpdfstyletables.css');
        $mpdf = new mPDF();
        $mpdf = new mPDF('A4');
        $mpdf->WriteHTML($stylesheet,1);    // The parameter 1 tells that this is css/style only and no body/html/text 
        $mpdf->WriteHTML($html,2);
        $mpdf->Output('payroll_slip_'.$id.'.pdf', 'I');
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
                    $this->template->add_js('assets/plugins/jquery-autonumeric/autoNumeric.js');
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