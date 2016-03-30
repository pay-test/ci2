<?php 
    
    if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    class payroll_config_tax extends MX_Controller {
        var $title = "payroll";
        var $filename = "payroll_config_tax";
        public $data;
        
        function __construct()    {
            parent::__construct();
            $this->load->model('payroll_config_tax_model','payroll');
        }

        
        function index()    {
            $this->data['title'] = $this->title;
            $filter_org = array('org_class_id'=>'where/4', 'status_cd'=>'where/normal', 'org_nm='=>'order/asc');
            $this->data['session'] = getAll('hris_global_sess', array('id'=>'order/desc'));
            $this->data['org'] = getAll('hris_orgs', $filter_org);
            //print_mz($this->data['org']->num_rows());
            permission();
            $this->_render_page($this->filename, $this->data);
        }

        
        function edit_tax_rate(){
            $table = 'payroll_tax_exchange_rate';
            $session_id = $this->input->post('session_id');
            permission();
            $filter =  array('session_id'=>'where/'.$session_id);
            $num_rows = getAll($table, $filter)->num_rows();//lastq();
            $data = array('value' => str_replace(',', '', $this->input->post('value')),
                          'session_id'=> $session_id,
                );
            if($num_rows>0)$this->db->where('session_id', $session_id)->update($table, $data);
            else $this->db->insert($table, $data);
            //lastq();
            return true;
        }

        function edit_umk($sess_id, $city_id)
        {
            $filter = array('session_id'=>'where/'.$sess_id, 'umk_city_id'=>'where/'.$city_id);
            $umk_id = getValue('id', 'payroll_umk', $filter);
            $value = str_replace(',','',$this->input->post('value'));
            if(!empty($umk_id)){
                $this->db->where('id', $umk_id)->update('payroll_umk', array('value'=>$value));
            }else{
                $data = array(
                        'session_id' => $sess_id,
                        'umk_city_id'=>$city_id,
                        'value'=>$value
                    );
                $this->db->insert('payroll_umk', $data);
            }
            lastq();
        }

        //FOR JS FUNCTION
        
        function get_tax_component()    {
            $sess_id = $this->input->post('id');
            $this->_render_page('config_tax/component', $this->data);
        }

        
        function get_ptkp()    {
            $sess_id = $this->input->post('id');
            $this->_render_page('config_tax/ptkp', $this->data);
        }

        
        function get_progressive()    {
            $sess_id = $this->input->post('id');
            $this->_render_page('config_tax/progressive', $this->data);
        }

        
        function get_method()    {
            $sess_id = $this->input->post('id');
            $this->_render_page('config_tax/method', $this->data);
        }

        
        function get_umk($sess_id)    {
            /*
            $filter = array('session_id'=>'where/'.$sess_id                        );
            $v = getAll('payroll_umk', $filter);
            
            if ($v->num_rows() > 0) {
                $v = $v->row();
                echo json_encode(array('value'=>$v->value, 'id'=>$v->id));
            } else {
                echo json_encode(array('value'=> 0, 'id'=> 0));
            }
            */
            //print_mz($v);
            $this->data['sess_id'] = $sess_id;
            //$this->data['umk_value'] = $this->payroll->get_umk($sess_id);print_mz($this->data['umk_value']->result());
            $this->data['city'] = GetAllSelect('payroll_umk_city', 'id, title');//print_mz($this->data['city']->result());
            $this->load->view('config_tax/umk', $this->data);
        }

        
        function get_tax_rate()    {
            $sess_id = $this->input->post('id');
            $filter = array('session_id'=>'where/'.$sess_id                        );
            $v = getAll('payroll_tax_exchange_rate', $filter);
            
            if ($v->num_rows() > 0) {
                $v = $v->row();
                echo json_encode(array('value'=>$v->value, 'id'=>$v->id));
            } else {
                echo json_encode(array('value'=> 0, 'id'=> 0));
            }

            //print_mz($v);
        }

        
        function _render_page($view, $data=null, $render=false)    {
            // $this->viewdata = (empty($data)) ? $this->data: $data;
            // $view_html = $this->load->view($view, $this->viewdata, $render);
            // if (!$render) return $view_html;
            $data = (empty($data)) ? $this->data :
            $data;
            
            if ( ! $render)        {
                $this->load->library('template');
                
                if(in_array($view, array($this->filename)))                {
                    $this->template->set_layout('default');
                    $this->template->add_css('assets/plugins/bootstrap-select2/select2.css');
                    $this->template->add_js('assets/plugins/data-tables/jquery.dataTables.min.js');
                    $this->template->add_css('assets/plugins/jquery-datatable/css/jquery.dataTables.css');
                    $this->template->add_js('assets/plugins/data-tables/jquery.dataTables.min.js');
                    $this->template->add_js('assets/plugins/jquery-datatable/extra/js/dataTables.tableTools.min.js');
                    $this->template->add_js('assets/plugins/datatables-responsive/js/datatables.responsive.js');
                    $this->template->add_js('assets/plugins/data-tables/jquery.dataTables.min.js');
                    $this->template->add_js('assets/plugins/jquery-maskmoney/jquery.maskMoney.js');
                    $this->template->add_js('modules/js/'.$this->title.'/'.$this->filename.'.js');
                    $this->template->add_js('modules/js/payroll/payroll_tax_method.js');
                    //$this->template->add_js('modules/js/payroll/payroll_tax_progressive.js');
                    //$this->template->add_js('modules/js/payroll/payroll_tax_component.js');
                    //$this->template->add_js('modules/js/payroll/payroll_ptkp.js');
                }

                
                if ( ! empty($data['username']))            {
                    $this->template->set_title($data['username']);
                }

                $this->template->load_view($view, $data);
            } else        {
                return $this->load->view($view, $data, TRUE);
            }

        }

    }

    ?>