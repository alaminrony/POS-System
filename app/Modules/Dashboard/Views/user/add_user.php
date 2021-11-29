                  
<div class="row">
    <div class="card col-md-12 col-lg-12">
        <div class="card-header py-2">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="fs-17 font-weight-600 mb-0">Add Employee</h6>
                </div>
                <div class="text-right">
                    <?php if ($permission->method('user_list', 'read')->access()) { ?>  
                        <a href="<?php echo base_url('user/user_list') ?>" class="btn btn-success btn-sm mr-1"><i class="fas fa-align-justify mr-1"></i>Employee List</a>
                    <?php } ?>

                </div>
            </div>
        </div>
        <div class="card-body">
            <?php echo form_open_multipart("user/add_user") ?>

            <?php echo form_hidden('id', $user->id) ?>                   
            <div class="form-group row">
                <label for="firstname" class="col-sm-3 col-form-label"><?php echo lan('firstname'); ?> <i class="text-danger">*</i></label>
                <div class="col-sm-9">
                    <input name="firstname" class="form-control" type="text" placeholder="<?php echo 'firstname'; ?>" id="firstname"  value="<?php echo $user->firstname; ?>">
                </div>
            </div>

            <div class="form-group row">
                <label for="lastname" class="col-sm-3 col-form-label"><?php echo lan('lastname'); ?> <i class="text-danger">*</i></label>
                <div class="col-sm-9">
                    <input name="lastname" class="form-control" type="text" placeholder="<?php echo 'lastname'; ?>" id="lastname" value="<?php echo $user->lastname; ?>">
                </div>
            </div>

            <div class="form-group row">
                <label for="email" class="col-sm-3 col-form-label"><?php echo lan('email'); ?> <i class="text-danger">*</i></label>
                <div class="col-sm-9">
                    <input name="email" class="form-control" type="email" placeholder="<?php echo 'email'; ?>" id="email" value="<?php echo $user->email; ?>">
                </div>
            </div> 



            <div class="form-group row">
                <label for="password" class="col-sm-3 col-form-label"><?php echo lan('password') ?> <i class="text-danger">*</i></label>
                <div class="col-sm-9">
                    <input name="password" class="form-control" type="password" placeholder="<?php echo lan('password') ?>" id="password">
                    <input name="old_password" class="form-control" type="hidden" value="<?php echo $user->password; ?>" id="hidden">
                </div>
            </div>
            <div class="form-group row">
                <label for="department" class="col-md-3 text-right col-form-label">Department:</label>
                <div class="col-sm-9">
                    <div class="">
                        <select name="department" id="department"  class="form-control select2" tabindex="4">
                            <option value="" selected="selected">--Select Department--</option>
                            <?php foreach ($departmentList as $index => $departmentName) { ?>
                                <option value="<?php echo $index; ?>" <?php
                                if ($index == $user->department) {
                                    echo 'selected';
                                }
                                ?>><?php echo $departmentName; ?></option>
                                    <?php } ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <label for="designation" class="col-md-3 text-right col-form-label">Designation:</label>
                <div class="col-sm-9">
                    <div class="">
                        <select name="designation" id="designation"  class="form-control select2" tabindex="4">
                            <option value="" selected="selected">--Select Designation--</option>
                            <?php foreach ($designationList as $designation) { ?>
                                <option value="<?php echo $designation->id; ?>" <?php
                                if ($designation->id == $user->designation) {
                                    echo 'selected';
                                }
                                ?>><?php echo $designation->designation; ?></option>
                                    <?php } ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <label for="user_id" class="col-sm-3 col-form-label">Employee Id:</label>
                <div class="col-sm-9">
                    <input name="user_id" class="form-control" type="text" placeholder="User Id" id="user_id" value="<?php echo $user->user_id; ?>">
                </div>
            </div> 

            <div class="form-group row">
                <label for="about" class="col-sm-3 col-form-label"><?php echo lan('about') ?></label>
                <div class="col-sm-9">
                    <textarea name="about" placeholder="<?php echo lan('about') ?>" class="form-control" id="about"><?php echo $user->about ?></textarea>
                </div>
            </div>


            <div class="form-group row">
                <label for="preview" class="col-sm-3 col-form-label"><?php echo lan('preview') ?></label>
                <div class="col-sm-9">
                    <img src="<?php echo base_url() . $user->image ?>" class="img-thumbnail" width="125" height="100">
                </div>
                <input type="hidden" name="old_image" value="<?php echo $user->image ?>">
            </div> 

            <div class="form-group row">
                <label for="image" class="col-sm-3 col-form-label"><?php echo lan('image') ?></label>
                <div class="col-sm-9">
                    <input type="file" name="image" id="image" aria-describedby="fileHelp">
                    <small id="fileHelp" class="text-muted"></small>
                </div>
            </div> 

            <div class="form-group row">
                <label for="status" class="col-sm-3 col-form-label">Is Admin <i class="text-danger">*</i></label>
                <div class="col-sm-9">
                    <label class="radio-inline">
                        <?php echo form_radio('is_admin', '1', (($user->is_admin == 1 || $user->is_admin == null) ? true : false), 'id="is_admin"'); ?>Admin
                    </label>
                    <label class="radio-inline">
                        <?php echo form_radio('is_admin', '0', (($user->is_admin == "0") ? true : false), 'id="is_admin"'); ?>Employee
                    </label> 
                </div>
            </div>

            <div class="form-group row">
                <label for="status" class="col-sm-3 col-form-label">Is Management <i class="text-danger"></i></label>
                <div class="col-sm-9">
                    <label class="radio-inline">
                        <?php echo form_radio('is_management', '0', (($user->is_management == "0") ? true : false), 'id="is_management"'); ?>Management
                    </label>
                    <label class="radio-inline">
                        <?php echo form_radio('is_management', '1', (($user->is_management == "1") ? true : false), 'id="is_management"'); ?>Non-management
                    </label> 
                </div>
            </div>
            <div class="form-group row">
                <label for="status" class="col-sm-3 col-form-label">Status <i class="text-danger">*</i></label>
                <div class="col-sm-9">
                    <label class="radio-inline">
                        <?php echo form_radio('status', '1', (($user->status == 1 || $user->status == null) ? true : false), 'id="status"'); ?>Active
                    </label>
                    <label class="radio-inline">
                        <?php echo form_radio('status', '0', (($user->status == "0") ? true : false), 'id="status"'); ?>Inactive
                    </label> 
                </div>
            </div>

            <div class="form-group text-right">
                <button type="reset" class="btn btn-primary w-md m-b-5"><?php echo 'reset'; ?></button>
                <button type="submit" class="btn btn-success w-md m-b-5"><?php echo 'save'; ?></button>
            </div>
            <?php echo form_close() ?>
        </div>
    </div>
</div>
