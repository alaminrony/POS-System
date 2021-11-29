<div class="row justify-content-center">
    <div class="col-12 col-lg-10 col-xl-8">
        <div class="header p-0 ml-0 mr-0 shadow-none">
            <div class="header-body">
                <div class="row align-items-center">
                    <div class="col">
                        <h6 class="header-pretitle fs-10 font-weight-bold text-muted text-uppercase mb-1"><?php echo lan('payments') ?></h6>
                        <h1 class="header-title fs-25 font-weight-600"><?php echo lan('requisition_no') ?>: <?php echo $requisition->requisition_no ?></h1>
                    </div>
                    <div class="col-auto">
                        <a href="<?php echo base_url('requisition/requisition_list') ?>" class="btn btn-success-soft ml-2"><i class="fas fa-align-justify mr-1"></i><?php echo lan('requisition_list') ?></a>
                        <a src="javascript:void(0)" onclick="printDiv('printArea')" class="btn btn-success ml-2"><i class="typcn typcn-printer mr-1"></i><?php echo lan('print_invoice') ?> </a>
                    </div>
                </div> 
            </div>
        </div>


        <div class="card card-body p-5">
            <div class="" id="printArea">
                <div class="row">
                    <div class="col text-center">
                        <img src="<?php echo base_url() . $settings_info->logo; ?>" alt="..." class="img-fluid mb-4" height="100px" width="250px;">
                        <h4 class="mb-0 font-weight-bold"><?php echo esc($settings_info->title); ?></h4>
                        <p class="text-muted mb-5"><?php echo lan('requisition') ?>: <?php echo esc($requisition->requisition_no) ?></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-md-6">
                        <h3 class="text-uppercase text-success font-weight-600">Requisition To</h3>
                        <p class="text-muted mb-4">
                            <strong class="text-body fs-16"><?php echo esc($settings_info->title); ?>.</strong> <br>
                            <?php echo esc($settings_info->address); ?> <br>
                            <?php echo esc($settings_info->email); ?> <br>
                            P: <?php echo esc($settings_info->phone); ?>
                        </p>
                        <h6 class="text-uppercase text-muted fs-12 font-weight-600"><?php echo lan('requisition_no') ?></h6>
                        <p class="mb-4"><?php echo esc($requisition->requisition_no); ?></p>
                    </div>
                    <div class="col-12 col-md-6 text-md-right">
                        <h3 class="text-uppercase text-success font-weight-600">Created By</h3>
                        <p class="text-muted mb-4">
                            <strong class="text-body fs-16"><?php echo $userArr[$requisition->requested_by] ?? ''; ?></strong>
                        </p>
                        <h6 class="text-uppercase text-muted fs-12 font-weight-600"> Created At</h6>
                        <p class="mb-4"><time datetime=""> <?php echo date('d F Y \a\t h:i A', strtotime($requisition->created_at)); ?></time></p>
                        <!--                        <h6 class="text-uppercase text-muted fs-12 font-weight-600"> Delivery At</h6>
                                                <p class="mb-4"><time datetime=""> <?php echo date('d F Y \a\t h:i A', strtotime($requisition->delivery_date)); ?></time></p>-->
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 text-md-left"> <strong class="text-body fs-16">Name :</strong> <?php echo $customer_data->customer_name ?? '' ?></div>
                    <div class="col-md-6 text-md-right"><strong class="text-body fs-16">ID :</strong><?php echo $customer_data->user_id_num ?? ''?></div>
                    <br>
                    <div class="col-md-6 text-md-left"><strong class="text-body fs-16">Department :</strong> <?php echo $departmentName->department_name ?? '' ?></div>
                    <div class="col-md-6 text-md-right"><strong class="text-body fs-16">Designation :</strong> <?php echo $designationName->details ?? '' ?> [<?php echo $designationName->designation ?? '' ?>] </div>
                </div>

                <div class="form-group row">
                    <label for="date" class="col-md-1 text-left col-form-label">Purpose <i
                            class="text-danger"> </i>:</label>
                    <div class="col-md-4">
                        <input type="checkbox" id="vehicle1" name="purpose[]" value="Official" 
                        <?php
                        if (!empty($purpose) && in_array('Official', $purpose))
                            echo 'checked';
                        ?>>
                        <label for="vehicle1"> Official</label><br>
                        <input type="checkbox" id="vehicle2" name="purpose[]" value="Personal"
                        <?php
                        if (!empty($purpose) &&  in_array('Personal', $purpose))
                            echo 'checked';
                        ?>>
                        <label for="vehicle2"> Personal</label><br>
                        <input type="checkbox" id="vehicle2" name="purpose[]" value="Family"
                        <?php
                        if (!empty($purpose) &&  in_array('Family', $purpose))
                            echo 'checked';
                        ?>>
                        <label for="vehicle2"> Family</label><br>
                        <input type="checkbox" id="vehicle2" name="purpose[]" value="Others"
                        <?php
                        if (!empty($purpose) &&  in_array('Others', $purpose))
                            echo 'checked';
                        ?>>
                        <label for="vehicle2"> Others</label><br>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table class="table my-4">
                                <thead>
                                    <tr>
                                        <th class="px-0 bg-transparent border-top-0">
                                            <span class="h6 font-weight-bold"><?php echo lan('sl_no') ?></span>
                                        </th>
                                        <th class="px-0 bg-transparent border-top-0">
                                            <span class="h6 font-weight-bold"><?php echo lan('medicine_name') ?></span>
                                        </th>
                                        <th class="px-0 bg-transparent border-top-0">
                                            <span class="h6 font-weight-bold"><?php echo lan('quantity') ?></span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                        <tr><?php $i = 0; ?>
                                    <?php foreach ($requisition_details as $details) { ?>
                                        <?php $i++; ?>
                                            <td class="px-0"><?php echo $i; ?></td>
                                            <td class="px-0">
                                                <?php echo $productArr[$details->product_id] ?? '' ?>
                                            </td>
                                            <td class="px-0">
                                                <?php echo $details->quantity ?? ''; ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <hr class="my-5">
                    </div>
                </div>
                <div class="pharmaCustom" style="padding: 10px 0 10px 0;">
                    <p>Previous Received: <?php echo $pre_requisition_date ?? '';?> 
                    </p>
                    <div class="name__sigature" style="padding: 30px 0 0 0;">
                        <span style="border-top: 1px solid #000;">Name & Signature</span>
                    </div>
                    <div class="date" style="padding: 30px 0 0 0;">
                        Date: 
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="bothSide" style="display: flex;justify-content: space-between;padding: 30px 0 0 0;">
                                <div class="leftSide">
                                    <span style="border-top: 1px solid #000;">Recommended By</span> <br>
                                    <span>Section Head</span>
                                </div>
                                <div class="rightSide">
                                    <span style="border-top: 1px solid #000;">Approved By</span> <br>
                                    <span>Executive Director</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="lastSignature" style="padding: 40px 0 0 0;">
                        <span style="border-top: 1px solid #000;">Received Date & Signature</span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
