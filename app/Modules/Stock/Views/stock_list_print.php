<div class="row justify-content-center">
    <div class="col-12 col-lg-10 col-xl-8">
        <div class="header p-0 ml-0 mr-0 shadow-none">
            <div class="header-body">
                <div class="row align-items-center">
                    <div class="col">
<!--                        <h6 class="header-pretitle fs-10 font-weight-bold text-muted text-uppercase mb-1"><?php echo lan('payments') ?></h6>-->
                        <!--<h1 class="header-title fs-25 font-weight-600">Stock List Report</h1>-->
                    </div>
                    <div class="col-auto">
                        <a src="javascript:void(0)" onclick="printDiv('printArea')" class="btn btn-success ml-2"><i class="typcn typcn-printer mr-1"></i>Print Report</a>
                    </div>
                </div> 
            </div>
        </div>


        <div class="card card-body p-5">
            <div class="" id="printArea">
                <div style="text-align: center;">
                    <h6 style="font-size: 16px; padding: 0; margin: 0; border-bottom: 1px solid rgb(202, 202, 202);">Eskayef Pharmaceuticals Ltd.</h6>
                    <p style="border-top: 1px solid rgb(202, 202, 202); text-align: center; margin-top: 5px;">Address : OHQ, Plot:82, Road No.14, Block‐B, Banani</p>
                </div>
                <div style="display: flex; justify-content: space-between; border-top: 2px solid rgb(202, 202, 202);">
                    <div style="display: flex;">
                        <p>Branch/Depot:</p>
                        <p style="padding-left: 15px;">OHQ</p>
                    </div>
                    <div>
                        <span>Location:</span><span style="padding-left: 15px;">OHQ</span>
                    </div>
                    <div>
<!--                        <span>#1.01A</span>-->
                    </div>
                </div>
                <div style="display: flex; justify-content: space-between; margin-top: 5px;">
                    <div>
                        <p style="border: 1px solid rgb(202, 202, 202); padding: 2px; font-size: 18px;">IC Stock Status- Batch Wise </p>
                    </div>
                    <div>
                        <p style="font-size: 14px;">Print Date & Time: 
                            <?php
                            echo date('d F Y \a\t h:i A')
                            ?></p>
                    </div>
                </div>
                <div style="border: 2px solid #5e5eff; text-align: center; padding: 2px; margin-top: 2px;">
                    <img style="width: 15%" src="<?php echo base_url() . $settings_info->logo; ?>" alt="img">
                </div>
                <div>
                    <!--<p style="text-align: right;"><a style="color: #000;" href="#" download="">Download</a></p>-->
                </div>
                <div style="padding: 10px 0 0 0;">
                    <table 
                        style="border-collapse: collapse;
                        width: 100%;"
                        >
                        <tr>
                            <th style="border: 1px solid #333;padding: 2px;font-size: 11px;text-align: center;">SL</th>
                            <th style="border: 1px solid #333;padding: 2px;font-size: 11px;text-align: center;" >Prod.ID</th>
                            <th style="border: 1px solid #333;padding: 2px;font-size: 11px;text-align: center;">Name</th>
                            <th style="border: 1px solid #333;padding: 2px;font-size: 11px;text-align: center;">Batch</th>
                            <!--<th style="border: 1px solid #333;padding: 2px;font-size: 11px;text-align: center;">Unit</th>-->
                            <!--<th style="border: 1px solid #333;padding: 2px;font-size: 11px;text-align: center;">Status</th>-->
                            <th style="border: 1px solid #333;padding: 2px;font-size: 11px;text-align: center;">Qty</th>
                            <th style="border: 1px solid #333;padding: 2px;font-size: 11px;text-align: center;">TP</th>
                            <th style="border: 1px solid #333;padding: 2px;font-size: 11px;text-align: center;">TP*Qty</th>
                            <th style="border: 1px solid #333;padding: 2px;font-size: 11px;text-align: center;">Strip</th>
                            <th style="border: 1px solid #333;padding: 2px;font-size: 11px;text-align: center;">Box</th>
                            <th style="border: 1px solid #333;padding: 2px;font-size: 11px;text-align: center;">Exp. Date</th>
                        </tr>
                        <?php foreach ($results as $result) { ?>
                            <tr>
                                <td style="border: 1px solid #333;padding: 2px;font-size: 11px; text-align: left;"><?php echo $result['sl'] ?></td>
                                <td style="border: 1px solid #333;padding: 2px;font-size: 11px; text-align: left;"><?php echo $result['product_id'] ?></td>
                                <td style="border: 1px solid #333;padding: 2px;font-size: 11px; text-align: left;"><?php echo $result['product_name'] ?></td>
                                <td style="border: 1px solid #333;padding: 2px;font-size: 11px;"><?php echo $result['batch_id'] ?></td>
                                <!--<td style="border: 1px solid #333;padding: 2px;font-size: 11px;"><?php echo $result['unit'] ?></td>-->
                                <!--<td style="border: 1px solid #333;padding: 2px;font-size: 11px;"><?php echo $result['status'] == '1' ? 'Active' : 'Pending' ?></td>-->
                                <td style="border: 1px solid #333;padding: 2px;font-size: 11px;"><?php echo floor($result['stock']) ?></td>
                                <td style="border: 1px solid #333;padding: 2px;font-size: 11px;"><?php echo $result['total_price'] ?></td>
                                <td style="border: 1px solid #333;padding: 2px;font-size: 11px;"><?php echo $result['total_amount'] ?></td>
                                <td style="border: 1px solid #333;padding: 2px;font-size: 11px;"><?php echo $result['strip'] ?></td>
                                <td style="border: 1px solid #333;padding: 2px;font-size: 11px;"><?php echo $result['stock_box'] ?></td>
                                <td style="border: 1px solid #333;padding: 2px;font-size: 11px;"><?php echo date('d-M-Y', strtotime($result['expeire_date'])) ?></td>
                            </tr>
                        <?php } ?>
                    </table>
                </div>
                <!--                <div>
                                    <p style="text-align: right; padding-top: 5px;">
                                        <span style="border-bottom: 2px solid #000; margin-right: 20px;">370</span>
                                        <span style="border-bottom: 2px solid #000; margin-right: 20px;">57,73</span>
                                        <span>972</span>
                                    </p>
                                </div>-->
                <div>
                    <p><?php echo count($results) ?? '0' ?> Item(s) Printed</p>
                    <p>NB: Zero Quantity Excluded</p>
                </div>
                <div style="display: flex; justify-content: space-between; width: 70%; padding-top: 20px;">
                    <p style="border-top: 1px solid #000;">Store Department</p>
                    <p style="background-color: #000; height: 1px; width: 40%;"></p>
                </div>
                <p>Computer Generated</p>
            </div>
        </div>
