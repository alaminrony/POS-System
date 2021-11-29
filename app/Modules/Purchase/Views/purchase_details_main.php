<div class="row justify-content-center">
    <div class="col-12 col-lg-10 col-xl-8">
        <div class="header p-0 ml-0 mr-0 shadow-none">
            <div class="header-body">
                <div class="row align-items-center">
                    <div class="col">
                        <h6 class="header-pretitle fs-10 font-weight-bold text-muted text-uppercase mb-1"><?php echo lan('payments') ?></h6>
                        <h1 class="header-title fs-25 font-weight-600" ><?php echo lan('invoice_no') ?>: <?php echo $invoice->invoice ?></h1>
                    </div>
                    <div class="col-auto">
                        <a href="<?php echo base_url('purchase/purchase_list') ?>" class="btn btn-success-soft ml-2"><i class="fas fa-align-justify mr-1"></i><?php echo lan('purchase_list') ?></a>
                        <a onclick="printDiv('printArea')" class="btn btn-success ml-2"><i class="typcn typcn-printer mr-1"></i><?php echo lan('print_invoice') ?> </a>
                    </div>
                </div> 
            </div>
        </div>
        <div class="card card-body p-5">
            <div class="" id="printArea">
                <!-- Header Section -->
                <header class="header_wrapper">
                    <div class="header_title" style="position: relative">
                        <h1
                            style="
                            text-align: center;
                            margin-top: 15px;
                            font-weight: 500;
                            font-size: 35px;
                            "
                            >
                                <?php echo esc($settings_info->title); ?>
                        </h1>
                        <p style="border-bottom: 1px solid #9c9c9c; margin-top: 7px"></p>
                        <p style="border-bottom: 1px solid #9c9c9c; margin-top: 7px"></p>
                    </div>
                    <ul
                        class="header_adress"
                        style="
                        margin-top: 18px;
                        display: -webkit-box;
                        display: -ms-flexbox;
                        display: flex;
                        -webkit-box-align: center;
                        -ms-flex-align: center;
                        align-items: center;
                        -ms-flex-wrap: wrap;
                        flex-wrap: wrap;
                        font-size: 12px;
                        -webkit-box-pack: center;
                        -ms-flex-pack: center;
                        justify-content: center;
                        padding-bottom: 5px;
                        border-bottom: 2px solid #a2a2a2;
                        ">
                        <li style="color: #000000;list-style-type: none;">
                            <?php echo esc($settings_info->address); ?>,
                        </li>
                        <li style="color: #000000;list-style-type: none;"> Phone : <?php echo esc($settings_info->phone); ?>,</li>
                        <li style="color: #000000;list-style-type: none;">
                            Email : <a href="mailto:" style="color: #000000;list-style-type: none;"
                                       ><?php echo esc($settings_info->email); ?></a
                            >
                        </li>
                    </ul>
                    <div class="invoice_title">
                        <h5
                            style="
                            text-align: center;
                            font-size: 24px;
                            font-weight: 600;
                            text-transform: uppercase;
                            "
                            >
                            invoice
                        </h5>
                    </div>
                    <div class="invoice_id_number">
                        <h6
                            style="
                            text-align: end;
                            font-size: 20px;
                            font-weight: 600;
                            margin: 5px 0;
                            "
                            >
                            Invoice No :<?php echo $purchase->chalan_no ?>
                        </h6>
                    </div>
                </header>
                <!-- Customer Info Section -->
                <section
                    class="customer_info_section"
                    style="
                    display: -ms-grid;
                    display: grid;
                    -ms-grid-columns: 1fr 1fr 1fr;
                    grid-template-columns: 1fr 1fr 1fr;
                    grid-gap: 0 10px;
                    border-bottom: 2px solid #a2a2a2;
                    "
                    >
                    <div class="customer_billing billing_area" style="margin-top: 15px">
                        <h3 style="font-size: 15px; font-weight: 600;">Bill To:</h3>
                        <ul style="margin-left: -40px;">
                            <li
                                style="
                                font-size: 13px;
                                display: -ms-grid;
                                display: grid;
                                -webkit-box-align: center;
                                -ms-flex-align: center;
                                align-items: center;
                                -ms-grid-columns: 50% 10% 40%;
                                grid-template-columns: 50% 10% 40%;
                                "
                                >
                                <p style="font-weight: 500;white-space: nowrap;"><?php echo lan('purchase_id') ?> : <?php echo $purchase->purchase_id; ?></p>
                            </li>  
                        </ul>
                    </div>

                    <div class="customer_billing" style="margin-top: 15px">
                        <div
                            class="date_header"
                            style="
                            display: -webkit-box;
                            display: -ms-flexbox;
                            display: flex;
                            -webkit-box-align: center;
                            -ms-flex-align: center;
                            align-items: center;
                            "
                            >
                            <h3 style="font-size: 15px; font-weight: 600">Date:</h3>
                            <h5 style="font-size: 22px; font-weight: 600"> <?php echo esc($purchase->purchase_date); ?></h5>
                        </div>
                        <div class="logo" style="text-align: center;">
                            <img
                                src="<?php echo base_url() . '/assets/dist/img/skf.png'; ?>"
                                alt="Eskaf Logo"
                                style="width: 150px; margin-top: 2px"
                                />
                        </div>
                    </div>

                </section>
                <!-- Order Status Section -->


                <!-- Invoice Table Section -->
                <section class="table_wrapper" style="margin-top: 10px">
                    <?php
                    $total_dis = 0;
                    foreach ($details as $dis_per) {
                        $total_dis += $dis_per['discount'];
                    }
                    $colspan = 0;
                    if ($total_dis > 0) {
                        $colspan = 1;
                    }
                    ?>
                    <table
                        style="width: 100%; border-collapse: collapse; border: 1px solid black"
                        >
                        <thead>
                            <tr>
                                <th style="border: 1px solid black; font-weight: 600; width: 6%;"><?php echo lan('sl_no') ?></th>
                                <th
                                    class="medicine_name_th"
                                    style="border: 1px solid black; font-weight: 600; width: 50%"
                                    >
                                        <?php echo lan('medicine_name') ?>
                                </th>
                                <th style="border: 1px solid black; font-weight: 600"><?php echo lan('qty_box') ?></th>
                                <th style="border: 1px solid black; font-weight: 600"><?php echo lan('pcs') ?></th>
                                <th style="border: 1px solid black; font-weight: 600"><?php echo lan('purchase_price') ?></th>
                                <th style="border: 1px solid black; font-weight: 600"><?php echo lan('per_pcs_price') ?></th>
                                <th style="border: 1px solid black; font-weight: 600"><?php echo lan('total_amount') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sum_total = 0;
                            if ($details) {
                                $sl = 1;
                                foreach ($details as $details) {
                                    ?>
                                    <tr>
                                        <td style="border: 1px solid black"><?php echo $sl++; ?></td>
                                        <td style="border: 1px solid black"> <?php echo esc($details['product_name']) . ' (' . esc($details['strength']) . ')' ?></td>
                                        <td style="border: 1px solid black; text-align: end"><?php echo esc($details['box_qty']) ?></td>
                                        <td style="border: 1px solid black; text-align: end"> <?php echo esc($details['quantity']) ?></td>
                                        <td style="border: 1px solid black; text-align: end"><?php echo esc($details['rate']) ?></td>
                                        <td style="border: 1px solid black; text-align: end"> <?php echo esc($details['unit_rate']) ?></td>
                                        <td style="border: 1px solid black; text-align: end">
                                            <?php
                                            echo esc($details['total_amount']);
                                            $sum_total += $details['total_amount'];
                                            ?>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                            <tr>
                                <td
                                    colspan="6"
                                    style="font-weight: 600; text-align: end; padding-right: 5px"
                                    >
                                    Sub Total:
                                </td>
                                <td
                                    style="border: 1px solid black; text-align: end; font-weight: 600"
                                    >
                                         <?php echo number_format($sum_total, 2); ?>
                                </td>
                            </tr>


<!--                            <tr>
    <td colspan="2" style="font-weight: 600">Sales Terms: Cash</td>
    <td colspan="3" style="text-align: end; padding-right: 5px">
        Less: Special Discount:
    </td>
    <td
        style="border: 1px solid black; text-align: end; font-weight: 600"
        >
        150
    </td>
</tr>-->
                            <tr>
<!--                                <td colspan="2" style="font-weight: 600">
                                    Taka in Words:
                                    <span style="font-weight: 400" id="words"></span>
                                    <input id="number" type="text" onload="inWords()"/>
                                </td>-->
                                <td
                                    colspan="6"
                                    style="text-align: end; padding-right: 5px; font-weight: 600"
                                    >
                                    <?php echo lan('grand_total') ?>:
                                </td>
                                <td
                                    style="border: 1px solid black; text-align: end; font-weight: 600"
                                    >
                                        <?php echo $purchase->grand_total_amount; ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </section>

                <!-- Remark Section -->
                <section class="remark_wrapper" style="margin-top: 50px">
                    <h6><b>Remark:</b></h6>
                    <div
                        class="remark_box_area"
                        style="
                        border: 1px solid black;
                        display: -ms-grid;
                        display: grid;
                        -ms-grid-columns: 1fr 1fr;
                        grid-template-columns: 1fr 1fr;
                        grid-gap: 10px;
                        padding: 5px;
                        "
                        >
                        <div class="remark_left">
                            <ul>
                                <li
                                    style="
                                    display: -ms-grid;
                                    display: grid;
                                    -ms-grid-columns: 40% 60%;
                                    grid-template-columns: 40% 60%;
                                    font-size: 14px;
                                    margin-top: 5px;
                                    "
                                    >
                                    <p style="font-weight: 600">Delivered By :</p>

                                </li>
                                <li
                                    style="
                                    display: -ms-grid;
                                    display: grid;
                                    -ms-grid-columns: 40% 60%;
                                    grid-template-columns: 40% 60%;
                                    font-size: 14px;
                                    margin-top: 5px;
                                    "
                                    >
                                    <p style="font-weight: 600">Prepared By :</p>

                                </li>
                            </ul>
                        </div>
                        <div class="remark_right" style="margin-top: 10px">
                            <ul>
                                <li
                                    style="
                                    display: -ms-grid;
                                    display: grid;
                                    -ms-grid-columns: 40% 60%;
                                    grid-template-columns: 40% 60%;
                                    font-size: 14px;
                                    margin-top: 5px;
                                    "
                                    >
                                    <p style="font-weight: 600;white-space: nowrap;">Created By : <?php echo $userArr[$purchase->purchase_by] ?></p>
                                </li>

                            </ul>
                        </div>
                    </div>
                </section>
                <!-- Signature Section -->
                <section
                    class="signature_wrapper"
                    style="
                    margin-top: 50px;
                    margin-bottom: 10px;
                    display: -ms-grid;
                    display: grid;
                    -ms-grid-columns: 60% 40%;
                    grid-template-columns: 60% 40%;
                    grid-gap: 20px;
                    "
                    >
                    <div
                        class="signature_left"
                        style="
                        display: -ms-grid;
                        display: grid;
                        -ms-grid-columns: 1fr 1fr 1fr;
                        grid-template-columns: 1fr 1fr 1fr;
                        grid-gap: 10px;
                        "
                        >
                        <h5
                            style="
                            font-size: 14px;
                            font-weight: 600;
                            border-top: 1px solid black;
                            text-align: center;
                            padding-top: 6px;
                            "
                            >
                            Customer
                        </h5>
                        <h5
                            style="
                            font-size: 14px;
                            font-weight: 600;
                            border-top: 1px solid black;
                            text-align: center;
                            padding-top: 6px;
                            "
                            >
                            Accounts
                        </h5>
                        <h5
                            style="
                            font-size: 14px;
                            font-weight: 600;
                            border-top: 1px solid black;
                            text-align: center;
                            padding-top: 6px;
                            "
                            >
                            Store
                        </h5>
                    </div>
                    <div class="signature_right">
                        <h5
                            style="
                            font-size: 14px;
                            font-weight: 600;
                            border-top: 1px solid black;
                            text-align: center;
                            padding-top: 6px;
                            "
                            >
                            For <?php echo esc($settings_info->title); ?>
                        </h5>
                    </div>
                </section>
                Computer Generated
            </div>
        </div>
    </div>
</div>

