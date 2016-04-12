<style>@media print{.head{display:none;}}</style>
<div id="title">
	<table width="100%" style="margin-bottom:10px;">
		<?php
		if(!$report) {?>
		<tr class="head">
			<td colspan="3" style="padding-bottom:20px;"><img src="<?php echo base_url();?>assets/assets/img/logo.png" width="150"></td>
			<td colspan="30" style="text-align:right;padding-bottom:10px;">
				<a style="text-decoration:none;" href="javascript:void(0);" onClick="window.location='<?php echo site_url("report/list_shift/1/".$start_date."~".$end_date."/".$div."/".$sec."/".$pos."/".$grade);?>'"><img src="<?php echo assets_url('assets/img/excel.png');?>"></a>
				<a style="text-decoration:none;" href="javascript:void(0);" onClick="print();"><img src="<?php echo assets_url('assets/img/print.png');?>"></a>
			</td>
		</tr>
		<?php }?>
		<tr>
			<td colspan="3">SUMMARY OF WORKING SCHEDULE</td>
			<td colspan="30" style="text-align:right;">Division : <?php $title = GetValue("org_nm", "hris_orgs", array("org_id"=> "where/".$div));echo ($title) ? $title : "All";?></td>
		</tr>
		<tr>
			<td colspan="3">Period : <?php echo "<b>".$period."</b>";?></td>
			<td colspan="30" style="text-align:right;">Section : <?php $title = GetValue("org_nm", "hris_orgs", array("org_id"=> "where/".$sec));echo ($title) ? $title : "All";?></td>
		</tr>
	</table>
</div>

<?php
$jml_hari_start = date("t", strtotime($start_date));
$cols_start = ($jml_hari_start%2 == 1) ? round($jml_hari_start/2) : $jml_hari_start/2;
$jml_hari_end = date("t", strtotime($end_date));
$cols_end = ($jml_hari_end%2 == 1) ? floor($jml_hari_end/2) : $jml_hari_end/2;
?>
<div class="grid-body">
	<table border="1" width="100%">
    <thead>
      <tr>
        <th style="width:5%" rowspan="2">No</th>
        <th style="width:5%" rowspan="2">NIK</th>
        <th style="width:15%" rowspan="2">Name</th>
        <th colspan="<?php echo $cols_start;?>"><?php echo GetMonth(substr($start_date,5,2))." ".substr($start_date,0,4);?></th>
        <th colspan="<?php echo $cols_end;?>"><?php echo GetMonth(substr($end_date,5,2))." ".substr($end_date,0,4);?></th>
      </tr>
      <tr>
      	<?php
      	for($i=substr($start_date,8,2);$i<=$jml_hari_start;$i++) {
      		echo "<th align='center'>".$i."</th>";
      	}
      	for($i=1;$i<=substr($end_date,8,2);$i++) {
      		echo "<th align='center'>".$i."</th>";
      	}
      	?>
      </tr>
    </thead>
    <tbody>
    	<?php
    	$no=0;
    	//print_mz($list);
    	foreach($list as $emp) {
    		foreach($emp as $key=> $val) {
	    		if($key==0) {
	    			echo "<tr>";
	    			echo "<td align='center'>".++$no."</td>";
	    			echo "<td align='right'>".$val['ext_id']."</td>";
	    			echo "<td>".$val['person_nm']."</td>";
	    			for($i=substr($start_date,8,2);$i<=$jml_hari_start;$i++) {
		      		echo "<td align='center' style='font-size:12px;'>".strtoupper($val['tgl_'.$i])."</td>";
		      	}
	    		}
	    		
	    		if($key==1) {
	    			for($i=1;$i<=substr($end_date,8,2);$i++) {
		      		echo "<td align='center' style='font-size:12px;'>".strtoupper($val['tgl_'.$i])."</td>";
		      	}
	    			echo "</tr>";
	    		}
    		}
    	}
    	/*echo "<tr>";
    	echo "<td colspan='4' style='text-align:center;'>TOTAL</td>";
    	echo "<td style='text-align:center;'>".Decimal($tot_act,1)."</td>";
    	echo "<td style='text-align:center;'>".Decimal($tot_cal)."</td>";
    	echo "<td>&nbsp;</td>";
    	echo "<td style='text-align:right;'>".Rupiah($tot_amt)."</td>";
    	echo "</tr>";*/
    	?>
    </tbody>
  </table>
  <br>
  <table width="100%">
	  <tr>
	  	<td colspan="3">
	  		Jakarta, <?php echo date('M d, Y')?><br/>Prepared By,<br /><br /><br /><br /><br />
	  		<span style="text-decoration:underline;"><?php echo "";?></span><br>
	  		HRD
	  	</td>
	  	<td colspan="15">
	  		<br/>Checked By,<br /><br /><br /><br /><br />
	  		<span style="text-decoration:underline;"><?php echo "";?></span><br>
	  		Admin & Personal Manager
	  	</td>
	  	<td colspan="15">
	  		<br/>Approved By,<br /><br /><br /><br /><br />
	  		<span style="text-decoration:underline;"></span><br>
	  		Authorized Signatory
	  	</td>
	  </tr>
	</table>
</div>