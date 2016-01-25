<?php
class upload_overtime extends MX_Controller {
    
function index(){
        $file = fopen('D:\DAY122015.csv', "r");

        $count = 0;
        while (($emapData = fgetcsv($file, 10000, ",")) !== FALSE)
        {
            $count++; 
            if($count>1){
                $date = explode("-", $emapData[10]);
                $data = array(
                    'acc_ot_incidental'=>$emapData[13],
                    'ovt_flag' => $emapData[14],
                    'ovt_reason' => $emapData[16],
                    'ovt_detail_reason'=>$emapData[15],
                );

                $this->db->where('id_employee', getPersonIdFromNik($emapData[2]))->where('tanggal', $date[0])->where('bulan', getMonthNumber($date[1]))->where('tahun', $emapData[12])->update('kg_kehadirandetil', $data);
                echo '<pre>';
                echo $this->db->last_query();
                echo '</pre>';
            }                           
        }
    }
}
