<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Payroll_setup extends MX_Controller {
    
    var $title = "payroll";
    var $page_title = "Setup";
    var $filename = "payroll_setup";
    public $data;
    function __construct()
    {
        parent::__construct();
        $this->load->model('payroll_setup_model','payroll');
        $this->load->model('all_model','all_model');
    }
    
    function index()
    {
        $this->data['title'] = ucfirst($this->title);
        $this->data['page_title'] = $this->page_title;

        $year_now = date('Y');
        $this->data['period'] = $this->payroll->render_periode($year_now);

        permission();
        $this->_render_page($this->filename, $this->data);
    }

    public function ajax_period($period = 0) {

        $result = $this->payroll->get_period($period);
        $row = $result->row();
        $status = $row->status;
        echo json_encode(array("status" => $status));
    }

    public function process() {
        $i = 0;
        $employee_id = "";
        $income = 0;
        $deduction = 0;
        $period_id = $this->input->post('period');

        $query = GetAllSelect('payroll_monthly_income','employee_id', array('payroll_period_id' => 'where/'.$period_id))->result();//lastq();
        
       foreach ($query as $row => $value) {
           //print_mz($value->employee_id);
        $monthly_income_id = getValue('id', 'payroll_monthly_income', array('employee_id'=>'where/'.$value->employee_id, 'payroll_period_id'=>'where/'.$period_id));//print_mz($monthly_income_id);
            $q = $this->payroll->get_monthly_income($monthly_income_id)->result();
           //lastq();
            foreach ($q as $rowx => $valuex) {
                //print_r('*'.$valuex->tax_component_id);
                
                if ($valuex->component_type_id == 1) {
                    //print_r($valuex->employee_id.'-'.'<br/>'.$valuex->value.'-');
                    $income = $income + $valuex->value;
                    //print_r($income."<br>");
                }

                if ($valuex->component_type_id == 2) {
                    //print_r($valuex->employee_id.'-'.'<br/>'.$valuex->value.'-');
                    $deduction = $deduction + $valuex->value;
                    //print_r($income."<br>");
                }
            }
            $ptkp = getValue('value',' payroll_ptkp', array('title'=>'where/'.'TK/0'));
            //$income_bruto = $income - $deduction;
            $biaya_jabatan= $income * (5/100);
            $income_netto = $income - $deduction - $biaya_jabatan;
            $income_netto_year = round($income_netto * 12);//echo $income_netto_year;
            $income_netto_year_pembulatan = substr_replace($income_netto_year, '000', -3);
            //print_mz($income_netto_year_pembulatan);
            $pkp = $income_netto_year_pembulatan - $ptkp;
            $pph_tahun = $pkp * (5/100);
            $pph_bulan = round($pph_tahun / 12);
            $pph_component_id = 55;
            $pph_num_rows = GetAllSelect('payroll_monthly_income_component','payroll_component_id', array('payroll_monthly_income_id' => 'where/'.$monthly_income_id, 'payroll_component_id'=>'where/'.$pph_component_id))->num_rows();
            $data = array('payroll_monthly_income_id' => $monthly_income_id,
                          'payroll_component_id' => $pph_component_id,
                          'value' => $pph_bulan,
             );
            if($pph_num_rows>0){$this->db->where('payroll_monthly_income_id', $monthly_income_id)->where('payroll_component_id', $pph_component_id)->update('payroll_monthly_income_component', $data);}else{$this->db->insert('payroll_monthly_income_component', $data);} 
       }
       
        $query = $this->payroll->get_monthly_income($period_id)->result(); //get all monthly income on the same period
        foreach ($query as $row => $value) {
            $employee_id = $value->employee_id;
            $component_id = $value->component_id;
            $component_value = $value->value;
            $tax_component_id = $value->component_type_id;
        }
        echo json_encode(array("result" => TRUE));
    }

    public function set_periode() {
        $period_id = $this->input->post('periode2');
        $status = $this->input->post('status');
        $data = array('status' => $status);
        $where = array('id' => $period_id);
        $update = $this->payroll->update($where,$data);
        echo json_encode(array("periode" => $period_id, "status"=> $status, "is_update" => $update));
    }

    public function ajax_list()
    {
        $list = $this->payroll->get_datatables();//lastq();//print_mz($list);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $p_comp) {
            //get component type
            $component_type = $this->all_model->GetValue('title','payroll_setup_type','id = '.$p_comp->component_type_id);
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

             $row[] = '<a class="btn btn-sm btn-primary" href="javascript:void(0);" username="Edit" onclick="edit_user('."'".$p_comp->id."'".')"><i class="fa fa-pencil"></i></a>
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

    public function ajax_edit($id)
    {
        $data = $this->payroll->get_by_id($id); // if 0000-00-00 set tu empty for datepicker compatibility
        echo json_encode($data);
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
                'title' => $this->input->post('title'),
                'code' => $this->input->post('code'),
                'component_type_id' => $this->input->post('component_type_id'),
                'is_annualized' => $this->input->post('is_annualized'),
                'tax_component_id' => $this->input->post('tax_component_id'),
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

    function generate_value() {
        //set session
        $session = 2016;
        $asid = 14;
        //generate configuration
        $actual_allowance = 2000;
        $employee_jam = 75/100;
        $std_jam = 79/100;
        //job match parameter
        $jm_param = GetAll('payroll_jm_parameter',array('session_id' => $session));
        $row = $jm_param->row();
        $jm_min = $row->min/100;
        $jm_max = $row->max/100;
        //print_mz($jm_param);

        $employee = GetAll('hris_employee',array('status_cd' => 'where/normal', 'employee_id' => 'where/644'));
        foreach ($employee->result_array() as $emp) {
            $employee_id = $emp['employee_id'];
            $employee_jm = GetValue('jm','hris_employee_competency_final_recap',array('asid' => 'where/'.$asid, 'employee_id' => 'where/'.$employee_id))/100;
            //print_mz($employee_jm);
            if ($employee_jm > 0) {
                $employee_jm = $employee_jm/100;
            } else {
                $employee_jm = 75/100;
            }
            
            $detail = $this->payroll->get_employee_detail($employee_id);
            $det = $detail->row();
            //print_mz($row);

            $filter = array(
                'session_id' => 'where/'.$session,
                //'org_id' => 'where/'.$det->org_id,
                'job_class_id' => 'where/'.$det->job_class_id
                );
            $job_value_matrix = GetAll('payroll_job_value_matrix',$filter);
            $jvm = $job_value_matrix->row();
            //print_mz($jvm);

            //generate configuration
            //compensation mix parameter
            $cm_param = GetAll('payroll_compensation_mix', array('session_id' => 'where/'.$session, 'job_class_id' => 'where/'.$det->job_class_id));
            //lastq();
            $row = $cm_param->row();
            $var = $row->var/100;
            $fix = $row->fix/100;
            //print_mz($var." ".$fix);

            if ($det->job_level == 'management') {
                $jvp = $jvm->value; //job value point
                $gs = $jvp * (67/100); //guarantee salary
                
                //count FIX compensation
                $fix_val = $jvp * $fix;
                $fix_gs_diff = $fix_val - $gs;
                //print_mz($fix_gs_diff);
                if ($employee_jm >= $jm_min AND $employee_jm <= $jm_max) {
                    $jm_diff = $employee_jm - $jm_min;
                    //value per 1%
                    $vp1 = $fix_gs_diff / (($jm_max - $jm_min));
                    $fix_value = ($jm_diff) * $vp1;

                    $fix_value = $fix_value + $gs; // fix value
                    //print_mz($fix_value);
                }elseif ($employee_jm <= $jm_min) {
                    $fix_value = $gs;
                }elseif ($employee_jm >= $jm_max) {
                    $fix_value = $fix_val;
                }

                //print_mz($fix_value);

                //count VAR compensation
                $var_value = $jvp * $var;
                $pip = $var_value - $actual_allowance;

                if ($pip > $var_value) {
                    $pip = $var_value;
                }else if($pip < $var_value) {
                    $pip = $pip;
                }

                $var_value = round(($employee_jam / $std_jam) * $pip);
                //print_mz(round($var_value));

                $total_sal = $fix_value + $var_value;
                //print_mz($salary);
            }else if($det->job_level == 'nonmanagement') {
                $min_range = $jvm->value_min;
                $max_range = $jvm->value_max;
                $grade = $det->gradeval_top;
                //print_mz($max_range);
                if ($grade == 6) {
                    $min_jm_p = 75/100;
                }elseif ($grade == 5) {
                    $min_jm_p = 60/100;
                }else{
                    $min_jm_p = 40/100;
                }
                //print_mz($min_jm_p);
                //count FIX compensation
                $fix = $max_range * $fix;
                //print_mz($fix);
                $range_salary = $fix - $min_range;
                $range = 1 - $min_jm_p;
                //print_mz($range);

                $val = ($range_salary / $range);
                //print_mz($val);
                $fix_value = ($employee_jm - $min_jm_p) * $val;
                $salary_1 = $fix_value + $min_range;
                //print_mz($salary_1);

                //count VAR compensation
                $var = $max_range * $var;
                $salary_2 = $employee_jam * $var;
                
                //SALARY
                $total_sal = $salary_1 + $salary_2;
                //print_mz($total_sal);
            }

            print_mz($total_sal);
            
        }
    }
}
?>