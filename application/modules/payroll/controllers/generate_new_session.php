<?php 
    
    if ( ! defined('BASEPATH')) exit('No direct script access allowed');

    class Generate_new_session extends MX_Controller
    {
        var $new_session = "2016";
    	var $old_session = "2015";
        function index()
        {
            $sess_now = sessNow()-1;
            $sess_now = sessNow();
            //TABLE COMPONENT_VALUE;
            
            $master = getAll('payroll_master', array('session_id'=>'where/'.date('year')-1));
            foreach ($master->result() as $m)
            {
                $masterz = array('employee_id' => $m->employee_id,                            'session_id' => $m->session_id);
                $m_num_rows = getAll('payroll_master', array('session_id'=>'where/'.$sess_now, 'employee_id'=>'where/'.$m->employee_id))->num_rows();
                
                if($m_num_rows>0)
                {
                    $this->db->where('session_id', $sess_now)->where('employee_id', $m->employee_id)->update('payroll_master', $masterz);
                    $m_id = getValue('id', 'payroll_master', array('session_id'=>'where/'.$sess_now, 'employee_id'=>'where/'.$m->employee_id));
                }
                else
                {
                    $this->db->insert('payroll_master', $masterz);
                    $m_id=$this->db->insert_id();
                }

                $m_comp = getAll('payroll_master_component', array('payroll_master_id'=>'where/'.$m->id));
                foreach($m_comp->result() as $m_c):
                $m_compz = array('payroll_master_id' => $m_id,                         
                				  'payroll_component_id' =>$m_c->payroll_component_id,                         
                				  'value'=>$m_c->value
                				);
                $m_comp_num_rows = getAll('payroll_master_component', array('payroll_master_id'=>'where/'.$m_id))->num_rows();
                
                if($m_comp_num_rows>0)
                {
                    $this->db->where('payroll_master_id', $m_id)->update('payroll_master_component', $m_compz);
                }
                else
                {
                    $this->db->insert('payroll_master_component', $m_compz);
                }

                endforeach;
            }

            echo json_encode(array("result" => TRUE));
        }

        function update_component()
        {
        	$comp = GetAllSelect('payroll_component', 'id')->result();
        	foreach ($comp as $c) {
        		$data = array('session_id' => $this->new_session,
        					  'payroll_component_id' => $c->id
        			);
        		$filter = array('session_id'=>'where/'.$this->new_session, 'payroll_component_id'=>'where/'.$c->id);
        		$num_rows = GetAllSelect('payroll_component_session', 'id', $filter)->num_rows();
        		if($num_rows>0)$this->db->where('session_id', $this->new_session)->where('payroll_component_id', $c->id)->update('payroll_component_session', $data);
        		else $this->db->insert('payroll_component_session', $data);

                $filter2 = array('session_id'=>'where/'.$this->old_session, 'payroll_component_id'=>'where/'.$c->id);
                $comp_sess_before = getValue('id', 'payroll_component_session', $filter2);
                print_ag($comp_sess_before);
                $comp_value = getAll('payroll_component_value', array('payroll_component_session_id'=>'where/'.$comp_sess_before))->row();
                print_ag($comp_value);

                $comp_sess_new = getValue('id', 'payroll_component_session', $filter);

                $num_rows_new = GetAllSelect('payroll_component_value', 'id', array('payroll_component_session_id'=>'where/'.$comp_sess_new))->num_rows();
                if(!empty($comp_value)){
                        $data2 = array(
                                'payroll_component_session_id'=>$comp_sess_new,
                                'from'=>$comp_value->from,
                                'to'=>$comp_value->to,
                                'formula'=>$comp_value->formula,
                                'is_condition'=>$comp_value->is_condition,
                                'min'=>$comp_value->min,
                                'max'=>$comp_value->max,
                                'created_by'=>sessId(),
                                'created_on'=>dateNow()
                            );
                    if($num_rows_new>0)$this->db->where('payroll_component_session_id', $comp_sess_new)->update('payroll_component_value', $data2);
                    else $this->db->insert('payroll_component_value', $data2);
                }
                print_ag($this->db->last_query());
        	}
        }

    }