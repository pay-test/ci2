<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Payroll_component extends MX_Controller {
	
	var $title = "payroll";
    var $page_title = "Component";
	var $filename = "payroll_component";
    var $session_id = '2015';
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

    public function add()
    {
        $this->data['title'] = ucfirst($this->title);
        $this->data['page_title'] = $this->page_title;
        $this->data['component_type'] = $this->payroll->get_component_type();
        $this->data['tax_component'] = $this->payroll->get_tax_component();
        $this->data['component_job_value'] = getAll('payroll_component_job_value');
        $this->data['job_value'] = getAll('payroll_job_value');
        $this->data['session_id'] = getAll('hris_global_sess');
        permission();
        $this->_render_page('payroll_component/add', $this->data);
    }

    public function doAdd()
    {
        permission();
        $session_id = $this->input->post('session_id');
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
        $data2 = array('session_id' => $session_id,
                       'payroll_component_id' => $insert,
         );

        $this->db->insert('payroll_component_session', $data2);
        echo json_encode(array("status" => TRUE, "id"=>$insert, "session_id"=>$session_id));
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
        $component_id = getValue('payroll_component_id', 'payroll_component_session', array('id'=>'where/'.$this->input->post('component_session_id'.$id)));
        $this->update_master_component_value($component_id);//lastq();
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
                }elseif(in_array($view, array($this->filename.'/edit',
                                              $this->filename.'/add'
                    )))
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

    function update_master_component_value($component_id)
    {
        $today = date('Y-m-d');
        $master_id = GetAllSelect('payroll_master_component', 'payroll_component_id, payroll_master_id', array('payroll_component_id'=>'where/'.$component_id))->result();
        $filter = array('payroll_component_id'=>'where/'.$component_id, 'session_id'=>'where/'.$this->session_id);
        $component_session_id = getValue('id', 'payroll_component_session', $filter);//lastq();
        $com_val = $this->db->select('*')->where('payroll_component_session_id', $component_session_id)->get('payroll_component_value')->result();
        $formula = '';
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
        $tx = explode(' ', $formula);$r='';
        foreach($master_id as $value){
            if($formula != null && $component_id!=60):
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
                                $g = getValue('value', 'payroll_master_component', array('payroll_master_id'=>'where/'.$value->payroll_master_id, 'payroll_component_id'=>'where/'.$g));
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
                        $com = explode(PHP_EOL, $formula);
                        //print_ag($com);   
                        for($j=0;$j<sizeof($com);$j++){  
                            $ntx = '';
                            $bwgs = getValue('value', 'payroll_master_component', array('payroll_master_id'=>'where/'.$value->payroll_master_id, 'payroll_component_id'=>'where/60'));
                            $hous = getValue('value', 'payroll_master_component', array('payroll_master_id'=>'where/'.$value->payroll_master_id, 'payroll_component_id'=>'where/66'));//print_mz($xj[$i]);
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
                                            $xj[$i] = getValue('value', 'payroll_master_component', array('payroll_master_id'=>'where/'.$value->payroll_master_id, 'payroll_component_id'=>'where/'.$code));
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
                $this->db->where('payroll_master_id', $value->payroll_master_id)->where('payroll_component_id', $component_id)->update('payroll_master_component', array('value'=>$tz));
                //echo '<pre>';print_r($this->db->last_query());echo "</pre><br/>";
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
}
?>