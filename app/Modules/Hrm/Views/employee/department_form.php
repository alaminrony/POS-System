<div class="row">
    <div class="col-md-12 col-lg-12">
        <div class="card">
            <div class="card-header py-2">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <?php if (!empty($department->id)) { ?>
                            <h6 class="fs-17 font-weight-600 mb-0">Edit Department</h6>
                        <?php } else { ?>
                            <h6 class="fs-17 font-weight-600 mb-0">Add Department</h6>
                        <?php } ?>

                    </div>
                    <div class="text-right">
                        <?php if ($permission->method('department_list', 'read')->access()) { ?>  
                            <a href="<?php echo base_url('employee/department_list') ?>" class="btn btn-success btn-sm mr-1"><i class="fas fa-align-justify mr-1"></i>Department List</a>
                        <?php } ?>

                    </div>
                </div>
            </div>
            <div class="card-body">


                <?php echo form_open_multipart("employee/add_department/" . $department->id) ?>            
                <?php echo form_hidden('department_id', $department->id) ?>
                <div class="form-group row">
                    <label for="department_name" class="col-sm-3 col-form-label">Department Name <i class="text-danger">*</i></label>
                    <div class="col-sm-3">
                        <input name="department_name" class="form-control" type="text" placeholder="Enter department name" id="department name" value="<?php echo $department->department_name; ?>">
                    </div>
                </div> 
                <div class="form-group row">
                    <label for="department_name" class="col-sm-3 col-form-label">Parent Department<i class="text-danger">*</i></label>
                    <div class="col-sm-3">
                        <select class="form-control select2" name="parent_id" id="parent_id">
                             <option value="">Select Option</option>
                            <?php foreach($departmentList as $parentDepartment){?>
                            <option value="<?php echo $parentDepartment->id?>" <?php if($department->parent_id==$parentDepartment->id){echo 'selected';}?>><?php echo $parentDepartment->department_name?></option>
                            <?php }?>
                        </select>
                    </div> 
                </div> 

                <div class="form-group row">
                    <div class="col-md-6 text-right">
                        <div class="">
                            <button type="submit"  class="btn btn-success">
                                <?php echo (empty($designation->id) ? lan('save') : lan('update')) ?></button>
                        </div>
                    </div>
                </div>

                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>


