
<div class="row column-seperation">
  <div class="col-md-12">
  	<div class="col-md-12">
		    <div class="row">
		        <div class="tiles white col-md-12 no-padding info_header">         
	            <div class="tiles-body">
	            	<?php
	            	foreach($emp->result_array() as $r) {
              	?>
	            	<div class="col-md-2 pull-left">
                  <div class="col-md-12">
                  	<img height="135" src="<?php echo GetPP($r['person_id']);?>" alt="<?php echo $r['person_nm'];?>" title="<?php echo $r['person_nm'];?>">
                  </div>
                </div>
                    
                <div class="col-md-5">
                  <div class="col-md-3">
                      <span class="semi-bold">Name</span>
                  </div>
                  <div class="col-md-9">
                      <span>: <?php echo $r['person_nm'];?></span>
                  </div>
                  <br/><br/>
                  <div class="col-md-3">
                      <span class="semi-bold">Division</span>
                  </div>
                  <div class="col-md-9">
                      <span>: <?php echo GetDivision($r['org_id']);?></span>
                  </div>
                  <br/><br/>
                  <div class="col-md-3">
                      <span class="semi-bold">Section</span>
                  </div>
                  <div class="col-md-9">
                      <span>: <?php echo $r['org_nm'];?></span>
                  </div>
                  <br/><br/>
                  <div class="col-md-3">
                      <span class="semi-bold">Job Title</span>
                  </div>
                  <div class="col-md-9">
                      <span>: <?php echo $r['job_nm'];?></span>
                  </div>
                </div>
                
                <div class="col-md-5">
                  <div class="col-md-3">
                      <span class="semi-bold">NIK</span>
                  </div>
                  <div class="col-md-9">
                      <span>: <?php echo $r['ext_id'];?></span>
                  </div>
                  <br/><br/>
                  <div class="col-md-3">
                      <span class="semi-bold">Group</span>
                  </div>
                  <div class="col-md-9">
                      <span>: <?php echo CekGroup($r['group_shift']);?></span>
                  </div>
                  <br/><br/>
                  <div class="col-md-3">
                      <span class="semi-bold">Grade</span>
                  </div>
                  <div class="col-md-9">
                      <span>: <?php echo $r['grade_job_class'];?></span>
                  </div>
                  <br/><br/>
                  <div class="col-md-3">
                      <span class="semi-bold">Period</span>
                  </div>
                  <div class="col-md-9">
                      <span>: <?php echo $period;?></span>
                  </div>
                </div>
                <?php
                }
                ?>
	            </div>
		        </div>
		    </div>
		</div>
		
		<!--<div class="grid simple transparent">
			<div class="grid-title">
				<h4>Attendance <span class="semi-bold">Detail</span></h4>
				<div class="actions">
					<a href="javascript:void(0);" onclick="backAtt('<?php echo $period;?>')"><i class='fa fa-chevron-circle-left'></i> Back</a>
				</div>
			</div>
		</div>-->

    <div class="grid-body">
    	<form id="form_edit_shift" action="attendance/update_shift" method="post" enctype="multipart/form-data">
    	<?php
    	$person_id = permission();
    	$holiday = GetHoliday();
    	$opt_shift = array("1"=> 1, "2"=> 2, "3"=> 3, "OFF"=> "OFF", "REG"=> "REG");
    	foreach($shift->result_array() as $r) {
    		echo form_hidden("id", isset($r["id"]) ? $r["id"] : 0);
				echo form_hidden("id_employee", $r['id_employee']);
				echo form_hidden("bulan", $r['bulan']);
				echo form_hidden("tahun", $r['tahun']);
    		
    		$jml_hari = date("t", mktime(0, 0, 0, $r['bulan'], 1, $r['tahun']));
    		$nm_bulan = date("M", mktime(0, 0, 0, $r['bulan'], 1, $r['tahun']));
				
				//for($i=1;$i<=2;$i++) {
					$awal=intval(substr($tgl,8,2));
					
					echo "<table class='shift table table-striped'><tr><th>Date ( ".$nm_bulan." )</th>";
					for($a=$awal;$a<=$jml_hari;$a++) {
						$dt = $r['tahun']."-".$r['bulan']."-".sprintf("%02d", $a);
						if(date("w", strtotime($dt)) == 0 || date("w", strtotime($dt)) == 6) $cls="sunday";
						else $cls="";
						$day = substr(date("l", strtotime($dt)),0,1);
						echo "<td width='50' class='".$cls."'>".$day." / ".$a."</td>";
					}
					
					echo "</tr><tr><th>Schedule</th>";
					for($a=$awal;$a<=$jml_hari;$a++) {
						$auto=0;
						$dt = $r['tahun']."-".$r['bulan']."-".sprintf("%02d", $a);
						if($r['tgl_'.$a] == 3) {
							$auto = CekLemburAuto($r['id_employee'], $dt) ? 1 : 0;
						}
						
						if(in_array($dt, $holiday)) {
							if(preg_match("/MCCI/", $holiday[$dt])) $cls="holiday_office";
							else $cls="holiday";
						}
						else if($auto) $cls="lembur_auto";
						else if(strtoupper($r['tgl_'.$a])=="OFF") $cls="offz";
						else if(strtoupper($shift_ril['tgl_'.$a]) != strtoupper($r['tgl_'.$a])) $cls="tukeran";
						else $cls="";
						
						if($person_id != $r['id_employee']) $do =	form_dropdown("tgl_".$a, $opt_shift, strtoupper($r['tgl_'.$a]), "class='span1 ".$cls."'");
						else $do=strtoupper($r['tgl_'.$a]);
						
						if($cls=="tukeran") echo "<td class='".$cls."' alt='".strtoupper($shift_ril['tgl_'.$a])."' title='".strtoupper($shift_ril['tgl_'.$a])."'>".$do."</td>";
						else echo "<td class='".$cls."'>".$do."</td>";
					}
					
					//if($i==2 && $jml_hari%2 == 1) echo "<td width='50' style='border:0px !important;'>&nbsp;</td>";
					echo "</tr></table>";
				//}
				//
    	}
    	
    	foreach($shift_2->result_array() as $r) {
    		echo form_hidden("id2", isset($r["id"]) ? $r["id"] : 0);
				echo form_hidden("id_employee", $r['id_employee']);
				echo form_hidden("bulan2", $r['bulan']);
				echo form_hidden("tahun2", $r['tahun']);
    		
    		//$jml_hari = date("t", mktime(0, 0, 0, $r['bulan'], 1, $r['tahun']));
				$nm_bulan = date("M", mktime(0, 0, 0, $r['bulan'], 1, $r['tahun']));
				
				//for($i=1;$i<=2;$i++) {
					$akhir=intval(substr($tgl,19,2));
					
					echo "<table class='shift table table-striped'><tr><th>Date ( ".$nm_bulan." )</th>";
					for($a=1;$a<=$akhir;$a++) {
						$dt = $r['tahun']."-".$r['bulan']."-".sprintf("%02d", $a);
						if(date("w", strtotime($dt)) == 0 || date("w", strtotime($dt)) == 6) $cls="sunday";
						else $cls="";
						$day = substr(date("l", strtotime($dt)),0,1);
						echo "<td width='50' class='".$cls."'>".$day." / ".$a."</td>";
					}
					
					echo "</tr><tr><th>Schedule</th>";
					for($a=1;$a<=$akhir;$a++) {
						$auto=0;
						$dt = $r['tahun']."-".$r['bulan']."-".sprintf("%02d", $a);
						if($r['tgl_'.$a] == 3) {
							$auto = CekLemburAuto($r['id_employee'], $dt) ? 1 : 0;
						}
						
						if(in_array($dt, $holiday)) {
							if(preg_match("/MCCI/", $holiday[$dt])) $cls="holiday_office";
							else $cls="holiday";
						}
						else if($auto) $cls="lembur_auto";
						else if(strtoupper($r['tgl_'.$a])=="OFF") $cls="offz";
						else if(strtoupper($shift_ril_2['tgl_'.$a]) != strtoupper($r['tgl_'.$a])) $cls="tukeran";
						else $cls="";
						
						if($person_id != $r['id_employee']) $do =	form_dropdown("tgl_".$a, $opt_shift, strtoupper($r['tgl_'.$a]), "class='span1 ".$cls."'");
						else $do=strtoupper($r['tgl_'.$a]);
						
						if($cls=="tukeran") echo "<td class='".$cls."' alt='".strtoupper($shift_ril['tgl_'.$a])."' title='".strtoupper($shift_ril['tgl_'.$a])."'>".$do."</td>";
						else echo "<td class='".$cls."'>".$do."</td>";
					}
					
					//if($i==2 && $jml_hari%2 == 1) echo "<td width='50' style='border:0px !important;'>&nbsp;</td>";
					echo "</tr></table>";
				//}
				//
    	}
    	?>
    	
    	<div>
    		<label class="legend offz"></label><label class="title_legend">OFF</label>
    		<label class="legend holiday_office"></label><label class="title_legend">Company Holiday</label>
    		<label class="legend holiday"></label><label class="title_legend">Holiday</label>
    		<label class="clearfix"></label>
    		<label class="legend lembur_auto"></label><label class="title_legend">Automatic OT</label>
    		<label class="legend tukeran"></label><label class="title_legend">Shift Exchange</label>
    	</div>
    	
    	<div class="clearfix_button pull-right">
				<br><br>
				<button type="submit" class="btn btn-success btn-submit-shift">&nbsp;Submit</button>
				<button type="submit" class="btn btn-cancel-shift" rel="<?php echo str_replace(" ", "%20", $period);?>">&nbsp;Back</button>
				<br><br>
			</div>
    	</form>
    </div>
  </div>
</div>


<script src="<?php echo assets_url('modules/js/attendance.js')?>"></script>