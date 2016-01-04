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
        <li><a href="<?php echo base_url('absensi/kehadiran')?>" class="active">Kehadiran</a> </li>
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

<form action="<?php echo site_url('overtime_analysis/uploads');?>" method="post" enctype="multipart/form-data">
<table style="width:400px;">
  <h3>File Upload OT</h3>
  <tr id="head">
    <td>Type</td>
  	<td colspan="2">
  		<input type="radio" id="type_upload" name="typez" value="weekly"/> Weekly&nbsp;&nbsp;&nbsp;
  		<input type="radio" id="type_upload" name="typez" value="rekap"/> Rekap
  	</td>
  </tr>
  <tr id="head">
  	<td>Periode</td>
  	<td style="white-space:nowrap;" colspan="2">
			<input name="periode_start" class="tanggal span3"> 
			s / d
			<input name="periode_end" class="tanggal span3">
		</td>
	</tr>
  <tr id="head">
    <td>File</td>
  	<td><input type="file" id="file_upload" name="userfile" size="20" data-validation-engine="validate[required]"/>	</td>
		<td><input type="submit" name="submit" value="Upload" class="btn"/></td>
  </tr><?php if(permissionactionz()){?>
  <tr>
  <td colspan="3"><a href="<?php echo base_url()?>uploads/template_ot_weekly.xls">Download Template OT Weekly</a></td>
  </tr>
  <tr>
  <td colspan="3"><a href="<?php echo base_url()?>uploads/template_ot_rekap.xls">Download Template OT Rekap</a></td>
  </tr>
  <?php }?>
</table>
</form>

</div>
            </div>
        </div>
    </div>
</div>