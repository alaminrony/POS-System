<div class="row">
    <div class="col-md-12 col-lg-12">
        <div class="card">
            <div class="card-header py-2">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fs-17 font-weight-600 mb-0"><?php echo lan('add_requisition') ?></h6>
                    </div>
                    <div class="text-right">
                        <?php if ($permission->method('requisition_list', 'read')->access()) { ?>
                            <a href="<?php echo base_url('requisition/requisition_list') ?>"
                               class="btn btn-success btn-sm mr-1"><i
                                    class="fas fa-align-justify mr-1"></i><?php echo lan('requisition_list') ?></a>
                            <?php } ?>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <?php echo form_open_multipart("requisition/requsition_add/", array('id' => 'manual_requisition_insert')) ?>
                <div class="form-group row">
                    <!--                    <label for="invoice_no"
                                               class="col-md-2 text-right col-form-label"><?php echo lan('requisition_no') ?>:</label>
                                        <div class="col-md-4">
                                            <div class="">
                                                <input type="text" class="form-control" name="requisition_no" id="requisition_no"
                                                       placeholder="<?php echo lan('requisition_no') ?>" value="<?php echo $requisition_no ?>"
                                                       readonly>
                                            </div>
                                        </div>-->
                    <?php if ($role != '4') { ?>
                        <label for="requisitionFor" class="col-md-2 text-right col-form-label">Employee:</label>
                        <div class="col-md-4">
                            <div class="">
                                <select name="requisition_for" id="requisitionFor" class="form-control select2" tabindex="4" required="required">
                                    <?php foreach ($customerArr as $id => $name) { ?>
                                        <option value="<?php echo $id ?>" ><?php echo $name ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    <?php } ?>
                </div>

                <div id="showCustomerInfo" style="display:none;">
                    <div class="form-group row">
                        <label for="department"
                               class="col-md-2 text-right col-form-label">Department:</label>
                        <div class="col-md-4">
                            <div class="">
                                <input type="text" class="form-control" name="department" id="department_from_response" value="" readonly>
                            </div>
                        </div>

                        <label for="designation" class="col-md-2 text-right col-form-label">Designation:</label>
                        <div class="col-md-4">
                            <div class="">
                                <input type="text" class="form-control" name="designation" id="designation_from_response" value="" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="user_id"
                               class="col-md-2 text-right col-form-label">User Id:</label>
                        <div class="col-md-4">
                            <div class="">
                                <input type="text" class="form-control" name="user_id" id="user_id_from_response" value="" readonly>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="form-group row">
                    <label for="date" class="col-md-2 text-right col-form-label"><?php echo lan('date') ?> <i
                            class="text-danger"> * </i>:</label>
                    <div class="col-md-4">
                        <div class="">

                            <input type="text" name="date" class="form-control datepicker" id="date"
                                   placeholder="<?php echo lan('date') ?>" value="" tabindex="2">
                        </div>

                    </div>

                    <label for="details" class="col-md-2 text-right col-form-label"><?php echo lan('details') ?>
                        :</label>
                    <div class="col-md-4">
                        <div class="">

                            <input type="text" class="form-control" name="details" id="details"
                                   placeholder="<?php echo lan('details') ?>" value="" tabindex="3">
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="date" class="col-md-2 text-right col-form-label">Purpose <i
                            class="text-danger"> </i>:</label>
                    <div class="col-md-4">
                        <input type="checkbox" id="vehicle1" name="purpose[]" value="Official">
                        <label for="vehicle1"> Official</label><br>
                        <input type="checkbox" id="vehicle2" name="purpose[]" value="Personal">
                        <label for="vehicle2"> Personal</label><br>
                        <input type="checkbox" id="vehicle2" name="purpose[]" value="Family">
                        <label for="vehicle2"> Family</label><br>
                        <input type="checkbox" id="vehicle2" name="purpose[]" value="Others">
                        <label for="vehicle2"> Others</label><br>
                        <input type="checkbox" id="vehicle2" name="purpose[]" value="DGDA">
                        <label for="vehicle2"> DGDA</label><br>
                        <input type="checkbox" id="vehicle2" name="purpose[]" value="CMB">
                        <label for="vehicle2"> Corporate Medicine Box</label><br>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-sm text-nowrap custom-table table-sm text-nowrap"
                           id="normalRequisition">
                        <thead>
                            <tr>
                                <th class="text-center" width="500"><?php echo lan('medicine_information') ?> <i
                                        class="text-danger">*</i></th>
                                <th class="text-center" width="130" style="display: none"><?php echo lan('batch') ?><i class="text-danger"></i></th>
                                <th class="text-center" width="130"><?php echo lan('available_qnty') ?></th>
                                <th class="text-center" width="120" style="display: none"><?php echo lan('expiry_date') ?></th>
                                <th class="text-center" width="300"><?php echo lan('unit') ?></th>
                                <th class="text-center" width="300">Strip</th>
                                <th class="text-center" width="300"><?php echo lan('quantity') ?> <i
                                        class="text-danger">*</i></th>
                                <th class="text-center" width="300"><?php echo lan('box_qty') ?> <i
                                        class="text-danger">*</i>
                                </th>
                                <th class="text-center" width="300"><?php echo lan('action') ?></th>
                            </tr>
                        </thead>
                        <tbody id="addRequisitionItem">
                            <tr>
                                <td class="product_field">
                                    <input type="text" name="product_name[]" onkeyup="requisition_productList(1);"
                                           onkeypress="requisition_productList(1);" class="form-control productSelection"
                                           placeholder='<?php echo lan('medicine_name') ?>' required="" id="product_name_1"
                                           tabindex="5">

                                    <input type="hidden" class="autocomplete_hidden_value product_id_1" name="product_id[]"
                                           id="product_id_1"/>

                                    <input type="hidden" class="baseUrl" value="<?php echo base_url(); ?>"/>
                                </td>
                                <td style="display: none">
                                    <select class="form-control select2" id="batch_id_1" name="batch_id[]"  onchange="product_stock_requsition(1)" tabindex="6" >
                                        <option></option>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" name="available_quantity[]" class="form-control text-right available_quantity_1" value="0" readonly="" id="available_quantity_1"/>
                                </td>
                                <td id="expire_date_1" style="display: none">

                                </td>
                                <td>
                                    <input name="product_unit[]" id="" class="form-control text-right unit_1 valid" value="None"
                                           readonly="" aria-invalid="false" type="text">
                                </td>
                                <td>
                                    <input name="strip[]" id="strip_1" class="form-control text-right strip_1 valid" value="None"
                                           readonly="" aria-invalid="false" type="text">
                                </td>
                                <td>
                                    <input type="text" name="product_quantity[]"
                                           class="total_qntt_1 form-control text-right valid_number" id="total_qntt_1"
                                           placeholder="0.00" min="0" tabindex="7" onkeyup="quantity_calculate_invoice(1);" onchange="quantity_calculate_invoice(1);" required/>
                                    <!--<input type="text" name="product_quantity[]"
                                           onkeyup="quantity_calculate_requisition(1),checkqty_invoice(1);"
                                           onchange="quantity_calculate_requisition(1);"
                                           class="total_qntt_1 form-control text-right valid_number" id="total_qntt_1"
                                           placeholder="0.00" min="0" tabindex="7" required/>-->
                                </td>

                                <td>
                                    <input type="text" name="box_quantity[]" class=" form-control text-right valid_number" onkeyup="quantity_calculate_invoice(1), checkqty_invoice(1);" onchange="quantity_calculate_invoice(1);" id="box_qty_1" placeholder="0.00"
                                           min="0" tabindex="-1" readonly=""/>

                                <!--<input type="text" name="box_quantity[]"
                                       onkeyup="quantity_calculate_requisition(1),checkqty_requisition(1);"
                                       onchange="quantity_calculate_requisition(1);"
                                       class=" form-control text-right valid_number" id="box_qty_1" placeholder="0.00"
                                       min="0" tabindex="-1" readonly=""/>-->
                                    <input type="hidden" id="u_box_1" name="b_qty"/>
                                </td>

                                <td>
                                    <button type="button" class="btn btn-danger-soft btn-sm" tabindex="10"
                                            onclick="deleteRowinvoice(this)"><i class="far fa-trash-alt"></i></button>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>

                            <tr>
                                <td colspan="6" rowspan="1"></td>
                                <td>
                                    <button class="btn btn-info" type="button"
                                            onClick="addInputFieldRequisition('addRequisitionItem');" tabindex="11"
                                            id="add_requisition_item"><i class="fa fa-plus"></i>
                                    </button>

                                </td>
                            </tr>
                            <tr>
                                <td align="center">
                                    <input type="submit" style="font-size: 18px;padding: 4px 12px;" id="add_requisition" class="btn btn-lg btn-success" name="add-requisition"
                                           value="<?php echo lan('save') ?>" tabindex="15"/>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>