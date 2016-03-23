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
      <li><a href="" class="active"><?php echo $title; ?></a> </li>
    </ul>
    <div class="page-title"> <a href="<?php echo base_url('payroll/payroll_component')?>"><i class="icon-custom-left"></i></a>
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
              <div class="col-md-12">
                <div class="grid simple">
                  <div class="grid-body no-border">
                    <fieldset>
                    <legend>Component Info</legend>      
                      <div class="row">
                        <button type="button" id="btnEdit" href="javascript:void(0)" title="Edit" onclick='edit()' class="btn btn-info pull-right"><i class="fa fa-pencil"></i> Edit</button>
                      </div>
                      <br/>
                      <form action="#" id="form" class="form-horizontal">
                        <input type="hidden" value="" name="id"/> 
                        <div class="row" id="form-component">
                          <div class="row form-row">
                            <div class="col-md-5">
                              <div class="col-md-12">
                                <label class="control-label col-md-3">Name</label>
                                <div class="col-md-9">
                                    <input name="title" placeholder="Name" class="form-control" type="text" value="<?php echo $data->title?>" disabled>
                                    <span class="help-block"></span>
                                </div>
                              </div>
                              <div class="col-md-12">
                                <label class="control-label col-md-3">Code</label>
                                  <div class="col-md-9">
                                    <input name="code" placeholder="Code" class="form-control" type="text" value="<?php echo $data->code?>" disabled>
                                    <span class="help-block"></span>
                                  </div>
                              </div>
                              <div class="col-md-12">
                                <label class="control-label col-md-3">Type</label>
                                <div class="col-md-9">
                                  <select id="type" name="component_type_id" class="form-control" disabled>
                                    <?php if ($component_type->num_rows() > 0) {
                                      foreach ($component_type->result() as $comp_type) {
                                        $selected = ($comp_type->id == $data->component_type_id) ? 'selected="selected"' : '';
                                       ?>
                                        <option value="<?php echo $comp_type->id; ?>" <?php echo $selected?>><?php echo $comp_type->title; ?></option>
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
                                      <input name="session" placeholder="session" class="form-control" type="text" readonly="readonly" value="<?php echo $session_id?>">
                                      <span class="help-block"></span>
                                  </div>
                              </div>
                              <div class="col-md-12">
                                  <label class="control-label col-md-3">Attribute</label>
                                  <div class="col-md-9">
                                      <select name="is_annualized" class="form-control" disabled>
                                        <option value="0" <?php echo ($data->is_annualized == 0) ? 'selected="selected"' : '';?>>Not Annualized</option>
                                        <option value="1" <?php echo ($data->is_annualized == 1) ? 'selected="selected"' : '';?>>Annualized</option>
                                      </select>
                                      <span class="help-block"></span>
                                  </div>
                              </div>
                              <div class="col-md-12">
                                  <label class="control-label col-md-3">Tax</label>
                                  <div class="col-md-9">
                                      <select name="tax_component_id" class="form-control" disabled>
                                        <?php if ($tax_component->num_rows() > 0) {
                                          
                                          foreach ($tax_component->result() as $tax_comp) {
                                              $selected = ($tax_comp->id == $data->tax_component_id) ? 'selected="selected"' : '';
                                            ?>
                                            <option value="<?php echo $tax_comp->id; ?>" <?php echo $selected ?>><?php echo $tax_comp->title; ?></option>
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
                          <div class="row pull-right edit-com" style="display:none">
                            <button type="button" href="javascript:void(0)" title="Save" onclick='save(<?php echo $data->id?>)' class="btn btn-primary">Save</button>
                            <button type="button" id="btnCancel" href="javascript:void(0)" title="Cancel" onclick='cancel()' class="btn btn-danger" data-dismiss="modal">Cancel</button>
                          </div>
                        </div>
                      </form>
                      <legend>Formula</legend>
                      <div class="row">
                      <button type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#addModal"><i class="fa fa-plus"></i> add new</button>
                      </div>
                      <br/>
                      <?php if($data2->num_rows()>0){
                        $last_date = $this->db->select('id, from')->where('payroll_component_session_id', $component_session_id)->order_by('from', 'asc')->get('payroll_component_value')->last_row()->id;//print_mz($last_date);
                            foreach($data2->result() as $d):
                              $dis = ($last_date == $d->id) ? "" : 'style="display:none"';
                        ?>
                        <div class="panel-group">
                          <div class="panel panel-default">
                            <div class="panel-heading2">
                              <h4 class="panel-title">
                                <a href="javascript:void(0)" title="Detail" onclick='formulaDetail(<?php echo $d->id ?>)'>
                                   <span class="m-r-50"><i class="fa fa-calendar"></i> Valid Date</span>
                                   <span class="m-r-50" id="from<?php echo $d->id ?>"> From : <?php echo $d->from ?></span>
                                   <span class="m-r-50" id="to<?php echo $d->id ?>"> To : <?php echo $d->to ?></span>
                                </a>
                              </h4>
                            </div>
                            <div id="panel-body<?php echo $d->id ?>" class="panel-collapse collapse in" <?php echo $dis?>>
                              <div class="panel-body">
                                <div class="row">
                                  <button type="button" id="btnEdit<?php echo $d->id?>" href="javascript:void(0)" title="Edit" onclick='formulaEdit(<?php echo $d->id ?>)' class="btn btn-info pull-right"><i class="fa fa-pencil"></i> Edit</button>
                                </div>
                                <br/>
                                <form id="form-edit<?php echo $d->id?>">
                                  <input type="hidden" name="component_session_id<?php echo $d->id?>" value=<?php echo $component_session_id?>>
                                  <div class="edit-com<?php echo $d->id?>" style="display:none">
                                    <div class="col-md-12">
                                      <label class="col-md-12 control-label text-left">Valid Date</label>
                                    </div>
                                    <div class="col-md-12">
                                      <label class="col-md-2 control-label text-left">From</label>
                                      <div class="col-md-3">
                                        <div id="datepicker_start" class="input-append date success no-padding">
                                          <input type="text" class="form-control tgl" name="from<?php echo $d->id?>" value="<?php echo $d->from?>" required>
                                          <span class="add-on"><span class="arrow"></span><i class="fa fa-th"></i></span> 
                                        </div>
                                      </div>
                                      <label class="col-md-2 control-label text-center">To</label>
                                      <div class="col-md-3">
                                        <div id="datepicker_end" class="input-append date success no-padding">
                                            <input type="text" class="form-control tgl" name="to<?php echo $d->id?>" value="<?php echo $d->to?>">
                                            <span class="add-on"><span class="arrow"></span><i class="fa fa-th"></i></span> 
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                    <div class="col-md-12">
                                        <label class="control-label col-md-1">Formula</label>
                                        <div class="col-md-1"></div>
                                        <div class="col-md-10">
                                            <textarea id="formula<?php echo $d->id?>" name="formula<?php echo $d->id?>" placeholder="Formula" class="form-control" disabled><?php echo $d->formula?></textarea> 
                                            <span class="help-block"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="control-label col-md-2 text-left">Have Condition</label>
                                        <div class="col-md-2" id="status">
                                            <input value="1" name="is_condition<?php echo $d->id?>" id="open<?php echo $d->id?>" type="radio" onclick='showMinMax(<?php echo $d->id ?>)' disabled <?php echo ($d->is_condition==1) ? 'checked="checked"' : ''?>> Yes
                                            <input value="0" name="is_condition<?php echo $d->id?>" id="close<?php echo $d->id?>" onclick='showMinMax(<?php echo $d->id ?>)' type="radio" disabled <?php echo ($d->is_condition==0)    ? 'checked="checked"' : ''?>> No
                                        </div>
                                        <?php $dis2 = (1 == $d->is_condition) ? "" : 'style="display:none"';?>
                                      <div id="con<?php echo $d->id?>" <?php echo $dis2?>>
                                          <label class="control-label col-md-2">Min Value</label>
                                          <div class="col-md-2">
                                              <input id="min<?php echo $d->id?>" name="min<?php echo $d->id?>" placeholder="Min" class="form-control text-right money" type="text" value="<?php echo number_format($d->min, 2)?>" disabled>
                                              <span class="help-block"></span>
                                          </div>
                                          <label class="control-label col-md-2">Max Value</label>
                                          <div class="col-md-2">
                                              <input id="max<?php echo $d->id?>" name="max<?php echo $d->id?>" placeholder="Max" class="form-control text-right money" type="text" value="<?php echo number_format($d->max, 2)?>" disabled>
                                              <span class="help-block"></span>
                                          </div>
                                      </div>   
                                    </div>
                                    <div class="col-md-12">
                                      <div class="row pull-right edit-com<?php echo $d->id?>" style="display:none">
                                        <button type="button" id="btnSave<?php echo $d->id?>" href="javascript:void(0)" title="Edit" onclick='formulaSave(<?php echo $d->id ?>)' class="btn btn-primary">Save</button>
                                        <button type="button" id="btnCancel<?php echo $d->id?>" href="javascript:void(0)" title="Edit" onclick='formulaCancel(<?php echo $d->id ?>)' class="btn btn-danger" data-dismiss="modal">Cancel</button>
                                      </div>
                                    </div>
                                </form>
                              </div>
                            </div>
                          </div>
                        </div>
                        <hr/>
                        <?php endforeach;  }else{?>
                          <div class="col-md-12 text-center">This component does not have formula</div>
                        <?php } ?>
                      </fieldset>
                  </div>
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
                            <td align="left">Basic Salary</td>
                          </tr>
                          <tr>
                            <td>HOUS</td>
                            <td align="left">Housing Allowance</td>
                          </tr>
                          <tr>
                            <td>UMK</td>
                            <td align="left">UMK</td>
                          </tr>
                          <tr>
                            <td>K0</td>
                            <td align="left">PTKP - Married Person With ZERO Dependant</td>
                          </tr>
                          <tr>
                            <td>K1</td>
                            <td align="left">PTKP - Married Person With ONE Dependant</td>
                          </tr>
                          <tr>
                            <td>K2</td>
                            <td align="left">PTKP - Married Person With TWO Dependant</td>
                          </tr>
                          <tr>
                            <td>K3</td>
                            <td align="left">PTKP - Married Person With THREE Dependant</td>
                          </tr>
                          </tbody>
                        </table>
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
  </div>
</div>

<!-- Bootstrap modal -->
<div class="modal fade" id="addModal" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h3 class="modal-title">Add Formula</h3>
      </div>
      <div class="modal-body form">
        <form action="#" id="form-add" class="form-horizontal">
          <input type="hidden" name="component_session_id" value=<?php echo $component_session_id?>>
          <div class="col-md-12">
            <label class="col-md-12 control-label text-left">Valid Date</label>
          </div>
          <div class="col-md-12">
            <label class="col-md-2 control-label text-left">From</label><div class="col-md-1"></div>
            <div class="col-md-3">
              <div id="datepicker_start" class="input-append date success no-padding">
                <input type="text" class="form-control tgl" name="from" value="" required>
                <span class="add-on"><span class="arrow"></span><i class="fa fa-th"></i></span> 
              </div>
            </div>
            <label class="col-md-2 control-label text-center">To</label>
            <div class="col-md-3">
              <div id="datepicker_end" class="input-append date success no-padding">
                  <input type="text" class="form-control tgl" name="to" value="">
                  <span class="add-on"><span class="arrow"></span><i class="fa fa-th"></i></span> 
              </div>
            </div>
          </div>
          <div class="col-md-12">
            <label class="control-label col-md-1">Formula</label>
            <div class="col-md-2"></div>
            <div class="col-md-9">
              <textarea name="formula" placeholder="Formula" class="form-control"></textarea> 
              <span class="help-block"></span>
            </div>
          </div>
          <div class="col-md-12">
            <label class="control-label col-md-3 text-left">Have Condition</label>
            <div class="col-md-3" id="status">
                <input value="1" name="is_condition" id="open" type="radio"> Yes
                <input checked="checked" value="0" name="is_condition" id="close" type="radio"> No
            </div>
          </div>
          <div class="col-md-12">
            <div id="con" style="display: none">
              <label class="control-label col-md-3 text-left">Min Value</label>
              <div class="col-md-3">
                  <input name="min" placeholder="Min" class="form-control text-right money" type="text">
                  <span class="help-block"></span>
              </div>
              <label class="control-label col-md-3 text-left">Max Value</label>
              <div class="col-md-3">
                  <input name="max" placeholder="Max" class="form-control text-right money" type="text">
                  <span class="help-block"></span>
              </div>
            </div>   
          </div>             
        </form>
      </div>
      <div class="modal-footer">
        <div class="col-md-12 pull-right">
          <button type="button" id="btnSave" class="btn btn-primary">Save</button>
          <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
        </div>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- End Bootstrap modal -->