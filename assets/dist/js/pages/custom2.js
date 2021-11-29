function addInputFieldRequisition(t) {

    for (var e = $("#normalRequisition tbody tr").length + 1, a = $("#txfieldnum").val(), l = "", n = 0; n < a; n++)
    {
        l += '<input id="total_tax' + n + "_" + e + '" class="total_tax' + n + "_" + e + ' valid_number" type="hidden"><input id="all_tax' + n + "_" + e + '" class="total_tax' + n + '" type="hidden" name="tax[]">';
    }
    if (500 == e)
        alert("You have reached the limit of adding " + e + " inputs");
    else {
        var o = "product_name_" + e,
                i = 5 * e,
                r = document.createElement("tr"),
                c = i + 1,
                s = i + 2,
                d = i + 3,
                u = i + 4,
                m = i + 5,
                p = i + 6,
                _ = i + 7,
                v = i + 8,
                f = i + 9,
                h = i + 10,
                x = i + 11;
        r.innerHTML = "<td><input type='text' name='product_name[]' onkeyup='requisition_productList(" + e + ")' onkeypress='requisition_productList(" + e + ")' class='form-control productSelection' placeholder='Medicine Name' id='" + o + "' required tabindex='" + c + "'><input type='hidden' class='autocomplete_hidden_value  product_id_" + e + "' name='product_id[]' id='product_id_" + e + "'/></td>" +
                "<td style='display: none'><select class='form-control select2' id='batch_id_" + e + "' name='batch_id[]'  onchange='product_stock_requsition(" + e + ")' tabindex='" +
                s + "'><option></option></select></td>" +
                "<td> <input type='text' name='available_quantity[]' class='form-control text-right available_quantity_" + e + "' value='' readonly='' id='available_quantity_" + e + "'/></td>" +
                "<td style='display: none' id='expire_date_" + e + "'></td>" +
                "<td><input name='product_unit[]' class='form-control text-right unit_" + e + " valid' value='None' readonly='' aria-invalid='false' type='text'></td>" +
                "<td><input name='strip[]' id='strip_" + e + "' class='form-control text-right strip_" + e + " valid' value='None' readonly='' aria-invalid='false' type='text'></td>" +
                "<td> <input type='text' name='product_quantity[]' onkeyup='quantity_calculate_invoice(" + e + ");' onchange='quantity_calculate_invoice(" + e + ");'  id='total_qntt_" + e + "' class='total_qntt_" + e + " form-control text-right valid_number' placeholder='0.00' min='0' tabindex='" + d + "' required/></td>" +
                "<td><input type='text' name='box_quantity[]' onkeyup='quantity_calculate_invoice(" + e + "), checkqty_invoice(" + e + ");' onchange='quantity_calculate_invoice(" + e + ");' class='form-control text-right' id='box_qty_" + e + "' placeholder='0.00' min='0'  readonly='' /><input type='hidden' id='u_box_" + e + "' name='b_qty'/></td>" +
                "<td>" + l + "<input type='hidden' id='all_discount_" + e + "' class='total_discount dppr'/><a tabindex='" + p + "' style='text-align: right;' class='btn btn-danger-soft'  value='Delete' onclick='deleteRowinvoice(this)'><i class='far fa-trash-alt'></i></a></td>",
                document.getElementById(t).appendChild(r),
                document.getElementById(o).focus(),
                document.getElementById("add_requisition_item").setAttribute("tabindex", _),
                $(".select2").select2(),
                document.getElementById("add_requisition").setAttribute("tabindex", x),
                e++;
    }
}

function requisition_productList(t) {
    var e = "price_item" + t,
            a = $("#base_url").val(),
            l = "unit_" + t,
            n = "batch_id_" + t,
            o = $('[name="csrf_test_name"]').val(),
            i = {
                minLength: 0,
                source: function (e, l) {
                    var n = $("#product_name_" + t).val();
                    $.ajax({
                        url: a + "/requisition/search_medicine",
                        method: "post",
                        dataType: "json",
                        data: {term: e.term, product_name: n, app_csrf: o},
                        success: function (t) {
                            l(t);
                        },
                    });
                },
                focus: function (t, e) {
                    return $(this).val(e.item.label), !1;
                },
                select: function (a, i) {
                    $(this).parent().parent().find(".autocomplete_hidden_value").val(i.item.value), $(this).val(i.item.label);
                    var r = i.item.value,
                            c = "u_box_" + t,
                            s = $(".baseUrl").val(),
                            d = "available_quantity_" + t,
                            strip = "strip_" + t;
                    return (
                            $.ajax({
                                type: "POST",
                                url: s + "/requisition/medicine_details_data",
                                data: {product_id: r, app_csrf: o},
                                cache: !1,
                                success: function (a) {

                                    for (var o = jQuery.parseJSON(a), i = 0; i < o.txnmber; i++) {
                                        var r = o.taxdta[i];
                                        $("." + ("total_tax" + i + "_" + t)).val(r);
                                    }
                                    $("." + e).val(o.price), $("." + l).val(o.unit), $("#" + n).html(o.batch), $("#" + c).val(o.box_qty), $("#" + d).val(o.total_product), $("#" + strip).val(o.strip);
                                },
                            }),
                            $(this).unbind("change"),
                            !1
                            );
                },
            };
    $("body").on("keypress.autocomplete", ".productSelection", function () {
        $(this).autocomplete(i);
    });
}

function product_stock_requsition(t) {
    var e = $("#batch_id_" + t).val(),
            a = $("#product_id_" + t).val(),
            l = "available_quantity_" + t,
            n = "expire_date_" + t,
            o = $("#base_url").val(),
            i = $('[name="csrf_test_name"]').val();
    return (
            $.ajax({
                type: "POST",
                url: o + "/requisition/get_batch_stock",
                data: {batch_id: e, product_id: a, app_csrf: i},
                cache: !1,
                success: function (e) {
                    var a = JSON.parse(e);
                    if ((o = (r = new Date()).getDate()) < 10)
                        var o = "0" + o;
                    if ((i = r.getMonth() + 1) < 10)
                        var i = "0" + i;
                    var r = r.getFullYear() + "-" + i + "-" + o;
                    new Date(r) >= new Date(a.expire_date)
                            ? (toastr.error("Date Expired Please Select another"),
                                    ($("#batch_id_" + t)[0].selectedIndex = 0),
                                    $("#" + n).html('<p style="color:red;align:center">' + a.expire_date + "</p>"),
                                    (document.getElementById("expire_date_" + t).innerHTML = ""))
                            : $("#" + n).html('<p style="color:green;align:center">' + a.expire_date + "</p>"),
                            $("#" + l).val(a.total_product);
                },
            }),
            $(this).unbind("change"),
            !1
            );
}

$(document).ready(function () {
    var t = $("#manual_requisition_insert");
    t.on("submit", function (e) {
        e.preventDefault(),
                $('#add_requisition').attr('disabled', 'disabled');
        $.ajax({
            url: $(this).attr("action"),
            method: $(this).attr("method"),
            dataType: "json",
            data: t.serialize(),
            success: function (t) {

                if (t.message == "success") {
                    toastr.success("Requisition Successfully Saved", 'Success', {timeOut: 5000});
                    var reqListUrl = $("#base_url").val() + "/requisition/requisition_list";
                    window.location.replace(reqListUrl);
//                            setTimeout(function () {
//                                location.reload();
//                            }, 1000);
//                            $("#normalRequisition tbody tr").remove();
//                            var reqListUrl = $("#base_url").val()+"/requisition/requisition_list";
                }

                if (t.message == "error") {
                    Object.keys(t.productValidationArr).forEach(key => {
                        toastr.error(`${t.productValidationArr[key]}`, 'Error', {timeOut: 5000});
                    });
                    $('#add_requisition').attr('disabled',false);
//
//                    return false;
//
//                    toastr.error("Requisition Number already exists", 'Error', {timeOut: 5000});
//                    var reqListUrl = $("#base_url").val() + "/requisition/requisition_list";
//                    window.location.replace(reqListUrl);
                }

            },
            error: function (t) {
                alert("failed to process requisition form!");
                $('#add_requisition').attr('disabled',false);
            },
        });
    });
});

$(document).ready(function () {
    var t = $("#manual_requisition_update");
    t.on("submit", function (e) {
        e.preventDefault(),
                $.ajax({
                    url: $(this).attr("action"),
                    method: $(this).attr("method"),
                    dataType: "json",
                    data: t.serialize(),
                    success: function (t) {
                        if (t.message == "success") {
                            setTimeout(function () {
                                location.reload();
                            }, 1000);
                            $("#normalRequisition tbody tr").remove();
                            toastr.success("Requisition Successfully Updated", 'Success', {timeOut: 5000});
                        }
                    },
                    error: function (t) {
                        alert("failed to process requisition form!");
                    },
                });
    });
});

function printRawHtmlRequisition(t) {
    printJS({printable: t, type: "raw-html", onPrintDialogClose: printRawHtmlRequisition()});
}
function printJobCompleteRequisition() {
    $("#normalRequisition tbody tr").remove(),
            setInterval(function () {
                location.reload();
            }, 1e3);
}

function quantity_calculate_requisition(t) {
    var e = $("#total_qntt_" + t).val(),
            n = ($("#invdcount").val(), e / $("#u_box_" + t).val());
    $("#box_qty_" + t).val(n);
    var c = $("#available_quantity_" + t).val();
    if (parseInt(e) > parseInt(c)) {
        var s = "You can Sale maximum " + c + " Items";
        $("#total_qntt_" + t).val("");
    }

}

function checkqty_requisition(t) {
    var e = parseInt($("#total_qntt_" + t).val()),
            a = parseFloat($("#price_item_" + t).val()),
            l = parseFloat($("#discount_" + t).val());
    return isNaN(e)
            ? (alert("must_input_numbers"), (document.getElementById("total_qntt_" + t).value = ""), !1)
            : isNaN(a)
            ? (alert("must_input_numbers"), (document.getElementById("price_item_" + t) ? document.getElementById("price_item_" + t).value = "" : ""), !1)
            : isNaN(l)
            ? (alert("must_input_numbers"), (document.getElementById("discount_" + t) ? document.getElementById("discount_" + t).value = "" : ""), !1)
            : void 0;
}


$(document).ready(function () {
    var t = $('[name="csrf_test_name"]').val(),
            e = $("#currency").val(),
            a = $("#base_url").val(),
            l = $("#RequisitionList").DataTable({
        responsive: !0,
        dom: "<'row'<'col-md-6'B><'col-md-6'f>>rt<'bottom'ip><'clear'>",
        aaSorting: [[2, "desc"]],
        columnDefs: [{bSortable: !1, aTargets: '_all'}],
        processing: !0,
        serverSide: !0,
//        pageLength: 2,
        lengthMenu: [
            [10, 25, 50, 100, 250, 500, -1],
            [10, 25, 50, 100, 250, 500, "All"],
        ],
        buttons: [
            {extend: "copyHtml5", text: '<i class="far fa-copy"></i>', titleAttr: "Copy", exportOptions: {columns: [0, 1, 2, 3, 4, 5]}, title: "Invoice List", className: "btn-light"},
            {extend: "excelHtml5", text: '<i class="far fa-file-excel"></i>', titleAttr: "Excel", exportOptions: {columns: [0, 1, 2, 3, 4, 5]}, title: "Invoice List", className: "btn-light"},
            {extend: "csvHtml5", text: '<i class="far fa-file-alt"></i>', titleAttr: "CSV", exportOptions: {columns: [0, 1, 2, 3, 4, 5]}, title: "Invoice List", className: "btn-light"},
            {extend: "pdfHtml5", text: '<i class="far fa-file-pdf"></i>', titleAttr: "PDF", exportOptions: {columns: [0, 1, 2, 3, 4, 5]}, title: "Invoice List", className: "btn-light"},
        ],
        serverMethod: "post",
        ajax: {
            url: a + "/requisition/requisition_list_check",
            data: function (e) {
                (e.fromdate = $("#from_date").val()), (e.todate = $("#to_date").val()), (e.status = $("#status").val()), (e.app_csrf = t);
            },
        },
        columns: [
            {data: "check"},
            {data: "sl_no"},
            {data: "requisition_no"},
            {data: "product_details"},
            {data: "details"},
            {data: "status"},
            {data: "delivery_date"},
            {data: "created_at"},
            {data: "created_by"},
            {data: "requisition_for"},
            {data: "button"},
        ],

    });
    $("#btn-filter-pur").on("click", function (t) {
        l.ajax.reload();
    });
})

$(document).on('click', '#btn-list', function () {
//    alert('hello');return false;

    var t = $("#requisition_status_update_form");
    t.on("submit", function (e) {
        e.preventDefault(),
                $.ajax({
                    url: $(this).attr("action"),
                    method: $(this).attr("method"),
                    dataType: "json",
                    data: t.serialize(),
                    success: function (t) {
                        if (t.message == "success") {
                            setTimeout(function () {
                                location.reload();
                            }, 1000);
                            toastr.success("Requisition Status updated Successfully", 'Success', {timeOut: 5000});
                        }
                    },
                    error: function (t) {
                        alert("failed to process requisition form!");
                    },
                });
    });
});

$(document).on('change', '#requisitionFor', function () {
    var customer_id = $(this).val();
//     alert(requisition_id);return false;
    var t = $('[name="csrf_test_name"]').val();
    var a = $("#base_url").val();

    $.ajax({
        url: a + "/requisition/requisition_customer_data",
        method: 'POST',
        dataType: "json",
        data: {customer_id: customer_id, app_csrf: t},
        success: function (data) {
            $('#user_id_from_response').val('');
            $('#designation_from_response').val('');
            $('#department_from_response').val('');
            if (data.message == "success") {
                if (data.cus_id) {
                    $('#user_id_from_response').val(data.user_id);
                    $('#designation_from_response').val(data.designation);
                    $('#department_from_response').val(data.department);
                    $('#showCustomerInfo').show();
                } else {
                    $('#showCustomerInfo').hide();
                }

//                console.log(data.customer_data[0].department);
//                console.log(data.customer_data[0].designation);
//                console.log(data.customer_data[0].user_id_num);
//                return false;
//                $("#normalRequisition tbody tr").remove();
//                toastr.success("Requisition Successfully Updated", 'Success', {timeOut: 5000});
            }
        }
        ,

    }
    );


});

$(document).on('click', '#notApproved', function () {
    var requisition_id = $(this).attr('data-id');
//     alert(requisition_id);return false;
    var t = $('[name="csrf_test_name"]').val();
    var a = $("#base_url").val();

    $.ajax({
        url: a + "/requisition/requisition_not_approved",
        method: 'POST',
        dataType: "json",
        data: {requisition_id: requisition_id, app_csrf: t},
        success: function (data) {
            if (data.message == "success") {
                setTimeout(() => {
                    location.reload();
                }, 1000);
                toastr.success("Requisition Not Approved Successfully", 'Success', {timeOut: 5000});
            }
        },

    }
    );


});


$(document).on('keyup', '#itemId', function () {
    var itemId = $(this).val();
//     alert(requisition_id);return false;
//   console.log(itemId);return false;
    var t = $('[name="csrf_test_name"]').val();
    var a = $("#base_url").val();

    if (itemId != '') {
        $.ajax({
            url: a + "/medicine/check_medicine_id",
            method: 'POST',
            dataType: "json",
            data: {itemId: itemId, app_csrf: t},
            success: function (data) {
//            console.log(data);return false;
                $('#itemId_error').text('');
                if (data == "exists") {
                    $('#itemId_error').show();
                    $('#itemId_error').text('Item Id already exists!!');
//                toastr.success("Item Id have been already exists!!", 'Success', {timeOut: 5000});
                }
            },

        }
        );
    } else {
        $('#itemId_error').hide();
    }
});


$(document).on('change', '#user_id', function (event) {
    event.preventDefault();
    var user_id = $(this).val();
    if (user_id != '') {
        $('#FilteruserId').val(user_id);
    } else {
        $('#FilteruserId').val('');
    }
});

$(document).on('change', '#from_date', function (event) {
    event.preventDefault();
    var from_date = $('#from_date').val();
    if (from_date != '') {
        $('#FilterfromDate').val(from_date);
    } else {
        $('#FilterfromDate').val('');
    }

});
$(document).on('change', '#to_date', function (event) {
    event.preventDefault();
    var to_date = $('#to_date').val();
    if (to_date != '') {
        $('#FiltertoDate').val(to_date);
    } else {
        $('#FiltertoDate').val('');
    }


});


$(document).on('keyup', '#itemId', function () {
    var itemId = $(this).val();
//     alert(requisition_id);return false;
//   console.log(itemId);return false;
    var t = $('[name="csrf_test_name"]').val();
    var a = $("#base_url").val();

    if (itemId != '') {
        $.ajax({
            url: a + "/medicine/check_medicine_id",
            method: 'POST',
            dataType: "json",
            data: {itemId: itemId, app_csrf: t},
            success: function (data) {
//            console.log(data);return false;
                $('#itemId_error').text('');
                if (data == "exists") {
                    $('#itemId_error').show();
                    $('#itemId_error').text('Item Id already exists!!');
//                toastr.success("Item Id have been already exists!!", 'Success', {timeOut: 5000});
                }
            },

        }
        );
    } else {
        $('#itemId_error').hide();
    }
});

$(document).on('change', '#orderBy', function (event) {
    event.preventDefault();
    var orderBy = $(this).val();
    if (orderBy != '') {
        $('#print_order_by').val(orderBy);
    } else {
        $('#print_order_by').val('');
    }
});

$(document).on('change', '#department_id', function (event) {
    event.preventDefault();
    var department_id = $(this).val();
    if (department_id != '') {
        $('#FilterDepId').val(department_id);
    } else {
        $('#FilterDepId').val('');
    }
});
