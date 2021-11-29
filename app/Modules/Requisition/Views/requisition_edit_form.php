<div class="row">
    <div class="col-md-12 col-lg-12">
        <div class="card">
            <div class="card-header py-2">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fs-17 font-weight-600 mb-0"><?php echo lan('edit_requisition') ?></h6>
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
                <?php echo form_open_multipart("requisition/requsition_update/", array('id' => 'manual_requisition_update')) ?>
                <input type="hidden" name="requisition_id" value="<?php echo $requisition->id; ?>">
                <div class="form-group row">
                    <label for="invoice_no"
                           class="col-md-2 text-right col-form-label"><?php echo lan('requisition_no') ?>:</label>
                    <div class="col-md-4">
                        <div class="">
                            <input type="text" class="form-control" name="requisition_no" id="requisition_no"
                                   placeholder="<?php echo lan('requisition_no') ?>" value="<?php echo $requisition->requisition_no ?>"
                                   readonly>
                        </div>
                    </div>
                    <?php if ($role != '4') { ?>
                        <label for="Status"
                               class="col-md-2 text-right col-form-label"><?php echo lan('Status') ?>:</label>
                        <div class="col-md-4 mt-2">
                            <label class="radio-inline">
                                <?php echo form_radio('status', '1', (($requisition->status == 1) ? true : false)); ?>Pending
                            </label>
                            <label class="radio-inline">
                                <?php echo form_radio('status', '2', (($requisition->status == 2) ? true : false)); ?>Approved
                            </label>
                            <label class="radio-inline">
                                <?php echo form_radio('status', '3', (($requisition->status == 3) ? true : false)); ?>Not Approved
                            </label>
                        </div>
                    <?php } ?>
                </div>
                <div class="form-group row">
                    <label for="date" class="col-md-2 text-right col-form-label"><?php echo lan('date') ?> <i
                            class="text-danger"> * </i>:</label>
                    <div class="col-md-4">
                        <div class="">

                            <input type="text" name="date" class="form-control datepicker" id="date"
                                   placeholder="<?php echo lan('date') ?>" value="<?php echo date('Y-m-d', strtotime($requisition->delivery_date)) ?>" tabindex="2">

                        </div>

                    </div>

                    <label for="details" class="col-md-2 text-right col-form-label"><?php echo lan('details') ?>
                        :</label>
                    <div class="col-md-4">
                        <div class="">

                            <input type="text" class="form-control" name="details" id="details"
                                   placeholder="<?php echo lan('details') ?>" value="<?php echo $requisition->information ?>" tabindex="3">
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-sm text-nowrap custom-table table-sm text-nowrap"
                           id="normalRequisition">
                        <thead>
                            <tr>
                                <th class="text-center" width="300"><?php echo lan('medicine_information') ?> <i
                                        class="text-danger">*</i></th>
                                <th class="text-center" width="150"><?php echo lan('unit') ?></th>
                                <th class="text-center" width="100"><?php echo lan('quantity') ?> <i
                                        class="text-danger">*</i></th>
                                <th class="text-center" width="100"><?php echo lan('box_qty') ?> <i
                                        class="text-danger">*</i>
                                </th>
                                <th class="text-center" width="100"><?php echo lan('action') ?></th>
                            </tr>
                        </thead>
                        <tbody id="addRequisitionItem">
                            <?php $i = 0 ?>
                            <?php foreach ($requisition_details as $detail) { ?>
                                <?php $i++; ?>
                                <tr>
                                    <td class="product_field">
                                        <input type="text" name="product_name[]" onkeyup="requisition_productList(<?php echo $i ?>);"
                                               onkeypress="requisition_productList(<?php echo $i ?>);" class="form-control productSelection"
                                               placeholder='<?php echo lan('medicine_name') ?>' value="<?php echo $productArr[$detail->product_id] ?? ''; ?>" required="" id="product_name_1"
                                               tabindex="5">

                                        <input type="hidden" class="autocomplete_hidden_value product_id_1" name="product_id[]"
                                               id="product_id_1"  value="<?php echo $detail->product_id; ?>"/>

                                        <input type="hidden" class="baseUrl" value="<?php echo base_url(); ?>"/>
                                    </td>
                                    <td>
                                        <input name="product_unit[]" id="" class="form-control text-right unit_1 valid" value="<?php echo $detail->unit ?? "None" ?>"
                                               readonly="" aria-invalid="false" type="text">
                                    </td>
                                    <td>
                                        <input type="text" name="product_quantity[]" onkeyup="quantity_calculate_requisition(<?php echo $i ?>), checkqty_requisition(<?php echo $i ?>);"
                                               onchange="quantity_calculate_requisition(<?php echo $i ?>);"
                                               class="total_qntt_1 form-control text-right valid_number" id="total_qntt_1"
                                               placeholder="0.00" min="0" tabindex="7" value="<?php echo $detail->quantity; ?>" required/>
                                    </td>

                                    <td>
                                        <input type="text" name="box_quantity[]" onkeyup="quantity_calculate_requisition(<?php echo $i ?>), checkqty_requisition(<?php echo $i ?>);"
                                               onchange="quantity_calculate_requisition(<?php echo $i ?>);" class=" form-control text-right valid_number" id="box_qty_1" placeholder="0.00"
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
                            <?php } ?>
                        </tbody>
                        <tfoot>

                            <tr>
                                <td colspan="4" rowspan="1"></td>
                                <td>
                                    <button class="btn btn-info" type="button"
                                            onClick="addInputFieldRequisition('addRequisitionItem');" tabindex="11"
                                            id="add_requisition_item"><i class="fa fa-plus"></i>
                                    </button>

                                </td>
                            </tr>
                            <tr>
                                <td align="center">
                                    <input type="submit" id="add_requisition" class="btn btn-success" name="add-requisition"
                                           value="<?php echo lan('update') ?>" tabindex="15"/>
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