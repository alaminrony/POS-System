<div class="table-responsive">
                    <table class="table table-bordered table-hover table-sm text-nowrap custom-table table-sm text-nowrap"
                           id="normalinvoice">
                        <thead>
                        <tr>
                            <th class="text-center" width="300"><?php echo lan('medicine_information') ?> <i
                                        class="text-danger">*</i></th>
                            <th class="text-center" width="150"><?php echo lan('unit') ?></th>
                            <th class="text-center" width="100"><?php echo lan('quantity') ?> <i
                                        class="text-danger">*</i></th>
                            <th class="text-center" width="100"><?php echo lan('box_qty') ?> <i class="text-danger">*</i>
                            </th>
                            <th class="text-center" width="100"><?php echo lan('action') ?></th>
                        </tr>
                        </thead>
                        <tbody id="addinvoiceItem">
                        <tr>
                            <td class="product_field">
                                <input type="text" name="product_name" onkeyup="requisition_productList(1);"
                                       onkeypress="requisition_productList(1);" class="form-control productSelection"
                                       placeholder='<?php echo lan('medicine_name') ?>' required="" id="product_name_1"
                                       tabindex="5">

                                <input type="hidden" class="autocomplete_hidden_value product_id_1" name="product_id[]"
                                       id="product_id_1"/>

                                <input type="hidden" class="baseUrl" value="<?php echo base_url(); ?>"/>
                            </td>
                            <td>
                                <input name="" id="" class="form-control text-right unit_1 valid" value="None"
                                       readonly="" aria-invalid="false" type="text">
                            </td>
                            <td>
                                <input type="text" name="product_quantity[]"
                                       class="total_qntt_1 form-control text-right valid_number" id="total_qntt_1"
                                       placeholder="0.00" min="0" tabindex="7" required/>
                            </td>

                            <td>
                                <input type="text" name="box_quantity[]"
                                       class=" form-control text-right valid_number" id="box_qty_1" placeholder="0.00"
                                       min="0" tabindex="-1" readonly=""/>
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
                            <td colspan="4" rowspan="1"></td>
                            <td>
                                <button class="btn btn-info" type="button"
                                        onClick="addInputFieldRequisition('addinvoiceItem');" tabindex="11"
                                        id="add_invoice_item"><i class="fa fa-plus"></i>
                                </button>

                            </td>
                        </tr>