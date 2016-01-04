<!-- BEGIN PAGE CONTAINER-->
  <div class="page-content">
    <!-- BEGIN SAMPLE PORTLET CONFIGURATION MODAL FORM-->
    <div id="portlet-config" class="modal hide">
      <div class="modal-header">
        <button data-dismiss="modal" class="close" type="button"></button>
        <h3>Widget Settings</h3>
      </div>
      <div class="modal-body"> Widget settings form goes here </div>
    </div>
    <div class="clearfix"></div>
    <div class="content">
      <ul class="breadcrumb">
        <li>
          <p>YOU ARE HERE</p>
        </li>
        <li><a href="<?php echo base_url('absensi/kehadiran')?>">Kehadiran</a> </li>
        <li><a href="<?php echo base_url($this->uri->uri_string())?>" class="active">Detail</a> </li>
      </ul>
      <div class="page-title"> <i class="icon-custom-left" onclick="javascript:window.location='<?php echo site_url("dashboard");?>'"></i>
        <h3><span class="semi-bold">Kehadiran</span></h3>
      </div>
      <div class="row-fluid">
        <div class="span12">
          <div class="grid simple ">
            <!--
            <div class="grid-title">
              <h4>Table <span class="semi-bold">Styles</span></h4>
            </div>
            -->
            <div class="grid-body ">

<form id="<?php echo $filename;?>">
  <h3><?php echo $title;?></h3>
  <table class="table table-striped table-hover">
    <tr>
    	<?php if(permissionaction()){?>
    	<input type="hidden" id="temp_id" value="">
    	<th class="box_delete">
    		<input type="checkbox" onclick="checkedAll('<?php echo $filename;?>', true)" id="primary_check" value="" name="">
    	</th>
		<th>NO</th>
    	<?php }?>
    	<?php
		
    	foreach($grid as $r)
    	{
    		echo "<th>".$r."</th>";
    	}
    	?>
    	<?php if(permissionaction()){?>
    	<th class='action'>Action</th>
    	<?php }?>
    </tr>
    <?php
	$no = $this->uri->segment(8) ? $this->uri->segment(8) : 0;
    foreach($query_list->result_array() as $r)
    {
    	if($shift) $r['id'] = $r['id_k'];
    	echo "<tr id='listz-".$r['id']."'>";
    	if(permissionaction()) echo "<td class='box_delete'><input type='checkbox' class='delete' id='del".$r['id']."' value='".$r['id']."'></td>";
    	echo "<td>".++$no."</td>";
		
		foreach($list as $s)
    	{
    		if($s == "tgl")
    		{
    			$r[$s] = GetTanggal($r['tanggal'])." ".GetMonthFull(intval($r['bulan']))." ".$r['tahun'];
    		}
    		else if($s == "id_employee")
    		{
    			$r[$s.'_temp'] = $r[$s];
    			$r[$s] = GetValue("person_nm","hris_persons", array("person_id"=> "where/".$r[$s]));
    		}
    		else if($s == "name" || $s == "keterangan" || $s == "scan_masuk" || $s == "scan_pulang") $r[$s] = $r[$s];
    		else if(($s == 'jh' || $s == 'hr') && $flag_tgl)
    		{
				//print_mz($r);
				if($r['hr'] == 1) { 
				$r['jh'] = "<i class='fa fa-check'></i>"; 
				}
    			else if($s == "jh" && $r['jh'] == 1){ 
				
				$r['jh'] = "<i class='fa fa-check'></i>";}
    			else $r[$s]="";
    		}
    		else if($r[$s] == 1 && $flag_tgl) $r[$s] = "<i class='fa fa-check'></i>";
    		else if($flag_tgl) $r[$s]="";
    		echo "<td>".$r[$s]."</td>";
    	}
    	if(!$r['id']) $r['id']=0;
    	if(permissionaction()){
    	echo "<td class='action'>";
	    if($flag_tgl){ 
	    	if(permissionactionz()){ 
		    	if($tgl_today==date("Y-m-d")) echo "-";
		    	else echo "<a href='".site_url('absensi/'.$filename.'/edit/0/'.$r['tahun'].'-'.$r['bulan'].'-'.GetTanggal($r['tanggal']).'/'.$r['id_employee_temp'].'/'.$r['id'].'/u')."'>Edit</a>";
	    	}
	    } else echo "<a href='".site_url($filename.'/main/'.$r['a_id'].'/'.$tgl)."'>Detail</a>";
	    echo "</td>";
	  	}
	    echo "</tr>";
    }
    ?>
	</table>
	<br><br>
  <div class="clear"></div>
  <div class="dataTables_paginate paging_bootstrap pagination">
    <ul>
      <?php echo $pagination;?>
    </ul>
  </div>
</form>
</div>
            </div>
        </div>
    </div>
</div>