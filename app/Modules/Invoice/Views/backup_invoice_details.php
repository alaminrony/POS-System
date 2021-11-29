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
                        <a href="<?php echo base_url('invoice/invoice_list') ?>" class="btn btn-success-soft ml-2"><i class="fas fa-align-justify mr-1"></i><?php echo lan('invoice_list') ?></a>
                        <a src="javascript:void(0)" onclick="printDiv('printArea')" class="btn btn-success ml-2"><i class="typcn typcn-printer mr-1"></i><?php echo lan('print_invoice') ?> </a>
                    </div>
                </div> 
            </div>
        </div>


        <div class="card card-body p-5">
            <div class="" id="printArea">
                <div class="row">
                    <div class="col text-center">
                        <h4 class="mb-0 font-weight-bold"><?php echo esc($settings_info->title); ?></h4>
                    </div>
                </div>
                <div class="border-top my-1"></div>
                <div class="border-top my-1"></div>
                <p class="text-center p-1 m-1">OHQ, Plot-84, Road-12, Block-B, Banani, Dhaka.mis@skf.transcombd.com P: 0961998086</p>
                <div class="border-top p-0 m-0" style="border: 2px solid #999793;"></div>
                <div class="col text-center">
                    <h4 class="mb-0 font-weight-bold">INVOICE</h4>
                </div>
                <div class="float-right">
                    <h6 class="font-weight-bold">SKINV220-689466</h6>
                </div>
                <br>
                <br>
                <div class="row">
                    <div class="col-12 col-md-6">
                        <h3 class="text-uppercase font-weight-600" style="font-size: 16px !important;">Bill To</h3>
                        <p class="text-dark mb-4">
                            Customer Id: <b>2323</b> <br>
                            Customer Name: <b>Alamin Rony</b> <br>
                        </p>
                        <h6 class="text-uppercase text-muted fs-12 font-weight-600"><?php echo lan('invoice_no') ?></h6>
                        <p class="mb-4"><?php echo esc($invoice->invoice); ?></p>
                    </div>
                    <div class="col-12 col-md-6 text-md-right">
                        <h3 class="text-uppercase font-weight-600" style="font-size: 16px !important;">Customer Information</h3>
                        <p class="text-muted mb-4">
                            <strong class="text-body fs-14"><?php echo esc($invoice->customer_name); ?></strong> 
                            <?php if ($invoice->customer_address) { ?>
                                <br>
                                <?php echo esc($invoice->customer_address) ?> <?php } ?>
                            <?php if ($invoice->user_id_num) { ?>
                                <br>
                                ID : <?php echo esc($invoice->user_id_num) ?>
                            <?php } ?>
                            <br>
                            <?php if ($invoice->customer_mobile) { ?>
                                <br>
                                P: <?php echo esc($invoice->customer_mobile) ?>
                            <?php } ?>
                        </p>
                        <h6 class="text-uppercase text-muted fs-12 font-weight-600"> <?php echo lan('date') ?></h6>
                        <p class="mb-4"><time datetime=""> <?php echo esc($invoice->date); ?></time></p>
                    </div>
                </div> 
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
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
                            <table class="table my-4">
                                <thead>
                                    <tr>
                                        <th class="px-0 bg-transparent border-top-0" >
                                            <span class="font-weight-bold"><?php echo lan('sl_no') ?></span>
                                        </th>
                                        <th class="px-0 bg-transparent border-top-0">
                                            <span class="font-weight-bold"><?php echo lan('medicine_name') ?></span>
                                        </th>
                                        <th class="px-0 bg-transparent border-top-0">
                                            <span class="font-weight-bold"><?php echo lan('quantity') ?></span>
                                        </th>
                                        <th class="px-0 bg-transparent border-top-0">
                                            <span class="font-weight-bold">Return Qty</span>
                                        </th>
                                        <th class="px-0 bg-transparent border-top-0 text-right">
                                            <span class="font-weight-bold"><?php echo lan('price') ?></span>
                                        </th>
                                        <?php if ($total_dis > 0) { ?>
                                            <th class="px-0 bg-transparent border-top-0 text-right"><span class="h6 font-weight-bold"><?php echo lan('discount') ?></span></th>
                                        <?php } ?>
                                        <th class="px-0 bg-transparent border-top-0 text-right">
                                            <span class="font-weight-bold"><?php echo lan('total_amount') ?></span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sum_total = 0;
                                    $sum_deduction = 0;
                                    if ($details) {
                                        $sl = 1;
                                        foreach ($details as $details) {
                                            ?>
                                            <tr>
                                                <td class="px-0"><?php echo $sl++; ?></td>
                                                <td class="px-0">
                                                    <?php echo esc($details['product_name']) . ' (' . esc($details['strength']) . ')' ?>
                                                </td>
                                                <td class="px-0">
                                                    <?php echo esc($details['quantity']) ?>
                                                </td>
                                                <td class="px-0">
                                                    <?php echo esc($returnProducts[$details['product_id']] ?? 0) ?>
                                                </td>
                                                <td class="px-0 text-right">
                                                    <?php echo esc(number_format($details['rate'], 2)) ?>
                                                </td>

                                                <?php if ($total_dis > 0) { ?>
                                                    <td class="px-0 text-right"><?php echo esc($details['discount']) ?></td>
                                                <?php } ?>
                                                <td class="px-0 text-right">
                                                    <?php
                                                    $remQtyPrice = ($details['quantity'] - $returnProducts[$details['product_id']]) * $details['rate'];

                                                    $sum_deduction = $sum_deduction + ($returnProducts[$details['product_id']] * $details['rate']);
                                                    $sum_total = $sum_total + $remQtyPrice;
                                                    echo esc(number_format($remQtyPrice, 2));
                                                    ?>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                    }
                                    ?>

                                    <tr>
                                        <td class="px-0 border-top border-top-2 text-right" colspan="<?php echo 5 + $colspan ?>">
                                            <strong><?php echo lan('sub_total') ?></strong>
                                        </td>
                                        <td class="px-0 text-right border-top border-top-2">
                                            <span class="fs-16 font-weight-600">
                                                <?php echo number_format($sum_total, 2); ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <!--
                                    <?php if ($invoice->invoice_discount > 0) { ?>
                                                <tr>
                                                    <td class="px-0 border-top border-top-2 text-right" colspan="////<?php echo 5 + $colspan ?>">
                                                        <strong>////<?php echo lan('invoice_discount') ?></strong>
                                                    </td>
                                                    <td class="px-0 text-right border-top border-top-2">
                                                        <span class="fs-16 font-weight-600">
                                        <?php echo esc($invoice->invoice_discount); ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                    <?php } ?>

                                    <?php if ($invoice->prevous_due > 0) { ?>
                                                <tr>
                                                    <td class="px-0 border-top border-top-2 text-right" colspan="////<?php echo 5 + $colspan ?>">
                                                        <strong>////<?php echo lan('previous') ?></strong>
                                                    </td>
                                                    <td class="px-0 text-right border-top border-top-2">
                                                        <span class="fs-16 font-weight-600">
                                        <?php echo esc($invoice->prevous_due); ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                    <?php } ?>

                                    <?php if ($invoice->total_tax > 0) { ?>
                                                <tr>
                                                    <td class="px-0 border-top border-top-2 text-right" colspan="////<?php echo 5 + $colspan ?>">
                                                        <strong>////<?php echo lan('total_vat') ?></strong>
                                                    </td>
                                                    <td class="px-0 text-right border-top border-top-2">
                                                        <span class="fs-16 font-weight-600">
                                        <?php echo esc($invoice->total_tax); ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                    <?php } ?>
                                    -->
                                    <tr>
                                        <td class="px-0 border-top border-top-2 text-right" colspan="<?php echo 5 + $colspan ?>">
                                            <strong><?php echo lan('grand_total') ?></strong>
                                        </td>
                                        <td class="px-0 text-right border-top border-top-2">
                                            <span class="fs-16 font-weight-600">
                                                <?php echo esc(number_format($invoice->total_amount - $sum_deduction, 2)); ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <!--
                                    <?php if ($invoice->paid_amount > 0) { ?>
                                                <tr>
                                                    <td class="px-0 border-top border-top-2 text-right" colspan="<?php echo 5 + $colspan ?>">
                                                        <strong><?php echo lan('paid_amount') ?></strong>
                                                    </td>
                                                    <td class="px-0 text-right border-top border-top-2">
                                                        <span class="fs-16 font-weight-600">
                                        <?php echo esc($invoice->paid_amount); ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                    <?php } ?>
                                    
                                    <?php if ($invoice->due_amount > 0) { ?>
                                                <tr>
                                                    <td class="px-0 border-top border-top-2 text-right" colspan="<?php echo 5 + $colspan ?>">
                                                        <strong><?php echo lan('due_amount') ?></strong>
                                                    </td>
                                                    <td class="px-0 text-right border-top border-top-2">
                                                        <span class="fs-16 font-weight-600">
                                        <?php echo esc($invoice->due_amount - $sum_deduction); ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                    <?php } ?>
                                    -->
                                </tbody>
                            </table>
                        </div>
                        <hr class="my-5">
                        <h6 class="text-uppercase font-weight-bold">Comments </h6>
                        <p class="text-muted mb-0">
                            thank you
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
