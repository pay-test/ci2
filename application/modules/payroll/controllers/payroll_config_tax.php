<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class payroll_config_tax extends MX_Controller {
    
    var $title = "payroll";
    var $filename = "payroll_config_tax";
    public $data;
    function __construct()
    {
        parent::__construct();
        $this->load->model('payroll_config_tax_model','payroll');
    }
    
    function index()
    {
        $this->data['title'] = $this->title;
        $filter_org = array('org_class_id'=>'where/4', 'status_cd'=>'where/normal', 'org_nm='=>'order/asc');
        $this->data['session'] = getAll('hris_global_sess', array('id'=>'order/desc'));
        $this->data['org'] = getAll('hris_orgs', $filter_org);//print_mz($this->data['org']->num_rows());
        
        permission();
        $this->_render_page($this->filename, $this->data);
    }


    function edit_tax_rate($type){
        $id = $this->input->post('id');
        $value = str_replace(',', '', $this->input->post('value'));

        $this->db->where('id', $id)->update('payroll_tax_exchange_rate', array($type=>$value));
        lastq();
    }


    //FOR JS FUNCTION
    function get_tax_rate()
    {
        $sess_id = $this->input->post('id');

        $filter = array('session_id'=>'where/'.$sess_id
                        );
        $v = getAll('payroll_tax_exchange_rate', $filter);
        if ($v->num_rows() > 0) {
            $v = $v->row();
            echo json_encode(array('value'=>$v->value, 'id'=>$v->id));
        }else{
            echo json_encode(array('value'=> 0, 'id'=> 0)); 
        }
        //print_mz($v);
        
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
                    $this->template->add_css('assets/plugins/bootstrap-select2/select2.css');
                    $this->template->add_css('assets/plugins/data-tables/datatables.min.css');
                    $this->template->add_js('assets/plugins/data-tables/jquery.dataTables.min.js');
                    $this->template->add_js('assets/plugins/jquery-maskmoney/jquery.maskMoney.js');
                    $this->template->add_js('modules/js/'.$this->title.'/'.$this->filename.'.js');
                }

            if ( ! empty($data['username']))
            {
                $this->template->set_title($data['username']);
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