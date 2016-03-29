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
        <div class="span12">
          <div class="grid simple ">
            <div class="grid-title">
              <h4>Deduction Data Upload for <span class="semi-bold"><?php echo $page_title ?></span></h4>
            </div>

            <div class="grid-body">
              <div class="row">
                <div class="col-md-4">
                  <label class="control-label col-md-3">Period</label>
                    <div class="col-md-9">
                        <select class="form-control select2" name="periode" id="periode">
                          <option value="0">Select session..</option>
                          <?php if ($period->num_rows() > 0) {
                            foreach ($period->result_array() as $p) { 
                              $selected = ($p['year'] == date('Y') && $p['month'] == date('m')) ? "selected='selected'" : '';
                            ?>
                            <option value="<?php echo $p['id']; ?>" <?php echo $selected?>><?php echo $p['title']; ?></option>
                            <?php }
                          } ?>
                        </select>
                        <span class="help-block"></span>
                    </div>
                </div>
                <div class="col-md-6">
                  <h3 class="text-center" id="periode-status"></h3>
                </div>
              </div>
              <form method="post" id="upload_excel" action="<?php echo base_url('payroll/cooperation/upload_excel') ?>" enctype="multipart/form-data">
                <input type="hidden" name="title" value="test">
                <h3>Select file to upload</h3>
                <div class="form-group">
                  <input type="file" id="excelfile" name="excelfile">
                  
                </div>
                <div class="form-group">
                  <input type="submit" value="Submit" class="btn btn-default" name="submit">
                </div> 
              </form>
              <div id="files"></div>
            </div>
          </div>
        </div>
      </div>
</div>
<!-- END CONTAINER -->