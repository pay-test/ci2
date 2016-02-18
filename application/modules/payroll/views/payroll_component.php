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
            <button type="button" class="btn btn-primary" onclick="add_user()"><i class="fa fa-plus"></i> add</button><br/><br/>
              <table class="table table-hover table-condensed" id="table">
                <thead>
                  <tr>
                    <th style="width:5%">No</th>
                    <th style="width:20%">Name</th>
                    <th style="width:10%">Code</th>
                    <th style="width:15%">Component Type</th>
                    <th style="width:15%">Attribute</th>
                    <th style="width:20%">Tax Component</th>
                    <th style="width:15%">Action</th>
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
                    <div class="row form-row">
                      <div class="col-md-6">
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
                            <label class="control-label col-md-3">Type</label>
                            <div class="col-md-9">
                                <select name="component_type_id" class="form-control select2">
                                  <?php if ($component_type->num_rows() > 0) {
                                    foreach ($component_type->result() as $comp_type) { ?>
                                      <option value="<?php echo $comp_type->id; ?>"><?php echo $comp_type->title; ?></option>
                                  <?php }
                                  } ?>
                                </select>
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                          <label class="control-label col-md-6 text-left">Have Condition</label>
                          <div class="col-md-6">
                            <div id="status">
                                <input value="1" name="is_condition" id="open" type="radio"> Yes
                                <input checked="checked" value="0" name="is_condition" id="close" type="radio"> No
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label col-md-3">Session</label>
                            <div class="col-md-9">
                                <input name="session" placeholder="session" class="form-control" type="text" readonly="readonly">
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
                        <div class="form-group">
                            <label class="control-label col-md-3">Tax</label>
                            <div class="col-md-9">
                                <select name="tax_component_id" class="form-control select2">
                                  <?php if ($tax_component->num_rows() > 0) {
                                    foreach ($tax_component->result() as $tax_comp) { ?>
                                      <option value="<?php echo $tax_comp->id; ?>"><?php echo $tax_comp->title; ?></option>
                                  <?php }
                                  } ?>
                                </select>
                                <span class="help-block"></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-3">Formula</label>
                            <div class="col-md-9">
                                <input name="formula" placeholder="Formula" class="form-control" type="text">
                                <span class="help-block"></span>
                            </div>
                        </div>

                        <div id="con" style="display: none">
                          <div class="form-group">
                            <label class="control-label col-md-3">Min Value</label>
                            <div class="col-md-9">
                                <input name="min" placeholder="Min" class="form-control text-right money" type="text">
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Max Value</label>
                            <div class="col-md-9">
                                <input name="max" placeholder="Max" class="form-control text-right money" type="text">
                                <span class="help-block"></span>
                            </div>
                        </div>
                        </div>
                      </div>
                      <div class="col-md-12 pull-center">
                        <table class="table table-hover table-condensed" id="table" style="display:none;">
                          <thead>
                            <tr>
                              <th style="width:30%">Job Value</th>
                              <th style="width:30%">Is Formula</th>
                              <th style="width:30%">Value/Formula</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php if($component_job_value->num_rows() > 0):
                            foreach($component_job_value->result() as $row):
                            ?>
                              <tr>
                                <td><?php echo $row->job_value?></td>
                                <td>
                                  <div class="checkbox check-success">
                                        <input type="checkbox" id="checkbox_comp<?php echo $i; ?>" value="<?php echo $pcomp['id']; ?>" name="p_component[]" class="inc">
                                          <label for="checkbox_comp<?php echo $i; ?>"></label>
                                      </div>
                                </td>
                                <td><input class="form-control" type="text" /></td>
                              </tr>
                              <?php endforeach;else:
                              foreach($job_value->result() as $j):
                              ?>
                                <tr>
                                  <td>
                                    <input class="form-control" type="text" name="job_value_id[]" value="<?php echo $j->id?>" />
                                    <?php echo $j->title?>
                                  </td>
                                  <td>
                                      <input type="checkbox" name="checkbox1_checkbox[]" id="checkbox1_checkbox" class="checkbox1" />
                                      <input type="hidden" name="checkbox1[]" value="0" />
                                  </td>
                                  <td>
                                    <input class="form-control" type="text" name="value[]" value="" />
                                  </td>
                                </tr>
                                <?php endforeach;endif;?>
                          </tbody>
                        </table>
                      </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
              <div class="row">
                <div class="col-md-6">
                  <div class="panel-group" id="accordion" data-toggle="collapse">
                    <div class="panel panel-default">
                      <div class="panel-heading">
                        <h4 class="panel-title">
                        <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
                        Formulas Legend&nbsp;&nbsp;&nbsp; 
                        </a>
                        </h4>
                      </div>
                      <div id="collapseOne" class="panel-collapse collapse">
                      <div class="panel-body">
                        <table class="table table-bordered">
                          <thead>
                            <th width="30%">Code</th>
                            <th width="70%">Description</th>
                          </thead>
                          <tbody>
                          <tr>
                            <td>BWGS</td>
                            <td align="left">Base Salary</td>
                          </tr>
                          <tr>
                            <td>HOUS</td>
                            <td align="left">Housing Allowance</td>
                          </tr>
                          </tbody>
                        </table>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>


                <div class="col-md-6">
                  <button type="button" id="btnSave" onclick="save()" class="btn btn-primary">Save</button>
                  <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                </div>

              </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- End Bootstrap modal -->