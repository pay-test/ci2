<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cooperation extends MX_Controller {
    
    var $title = "payroll";
    var $page_title = "Cooperation";
    var $filename = "cooperation";
    public $data;
    function __construct()
    {
        parent::__construct();
        $this->load->model('all_model');
        //$this->load->model('payroll_cooperation_model','payroll');
    }
    
    function index()
    {
        $this->data['title'] = ucfirst($this->title);
        $this->data['page_title'] = $this->page_title;
        $this->data['period'] = getAll('payroll_period');
        permission();
        $this->_render_page($this->filename, $this->data);
    }

    function upload_excel() {
        $config['upload_path'] = './upload/files/excel';
        $config['allowed_types'] = 'xlsx|xls|csv';
        $config['overwrite'] = TRUE;
        $config['max_size']  = '10000';

        $val = 0;
        
        $this->load->library('upload', $config);

        if ( ! $this->upload->do_upload('excelfile')){
            $error = array('error' => $this->upload->display_errors());
            die(print_r($error));
        }
        else{
            $data = array('upload_data' => $this->upload->data());
           // echo "success";
            $data_upload = $this->upload->data();
            //die(print_r($data_upload));
            $result = $this->run_import($data_upload);

            foreach ($result as $row) {
                $employee_ext_id = $row['A'];
                $val = $row['C'];
                $employee_id = GetValue('employee_id','hris_employee',array('employee_ext_id' => 'where/'.$employee_ext_id));

                //count
                $count = GetAll('payroll_monthly_deduction_cooperation',array('payroll_period_id' => 'where/'.sessNow(), 'employee_id' => 'where/'.$employee_id))->num_rows();

                if ($count > 0) {
                    $val_old = GetValue('value','payroll_monthly_deduction_cooperation',array('employee_id' => 'where/'.$employee_id, 'payroll_period_id' => 'where/'.sessNow()));
                    $val_new = $val_old + $val;
                    $data_update = array('value' => $val_new);
                    $this->all_model->update('payroll_monthly_deduction_cooperation',$data_update,array('employee_id' => $employee_id, 'payroll_period_id' => sessNow()));
                    //lastq();
                }else{
                    $data_insert = array(
                        'payroll_period_id' => sessNow(),
                        'employee_id' => $employee_id,
                        'value' => $val,
                        'created_by' => sessId(),
                        'created_on' => date('Y-m-d H:i:s')
                        );
                    $this->all_model->insert('payroll_monthly_deduction_cooperation',$data_insert);
                }
            }
            
        }
        redirect('payroll/cooperation');
    }

    public function run_import($file_upload) {
        $file_path = './upload/files/excel/'.$file_upload['file_name'];
        //load the excel library
        $this->load->library('excel');
        //read file from path
        $inputFileType = PHPExcel_IOFactory::identify($file_path);
        //die(print_r($inputFileType));
        /**  Create a new Reader of the type defined in $inputFileType  **/
        $objReader = PHPExcel_IOFactory::createReader($inputFileType);

        $objPHPExcel = $objReader->load($file_path);
        //die(print_r($objPHPExcel));
        //get only the Cell Collection
        $cell_collection = $objPHPExcel->getActiveSheet()->getCellCollection();
        //extract to a PHP readable array format
        foreach ($cell_collection as $cell) {
            $column = $objPHPExcel->getActiveSheet()->getCell($cell)->getColumn();
            $row = $objPHPExcel->getActiveSheet()->getCell($cell)->getRow();
            $data_value = $objPHPExcel->getActiveSheet()->getCell($cell)->getValue();
            //header will/should be in row 1 only. of course this can be modified to suit your need.
            if ($row == 1) {
                $header[$row][$column] = $data_value;
            } else {
                $arr_data[$row][$column] = $data_value;
            }
        }
        //send the data in an array format
        $data['header'] = $header;
        $data['values'] = $arr_data;

        return $arr_data;
    }

    public function run_import_($file_upload){
        $file   = explode('.',$file_upload['file_name']);
        //die(print_r($file));
        $length = count($file);
        //die(print_r($file[$length -1]));
        if($file[$length -1] == 'xlsx' || $file[$length -1] == 'xls'){//jagain barangkali uploadnya selain file excel <span class="wp-smiley wp-emoji wp-emoji-smile" title=":-)">:-)</span>
            //$tmp = $file_upload['full_path'];//Baca dari tmp folder jadi file ga perlu jadi sampah di server :-p
            $tmp = $file_path = './upload/files/excel/'.$file_upload['file_name'];
            $this->load->library('excel');

            /**  Identify the type of $inputFileName  **/
            $inputFileType = PHPExcel_IOFactory::identify($tmp);
            //die($inputFileType);
            /**  Create a new Reader of the type that has been identified  **/
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            /**  Load $inputFileName to a PHPExcel Object  **/
            $objPHPExcel = $objReader->load($tmp);

            $read   = PHPExcel_IOFactory::createReaderForFile($tmp);

            $read->setReadDataOnly(true);
            $excel  = $read->load($tmp);

            $sheets = $read->listWorksheetNames($tmp);//baca semua sheet yang ada
           // die(print_r($sheets));
            foreach($sheets as $sheet){
                if($this->db->table_exists($sheet)){//check sheet-nya itu nama table ape bukan, kalo bukan buang aja... nyampah doank :-p
                    $_sheet = $excel->setActiveSheetIndexByName($sheet);//Kunci sheetnye biar kagak lepas :-p
                    $maxRow = $_sheet->getHighestRow();
                    $maxCol = $_sheet->getHighestColumn();
                    $field  = array();
                    $sql    = array();
                    $maxCol = range('A',$maxCol);
                    foreach($maxCol as $key => $coloumn){
                        $field[$key]    = $_sheet->getCell($coloumn.'1')->getCalculatedValue();//Kolom pertama sebagai field list pada table
                    }
                    for($i = 2; $i <= $maxRow; $i++){
                        foreach($maxCol as $k => $coloumn){
                            $sql[$field[$k]]  = $_sheet->getCell($coloumn.$i)->getCalculatedValue();
                        }
                        //$this->db->insert($sheet,$sql);//ribet banget tinggal insert doank...
                        die(print_r($sql));
                    }
                }
            }
        }else{
            die("error");
            exit('do not allowed to upload');//pesan error tipe file tidak tepat

        }
        die("home");
        redirect('home');//redirect after success
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