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
        <li><a href="#" class="active"><?php echo 'Payroll'; ?></a> </li>
        <li><a href="#" class="active"><?php echo 'UMK'; ?></a> </li>
      </ul>
        <div class="tabbable">
            <div class="tab-content" style="height:550px;overflow:auto;">
                 <!-- TAB Exchange Rate -->
                <div class="tab-pane fade in active" id="tab-rate">
                    <div class="container-fluid container-fullw bg-white">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="rate">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label class="label-form">Select Session</label>
                                        </div>
                                        <div class="col-md-3">
                                            <select class="select2" id="session_select" style="width:100%">
                                                <option>-- Select Session --</option>
                                                <?php foreach($session->result() as $s):
                                                    $selected = ($s->id == sessNow()) ? "selected='selected'" : '';
                                                ?>
                                                    <option value="<?php echo $s->id?>" <?php echo $selected?>><?php echo $s->description?></option>
                                                <?php endforeach;?>
                                            </select>
                                        </div>
                                        <div class="col-md-1">
                                            <label class="label-form">Value</label>
                                        </div>
                                        <div class="col-md-2">
                                            <label style="font-weight:700" class="form-label" id="value"></label>
                                            <input type="hidden" class="form-control" id="id">
                                            <input type="text" style="display:none" class="form-control money" id="value-text">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END CONTAINER -->

