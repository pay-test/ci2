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
      <li><a href="<?php echo base_url('report')?>" class="active"><?php echo ucwords(lang('report'))?></a> </li>
    </ul>
    <!--<div class="page-title"> <i class="icon-custom-left" onclick="javascript:window.location='<?php echo site_url("dashboard");?>'"></i>
      <h3><span class="semi-bold"><?php echo ucwords(lang('attendance'))?></span></h3>
    </div>-->
    <div class="row-fluid">
      <div class="span12">
          <div class="col-md-12">
            <div class="tab-content">
            	<div class="row column-seperation">
							  <div class="col-md-12">
								  <div class="panel-body">
								  	
						        <div class="row">
						        	<div class="col-md-12">
							          <div class="col-sm-12" style="padding:0px;margin-bottom:15px;">
						              <div class="form-group">
														<label class="form-label">Report</label>
														<?php echo form_dropdown("s_report", $opt_report, "", "class='type_report span5'");?>
													</div>
					            	</div>
					            </div>
						        </div>
						        
						        <div class="tab-pane active" id="content" style="min-height:400px;"></div>
								  </div>
              	</div>
              </div>
            </div>
          </div>
        </div>
    </div>
  </div>
</div>