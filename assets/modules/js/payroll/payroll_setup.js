var save_method; //for save method string
var table;

$(document).ready(function() {
    $(".period").select2();
    $(".period2").select2()
    .on("change", function (e){
        $.ajax({
            url: 'payroll_setup/ajax_period/'+e.target.value,
            type: 'GET',
            dataType: 'JSON',
            success: function(data){
                if(data.status == 1){
                     $('.radio').find(':radio[name=status][value="1"]').prop('checked', true)
                }else{
                    $('.radio').find(':radio[name=status][value="0"]').prop('checked', true)
                }
                
            },
            error: function(jqXHR, textStatus, errorThrown){

            }
        })
        //console.log("Session: ", e.target.value)
    });

    $("#period").datepicker( {
        format: "mm-yyyy",
        startView: "months", 
        minViewMode: "months",
        autoclose: true,
    });

    $("#form-process").submit(function(e) {
    // ajax adding data to database
    $.ajax({
        url : 'payroll_setup/process',
        type: "POST",
        data: $('#form-process').serialize(),
        dataType: "JSON",
        success: function(data)
        {
            alert('Process Finished');
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error Process');

        }
    });
    e.preventDefault();
    });

    $("#form-period").submit(function(e) {
    // ajax adding data to database
    $.ajax({
        url : 'payroll_setup/set_periode',
        type: "POST",
        data: $('#form-period').serialize(),
        dataType: "JSON",
        success: function(data)
        {
            if(data.is_update == 1){
                alert('Periode status has been updated');
            }else{
                alert('Please set the status first');
            }
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error Apply');

        }
    });
    e.preventDefault();
    });
    
    //set input/textarea/select event when change value, remove class error and remove text help block
    /*$("input").change(function(){
        $(this).parent().parent().removeClass('has-error');
        $(this).next().empty();
    });*/
});



function add_user()
{
    save_method = 'add';
    $('#form')[0].reset(); // reset form on modals
    $('#group_id').val(0);
    $('.form-group').removeClass('has-error'); // clear error class
    $('.help-block').empty(); // clear error string
    $('#modal_form').modal('show'); // show bootstrap modal
    $('.modal-title').text('Add Payroll Group'); // Set Title to Bootstrap modal title
}

function edit_user(id)
{
    save_method = 'update';
    $('#form')[0].reset(); // reset form on modals
    $('.form-group').removeClass('has-error'); // clear error class
    $('.help-block').empty(); // clear error string
    var array_comp = [];
    var array_thp = [];

    //Ajax Load data from ajax
    $.ajax({
        url : "payroll_group/ajax_edit/" + id,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {

            $.ajax({
                url : "payroll_group/render_group_component/"+data.id,
                type : "GET",
                dataType: "JSON",
                success: function(data2)
                {
                    for (index = 0; index < data2.length; ++index) {
                        array_comp.push(data2[index].payroll_component_id);
                        if (data2[index].is_thp == 1) {
                            $(".td_is_thp").find('[value=' + data2[index].payroll_component_id + ']').prop("checked", true);
                        };
                    }
                    $(".td_p_component").find('[value=' + array_comp.join('], [value=') + ']').prop("checked", true);
                    //alert(array_comp);
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    alert('Error get data from ajax');
                }
            });

            $('[name="id"]').val(data.id);
            $('[name="title"]').val(data.title);
            $('[name="code"]').val(data.code);
            $('#modal_form').modal('show'); // show bootstrap modal when complete loaded
            $('.modal-title').text('Edit Group'); // Set title to Bootstrap modal title

        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
    });
}

function reload_table()
{
    table.ajax.reload(null,false); //reload datatable ajax 
}

function save()
{
    $('#btnSave').text('saving...'); //change button text
    $('#btnSave').attr('disabled',true); //set button disable 
    var url;

    if(save_method == 'add') {
        url = "payroll_group/ajax_add";
    } else {
        url = "payroll_group/ajax_update";
    }

    // ajax adding data to database
    $.ajax({
        url : url,
        type: "POST",
        data: $('#form').serialize(),
        dataType: "JSON",
        success: function(data)
        {

            if(data.status) //if success close modal and reload ajax table
            {
                $('#modal_form').modal('hide');

                reload_table();
            }
            else
            {
                for (var i = 0; i < data.inputerror.length; i++) 
                {
                    $('[name="'+data.inputerror[i]+'"]').parent().parent().addClass('has-error'); //select parent twice to select div form-group class and add has-error class
                    $('[name="'+data.inputerror[i]+'"]').next().text(data.error_string[i]); //select span help-block class set text error string
                }
            }
            $('#btnSave').text('save'); //change button text
            $('#btnSave').attr('disabled',false); //set button enable 


        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error adding / update data');
            $('#btnSave').text('save'); //change button text
            $('#btnSave').attr('disabled',false); //set button enable 

        }
    });
}

function delete_user(id)
{
    if(confirm('Are you sure delete this data?'))
    {
        // ajax delete data to database
        $.ajax({
            url : "payroll_group/ajax_delete/"+id,
            type: "POST",
            dataType: "JSON",
            success: function(data)
            {
                //if success reload ajax table
                $('#modal_form').modal('hide');
                reload_table();
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error deleting data');
            }
        });

    }
}

/*************************************
  * Created : Jan 2016
  * Creator : A.Ghanni
*************************************/  
/* Set the defaults for DataTables initialisation */
$.extend( true, $.fn.dataTable.defaults, {
    "sDom": "<'row-fluid'<'span6'l><'span6'f>r>t<'row-fluid'<'span12'p i>>",
    "sPaginationType": "bootstrap",
    "oLanguage": {
        "sLengthMenu": "_MENU_"
    }
} );


/* Default class modification */
$.extend( $.fn.dataTableExt.oStdClasses, {
    "sWrapper": "dataTables_wrapper form-inline"
} );

/* API method to get paging information */
$.fn.dataTableExt.oApi.fnPagingInfo = function ( oSettings )
{
    return {
        "iStart":         oSettings._iDisplayStart,
        "iEnd":           oSettings.fnDisplayEnd(),
        "iLength":        oSettings._iDisplayLength,
        "iTotal":         oSettings.fnRecordsTotal(),
        "iFilteredTotal": oSettings.fnRecordsDisplay(),
        "iPage":          oSettings._iDisplayLength === -1 ?
            0 : Math.ceil( oSettings._iDisplayStart / oSettings._iDisplayLength ),
        "iTotalPages":    oSettings._iDisplayLength === -1 ?
            0 : Math.ceil( oSettings.fnRecordsDisplay() / oSettings._iDisplayLength )
    };
};

/* Bootstrap style pagination control */
$.extend( $.fn.dataTableExt.oPagination, {
    "bootstrap": {
        "fnInit": function( oSettings, nPaging, fnDraw ) {
            var oLang = oSettings.oLanguage.oPaginate;
            var fnClickHandler = function ( e ) {
                e.preventDefault();
                if ( oSettings.oApi._fnPageChange(oSettings, e.data.action) ) {
                    fnDraw( oSettings );
                }
            };

            $(nPaging).addClass('pagination').append(
                '<ul>'+
                    '<li class="prev disabled"><a href="#"><i class="fa fa-chevron-left"></i></a></li>'+
                    '<li class="next disabled"><a href="#"><i class="fa fa-chevron-right"></i></a></li>'+
                '</ul>'
            );
            var els = $('a', nPaging);
            $(els[0]).bind( 'click.DT', { action: "previous" }, fnClickHandler );
            $(els[1]).bind( 'click.DT', { action: "next" }, fnClickHandler );
        },

        "fnUpdate": function ( oSettings, fnDraw ) {
            var iListLength = 5;
            var oPaging = oSettings.oInstance.fnPagingInfo();
            var an = oSettings.aanFeatures.p;
            var i, ien, j, sClass, iStart, iEnd, iHalf=Math.floor(iListLength/2);

            if ( oPaging.iTotalPages < iListLength) {
                iStart = 1;
                iEnd = oPaging.iTotalPages;
            }
            else if ( oPaging.iPage <= iHalf ) {
                iStart = 1;
                iEnd = iListLength;
            } else if ( oPaging.iPage >= (oPaging.iTotalPages-iHalf) ) {
                iStart = oPaging.iTotalPages - iListLength + 1;
                iEnd = oPaging.iTotalPages;
            } else {
                iStart = oPaging.iPage - iHalf + 1;
                iEnd = iStart + iListLength - 1;
            }

            for ( i=0, ien=an.length ; i<ien ; i++ ) {
                // Remove the middle elements
                $('li:gt(0)', an[i]).filter(':not(:last)').remove();

                // Add the new list items and their event handlers
                for ( j=iStart ; j<=iEnd ; j++ ) {
                    sClass = (j==oPaging.iPage+1) ? 'class="active"' : '';
                    $('<li '+sClass+'><a href="#">'+j+'</a></li>')
                        .insertBefore( $('li:last', an[i])[0] )
                        .bind('click', function (e) {
                            e.preventDefault();
                            oSettings._iDisplayStart = (parseInt($('a', this).text(),10)-1) * oPaging.iLength;
                            fnDraw( oSettings );
                        } );
                }

                // Add / remove disabled classes from the static elements
                if ( oPaging.iPage === 0 ) {
                    $('li:first', an[i]).addClass('disabled');
                } else {
                    $('li:first', an[i]).removeClass('disabled');
                }

                if ( oPaging.iPage === oPaging.iTotalPages-1 || oPaging.iTotalPages === 0 ) {
                    $('li:last', an[i]).addClass('disabled');
                } else {
                    $('li:last', an[i]).removeClass('disabled');
                }
            }
        }
    }
} );

/*
 * TableTools Bootstrap compatibility
 * Required TableTools 2.1+
 */

    // Set the classes that TableTools uses to something suitable for Bootstrap
    $.extend( true, $.fn.DataTable.TableTools.classes, {
        "container": "DTTT ",
        "buttons": {
            "normal": "btn btn-white",
            "disabled": "disabled"
        },
        "collection": {
            "container": "DTTT_dropdown dropdown-menu",
            "buttons": {
                "normal": "",
                "disabled": "disabled"
            }
        },
        "print": {
            "info": "DTTT_print_info modal"
        },
        "select": {
            "row": "active"
        }
    } );

    // Have the collection use a bootstrap compatible dropdown
    $.extend( true, $.fn.DataTable.TableTools.DEFAULTS.oTags, {
        "collection": {
            "container": "ul",
            "button": "li",
            "liner": "a"
        }
    });
/*************************************
                  End
*************************************/  