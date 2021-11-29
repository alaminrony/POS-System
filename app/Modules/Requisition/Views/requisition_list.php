<div class="row">
    <div class="col-md-12 col-md-12">
        <div class="card ">
            <div class="card-header py-2">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fs-17 font-weight-600 mb-0"><?php echo lan('Requisition_list') ?></h6>
                    </div>
                    <div class="text-right">
                        <?php if ($permission->method('add_requisition', 'create')->access()) { ?>  
                            <a href="<?php echo base_url('requisition/add_requisition') ?>" class="btn btn-success btn-sm mr-1"><i class="fas fa-plus mr-1"></i><?php echo lan('add_requisition') ?></a>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-12">


                        <form action="" class="form-inline" method="post">
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

                            <label class="sr-only" for="status">Status</label>
                            <div class="input-group mb-2 mr-sm-2">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">Status</div>
                                </div>
                                <select name="status" id="status" class="form-control">
                                    <option value="1" >Pending</option>
                                    <option value="2" >Approved</option>
                                    <option value="3" >Not Approved</option>
                                </select>
                            </div>
                            <button type="button" id="btn-filter-pur" class="btn btn-success mb-2"><?php echo lan('find') ?></button>
                        </form>
                    </div>

                </div>
                <?php echo form_open_multipart("requisition/requisition_status_update/", array('id' => 'requisition_status_update_form')) ?>
                <?php if ($role != '4') { ?>
                    <button type="submit" onclick="return confirm('Are you sure, you want to approve this!!')" id="btn-list" class="btn btn-success mb-2">Approve & Invoice</button>
                <?php } ?>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered custom-table" cellspacing="0" width="100%" id="RequisitionList"> 
                        <thead>
                            <tr>
                                <th>Check</th>
                                <th><?php echo lan('sl_no') ?></th>
                                <th><?php echo lan('requisition_no') ?></th>
                                <th><?php echo lan('product_details') ?></th>
                                <th><?php echo lan('details') ?></th>
                                <th><?php echo lan('status') ?></th>
                                <th><?php echo lan('delivery_date') ?></th>
                                <th><?php echo lan('created_at') ?></th>
                                <th>Created By</th>
                                <th>Requisition For</th>
                                <th><?php echo lan('action') ?></th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                    <?php echo form_close(); ?>
                </div>
            </div> 
        </div>
    </div>
</div>

