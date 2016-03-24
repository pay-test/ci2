<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cooperation extends MX_Controller {
    
    var $title = "payroll";
    var $page_title = "Cooperation";
    var $filename = "cooperation";
    public $data;
    function __construct()
    {
        parent::__construct();
        //$this->load->model('payroll_cooperation_model','payroll');
        $this->load->library('phpexcel');
        $this->load->library('PHPExcel/iofactory');
    }
    
    function index()
    {
        $this->data['title'] = ucfirst($this->title);
        $this->data['page_title'] = $this->page_title;

        permission();
        $this->_render_page($this->filename, $this->data);
    }

    function upload_excel() {
        //die(base_url('upload/files/payroll/'));
        $config['upload_path'] = './upload/files/excel';
        $config['allowed_types'] = 'xlsx|xls|csv';
        $config['max_size']  = '10000';
        
        $this->load->library('upload', $config);

        if ( ! $this->upload->do_upload('deduction_excel')){
            $error = array('error' => $this->upload->display_errors());
            die(print_r($error));
        }
        else{
            $data = array('upload_data' => $this->upload->data());
           // echo "success";
            die($_FILES['deduction_excel']['name']);
        }
    }

    function upload_barang(){
               $file = fopen('D:\barang.csv', "r");

        $count = 0;
        /*satuan :
        Pcs
        M
        roll=300m
        roll
        pack
        set
        */

        while (($emapData = fgetcsv($file, 10000, ",")) !== FALSE)
        {
            $count++; 
            if($count>7){

                switch ($emapData[5]) {
                    case 'PCS':
                        $satuan = 1;
                        break;
                    case 'ROLL':
                        $satuan = 2;
                        break;
                    case 'roll=300m':
                        $satuan = 3;
                        break;
                    case 'METER':
                        $satuan = 4;
                        break;
                    case 'PACK':
                        $satuan = 5;
                        break;
                    case 'SET':
                        $satuan = 6;
                        break;
                    
                    default:
                        $satuan = 1;
                        break;
                }

                if($emapData[4] == 'BARANG INVENTARIS'){
                    $jenis = 3;
                }elseif($emapData[4] == 'BARANG MENTAH'){
                    $jenis = 2;
                }else{
                    $jenis = 1;
                }
                $data = array(
                    'kode'=>$emapData[1],
                    'title' => $emapData[2],
                    'satuan' => $satuan,
                    'jenis_barang_id'=>$jenis,
                    'created_by'=>1,
                    'created_on'=>dateNow(),
                );
                $cek = getAll('barang', array('kode'=>'where/'.$emapData[1]))->num_rows();

                if($cek<1)$this->db->insert('barang', $data);else $this->db->where('kode', $emapData[1])->update('barang', $data);
                echo '<pre>';
                echo $count.'-'.$this->db->last_query();
                echo '</pre>';
            }                           
        }
    }

    public function run_import(){
        $file   = explode('.',$_FILES['excelfile']['name']);
        //die(print_r($file));
        $length = count($file);
        //die(print_r($file[$length -1]));
        if($file[$length -1] == 'xlsx' || $file[$length -1] == 'xls'){//jagain barangkali uploadnya selain file excel <span class="wp-smiley wp-emoji wp-emoji-smile" title=":-)">:-)</span>
            $tmp    = $_FILES['excelfile']['tmp_name'];//Baca dari tmp folder jadi file ga perlu jadi sampah di server :-p

            $this->load->library('phpexcel');
            $this->load->library('PHPExcel/iofactory');
            /**  Identify the type of $inputFileName  **/
            $inputFileType = IOFactory::identify($tmp);
            /**  Create a new Reader of the type that has been identified  **/
            $objReader = IOFactory::createReader($inputFileType);
            /**  Load $inputFileName to a PHPExcel Object  **/
            $objPHPExcel = $objReader->load($tmp);

            $read   = IOFactory::createReaderForFile($tmp);

            $read->setReadDataOnly(true);
            $excel  = $read->load($tmp);

            $sheets = $read->listWorksheetNames($tmp);//baca semua sheet yang ada
            die(print_r($sheets));
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
                    }
                }
            }
            die(print_r($file));
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