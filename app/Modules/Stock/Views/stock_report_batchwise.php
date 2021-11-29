<div class="row">
    <div class="col-md-12 col-md-12">
        <div class="card ">
            <div class="card-header py-2">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fs-17 font-weight-600 mb-0"><?php echo lan('stock_report_batchwise') ?></h6>
                    </div>
                    <?php echo form_open_multipart('stock/stock_list_batchWise_print', 'id="filterForm"') ?>
                    <div class="text-right">
<!--                        <input type="hidden" class="datepicker psdate" name="FilterfromDate" id="FilterfromDate" value="">
                        <input type="hidden" class="datepicker pedate" name="FiltertoDate" id="FiltertoDate" value=""> -->
                        <input type="hidden" class="" name="print_order_by" id="print_order_by" value="">
                        <input type="submit" class="btn btn-success btn-sm mr-1" value="Print">
                        <!--<a href="" class="btn btn-success btn-sm mr-1"><i class="fas fa-align-justify mr-1"></i>Print</a>-->
                    </div>
                    </form>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-12">
                        <form action="" class="form-inline" method="post" accept-charset="utf-8">
                            <div class="col-md-3 input-group mb-2 mr-sm-2 list-width">
                                <?php echo form_dropdown('orderBy', ['0' => '--Select order--', 'product_id' => 'Item Id', 'product_name' => 'Medicine Name', 'batch_id' => 'Batch Id', 'expeire_date' => 'Expiry Date'], null, 'class="form-control select2" id="orderBy"') ?> 
                            </div>
<!--                            <label class="sr-only" for="from_date"><?php echo lan('start_date') ?></label>
                            <div class="input-group mb-2 mr-sm-2">
                                <div class="input-group-prepend">
                                    <div class="input-group-text"><?php echo lan('start_date') ?></div>
                                </div>
                                <input type="text" class="form-control datepicker psdate" name="from_date" id="from_date" placeholder="<?php echo lan('start_date') ?>" value="">
                            </div>

                            <label class="sr-only" for="to_date"><?php echo lan('end_date') ?></label>
                            <div class="input-group mb-2 mr-sm-2">
                                <div class="input-group-prepend">
                                    <div class="input-group-text"><?php echo lan('end_date') ?></div>
                                </div>
                                <input type="text" class="form-control datepicker pedate" id="to_date" name="to_date" placeholder="<?php echo lan('end_date') ?>">
                            </div>-->

                            <button type="button" id="btn-filter-pur" class="btn btn-success mb-2"><?php echo lan('find') ?></button>
                        </form>
                    </div>

                </div>

                <div class="table-responsive">
                    <table class="table lan table-bordered table-striped table-hover custom-table" width="100%" id="StockListBatchwise">
                        <thead>
                            <tr>
                                <th class="text-center"><?php echo lan('sl_no') ?></th>
                                <th class="text-center">Item Id</th>
                                <th class="text-center"><?php echo lan('medicine_name') ?></th>
                                <th class="text-center"><?php echo lan('batch_id') ?></th>
                                <th class="text-center"><?php echo lan('expiry_date') ?></th>
                                <th class="text-center"><?php echo lan('in_qty') ?> (Piece)</th>
                                <th class="text-center"><?php echo lan('out_qty') ?> (Piece)</th>
                                <th class="text-center"><?php echo lan('stock') ?> (Piece)</th>
                                <th class="text-center">Strip</th>
                                <th class="text-center"><?php echo lan('stock') . ' ' . 'box'; ?></th>
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

