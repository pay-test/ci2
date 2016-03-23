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
              <h4>Table <span class="semi-bold"><?php echo $page_title ?></span></h4>
            </div>

            <div class="grid-body ">
              <div class="row">
                  <div class="col-md-2">
                      <label class="label-form">Select Session</label>
                  </div>
                  <div class="col-md-4">
                      <select class="select2" id="session_select" style="width:100%">
                          <option>-- Select Session --</option>
                          <?php foreach($session->result() as $s):
                          $selected = ($s->id == sessNow()) ? "selected='selected'" : '';
                          ?>
                              <option value="<?php echo $s->id?>" <?php echo $selected?>><?php echo $s->description?></option>
                          <?php endforeach;?>
                      </select>
                  </div>
              </div>
              <div class="row">
                <div class="col-md-12">
                  <table class="table table-hover table-condensed" id="table">
                    <thead>
                      <tr>
                        <th style="width:5%">No</th>
                        <th style="width:20%">NIK</th>
                        <th style="width:25%">Name</th>
                        <th style="width:20%">Position</th>
                        <th style="width:20%">Section</th>
                        <th style="width:10%">Action</th>
                      </tr>
                    </thead>
                    <tbody>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
</div>
<!-- END CONTAINER -->

<!-- Bootstrap modal -->
  <div class="modal fade" id="modal_form" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h3 class="modal-title">Form</h3>
          <h5 id="session-show" style="margin-left:-10px"></h5>
        </div>
        <form action="#" id="form" class="form">
        <input type="hidden" name="employee_id" value="">
        <input type="hidden" name="session_id" value="">
        <div class="modal-body">
          <div class="col-md-6">
            <div class="row form-row">
              <label class="control-label col-md-4">NIK</label>
              <div class="col-md-8">
                <input name="user_nm" placeholder="" class="form-control" type="text" disabled>
                <span class="help-block"></span>
              </div>
            </div>

            <div class="row form-row">
              <label class="control-label col-md-4">Name</label>
              <div class="col-md-8">
              <input name="person_nm" placeholder="" class="form-control" type="text" disabled>
              </div>
            </div>

            <div class="row form-row">
              <label class="control-label col-md-4">Payroll Group</label>
              <div class="col-md-8">
                <input type="hidden" class="form-control" name="group_id" value="">
                <input type="text" class="form-control" name="job_class_nm" value="" readonly="">
              </div>
            </div>

            <div class="row form-row">
              <label class="control-label col-md-4">Tax Method</label>
              <div class="col-md-8">
                <?php 
                    $js = 'class="select2 form-control" style="width:100%" id="tax_method"';
                    echo form_dropdown('tax_method', $tax_method,'',$js); 
                ?>
              </div>
            </div><br/>

          </div>

          <div class="col-md-6">
            <div class="row form-row">
              <label class="control-label col-md-4">Expatriate</label>
              <div class="col-md-8">
                <select class="select2 form-control" style="width:100%" name="is_expatriate">
                  <option value="0">Non-Expatriate</option>
                  <option value="1">Expatriate</option>
                </select>
              </div>
            </div><br/>
            <div class="row form-row">
              <label class="control-label col-md-4">Currency</label>
              <div class="col-md-8">
                <?php 
                    $js = 'class="select2 form-control" style="width:100%" id="currency"';
                    echo form_dropdown('currency', $currency,'',$js); 
                ?>
              </div>
            </div><br/>
            <div class="row form-row">
              <label class="control-label col-md-4">Tax Status</label>
              <div class="col-md-8">
                <?php 
                  $js = 'class="select2 form-control" style="width:100%" id="payroll_ptkp_id"';
                  echo form_dropdown('payroll_ptkp_id', $ptkp,'',$js); 
                ?>
              </div>
            </div><br/>
          </div>
          <br/>
          <br/>
          <div class="row form-row">
            <div class="col-md-12">
              <div>
                <table class="table table-bordered no-more-tables" id="component_table">
                  <thead>
                    <tr>
                      <th style="width:25%">Payroll Component</th>
                      <th style="width:25%">Code</th>
                      <th style="width:25%">Component_type</th>
                      <th style="width:25%">Value</th>
                    </tr>
                  </thead>
                  <tbody id="component_table_body">
                  </tbody>
                </table>
              </div>
              <div class="pull-right">
                <button type="button" id="btnSave" onclick="save()" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
              </div>
            </div>
              
          </div>
        </div>
        <input type="hidden" value="" name="id" class="form-control"> 
        
        </form>
      </div>
    </div>
  </div>
  <!-- End Bootstrap modal -->
