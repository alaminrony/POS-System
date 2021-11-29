<div class="row">
    <div class="col-sm-12 col-md-12">
        <div class="card ">
            <div class="card-header py-2">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fs-17 font-weight-600 mb-0">Department List</h6>
                    </div>
                    <div class="text-right">
                        <?php if ($permission->method('add_department', 'create')->access()) { ?>  
                            <a href="<?php echo base_url('employee/add_department') ?>" class="btn btn-success btn-sm mr-1"><i class="fas fa-plus mr-1"></i>Add Department</a>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="card-body">

                <div class="table-responsive">
                    <table class="table display table-bordered table-striped table-hover custom-table" id="example">
                        <thead>
                            <tr>
                                <th><?php echo lan('sl_no') ?></th>
                                <th>Department Name</th>
                                <th>Parent Department</th>
                                <th><?php echo lan('action') ?>

                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sl = 1;
                            if ($department_list) {
                                ?>
                                <?php foreach ($department_list as $department) { 
                                    
                                    ?>
                                    <tr>
                                        <td><?php echo $sl; ?></td>
                                        <td><?php echo $department->department_name; ?></td>
                                        <td><?php echo $mainDepArr[$parentDepArr[$department->id][$department->parent_id]] ?? ''; ?></td>
                                        <td>
                                            <a href="<?php echo base_url() . '/employee/edit_department/' . $department->id ?>" class="btn btn-success-soft btn-sm" data-toggle="tooltip" data-placement="left" title="Update"><i class="fas fa-edit" aria-hidden="true"></i></a>
                                            <a href="<?php echo base_url() . '/employee/delete_department/' . $department->id ?>" onclick="return confirm('Are You Sure?')" class="btn btn-danger-soft btn-sm" data-toggle="tooltip" data-placement="left" title="Delete"><i class="far fa-trash-alt" aria-hidden="true"></i></a>
                                        </td>
                                    </tr>
                                    <?php
                                    $sl++;
                                }
                                ?>
                            <?php } else { ?>
                                <tr><td colspan="4" class="text-center"><b>No Data Found</b></td></tr>
                            <?php } ?>
                        </tbody>

                    </table>

                </div>
            </div> 
        </div>
    </div>
</div>

