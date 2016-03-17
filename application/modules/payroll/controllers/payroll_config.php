<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class payroll_config extends MX_Controller {
    
    var $title = "payroll";
    var $filename = "payroll_config";
    public $data;
    function __construct()
    {
        parent::__construct();
        $this->load->model('payroll_config_model','payroll');
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

    function edit_matrix($sess, $class, $val, $type=null){
        $id = $this->input->post('id');
        $value = str_replace(',', '', $this->input->post('value'));
        $data = array(
                'session_id' => $sess,
                'job_class_id'=> $class,
                'job_value_id' => $val,
                'value'.$type=>$value
            );

        $num_rows = getAll('payroll_job_value_matrix', array('session_id'=>'where/'.$sess, 'job_class_id'=>'where/'.$class, 'job_value_id'=>'where/'.$val))->num_rows();
        if($num_rows>0){$this->db->where('session_id',$sess)->where('job_class_id', $class)->where('job_value_id', $val)->update('payroll_job_value_matrix', array('value'.$type=>$value));}else{$this->db->insert('payroll_job_value_matrix', $data);}
        lastq();
    }

    function edit_com($type){
        $id = $this->input->post('id');
        $value = str_replace(',', '', $this->input->post('value'));

        $this->db->where('id', $id)->update('payroll_compensation_mix', array($type=>$value));
        lastq();
    }

    function edit_jm($type){
        $id = $this->input->post('id');
        $value = str_replace(',', '', $this->input->post('value'));

        $this->db->where('id', $id)->update('payroll_jm_parameter', array($type=>$value));
        lastq();
    }

    function edit_jam(){
        $id = $this->input->post('id');
        $filter =  array('id'=>'where/'.$id);
        $session_id = $this->input->post('session_id');
        $value = str_replace(',', '', $this->input->post('value'));
        $data = array('session_id' => $session_id,
                      'value' => $value
         );
        $num = getAll('payroll_jam',array('session_id'=>'where/'.$session_id))->num_rows();
        if($num>0)$this->db->where('id', $id)->update('payroll_jam', $data);else $this->db->insert('payroll_jam', $data);
        lastq();
    }

    function edit_jam_std(){
        $id = $this->input->post('id');
        $filter =  array('id'=>'where/'.$id);
        $session_id = $this->input->post('session_id');
        $value = str_replace(',', '', $this->input->post('value'));
        $data = array('session_id' => $session_id,
                      'value' => $value
         );
        $num = getAll('payroll_jam_std',array('session_id'=>'where/'.$session_id))->num_rows();
        if($num>0)$this->db->where('id', $id)->update('payroll_jam_std', $data);else $this->db->insert('payroll_jam_std', $data);
        lastq();
    }

    function edit_divider(){
        $id = $this->input->post('id');
        $value = str_replace(',', '', $this->input->post('value'));

        $this->db->where('id', $id)->update('payroll_pembagi', array('value'=>$value));
        lastq();
    }

    function edit_rate(){
        $id = $this->input->post('id');
        $value = str_replace(',', '', $this->input->post('value'));

        $this->db->where('id', $id)->update('payroll_exchange_rate', array('value'=>$value));
        lastq();
    }

    function edit_cola(){
        $id = $this->input->post('id');
        $value = str_replace(',', '', $this->input->post('value'));

        $this->db->where('id', $id)->update('payroll_cola', array('value'=>$value));
        lastq();
    }

    //FOR JS FUNCTION
    function get_table_matrix($sess_id)
    {
        $data['session_id'] = $sess_id;
        $data['pos'] = getAll('hris_job_class', array('job_level'=>"where/management", 'gradeval_top'=>'order/desc', 'job_class_cd'=>'order/asc'));
        $data['pos_non'] = getAll('hris_job_class', array('job_level'=>"where/nonmanagement", 'gradeval_top'=>'order/desc'));
        $data['val'] = getAll('payroll_job_value', array('is_deleted'=>"where/0"));
        $this->load->view('payroll_config/matrix_table', $data);
    }

    function get_table_com($sess_id)
    {

        $filter = array(
                        'session_id'=>'where/'.$sess_id
                        );
        //$data['com'] = getAll('payroll_job_value_com', $filter);
        $data['com'] = $this->payroll->get_com_table($sess_id);

        $this->load->view('payroll_config/com_table', $data);
    }

    function get_table_jm($sess_id)
    {

        $filter = array(
                        'session_id'=>'where/'.$sess_id
                        );
        $data['jm'] = getAll('payroll_jm_parameter', $filter);
        //$data['com'] = $this->payroll->get_com_table($sess_id);

        $this->load->view('payroll_config/jm_table', $data);
    }

    function get_jam()
    {
        $sess_id = $this->input->post('id');
        $filter = array('session_id'=>'where/'.$sess_id
                        );
        $q = getAll('payroll_jam', $filter);
        $num = $q->num_rows();
        $v = $q->row();
        $value = ($num>0)?$v->value:0;
        $id = ($num>0)?$v->id:0;

        $q2 = getAll('payroll_jam_std', $filter);//print_mz($q2->result());
        $num2 = $q2->num_rows();
        $v2 = $q2->row();
        $value2 = ($num2>0)?$v2->value:0;
        $id2 = ($num2>0)?$v2->id:0;

        $jam = array('value'=>$value, 'id'=>$id);
        $std = array('value'=>$value2, 'id'=>$id2);


        echo json_encode(array('jam'=>$jam, 'std'=>$std));
    }

    function get_divider()
    {
        $sess_id = $this->input->post('id');
        $filter = array('session_id'=>'where/'.$sess_id
                        );
        $v = getAll('payroll_pembagi', $filter)->row();
        echo json_encode(array('value'=>$v->value, 'id'=>$v->id));
    }

    function get_rate()
    {
        $sess_id = $this->input->post('id');
        $filter = array('session_id'=>'where/'.$sess_id
                        );
        $v = getAll('payroll_exchange_rate', $filter)->row();
        echo json_encode(array('value'=>$v->value, 'id'=>$v->id));
    }

    function get_cola()
    {
        $sess_id = $this->input->post('id');
        $filter = array('session_id'=>'where/'.$sess_id
                        );
        $v = getAll('payroll_cola', $filter)->row();
        echo json_encode(array('value'=>$v->value, 'id'=>$v->id));
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