<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*************************************
  * Created : Dec 2011
  * Creator : Mazhters Irwan
  * Email   : irwansyah@komunigrafik.com
  * CMS ver : CI ver.2.0
*************************************/	

class employee extends CI_Controller {
	
	var $filename = "employee";
	var $tabel = "employee";
	var $id_primary = "id";
	var $title = "Data Employee";
	
	function __construct()
	{
		parent::__construct();
		$this->lang->load('attendance');
	}
	
	function index()
	{
    $data['title'] = $this->title;
    permission();
		$this->_render_page('index', $data);
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

                if(in_array($view, array('index', 'employee_edit')))
                {
                    $this->template->set_layout('default');

                    $this->template->add_css('assets/plugins/bootstrap-select2/select2.css');
                    $this->template->add_css('assets/plugins/bootstrap-datepicker/css/datepicker.css');
                    $this->template->add_css('assets/plugins/boostrap-clockpicker/bootstrap-clockpicker.min.css');

                    $this->template->add_js('assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
                    $this->template->add_js('assets/plugins/boostrap-clockpicker/bootstrap-clockpicker.min.js');
                    $this->template->add_js('modules/js/'.$this->filename.'.js');
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
    
	function main($dep=0,$user=0)
	{
		//Set Global
		permission();
    $data = GetHeaderFooter();
		$data['path_file'] = $this->filename;
		//permissionkaryawan($this->session->userdata('webmaster_id'), $data['path_file']);
		$data['main_content'] = $data['path_file'];
		$data['filename'] = $this->filename;
		$data['title'] = $this->title;
		
		$path_paging = base_url().$this->filename."/main/".$dep."/".$user;
		$uri_segment = 5;
		$pg = $this->uri->segment($uri_segment);
		$per_page=1000;
		//End Global
		
		$filter = array();
		$filter_where_in = array("employee_grup"=> array("A", "B", "C", "D"));
		
		//Grup Admin
		$id_grup = $this->session->userdata("webmaster_grup");
		//End Grup Admin1
		
		$data['grid'] = array("Name","NIK","Group","Gender","BOD");
		$data['query_all'] = GetAll("hris_view_".$this->tabel, $filter, $filter_where_in);
		$filter['limit'] = $pg."/".$per_page;
		$filter['person_nm'] = "order/asc";
		$data['query_list'] = GetAll("hris_view_".$this->tabel, $filter, $filter_where_in);
		$data['list'] = array("person_nm","ext_id","employee_grup","adm_gender_cd","ttl");
		
		//Page
		$pagination = Page($data['query_all']->num_rows(),$per_page,$pg,$path_paging,$uri_segment);
		if(!$pagination) $pagination = "<strong>1</strong>";
		$data['pagination'] = $pagination;
		//End Page
		
		$this->load->view('employee',$data);		
	}
	
	function detail($id=0)
	{
		//Set Global
		permission();
		$data = GetHeaderFooter();
		$data['path_file'] = $this->filename;
		$data['main_content'] = $data['path_file'].'_form';
		$data['filename'] = $this->filename;
		$data['title'] = $this->title;
		if($id > 0) $data['val_button'] = lang("edit");
		else $data['val_button'] = lang("add");
		//End Global
		
		$q = GetAll("hris_view_".$this->tabel, array("person_id"=> "where/".$id));
		$r = $q->result_array();
		if($q->num_rows() > 0) $data['val'] = $r[0];
		else $data['val'] = array();
		
		$data['opt_emp_grup'] = array(""=> "- Group -", "N.A."=>"N.A.", "A"=>"A", "B"=>"B", "C"=>"C", "D"=>"D");
		/*$data['opt_agama'] = GetOptAgama();
		$data['opt_blood'] = GetOptBlood();
		$data['opt_pernikahan'] = GetOptAll("marrital_status");
		$data['opt_emp_status'] = GetOptEmployeeStatus();
		$data['opt_position'] = GetOptPosition();
		$data['opt_department'] = GetOptDepartment();
		$data['opt_grup'] = GetOptAll("admin_grup");
		$data['opt_contract'] = GetOptLatestContract();
		$data['opt_stcontract'] = GetOptStatusContract();
		$data['opt_keanggotaan'] = GetOptKeanggotaan();*/
		
		$this->load->view('employee_form',$data);
	}
	
	function update()
	{
		$webmaster_id = permission();
		$id = $this->input->post('id');
		$GetColumns = GetColumns("hris_persons");
		foreach($GetColumns as $r)
		{
			if($this->input->post($r['Field']."_file") || isset($_FILES[$r['Field']]['name']))
			{
				if($_FILES[$r['Field']]['name'])
				{
					$data[$r['Field']] = InputFile($r['Field'], 1000);
					if($data[$r['Field']] == "2")
					{
						$this->session->set_flashdata("message", lang('msg_err_size'));
						redirect($this->filename.'/detail/'.$id);
					}
					else if($data[$r['Field']] == "3")
					{
						$this->session->set_flashdata("message", lang('msg_err_ext'));
						redirect($this->filename.'/detail/'.$id);
					}
					
					$file_old = $this->input->post($r['Field']."_file");
					if(file_exists("./".$this->config->item('path_upload')."/".$file_old) && $file_old) unlink("./".$this->config->item('path_upload')."/".$file_old);
					
					$thumb = GetThumb($file_old);
					if(file_exists("./".$this->config->item('path_upload')."/".$thumb) && $thumb) unlink("./".$this->config->item('path_upload')."/".$thumb);
				}
			}
			else
			{
				$data[$r['Field']] = $this->input->post($r['Field']);
				$data[$r['Field']."_temp"] = $this->input->post($r['Field']."_temp");
				
				if($r['Field'] == "userpass")
				{
					if($data[$r['Field']] != $data[$r['Field']."_temp"]) $data[$r['Field']] = md5($this->config->item('encryption_key').$data[$r['Field']]);
				}
				
				if(!$data[$r['Field']] && !$data[$r['Field']."_temp"]) unset($data[$r['Field']]);
				unset($data[$r['Field']."_temp"]);
			}
		}
		$dt = date("Y-m-d H:i:s");
		//$data['employee_grup'] = $this->input->post("employee_grup");
		//print_mz($data);
		if($id > 0)
		{
			//Insert ke tabel history
			/*$q = GetAll($this->tabel, array("id"=> "where/".$id));
			$history = $q->result_array();
			$history[0]['modify_date'] = date("Y-m-d H:i:s");
			$this->db->insert($this->tabel."_history", $history[0]);*/
			
			//$data['modify_user_id'] = $webmaster_id;
			$this->db->where("person_id", $id);
			$this->db->update("hris_persons", $data);
			
			$this->db->where("person_id", $id);
			$this->db->update("kg_hris_employee", array("employee_grup"=> $this->input->post("employee_grup"), "employee_grup_active"=> $this->input->post("employee_grup_active")));
			
			$this->exe_shift($id, $this->input->post('employee_grup_active'));
			//Admin Log
			//$logs = $this->db->last_query();
			//$this->model_admin_all->LogActivities($webmaster_id,$this->tabel,$this->db->insert_id(),$logs,lang($this->filename),$data[$this->title_table],$this->filename,"Add");
			
			//$this->session->set_flashdata("message", lang('edit')." ".$this->title." ".lang('msg_sukses'));
		}
		else
		{
			//$data['create_user_id'] = $webmaster_id;
			$data['created_dttm'] = $dt;
			$this->db->insert("hris_persons", $data);
			$id = $this->db->insert_id();
			//Admin Log
			//$logs = $this->db->last_query();
			//$this->model_admin_all->LogActivities($webmaster_id,$this->tabel,$this->db->insert_id(),$logs,lang($this->filename),$data[$this->title_table],$this->filename,"Add");
			
			//$this->session->set_flashdata("message", lang('add')." ".$this->title." ".lang('msg_sukses'));
		}
		
		/*$k=0;
		$filter = array("urut_position"=> "order/asc");
		$q = GetAll("view_employee", $filter);
		foreach($q->result_array() as $r)
		{
			$k++;
			if($r['id'] == $id)
			{
				if($k%15) $pg = floor($k/15) * 15;
				else $pg = (floor($k/15)-1) * 15;
				break;
			}
		}
		
		if($this->input->post("stay")) redirect($this->filename.'/detail/'.$id);
		else redirect($this->filename."/main/0/0/".$pg);*/
	}
	
	function delete()
	{
		$webmaster_id = permission();
		$data=array();
		
		$exp = explode("-",$this->input->post('del_id'));
		foreach($exp as $r)
		{
			if($r)
			{
				$data[]=$r;
				//Admin Log
				//$logs = "DELETE from ".$this->tabel." where id='".$r."'";
				//$this->model_admin_all->LogActivities($webmaster_id,$this->tabel,$r,$logs,lang($this->filename),'',$this->filename,"Delete");
				$q = GetAll($this->tabel, array("id"=> "where/".$r));
				$r = $q->result_array();
				$data_ins = array("id_employee"=> $r[0]['id'], "create_date"=> date("Y-m-d H:i:s"),
				 "modify_date"=> date("Y-m-d H:i:s"), "create_user_id"=> GetUserID(), "modify_user_id"=> GetUserID());
				$this->db->insert("exitmng", $data_ins);
			}
		}
		
		//$this->db->where_in($this->id_primary, $data);
		//$this->db->delete($this->tabel);
		$this->db->where_in($this->id_primary, $data);
		$this->db->update($this->tabel, array("is_active"=> "InActive", "modify_date"=> date("Y-m-d H:i:s"), "modify_user_id"=> GetUserID()));
		$this->session->set_flashdata("message", lang('delete')." ".count($data)." ".lang($this->filename)." ".lang('msg_sukses'));
	}
	
	function exe_shift($person_id, $tgl, $thn=2016) 
	{
		$webmaster_id = permission();
		$exp = explode("-", $tgl);
		$thn=$exp[0];
		$grup=array("A", "B", "C", "D");
		$bln=array("01","02","03","04","05","06","07","08","09","10","11","12");
		if($thn == "2016") {
			$jadwal_grup['A'] = array(1,2,2,2,"off",3,3,"off",1,1,1,2,2,"off",3,3,"off","off",1,1,2,2,"off",3,3,3,"off",1);
			$jadwal_grup['B'] = array("off",1,1,1,2,2,"off",3,3,"off","off",1,1,2,2,"off",3,3,3,"off",1,1,2,2,2,"off",3,3);
			$jadwal_grup['C'] = array(3,3,"off","off",1,1,2,2,"off",3,3,3,"off",1,1,2,2,2,"off",3,3,"off",1,1,1,2,2,"off");
			$jadwal_grup['D'] = array(2,"off",3,3,3,"off",1,1,2,2,2,"off",3,3,"off",1,1,1,2,2,"off",3,3,"off","off",1,1,2);
		} else if($thn == "2015") {
			$jadwal_grup['A'] = array(1,1,2,2,2,"off",3,3,"off",1,1,1,2,2,"off",3,3,"off","off",1,1,2,2,"off",3,3,3,"off");
			$jadwal_grup['B'] = array(3,"off",1,1,1,2,2,"off",3,3,"off","off",1,1,2,2,"off",3,3,3,"off",1,1,2,2,2,"off",3);
			$jadwal_grup['C'] = array("off",3,3,"off","off",1,1,2,2,"off",3,3,3,"off",1,1,2,2,2,"off",3,3,"off",1,1,1,2,2);
			$jadwal_grup['D'] = array(2,2,"off",3,3,3,"off",1,1,2,2,2,"off",3,3,"off",1,1,1,2,2,"off",3,3,"off","off",1,1);
		}
		//print_mz($jadwal_grup);
		
		//foreach($grup as $g) {
			$emp = GetAll("kg_hris_employee", array("person_id"=> "where/".$person_id));
			foreach($emp->result_array() as $r) {
				$g=$r['employee_grup'];
				if(isset($jadwal_grup[$g])) {
					$id_employee=$r['person_id'];
					$loop=0;
					foreach($bln as $b) {
						if($b >= $exp[1]) {
							$datanginput=array('id_employee'=> $id_employee, 'bulan'=> $b, 'tahun'=> $thn);
							$jml_hari = date("t", mktime(0, 0, 0, $b, 1, $thn));
							$awal = $b==$exp[1] ? intval($exp[2]) : 1;
							for($i=$awal;$i<=$jml_hari;$i++) {
								if($loop==28) $loop=0;
								$datanginput['tgl_'.$i]=$jadwal_grup[$g][$loop];
								$loop++;
							}
							
							$temp_datahitung = $datanginput;
							unset($temp_datahitung['bulan']);
							unset($temp_datahitung['tahun']);
							unset($temp_datahitung['id_employee']);
							
							$hitung=array_count_values($temp_datahitung);
							//print_mz($datanginput);
							if(!isset($hitung['1'])) $hitung['1']=0;
							if(!isset($hitung['2'])) $hitung['2']=0;
							if(!isset($hitung['3'])) $hitung['3']=0;
							if(!isset($hitung['ns'])) $hitung['ns']=0;
							if(!isset($hitung['off'])) $hitung['off']=0;	
								
							$datanginput['jum_p']=$hitung['1'];
							$datanginput['jum_s']=$hitung['2'];
							$datanginput['jum_m']=$hitung['3'];
							$datanginput['jum_ns']=$hitung['ns'];
							$datanginput['jum_off']=$hitung['off'];
							
							$cekdata=GetValue("id", "kg_jadwal_shift", array("id_employee"=> "where/".$id_employee, "bulan"=> "where/".$b, "tahun"=> "where/".$thn));
							if(!$cekdata) {
								$datanginput['create_user_id'] = $webmaster_id;
								$datanginput['create_date'] = date("Y-m-d H:i:s");
								//print_mz($datanginput);
								$this->db->insert('kg_jadwal_shift',$datanginput);
								//$log[]=date("d-m-Y H:i:s").'; BERHASIL! Karyawan dengan nama '.GetValue('name','kg_employee',array('nik'=>'where/'.$nik)).' dari departemen '.GetValue('title','kg_department',array('id'=>'where/'.GetValue('id_department','kg_employee',array('nik'=>'where/'.$nik)))).'. untuk periode '.$bln.' - '.$thn.' telah diinput';
							} else {
								$datanginput['modify_user_id'] = $webmaster_id;
								$datanginput['modify_date'] = date("Y-m-d H:i:s");
								$this->db->where("id_employee", $id_employee);
								$this->db->where("bulan", $b);
								$this->db->where("tahun", $thn);
								$this->db->update('kg_jadwal_shift',$datanginput);
								//lastq();
							}
						} else {
							$datanginput=array('id_employee'=> $id_employee, 'bulan'=> $b, 'tahun'=> $thn);
							$jml_hari = date("t", mktime(0, 0, 0, $b, 1, $thn));
							//$awal = $b==$exp[1] ? intval($exp[2]) : 1;
							for($i=1;$i<=$jml_hari;$i++) {
								if($loop==28) $loop=0;
								//$datanginput['tgl_'.$i]=$jadwal_grup[$g][$loop];
								$loop++;
							}
						}
					}
				}
			}
		//}
	}
}
?>