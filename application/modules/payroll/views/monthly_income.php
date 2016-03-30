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
        <li><a href="#"><?php echo $title; ?></a> </li>
        <li><a href="<?php echo base_url('payroll/monthly_income')?>" class="active"><?php echo $title.' - Monthly Income'; ?></a> </li>
      </ul>
      <div class="row-fluid">
        <div class="span12">
          <div class="grid simple ">
            <div class="grid-title">
              <h4>Table <span class="semi-bold"><?php echo $page_title ?></span></h4>
            </div>

            <div class="grid-body ">
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
              <hr/>
              <div class="row">
                <div class="col-md-12">
                  <table class="table table-hover table-condensed" id="table">
                    <thead>
                      <tr>
                        <th style="width:5%">No</th>
                        <th style="width:25%">NIK</th>
                        <th style="width:25%">Name</th>
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
    <div class="modal-dialog custom-class">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h3 class="modal-title">Form</h3>
        </div>
        <form action="#" id="form" class="form">
          <input type="hidden" name="employee_id" value="">
          <input type="hidden" name="period_id" value="">
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
                <input name="group_id" placeholder="" class="form-control" value="" type="hidden">
                  <input name="group_title" placeholder="" class="form-control" value="" type="text" disabled="">
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
                          <th style="width:25%">Component Type</th>
                          <th style="width:25%">Value</th>
                        </tr>
                      </thead>
                      <tbody id="component_table_body">
                      </tbody>
                    </table>
                    <button type="button" id="btnAdd" class="btn btn-primary btn-lg" onclick="addRow('component_table')"><i class="icon-plus"></i>&nbsp;Add Component</button>
                  </div>
                </div>
              </div>


          </div>
          <div class="modal-footer">
            <input type="hidden" value="" name="id" class="form-control"> 
            <button type="button" id="btnSave" onclick="save()" class="btn btn-primary">Save</button>
            <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <!-- End Bootstrap modal -->

  <script type="text/javascript">
  function addRow(tableID){
  var table=document.getElementById(tableID);
  var rowCount=table.rows.length;
  var row=table.insertRow(rowCount);

  var cell1=row.insertCell(0);
  cell1.innerHTML = "<select name='component_id[]' class='select2' style='width:100%'><?php foreach($component as $c):?><option value='<?php echo $c->id?>'><?php echo $c->title.' - '.$c->code?></option><?php endforeach;?></select>";  
  cell1.colSpan = 2;
  var cell3=row.insertCell(1);
  cell3.innerHTML = '<input class="form-control auto text-right" data-a-sep="," data-a-dec="." type="text" value="" name="value[]"></input>';  
}
  </script>
