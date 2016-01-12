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
        <div class="col-md-12">
          <div class="grid simple ">
            <div class="grid-title">
              <h4><span class="semi-bold">Process Payroll</span></h4>
            </div>
            <div class="grid-body">
              <form id="form-process" action="#">
                <div class="form-body">
                  <div class="form-group">
                    <label class="control-label col-md-1">Period</label>
                    <div class="col-md-3">
                        <div id="period" class="input-append success date">
                          <input type="text" class="form-control" name="period">
                          <span class="add-on"><span class="arrow"></span><i class="fa fa-th"></i></span>
                        </div>
                          <span class="help-block"></span>
                    </div>
                    <div class="col-md-1">
                      <span>s.d.</span>
                    </div>
                    <label class="control-label col-md-1">Period</label>
                    <div class="col-md-3">
                        <div id="period2" class="input-append success date">
                          <input type="text" class="form-control" name="period2">
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
      </div>
</div>
<!-- END CONTAINER -->