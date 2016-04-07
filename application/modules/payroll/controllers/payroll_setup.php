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

    //deni_dev
    //FUNGSI BUAT NGITUNG PPH

    public function process() {
        $i = 0;
        //$employee_id = 300;
        $period_id = $this->input->post('period_id');
        $status = $this->input->post('status');
        $data = array('status' => $status);
        $this->db->where('id', $period_id)->update('payroll_period', $data);
        //$this->cek_master_component();
        
        $query = GetAllSelect('payroll_monthly_income','employee_id', array('payroll_period_id' => 'where/'.$period_id))->result();//lastq();
        //print_mz($query);

        //Biaya Jabatan
        $bj_persen = getValue('value', 'payroll_biaya_jabatan', array('id'=>'where/1'))/100;
        $bj_max = getValue('max', 'payroll_biaya_jabatan', array('id'=>'where/1'));
        foreach ($query as $value) {
            $emp_id = $value->employee_id;
            $deduction = 0;
            $income_tax = 0;
            $total_biaya_jabatan = 0;
            $total_income = 0;
            $total_ireguler_income = 0;
            $total_jk_jkk = 0;

            //get component from master
            $this->update_monthly($emp_id,$period_id);//lastq();
            //hitung pph bulan berjalan
            $curr_month = getValue('month','payroll_period',array('id' => 'where/'.$period_id));
            $curr_year = getValue('year','payroll_period',array('id' => 'where/'.$period_id));
            //print_mz($curr_month);
            for ($i=1; $i <= (int)$curr_month; $i++) {
                $income = 0;
                $ireguler_income = 0;
                $jk_jkk = 0;
                $income_periode = $i;
                $period_id = getValue('id','payroll_period',array('year' => 'where/'.$curr_year, 'month' => 'where/'.str_pad($i,2,'0',STR_PAD_LEFT)));
                $monthly_income_id = getValue('id', 'payroll_monthly_income', array('employee_id'=>'where/'.$emp_id, 'payroll_period_id'=>'where/'.$period_id));//lastq();print_mz($monthly_income_id);
                //get overtime
               // $ot = $this->get_ot_value($emp_id, $period_id, $monthly_income_id);
               // $ot = 1800000;
                //print_mz($ot);
                $q = $this->payroll->get_monthly_income($monthly_income_id)->result();//print_mz($q);
                //print_mz($i);

                foreach ($q as $valuex) {
                    //print_r('*'.$valuex->tax_component_id);
                    
                    if ($valuex->component_type_id == 1 && $valuex->is_annualized == 1) {
                        //print_r($valuex->employee_id.'-'.'<br/>'.$valuex->value.'-');
                        $income = $income + $valuex->value;
                        //print_r($income."<br>");
                    }

                    if ($valuex->component_type_id == 1 && $valuex->is_annualized == 0) {
                        //print_r($valuex->employee_id.'-'.'<br/>'.$valuex->value.'-');
                        $ireguler_income = $ireguler_income + $valuex->value;
                        //print_r($income."<br>");
                    }

                    if ($valuex->component_id == 2 OR $valuex->component_id == 8) {
                       //print_r($valuex->component_id.'-'.'<br/>'.$valuex->value.'-');
                        $jk_jkk = $jk_jkk + $valuex->value;
                        //print_r($income."<br>");
                    }

                    if ($valuex->component_type_id == 2 && $valuex->component_id != 55 && $valuex->tax_component_column != 0) {
                        //print_r($valuex->component_id.'-'.'<br/>'.$valuex->value.'-');
                        $deduction = $deduction + $valuex->value;
                        //print_r($income."<br>");
                    }

                    if ($valuex->component_id == 55 AND $i < (int)$curr_month) {
                        $income_tax = $income_tax + $valuex->value;
                    }
                }

                //total income
                $total_income = $total_income + $income;
                //total ireguler income
                $total_ireguler_income = $total_ireguler_income + $ireguler_income;
                //total jamsostek jk_jkk
                $total_jk_jkk = $total_jk_jkk + $jk_jkk;
            }
            //total regular income
            $total_regular_income = $total_income + $total_jk_jkk;
            //print_mz($total_regular_income);

            //biaya jabatan
            if ((($total_regular_income/$income_periode)*$bj_persen) < $bj_max) {
                $total_biaya_jabatan = ($total_regular_income/$income_periode) * $bj_persen * $income_periode;
            }else{
                $total_biaya_jabatan = $bj_max * $income_periode;
            }
            //print_mz(round($total_biaya_jabatan));
            //total deduction
            $total_deduction = $deduction + round($total_biaya_jabatan);
            //print_mz($total_biaya_jabatan);
            $curr_monthly_income_id = getValue('id', 'payroll_monthly_income', array('employee_id'=>'where/'.$emp_id, 'payroll_period_id'=>'where/'.$period_id));//lastq();print_mz($curr_monthly_income_id);
           //print_mz($income_periode." - ".$total_regular_income." - ".$total_deduction." - ".$income_tax);
            $pph_value = $this->get_pph($emp_id,$income_periode,$bj_max,$bj_persen,$total_biaya_jabatan,$total_regular_income,$total_deduction,$total_ireguler_income,$income_tax);
            //print_mz($pph_value);
            //insert/update pph
            $pph_component_id = 55;
            $pph_num_rows = GetAllSelect('payroll_monthly_income_component','payroll_component_id', array('payroll_monthly_income_id' => 'where/'.$curr_monthly_income_id, 'payroll_component_id'=>'where/'.$pph_component_id))->num_rows();
            $data = array('payroll_monthly_income_id' => $curr_monthly_income_id,
                          'payroll_component_id' => $pph_component_id,
                          'value' => $pph_value,
             );
            if($pph_num_rows>0) {
                $this->db->where('payroll_monthly_income_id', $curr_monthly_income_id)->where('payroll_component_id', $pph_component_id)->update('payroll_monthly_income_component', $data);
            }else {
                $this->db->insert('payroll_monthly_income_component', $data);
            }
        }

        echo json_encode(array("result" => TRUE));
    }

    function get_pph($emp_id,$income_periode,$bj_max,$bj_persen,$total_biaya_jabatan,$total_regular_income,$total_deduction,$total_ireguler_income,$income_tax) {
        $filter = array('employee_id'=>'where/'.$emp_id);
        //Nilai PTKP Tiap Karyawan
        $emp_ptkp_id = getValue('payroll_ptkp_id', 'payroll_master', $filter);
        $emp_ptkp = getValue('value', 'payroll_ptkp', array('id'=>'where/'.$emp_ptkp_id));
        $ptkp = $emp_ptkp;

        $income_netto = $total_regular_income - $total_deduction;//echo $income_netto.'-';
        //print_mz($income_netto);
        $income_netto_year = ($income_netto * 12) / $income_periode;
        //print_mz($income_netto_year);
        //pendapatan sebelum kena pajak
        $pskp = $income_netto_year - $ptkp;//print_r('pkp = '.$pkp.'-');

        $pph_x = $this->get_hitung_pphx($pskp,$total_ireguler_income);
        //print_mz($pph_x);
        $pph_y = $this->get_hitung_pphy($pskp,$income_periode,$total_ireguler_income,$total_biaya_jabatan,$bj_max,$bj_persen);
        //print_mz($pph_y);
        //pph iregeguler
        $pph_ireg = $pph_x - $pph_y;
        //print_mz($pph_ireg);
        $pph_ireg = round($pph_ireg); 
        //pph reguler
        $pph_reg = ($pph_y * $income_periode) / 12;
        $pph_reg = round($pph_reg);
        //print_mz($pph_reg);
        //pph disetahunkan
        $pph_disetahunkan = $pph_ireg + $pph_reg;
        //pph yang sudah dibayar
        $pph_dibayar = $income_tax;
        //pph bulan berjalan
        $pph_value = $pph_disetahunkan - $pph_dibayar;
        //print_mz($pph_value);

        return $pph_value;
    }

    //fungsi hitung pph termasuk ireguler income
    function get_hitung_pphx($pskp,$total_ireguler_income) {
        //pengurangan pendapatan ireguler
        /*if (($total_ireguler_income + $pskp) >= $bj_max * $income_periode) {
            $sisa_biaya_jabatan = $bj_max * $income_periode - $total_biaya_jabatan;
        }else{
            $sisa_biaya_jabatan = $total_ireguler_income * $bj_persen;
        }*/
        //print_mz($sisa_biaya_jabatan);
        //pendapatan kena pajak
        $pkp = $pskp + $total_ireguler_income;// - $sisa_biaya_jabatan;
        $pkp_pembulatan = 1000 * floor($pkp/1000);
        //print_mz($pkp_pembulatan);

        // Pajak Progressif
        $pph_tahun = $this->hitung_pajak_progressif($pkp_pembulatan);
        //print_mz($pph_tahun);
        return $pph_tahun;
    }
    //fungsi hitung pph diluar ireguler income
    function get_hitung_pphy($pskp,$income_periode,$total_ireguler_income,$total_biaya_jabatan,$bj_max,$bj_persen) {
        //pengurangan pendapatan ireguler
        if (($total_ireguler_income + $pskp) >= $bj_max * $income_periode) {
            $sisa_biaya_jabatan = $bj_max * $income_periode - $total_biaya_jabatan;
        }else{
            $sisa_biaya_jabatan = $total_ireguler_income * $bj_persen;
        }
        //print_mz($sisa_biaya_jabatan);
        //pendapatan kena pajak
        $pkp = $pskp;// - $sisa_biaya_jabatan;
        $pkp_pembulatan = 1000 * floor($pkp/1000);
        //print_mz($pkp_pembulatan);

        // Pajak Progressif
        $pph_tahun = $this->hitung_pajak_progressif($pkp_pembulatan);
        //print_mz($pph_tahun);
        return $pph_tahun;
    }
    //e.o. deni_dev

    //FUNGSI BUAT NGITUNG PPH

    public function process_() {
        $i = 0;
        //$employee_id = "113";
        $period_id = $this->input->post('period_id');
        $status = $this->input->post('status');
        $data = array('status' => $status);
        $this->db->where('id', $period_id)->update('payroll_period', $data);
        $this->cek_master_component();
        $this->update_monthly($period_id);//lastq();
        $query = GetAllSelect('payroll_monthly_income','employee_id', array('payroll_period_id' => 'where/'.$period_id))->result();//lastq();
        //print_mz($query);
        //Biaya Jabatan
        
        $bj_persen = getValue('value', 'payroll_biaya_jabatan', array('id'=>'where/1'))/100;
        $bj_max = getValue('max', 'payroll_biaya_jabatan', array('id'=>'where/1'));
       foreach ($query as $value) {
            $emp_id = $value->employee_id;
            //$emp_id = 133;
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

                if ($valuex->component_type_id == 2 && $valuex->component_id != 55 && $valuex->component_tax_id != 0) {
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
    public function update_monthly($employee_id,$period_id)
    {
        //$employee_id = GetAllSelect('payroll_master', 'employee_id', array())->result();
        //foreach($employee_id as $e):
            $num_rows = getAll('payroll_monthly_income', array('employee_id'=>'where/'.$employee_id, 'payroll_period_id'=>'where/'.$period_id))->num_rows();
            $monthly_income_id = getValue('id', 'payroll_monthly_income', array('employee_id'=>'where/'.$employee_id, 'payroll_period_id'=>'where/'.$period_id));
            $old_group = getValue('payroll_group_id', 'payroll_monthly_income', array('employee_id'=>'where/'.$employee_id, 'payroll_period_id'=>'where/'.$period_id));
            $group_id = getValue('payroll_group_id', 'payroll_master', array('employee_id'=>'where/'.$employee_id));//print_mz($group_id);
            $payroll_ptkp_id = getValue('payroll_ptkp_id', 'payroll_master', array('employee_id'=>'where/'.$employee_id));
            $payroll_currency_id = getValue('payroll_currency_id', 'payroll_master', array('employee_id'=>'where/'.$employee_id));
            $payroll_tax_method_id = getValue('payroll_tax_method_id', 'payroll_master', array('employee_id'=>'where/'.$employee_id));
            $is_expatriate = getValue('is_expatriate', 'payroll_master', array('employee_id'=>'where/'.$employee_id));
            //print_mz($group_id);
            //if($old_group != $group_id)$this->db->where('payroll_monthly_income_id', $monthly_income_id)->update('payroll_monthly_income_component', array('is_deleted' => 1));
            $data = array(
                    'employee_id' => $employee_id,
                    'payroll_group_id' => $group_id,
                    'payroll_period_id' => $period_id,
                    'payroll_ptkp_id'=>$payroll_ptkp_id,
                    'payroll_currency_id'=>$payroll_currency_id,
                    'payroll_tax_method_id'=>$payroll_tax_method_id,
                    'is_expatriate'=>$is_expatriate
                );
            if($num_rows>0)$this->db->where('employee_id', $employee_id)->where('payroll_period_id', $period_id)->update('payroll_monthly_income', $data);
                else $this->db->insert('payroll_monthly_income', $data);//lastq();
                //print_r($this->db->last_query());
            $monthly_income_id = ($num_rows>0) ? $monthly_income_id : $this->db->insert_id();
            $monthly_group = getAll('payroll_monthly_income', array('employee_id'=>'where/'.$employee_id, 'payroll_group_id'=>'where/'.$old_group))->num_rows();//print_mz($monthly_group);
            $master_id = getValue('id', 'payroll_master', array('employee_id'=>'where/'.$employee_id));
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
        //endforeach;
        return true;
    }

    //FUNGSI BUAT UPDATE NILAI GAJI BULANAN NGAMBIL DARI MASTER
    public function update_monthly_($period_id)
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
        //$employee_id = 644;
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


    function cek_master_component($session_id = 2015)
    {
        $master = GetAllSelect('payroll_master', 'id')->result();
        foreach($master as $m){
            $master_id= $m->id;
            //$filter =  array('id'=>'where/'.$m->id, 'session_id'=>'where/'.sessNow());
            $filter =  array('id'=>'where/'.$m->id);
            $group_id = getValue('payroll_group_id', 'payroll_master',$filter);//print_mz($group_id);
            $group_id = getValue('id', 'payroll_group', array('job_class_id'=>'where/'.$group_id));//lastq()
            $cek_group_component = GetAllSelect('payroll_group_component', 'payroll_component_id', array('payroll_group_id'=>'where/'.$group_id));//print_mz($cek_group_component->result());
            foreach ($cek_group_component->result() as $r) {
                $component_num_rows = GetAllSelect('payroll_master_component', 'payroll_component_id', array('payroll_master_id'=>'where/'.$master_id, 'payroll_component_id'=>'where/'.$r->payroll_component_id))->num_rows();//print_r("num_rows-".$component_num_rows);
                if($component_num_rows<1):
                    $data = array('payroll_master_id' => $master_id,
                                  'payroll_component_id'=> $r->payroll_component_id,
                                  'value' => 0
                        );
                    $this->db->insert('payroll_master_component', $data);
                endif;
            }

            $this->get_formula($m->id, $session_id);
        }
    }

    function get_formula($payroll_master_id, $session_id){
        $this->load->model('payroll_master_model','master');
        $today = date('Y-m-d');
        $data2 = $this->master->get_master_component($payroll_master_id)->result();
        //lastq();
        //print_mz($data2);
        $emp_id = getValue('employee_id', 'payroll_master', array('id'=>'where/'.$payroll_master_id));
        $session_before = $session_id - 1;
        $master_id_before = getValue('id', 'payroll_master', array('employee_id'=>'where/'.$emp_id, 'session_id'=>'where/'.$session_before));//lastq();
        $sal_session_before = getValue('value', 'payroll_master_component', array('payroll_master_id'=>'where/'.$master_id_before, 'payroll_component_id'=>'where/60'));
        $sal_session_now = getValue('value', 'payroll_master_component', array('payroll_master_id'=>'where/'.$payroll_master_id, 'payroll_component_id'=>'where/60'));
        $cola = getValue('value', 'payroll_cola', array('session_id'=>'where/'.$session_id));
        if($sal_session_now - $sal_session_before > $cola){
            $new_sal = $sal_session_now;
        }else{
            $new_sal = $sal_session_before + $cola;
        }
        $this->db->where('payroll_master_id', $payroll_master_id)->where('payroll_component_id', 60)->update('payroll_master_component', array('value'=>$new_sal));//lastq();
        //print_r($sal_session_before);
        //print_r($sal_session_now);
        //print_mz($new_sal);
        $m = 0;
        foreach ($data2 as $value) {
            //$component_id = 66;
            $filter = array('payroll_component_id'=>'where/'.$value->component_id, 'session_id'=>'where/'.$session_id);
            $component_session_id = getValue('id', 'payroll_component_session', $filter);//lastq();
            $com_val = $this->db->select('*')->where('payroll_component_session_id', $component_session_id)->get('payroll_component_value')->result();
            $formula = '';
            //print_ag($com_val);
            if(!empty($com_val)){
                //print_ag($com_val);
                foreach ($com_val as $c) {
                    $from = date('Y-m-d', strtotime($c->from));
                    $to = date('Y-m-d', strtotime($c->to));
                    //print_ag("$today lebih besar dari $from , $today kurang dari $to");
                    //print_mz($from);
                    //if($today > $from && $today < $to)die('s');
                    if($today >= $from && $today <= $to){
                        $formula = $c->formula;//echo $formula;
                        $is_condition = $c->is_condition;
                        $min = $c->min;
                        $max = $c->max;
                    }
                }
            }
                //print_ag($is_condition);
            //die();
            //echo $formula;
            $t = $formula;//print_ag($formula);//print_r("idnya $value->component formulanya $value->formula <br/>");
            //$t = 'IF ( BWGS * HOUS ) > 2 * 5000000 ; 2 * 5000000 * 4 / 100';
            //$t = getValue('formula', 'payroll_component_value', array('id'=>'where/28'));//print_mz($t);
            $tx = explode(' ', $t);$r='';//print_mz($tx);
            if($t != null && $value->component_id!=60):
                if(!in_array('IF', $tx)){
                    for($i=0;$i<sizeof($tx);$i++)://print_mz($tx);
                        if(preg_match("/[a-z]/i", $tx[$i])){
                            
                            $g = getValue('id', 'payroll_component', array('code'=>'where/'.$tx[$i]));
                            $detail = $this->payroll->get_employee_detail($emp_id);
                            $det = $detail->row();
                            $employee_job_id = getValue('job_id', 'hris_employee_job', array('employee_id'=>'where/'.$emp_id));
                            $job_value_id = getValue('job_value_id', 'hris_jobs', array('job_id'=>'where/'.$employee_job_id));
                            $filter = array(
                                'session_id' => 'where/'.$session_id,
                                'job_value_id' => 'where/'.$job_value_id,
                                'job_class_id' => 'where/'.$det->job_class_id
                                );
                            $job_value_matrix = GetAll('payroll_job_value_matrix',$filter);//lastq();
                            $job_value_matrix_num = GetAll('payroll_job_value_matrix',$filter)->num_rows();//lastq();

                            if($tx[$i] == 'JVM'){
                                $g = $jvm = ($job_value_matrix_num>0)?$job_value_matrix->row()->value:0;
                                //print_r('jvm='.$g.'-');
                            }elseif($tx[$i] == 'VAR'){
                                $cm_param = GetAll('payroll_compensation_mix', array('session_id' => 'where/'.$session_id, 'job_class_id' => 'where/'.$det->job_class_id));
                                $cm_param_num = GetAll('payroll_compensation_mix', array('session_id' => 'where/'.$session_id, 'job_class_id' => 'where/'.$det->job_class_id))->num_rows();
                                //lastq();
                                $row = $cm_param->row();
                                $g = $var = ($cm_param_num>0)?$row->var/100:0/100;

                                //print_mz('var='.$var.'-');
                            }elseif($tx[$i] == 'CONV'){
                                $g = getValue('value', 'payroll_exchange_rate', array('session_id'=>'where/'.$session_id));
                            }else{
                                $g = getValue('value', 'payroll_master_component', array('payroll_master_id'=>'where/'.$payroll_master_id, 'payroll_component_id'=>'where/'.$g));
                                //print_r($g.'-');
                            }
                            //print_r($value->component.'='.$g.'<br/>');
                            $tx[$i] = $g;
                        }

                        if (strpos($tx[$i], '%') !== false) {
                             $tx[$i] =substr_replace($tx[$i], '/100', -1);
                        }else{false;}
                        $r .= $tx[$i];
                     endfor;
                }else{ 
                        $com = explode(PHP_EOL, $t);
                        //print_ag($com);   
                        for($j=0;$j<sizeof($com);$j++){  
                            $ntx = '';
                            $bwgs = getValue('value', 'payroll_master_component', array('payroll_master_id'=>'where/'.$payroll_master_id, 'payroll_component_id'=>'where/60'));
                            $hous = getValue('value', 'payroll_master_component', array('payroll_master_id'=>'where/'.$payroll_master_id, 'payroll_component_id'=>'where/66'));//print_mz($xj[$i]);
                            $bwgshous = $bwgs+$hous;//print_mz($bwgshous);
                            $xj = explode(' ', $com[$j]);//print_ag($xj);
                            $n = '';
                            for($i=7;$i<sizeof($xj);$i++){
                                if(preg_match("/[a-z]/i", $xj[$i])){
                                        //echo $j.'-'.$xj[$i].'<br/>';
                                    switch ($xj[$i]) {
                                        case 'TK0':
                                            $xj[$i]=getValue('value', 'payroll_ptkp', array('title'=>'where/'.'TK0'));
                                            break;
                                        case 'K0':
                                            $xj[$i]=getValue('value', 'payroll_ptkp', array('title'=>'where/'.'K0'));
                                            break;
                                        case 'K1':
                                            $xj[$i]=getValue('value', 'payroll_ptkp', array('title'=>'where/'.'K1'));//print_mz($xj[$i]);
                                            break;
                                        case 'K2':
                                            $xj[$i]=getValue('value', 'payroll_ptkp', array('title'=>'where/'.'K2'));
                                            break;
                                        case 'K3':
                                            $xj[$i]=getValue('value', 'payroll_ptkp', array('title'=>'where/'.'K3'));
                                            break;
                                        case 'UMK':
                                            //$xj[$i]=getValue('value', 'UMK', array('session'=>'where/'.$session_id));
                                            $xj[$i]=3000000;
                                            break;
                                        
                                        default:

                                            //print_ag($xj[$i]);
                                            $code = getValue('id', 'payroll_component', array('code'=>'where/'.$xj[$i]));
                                            $xj[$i] = getValue('value', 'payroll_master_component', array('payroll_master_id'=>'where/'.$payroll_master_id, 'payroll_component_id'=>'where/'.$code));
                                            //print_ag($xj[$i]);
                                            break;
                                    }

                                    //print_ag('awa'.$j.'-'.$xj[$i]);
                                }
                                //print_ag($value->component);
                                //print_ag($xj[$i]);
                                if ($xj[$i] == '%'){
                                     $xj[$i] ='/100';
                                     //print_ag('ke_replace '.$xj[$i]);
                                }

                                $ntx .= $xj[$i];
                            }

                                //print_ag($value->code);
                                //print_ag($xj);

                            if($xj[6] == '>') {
                                $f = current(explode(";", $ntx));//print_mz($ntx);
                                $f= $this->evalmath($f);
                                $f = @eval("return " . $f . ";" );
                                //print_mz($f);
                                $l = substr($ntx, strpos($ntx, ";") + 1);//print_ag($j.'-'.$l);
                                $l = $this->evalmath($l);
                                $l = @eval("return " . $l . ";" );//print_ag('>'.$bwgshous.$xj[6].$f.'='.$r);
                                //print_mz($bwgshous.' > '.$f);
                                if($bwgshous > $f)$r = $l;
                            }elseif($xj[6] == '<') {
                                $f = current(explode(";", $ntx));//print_mz($ntx);
                                $f= $this->evalmath($f);
                                $f = @eval("return " . $f . ";" );
                                //print_mz($f);
                                $l = substr($ntx, strpos($ntx, ";") + 1);//print_ag($j.'-'.$l);
                                $l = $this->evalmath($l);
                                $l = @eval("return " . $l . ";" );//print_ag('>'.$bwgshous.$xj[6].$f.'='.$r);
                                //print_mz($bwgshous.' > '.$f);
                                if($bwgshous < $f)$r=$l;
                            }elseif($xj[6] == '<=') {
                                $f = current(explode(";", $ntx));//print_mz($ntx);
                                $f= $this->evalmath($f);
                                $f = @eval("return " . $f . ";" );
                                //print_mz($f);
                                $l = substr($ntx, strpos($ntx, ";") + 1);//print_ag($j.'-'.$l);
                                $l = $this->evalmath($l);
                                $l = @eval("return " . $l . ";" );//print_ag('>'.$bwgshous.$xj[6].$f.'='.$r);
                                //print_mz($bwgshous.' > '.$f);
                                if($bwgshous <= $f)$r=$l;
                            }elseif($xj[6] == '>=') {
                                $f = current(explode(";", $ntx));//print_mz($ntx);
                                $f= $this->evalmath($f);
                                $f = @eval("return " . $f . ";" );
                                //print_mz($f);
                                $l = substr($ntx, strpos($ntx, ";") + 1);//print_ag($j.'-'.$l);
                                $l = $this->evalmath($l);
                                $l = @eval("return " . $l . ";" );//print_ag('>'.$bwgshous.$xj[6].$f.'='.$r);
                                //print_mz($bwgshous.' > '.$f);
                                if($bwgshous >= $f)$r=$l;
                            }
                            //print_ag($bwgshous.$xj[6].$f.'='.$r);
                        }
                            //print_ag($bwgshous.$xj[6].$f.$r);
                            //print_ag($bwgshous.$xj[6].$f.$r);
                            //print_mz($com);
                            //print_ag($ntx);

                //print_ag($f);
                }
                
                $f= $this->evalmath($r);
                $tz =@eval("return " . $f . ";" );//print_ag($tz);
                //$is_condition = getValue('is_condition', 'payroll_component_value', array('payroll_component_id'=>'where/'.$value->component_id));
                //$is_condition = 0;
                if($is_condition == 1){
                    //$min = 1000;
                    //$max = 70000000;
                    //$min = getValue('min', 'payroll_component_value', array('payroll_component_id'=>'where/'.$value->component_id));
                    //$max = getValue('max', 'payroll_component_value', array('payroll_component_id'=>'where/'.$value->component_id));

                    if($tz > $max):
                        $tz = $max;
                    elseif($tz < $min):
                        $tz = $min;
                    else:
                        $tz= $tz;
                    endif;
                }
                $this->db->where('id', $value->id)->update('payroll_master_component', array('value'=>$tz));
                //echo '<pre>';print_r($this->db->last_query());echo "</pre><br/>";
            endif;
        }
        //die();
       return true;
    }

    function evalmath($equation)
    {
        $result = 0;
        // sanitize imput
        $equation = preg_replace("/[^a-z0-9+\-.*\/()%]/","",$equation);

        // convert alphabet to $variabel 
        $equation = preg_replace("/([a-z])+/i", "\$$0", $equation); 


        // convert percentages to decimal
        $equation = preg_replace("/([+-])([0-9]{1})(%)/","*(1\$1.0\$2)",$equation);
        $equation = preg_replace("/([+-])([0-9]+)(%)/","*(1\$1.\$2)",$equation);
        $equation = preg_replace("/([0-9]{1})(%)/",".0\$1",$equation);
        $equation = preg_replace("/([0-9]+)(%)/",".\$1",$equation);
        /* 
        if ( $equation != "" )
        {
        $result = @eval("return " . $equation . ";" );

        }
        */
        /* 
        if ($result == null)
        {
        throw new Exception("Unable to calculate equation");
        }

        return $result;
        */
        return $equation;

    }
}
?>