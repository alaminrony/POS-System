<div class="row">
    <div class="col-md-12 col-md-12">
        <div class="card ">
            <div class="card-header py-2">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fs-17 font-weight-600 mb-0">Department Wise Sales Report</h6>
                    </div>
                    <?php echo form_open_multipart('report/department_wise_sales_report_print', 'id="filterForm"') ?>
                    <div class="text-right">
                        <input type="hidden" class="datepicker psdate" name="FilterfromDate" id="FilterfromDate" value="">
                        <input type="hidden" class="datepicker pedate" name="FiltertoDate" id="FiltertoDate" value=""> 
                        <input type="hidden" class="pedate" name="FilterDepId" id="FilterDepId" value="">
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
                            <div class="col-md-2 input-group mb-2 mr-sm-2 list-width">
                                <?php echo form_dropdown('department_id', $dep_list, null, 'class="form-control select2" id="department_id"') ?> 
                            </div>
                            <label class="sr-only" for="from_date"><?php echo lan('start_date') ?></label>
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
                            </div>

                            <button type="button" id="btn-filter-pur" class="btn btn-success mb-2"><?php echo lan('find') ?></button>
                        </form>
                    </div>

                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered custom-table" cellspacing="0" width="100%" id="depWiseSalesreport"> 
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>Department</th>
                                <th>Quantity</th>
                                <th>Total Price</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
<!--                        <tfoot>
                        <th colspan="5" class="text-right">Total:</th>

                        <th></th>  
                        <th></th> 
                        </tfoot>-->
                    </table>

                </div>
            </div> 
        </div>
    </div>
</div>


