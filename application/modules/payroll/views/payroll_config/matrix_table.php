<table class="table table-hover table-condensed" id="tbl">
  <thead>
    <tr>
      <th style="width:15%">Job Class</th>
      <th style="width:15%">Job level</th>
      <th style="width:15%">Job Value</th>
      <th style="width:15%">Value</th>
      <th style="width:15%">Value Min</th>
      <th style="width:15%">Value Max</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach($matrix->result() as $m):?>
      <tr>
        <td><?php echo $m->job_class?></td>
        <td><?php echo $m->job_level?></td>
        <td><a href="javascript:void(0);" title="Edit"><?php echo $m->job_value?></a></td>
        <td><?php echo $m->value?></td>
        <td ><?php echo $m->value_min?></td>
        <td><?php echo $m->value_max?></td>
      </tr>
    <?php endforeach;?>
  </tbody>
</table>
<script type="text/javascript">
$(document).ready(function() {
    var sess_id = $('#session_select option:selected').val()
    var id = $('#section_select option:selected').val()
    $('td').dblclick(function() {
        var editarea = document.createElement('input');
        editarea.setAttribute('type', 'text');

        editarea.setAttribute('value', $(this).html());

        $(this).html(editarea);

        $(editarea).focus();

    });
});
</script>