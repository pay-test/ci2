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
        $employee_id = "113";
        $period_id = $this->input->post('period_id');
        $status = $this->input->post('status');
        $data = array('status' => $status);
        $this->db->where('id', $period_id)->update('payroll_period', $data);//lastq();
        //$this->update_monthly($period_id);
        $query = GetAllSelect('payroll_monthly_income','employee_id', array('payroll_period_id' => 'where/'.$period_id))->result();//lastq();
        //print_mz($query);
       foreach ($query as $value) {
           //print_mz($value->employee_id);
            $monthly_income_id = getValue('id', 'payroll_monthly_income', array('employee_id'=>'where/'.$value->employee_id, 'payroll_period_id'=>'where/'.$period_id));//lastq();print_mz($monthly_income_id);
            $q = $this->payroll->get_monthly_income($monthly_income_id)->result();//print_mz($q);
            /*echo '<pre>';
            print_r($q);
            echo '</pre>';
            echo '<pre>';
            print_r($q);
            echo '</pre>';*/
            //$q = getAll('payroll_monthly_income_component', array('payroll_monthly_income_id'=>'where/'.$monthly_income_id))->result();
           //lastq();

            $income = 0;
            $deduction = 0;
            foreach ($q as $valuex) {
                //print_r('*'.$valuex->tax_component_id);
                
                if ($valuex->component_type_id == 1) {
                    //print_r($valuex->employee_id.'-'.'<br/>'.$valuex->value.'-');
                    $income = $income + $valuex->value;
                    //print_r($income."<br>");
                }

                if ($valuex->component_type_id == 2 && $valuex->component_id != 55) {
                    //print_r($valuex->employee_id.'-'.'<br/>'.$valuex->value.'-');
                    $deduction = $deduction + $valuex->value;
                    //print_r($income."<br>");
                }
            }
            /*
            echo '<pre>';
            print_r('income = '.$income.'<br>');
            echo '</pre>';
            echo '<pre>';
            print_r('ded = '.$deduction.'<br>');
            echo '</pre>';
            */
            $ptkp = getValue('value',' payroll_ptkp', array('id'=>'where/1'));//echo $ptkp.'-';
            //$income_bruto = $income - $deduction;
            $biaya_jabatan= $income * (5/100);//echo $biaya_jabatan.'-';
            $income_netto = $income - $deduction - $biaya_jabatan;//echo $income_netto.'-';
            $income_netto_year = round($income_netto * 12);//echo $income_netto_year.'-';
            $income_netto_year_pembulatan = substr_replace($income_netto_year, '000', -3);
            //print_r($income_netto_year_pembulatan.'-');
            $pkp = $income_netto_year_pembulatan - $ptkp;//print_r('pkp = '.$pkp.'-');
            $pph_tahun = $pkp * (5/100);//echo $pph_tahun.'-';
            $pph_bulan = round($pph_tahun / 12);//print_mz($pph_bulan);
            $pph_component_id = 55;
            $pph_num_rows = GetAllSelect('payroll_monthly_income_component','payroll_component_id', array('payroll_monthly_income_id' => 'where/'.$monthly_income_id, 'payroll_component_id'=>'where/'.$pph_component_id))->num_rows();
            $data = array('payroll_monthly_income_id' => $monthly_income_id,
                          'payroll_component_id' => $pph_component_id,
                          'value' => $pph_bulan,
             );
            if($pph_num_rows>0){$this->db->where('payroll_monthly_income_id', $monthly_income_id)->where('payroll_component_id', $pph_component_id)->update('payroll_monthly_income_component', $data);}else{$this->db->insert('payroll_monthly_income_component', $data);} 
         /*
       echo '<pre>';
            print_r($this->db->last_query());
            echo '</pre>';
            */
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

    public function update_monthly($period_id)
    {
        //$this->_validate();
        //$employee_id = $this->input->post('employee_id');
        $employee_id = GetAllSelect('payroll_master', 'employee_id', array())->result();
        foreach($employee_id as $e):
            $num_rows = getAll('payroll_monthly_income', array('employee_id'=>'where/'.$e->employee_id, 'payroll_period_id'=>'where/'.$period_id))->num_rows();
            $monthly_income_id = getValue('id', 'payroll_monthly_income', array('employee_id'=>'where/'.$e->employee_id, 'payroll_period_id'=>'where/'.$period_id));
            $old_group = getValue('payroll_group_id', 'payroll_monthly_income', array('employee_id'=>'where/'.$e->employee_id, 'payroll_period_id'=>'where/'.$period_id));
            $group_id = getValue('payroll_group_id', 'payroll_master', array('employee_id'=>'where/'.$e->employee_id));//print_mz($group_id);
            //print_mz($group_id);
            //if($old_group != $group_id)$this->db->where('payroll_monthly_income_id', $monthly_income_id)->update('payroll_monthly_income_component', array('is_deleted' => 1));
            $data = array(
                    'employee_id' => $e->employee_id,
                    'payroll_group_id' => $group_id,
                    'payroll_period_id' => $period_id,
                );
            if($num_rows>0)$this->db->where('employee_id', $e->employee_id)->where('payroll_period_id', $period_id)->update('payroll_monthly_income', $data);
                else $this->db->insert('payroll_monthly_income', $data);//lastq();
                //print_r($this->db->last_query());
            $monthly_income_id = ($num_rows>0) ? $monthly_income_id : $this->db->insert_id();
            $monthly_group = getAll('payroll_monthly_income', array('employee_id'=>'where/'.$e->employee_id, 'payroll_group_id'=>'where/'.$old_group))->num_rows();//print_mz($monthly_group);
            $master_id = getValue('id', 'payroll_master', array('employee_id'=>'where/'.$e->employee_id));
            $component= GetAllSelect('payroll_master_component', 'payroll_component_id, value', array('payroll_master_id'=>'where/'.$master_id))->result();
            
            //print_mz($component);
            
            foreach($component as $c):
                $component_num_rows = getAll('payroll_monthly_income_component', array('payroll_component_id'=>'where/'.$c->payroll_component_id,'payroll_monthly_income_id'=>'where/'.$monthly_income_id))->num_rows;
                $master_component_id = getValue('id', 'payroll_monthly_income_component', array('payroll_component_id'=>'where/'.$c->payroll_component_id,'payroll_monthly_income_id'=>'where/'.$monthly_income_id));
                $data2 = array(
                        'payroll_monthly_income_id'=>$monthly_income_id,
                        'payroll_component_id' =>$c->payroll_component_id,
                        'value' =>$c->value,
                    );
                if($component_num_rows>0){$this->db->where('id', $master_component_id)->update('payroll_monthly_income_component', $data2);}
                    else{$this->db->insert('payroll_monthly_income_component', $data2);}
                    //print_r($this->db->last_query());
            endforeach;
        endforeach;
        return true;
    }
/*
    public function set_periode() {
        $period_id = $this->input->post('periode2');
        $status = $this->input->post('status');
        $data = array('status' => $status);
        $where = array('id' => $period_id);
        $update = $this->payroll->update($where,$data);
        echo json_encode(array("periode" => $period_id, "status"=> $status, "is_update" => $update));
    }
*/
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
        $y = date('Y');
        $start_ses = $y."-04-01 00:00:00";
        $session = (date('Y-m-d H:i:s') < $start_ses) ? $y-1 : $y;//print_mz($session); 
        $session = 2016;
        $asid = 14;
        //$employee_id = 644;
        //generate configuration
        $actual_allowance = 2000;
        $employee_jam = 75/100;
        $std_jam = 79/100;
        $exchange_rate = GetValue('value','payroll_exchange_rate',array('session_id' => 'where/'.$session));
        $divider = GetValue('value','payroll_pembagi',array('session_id' => 'where/'.$session));

        //job match parameter
        $jm_param = GetAll('payroll_jm_parameter',array('session_id' => $session));
        $row = $jm_param->row();
        $jm_min = $row->min/100;
        $jm_max = $row->max/100;
        //print_mz($jm_param);

        //$employee = GetAll('hris_employee',array('status_cd' => 'where/normal', 'employee_id' => 'where/'.$employee_id));
        
        $employee = $this->db->query("SELECT * FROM (`hris_employee`) WHERE `status_cd` = 'normal' AND `employee_id` != 1 AND `employee_id` != 441");//lastq();
        //$employee = $this->db->query("SELECT * FROM (`hris_employee`) WHERE `status_cd` = 'normal' AND `employee_id` = '$employee_id'");//lastq();
        foreach ($employee->result_array() as $emp) {
            $employee_id = $emp['employee_id'];
            $employee_id = 644;
            $employee_jm = GetValue('jm','hris_employee_competency_final_recap',array('asid' => 'where/'.$asid, 'employee_id' => 'where/'.$employee_id))/100;
            //print_mz($employee_jm*100);
            //die($employee_id);
            if ($employee_jm > 0) {
                $employee_jm = $employee_jm;
            } else {
                $employee_jm = 75/100;
            }

           $employee_jm = 75/100;

            //employee job match
           //print_mz($employee_jm);
            $employee_job_id = getValue('job_id', 'hris_employee_job', array('employee_id'=>'where/'.$employee_id));
            $job_value_id = getValue('job_value_id', 'hris_jobs', array('job_id'=>'where/'.$employee_job_id));
            $detail = $this->payroll->get_employee_detail($employee_id);
            $det = $detail->row();
            //print_mz($row);

            $filter = array(
                'session_id' => 'where/'.$session,
                'job_value_id' => 'where/'.$job_value_id,
                'job_class_id' => 'where/'.$det->job_class_id
                );
            $job_value_matrix = GetAll('payroll_job_value_matrix',$filter);//lastq();
            $job_value_matrix_num = GetAll('payroll_job_value_matrix',$filter)->num_rows();//lastq();
            $jvm = ($job_value_matrix_num>0)?$job_value_matrix->row():0;
            $job_class_id = (!empty($det->job_class_id)) ? $det->job_class_id : 0;
            $data_master = array(
                'session_id'=>$session,
                            'employee_id'=>$employee_id,
                            'payroll_group_id'=>$job_class_id
                                );
            $payroll_master_num_rows = getAll('payroll_master', array('employee_id'=>'where/'.$employee_id))->num_rows();
            if($payroll_master_num_rows<1)$this->db->insert('payroll_master', $data_master);
            //print_mz($job_value_matrix);

            //generate configuration
            //compensation mix parameter
            $cm_param = GetAll('payroll_compensation_mix', array('session_id' => 'where/'.$session, 'job_class_id' => 'where/'.$job_class_id));
            $cm_param_num = GetAll('payroll_compensation_mix', array('session_id' => 'where/'.$session, 'job_class_id' => 'where/'.$job_class_id))->num_rows();
            //lastq();
            $row = $cm_param->row();
            $var = ($cm_param_num>0)?$row->var/100:0;
            $fix = ($cm_param_num>0)?$row->fix/100:0;
            //print_mz($var." ".$fix);
            //print_r($employee_id);
            //print_r($det->job_level);echo '<br/>';print_r($jvm);
            if ($det->job_level == 'management') {
                $jvp = ($job_value_matrix_num!=0)?$jvm->value:0; //job value point
                //print_r('jvp = '.$jvp);
                
                //count FIX compensation
                $fix_val = $jvp * $fix;
                //print_r(' fix value = '.$fix_val);
                $gs = $fix_val * (67/100); //guarantee salary

                $fix_gs_diff = $fix_val - $gs;
                //print_mz($gs);
                if ($employee_jm >= $jm_min AND $employee_jm <= $jm_max) {
                    $jm_diff = $employee_jm - $jm_min;
                    //value per 1%
                    $vp1 = $fix_gs_diff / ($jm_max - $jm_min);
                    $fix_value = ($jm_diff) * $vp1;

                    $fix_value = $fix_value + $gs; // fix value
                    //print_mz($jm_min);
                }elseif ($employee_jm <= $jm_min) {
                    $fix_value = $gs;
                }elseif ($employee_jm >= $jm_max) {
                    $fix_value = $fix_val;
                }

                //print_mz($fix_value);

                //count VAR compensation
                $master_id = getValue('id','payroll_master', array('employee_id'=>'where/'.$employee_id, 'session_id'=>'where/'.$session));
                $meal = getValue('value', 'payroll_master_component', array('payroll_master_id'=>'where/'.$master_id, 'payroll_component_id'=>'where/73'));
                $housing = getValue('value', 'payroll_master_component', array('payroll_master_id'=>'where/'.$master_id, 'payroll_component_id'=>'where/66'));
                $transport = getValue('value', 'payroll_master_component', array('payroll_master_id'=>'where/'.$master_id, 'payroll_component_id'=>'where/112'));
                $actual_allowance = $meal+$housing+$transport;
                $var_value = $jvp * $var;
                $pip = $var_value - $actual_allowance;
                print_mz($var_value);
                $data_pip = array('payroll_component_id' => 84,
                                  'payroll_master_id'=> $master_id,
                                  'value'=>$pip,
                 );
                $pip_num_row = GetAllSelect('payroll_master_component', 'payroll_component_id, payroll_master_id', array('payroll_master_id'=>'where/'.$master_id, 'payroll_component_id'=>'where/84'))->num_rows();
                if($pip_num_row>0):
                    $this->db->where('payroll_component_id', 84)->update('payroll_master_component', $data_pip);
                else:
                    $this->db->insert('payroll_master_component', $data_pip);
                endif;//lastq();
                //print_ag($employee_id);
                //print_ag($actual_allowance);
                if ($pip > $var_value) {
                    $pip = $var_value;
                }else if($pip < $var_value) {
                    $pip = $pip;
                }
                $var_value = round(($employee_jam / $std_jam) * $pip);
                //print_mz(round($var_value));

                $total_sal = $fix_value; //+ $var_value;
                //print_mz($salary);
            }else if($det->job_level == 'nonmanagement') {
                $min_range = ($job_value_matrix_num!=0)?$jvm->value_min:0;
                $max_range = ($job_value_matrix_num!=0)?$jvm->value_max:0;
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
                //print_mz($salary_1);
            }

            $total_sal = ($total_sal * $exchange_rate) / $divider;
            //print_mz($total_sal);
            $data = array('value' => $total_sal);
            $master_id = GetValue('id','payroll_master',array('employee_id' => 'where/'.$employee_id, 'session_id' => 'where/'.$session));
            //check if component Salary on master is exist
            $sal_component = GetAll('payroll_master_component',array('payroll_master_id' => 'where/'.$master_id, 'payroll_component_id' => 'where/60'));
            $row = $sal_component->row();//print_mz($row);
            //print_mz($master_component_id);
            $component_num_row = $sal_component->num_rows();
            //lastq();
            if ($component_num_row > 0) {

                $master_component_id = $row->id;
                $this->db->where('payroll_master_id', $master_id)->where('payroll_component_id', 60)->update('payroll_master_component',$data);
            } else {
                $data_insert = array(
                    'payroll_master_id' => $master_id,
                    'payroll_component_id' => 60,
                    'value' => $total_sal
                    );
                $this->all_model->Insert('payroll_master_component',$data_insert);
            }
            //echo'<pre>';print_r($this->db->last_query());echo '</pre>';
        }
            echo json_encode(array('st'=>1));
    }
}
?>