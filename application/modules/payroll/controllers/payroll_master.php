<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Payroll_master extends MX_Controller {
	
	var $title = "payroll";
    var $page_title = "Payroll Master";
	var $filename = "payroll_master";
	public $data;
	function __construct()
	{
		parent::__construct();
        $this->load->model('payroll_master_model','payroll');
        $this->load->model('payroll_setup_model','setup');
        $this->load->model('all_model','all_model');
	}
	
	function index()
	{
        $this->data['title'] = ucfirst($this->title);
		$this->data['page_title'] = $this->page_title;

        permission();

         $year_now = date('Y');
        $this->data['period'] = $this->setup->render_periode($year_now);
        $this->data['session'] = getAll('hris_global_sess', array('id'=>'order/desc'));
        $this->data['ptkp'] = options_row('payroll', 'get_ptkp','id','title', '-- Choose Tax Status --');
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
            $row[] = $payroll->job_abbr;
            $row[] = $payroll->org_nm;

            //add html for action
            $row[] = '<a class="btn btn-sm btn-primary" href="javascript:void(0);" title="Edit" onclick="edit_user('."'".$payroll->employee_id."'".')"><i class="fa fa-pencil"></i></a>';
        
            $data[] = $row;
        }

        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->payroll->count_all(),
                        "recordsFiltered" => $this->payroll->count_filtered(),
                        "data" => $data
                );
        //output to json format
        echo json_encode($output);
    }

    public function ajax_edit($id, $session_id)
    {
        $data = $this->payroll->get_by_id($id);//print_mz($data); 
        $payroll_master_id = getValue('id', 'payroll_master', array('employee_id'=>'where/'.$id, 'session_id'=>'where/'.$session_id));//print_mz($payroll_master_id);
        $this->cek_master_component($payroll_master_id, $session_id);
        $this->get_formula($payroll_master_id, $session_id);
        $ptkp = getValue('payroll_ptkp_id', 'payroll_master', array('id'=>'where/'.$payroll_master_id));
        $data2 = $this->payroll->get_master_component($payroll_master_id)->result_array();//print_mz($data2);
        //$data2 = $this->payroll->get_master_component_s($payroll_master_id, $session_id)->result_array();//print_mz($data2);
        echo json_encode(array('data1'=>$data, 'data2'=>$data2, 'ptkp'=>$ptkp));
    }

    function get_formula($payroll_master_id, $session_id){
        $data2 = $this->payroll->get_master_component($payroll_master_id)->result();//lastq();
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
            $t = $value->formula;//print_r("idnya $value->component formulanya $value->formula <br/>");
            //$t = 'IF ( BWGS * HOUS ) > 2 * 5000000 ; 2 * 5000000 * 4 / 100';
            //$t = getValue('formula', 'payroll_component_value', array('id'=>'where/28'));//print_mz($t);
            $tx = explode(' ', $t);$r='';//print_mz($tx);
            if($t != null && $value->component_id!=60):
               //echo $value->formula;
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
                        //print_mz($com);   
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
                                            $xj[$i] = getValue('value', 'payroll_master_component', array('payroll_master_id'=>'where/'.$payroll_master_id, 'payroll_component_id'=>'where/'.$code));//print_ag($xj[$i]);
                                            break;
                                    }

                                    //print_ag('awa'.$j.'-'.$xj[$i]);
                                }
                                //print_mz('ds');

                                if (strpos($tx[$i], '%') !== false) {
                                     $xj[$i] =substr_replace($xj[$i], '/100', -1);
                                }

                                $ntx .= $xj[$i];
                            }
                                //print_ag($j.'-'.$ntx);

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
                            //print_mz($ntx);

                //print_ag($f);
                }
                
                $f= $this->evalmath($r);
                $tz =@eval("return " . $f . ";" );//print_ag($tz);
                $is_condition = getValue('is_condition', 'payroll_component_value', array('payroll_component_id'=>'where/'.$value->component_id));
                if($is_condition == 1){
                    $min = getValue('min', 'payroll_component_value', array('payroll_component_id'=>'where/'.$value->component_id));
                    $max = getValue('max', 'payroll_component_value', array('payroll_component_id'=>'where/'.$value->component_id));

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
       return true;
    }

    function cek_master_component($master_id, $session_id)
    {
        $group_id = getValue('payroll_group_id', 'payroll_master', array('id'=>'where/'.$master_id, 'session_id'=>'where/'.$session_id));//print_mz($group_id);
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

    public function ajax_update()
    {
        //print_mz($this->input->post('payroll_ptkp_id'));
        //$this->_validate();
        $session_id= $this->input->post('session_id');
        $employee_id = $this->input->post('employee_id');
        $group_id = $this->input->post('group_id');
        $group_id = getValue('job_class_id', 'payroll_group', array('id'=>'where/'.$group_id));
        $filter_master = array('employee_id'=>'where/'.$employee_id, 'session_id'=>'where/'.$session_id);

        $num_rows = getAll('payroll_master', $filter_master)->num_rows();
        //lastq();
        $payroll_master_id = getValue('id', 'payroll_master', $filter_master);
        $old_group = getValue('payroll_group_id', 'payroll_master', $filter_master);
        if($old_group != $group_id) {
            $this->db->where('payroll_master_id', $payroll_master_id)->update('payroll_master_component', array('is_deleted' => 1));
        }
            
        $data = array(
                'session_id' => $session_id,
                'employee_id' => $employee_id,
                'payroll_group_id' => $group_id,
                'payroll_ptkp_id' => $this->input->post('payroll_ptkp_id'),
            );

        if($num_rows>0) {
            $this->db->where('employee_id', $employee_id)->where('session_id', $this->input->post('session_id'))->update('payroll_master', $data);   
        }else{
            $this->db->insert('payroll_master', $data);
        }
        //print_r($this->db->last_query());
        $payroll_master_id = ($num_rows>0) ? $payroll_master_id : $this->db->insert_id();

        $monthly_group = getAll('payroll_master', array('employee_id'=>'where/'.$employee_id, 'payroll_group_id'=>'where/'.$old_group))->num_rows();//print_mz($monthly_group);
        //$component_num_rows = getAll('payroll_master_component', array('payroll_master_id'=>'where/'.$payroll_master_id))->num_rows;
        $n = array(
            ',',
            '.'
            );
        $component = array('master_component_id' => $this->input->post('master_component_id'),
                           'component_id' => $this->input->post('component_id'),
                           'value' => str_replace(',', '', $this->input->post('value')),
                    );
        //print_mz($component);
        
        for($i=0;$i<sizeof($component['component_id']);$i++):
            $data2 = array(
                    'payroll_master_id'=>$payroll_master_id,
                    'payroll_component_id' =>$component['component_id'][$i],
                    'value' =>$component['value'][$i],
                );
            if($old_group == $group_id) {
                $this->db->where('id', $component['master_component_id'][$i])->update('payroll_master_component', $data2);   
            } else {
                 $this->db->insert('payroll_master_component', $data2);
            }
                //print_r($this->db->last_query());
            //lastq();
        endfor;
        echo json_encode(array("status" => $this->db->last_query()));
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
        $this->load->view('payroll_master/component_table', $this->data);
    }

    public function get_component_table_val()
    {
        $id = $this->input->post('id');

        $q = $this->data['component'] = $this->payroll->get_component($id)->result();//lastq();print_mz($q);
        $this->data['component_value'] = $this->input->post('value');
        $this->load->view('payroll_master/component_table_val', $this->data);
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
                     //$this->template->add_js('assets/plugins/jquery-maskmoney/jquery.maskMoney.js');
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