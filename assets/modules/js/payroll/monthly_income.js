var save_method; //for save method string
var table;

$(document).ready(function() {
    $(".select2").select2();
    $('.auto').maskMoney();
    //datatables
    $('#btnAdd').on('click', function () {
        $(document).find("select.select2").select2();
        $(document).find('.auto').maskMoney();
        $('#btnRemove').show();
    });

    table = $('#table').DataTable({ 

        "processing": true, //Feature control the processing indicator.
        "serverSide": true, //Feature control DataTables' server-side processing mode.
        "order": [], //Initial no order.
        "scrollY":        "300px",
        "scrollCollapse": true,
        // Load data for the table's content from an Ajax source
        "ajax": {
            "url": "monthly_income/ajax_list/",
            "type": "POST"
        },
        "sDom": "<'row'<'col-md-5'l><'col-md-7'f>r><'row'<'pull-left m-l-20'pi>>t",
        //Set column definition initialisation properties.
        "columnDefs": [
        { 
            "targets": [0, 2, -1], //last column
            "orderable": false, //set not orderable
        },
        { "sClass": "text-center", "aTargets": [-1] }
        ],

    });

    $('#table_wrapper .dataTables_length select').addClass("select2-wrapper span12");
    $(".select2-wrapper").select2({minimumResultsForSearch: -1});
});

$("#group").change(function() {
        $("#component_table_body").empty();
        var Id = $(this).val();
        getComponent(Id);
    })
    .change();

function getComponent(Id)
{
    $.ajax({
        type: 'POST',
        url: 'monthly_income/get_component_table/',
        data: {id : Id},
        success: function(data) {
            $('#component_table_body').html(data);
        }
    });
}

$("#periode").change(function() {
        var Id = $(this).val();
        getStatus(Id);
    })
    .change();

function getStatus(Id)
{
    $.ajax({
        type: 'POST',
        url: 'monthly_income/get_periode_status/',
        data: {id : Id},
        success: function(data) {
            $('#periode-status').text(data);
        }
    });
}

function print(id)
{
    var period_id = $('#periode option:selected').val()
    //Ajax Load data from ajax
    if(period_id == 0){
        alert('Please Choose Period !!');
    }else{
        $.ajax({
            url : "monthly_income/cek_period/" + id +"/" + period_id,
            type: "GET",
            dataType: "JSON",
            success: function(data)
            {
                if(data < 1){
                    alert('Employee Payroll Is Empty In This Period');
                }else{
                    window.open("monthly_income/print_slip/" + id +"/" + period_id,'_blank');
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error get data from ajax');
            }
        });
    }
}
function edit_user(id)
{
    save_method = 'update';
    $("#component_table_body").empty();
    $('#form')[0].reset(); // reset form on modals
    $('.form-group').removeClass('has-error'); // clear error class
    $('.help-block').empty(); // clear error string
    var period_id = $('#periode option:selected').val()
    //Ajax Load data from ajax
    if(period_id == 0){
        alert('Please Choose Period !!');
    }else{
        $.ajax({
            url : "monthly_income/ajax_edit/" + id +"/" + period_id,
            type: "GET",
            dataType: "JSON",
            success: function(data)
            {
                if(data.master_num_rows < 1){
                    alert('Employee Payroll Master Is Empty');
                }else{
                    var period = $('#periode option:selected').text()
                    var d = data.data2;
                    var period = $('#periode option:selected').text()
                    $('[name="period_id"]').val(period_id);
                    $('[name="employee_id"]').val(data.data1.employee_id);
                    $('[name="user_nm"]').val(data.data1.user_nm);
                    $('[name="person_nm"]').val(data.data1.person_nm);
                    $('[name="group_id"]').val(data.data1.group_id);
                    $('[name="payroll_ptkp_id"]').select2().select2('val',data.data1.payroll_ptkp_id);
                    $('[name="currency"]').select2().select2('val',data.data1.payroll_currency_id);
                    $('[name="is_expatriate"]').select2().select2('val',data.data1.is_expatriate);
                    $('[name="tax_method"]').select2().select2('val',data.data1.payroll_tax_method_id);
                    $('[name="group_title"]').val(data.data1.group_title);
                    $('#modal_form').modal('show'); // show bootstrap modal when complete loaded
                    $('.modal-title').text('Monthly Income '+period); // Set title to Bootstrap modal title
                    if(data.data2 != null)drawTable(data.data2);
                    //getComponentVal(data.data1.group_id,data.data2);
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error get data from ajax');
            }
        });
    }

    function drawTable(data) {
    for (var i = 0; i < data.length; i++) {
        drawRow(data[i]);
    }
    }

    function drawRow(rowData) {
        var row = $("<tr />")
        $("#component_table_body").append(row);
        $(document).find('.auto').maskMoney({allowNegative:true});
        var v = parseFloat(rowData.value);
        row.append($("<td>" + rowData.component + "</td>"));
        row.append($("<td>" + rowData.code + "</td>"));
        row.append($("<td>" + rowData.component_type + "</td>"));
        row.append($("<td>" + "<input type='hidden' name='component_id[]'' value='"+rowData.component_id+"'><input type='hidden' name='monthly_component_id[]' value='"+rowData.id+"'><input class='text-right auto' type='text' name='value[]' value='"+v.formatMoney() +"'></td>"));
    }

}
Number.prototype.formatMoney = function(c, d, t){
var n = this, 
    c = isNaN(c = Math.abs(c)) ? 2 : c, 
    d = d == undefined ? "." : d, 
    t = t == undefined ? "," : t, 
    s = n < 0 ? "-" : "", 
    i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", 
    j = (j = i.length) > 3 ? j % 3 : 0;
   return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
 };
function reload_table()
{
    table.ajax.reload(null,false); //reload datatable ajax 
}

function save()
{
    $('#btnSave').text('saving...'); //change button text
    $('#btnSave').attr('disabled',true); //set button disable 
    var url;

    /*
    if(save_method == 'add') {
        url = "monthly_income/ajax_add";
    } else {
        url = "monthly_income/ajax_update";
    }
    */

    // ajax adding data to database
    $.ajax({
        url : 'monthly_income/ajax_update',
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
            url : "monthly_income/ajax_delete/"+id,
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