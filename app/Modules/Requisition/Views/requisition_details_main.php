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
                <div style="text-align: center;">
                    <h6 style="font-size: 14px; padding: 0; margin: 0; font-size: 18px; font-weight: bold"><?php echo strtoupper($settings_info->title) ?></h6>
                    <p style="padding: 0; margin: 0; font-weight: bold">OHQ, Plot:82, Road No.14, Block‐B, Banani</p>
                    <p style="text-decoration: underline; padding: 0; margin: 0; font-weight: bold">MEDICINE REQUISITION</p>
                </div>
                <div style="display: flex; justify-content: space-between; width: 100%; padding: 20px 0 0 0;">
                    <p>
                        <span>Date:</span> <span style="border: 1px solid #000; padding: 0 30px 0 2px;"><?php echo date('d F Y', strtotime($requisition->created_at)); ?></span>
                    </p>
                    <div style="padding: 20px 0 0 0; font-size: 18px; font-weight: bold">
                    <p>Requisition No: <?php echo $requisition->requisition_no ?></p>
                    </div>
                </div>
                <div style="display: flex; justify-content: space-between; width: 100%; padding: 20px 0 0 0; border-bottom: .5px solid #000;">
                    <p>Name: <?php echo $customer_data->customer_name ?? '' ?></p>
                    <p>Dept: <?php echo $departmentName->department_name ?></p>
                    <p>ID: <?php echo $customer_data->user_id_num ?? '' ?></p>
                </div>
                <div>
                    <p>Desig : <?php echo $designationName->details ?> [<?php echo $designationName->designation ?>]</p>
                </div>
            
                <div style="display: flex; justify-content: space-between; width: 100%; padding: 20px 0 0 0;">
                    <p>Purpose:</p>
                </div>
                <div style="padding: 20px 0 0 0;">
                    <form>
                        <div class="form-group">
                            <input type="checkbox" id="html" value="Official"
                            <?php
                            if (!empty($purpose) && in_array('Official', $purpose))
                            echo 'checked';
                            ?>>
                            <label for="html">Official [Description] ……...……….</label>
                        </div>
                        <div class="form-group">
                            <input type="checkbox" id="css"
                                   value="Personal"
                                   <?php
                                   if (!empty($purpose) && in_array('Personal', $purpose))
                                   echo 'checked';
                                   ?>>
                            <label for="css">Personal</label>
                        </div>
                        <div class="form-group">
                            <input type="checkbox" id="javascript"
                                   value="Family"
                                   <?php
                                   if (!empty($purpose) && in_array('Family', $purpose))
                                   echo 'checked';
                                   ?>>
                            <label for="javascript">Family [only Spouse, Children]</label>
                        </div>
                        <div class="form-group">
                            <input type="checkbox" id="javascript" value="Others"
                            <?php
                            if (!empty($purpose) && in_array('Others', $purpose))
                            echo 'checked';
                            ?>>
                            <label for="javascript">Others  [Please Specify] ………………..</label>
                        </div>
                        <div class="form-group">
                            <input type="checkbox" id="javascript" value="DGDA"
                            <?php
                            if (!empty($purpose) && in_array('DGDA', $purpose))
                            echo 'checked';
                            ?>>
                            <label for="javascript">DGDA</label>
                        </div>
                        <div class="form-group">
                            <input type="checkbox" id="javascript" value="CMB"
                            <?php
                            if (!empty($purpose) && in_array('CMB', $purpose))
                            echo 'checked';
                            ?>>
                            <label for="javascript">Corporate Medicine Box</label>
                        </div>
                    </form>
                </div>
                <div style="padding: 20px 0 0 0;">
                    <table 
                        style="border-collapse: collapse;
                        width: 100%;"
                        >
                        <tr>
                            <th style="border: 1px solid #333;padding: 2px;text-align: center;">SL No.</th>
                            <th style="border: 1px solid #333;padding: 2px;text-align: center;">Item ID</th>
                            <th style="border: 1px solid #333;padding: 2px;text-align: center;"> Name of Product/ Item</th>
                            <th style="border: 1px solid #333;padding: 2px;text-align: center;">Requisition  Quantity</th>
                            <th style="border: 1px solid #333;padding: 2px;text-align: center;">Received  Quantity</th>
                        </tr>
                        <?php $i = 0; ?>
                        <?php foreach ($requisition_details as $proId => $qty) { ?>
                        <?php $i++; ?>
                        <tr>
                            <td style="border: 1px solid #333;padding: 2px; text-align: center;"><?php echo $i; ?></td>
                            <td style="border: 1px solid #333;padding: 2px;text-align: center;"> <?php echo $proId ?? '' ?></td>
                            <td style="border: 1px solid #333;padding: 2px;text-align: center;"> <?php echo $productArr[$proId] ?? '' ?></td>
                            <td style="border: 1px solid #333;padding: 2px;text-align: center;"><?php echo $qty ?? ''; ?></td>
                            <td style="border: 1px solid #333;padding: 2px;text-align: center;">
                                <?php if($requisition->status == '2'){ ?>
                                <?php echo $qty ?? ''; ?>
                                <?php } ?>
                            </td>
                        </tr>
                        <?php } ?>
                    </table>
                </div>
               
                <div  style="padding: 20px 0 10px 0;">
                    <!--<p>Previous Received:</p>-->
                    <div style="padding: 50px 0 0 0;">
                        <span style="border-top: 1px solid #000;" >
                            Name & Signature
                        </span>
                    </div>
                    <div style="padding: 30px 0 0 0;">
                        Date: 
                    </div>
                    <div
                        style="display: flex;
                        justify-content: space-between;
                        padding: 50px 0 0 0;"
                        >
                        <div>
                            <span style="border-top: 1px solid #000;">Recommended By</span> <br>
                            <span>Section Head</span>
                        </div>
                        <div>
                            <span style="border-top: 1px solid #000;">Recommended By</span> <br>
                            <span>Executive Director</span>
                        </div>
                    </div>
                    <div style="padding: 50px 0 0 0;">
                        <span style="border-top: 1px solid #000;">Received Date & Signature</span>
                    </div>
                    <div style="padding: 20px 0 0 0;">
                        <span>Computer Generated</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
