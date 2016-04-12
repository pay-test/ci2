<style>@media print{.head{display:none;}}</style>
<div id="title">
	<table width="100%" style="margin-bottom:5px;">
		<?php
		if(!$report) {?>
		<tr class="head">
			<td colspan="5" style="padding-bottom:20px;"><img src="<?php echo base_url();?>assets/assets/img/logo.png" width="150"></td>
			<td colspan="3" style="text-align:right;padding-bottom:10px;">
				<a style="text-decoration:none;" href="javascript:void(0);" onClick="window.location='<?php echo site_url("report/list_ovt/1/".$rekap_full."/".$start_date."~".$end_date."/".$regs."/".$div."/".$sec."/".$pos."/".$grade);?>'"><img src="<?php echo assets_url('assets/img/excel.png');?>"></a>
				<a style="text-decoration:none;" href="javascript:void(0);" onClick="print();"><img src="<?php echo assets_url('assets/img/print.png');?>"></a>
			</td>
		</tr>
		<?php }?>
		<tr>
			<td colspan="5">LIST OF OVERTIME (FULL)</td>
			<td colspan="3" style="text-align:right;">Division : <?php $title = GetValue("org_nm", "hris_orgs", array("org_id"=> "where/".$div));echo ($title) ? $title : "All";?></td>
		</tr>
		<tr>
			<td colspan="5">Period : <?php echo "<b>".$period."</b>";?></td>
			<td colspan="3" style="text-align:right;">Section : <?php $title = GetValue("org_nm", "hris_orgs", array("org_id"=> "where/".$sec));echo ($title) ? $title : "All";?></td>
		</tr>
	</table>
</div>
<div class="grid-body">
	<table border="1" width="100%">
    <thead>
      <tr>
	      <th style="width:5%">No</th>
	      <th style="width:5%">NIK</th>
	      <th style="width:15%">Name</th>
	      <th>Date</th>
	      <th>Actual Hours</th>
	      <th>Calculation Hours</th>
	      <th>Overtime Reason</th>
	      <th>Overtime Detail Reason</th>
	    </tr>
    </thead>
    <tbody>
    	<?php
    	foreach($list as $key=> $val) {
    		echo "<tr>";
    		for($i=0;$i<=$jum_kolom;$i++) {
    			if($i==1) echo "<td style='vertical-align:top;text-align:right;'>".$val[$i]."</td>";
    			else if($i==2 || $i==6 || $i==7) echo "<td style='vertical-align:top;'>".$val[$i]."</td>";
    			else if($i==3) echo "<td style='vertical-align:top;white-space:nowrap;'>".$val[$i]."</td>";
    			else echo "<td style='vertical-align:top;text-align:center;'>".$val[$i]."</td>";
    		}
    		echo "</tr>";
    	}
    	echo "<tr>";
    	echo "<td colspan='4' style='text-align:center;'>TOTAL</td>";
    	echo "<td style='text-align:center;'>".Decimal($tot_act,1)."</td>";
    	echo "<td style='text-align:center;'>".Decimal($tot_cal)."</td>";
    	echo "<td>&nbsp;</td>";
    	echo "<td style='text-align:right;'>&nbsp;</td>";
    	echo "</tr>";
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
	  	<td colspan="3">
	  		<br/>Checked By,<br /><br /><br /><br /><br />
	  		<span style="text-decoration:underline;"><?php echo "";?></span><br>
	  		Admin & Personal Manager
	  	</td>
	  	<td colspan="2">
	  		<br/>Approved By,<br /><br /><br /><br /><br />
	  		<span style="text-decoration:underline;"></span><br>
	  		Authorized Signatory
	  	</td>
	  </tr>
	</table>
</div>