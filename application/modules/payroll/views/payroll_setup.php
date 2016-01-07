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
        <li><a href="" class="active"><?php echo $title; ?></a> </li>
      </ul>
      <div class="page-title"> <i class="icon-custom-left"></i>
        <h3><?php echo $title ?> - <span class="semi-bold"><?php echo $page_title; ?></span></h3>
      </div>
      <div class="row-fluid">
        <div class="col-md-6">
          <div class="grid simple ">
            <div class="grid-title">
              <h4><span class="semi-bold">Process Payroll</span></h4>
            </div>
            <div class="grid-body">
              <form id="form-process" action="#">
                <div class="form-body">
                  <div class="form-group">
                    <label class="control-label col-md-3">Period</label>
                    <div class="col-md-9">
                        <div id="period" class="input-append success date">
                          <input type="text" class="form-control" name="period">
                          <span class="add-on"><span class="arrow"></span><i class="fa fa-th"></i></span>
                        </div>
                          <span class="help-block"></span>
                    </div>
                  </div>
                  <div class="form-group">
                    <button type="submit" id="btnSave" class="btn btn-primary">Process</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="grid simple ">
            <div class="grid-title">
              <h4><span class="semi-bold">Setup Periode</span></h4>
            </div>
            <div class="grid-body">
               <form id="form-period" action="#">
                <div class="form-body">
                  <div class="form-group">
                    <label class="control-label col-md-3">Period</label>
                    <div class="col-md-9">
                        <select class="form-control select2" name="periode2">
                          <option value="0">Select session..</option>
                          <?php if ($period->num_rows() > 0) {
                            foreach ($period->result_array() as $p) { ?>
                            <option value="<?php echo $p['id']; ?>"><?php echo $p['title']; ?></option>
                            <?php }
                          } ?>
                        </select>
                        <span class="help-block"></span>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="control-label col-md-3">Status</label>
                    <div class="col-md-9">
                        <div class="radio radio-success">
                          <input value="0" name="status" id="open" type="radio">
                          <label for="open">Open</label>
                          <input checked="checked" value="1" name="status" id="close" type="radio">
                          <label for="close">Close</label>
                        </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <button type="submit" id="btnSave2" class="btn btn-primary">Apply</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
</div>
<!-- END CONTAINER -->

<!-- Bootstrap modal -->
<div class="modal fade" id="modal_form" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title"></h3>
            </div>
            <div class="modal-body form">
                <form action="#" id="form" class="form-horizontal">
                    <input type="hidden" value="" name="id"/> 
                    <div class="form-body">
                        <div class="form-group">
                            <label class="control-label col-md-3">Name</label>
                            <div class="col-md-9">
                                <input name="title" placeholder="Name" class="form-control" type="text">
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Code</label>
                            <div class="col-md-9">
                                <input name="code" placeholder="Code" class="form-control" type="text">
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Attribute</label>
                            <div class="col-md-9">
                                <select name="is_annualized" class="form-control select2">
                                  <option value="0">Not Annualized</option>
                                  <option value="1">Annualized</option>
                                </select>
                                <span class="help-block"></span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnSave" onclick="save()" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- End Bootstrap modal -->