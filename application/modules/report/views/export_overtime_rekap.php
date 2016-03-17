<table class="table table-hover table-condensed" id="table">
  <thead>
    <tr>
      <th style="width:5%">No</th>
      <th style="width:5%">NIK</th>
      <th style="width:15%">Name</th>
      <th>Period</th>
      <th>Actual Hours</th>
      <th>Calculation Hours</th>
      <th>Overtime Rasio</th>
      <th>Amount</th>
    </tr>
  </thead>
  <tbody>
  	<?php
  	foreach($list as $r) {
  		?>
  		<tr>
  			<td><?php echo $r['0'];?></td>
  			<td><?php echo $r['1'];?></td>
  			<td><?php echo $r['2'];?></td>
  			<td><?php echo $r['3'];?></td>
  			<td><?php echo $r['4'];?></td>
  			<td><?php echo $r['5'];?></td>
  			<td><?php echo $r['6'];?></td>
  			<td><?php echo $r['7'];?></td>
  		</tr>
  		<?php
  	}
  	?>
  </tbody>
</table>