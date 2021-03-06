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
      <li><a href="<?php echo base_url('attendance_form/overtime')?>" class="active"><?php echo ucwords(lang('overtime'))?></a> </li>
    </ul>
    <!--<div class="page-title"> <i class="icon-custom-left" onclick="javascript:window.location='<?php echo site_url("dashboard");?>'"></i>
      <h3><span class="semi-bold"><?php echo ucwords(lang('attendance'))?></span></h3>
    </div>-->
    <div class="row-fluid">
      <div class="span12">
          <div class="col-md-12">
            <ul class="nav nav-tabs" role="tablist">
            	<?php if($flag) {$cls1="";$cls3="active";}
            	else {$cls3="";$cls1="active";}?>
              <li class="<?php echo $cls1;?>">
                <a href="#" onclick="loadFormOvt()" role="tab" data-toggle="tab"><?php echo "Form ".ucwords(lang('overtime'))?></a>
              </li>
              <li>
                <a href="#" onclick="loadHistoryOvt()" role="tab" data-toggle="tab"><?php echo  "List of ".ucwords(lang('overtime'))?></a>
              </li>
              <li class="<?php echo $cls3;?>">
                <a href="#" onclick="loadApprovalOvt()" role="tab" data-toggle="tab"><?php echo  "Overtime Request Status"?></a>
              </li>
            </ul>
          	<div class="tools"> <a href="javascript:;" class="collapse"></a> <a href="#grid-config" data-toggle="modal" class="config"></a> <a href="javascript:;" class="reload"></a> <a href="javascript:;" class="remove"></a> </div>
            
            <div class="tab-content">
              <div class="tab-pane active" id="content">
            </div>
          </div>
        </div>
    </div>
  </div>
</div>

<script type="text/javascript">window.onload = function(){loadFormOvt(<?php echo $flag;?>);};</script>