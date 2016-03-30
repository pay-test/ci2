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
        $this->data['period'] = getAll('payroll_period', array('year'=>'order/asc', 'month'=>'order/asc'));
        $this->data['ireg_comp'] = GetAllSelect('payroll_component', 'id, title', array('is_annualized'=>'where/0'));
        permission();
        $this->_render_page($this->filename, $this->data);
    }

    public function ajax_period($period = 0) {

        $result = $this->payroll->get_period($period);
        $row = $result->row();
        $status = $row->status;
        echo json_encode(array("status" => $status));
    }

    //FUNGSI BUAT COPY DATA DATA DARI SESSION SEBELUMNYA KE SESSION SEKARANG
    public function generate_new_session()
    {
        $sess_now = sessNow();
        $sess_now = 2016;
        //TABLE COMPONENT_VALUE;
        $comp = getAll('payroll_component_value', array('session_id'=>'where/'.date('year')-1));
        foreach($comp->result() as $c):
            $component = array(
                    'session_id' => $sess_now,
                    'payroll_component_id' => $c->payroll_component_id,
                    'formula' =>$c->formula,
                    'is_condition' => $c->is_condition,
                    'min'=>$c->min,
                    'max'=> $c->max,

                );

        $c_num_rows = getAll('payroll_component_value', array('session_id'=>'where/'.$sess_now, 'payroll_component_id'=>'where/'.$c->payroll_component_id))->num_rows();
        if($c_num_rows>0)$this->db->where('session_id', $sess_now)->where('payroll_component_id', $c->payroll_component_id)->update('payroll_component_value', $component);
        else $this->db->insert('payroll_component_value', $component);
        endforeach;
        $master = getAll('payroll_master', array('session_id'=>'where/'.date('year')-1));
        foreach ($master->result() as $m) {
            $masterz = array('employee_id' => $m->employee_id,
                            'session_id' => $m->session_id);
        
         $m_num_rows = getAll('payroll_master', array('session_id'=>'where/'.$sess_now, 'employee_id'=>'where/'.$m->employee_id))->num_rows();
        if($m_num_rows>0){
            $this->db->where('session_id', $sess_now)->where('employee_id', $m->employee_id)->update('payroll_master', $masterz);
            $m_id = getValue('id', 'payroll_master', array('session_id'=>'where/'.$sess_now, 'employee_id'=>'where/'.$m->employee_id));
        }
        else{ $this->db->insert('payroll_master', $masterz);$m_id=$this->db->insert_id();}
        $m_comp = getAll('payroll_master_component', array('payroll_master_id'=>'where/'.$m->id));
        foreach($m_comp->result() as $m_c):
        $m_compz = array('payroll_master_id' => $m_id,
                         'payroll_component_id' =>$m_c->payroll_component_id,
                         'value'=>$m_c->value,
         );
        $m_comp_num_rows = getAll('payroll_master_component', array('payroll_master_id'=>'where/'.$m_id))->num_rows();
        if($m_comp_num_rows>0){$this->db->where('payroll_master_id', $m_id)->update('payroll_master_component', $m_compz);}
        else{ $this->db->insert('payroll_master_component', $m_compz);}
        endforeach;
    }
        echo json_encode(array("result" => TRUE));
    }

    //FUNGSI BUAT NGITUNG PPH

    public function process() {
        $i = 0;
        //$employee_id = "113";
        $period_id = $this->input->post('period_id');
        $status = $this->input->post('status');
        $data = array('status' => $status);
        $this->db->where('id', $period_id)->update('payroll_period', $data);
        $this->update_monthly($period_id);
        $query = GetAllSelect('payroll_monthly_income','employee_id', array('payroll_period_id' => 'where/'.$period_id))->result();//lastq();
        //print_mz($query);
        //Biaya Jabatan
        
        $bj_persen = getValue('value', 'payroll_biaya_jabatan', array('id'=>'where/1'))/100;
        $bj_max = getValue('max', 'payroll_biaya_jabatan', array('id'=>'where/1'));
       foreach ($query as $value) {
            $emp_id = $value->employee_id;
            $emp_id = 133;
            $filter = array('employee_id'=>'where/'.$emp_id);
            //Nilai PTKP Tiap Karyawan
            $emp_ptkp_id = getValue('payroll_ptkp_id', 'payroll_master', $filter);
            $emp_ptkp = getValue('value', 'payroll_ptkp', array('id'=>'where/'.$emp_ptkp_id));
            $monthly_income_id = getValue('id', 'payroll_monthly_income', array('employee_id'=>'where/'.$emp_id, 'payroll_period_id'=>'where/'.$period_id));//lastq();print_mz($monthly_income_id);

            $ot = $this->get_ot_value($emp_id, $period_id, $monthly_income_id);
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

            $income = $income;
            //$deduction = 1000162-500000; 
 


            /*
            echo '<pre>';
            print_r('income = '.$income.'<br>');
            echo '</pre>';
            echo '<pre>';
            print_r('ded = '.$deduction.'<br>');
            echo '</pre>';
            */
            $ptkp = $emp_ptkp;//echo $ptkp.'-';
            //$income_bruto = $income - $deduction;
            $biaya_jabatan = $income * $bj_persen;//echo $biaya_jabatan.'-';
            $biaya_jabatan = ($biaya_jabatan>$bj_max) ? $bj_max : $biaya_jabatan; 
            $income_netto = $income - $deduction - $biaya_jabatan;//echo $income_netto.'-';
            $income_netto_year = round($income_netto * 12);//echo $income_netto_year.'-';
            $income_netto_year_pembulatan = substr_replace($income_netto_year, '000', -3);
            /*
            if ($value->employee_id == 644) {
                //print_mz($income_netto_year);
            }
            */
            //print_r($income_netto_year_pembulatan.'-');
            $pkp = $income_netto_year_pembulatan - $ptkp;//print_r('pkp = '.$pkp.'-');
            // Pajak Progressif
            $pph_tahun = $this->hitung_pajak_progressif($pkp);//echo $pph_tahun.'-';
            $pph_bulan = round($pph_tahun / 12);//print_mz($pph_bulan);
            $pph_bulan = ($pph_bulan>0) ? $pph_bulan : 0;//print_mz($pph_bulan);
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
            //die();
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

    //FUNGSI BUAT NGAMBIL UPAH OVERTIME DI PERIODE GAJIAN
    function get_ot_value($emp_id, $period_id, $monthly_income_id)
    {
        $is_management = getValue('job_level', 'kg_view_overtime', array('id_employee'=>'where/'.$emp_id));
        $filter_period = array('id'=>'where/'.$period_id);
        $month = getValue('month', 'payroll_period', $filter_period);
        $year = getValue('year', 'payroll_period', $filter_period);
        $date_end = $year.'-'.$month.'-'.'15';
        $date_start = date("Y-m-d", strtotime( date( $date_end, strtotime( date("Y-m-d") ) ) . "-1 month" ) );
        $date_start = date("Y-m-d", strtotime( date( $date_start, strtotime( date("Y-m-d") ) ) . "+1 day" ) );
        //Acc Ovt
        $acc=0;
        $q = GetAll("kg_view_overtime", array("id_employee"=> "where/".$emp_id, "date_full >="=> "where/".$date_start, "date_full <="=> "where/".$date_end, "date_temp"=> "where/0000-00-00"));
        foreach($q->result_array() as $s) {
            if($s['job_level'] != "nonmanagement") {
                if($s['ovt_hour_sum'] >= 2) $acc += $s['ovt_hour_sum'];
            } else $acc += $s['ovt_hour_cal'];
        }

        //print_mz($acc);
        
        $q = GetAll("kg_view_overtime", array("id_employee"=> "where/".$emp_id, "date_temp >="=> "where/".$date_start, "date_temp <="=> "where/".$date_end));
        foreach($q->result_array() as $s) {
            if($s['job_level'] != "nonmanagement") {
                if($s['ovt_hour_sum'] >= 2) $acc += $s['ovt_hour_sum'];
            } else $acc += $s['ovt_hour_cal'];
        }

        if($is_management != "nonmanagement") {
            $upah = $acc * GetConfigDirect('rest_time');
            $component_id = 82;
             $num_rows = GetAllSelect('payroll_monthly_income_component','payroll_component_id', array('payroll_monthly_income_id' => 'where/'.$monthly_income_id, 'payroll_component_id'=>'where/'.$component_id))->num_rows();
            $data = array('payroll_monthly_income_id' => $monthly_income_id,
                          'payroll_component_id' => $component_id,
                          'value' => $upah,
             );
            if($num_rows>0){$this->db->where('payroll_monthly_income_id', $monthly_income_id)->where('payroll_component_id', $component_id)->update('payroll_monthly_income_component', $data);}else{$this->db->insert('payroll_monthly_income_component', $data);} 
        }else{
            $upah = $acc * ( GetGapok($emp_id, $date_start) + GetHA($emp_id, $date_start) ) / GetConfigDirect('total_hour_ovt');
            $component_id = 82;
            $num_rows = GetAllSelect('payroll_monthly_income_component','payroll_component_id', array('payroll_monthly_income_id' => 'where/'.$monthly_income_id, 'payroll_component_id'=>'where/'.$component_id))->num_rows();
            $data = array('payroll_monthly_income_id' => $monthly_income_id,
                          'payroll_component_id' => $component_id,
                          'value' => $upah,
             );
            if($num_rows>0){$this->db->where('payroll_monthly_income_id', $monthly_income_id)->where('payroll_component_id', $component_id)->update('payroll_monthly_income_component', $data);}else{$this->db->insert('payroll_monthly_income_component', $data);} 
        }
        $ot_rasio = GetOTRasio($emp_id, $date_end);//$upah / (GetGapok($emp_id, $exp[0]) + GetHA($emp_id, $exp[0]) + $upah) * 100;
        return $upah;
        //$data[] = array($no, $r->ext_id, $r->person_nm, GetMonth(intval(substr($tgl,16,2))).' '.substr($tgl,11,4), $r->ovt_hour_sum, $acc, Decimal($ot_rasio)."%", Rupiah($upah), $edit);
      
    }

    //FUNGSI BUAT UPDATE NILAI GAJI BULANAN NGAMBIL DARI MASTER
    public function update_monthly($period_id)
    {
        $employee_id = GetAllSelect('payroll_master', 'employee_id', array())->result();
        foreach($employee_id as $e):
            $num_rows = getAll('payroll_monthly_income', array('employee_id'=>'where/'.$e->employee_id, 'payroll_period_id'=>'where/'.$period_id))->num_rows();
            $monthly_income_id = getValue('id', 'payroll_monthly_income', array('employee_id'=>'where/'.$e->employee_id, 'payroll_period_id'=>'where/'.$period_id));
            $old_group = getValue('payroll_group_id', 'payroll_monthly_income', array('employee_id'=>'where/'.$e->employee_id, 'payroll_period_id'=>'where/'.$period_id));
            $group_id = getValue('payroll_group_id', 'payroll_master', array('employee_id'=>'where/'.$e->employee_id));//print_mz($group_id);
            $payroll_ptkp_id = getValue('payroll_ptkp_id', 'payroll_master', array('employee_id'=>'where/'.$e->employee_id));
            $payroll_currency_id = getValue('payroll_currency_id', 'payroll_master', array('employee_id'=>'where/'.$e->employee_id));
            $payroll_tax_method_id = getValue('payroll_tax_method_id', 'payroll_master', array('employee_id'=>'where/'.$e->employee_id));
            $is_expatriate = getValue('is_expatriate', 'payroll_master', array('employee_id'=>'where/'.$e->employee_id));
            //print_mz($group_id);
            //if($old_group != $group_id)$this->db->where('payroll_monthly_income_id', $monthly_income_id)->update('payroll_monthly_income_component', array('is_deleted' => 1));
            $data = array(
                    'employee_id' => $e->employee_id,
                    'payroll_group_id' => $group_id,
                    'payroll_period_id' => $period_id,
                    'payroll_ptkp_id'=>$payroll_ptkp_id,
                    'payroll_currency_id'=>$payroll_currency_id,
                    'payroll_tax_method_id'=>$payroll_tax_method_id,
                    'is_expatriate'=>$is_expatriate
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
                $is_reguler = getValue('is_annualized', 'payroll_component', array('id'=>'where/'.$c->payroll_component_id));
                if($is_reguler == 1){
                    if($component_num_rows>0){$this->db->where('id', $master_component_id)->update('payroll_monthly_income_component', $data2);
                    }else{$this->db->insert('payroll_monthly_income_component', $data2);}
                }
                    //print_r($this->db->last_query());
            endforeach;
        endforeach;
        return true;
    }

    function hitung_pajak_progressif($pkp)
    {
        $p1_max = getValue('value_max', 'payroll_tax_progressive', array('id'=>'where/1'));
        $p2_min = getValue('value_min', 'payroll_tax_progressive', array('id'=>'where/2'));
        $p3_min = getValue('value_min', 'payroll_tax_progressive', array('id'=>'where/3'));
        $p4_min = getValue('value_min', 'payroll_tax_progressive', array('id'=>'where/4'));
        $p2_max = getValue('value_max', 'payroll_tax_progressive', array('id'=>'where/2'));
        $p3_max = getValue('value_max', 'payroll_tax_progressive', array('id'=>'where/3'));
        if($pkp<=$p1_max){
            $prg = $pkp * 5/100;
        }elseif($pkp>$p2_min&&$pkp<=$p2_max){
            $prg1 = $p1_max * 5/100;
            $prg2 = ($pkp - $p1_max) * 15/100;
            $prg = $prg1 + $prg2;
        }elseif($pkp>$p3_min&&$pkp<=$p3_max){
            $prg1 = $p1_max * 5/100;//print_ag($prg1);
            $prg2 = ($p2_max-$p1_max) * 15/100;//print_ag($prg2);
            $prg3 = ($pkp - $p2_max) * 25/100;
            $prg = $prg1  + $prg2 + $prg3;
        }elseif($pkp>$p4_min){
            $prg1 = $p1_max * 5/100;//print_ag($prg1);
            $prg2 = ($p2_max-$p1_max) * 15/100;//print_ag($prg2);
            $prg3 = ($p3_max-$p2_max) * 25/100;//print_ag($prg3);
            $prg4 = ($pkp - $p3_max) * 30/100;//print_ag($prg4);
            $prg = $prg1  + $prg2 + $prg3 + $prg4;
        }

        return $prg;
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

    // FUNCTION BUAT NGITUNG BASIC SALARY
    function generate_value() {
        //set session
        $y = date('Y');
        $start_ses = $y."-04-01 00:00:00";
        $session = (date('Y-m-d H:i:s') < $start_ses) ? $y-1 : $y;//print_mz($session); 
        //$session = 2016;
        $asid = 14;
        $employee_id = 644;
        //generate configuration
        $actual_allowance = 2000;
        $employee_jam = 75/100;
        $std_jam = 79/100;
        $exchange_rate = GetValue('value','payroll_exchange_rate',array('session_id' => 'where/'.$session));
        $divider = GetValue('value','payroll_pembagi',array('session_id' => 'where/'.$session));

        //job match parameter
        $jm_param = getAll('payroll_jm_parameter',array('session_id' => 'where/'.$session));//lastq();
        $row = $jm_param->row();
        $jm_min = $row->min/100;
        $jm_max = $row->max/100;

        //$employee = GetAll('hris_employee',array('status_cd' => 'where/normal', 'employee_id' => 'where/'.$employee_id));
        
        $employee = $this->db->query("SELECT * FROM (`hris_employee`) WHERE `status_cd` = 'normal' AND `employee_id` != 1 AND `employee_id` != 441");//lastq();
        //$employee = $this->db->query("SELECT * FROM (`hris_employee`) WHERE `status_cd` = 'normal' AND `employee_id` = '$employee_id'");//lastq();
        foreach ($employee->result_array() as $emp) {
            $employee_id = $emp['employee_id'];
            //$employee_id = 644;
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
            $payroll_master_num_rows = getAll('payroll_master', array('employee_id'=>'where/'.$employee_id, 'session_id'=>'where/'.$session))->num_rows();
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
                    //print_ag('gs'.$gs);
                $fix_gs_diff = $fix_val - $gs;
                //print_ag('fixvalgsdiff'.$fix_gs_diff);
                if ($employee_jm >= $jm_min AND $employee_jm <= $jm_max) {
                    $jm_diff = ($employee_jm - $jm_min) * 100;
                    //value per 1%
                    $vp1 = $fix_gs_diff / (($jm_max - $jm_min)*100);//print_mz($vp1);
                    $fix_value = ($jm_diff) * $vp1;

                    $fix_value = $fix_value + $gs; // fix value
                    //print_mz($jm_min);
                }elseif ($employee_jm <= $jm_min) {
                    $fix_value = $gs;
                }elseif ($employee_jm >= $jm_max) {
                    $fix_value = $fix_val;
                }

                //print_ag('jm_diff'.$jm_diff);
                //print_ag('vp1'.$vp1);
                //print_ag('fix_value'.$fix_value);
                /*
                print_ag('fixgsdiff'.$fix_gs_diff);
                print_ag($jm_max. '-' .$jm_min);
                */
                //print_mz($fix_value);

                //count VAR compensation
               /* $master_id = getValue('id','payroll_master', array('employee_id'=>'where/'.$employee_id, 'session_id'=>'where/'.$session));
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
    */
                $total_sal = $fix_value; //+ $var_value;
                $total_sal = ($total_sal * $exchange_rate) / $divider;
                //print_ag($total_sal);
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
                //$total_sal = $total_sal;
            }
            //print_mz($jvp);
            
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
                //lastq();
            $hous_component = GetAll('payroll_master_component',array('payroll_master_id' => 'where/'.$master_id, 'payroll_component_id' => 'where/66'));
            $hous_num_row = $hous_component->num_rows();
            //$formula = getValue('formula', 'payroll_component_value', array('payroll_component_id'=>'where/66', 'session_id'=>'where'.$session_id));
            $formula = $total_sal * (10/100);
            if($formula>800000){
                $formula = 800000;
            }elseif($formula<400000){
                $formula = 400000;
            }else{
                $formula = $total_sal*(10/100);
            }
            //die();
            if ($hous_num_row > 0) {
                $this->db->where('payroll_master_id', $master_id)->where('payroll_component_id', 66)->update('payroll_master_component',array('value'=>$formula));
            } else {
                $data_insert = array(
                    'payroll_master_id' => $master_id,
                    'payroll_component_id' => 66,
                    'value' => $formula
                    );
                $this->all_model->Insert('payroll_master_component',$data_insert);
            }
            //echo'<pre>';print_r($this->db->last_query());echo '</pre>';
        }
            echo json_encode(array('st'=>1));
    }
}
?>