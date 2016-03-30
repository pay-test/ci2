<input type="hidden" value="<?php echo base_url()?>" id="base_url">
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
      <li><a href="<?php echo base_url('payroll/payroll_component')?>" class="active"><?php echo $title.' - Component'; ?></a> </li>
      <li><a href="<?php echo base_url('payroll/payroll_component/add')?>" class="active"><?php echo 'Add'; ?></a> </li>
    </ul>
    <div class="row-fluid">
      <div class="span12">
        <div class="grid simple ">
          <div class="grid-title">
            <h4>Table <span class="semi-bold"><?php echo $page_title ?></span></h4>
          </div>
          <div class="grid-body ">
            <div class="row">
              <div class="col-md-12">
                <div class="grid simple">
                  <div class="grid-body no-border">
                    <fieldset>
                    <legend>Component Info</legend>
                      <br/>
                      <form action="#" id="formAdd" class="form-horizontal">

                        <input type="hidden" id="base_url" value="<?php echo base_url()?>">
                        <input type="hidden" value="" name="id"/> 
                        <div class="row" id="form-component">
                          <div class="row form-row">
                            <div class="col-md-5">
                              <div class="col-md-12">
                                <label class="control-label col-md-3">Name</label>
                                <div class="col-md-9">
                                    <input name="title" placeholder="Name" class="form-control" type="text" value="">
                                    <span class="help-block"></span>
                                </div>
                              </div>
                              <div class="col-md-12">
                                <label class="control-label col-md-3">Code</label>
                                  <div class="col-md-9">
                                    <input name="code" placeholder="Code" class="form-control" type="text" value="">
                                    <span class="help-block"></span>
                                  </div>
                              </div>
                              <div class="col-md-12">
                                <label class="control-label col-md-3">Type</label>
                                <div class="col-md-9">
                                  <select id="type" name="component_type_id" class="form-control">
                                    <?php if ($component_type->num_rows() > 0) {
                                      foreach ($component_type->result() as $comp_type) {
                                       ?>
                                        <option value="<?php echo $comp_type->id; ?>"><?php echo $comp_type->title; ?></option>
                                    <?php }
                                    } ?>
                                  </select>
                                  <span class="help-block"></span>
                                </div>
                              </div>
                            </div>
                            <div class="col-md-7">
                              <div class="col-md-12">
                                  <label class="control-label col-md-3">Session</label>
                                  <div class="col-md-9">
                                      <select name="session_id" class="form-control">
                                        <?php if ($session_id->num_rows() > 0) {
                                          foreach ($session_id->result() as $sess) {
                                          $selected = ($sess->id == sessNow()) ? 'selected="selected"' : '';
                                            ?>
                                            <option value="<?php echo $sess->id; ?>" <?php echo $selected?>><?php echo $sess->id; ?></option>
                                        <?php }
                                        } ?>
                                      </select>
                                      <span class="help-block"></span>
                                  </div>
                              </div>
                              <div class="col-md-12">
                                  <label class="control-label col-md-3">Attribute</label>
                                  <div class="col-md-9">
                                      <select name="is_annualized" class="form-control">
                                        <option value="0">Iregular</option>
                                        <option value="1">Regular</option>
                                      </select>
                                      <span class="help-block"></span>
                                  </div>
                              </div>
                              <div class="col-md-12">
                                  <label class="control-label col-md-3">Tax</label>
                                  <div class="col-md-9">
                                      <select name="tax_component_id" class="form-control">
                                        <?php if ($tax_component->num_rows() > 0) {
                                          
                                          foreach ($tax_component->result() as $tax_comp) {
                                            ?>
                                            <option value="<?php echo $tax_comp->id; ?>"><?php echo $tax_comp->title; ?></option>
                                        <?php }
                                        } ?>
                                      </select>
                                      <span class="help-block"></span>
                                  </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-12">
                          <div class="row pull-right edit-com">
                            <button type="button" href="javascript:void(0)" title="Save" onclick='addComponent()' class="btn btn-primary">Save</button>
                            <a href="<?php echo base_url('payroll/payroll_component')?>"><button type="button" title="Cancel" class="btn btn-danger" data-dismiss="modal">Cancel</button></a>
                          </div>
                        </div>
                      </form>
                    </fieldset>
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