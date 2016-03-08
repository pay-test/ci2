<?php
class load extends CI_Controller {

	function __construct()
	{
		parent::__construct();
	}
	
	function delete_image()
	{
		$id = $this->input->post('del_id_img');
		$table = $this->input->post('del_table');
		$field = $this->input->post('del_field');
		
		$GetFile = GetValue($field,$table, array("id"=> "where/".$id));
		$GetThumb = GetThumb($GetFile);
		if($table != "admin")
		{
			if(file_exists("./".$this->config->item('path_upload')."/".$GetFile)) unlink("./".$this->config->item('path_upload')."/".$GetFile);
			if(file_exists("./".$this->config->item('path_upload')."/".$GetThumb)) unlink("./".$this->config->item('path_upload')."/".$GetThumb);
		}
		else
		{
			if(file_exists("./".$this->config->item('path_upload')."/foto/".$GetFile)) unlink("./".$this->config->item('path_upload')."/foto/".$GetFile);
			if(file_exists("./".$this->config->item('path_upload')."/foto/".$GetThumb)) unlink("./".$this->config->item('path_upload')."/foto/".$GetThumb);
		}
		
		$data[$field] = "";
		$this->db->where("id", $id);
		$this->db->update($table, $data);
	}
	
	function get_section()
	{
		$id_div = $this->input->post('id_division');
		$id_sec = $this->input->post('id_section');
		$opt[''] = "- Section -";
		$q = GetAll("hris_orgs", array("org_class_id"=> "where/4", "status_cd"=> "where/normal", "parent_id"=> "where/".$id_div));
		foreach($q->result_array() as $r)
		{
			$opt[$r['org_id']] = $r['org_nm'];
		}
		echo form_dropdown('s_sec', $opt, $id_sec, "id='id_sec' class='span2_2' onChange='get_position(this.value);'");
	}
	
	function get_position()
	{
		$id_sec = $this->input->post('id_section');
		$id_pos = $this->input->post('id_position');
		$opt[''] = "- Job Title -";
		$q = GetAll("hris_jobs", array("org_id"=> "where/".$id_sec));
		foreach($q->result_array() as $r)
		{
			$opt[$r['job_id']] = $r['job_nm'];
		}
		echo form_dropdown('s_pos', $opt, $id_sec, "id='id_pos' class='span2_2'");
	}
	
	function employee()
	{
		$q = GetAll("employee");
		foreach($q->result_array() as $r)
		{
			//$id_dep = $r['id_department']."000";
			//$data = array("id_department"=> $id_dep);
			$nik=str_replace(" ", "", $r['nik']);
			$nik_old=str_replace(" ", "", $r['nik_old']);
			$data = array("nik"=> $nik, "nik_old"=> $nik_old);
			$this->db->where("id", $r['id']);
			$this->db->update("employee", $data);
		}
	}
	
	/*function baca_absen($tglz, $blnz, $thnz, $shift)
	{
		
		for($k=$shift;$k<=3;$k++)
		{
			$filez = './uploads/tgl '.$tglz.' shift '.$k.'.dbf';
			$dbf = dbase_open($filez, 0);
			$column_info = dbase_get_header_info($dbf);
			$loop = dbase_numrecords($dbf);
			for($i=1;$i<=$loop;$i++)
			{
				$row = dbase_get_record_with_names($dbf,$i);
				$nik = $row['FCCARDNO'];
				$nik = substr($nik,0,1)."-".substr($nik,1,4)."-".substr($nik,5,3);
				$date = $row['FDDATE'];
				$tgl = substr($date,6,2);
				$bln = substr($date,4,2);
				$thn = substr($date,0,4);
				$masuk = $row['FCFIRSTIN'];
				$keluar = $row['FCLASTOUT'];
				if($thn."-".$bln."-".$tgl == $thnz."-".$blnz."-".$tglz)
				{
					$id_employee = GetValue("id", "employee", array("nik"=> "where/".$nik));
					if($id_employee)
					{
						$cek_hadir = GetValue("id", "kehadirandetil", array("id_employee"=> "where/".$id_employee, "tanggal"=> "where/".$tgl, "bulan"=> "where/".$bln, "tahun"=> "where/".$thn));
						if(!$cek_hadir && ($masuk || $keluar))
						{
							$data = array("id_employee"=> $id_employee, "jhk"=> 1, "jh"=> 1, "tanggal"=> $tgl, "bulan"=> $bln, "tahun"=> $thn,
							"scan_masuk"=> $masuk, "scan_pulang"=> $keluar);
							$this->db->insert("kehadirandetil", $data);
						}
					}
				}
			}
			
			if($k == 3)
			{
				$masuk=$keluar="";
				$sql = "select * from kg_employee where id not in (select id_employee from kg_kehadirandetil where tanggal='$tglz' and bulan='$blnz' and tahun='$thnz')";
				$q = $this->db->query($sql);
				foreach($q->result_array() as $r)
				{
					$data = array("id_employee"=> $r['id'], "jhk"=> 1, "alpa"=> 1, "tanggal"=> $tglz, "bulan"=> $blnz, "tahun"=> $thnz,
						"scan_masuk"=> $masuk, "scan_pulang"=> $keluar);
					$this->db->insert("kehadirandetil", $data);
				}
			}
		}
		
		die();	
	}*/
	
	function baca_absen_cron($jam=10, $tglz=NULL, $blnz=NULL, $thnz=NULL)
	{
		if(!$tglz) $tglz = date("d");
		if(!$blnz) $blnz = date("m");
		if(!$thnz) $thnz = date("Y");
		/*$waktu=array("","2","10");
		for($xx=7;$xx<=7;$xx++)
		{
			for($jj=1;$jj<=1;$jj++)
			{
				$jam=$waktu[$jj];
				$tglz=strlen($xx) == 1 ? "0".$xx : $xx;
				$blnz="08";
				$thnz="2014";*/
				if($jam==10)
				{
					$cek_log = GetValue("id", "log_cron", array("date"=> "where/".$thnz."-".$blnz."-".$tglz, "jam"=> "where/2"));
					$cek_log_skrg = GetValue("id", "log_cron", array("date"=> "where/".$thnz."-".$blnz."-".$tglz, "jam"=> "where/10"));
				}
				else
				{
					$log_kemarin = explode("-", date("Y-m-d", mktime(0, 0, 0, $blnz, $tglz-1, $thnz)));
					$cek_log = GetValue("id", "log_cron", array("date"=> "where/".$log_kemarin[0]."-".$log_kemarin[1]."-".$log_kemarin[2], "jam"=> "where/10"));
					$cek_log_skrg = GetValue("id", "log_cron", array("date"=> "where/".$thnz."-".$blnz."-".$tglz, "jam"=> "where/2"));
				}
				/*$filez = file_get_contents('http://127.0.0.1/dus/device/db.php?tahun='.$thnz.'&bulan='.$blnz.'&tanggal='.$tglz.'&jam='.$jam);
				$absen = explode("<br>", $filez);
				$absen = array_filter($absen);
				print_mz($absen);*/
				
				if(!$cek_log_skrg && $cek_log) {
					//$xx='http://127.0.0.1/dus/device/db.php?tahun='.$thnz.'&bulan='.$blnz.'&tanggal='.$tglz.'&jam='.$jam;
					//die($xx."S");
					$create_date=date("Y-m-d H:i:s");
					$filez = file_get_contents('http://127.0.0.1/dus/device/db.php?tahun='.$thnz.'&bulan='.$blnz.'&tanggal='.$tglz.'&jam='.$jam);
					//$filez = file_get_contents('http://10.170.4.65/db/db.php?tahun='.$thnz.'&bulan='.$blnz.'&tanggal='.$tglz.'&jam='.$jam);
					$absen = explode("<br>", $filez);
					$absen = array_filter($absen);
					//die($filez."S");
					//print_mz($absen);die();
					if(count($absen) > 5) {
					$tgl="";
					foreach($absen as $r)	{
						$exp = explode(";", $r);
						$nik = $exp[0];
						$nik = substr($nik,0,1)."-".substr($nik,1,4)."-".substr($nik,5,3);
						$date = $exp[1];
						$tgl = substr($date,6,2);
						$bln = substr($date,4,2);
						$thn = substr($date,0,4);
						$date_kemarin = explode("-", date("Y-m-d", mktime(0, 0, 0, $bln, $tgl-1, $thn)));
						$masuk = $exp[2];
						$keluar = $exp[3];
						//die(strtotime($masuk)."/".strtotime($keluar));
						$id_employee = GetValue("id", "employee", array("nik"=> "where/".$nik));
						if(!$id_employee) $id_employee = GetValue("id", "employee", array("nik_old"=> "where/".$nik));
						//lastq();
						if($id_employee) {
							$jadwal = strtoupper(GetValue("tgl_".intval($tgl), "jadwal_shift", array("bulan"=> "where/".$bln, "tahun"=> "where/".$thn, "id_employee"=> "where/".$id_employee)));
							$cek_hadir = GetValue("id", "kehadirandetil", array("id_employee"=> "where/".$id_employee, "tanggal"=> "where/".$tgl, "bulan"=> "where/".$bln, "tahun"=> "where/".$thn));
							if(!$cek_hadir && ($masuk || $keluar)) {
								$cek_jam=intval(substr($masuk,0,2));
								$cek_menit=substr($masuk,3,2);
								$jam_menit = $cek_jam.$cek_menit;
								$telat=0;
								if((strtotime($keluar) - strtotime($masuk)) <= 7200) {//4 jam
									$jadwal_sebelumnya = strtoupper(GetValue("tgl_".intval($date_kemarin[2]), "jadwal_shift", array("bulan"=> "where/".$date_kemarin[1], "tahun"=> "where/".$date_kemarin[0], "id_employee"=> "where/".$id_employee)));
									if($jadwal_sebelumnya == "M" || $jadwal == "M") {
										//Shift Malem
										//echo $id_employee."/".$masuk."<br>";
										if(substr($masuk,0,2) <= 2 || (substr($masuk,0,2) >= 12 && substr($masuk,0,2) <= 24)) {
											//Cron jam 2
											if($jam_menit >= 2301 || ($cek_jam >= 0 && $cek_jam <= 2)) $telat=1;
											$data = array("id_employee"=> $id_employee, "jhk"=> 1, "jh"=> 1, "terlambat"=> $telat, "tanggal"=> $tgl, "bulan"=> $bln, "tahun"=> $thn,
											"scan_masuk"=> $keluar, "scan_pulang"=> "", "ot_cont_allow"=> 1, "create_date"=> $create_date);
											$this->db->insert("kehadirandetil", $data);
										} else {
											//Cron jam 10
											$cek_absen_sebelumnya = GetAll("kehadirandetil", array("id_employee"=> "where/".$id_employee, "tanggal"=> "where/".$date_kemarin[2], "bulan"=> "where/".$date_kemarin[1], "tahun"=> "where/".$date_kemarin[0], "scan_pulang"=> "where/", "scan_masuk !="=> "where/"));
											if($cek_absen_sebelumnya->num_rows() > 0) {
												$shift3 = $cek_absen_sebelumnya->result_array();
												$data = array("scan_pulang"=> $keluar, "modify_date"=> $create_date);
												$overtime = $this->overtime($shift3[0]['scan_masuk'], $keluar, $jadwal);
												if($overtime) {
													$data['lembur'] = 1;
													$data['alasan_lembur'] = 1;
													$data['ot_incidental'] = $overtime;
												}
												$this->db->where("id", $shift3[0]['id']);
												$this->db->update("kehadirandetil", $data);
											}
										}
									} else if($jadwal_sebelumnya == "S") {
										//Shift Sore Pulang Pagi
										//Cron jam 10
										$cek_absen_sebelumnya = GetAll("kehadirandetil", array("id_employee"=> "where/".$id_employee, "tanggal"=> "where/".$date_kemarin[2], "bulan"=> "where/".$date_kemarin[1], "tahun"=> "where/".$date_kemarin[0], "scan_pulang"=> "where/", "scan_masuk !="=> "where/"));
										if($cek_absen_sebelumnya->num_rows() > 0) {
											$shift3 = $cek_absen_sebelumnya->result_array();
											$data = array("scan_pulang"=> $keluar, "modify_date"=> $create_date);
											$overtime = $this->overtime($shift3[0]['scan_masuk'], $keluar, $jadwal);
											if($overtime) {
												$data['lembur'] = 1;
												$data['alasan_lembur'] = 1;
												$data['ot_incidental'] = $overtime;
											}
											$this->db->where("id", $shift3[0]['id']);
											$this->db->update("kehadirandetil", $data);
										} else {
											$id_dept = GetValue("id_department", "employee", array("id"=> "where/".$id_employee));
											if(($jadwal) == "P" && $jam_menit >= 701 && $cek_jam <= 9) $telat=1;
											else if(($jadwal) == "NS" && $jam_menit >= 801 && $cek_jam <= 10) $telat=1;
											else if(($jadwal) == "S" && $jam_menit >= 1501 && $cek_jam <= 17) $telat=1;
											else if(($jadwal) == "OFF") $telat=0;
											else if($jam_menit >= 801 && $cek_jam <= 10) $telat=1;
											
											if($cek_jam > 12) $masuk="-";
											else $keluar="";
											
											$data = array("id_employee"=> $id_employee, "jhk"=> 1, "jh"=> 1, "terlambat"=> $telat, "tanggal"=> $tgl, "bulan"=> $bln, "tahun"=> $thn,
											"scan_masuk"=> $masuk, "scan_pulang"=> $keluar, "ot_cont_allow"=> 1, "create_date"=> $create_date);
											$this->db->insert("kehadirandetil", $data);
										}
									} else {
										//Shift Pagi / Non Shift Absen Masuk
										$id_dept = GetValue("id_department", "employee", array("id"=> "where/".$id_employee));
										if(($jadwal) == "P" && $jam_menit >= 701 && $cek_jam <= 9) $telat=1;
										else if(($jadwal) == "NS" && $jam_menit >= 801 && $cek_jam <= 10) $telat=1;
										else if(($jadwal) == "OFF") $telat=0;
										else if($jam_menit >= 801 && $cek_jam <= 10) $telat=1;
										
										if($cek_jam > 12) $masuk="-";
										else $keluar="";
										
										$data = array("id_employee"=> $id_employee, "jhk"=> 1, "jh"=> 1, "terlambat"=> $telat, "tanggal"=> $tgl, "bulan"=> $bln, "tahun"=> $thn,
										"scan_masuk"=> $masuk, "scan_pulang"=> $keluar, "ot_cont_allow"=> 1, "create_date"=> $create_date);
										
										$cek_sabtu = date("w", strtotime(date("Y-m-d")));
										if($cek_sabtu == 6 && $jadwal == "NS") $data['ot_cont_allow'] = 0;
										$this->db->insert("kehadirandetil", $data);
									}
								}
								else
								{
									//Shift Sore
									if($jam_menit >= 1501 && $cek_jam <= 17) $telat=1;
									$data = array("id_employee"=> $id_employee, "jhk"=> 1, "jh"=> 1, "terlambat"=> $telat, "tanggal"=> $tgl, "bulan"=> $bln, "tahun"=> $thn,
									"scan_masuk"=> $masuk, "scan_pulang"=> $keluar, "ot_cont_allow"=> 1, "create_date"=> $create_date);
									
									$overtime = $this->overtime($masuk, $keluar, strtoupper($jadwal));
									if($overtime) {
										$data['lembur'] = 1;
										$data['alasan_lembur'] = 1;
										$data['ot_incidental'] = $overtime;
									}
									$this->db->insert("kehadirandetil", $data);
								}
							}
							else
							{
								//Shift Pagi / Non Shift Absen Pulang
								$scan_masuk = GetValue("scan_masuk", "kehadirandetil", array("id_employee"=> "where/".$id_employee, "tanggal"=> "where/".$tgl, "bulan"=> "where/".$bln, "tahun"=> "where/".$thn));
								$data = array("scan_pulang"=> $keluar, "modify_date"=> $create_date);
								$overtime = $this->overtime($scan_masuk, $keluar, strtoupper($jadwal));
								if($overtime) {
									$data['lembur'] = 1;
									$data['alasan_lembur'] = 1;
									$data['ot_incidental'] = $overtime;
								}
								$this->db->where("id", $cek_hadir);
								$this->db->update("kehadirandetil", $data);
							}
						}
					}
					
					//Jam 
					if($jam==2)
					{
						//die("sini");
						$masuk=$keluar="";
						if($tgl) {
							$sql = "select * from kg_employee where is_active='Active' AND id not in (select id_employee from kg_kehadirandetil where tanggal='$tgl' and bulan='$bln' and tahun='$thn')";
							$q = $this->db->query($sql);
							foreach($q->result_array() as $r)
							{
								//cek hari sabtu & minggu
								$weekend = date("w", strtotime($thn."-".$bln."-".$tgl));
								//cek holiday
								$holiday = GetValue("id", "holiday", array("tgl_penuh"=> "where/".$thn."-".$bln."-".$tgl));
								//cek jadwal off
								$cek_jadwal = GetValue("tgl_".intval($tgl), "jadwal_shift", array("id_employee"=> "where/".$r['id'], "bulan"=> "where/".$bln, "tahun"=> "where/".$thn));
								if(!$cek_jadwal && ($weekend == 0 || $holiday)) {
									$data = array("id_employee"=> $r['id'], "jhk"=> 1, "off"=> 1, "tanggal"=> $tgl, "bulan"=> $bln, "tahun"=> $thn,
										"scan_masuk"=> $masuk, "scan_pulang"=> $keluar, "create_date"=> $create_date);
									$this->db->insert("kehadirandetil", $data);
								} else if($cek_jadwal != "off") {
									$data = array("id_employee"=> $r['id'], "jhk"=> 1, "alpa"=> 1, "tanggal"=> $tgl, "bulan"=> $bln, "tahun"=> $thn,
										"scan_masuk"=> $masuk, "scan_pulang"=> $keluar, "create_date"=> $create_date);
									$this->db->insert("kehadirandetil", $data);
								} else {
									$data = array("id_employee"=> $r['id'], "jhk"=> 1, "off"=> 1, "tanggal"=> $tgl, "bulan"=> $bln, "tahun"=> $thn,
										"scan_masuk"=> $masuk, "scan_pulang"=> $keluar, "create_date"=> $create_date);
									$this->db->insert("kehadirandetil", $data);
								}
							}
						}
					}
					
					//Insert Log
					$data = array("date"=> $thnz."-".$blnz."-".$tglz, "jam"=> intval($jam), "create_date"=> $create_date);
					$this->db->insert("log_cron", $data);
					//if($tglz == "27" && $jam=="10") die("ok");
					} else {
						die($tglz."/".$jam." data absen belum di load");
					}
				}
				else{
					if(!$cek_log_skrg) die($tglz."/".$jam." cron gagal, cron sebelumnya belum dijalankan");
					else die($tglz."/".$jam." cron ini sudah pernah dijalankan");
				}
			//Remark Manual
			//}
		//}
	}
	
	function baca_absen_rutin($jam=NULL, $tglz=NULL, $blnz=NULL, $thnz=NULL)
	{
		set_time_limit(0);
		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', -1);
		
		$tabel = "kehadirandetil_rutin";
		$menit = date("i");
		if(!$jam) $jam = date("H");
		if(!$tglz) $tglz = date("d");
		if(!$blnz) $blnz = date("m");
		if(!$thnz) $thnz = date("Y");
		/*$waktu=array("","2","10");
		for($xx=7;$xx<=7;$xx++)
		{
			for($jj=1;$jj<=1;$jj++)
			{
				$jam=$waktu[$jj];
				$tglz=strlen($xx) == 1 ? "0".$xx : $xx;
				$blnz="08";
				$thnz="2014";*/
				//$log_jam_sebelumnya = explode("-", date("Y-m-d-H", mktime($jam-1, 0, 0, $blnz, $tglz, $thnz)));
				//$cek_log = GetValue("id", "log_cron_rutin", array("date"=> "where/".$log_jam_sebelumnya[0]."-".$log_jam_sebelumnya[1]."-".$log_jam_sebelumnya[2], "jam"=> "where/".$log_jam_sebelumnya[3]));
				//$cek_log_skrg = GetValue("id", "log_cron_rutin", array("date"=> "where/".$thnz."-".$blnz."-".$tglz, "jam"=> "where/".$jam));
				/*$filez = file_get_contents('http://127.0.0.1/dus/device/db.php?tahun='.$thnz.'&bulan='.$blnz.'&tanggal='.$tglz.'&jam='.$jam);
				$absen = explode("<br>", $filez);
				$absen = array_filter($absen);
				print_mz($absen);*/
				
				//if(!$cek_log_skrg && $jam > 6)
				$create_date=date("Y-m-d H:i:s");
				echo "JANGAN DICLOSE TAB INI<br>".$create_date."<meta http-equiv='refresh' content='1200'>";
	
				if($jam >= 6)
				{
					exec('"C:/Program Files (x86)/Cardnetic/Smart2K-Bio Utility 1.11/Smart2KBio.exe"', $out);
					$filez = file_get_contents('http://127.0.0.1/dus/device/db_rutin.php?tahun='.$thnz.'&bulan='.$blnz.'&tanggal='.$tglz.'&jam='.$jam.'&menit='.$menit);
					$absen = explode("<br>", $filez);
					$absen = array_filter($absen);
					//die($filez."S");
					//print_mz($absen);die();
					$tgl="";
					foreach($absen as $r) {
						$exp = explode(";", $r);
						$nik = $exp[0];
						$nik = substr($nik,0,1)."-".substr($nik,1,4)."-".substr($nik,5,3);
						$date = $exp[1];
						$tgl = substr($date,6,2);
						$bln = substr($date,4,2);
						$thn = substr($date,0,4);
						$date_kemarin = explode("-", date("Y-m-d", mktime(0, 0, 0, $bln, $tgl-1, $thn)));
						$masuk = $exp[2];
						$keluar = $exp[3];
						//die(strtotime($masuk)."/".strtotime($keluar));
						$id_employee = GetValue("id", "employee", array("nik"=> "where/".$nik));
						if(!$id_employee) $id_employee = GetValue("id", "employee", array("nik_old"=> "where/".$nik));
						//lastq();
						if($id_employee) {
							$jadwal = strtoupper(GetValue("tgl_".intval($tgl), "jadwal_shift", array("bulan"=> "where/".$bln, "tahun"=> "where/".$thn, "id_employee"=> "where/".$id_employee)));
							$jadwal_sebelumnya = strtoupper(GetValue("tgl_".intval($date_kemarin[2]), "jadwal_shift", array("bulan"=> "where/".$date_kemarin[1], "tahun"=> "where/".$date_kemarin[0], "id_employee"=> "where/".$id_employee)));
							$cek_hadir = GetValue("id", $tabel, array("id_employee"=> "where/".$id_employee, "tanggal"=> "where/".$tgl, "bulan"=> "where/".$bln, "tahun"=> "where/".$thn));
							if(!$cek_hadir) {
								if($masuk) {
									if($jadwal_sebelumnya == "S" && $jam <= 10) {
										$cek_absen_sebelumnya = GetAll("kehadirandetil", array("id_employee"=> "where/".$id_employee, "tanggal"=> "where/".$date_kemarin[2], "bulan"=> "where/".$date_kemarin[1], "tahun"=> "where/".$date_kemarin[0], "scan_pulang"=> "where/", "scan_masuk !="=> "where/"));
										if($cek_absen_sebelumnya->num_rows() == 0) {
											if(($jadwal) == "P" && $jam_menit >= 701 && $cek_jam <= 9) $telat=1;
											else if(($jadwal) == "NS" && $jam_menit >= 801 && $cek_jam <= 10) $telat=1;
											else if(($jadwal) == "S" && $jam_menit >= 1501 && $cek_jam <= 17) $telat=1;
											else if(($jadwal) == "OFF") $telat=0;
											else if($jam_menit >= 801 && $cek_jam <= 10) $telat=1;
											
											$data = array("id_employee"=> $id_employee, "jhk"=> 1, "jh"=> 1, "tanggal"=> $tgl, "bulan"=> $bln, "tahun"=> $thn,
											"scan_masuk"=> $masuk, "scan_pulang"=> "", "terlambat"=> $telat, "create_date"=> $create_date);
											$this->db->insert($tabel, $data);
										}
									} else if(($jadwal) != "M" || (($jadwal) == "M" && $jam >= 21)) {
										$telat=0;
										$cek_jam=intval(substr($masuk,0,2));
										$cek_menit=substr($masuk,3,2);
										$jam_menit = $cek_jam.$cek_menit;
										
										if(($jadwal) == "P" && $jam_menit >= 701 && $cek_jam <= 9) $telat=1;
										else if(($jadwal) == "NS" && $jam_menit >= 801 && $cek_jam <= 10) $telat=1;
										else if(($jadwal) == "S" && $jam_menit >= 1501 && $cek_jam <= 17) $telat=1;
										else if(($jadwal) == "M" && $jam_menit >= 2301) $telat=1;
										else if(($jadwal) == "OFF") $telat=0;
										else {
											if($jam_menit >= 801 && $cek_jam <= 10) $telat=1;
											else if($jam_menit >= 1501 && $cek_jam <= 17) $telat=1;
											else if($jam_menit >= 2301) $telat=1;
										}
										$data = array("id_employee"=> $id_employee, "jhk"=> 1, "jh"=> 1, "tanggal"=> $tgl, "bulan"=> $bln, "tahun"=> $thn,
										"scan_masuk"=> $masuk, "scan_pulang"=> "", "terlambat"=> $telat, "create_date"=> $create_date);
										$this->db->insert($tabel, $data);
									}
								}
							} else {
								$cek_jam_masuk = GetValue("scan_masuk", $tabel, array("id_employee"=> "where/".$id_employee, "tanggal"=> "where/".$tgl, "bulan"=> "where/".$bln, "tahun"=> "where/".$thn));
								if(($jam >= 14 && substr($cek_jam_masuk,0,2) < 9) || ($jam >= 22 && substr($cek_jam_masuk,0,2) < 17)){
									$data = array("scan_pulang"=> $masuk, "modify_date"=> $create_date);
									$this->db->where("id", $cek_hadir);
									$this->db->update($tabel, $data);
								}
							}
						}
					}
					
					//Insert Log
					$data = array("date"=> $thnz."-".$blnz."-".$tglz, "jam"=> $jam, "create_date"=> $create_date);
					$this->db->insert("log_cron_rutin", $data);
				}
				/*else{
					if(!$cek_log_skrg) die($tglz."/".$jam." cron gagal, cron sebelumnya belum dijalankan");
					else die($tglz."/".$jam." cron ini sudah pernah dijalankan");
				}*/
			//Remark Manual
			//}
		//}
	}
	
	function baca_absen_late($jam=8, $tglz=NULL, $blnz=NULL, $thnz=NULL, $exec=NULL)
	{
		set_time_limit(0);
		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', -1);
		if(!$tglz) $tglz = date("d");
		if(!$blnz) $blnz = date("m");
		if(!$thnz) $thnz = date("Y");
		/*$waktu=array("","2","10");
		for($xx=7;$xx<=7;$xx++)
		{
			for($jj=1;$jj<=1;$jj++)
			{
				$jam=$waktu[$jj];
				$tglz=strlen($xx) == 1 ? "0".$xx : $xx;
				$blnz="08";
				$thnz="2014";*/
				
				//$xx='http://127.0.0.1/dus/device/db.php?tahun='.$thnz.'&bulan='.$blnz.'&tanggal='.$tglz.'&jam='.$jam;
				//die($xx."S");
				if(!$exec) exec('"C:/Program Files (x86)/Cardnetic/Smart2K-Bio Utility 1.11/Smart2KBio.exe"', $out);
				//if(exec('"C:/Program Files (x86)/Cardnetic/Smart2K-Bio Utility 1.11/Smart2KBio.exe"')) {
				//die("sini");
					$create_date=date("Y-m-d H:i:s");
					$filez = file_get_contents('http://127.0.0.1/dus/device/db_late.php?tahun='.$thnz.'&bulan='.$blnz.'&tanggal='.$tglz.'&jam='.$jam);
					$absen = explode("<br>", $filez);
					$absen = array_filter($absen);
					//die($filez."S");
					//print_mz($absen);die();
					$tgl="";
					foreach($absen as $r)
					{
						$exp = explode(";", $r);
						$nik = $exp[0];
						$nik = substr($nik,0,1)."-".substr($nik,1,4)."-".substr($nik,5,3);
						$date = $exp[1];
						$tgl = substr($date,6,2);
						$bln = substr($date,4,2);
						$thn = substr($date,0,4);
						$date_kemarin = explode("-", date("Y-m-d", mktime(0, 0, 0, $bln, $tgl-1, $thn)));
						$masuk = $exp[2];
						$keluar = $exp[3];
						//die(strtotime($masuk)."/".strtotime($keluar));
						$id_employee = GetValue("id", "employee", array("nik"=> "where/".$nik));
						if(!$id_employee) $id_employee = GetValue("id", "employee", array("nik_old"=> "where/".$nik));
						//lastq();
						if($id_employee)
						{
							$jadwal = GetValue("tgl_".intval($tgl), "jadwal_shift", array("bulan"=> "where/".$bln, "tahun"=> "where/".$thn, "id_employee"=> "where/".$id_employee));
							$cek_hadir = GetValue("id", "kehadirandetil_late", array("id_employee"=> "where/".$id_employee, "tanggal"=> "where/".$tgl, "bulan"=> "where/".$bln, "tahun"=> "where/".$thn));
							if(!$cek_hadir && $masuk)
							{
								$telat=0;
								$cek_jam=intval(substr($masuk,0,2));
								$cek_menit=substr($masuk,3,2);
								$jam_menit = $cek_jam.$cek_menit;
								//$id_dept = GetValue("id_department", "employee", array("id"=> "where/".$id_employee));
								//if($id_dept == "6" && $jam_menit >= 701 && $cek_jam <= 9) $telat=1;
								if(($jadwal) == "P" && $jam_menit >= 701 && $cek_jam <= 9) $telat=1;
								else if(($jadwal) == "NS" && $jam_menit >= 801 && $cek_jam <= 10) $telat=1;
								else if(($jadwal) == "S" && $jam_menit >= 1501 && $cek_jam <= 17) $telat=1;
								else if(($jadwal) == "M" && $jam_menit >= 2301 || ($cek_jam >= 0 && $cek_jam <= 2)) $telat=1;
								else
								{
									if($jam_menit >= 801 && $cek_jam <= 10) $telat=1;
									else if($jam_menit >= 1501 && $cek_jam <= 17) $telat=1;
									else if($jam_menit >= 2301 || ($cek_jam >= 0 && $cek_jam <= 2)) $telat=1;
								}
								//if($telat==1) {
									$data = array("id_employee"=> $id_employee, "jhk"=> 1, "jh"=> 1, "terlambat"=> $telat, "tanggal"=> $tgl, "bulan"=> $bln, "tahun"=> $thn,
									"scan_masuk"=> $masuk, "scan_pulang"=> $keluar, "create_date"=> $create_date);
									$this->db->insert("kehadirandetil_late", $data);
								//}
							}
						}
					}
					
					if($jam==15) {$ket="2";$val_shift = array("s");}
					else if($jam==23) {$ket="3";$val_shift = array("m");}
					else {$ket="1";$val_shift = array("p", "ns");}
					
					$shift = GetAll("jadwal_shift", array("bulan"=> "where/".$bln, "tahun"=> "where/".$thn), array("tgl_".intval($tgl)=> $val_shift));
					foreach($shift->result_array() as $r)
					{
						$cek_late = GetValue("id", "kehadirandetil_late", array("id_employee"=> "where/".$r['id_employee'], "tanggal"=> "where/".$tgl, "bulan"=> "where/".$bln, "tahun"=> "where/".$thn));
						if(!$cek_late) {
							$data = array("id_employee"=> $r['id_employee'], "jhk"=> 1, "off"=> 1, "tanggal"=> $tgl, "bulan"=> $bln, "tahun"=> $thn,
									"keterangan"=> $ket, "create_date"=> $create_date);
							$this->db->insert("kehadirandetil_late", $data);
						}
					}
				//}
			//Remark Manual
			//}
		//}
	}
	
	function gaji()
	{
		$q = GetJoin("gaji_bu", "employee", "gaji_bu.nik=employee.nik", "inner", "employee.id, employee.nik");
		foreach($q->result_array() as $r)
		{
			$data=array("id_employee"=> $r['id']);
			$this->db->where("nik", $r['nik']);
			$this->db->update("gaji_bu", $data);
		}
	}
	
	function gaji_tmk()
	{
		//die();
		$this->load->library('excel');
		$bulan=array("Jan"=> "01", "Feb"=> "02", "Mar"=> "03", "Apr"=> "04", "May"=> "05", "Jun"=> "06", "Jul"=> "07", "Aug"=> "08",
		"Sep"=> "09", "Oct"=> "10", "Nov"=> "11", "Dec"=> "12");
		$create_date=date("Y-m-d H:i:s");
		$filez = './uploads/gaji_mar2014.xls';
		$objPHPExcel = PHPExcel_IOFactory::load($filez);
		
		$objPHPExcel->setActiveSheetIndex(0);
		$loop=$objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
		$sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
		for($i=1;$i<=$loop;$i++) {
			$kolom = $sheetData[$i];
			if($kolom['D']) {
				$nik = $kolom['D'];
				$exp = explode("-", $nik);
				if(isset($exp[2])){
					$id_employee = GetValue("id", "employee", array("nik"=> "like_before/-".str_replace(" ","",$exp[2])));
					if(!$id_employee) $id_employee = GetValue("id", "employee", array("nik_old"=> "like_before/-".str_replace(" ","",$exp[2])));
					//echo $id_employee."/".$nik."<br>";
					if($id_employee) {
						$cek_gaji = GetValue("id", "gaji_mar", array("id_employee"=> "where/".$id_employee));
						$data_ins = array("id_employee"=> $id_employee, 
						"ot_allow_shift"=> $kolom['H'],
						"ot_incidental"=> $kolom['I'], 
						"hari_kerja"=> $kolom['J'],
						"masa_kerja"=> str_replace(",","",$kolom['L']),
						"basic"=> str_replace(",","",$kolom['K']), 
						"bulan_ini"=> str_replace(",","",$kolom['M']),
						"tj_transport"=> str_replace(",","",$kolom['S']),
						"tj_kehadiran"=> str_replace(",","",$kolom['T']),
						"mkhr"=> str_replace(",","",$kolom['U']), 
						"total_gaji_bruto"=> str_replace(",","",$kolom['W']), 
						"lembur"=> $kolom['X'],
						"absen"=> str_replace(",","",$kolom['Z']),
						"bpr"=> str_replace(",","",$kolom['AA']), "btn"=> str_replace(",","",$kolom['AB']), 
						"iuran_sp"=> str_replace(",","",$kolom['AC']),
						"total_gaji_netto"=> str_replace(",","",$kolom['AE']), 
						"bulan"=> 3, "tahun"=> 2014, "date_full"=> "2014-03-15", "create_date"=> date("Y-m-d H:i:s"), "create_user_id"=> 1);
						
						if(!$cek_gaji) {
							echo $nik." / sukses<br>";
							$this->db->insert("gaji_mar", $data_ins);
						}
						/*else {
							$this->db->where("id_employee", $id_employee);
							$this->db->update("gaji", $data_ins);
						}*/
					}
					else echo $nik."<br>";
				}
			}
		}
	}
	
	function telat()
	{
		$q = GetAll("kehadirandetil_telat");
		foreach($q->result_array() as $r)
		{
			$data=array("terlambat"=> $r['terlambat']);
			$this->db->where("id", $r['id']);
			$this->db->update("kehadirandetil", $data);
		}
	}
	
	function absen_bulan()
	{
		$dt = strtotime("2014-01-03");
		$jam = array("", "2", "10");
		for($i=$dt;$i<=strtotime("2014-02-08");$i+=86400)
		{
			for($j=1;$j<=2;$j++)
			{
				$tgl = date("d", $i);
				$bln = date("m", $i);
				$thn = date("Y", $i);
				
				$this->baca_absen_cron($jam[$j], $tgl, $bln, $thn);
			}
		}
	}
	
	function master_list()
	{
		die();
		$this->load->library('excel');
		$bulan=array("Jan"=> "01", "Feb"=> "02", "Mar"=> "03", "Apr"=> "04", "May"=> "05", "Jun"=> "06", "Jul"=> "07", "Aug"=> "08",
		"Sep"=> "09", "Oct"=> "10", "Nov"=> "11", "Dec"=> "12");
		$create_date=date("Y-m-d H:i:s");
		$filez = './uploads/Masterlist-Mar 14.xls';
		$objPHPExcel = PHPExcel_IOFactory::load($filez);
		$site=3;
		$id_department = 3;
		$baris=11;
		for($site=3;$site<=12;$site++)
		{
		$objPHPExcel->setActiveSheetIndex($site);
		$loop=$objPHPExcel->setActiveSheetIndex($site)->getHighestRow();
		$sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
		for($i=$baris;$i<=$loop;$i++)
		{
			$kolom = $sheetData[$i];
			if($kolom['C'])
			{
				/*$position = GetValue("id", "position", array("title"=> "where/".$kolom['D']));
				if(!$position) $position = 1000;
				$sql = "select id from kg_marrital_status where title='".str_replace(" ","",$kolom['L'])."'";
				$q = $this->db->query($sql);
				if($q->num_rows() > 0)
				{
					$hasil = $q->result_array();
					$id_marital_status = $hasil[0]['id'];
				}
				else $id_marital_status = 1000;
				
				$exp = explode("-", str_replace(" ","-",$kolom['F']));
				if(isset($exp[2]))
				{
					$date_hire_since = "20".$exp[2]."-".$bulan[$exp[1]]."-".$exp[0];
				}
				else $date_hire_since="0000-00-00";
				
				$exp = explode("-", str_replace(" ","-",$kolom['G']));
				if(isset($exp[2]))
				{
					$start_permanent = "20".$exp[2]."-".$bulan[$exp[1]]."-".$exp[0];
				}
				else $start_permanent = "0000-00-00";
				
				$exp = explode("-", str_replace(" ","-",$kolom['R']));
				if(isset($exp[2]))
				{
					if($exp[2] > 20) $thn="19".$exp[2];
					else $thn="20".$exp[2];
					$date_of_birth = $thn."-".$bulan[$exp[1]]."-".$exp[0];
				}
				else $date_of_birth = "0000-00-00";
				
				$data = array("nik"=> $kolom['C'], "name"=> $kolom['B'], "id_department"=> $id_department, "id_position"=> $position, "education"=> $kolom['E'],
				"date_hire_since"=> $date_hire_since, "start_permanent"=> $start_permanent, "rekening"=> $kolom['H'], "jamsostek"=> $kolom['I'],
				"sex"=> $kolom['J'], "blood_type"=> str_replace(" ","",$kolom['K']), "id_marrital_status"=> $id_marital_status,
				"employe_status"=> $kolom['M'], "religion"=> $kolom['P'], "place_of_birth"=> $kolom['Q'], "date_of_birth"=> $date_of_birth,
				"address"=> $kolom['S'], "date_start_contract"=> $kolom['N'], "date_end_contract"=> $kolom['O']);
				//print_mz($data);
				$this->db->insert("employee", $data);*/
				$exp = explode("-", str_replace(" ","-",$kolom['N']));
				if(isset($exp[2]))
				{
					if(strlen($exp[0])==1) $exp[0]="0".$exp[0];
					$date_start = "20".$exp[2]."-".$bulan[$exp[1]]."-".$exp[0];
				}
				else $date_start = "";
				
				$exp = explode("-", str_replace(" ","-",$kolom['O']));
				if(isset($exp[2]))
				{
					if(strlen($exp[0])==1) $exp[0]="0".$exp[0];
					$date_end = "20".$exp[2]."-".$bulan[$exp[1]]."-".$exp[0];
				}
				else $date_end = "";
				$name = $kolom['B'];
				if(substr($name,0,1) == " ")
				{
					$name = substr($name,1);
				}
				
				$id = GetValue("id", "employee", array("nik"=> "where/".$kolom['C'], "name"=> "where/".$name));
				if($id && $date_start && $date_end)
				{
					$data_upd = array("date_start_contract"=> $date_start, "date_end_contract"=> $date_end);
					$this->db->where("id", $id);
					$this->db->update("employee", $data_upd);
				}
			}
		}
	}
	}
	
	function plafon_cuti()
	{
		$this->load->library('excel');
		$bulan=array("Jan"=> "01", "Feb"=> "02", "Mar"=> "03", "Apr"=> "04", "May"=> "05", "Jun"=> "06", "Jul"=> "07", "Aug"=> "08",
		"Sep"=> "09", "Oct"=> "10", "Nov"=> "11", "Dec"=> "12");
		$create_date=date("Y-m-d H:i:s");
		$filez = './uploads/Cuti All Section Maret 2014.xls';
		$objPHPExcel = PHPExcel_IOFactory::load($filez);
		$site=4;
		$baris=7;
		$bulan="03";
		$tahun="2014";
		//for($site=3;$site<=12;$site++)
		//{
		$objPHPExcel->setActiveSheetIndex($site);
		$loop=$objPHPExcel->setActiveSheetIndex($site)->getHighestRow();
		$sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
		for($i=$baris;$i<=$loop;$i++)
		{
			$kolom = $sheetData[$i];
			if($kolom['A'])
			{
				$id_employee = GetValue("id", "employee", array("name"=> "where/".$kolom['B']));
				if(!$id_employee)
				{
					$id_employee = $temp_id+1;
					echo "GAGAL ===== ".$kolom['B']." / ".$id_employee."<br>";
				}
				else echo $kolom['B']."<br>";
				
				if(substr($kolom['F'],0,1) == "(") $kolom['F'] = "-".str_replace("(","", str_replace(")","",$kolom['F']));
				$data = array("id_employee"=> $id_employee, "bulan"=> $bulan, "tahun"=> $tahun, "hak_cuti_sebelumnya"=> str_replace(" ","",$kolom['F']), 
				"hak_cuti"=> str_replace(" ","",$kolom['G']), "pengambilan_cuti"=> str_replace(" ","",$kolom['I']));
				$this->db->insert('cuti_platfon', $data);
				$temp_id=$id_employee;
			}
		}
	}
	
	function rekap_tunjangan($site=0)
	{
		$blok=array('E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI');
		
		$this->load->library('excel');
		$filez = './uploads/Rekap Tunjangan April 2014.xls';
		if(file_exists($filez))
		{
			$objPHPExcel = PHPExcel_IOFactory::load($filez);
			$objPHPExcel->setActiveSheetIndex($site);
		//for($k=$shift;$k<=3;$k++)
		//{
			$loop=$objPHPExcel->setActiveSheetIndex($site)->getHighestRow();
			//$sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
			for($i=6;$i<=$loop;$i++)
			{
				//$kolom = $sheetData[$i];
				$nik = str_replace("'","",$objPHPExcel->getActiveSheet()->getCell('B'.$i)->getValue());
				if($nik)
				{
					$nik = substr($nik,0,1)."-".substr($nik,1,4)."-".substr($nik,5,3);
					
					$id_employee = GetValue("id", "employee", array("nik"=> "where/".$nik));
					if(!$id_employee) $id_employee = GetValue("id", "employee", array("nik_old"=> "where/".$nik));
					if($id_employee)
					{
						for($x=0;$x< count($blok);$x++)
						{
							$tgl=date("d", mktime(0, 0, 0, 3, 16+$x, 2014));
							$bln=date("m", mktime(0, 0, 0, 3, 16+$x, 2014));
							$thn=2014;
							$create_date = "2014-".$bln."-".$tgl." 00:00:00";
							$absen = strtoupper($objPHPExcel->getActiveSheet()->getCell($blok[$x].$i)->getValue());
							
							$field="";
							if($absen == "O") $field="off=1";
							else if($absen == "H") $field="jh=1";
							else if($absen == "OT") $field="jh=1";
							else if($absen == "T") $field="terlambat=1, jh=1";
							else if($absen == "A") $field="alpa=1";
							else if($absen == "S") $field="sakit=1";
							else if($absen == "C") $field="cuti=1";
							else if($absen == "D") $field="ijin=1";
							else if($absen == "PG") $field="potong_gaji=1";
							else if($absen == "OP") $field="opname=1";
							else if($absen == "S2") $field="opname_istirahat=1";
							else if($absen == "KK") $field="kecelakaan_kerja=1";
							
							if(!$field) die($x."-".$i." / ".$absen." / ".$nik." / ".$objPHPExcel->getActiveSheet()->getCell('C'.$i)->getValue());
							//$data = array("id_employee"=> $id_employee, "jhk"=> 1, $field=> 1, "tanggal"=> $tgl, "bulan"=> $bln, "tahun"=> $thn,
							//			"create_date"=> $create_date);
							//$this->db->insert("kehadirandetil_april", $data);
							$sql = "insert into kg_kehadirandetil_april set id_employee='".$id_employee."', jhk=1, ".$field.", tanggal='".$tgl."',
							bulan='".$bln."', tahun='".$thn."', create_date='".$create_date."'";
							echo $sql.";<br>";
						}
					}
					else echo $nik." / ".$objPHPExcel->getActiveSheet()->getCell('C'.$i)->getValue()."<br>";
					//die();
				}
				//echo $nik." / ".$kolom['C']."<br>";
			}
		}
		die();	
	}
	
	function rekap_overtime($site=0)
	{
		$blok=array('D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH');
		$field=array("ot_allow_shift","acc_allow_shift","ot_incidental","acc_ot_incidental","ot_cont_allow","acc_ot_cont_allow");
		
		$this->load->library('excel');
		$filez = './uploads/Back up Gaji 04 14.xls';
		if(file_exists($filez))
		{
			$objPHPExcel = PHPExcel_IOFactory::load($filez);
			$objPHPExcel->setActiveSheetIndex($site);
		
			$loop=$objPHPExcel->setActiveSheetIndex($site)->getHighestRow();
			
			$sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
			for($i=8;$i<=$loop;$i+=7)
			{
				$kolom = $sheetData[$i];
				//$urut = $objPHPExcel->getActiveSheet()->getCell('A'.$i)->getValue() + 7;
				$urut = str_replace(" ","",$kolom['A'])+7;
				if($urut > 7)
				{
					$nik = str_replace("'","",$objPHPExcel->getActiveSheet()->getCell('AO'.$urut)->getValue());
					if($nik)
					{
						$nik = substr($nik,0,1)."-".substr($nik,1,4)."-".substr($nik,5,3);
						
						$id_employee = GetValue("id", "employee", array("nik"=> "where/".$nik));
						if(!$id_employee) $id_employee = GetValue("id", "employee", array("nik_old"=> "where/".$nik));
						//echo $i." = ".$nik." / ".$id_employee."<br>";
						if($id_employee)
						{
							for($x=0;$x< count($blok);$x++)
							{
								$tgl=date("d", mktime(0, 0, 0, 3, 16+$x, 2014));
								$bln=date("m", mktime(0, 0, 0, 3, 16+$x, 2014));
								$thn=2014;
								$upd = "";
								for($k=($i+1);$k<=$i+6;$k++)
								{
									$col = $sheetData[$k];
									//$upd .= $field[$k-($i+1)]."='".$objPHPExcel->getActiveSheet()->getCell($blok[$x].$k)->getValue()."', ";
									$upd .= $field[$k-($i+1)]."='".$col[$blok[$x]]."', ";
								}
								$upd .= "modify_date='".date("Y-m-d H:i:s")."'";
								$sql = "update kg_kehadirandetil_april set ".$upd." where id_employee='".$id_employee."' AND tanggal='".$tgl."' AND 
								bulan='".$bln."' AND tahun='2014'";
								echo $sql.";<br>";
							}
							echo "<br><br>";
						}
					}
					else echo $i." = ".$nik." / ".$id_employee."<br>";
				}
			}
		}
	}
	
	function rekap_overtime_finance($site=0)
	{
		$blok=array('D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH');
		$field=array("ot_incidental","acc_ot_incidental","ot_cont_allow","acc_ot_cont_allow");
		$lompatan=5;
		$this->load->library('excel');
		$filez = './uploads/Back up Gaji 04 14.xls';
		if(file_exists($filez))
		{
			$objPHPExcel = PHPExcel_IOFactory::load($filez);
			$objPHPExcel->setActiveSheetIndex($site);
		
			$loop=$objPHPExcel->setActiveSheetIndex($site)->getHighestRow();
			
			$sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
			for($i=8;$i<=$loop;$i+=$lompatan)
			{
				$kolom = $sheetData[$i];
				//$urut = $objPHPExcel->getActiveSheet()->getCell('A'.$i)->getValue() + $lompatan;
				$urut = str_replace(" ","",$kolom['A'])+7;
				if($urut > $lompatan)
				{
					$nik = str_replace("'","",$objPHPExcel->getActiveSheet()->getCell('AM'.$urut)->getValue());
					if($nik)
					{
						$nik = substr($nik,0,1)."-".substr($nik,1,4)."-".substr($nik,5,3);
						
						$id_employee = GetValue("id", "employee", array("nik"=> "where/".$nik));
						if(!$id_employee) $id_employee = GetValue("id", "employee", array("nik_old"=> "where/".$nik));
						//echo $i." = ".$nik." / ".$id_employee."<br>";
						if($id_employee)
						{
							for($x=0;$x< count($blok);$x++)
							{
								$tgl=date("d", mktime(0, 0, 0, 3, 16+$x, 2014));
								$bln=date("m", mktime(0, 0, 0, 3, 16+$x, 2014));
								$thn=2014;
								$upd = "";
								for($k=($i+1);$k<=$i+($lompatan-1);$k++)
								{
									$col = $sheetData[$k];
									//$upd .= $field[$k-($i+1)]."='".$objPHPExcel->getActiveSheet()->getCell($blok[$x].$k)->getValue()."', ";
									$upd .= $field[$k-($i+1)]."='".$col[$blok[$x]]."', ";
								}
								$upd .= "modify_date='".date("Y-m-d H:i:s")."'";
								$sql = "update kg_kehadirandetil_april set ".$upd." where id_employee='".$id_employee."' AND tanggal='".$tgl."' AND 
								bulan='".$bln."' AND tahun='2014'";
								echo $sql.";<br>";
							}
							echo "<br><br>";
						}
					}
					else echo $i." = ".$nik." / ".$id_employee."<br>";
				}
			}
		}
	}
	
	function overtime($masuk="", $keluar="", $shift="")
	{
		if($masuk && $keluar) {
			$hour = floor((strtotime($keluar) - strtotime($masuk)) / 3600);
			if($hour < 0)
			{
				$hour = 24 + $hour;
				$minute = ceil((((strtotime($keluar) + 86400) - strtotime($masuk)) - (3600 * $hour) ) / 60);
			}
			else $minute = ceil(((strtotime($keluar) - strtotime($masuk)) - (3600 * $hour) ) / 60);
			
			$cek_sabtu = date("w", strtotime(date("Y-m-d")));
			//lembur
			if($cek_sabtu == 6 && $shift == "NS") {
				if(/* ($hour >= 6 && $minute >= 55) || */ $hour > 6) {
					//if($hour >= 6 && $minute >= 55) $jam = $hour-5;
					//else $jam = $hour-6;
					$jam = $hour-6;
					return $jam;
				}
				else return 0;
			}
			else if(/* ($hour >= 8 && $minute >= 55) || */ $hour > 8) {
				//if($hour >= 8 && $minute >= 55) $jam = $hour-7;
				//else $jam = $hour-8;
				$jam = $hour-8;
				return $jam;
			}
			else return 0;
		}
	}
	
	function baca_ot($tgl_awal="", $tgl_akhir="")
	{
		if($tgl_awal && $tgl_akhir) {
			$q = GetAll("view_kehadiran", array("scan_masuk !="=> "where/", "date_full >="=> "where/".$tgl_awal, "date_full <="=> "where/".$tgl_akhir));
			foreach($q->result_array() as $r)
			{
				$tgl = $r['tanggal'];
				$bln = $r['bulan'];
				$thn = $r['tahun'];
				$id_employee = $r['id_employee'];
				$jadwal = GetValue("tgl_".intval($tgl), "jadwal_shift", array("bulan"=> "where/".$bln, "tahun"=> "where/".$thn, "id_employee"=> "where/".$id_employee));
				$overtime = $this->overtime($r['scan_masuk'], $r['scan_pulang'], strtoupper($jadwal));
				if($overtime) {
					$data['lembur'] = 1;
					$data['alasan_lembur'] = 1;
					$data['ot_incidental'] = $overtime;
					//$sql = "update kg_kehadirandetil set lembur='1', alasan_lembur='1', ot_incidental='".$overtime."' where id='".$r['id']."';";
					//echo $sql."<br>";
					$this->db->where("id", $r['id']);
					$this->db->update("kehadirandetil", $data);
				}
			}
		} else die("Tanggal awal & akhir harus ada");
	}
	
	function baca_absen_bulan($tglz=NULL, $blnz=NULL, $thnz=NULL)
	{
		$create_date=date("Y-m-d H:i:s");
		$path=str_replace("localhost", "127.0.0.1", base_url());
		
		$emp=$flag_emp=array();
		$q=GetAll("hris_persons", array("ext_id !="=> "where/00000000", "group_shift !="=> "where/NULL", "status_cd"=> "where/normal"));
		foreach($q->result_array() as $r) {
			$emp[$r['ext_id']] = $r['person_id'];
		}
		
		//Ambil dari DB
		//$file = "./device/DATATKS.SDF";
		$file = "./device/fam/BACKUPDATA/".$tglz;
		if(file_exists($file)) {
			$linesz = filesize($file);
			if($linesz > 0)
			{
				$open = fopen($file, "r");
				$baca = fread($open, $linesz);
				$exp = explode("\n", $baca);
				
				$exp = array_filter($exp);
				
				$absen=array();
				foreach($exp as $r) {
					$datez=substr($r,0,8);
					$jamz=substr($r,8,4);
					$nikz=substr($r,12,8);
					$in_outz=substr($r,20,1);
					$absen[$datez][$nikz][$in_outz] = $jamz;
				}
				//print_mz($absen);
				
				
				//print_mz($emp);
				$create_date=date("Y-m-d H:i:s");
				foreach($absen as $day=> $arr_nik) {
					$tgl=substr($day,6,2);
					$bln=substr($day,4,2);
					$thn=substr($day,0,4);
					foreach($arr_nik as $nik=> $arr_in_out) {
						$m=0;
						foreach($arr_in_out as $in_out=> $jam) {
							$nik=intval($nik);
							$m++;
							$jam = substr($jam,0,2).":".substr($jam,2,2);
							if(isset($emp[$nik])) {
								//Menandakan bahwa karyawan absen
								$flag_emp[$day][$nik]=1;
								
								if($in_out==1) {
									$data = array("id_employee"=> $emp[$nik], "jhk"=> 1, "jh"=> 1, "alpa"=> 0, "off"=> 0, "tanggal"=> $tgl, "bulan"=> $bln, "tahun"=> $thn,
									"scan_masuk"=> $jam, "create_date"=> $create_date);
									$cek_absen = GetValue("id", "kehadirandetil", array("id_employee"=> "where/".$emp[$nik], "tanggal"=> "where/".$tgl, "bulan"=> "where/".$bln, "tahun"=> "where/".$thn));
									if(!$cek_absen) {
										$this->db->insert("kehadirandetil", $data);
									} else {
										$this->db->where("id_employee", $emp[$nik]);
										$this->db->where("tanggal", $tgl);
										$this->db->where("bulan", $bln);
										$this->db->where("tahun", $thn);
										$this->db->update("kehadirandetil", $data);
									}
								} else if($m==2 && $in_out==0) {
									$data = array("scan_pulang"=> $jam);
									$this->db->where("id_employee", $emp[$nik]);
									$this->db->where("tanggal", $tgl);
									$this->db->where("bulan", $bln);
									$this->db->where("tahun", $thn);
									$this->db->update("kehadirandetil", $data);
								} else if($m==1 && $in_out==0) {
									$exp = explode("-", date("Y-m-d", mktime(0, 0, 0, $bln, $tgl-1, $thn)));
									$cek_kemarin = GetValue("id", "kehadirandetil", array("id_employee"=> "where/".$emp[$nik], "tanggal"=> "where/".$exp[2], "bulan"=> "where/".$exp[1], "tahun"=> "where/".$exp[0]));
									if($cek_kemarin) {
										$data = array("scan_pulang"=> $jam);
										$this->db->where("id_employee", $emp[$nik]);
										$this->db->where("tanggal", $exp[2]);
										$this->db->where("bulan", $exp[1]);
										$this->db->where("tahun", $exp[0]);
										$this->db->update("kehadirandetil", $data);
									}
								}
							}
						}
					}
					//print_mz($flag_emp);
					/*foreach($emp as $nik=> $r) {
						if(!isset($flag_emp[$day][$nik])) {
							$cek_absen = GetValue("id", "kehadirandetil", array("id_employee"=> "where/".$emp[$nik], "tanggal"=> "where/".$tgl, "bulan"=> "where/".$bln, "tahun"=> "where/".$thn));
							if(!$cek_absen) {
								$data = array("id_employee"=> $emp[$nik], "jhk"=> 1, "alpa"=> 1, "tanggal"=> $tgl, "bulan"=> $bln, "tahun"=> $thn,
								"create_date"=> $create_date);
								$this->db->insert("kehadirandetil", $data);
							}
						}
					}*/
				}
			}
		}
		
		//Ambil dari DB
		//$file = "./device/DATATKS.SDF";
		$file = "./device/jho/BACKUPDATA/".$tglz;
		if(file_exists($file)) {
			$linesz = filesize($file);
			if($linesz > 0)
			{
				$open = fopen($file, "r");
				$baca = fread($open, $linesz);
				$exp = explode("\n", $baca);
			
				$exp = array_filter($exp);
			
				$absen=array();
				foreach($exp as $r) {
					$datez=substr($r,0,8);
					$jamz=substr($r,8,4);
					$nikz=substr($r,12,8);
					$in_outz=substr($r,20,1);
					$absen[$datez][$nikz][$in_outz] = $jamz;
				}
				//print_mz($absen);
				
				
				//print_mz($emp);
				$create_date=date("Y-m-d H:i:s");
				foreach($absen as $day=> $arr_nik) {
					$tgl=substr($day,6,2);
					$bln=substr($day,4,2);
					$thn=substr($day,0,4);
					foreach($arr_nik as $nik=> $arr_in_out) {
						$m=0;
						foreach($arr_in_out as $in_out=> $jam) {
							$nik=intval($nik);
							$m++;
							$jam = substr($jam,0,2).":".substr($jam,2,2);
							//die($jam."S");
							if(isset($emp[$nik])) {
								//Menandakan bahwa karyawan absen
								$flag_emp[$day][$nik]=1;
								
								if($in_out==1) {
									$data = array("id_employee"=> $emp[$nik], "jhk"=> 1, "jh"=> 1, "alpa"=> 0, "off"=> 0, "tanggal"=> $tgl, "bulan"=> $bln, "tahun"=> $thn,
									"scan_masuk"=> $jam, "create_date"=> $create_date);
									$cek_absen = GetValue("id", "kehadirandetil", array("id_employee"=> "where/".$emp[$nik], "tanggal"=> "where/".$tgl, "bulan"=> "where/".$bln, "tahun"=> "where/".$thn));
									if(!$cek_absen) {
										$this->db->insert("kehadirandetil", $data);
									} else {
										$this->db->where("id_employee", $emp[$nik]);
										$this->db->where("tanggal", $tgl);
										$this->db->where("bulan", $bln);
										$this->db->where("tahun", $thn);
										$this->db->update("kehadirandetil", $data);
									}
								} else if($m==2 && $in_out==0) {
									$data = array("scan_pulang"=> $jam);
									$this->db->where("id_employee", $emp[$nik]);
									$this->db->where("tanggal", $tgl);
									$this->db->where("bulan", $bln);
									$this->db->where("tahun", $thn);
									$this->db->update("kehadirandetil", $data);
								} else if($m==1 && $in_out==0) {
									$exp = explode("-", date("Y-m-d", mktime(0, 0, 0, $bln, $tgl-1, $thn)));
									$cek_kemarin = GetValue("id", "kehadirandetil", array("id_employee"=> "where/".$emp[$nik], "tanggal"=> "where/".$exp[2], "bulan"=> "where/".$exp[1], "tahun"=> "where/".$exp[0]));
									if($cek_kemarin) {
										$data = array("scan_pulang"=> $jam);
										$this->db->where("id_employee", $emp[$nik]);
										$this->db->where("tanggal", $exp[2]);
										$this->db->where("bulan", $exp[1]);
										$this->db->where("tahun", $exp[0]);
										$this->db->update("kehadirandetil", $data);
									}
								}
							}
						}
					}
					//print_mz($flag_emp);
					/*foreach($emp as $nik=> $r) {
						if(!isset($flag_emp[$day][$nik])) {
							$cek_absen = GetValue("id", "kehadirandetil", array("id_employee"=> "where/".$emp[$nik], "tanggal"=> "where/".$tgl, "bulan"=> "where/".$bln, "tahun"=> "where/".$thn));
							if(!$cek_absen) {
								$data = array("id_employee"=> $emp[$nik], "jhk"=> 1, "alpa"=> 1, "tanggal"=> $tgl, "bulan"=> $bln, "tahun"=> $thn,
								"create_date"=> $create_date);
								$this->db->insert("kehadirandetil", $data);
							}
						}
					}*/
				}
			}
		}
		die("Success");
	}
	
	function xx()
	{
		$emp=$flag_emp=array();
		$q=GetAll("hris_employee");
		foreach($q->result_array() as $r) {
			$emp[$r['employee_ext_id']] = $r['person_id'];
		}
		
		//Ambil dari DB
		$file = "./device/DATATKS - Copy.SDF";
		//$file = "./device/jho/BACKUPDATA/".$tglz;
		if(file_exists($file)) {
			$linesz = filesize($file);
			if($linesz > 0)
			{
				$open = fopen($file, "r");
				$baca = fread($open, $linesz);
				$exp = explode("\n", $baca);
			
				$exp = array_filter($exp);
			
				$absen=array();
				foreach($exp as $r) {
					$datez=substr($r,0,8);
					$jamz=substr($r,8,4);
					$nikz=substr($r,12,8);
					$in_outz=substr($r,20,1);
					$absen[$datez][$nikz][$in_outz] = $jamz;
				}
				//print_mz($absen);
				
				
				//print_mz($emp);
				$create_date=date("Y-m-d H:i:s");
				foreach($absen as $day=> $arr_nik) {
					$tgl=substr($day,6,2);
					$bln=substr($day,4,2);
					$thn=substr($day,0,4);
					foreach($arr_nik as $nik=> $arr_in_out) {
						$m=0;
						foreach($arr_in_out as $in_out=> $jam) {
							$nik=intval($nik);
							$m++;
							$jam = substr($jam,0,2).":".substr($jam,2,2);
							//die($jam."S");
							if(isset($emp[$nik])) {
								//Menandakan bahwa karyawan absen
								$flag_emp[$day][$nik]=1;
								
								if($in_out==1) {
									$data = array("id_employee"=> $emp[$nik], "jhk"=> 1, "jh"=> 1, "alpa"=> 0, "off"=> 0, "tanggal"=> $tgl, "bulan"=> $bln, "tahun"=> $thn,
									"scan_masuk"=> $jam, "create_date"=> $create_date);
									$cek_absen = GetValue("id", "kehadirandetil", array("id_employee"=> "where/".$emp[$nik], "tanggal"=> "where/".$tgl, "bulan"=> "where/".$bln, "tahun"=> "where/".$thn));
									if(!$cek_absen) {
										$this->db->insert("kehadirandetil", $data);
									} else {
										$this->db->where("id_employee", $emp[$nik]);
										$this->db->where("tanggal", $tgl);
										$this->db->where("bulan", $bln);
										$this->db->where("tahun", $thn);
										$this->db->update("kehadirandetil", $data);
									}
								} else if($m==2 && $in_out==0) {
									$data = array("scan_pulang"=> $jam);
									$this->db->where("id_employee", $emp[$nik]);
									$this->db->where("tanggal", $tgl);
									$this->db->where("bulan", $bln);
									$this->db->where("tahun", $thn);
									$this->db->update("kehadirandetil", $data);
								} else if($m==1 && $in_out==0) {
									$exp = explode("-", date("Y-m-d", mktime(0, 0, 0, $bln, $tgl-1, $thn)));
									$cek_kemarin = GetValue("id", "kehadirandetil", array("id_employee"=> "where/".$emp[$nik], "tanggal"=> "where/".$exp[2], "bulan"=> "where/".$exp[1], "tahun"=> "where/".$exp[0]));
									if($cek_kemarin) {
										$data = array("scan_pulang"=> $jam);
										$this->db->where("id_employee", $emp[$nik]);
										$this->db->where("tanggal", $exp[2]);
										$this->db->where("bulan", $exp[1]);
										$this->db->where("tahun", $exp[0]);
										$this->db->update("kehadirandetil", $data);
									}
								}
							}
						}
					}
					//print_mz($flag_emp);
					/*foreach($emp as $nik=> $r) {
						if(!isset($flag_emp[$day][$nik])) {
							$cek_absen = GetValue("id", "kehadirandetil", array("id_employee"=> "where/".$emp[$nik], "tanggal"=> "where/".$tgl, "bulan"=> "where/".$bln, "tahun"=> "where/".$thn));
							if(!$cek_absen) {
								$data = array("id_employee"=> $emp[$nik], "jhk"=> 1, "alpa"=> 1, "tanggal"=> $tgl, "bulan"=> $bln, "tahun"=> $thn,
								"create_date"=> $create_date);
								$this->db->insert("kehadirandetil", $data);
							}
						}
					}*/
				}
			}
		}
	}
	
	function baca_cron_bulan($tglz=NULL, $blnz=NULL, $thnz=NULL)
	{
		$create_date=date("Y-m-d H:i:s");
		
		$loop=$tglz;
		for($b=1;$b<=$loop;$b++) {
			$day = date("w", mktime(1, 1, 1, $blnz, $b, $thnz));
			if($day != 0 && $day != 6) {
				$tglz=$b;
				if(strlen($tglz)==1) $tglz="0".$tglz;
				$temp=$temp_key="";
				$absen=array();
				$sql = GetJoin("employee", "kehadiran_history", "kg_employee.id=kg_kehadiran_history.id_employee AND DAY(scan)='".$tglz."' AND MONTH(scan)='".$blnz."' AND YEAR(scan)='".$thnz."'", "left", "kg_employee.id as id_emp, kg_kehadiran_history.*", array("id_emp"=> "order/asc", "scan"=> "order/asc"));
				foreach($sql->result_array() as $r) {
					$cardno=$r['id_emp'];
					if($temp != $cardno)
					{
						if($temp) $absen[$temp_key]['keluar'] = $last_data;
						$absen[$cardno]['masuk'] = substr($r['scan'],11);
						$temp = $cardno;
						$temp_key=$cardno;
					}
					$last_data= substr($r['scan'],11);
				}
				if($temp) $absen[$cardno]['keluar'] = $last_data;
				
				//print_mz($absen);
				foreach($absen as $id_employee=> $r)
				{
					$masuk=$r['masuk'];
					$keluar=$r['keluar'];
					if($masuk==$keluar) $keluar="";
					$cek_hadir = GetValue("id", "kehadirandetil", array("id_employee"=> "where/".$id_employee, "tanggal"=> "where/".$tglz, "bulan"=> "where/".$blnz, "tahun"=> "where/".$thnz));
					if(!$cek_hadir) {
						$jh=$alpa=$telat=0;
						//$jadwal = strtoupper(GetValue("tgl_".intval($tglz), "jadwal_shift", array("bulan"=> "where/".$blnz, "tahun"=> "where/".$thnz, "id_employee"=> "where/".$id_employee)));
						if($masuk) {
							$cek_jam=intval(substr($masuk,0,2));
							$cek_menit=substr($masuk,3,2);
							$jam_menit = $cek_jam.$cek_menit;
							$jh=1;
							//if(($jadwal) == "NS" && $jam_menit >= 901) $telat=1;
							//else 
							if($jam_menit >= 901) $telat=1;
						} else {
							$alpa=1;
						}
						
						$data = array("id_employee"=> $id_employee, "jhk"=> 1, "jh"=> $jh, "alpa"=> $alpa, "terlambat"=> $telat, "tanggal"=> $tglz, "bulan"=> $blnz, "tahun"=> $thnz,
						"scan_masuk"=> $masuk, "scan_pulang"=> $keluar, "create_date"=> $create_date);
						$this->db->insert("kehadirandetil", $data);
						echo $this->db->last_query().";<br>";
					}
				}
			}
		}
	}
	
	function group_shift()
	{
		$q = GetAll("employee");
		foreach($q->result_array() as $r) {
			if($r['emp_no']) {
				$data = array("group_shift"=> $r['group_shift'], "grade"=> $r['grade']);
				$this->db->where("ext_id", $r['emp_no']);
				$this->db->update("hris_persons", $data);
			}
		}
	}
	
	function exe_shift($thn=2016) 
	{
		$webmaster_id = permission();
		$grup=array("N.A.", "A", "B", "C", "D");
		$bln=array("01","02","03","04","05","06","07","08","09","10","11","12");
		$jadwal_grup['N.A.'] = array("reg","off","off","reg","reg","reg","reg","reg","off","off","reg","reg","reg","reg","reg","off","off","reg","reg","reg","reg","reg","off","off","reg","reg","reg","reg");
		$jadwal_grup['A'] = array(1,2,2,2,"off",3,3,"off",1,1,1,2,2,"off",3,3,"off","off",1,1,2,2,"off",3,3,3,"off",1);
		$jadwal_grup['B'] = array("off",1,1,1,2,2,"off",3,3,"off","off",1,1,2,2,"off",3,3,3,"off",1,1,2,2,2,"off",3,3);
		$jadwal_grup['C'] = array(3,3,"off","off",1,1,2,2,"off",3,3,3,"off",1,1,2,2,2,"off",3,3,"off",1,1,1,2,2,"off");
		$jadwal_grup['D'] = array(2,"off",3,3,3,"off",1,1,2,2,2,"off",3,3,"off",1,1,1,2,2,"off",3,3,"off","off",1,1,2);
		
		
		foreach($grup as $g) {
			//$emp = GetAll("kg_hris_employee", array("employee_grup"=> "where/".$g));
			$emp = GetAll("hris_persons", array("status_cd"=> "where/normal", "group_shift"=> "where/".$g));
			foreach($emp->result_array() as $r) {
				$id_employee=$r['person_id'];
				$loop=0;
				foreach($bln as $b) {
					$datanginput=array('id_employee'=> $id_employee, 'bulan'=> $b, 'tahun'=> $thn);
					$jml_hari = date("t", mktime(0, 0, 0, $b, 1, $thn));
					for($i=1;$i<=$jml_hari;$i++) {
						if($loop==28) $loop=0;
						$datanginput['tgl_'.$i]=$jadwal_grup[$g][$loop];
						$loop++;
					}
					
					$temp_datahitung = $datanginput;
					unset($temp_datahitung['bulan']);
					unset($temp_datahitung['tahun']);
					unset($temp_datahitung['id_employee']);
					
					$hitung=array_count_values($temp_datahitung);
					//print_mz($hitung);
					if(!isset($hitung['1'])) $hitung['1']=0;
					if(!isset($hitung['2'])) $hitung['2']=0;
					if(!isset($hitung['3'])) $hitung['3']=0;
					if(!isset($hitung['reg'])) $hitung['reg']=0;
					if(!isset($hitung['off'])) $hitung['off']=0;
						
					$datanginput['jum_p']=$hitung['1'];
					$datanginput['jum_s']=$hitung['2'];
					$datanginput['jum_m']=$hitung['3'];
					$datanginput['jum_ns']=$hitung['reg'];
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
					}
				}
			}
		}
	}
	
	function exe_shift_2015($thn=2015) 
	{
		$webmaster_id = permission();
		$grup=array("N.A.", "A", "B", "C", "D");
		$bln=array("01","02","03","04","05","06","07","08","09","10","11","12");
		//$bln=array("12","11","10");//,"09","08","07","06","05","04","03","02","01");
		$jadwal_grup['N.A.'] = array("reg","reg","off","off","reg","reg","reg","reg","reg","off","off","reg","reg","reg","reg","reg","off","off","reg","reg","reg","reg","reg","off","off","reg","reg","reg");
		$jadwal_grup['A'] = array(1,1,2,2,2,"off",3,3,"off",1,1,1,2,2,"off",3,3,"off","off",1,1,2,2,"off",3,3,3,"off");
		$jadwal_grup['B'] = array(3,"off",1,1,1,2,2,"off",3,3,"off","off",1,1,2,2,"off",3,3,3,"off",1,1,2,2,2,"off",3);
		$jadwal_grup['C'] = array("off",3,3,"off","off",1,1,2,2,"off",3,3,3,"off",1,1,2,2,2,"off",3,3,"off",1,1,1,2,2);
		$jadwal_grup['D'] = array(2,2,"off",3,3,3,"off",1,1,2,2,2,"off",3,3,"off",1,1,1,2,2,"off",3,3,"off","off",1,1);
		/*$jadwal_grup['A'] = array("p","s","s","s","off","m","m","off","p","p","p","s","s","off","m","m","off","off","p","p","s","s","off","m","m","m","off","p");
		$jadwal_grup['B'] = array("off","p","p","p","s","s","off","m","m","off","off","p","p","s","s","off","m","m","m","off","p","p","s","s","s","off","m","m");
		$jadwal_grup['C'] = array("m","m","off","off","p","p","s","s","off","m","m","m","off","p","p","s","s","s","off","m","m","off","p","p","p","s","s","off");
		$jadwal_grup['D'] = array("s","off","m","m","m","off","p","p","s","s","s","off","m","m","off","p","p","p","s","s","off","m","m","off","off","p","p","s");*/
		
		foreach($grup as $g) {
			$emp = GetAll("hris_persons", array("status_cd"=> "where/normal", "group_shift"=> "where/".$g));
			foreach($emp->result_array() as $r) {
				$id_employee=$r['person_id'];
				$loop=0;
				foreach($bln as $b) {
					$datanginput=array('id_employee'=> $id_employee, 'bulan'=> $b, 'tahun'=> $thn);
					$jml_hari = date("t", mktime(0, 0, 0, $b, 1, $thn));
					for($i=1;$i<=$jml_hari;$i++) {
						if($loop==28) $loop=0;
						$datanginput['tgl_'.$i]=$jadwal_grup[$g][$loop];
						$loop++;
					}
					
					$temp_datahitung = $datanginput;
					unset($temp_datahitung['bulan']);
					unset($temp_datahitung['tahun']);
					unset($temp_datahitung['id_employee']);
					
					$hitung=array_count_values($temp_datahitung);
					//print_mz($hitung);
					if(!isset($hitung['1'])) $hitung['1']=0;
					if(!isset($hitung['2'])) $hitung['2']=0;
					if(!isset($hitung['3'])) $hitung['3']=0;
					if(!isset($hitung['reg'])) $hitung['reg']=0;
					if(!isset($hitung['off'])) $hitung['off']=0;
						
					$datanginput['jum_p']=$hitung['1'];
					$datanginput['jum_s']=$hitung['2'];
					$datanginput['jum_m']=$hitung['3'];
					$datanginput['jum_ns']=$hitung['reg'];
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
					}
				}
			}
		}
	}
	
	function otherz() 
	{
		$q = GetAll("kg_kehadirandetil");
		foreach($q->result_array() as $r) {
			if($r['acc_ot_incidental'] > 0) {
				$this->db->where("id", $r['id']);
				$this->db->update("kg_kehadirandetil", array("lembur"=> 1));
			}
		}
	}
	
	function sync_lembur() 
	{
		$q = GetAll("kg_kehadirandetil_asli");
		foreach($q->result_array() as $r) {
			$this->db->where("id_employee", $r['id_employee']);
			$this->db->where("tanggal", $r['tanggal']);
			$this->db->where("bulan", $r['bulan']);
			$this->db->where("tahun", $r['tahun']);
			$this->db->update("kg_kehadirandetil", array("lembur"=> $r['lembur'], "acc_ot_incidental"=> $r['acc_ot_incidental'], "ovt_flag"=> $r['ovt_flag'], "ovt_reason"=> $r['ovt_reason'], "ovt_detail_reason"=> $r['ovt_detail_reason']));
		}
	}
	
	function baca_all_data()
	{
		$create_date=date("Y-m-d H:i:s");
		$path=str_replace("localhost", "127.0.0.1", base_url());
		
		$emp=$flag_emp=array();
		$q=GetAll("hris_persons", array("status_cd"=> "where/normal"));
		//die($q->num_rows()."S");
		foreach($q->result_array() as $r) {
			if(!isset($emp[$r['ext_id']])) $emp[$r['ext_id']] = $r['person_id'];
		}
		//print_mz($emp);
		
		if($handle = opendir("./device/fam/BACKUPDATA/")) {
			$baca="";
	    while (false !== ($entry = readdir($handle))) {
        if ($entry != "." && $entry != "..") {
        	$tglz = $entry;
					$file = "./device/fam/BACKUPDATA/".$tglz;
					if(file_exists($file)) {
						$linesz = filesize($file);
						if($linesz > 0)
						{
							$open = fopen($file, "r");
							$baca .= fread($open, $linesz);
						}
					}
					
					$file = "./device/jho/BACKUPDATA/".$tglz;
					if(file_exists($file)) {
						$linesz = filesize($file);
						if($linesz > 0)
						{
							$open = fopen($file, "r");
							$baca .= fread($open, $linesz);
						}
					}
        }
	    }
	    closedir($handle);
	    //die($baca);
	    $file = "./device/DATATKS.SDF";
			if(file_exists($file)) {
				$linesz = filesize($file);
				if($linesz > 0)
				{
					$open = fopen($file, "r");
					$baca .= fread($open, $linesz);
				}
			}
	    $exp = explode("\n", $baca);		
			$exp = array_filter($exp);
										
			$absen=array();
			foreach($exp as $r) {
				$datez=substr($r,0,8);
				$jamz=substr($r,8,4);
				$nikz=substr($r,12,8);
				$in_outz=substr($r,20,1);
				$absen[$datez][$nikz][$in_outz] = $jamz;
			}
			//print_mz($absen);
			
			
			//print_mz($emp);
			$create_date=date("Y-m-d H:i:s");
			$period = explode("~", GetPeriod("Dec 2015"));
			
			for($d=strtotime($period[0]);$d<=strtotime($period[1]);$d+=86400) {
				$new_tgl = date("Ymd", $d);
				//echo $new_tgl."<br>";
				if(isset($absen[$new_tgl])) {
					foreach($absen[$new_tgl] as $nik=> $arr_in_out) {
						$day=$new_tgl;
						
						//if($day >= "20151201" && $day <= "20151215") {
						//if($day >= "20151116" && $day <= "20151130") {
							$tgl=substr($day,6,2);
							$bln=substr($day,4,2);
							$thn=substr($day,0,4);
							//foreach($arr_nik as $nik=> $arr_in_out) {
								$m=0;
								//if($nik=="09503612" || $nik=="09304218") {
								foreach($arr_in_out as $in_out=> $jam) {
									$m++;
									$jam = substr($jam,0,2).":".substr($jam,2,2);
									if(isset($emp[$nik])) {
										//Menandakan bahwa karyawan absen
										//$flag_emp[$day][$nik]=1;
										
										if($in_out==1) {
											$data = array("id_employee"=> $emp[$nik], "jhk"=> 1, "jh"=> 1, "alpa"=> 0, "off"=> 0, "tanggal"=> $tgl, "bulan"=> $bln, "tahun"=> $thn,
											"scan_masuk"=> $jam, "create_date"=> $create_date);
											
											//Cek Late
											$cek_jadwal = GetValue("tgl_".intval($tgl), "kg_jadwal_shift", array("id_employee"=> "where/".$emp[$nik], "bulan"=> "where/".$bln, "tahun"=> "where/".$thn));
											$data['late'] = $this->cek_late($cek_jadwal, $jam);
											
											$cek_absen = GetValue("id", "kg_kehadirandetil", array("id_employee"=> "where/".$emp[$nik], "tanggal"=> "where/".$tgl, "bulan"=> "where/".$bln, "tahun"=> "where/".$thn));
											if(!$cek_absen) {
												$this->db->insert("kg_kehadirandetil", $data);
											} else {
												$this->db->where("id_employee", $emp[$nik]);
												$this->db->where("tanggal", $tgl);
												$this->db->where("bulan", $bln);
												$this->db->where("tahun", $thn);
												$this->db->update("kg_kehadirandetil", $data);
											}
										} else if($m==2 && $in_out==0) {
											$data = array("scan_pulang"=> $jam);
											$this->db->where("id_employee", $emp[$nik]);
											$this->db->where("tanggal", $tgl);
											$this->db->where("bulan", $bln);
											$this->db->where("tahun", $thn);
											$this->db->update("kg_kehadirandetil", $data);
										} else if($m==1 && $in_out==0) {
											$exp = explode("-", date("Y-m-d", mktime(0, 0, 0, $bln, $tgl-1, $thn)));
											$cek_malam = GetValue("tgl_".intval($exp[2]), "kg_jadwal_shift", array("id_employee"=> "where/".$emp[$nik], "bulan"=> "where/".$exp[1], "tahun"=> "where/".$exp[0]));
											if($cek_malam == "3") {
												$cek_kemarin = GetValue("id", "kg_kehadirandetil", array("id_employee"=> "where/".$emp[$nik], "tanggal"=> "where/".$exp[2], "bulan"=> "where/".$exp[1], "tahun"=> "where/".$exp[0]));
												if($cek_kemarin) {
													$data = array("scan_pulang"=> $jam);
													$this->db->where("id_employee", $emp[$nik]);
													$this->db->where("tanggal", $exp[2]);
													$this->db->where("bulan", $exp[1]);
													$this->db->where("tahun", $exp[0]);
													$this->db->update("kg_kehadirandetil", $data);
												} else {
													$data = array("id_employee"=> $emp[$nik], "jhk"=> 1, "jh"=> 1, "alpa"=> 0, "off"=> 0, "tanggal"=> $exp[2], "bulan"=> $exp[1], "tahun"=> $exp[0],
													"scan_pulang"=> $jam, "create_date"=> $create_date);
													$this->db->insert("kg_kehadirandetil", $data);
												}
											} else {
												$data = array("id_employee"=> $emp[$nik], "jhk"=> 1, "jh"=> 1, "alpa"=> 0, "off"=> 0, "tanggal"=> $tgl, "bulan"=> $bln, "tahun"=> $thn,
												"scan_pulang"=> $jam, "create_date"=> $create_date);
												$this->db->insert("kg_kehadirandetil", $data);
											}
										}
									}
								}
								//}
							//}
							//print_mz($flag_emp);
						//}
					}
					
					foreach($emp as $nik=> $r) {
						//if($nik=="09503612" || $nik=="09304218") {
							//if(!isset($flag_emp[$day][$nik])) {
								//echo $day."<br>";
								$cek_absen = GetValue("id", "kg_kehadirandetil", array("id_employee"=> "where/".$emp[$nik], "tanggal"=> "where/".$tgl, "bulan"=> "where/".$bln, "tahun"=> "where/".$thn));
								if(!$cek_absen) {
									$cek_off = GetValue("tgl_".intval($tgl), "kg_jadwal_shift", array("id_employee"=> "where/".$emp[$nik], "bulan"=> "where/".$bln, "tahun"=> "where/".$thn));
									if(strtolower($cek_off) != "off") {
										$data = array("id_employee"=> $emp[$nik], "jhk"=> 1, "alpa"=> 1, "tanggal"=> $tgl, "bulan"=> $bln, "tahun"=> $thn,
										"create_date"=> $create_date);
										$this->db->insert("kg_kehadirandetil", $data);
									} else {
										$data = array("id_employee"=> $emp[$nik], "jhk"=> 1, "off"=> 1, "tanggal"=> $tgl, "bulan"=> $bln, "tahun"=> $thn,
										"create_date"=> $create_date);
										$this->db->insert("kg_kehadirandetil", $data);
									}
								}
							//}
						//}
					}
					//die();
				} else die($new_tgl."S");
			}
			//die($entry);
		}
		die("Success");
	}
	
	function cek_late($jadwal, $jam)
	{
		$jam = intval(str_replace(":","",$jam));
		if($jadwal > 0 && $jadwal <= 3) $jam_config = GetConfig("shift_".$jadwal, "shift");
		else $jam_config = GetConfig("reguler_start", "shift");
		$jam_config = str_replace(":","",substr($jam_config, 0, 5));
		if($jam >= $jam_config) $late=1;
		else $late=0;
		
		return $late;
	}
	
	function compare_ovt()
	{
		$ext=array();
		$q = GetAll("kg_overtime_temp");
		foreach($q->result_array() as $r) {
			$exp = explode("-", $r['ovt_date']);
			$dt = $exp[2]."-".GetMonthIndex($exp[1])."-".sprintf("%02d", $exp[0]);
			$id_kehadirandetil = GetValue("id", "kg_view_attendance", array("date_full"=> "where/".$dt, "ext_id"=> "where/".$r['ext_id']));
			if($id_kehadirandetil) {
				//$cek = GetValue("id", "kg_overtime", array("id_kehadirandetil"=> "where/".$id_kehadirandetil));
				//if(!$cek) {
					$ins = array("id_kehadirandetil"=> $id_kehadirandetil,
											"ovt_hour_sum"=> $r['ovt_hour_sum'],
											"ovt_flag"=> $r['ovt_flag'],
											"ovt_reason"=> $r['ovt_reason'],
											"ovt_detail_reason"=> $r['ovt_detail_reason']
											);
											
					$this->db->insert("kg_overtime", $ins);
				//}
			}// else $ext[$r['ext_id']] = $r['ext_id'];
		}
		//print_mz($ext);
	}
}
?>
