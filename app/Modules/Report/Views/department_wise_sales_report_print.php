<style>

    p {
        padding: 0;
        margin: 0;
    }
    h1, h2, h3, h4, h5, h6 {
        padding: 0;
        margin: 0;
    }
    .form-group {
        display: block;
        margin-bottom: 3px;
    }

    .form-group input {
        padding: 0;
        height: initial;
        width: initial;
        margin-bottom: 0;
        display: none;
        cursor: pointer;
    }

    .form-group label {
        position: relative;
        cursor: pointer;
    }

    .form-group label:before {
        content:'';
        -webkit-appearance: none;
        background-color: transparent;
        border: 1px solid #000;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05), inset 0px -15px 10px -12px rgba(0, 0, 0, 0.05);
        padding: 5px;
        display: inline-block;
        position: relative;
        vertical-align: middle;
        cursor: pointer;
        margin-right: 10px;
    }

    .form-group input:checked + label:after {
        content: '';
        display: block;
        position: absolute;
        top: 4px;
        left: 0px;
        width: 12px;
        height: 12px;
        background: #000;
    }
    td, th {
        border: 1px solid #333;
        padding: 2px;
        font-size: 10px;
    }
    th {
        text-align: center;
    }
    table tr td:first-child {
        text-align: left;
    }
    table tr td:nth-child(2), td:nth-child(3) {
        text-align: left;
    }
    a {
        text-decoration: none;
    }
</style>
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
                    <p style="border: 1px solid rgb(202, 202, 202); text-align: center; margin-top: 5px;">Address : OHQ, Plot:82, Road No.14, Block‐B, Banani</p>
                </div>
                <div style="display: grid; row-gap: 0px; column-gap: 10px; grid-template-columns: auto 1fr 1fr 1fr 1fr; font-size: 10px; padding-top: 5px;">
                    <div>
                        <span>Tran.Date From: </span> <span><?php echo $fromDate ?></span>
                    </div>
                    <div>
                        <span>Tran.Date To: </span> <span><?php echo $toDate ?></span>
                    </div>
                    <div>
                        <span>Department:</span> <?php echo $depName ?> <span></span>
                    </div>
                    <!--                    <div>
                                            <span>Market: </span> <span></span>
                                        </div>
                                        <div>
                                            <span>Depot: </span> <span>Khulna</span>
                                        </div>
                                        <div>
                                            <span>Store:</span> <span>Commercial</span>
                                        </div>
                                        <div>
                                            <span>DP:</span> <span></span>
                                        </div>
                                        <div>
                                            <span>Territory:</span> <span></span>
                                        </div>
                                        <div>
                                            <span>Customer:</span> <?php echo $depName ?> <span></span>
                                        </div>
                                        <div>
                                            <span>MSO:</span> <span></span>
                                        </div>-->
                </div>
                <div style="display: flex; padding-top: 10px;">
                    <div style="width: 80%;">
                        <p style="border: 1px solid rgb(202, 202, 202); text-align: left;padding-left: 5px;">Department Wise Sales Details</p>
                    </div>
                    <div style="text-align: right; width: 30%;">
                        <img style="width: 30%;" src="<?php echo base_url() . $settings_info->logo; ?>" alt="img">
                    </div>
                </div>
                <div style="padding: 10px 0 0 0;">
                    <table style="border-collapse: collapse; width: 100%; border-bottom: 1px solid black;">
                        
                        <tr>
                            <th style="border: 1px solid #333;padding: 2px;font-size: 10px;text-align: center;">SL</th>
                            <th style="border: 1px solid #333;padding: 2px;font-size: 10px;text-align: center;">Department Name</th>
                            <th style="border: 1px solid #333;padding: 2px;font-size: 10px;text-align: center;">Quantity</th>
                            <th style="border: 1px solid #333;padding: 2px;font-size: 10px;text-align: center;">Total Price</th>

                        </tr>
                        <?php foreach ($results as $result) { ?>
                            <tr>
                                <td style="border: 1px solid #333;padding: 2px;font-size: 10px;text-align: left;"><?php echo $result['sl']; ?></td>
                                <td style="border: 1px solid #333;padding: 2px;font-size: 10px;text-align: left;"><?php echo $result['name'] ?? '' ?></td>
                                <td style="border: 1px solid #333;padding: 2px;font-size: 10px;text-align: left;"><?php echo $result['quantity'] ?? '' ?></td>
                                <td style="border: 1px solid #333;padding: 2px;font-size: 10px;text-align: left;"><?php echo $result['total_price'] ?? '' ?></td>
                            </tr>
                        <?php }?>
                    </table>
                </div>
                <br/>
                <br/>
                <br/>
                <div style="margin-top: 25px; margin-left: 10px;">
                    <p style="border-top: 1px solid #000; width: 20%; text-align: center;">Printed By</p>
                </div>
                <br/>
                <p style="">Computer Generated</p>
            </div>
        </div>
    </div>
</div>
