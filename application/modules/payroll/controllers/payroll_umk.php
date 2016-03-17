<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class payroll_umk extends MX_Controller {
    
    var $title = "payroll";
    var $filename = "payroll_umk";
    public $data;
    function __construct()
    {
        parent::__construct();
        //$this->load->model('payroll_umk_model','payroll');
    }
    
    function index()
    {
        $this->data['title'] = $this->title;
        $filter_org = array('org_class_id'=>'where/4', 'status_cd'=>'where/normal', 'org_nm='=>'order/asc');
        $this->data['session'] = getAll('hris_global_sess', array('id'=>'order/desc'));
        
        permission();
        $this->_render_page($this->filename, $this->data);
    }

    function get_umk()
    {
        $session_id = $this->input->post('id');
        permission();
        $filter =  array('session_id'=>'where/'.$session_id);
        $num_rows = getAll('payroll_umk', $filter)->num_rows();
        $value = ($num_rows>0)?getValue('value', 'payroll_umk',$filter):0;
        $id = ($num_rows>0)?getValue('id', 'payroll_umk',$filter):'';
        //lastq();
        echo json_encode(array('value'=>$value, 'id'=>$id));
    }

    function edit(){
        $session_id = $this->input->post('session_id');
        permission();
        $filter =  array('session_id'=>'where/'.$session_id);
        $num_rows = getAll('payroll_umk', $filter)->num_rows();//lastq();
        $data = array('value' => str_replace(',', '', $this->input->post('value')),
                      'session_id'=> $session_id,
            );
        if($num_rows>0)$this->db->where('session_id', $session_id)->update('payroll_umk', $data);
        else $this->db->insert('payroll_umk', $data);

        return true;
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