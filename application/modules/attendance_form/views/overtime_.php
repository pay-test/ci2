<div class="panel-group" id="accordion" data-toggle="collapse">
  <div class="panel panel-default">
		<div class="panel-heading">
		  <h4 class="panel-title">
			<a class="" data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
			   Search
			</a>
		  </h4>
		</div>
		<div id="collapseOne" class="panel-collapse collapse in">
		  <div class="panel-body">
			 <form id="search" action="<?php echo site_url($path_file.'/overtime');?>" method="post">
        <div class="row">
          <div class="col-md-12">
              <!--<div class="col-md-3">
                  <label>Departement</label>
                  <select class="select2" style="width:100%">
                      <option value="0">-- Choose Departement --</option>
                      <option value="0">Tes</option>
                      <option value="0">Tes 2</option>
                  </select>
              </div>-->
              <div class="col-md-2">
                  <div class="row">
                      <label>Period</label>
                      <div id="period" class="input-append success no-padding">
                        <input type="text" id="periode" class="form-control s_periode span2" value="<?php echo $period;?>" required>
                        <span class="add-on"><span class="arrow"></span><i class="fa fa-th"></i></span> 
                      </div>
                  </div>
              </div>
              <div class="col-md-2">
                  <div class="row">
                      <label>Date</label>
                      <div id="datepicker_start" class="input-append date success no-padding">
                        <input type="text" class="form-control start_att tgl span2" name="start_cuti" value="<?php echo $start_date;?>" required>
                        <span class="add-on"><span class="arrow"></span><i class="fa fa-th"></i></span> 
                      </div>
                  </div>
              </div>
              <div class="to">to</div>
              <div class="col-md-2">
                  <div class="row">
                      <label>&nbsp;</label>
                      <div id="datepicker_end" class="input-append date success no-padding">
                          <input type="text" class="form-control end_att tgl span2" name="end_cuti" value="<?php echo $end_date;?>">
                          <span class="add-on"><span class="arrow"></span><i class="fa fa-th"></i></span> 
                      </div>
                  </div>
              </div>

              <div class="col-md-2">
                  <label>&nbsp;</label>
                  <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-info btn-search-ovt"><i class="fa fa-search"></i>&nbsp;Search</button>
                    </div>
                  </div>
              </div>
          </div>
        </div>
      </form>
		  </div>
		</div>
  </div>
  <div class="panel panel-default" style="display:none;">
		<div class="panel-heading">
		  <h4 class="panel-title">
			<a class="collapsed" data-toggle="collapse" data-parent="#accordion"  href="#collapseTwo">
			  Syncronize
			</a>
		  </h4>
		</div>
		<div id="collapseTwo" class="panel-collapse collapse">
		  <div class="panel-body">				
        <div class="row">
          <div class="col-md-12">
            <div class="col-md-3">
              <div class="row">
                  <label>Date</label>
                  <div id="datepicker_start" class="input-append date success no-padding">
                    <input type="text" class="form-control tgl_absen tgl" required>
                    <span class="add-on"><span class="arrow"></span><i class="fa fa-th"></i></span> 
                  </div>
              </div>
            </div>
            <div class="col-md-2">
              <label>&nbsp;</label>
              <button type="button" name="submit" class="btn btn-success btn-sync"><i class='fa fa-refresh'> Sync</i></button>
              <div class="sync"></div>
            </div>
          </div>
        </div>
		  </div>
		</div>
  </div>
  <div class="panel panel-default" style="display:none;">
		<div class="panel-heading">
		  <h4 class="panel-title">
			<a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseThree">
			 Upload
			</a>
		  </h4>
		</div>
		<div id="collapseThree" class="panel-collapse collapse">
		  <div class="panel-body">
				<div class="row">
          <div class="col-md-12">
            <div class="col-md-4">
                <label>File</label>
                <input type="file" id="file_upload" name="userfile">
            </div>
            <div class="col-md-2">
                <label>&nbsp;</label>
                <button type="submit" name="submit" class="btn btn-primary"><i class='fa fa-upload'> Upload</i></button>
            </div>
          </div>
          <!--<div class="col-md-12">
            <br/>
            <legend>
                <label><a href="http://localhost/dus/uploads/template_kehadiran.xls">Download Template</a></label>  
            </legend>
          </div>-->
        </div>
		  </div>
		</div>
  </div>
</div>

<div class="grid simple transparent">
	<div class="grid-title">
		<h4>List <span class="semi-bold">Overtime</span></h4>
	</div>
</div>

<div class="tab-pane" id="tab1FollowUs">
  <div class="row">
    <div class="col-md-12">
      <form id="<?php echo $filename;?>">
        <table class="table table-striped table-hover">
          <tr>
            <?php if(permissionaction()){?>
            <input type="hidden" id="temp_id" value="">
            <!--<th class="box_delete" width="70">
              <input type="checkbox" onclick="checkedAll('<?php echo $filename;?>', true)" id="primary_check" value="" name="">
            </th>-->
            <?php }?>
            <?php
            foreach($grid as $r)
            {
            	if($r=="Name") echo "<th width='295'>".$r."</th>";
            	else if($r=="Hour SUM") echo "<th>".$r."</th>";
            	else echo "<th width='150'>".$r."</th>";
            }
            ?>
            <th class='action' width='150'>Action</th>
          </tr>
          <?php
          $grup=array();
	        $cek_grup = GetAll("kg_hris_employee", array(), array("employee_grup"=> array("A", "B", "C", "D")));
	        foreach($cek_grup->result_array() as $s) {
	        	$grup[$s['person_id']] = $s['person_id']; 
	        }
          foreach($query_list->result_array() as $r)
          {
          	if(isset($grup[$r['a_id']])) {
	            echo "<tr id='listz-".$r['a_id']."'>";
	            //if(permissionaction()) echo "<td class='box_delete'><input type='checkbox' class='delete' id='del".$r['id']."' value='".$r['id']."'></td>";
	            foreach($list as $s)
	            {
	              if($s == "name") $r[$s] = $r[$s];
	              else if($r[$s] == 1 && $flag_tgl) $r[$s] = "<img src='".base_url()."assets/images/1.gif'>";
	              else if($flag_tgl) $r[$s]="";
	              echo "<td>".$r[$s]."</td>";
	            }
	            if(!$r['id']) $r['id']=0;
	            echo "<td class='action'>";
				    	//echo "<a href='".site_url($filename.'/detail/'.$r)."'>Edit</a>";
				    	echo '<a class="btn btn-sm btn-primary detail-OT" href="javascript:void(0);" title="detail" rel="'.$r['a_id'].'"><i class="fa fa-edit"></i></a>';
				    	echo "</tr>";
				    	
				    	echo "<tr></tr>";
				    	echo "<tr>";
				    	echo "<td colspan='5' style='padding:0px !important;'>";
				    	echo "<div id='OT".$r['a_id']."' style='display:none;' class='listdetailOT'>";
				    	echo "<table class='table table-striped table-hover table-detailOT'>";
				    	echo "<tr><th>No</th><th>Date</th><th>Hour SUM</th><th>Detail Reason</th></tr>";
				    	$no=0;
				    	foreach($query_detail[$r['a_id']] as $key=> $d) {
				    		$no++;
				    		$tgl = GetTanggal($d['tanggal'])." ".GetMonthFull(intval($d['bulan']))." ".$d['tahun'];;
				    		echo "<tr><td width='70'>".$no."</td><td width='225'>".$tgl."</td><td>".$d['acc_ot_incidental']."</td><td>".$d['ovt_detail_reason']."</td></tr>";
				    	}
				    	echo "</table>";
				    	echo "</div>";
				    	echo "</td>";
				    	echo "</tr>";
				    }
          }
          ?>
          <div id="temp_open" rel="0"></div>
        </table>
      </form>
    </div>
  </div>
</div>

<script src="<?php echo assets_url('modules/js/attendance.js')?>"></script>
<link href="<?php echo assets_url('modules/css/custom.css')?>" rel="stylesheet" type="text/css" />
