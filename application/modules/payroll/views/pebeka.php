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

              <form method="post" enctype="multipart/form-data">
                <h3>Select file to upload</h3>
                <div class="form-group">
                  <input type="file" id="deduction_excel" name="deduction_excel">
                  
                </div>
                <div class="form-group">
                  <input type="submit" value="Submit" class="btn btn-default" name="submit">
                </div> 
              </form>

            </div>
          </div>
        </div>
      </div>
</div>
<!-- END CONTAINER -->