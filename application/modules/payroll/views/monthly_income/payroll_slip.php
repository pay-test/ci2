<!doctype html>
<html>
<head>
<meta charset="utf-8">
<style type="text/css">
<!--
-->
</style>
</head>
<body>
  <div style="float: left; width: 50%;">PT Mitsubishi Chemical Indonesia</div><div style="float: right; width: 50%; text-align:right;">Payroll Slip</div>
  <hr style="width:100%">
  <div style="float: right; width: 50%; text-align:right; font-size:12px">Period <?php echo $period ?></div>
<br/><p></p>
<table width="75%" border="0.0">
  <tbody>
  	<?php 
  	if ($employee_detail->num_rows() > 0) { 
  		$row = $employee_detail->row();
  		$employee_nm = $row->person_nm;
  	?>
    <tr>
      <td width="20%">Name</td>
      <td width="2%">:</td>
      <td width="78%"><?php echo $employee_nm ?></td>
    </tr>
    <tr>
      <td>NIK</td>
      <td>:</td>
      <td><?php echo $row->user_nm ?></td>
    </tr>
    <tr>
      <td>Position</td>
      <td>:</td>
      <td><?php echo $row->job_nm." (".$row->job_abbr.")" ?></td>
    </tr>
    <tr>
      <td>Section</td>
      <td>:</td>
      <td><?php echo $row->org_nm ?></td>
    </tr>
    <?php } ?>
  </tbody>
</table>
<hr style="width:100%">
<table width="100%" border="0.0">
    <tr>
      <th colspan="2">INCOME</th>
	<hr style="width:100%">
      <th>&nbsp;</th>
	<hr style="width:100%">
      <th colspan="2">DEDUCTION</th>
	<hr style="width:100%">
    </tr>
    <tr class="oddrow">
		<td>
		<?php foreach($income as $in):?>
      <?php if($in->value != 0): ?>
			<p><?php echo ucwords(strtolower($in->component))?></p><br/>
      <?php endif;?>
		<?php endforeach?>
		</td>
		<td align="right">
		<?php $tincome = 0;foreach($income as $in):?>
      <?php if($in->value != 0): ?>
			<p><?php echo number_format($in->value, 2);$tincome+=$in->value;?></p><br/>
      <?php endif;?>
		<?php endforeach;?>
		</td>
		<td></td>
		<td>
		<?php foreach($deduction as $de):?>
      <?php if($de->value != 0): ?>
			<p><?php echo ucwords(strtolower($de->component))?></p><br/>
      <?php endif;?>
		<?php endforeach;?>
		</td>
		<td align="right">
			<?php $tdeduction=0;foreach($deduction as $de):?>
      <?php if($de->value != 0): ?>
			<p><?php echo number_format($de->value, 2);$tdeduction+=$de->value;?></p><br/>
      <?php endif;?>
		<?php endforeach;?>
		</td>
	</tr>

	<!--
	<td>
		<?php foreach($income as $in):?>
		    <tr>
		      <td width="30%">gaji</td>
		      <td width="15%" align="right"><?php echo $in->value?></td>
		    </tr>
		<?php endforeach;?>
	</td>
	<td>
		<?php foreach($deduction as $de):?>
			<tr>
		    	<td width="30%">potongan</td>
		    	<td width="15%" align="right"><?php echo $de->value?></td>
		    </tr>
		<?php endforeach;?>
	</td>
	-->
</table>

	<hr style="width:100%">
<br/>
  <table width="100%" border="0.0">
    <tbody>
    <tr >
      <td width="30%" height="30">Income Total</td>
      <td width="15%" align="right"><?php echo number_format($tincome, 2)?></td>
      <td width="10%">&nbsp;</td>
      <td width="30%">Deduction Total</td>
      <td width="15%" align="right"><?php echo number_format($tdeduction, 2)?></td>
    </tr>
    <tr >
	    <td width="30%">Take Home Pay</td>
      <td width="15%" align="right" style="border:1"><?php echo number_format($tincome-$tdeduction, 2)?></td>
      <td width="10%">&nbsp;</td>
      <td width="30%"></td>
      <td width="15%" align="right"></td>
    </tr>
    </tbody>
  </table>
<!--
<div style="float: left; width: 50%; margin-top: 50px; text-align:center; font-size:12px">
Approved,
<p></p>
<p></p>
<p></p>
<p></p>
<p></p>
<p></p>
HRD
</div>

<div style="float: right; width: 50%; text-align:center; font-size:12px">
	Received by,
	<p></p>
	<p></p>
	<p></p>
	<p></p>
	<p></p>
	<p></p>
	<?php echo $employee_nm ?>
</div>
-->
</body>
</html>