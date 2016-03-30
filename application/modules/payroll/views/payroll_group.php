
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
        <li><a href="<?php echo base_url('payroll/payroll_group')?>" class="active"><?php echo $title.' - Group'; ?></a> </li>
      </ul>
      <div class="row-fluid">
        <div class="span12">
          <div class="grid simple ">
            <div class="grid-title">
              <h4>Table <span class="semi-bold"><?php echo $page_title ?></span></h4>
            </div>

            <div class="grid-body ">
            <button type="button" class="btn btn-primary" onclick="add_user()"><i class="fa fa-plus"></i> add</button><br/><br/>
              <table class="table table-hover table-condensed" id="table">
                <thead>
                  <tr>
                    <th style="width:5%">No</th>
                    <th style="width:25%">Name</th>
                    <th style="width:25%">Code</th>
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
<!-- END CONTAINER -->


<!-- Bootstrap modal -->
  <div class="modal fade" id="modal_form" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h3 class="modal-title">Form</h3>
        </div><form action="#" id="form" class="form">
          <input type="hidden" name="employee_id" value="">
          <input type="hidden" name="period_id" value="">
          <div class="modal-body">
              <input id="group_id" type="hidden" value="" name="id"/>
          <div class="col-md-12">
              <label class="control-label col-md-4">Name</label>
              <div class="col-md-8">
                  <select name="job_class_id" class="job_class_id form-control select2">
                    <?php if ($job_class) {
                      foreach ($job_class as $jc) { ?>
                        <option value="<?php echo $jc['job_class_id']; ?>"><?php echo $jc['job_class_nm']." (".$jc['job_class_cd'].")"; ?></option>
                      <?php }
                    } ?>
                  </select>
                  <span class="help-block"></span>
              </div>
          </div>
          <div class="col-md-12">
              <label class="control-label col-md-4">Component</label>
          </div>
          <div class="row">
            <div class="col-md-12">
              <table class="table table-bordered" id="table-group-component">
                <thead>
                  <tr>
                    <th style="width:5%">No</th>
                    <th style="width:25%">Name</th>
                    <th style="width:15%">Code</th>
                    <th style="width:15%">Type</th>
                    <th style="width:15%"> 
                        <input id="check-inc" type="checkbox">
                      Include
                      
                    </th>
                    <th style="width:15%"><input id="check-thp" type="checkbox"> THP</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if ($p_component): ?>
                  <?php $i = 1; foreach ($p_component as $pcomp): ?>
                  <tr>
                    <td><?php echo $i; ?></td>
                    <td><?php echo $pcomp['title']; ?></td>
                    <td><?php echo $pcomp['code']; ?></td>
                    <?php if ($pcomp['component_type_id'] == 1) {
                      $component_t = "Income";
                    }elseif ($pcomp['component_type_id'] == 2) {
                      $component_t = "Deduction";
                    }else{
                      $component_t = "All";
                    } ?>
                    <td><?php echo $component_t; ?></td>
                    <td class="td_p_component">
                      <div class="checkbox check-success">
                        <input type="checkbox" id="checkbox_comp<?php echo $i; ?>" value="<?php echo $pcomp['id']; ?>" name="p_component[]" class="inc">
                        <label for="checkbox_comp<?php echo $i; ?>"></label>
                      </div>
                    </td>
                    <td class="td_is_thp">
                      <div class="checkbox check-success">
                        <input type="checkbox" id="checkbox_thp<?php echo $i; ?>" value="<?php echo $pcomp['id']; ?>" name="is_thp[]" class="thp">
                        <label for="checkbox_thp<?php echo $i; ?>"></label>
                      </div>
                    </td>
                  </tr>
                  <?php $i++; endforeach ?>
                  <?php endif ?>
                </tbody>
              </table>
              <div class="pull-right">
                <input type="hidden" value="" name="id" class="form-control"> 
                <button type="button" id="btnSave" onclick="save()" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
              </div>
              </div>
            </div>
          </div>

        </form>
      </div>
    </div>
  </div>
  <!-- End Bootstrap modal -->

  