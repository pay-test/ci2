$(document).ready(function() {
    $(".select2").select2();
    
    $("#section_select").change(function(){
        var id = $(this).val();
        var sess_id = $('#session_select option:selected').val()
        if(id != 0){
           $('#table_matrix').load('payroll_config/get_table_matrix/'+sess_id+'/'+id);
        }
    })
    .change();

    $('td').click(function() {

        // create input for editing
        var editarea = document.createElement('input');
        editarea.setAttribute('type', 'text');

        // put current value in it
        editarea.setAttribute('value', $(this).html());

        // rewrite current value with edit area
        $(this).html(editarea);

        // set focus to newly created input
        $(editarea).focus();

    });
});