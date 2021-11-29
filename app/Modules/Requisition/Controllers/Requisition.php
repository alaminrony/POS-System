<?php

namespace App\Modules\Requisition\Controllers;

class Requisition extends BaseController {

    public function index() {
        if (!$this->session->get('isLogIn')) {
            return redirect()->route('login');
        }

        $userId = $this->session->get('id');
        $findRole = $this->db->table('sec_userrole')->where('user_id', $userId)->get()->getRow();

        if ($findRole) {
            $role = $findRole->roleid;
        } else {
            $role = '';
        }

        $data['title'] = 'Requisition List';
        $data['module'] = "Requisition";
        $data['page'] = "requisition_list";
        $data['role'] = $role;
        return $this->template->layout($data);
    }

    //  public function bdtask_CheckinvoiceList()
    //  {
    //     $postData = $this->request->getVar();
    //     $data     = $this->invoiceModel->getinvoiceList($postData);
    //     echo json_encode($data);
    // } 

    public function add_requisition() {


        $setting_data = $this->requisitionModel->setting_data();

        if ($this->request->isAJAX()) {
            $query = service('request')->getPost('query');
            var_dump($this->request->getPost('query'));
        }


        if (isset($setting_data->timezone)) {
            date_default_timezone_set($setting_data->timezone);
        }

        if (!$this->session->get('isLogIn')) {
            return redirect()->route('login');
        }

//        $customerID = $this->db->table('user')
//                ->join('sec_userrole','sec_userrole.user_id = user.id')
//                ->select('user.id,user.firstname,user.lastname')
//                ->where('sec_userrole.roleid','4')
//                ->get()
//                ->getResultArray();
//        echo "<pre>"; print_r($customerIDArr);exit;

        $userId = $this->session->get('id');
        $findRole = $this->db->table('sec_userrole')->where('user_id', $userId)->get()->getRow();

        if ($findRole) {
            $role = $findRole->roleid;
        } else {
            $role = '';
        }


        $data['module'] = "Requisition";
        $data['title'] = 'Requisition Form';
        $walking_customer = $this->requisitionModel->pos_customer_setup();
        $data['customer_id'] = ($walking_customer ? $walking_customer[0]['customer_id'] : '');
        $data['customer_name'] = ($walking_customer ? $walking_customer[0]['customer_name'] : '');
        $data['bank_list'] = $this->requisitionModel->bank_list();
        $data['customerArr'] = $this->requisitionModel->customerArr();
        $data['designationList'] = $this->requisitionModel->designationList();
        $data['departmentList'] = $this->requisitionModel->departmentList();
        $data['role'] = $role;
        $data['taxes'] = $this->requisitionModel->tax_fields();
        $data['requisition_no'] = $this->number_generator();
        $data['page'] = "requisition_form";
        return $this->template->layout($data);
    }

    public function requsition_add() {

        $productValidationArr = $this->requisitionModel->quantity_check($this->request->getVar('product_id'), $this->request->getVar('product_quantity'));

//        echo "<pre>";
//        print_r($productValidationArr);
//        exit;

        if (!empty($productValidationArr)) {
            $info['message'] = 'error';
            $info['status'] = true;
            $info['productValidationArr'] = $productValidationArr;
            echo json_encode($info);
            exit;
        }



        $reqForCustomerID = !empty($this->request->getVar('requisition_for')) ? $this->request->getVar('requisition_for') : '';
        if (!empty($reqForCustomerID)) {
            $customerUserId = $this->db->table('customer_information')->where('customer_id', $reqForCustomerID)->get()->getRow();
        }

        if (!empty($customerUserId->user_id)) {
            $requisition_for = $customerUserId->user_id;
        } else {
            $requisition_for = $this->session->get('id');
        }



        $requsitionData = array(
//            'requisition_no' => $this->request->getVar('requisition_no'),
            'requisition_no' => $this->number_generator(),
            'delivery_date' => $this->request->getVar('date'),
            'information' => $this->request->getVar('details', FILTER_SANITIZE_STRING),
            'requested_by' => $this->session->get('id'),
            'requisition_for' => $requisition_for,
            'status' => 1,
            'purpose' => !empty($this->request->getVar('purpose')) ? json_encode($this->request->getVar('purpose')) : '',
            'created_at' => date('Y-m-d H:i:s'),
        );

//        echo "<pre>";
//        print_r($requsitionData);
//        exit;



        if ($this->request->getMethod() == 'post') {

            $requsition_save_id = $this->requisitionModel->save_requsition($requsitionData);

            $requsitionDetailsData = [];
            if ($this->request->getVar('product_id') != '') {
                foreach ($this->request->getVar('product_id') as $key => $product_id) {

                    $productBatchId = $this->db->table('product_purchase_details')->where('product_id', $product_id)->where('expeire_date >=', date('Y-m-d'))->select('product_id,batch_id,quantity')->get()->getResult();
                    $productSalesBatchId = $this->db->table('invoice_details')->where('product_id', $product_id)->select('product_id,batch_id,quantity')->get()->getResult();

                    $productWiseQtyArr = [];
                    if (!empty($productBatchId)) {
                        foreach ($productBatchId as $batchId) {
                            $productWiseQtyArr[$batchId->batch_id]['quantity'][] = $batchId->quantity;
                        }
                    }

                    $productBatchIdArr = [];
                    if (!empty($productWiseQtyArr)) {
                        foreach ($productWiseQtyArr as $batchId => $quantities) {
                            $productBatchIdArr[$batchId] = array_sum($quantities['quantity']);
                        }
                    }

                    $productSalesBatchIdArr = [];
                    if (!empty($productBatchId)) {
                        foreach ($productBatchId as $batchId) {
                            if (empty($productSalesBatchId)) {
                                $productSalesBatchIdArr[$batchId->batch_id] = 0;
                            } else {
                                $SalesBatchQty = 0;
                                foreach ($productSalesBatchId as $SalesBatchId) {
                                    if ($batchId->batch_id == $SalesBatchId->batch_id) {
                                        $SalesBatchQty += $SalesBatchId->quantity;
                                        $productSalesBatchIdArr[$SalesBatchId->batch_id] = $SalesBatchQty;
                                    }
                                }
                            }
                        }
                    }



                    $productStockBatchIdArr = [];
                    if (!empty($productBatchIdArr)) {
                        foreach ($productBatchIdArr as $batchId => $purchaseValue) {
                            $productStockBatchIdArr[$batchId] = $purchaseValue - $productSalesBatchIdArr[$batchId];
                        }
                    }


                    $finalBatchArr = [];
                    if (!empty($productStockBatchIdArr)) {
                        $quantityValue = $this->request->getVar('product_quantity')[$key];

                        foreach ($productStockBatchIdArr as $finalBatchId => $finalStock) {
                            if ($finalStock > 0) {
                                if ($quantityValue <= $finalStock) {
                                    $finalBatchArr[$finalBatchId] = $quantityValue;
                                    break;
                                } elseif ($quantityValue >= $finalStock) {
                                    $batchReceiveQty = $quantityValue - $finalStock;
                                    $finalBatchArr[$finalBatchId] = $finalStock;
                                }

                                $quantityValue = $batchReceiveQty;
                            }
                        }
                    }



//                    echo "<pre>";
//                    print_r($productBatchIdArr);
//                    echo "<pre>";
//                    print_r($productSalesBatchIdArr);
//                    echo "<pre>";
//                    print_r($productStockBatchIdArr);
//                    echo "<pre>";
//                    print_r($finalBatchArr);
//                    exit;


                    if (!empty($finalBatchArr)) {
                        $i = 1;
                        $randomNumber = mt_rand();
                        foreach ($finalBatchArr as $batchId => $batchQty) {
                            $requsitionDetailsData[$i . $randomNumber]['requisition_id'] = $requsition_save_id;
                            $requsitionDetailsData[$i . $randomNumber]['batch_id'] = $batchId;
                            $requsitionDetailsData[$i . $randomNumber]['product_id'] = $product_id;
                            $requsitionDetailsData[$i . $randomNumber]['quantity'] = $batchQty;
                            $requsitionDetailsData[$i . $randomNumber]['unit'] = $this->request->getVar('product_unit')[$key];
                            $requsitionDetailsData[$i . $randomNumber]['requested_by'] = $this->session->get('id');
                            $requsitionDetailsData[$i . $randomNumber]['status'] = 1;
                            $requsitionDetailsData[$i . $randomNumber]['created_at'] = date('Y-m-d H:i:s');
                            $i++;
                        }
                    }
                }
            }

//            echo "<pre>";
//            print_r($requsitionDetailsData);
//            exit;

            $requsition_save_id = $this->requisitionModel->save_requsition_details($requsitionDetailsData);
            if (!empty($requsition_save_id)) {
                $info['message'] = 'success';
                $info['status'] = true;
                echo json_encode($info);
                exit;
            }
        }
    }

    public function requisition_edit($id) {
        $userId = $this->session->get('id');
        $findRole = $this->db->table('sec_userrole')->where('user_id', $userId)->get()->getRow();

        if ($findRole) {
            $role = $findRole->roleid;
        } else {
            $role = '';
        }




        $data['requisition'] = $this->requisitionModel->singleRequisition($id);
        $data['requisition_details'] = $this->requisitionModel->singleRequisitionDetails($id);
        $data['productArr'] = $this->requisitionModel->productList();
        $data['role'] = $role;

        $data['requisition_no'] = $this->number_generator();
        $data['module'] = "Requisition";
        $data['title'] = 'Requisition Edit';
        $data['page'] = "requisition_edit_form";
        return $this->template->layout($data);
    }

    public function requsition_update() {
//        echo "<pre>";
//        print_r($this->request->getVar());
//        exit;
        $requsitionData = array(
            'requisition_no' => $this->request->getVar('requisition_no'),
            'delivery_date' => $this->request->getVar('date'),
            'information' => $this->request->getVar('details', FILTER_SANITIZE_STRING),
            'requested_by' => $this->session->get('id'),
            'status' => $this->request->getVar('status'),
            'created_at' => date('Y-m-d H:i:s'),
        );

        if ($this->request->getMethod() == 'post') {
            $requisition_id = $this->request->getVar('requisition_id');

            $requsition_update = $this->requisitionModel->update_requsition($requisition_id, $requsitionData);

//            echo "<pre>";print_r($requsition_update);exit;

            $requsitionDetailsData = [];
            if ($this->request->getVar('product_id') != '') {
                foreach ($this->request->getVar('product_id') as $key => $product_id) {
                    $requsitionDetailsData[$key + 1]['requisition_id'] = $requisition_id;
                    $requsitionDetailsData[$key + 1]['product_id'] = $product_id;
                    $requsitionDetailsData[$key + 1]['quantity'] = $this->request->getVar('product_quantity')[$key];
                    $requsitionDetailsData[$key + 1]['unit'] = $this->request->getVar('product_unit')[$key] ?? '';
                    $requsitionDetailsData[$key + 1]['requested_by'] = $this->session->get('id');
                    $requsitionDetailsData[$key + 1]['status'] = 1;
                    $requsitionDetailsData[$key + 1]['created_at'] = date('Y-m-d H:i:s');
                }
            }

            $requsition_details_update = $this->requisitionModel->update_requsition_details($requisition_id, $requsitionDetailsData);
            if (!empty($requsition_details_update)) {
                $info['message'] = 'success';
                $info['status'] = true;
                echo json_encode($info);
                exit;
            }
        }



        $data['requisition'] = $this->requisitionModel->singleRequisition($id);
        $data['requisition_details'] = $this->requisitionModel->singleRequisitionDetails($id);
        $data['productArr'] = $this->requisitionModel->productList();
        $data['module'] = "Requisition";
        $data['title'] = 'Requisition Edit';
        $data['page'] = "requisition_edit_form";
        return $this->template->layout($data);
    }

    public function requisition_status_update() {
        $alreadyApprovedRequisition = $this->db->table('requisition')->select('id')->where('status', 2)->get()->getResult();
        if (!empty($alreadyApprovedRequisition)) {
            $approvedRequisitionArr = [];
            foreach ($alreadyApprovedRequisition as $approvedRequisition) {
                $approvedRequisitionArr[] = $approvedRequisition->id;
            }
        }


        if ($this->request->getMethod() == 'post') {
            $requisitionIdList = $this->request->getVar('requisitionIdArr');

            if (!empty($approvedRequisitionArr)) {
                $requisitionIdArr = array_diff($requisitionIdList, $approvedRequisitionArr);
            } else {
                $requisitionIdArr = $requisitionIdList;
            }

//             echo "<pre>";print_r($requisitionIdArr);exit;
            if ($requisitionIdArr) {
                $data = [
                    'status' => '2',
                ];
                $updateData = [];
                foreach ($requisitionIdArr as $requisitionId) {
                    $updateData[] = $this->db->table('requisition')->update($data, ['id' => $requisitionId]);
                }
            }

            if ($requisitionIdArr) {
                foreach ($requisitionIdArr as $requisitionId) {

                    $requisition = $this->db->table('requisition')->where('id', $requisitionId)->get()->getRow();
                    $requisition_details = $this->db->table('requisition_item')->where('requisition_id', $requisitionId)->get()->getResult();
                    $customer_previous_amount = $this->requisitionModel->previous($requisition->requisition_for);

//                    echo "<pre>";print_r($requisition);exit;
                    $totalAmountReq = 0;
                    if ($requisition_details) {
                        foreach ($requisition_details as $reqItem) {
                            $reqItemPrice = 0;
                            $reqItemDetails = $this->db->table('product_information')->where('product_id', $reqItem->product_id)->get()->getRow();

                            if (!empty($reqItemDetails)) {
                                $reqItemPrice = $reqItemDetails->price * $reqItem->quantity;
                            }

                            $totalAmountReq += $reqItemPrice;
                        }
                    }

//                    if (!empty($customer_previous_amount)) {
//                        $requisitionTotalAmount = $totalAmountReq + $customer_previous_amount;
//                    } else {
//                        $requisitionTotalAmount = $totalAmountReq;
//                    }

                    $requisitionTotalAmount = $totalAmountReq;

                    $reqForCustomerID = !empty($requisition->requisition_for) ? $requisition->requisition_for : '';
                    if (!empty($reqForCustomerID)) {
                        $customerUserId = $this->db->table('customer_information')->where('user_id', $reqForCustomerID)->get()->getRow();
                    }

                    if (!empty($customerUserId->customer_id)) {
                        $requisition_for = $customerUserId->customer_id;
                    } else {
                        $requisition_for = '';
                    }


                    $invoice_id = $this->generator(10);
                    $data = [];
                    $data['invoice'] = (object) $invoiceData = array(
                        'invoice_id' => $invoice_id,
                        'requisition_id' => $requisitionId,
                        'requisition_no' => $requisition->requisition_no,
                        'customer_id' => $requisition_for,
                        'date' => date('Y-m-d'),
                        'total_amount' => $requisitionTotalAmount ?? 0,
                        'invoice' => $this->invoice_number_generator(),
                        'total_tax' => 0,
                        'prevous_due' => $customer_previous_amount ?? 0,
                        'paid_amount' => ($this->request->getVar('paid_amount') ? $this->request->getVar('paid_amount') : 0),
                        'due_amount' => $totalAmountReq ?? 0,
                        'total_discount' => ($this->request->getVar('total_discount') ? $this->request->getVar('total_discount') : 0),
                        'invoice_discount' => ($this->request->getVar('invoice_discount') ? $this->request->getVar('invoice_discount') : 0),
                        'bank_id' => ($this->request->getVar('bank_id') ? $this->request->getVar('bank_id') : 0),
                        'sales_by' => $requisition->requested_by,
                        'invoice_details' => 'Thank you',
                        'payment_type' => ($this->request->getVar('payment_type') ? $this->request->getVar('payment_type') : 1),
                        'status' => 1
                    );

                    $invoice_id = $this->requisitionModel->save_invoice($invoiceData, $requisition_details);
                }
            }

            if (!empty($updateData)) {
                $info['message'] = 'success';
                $info['status'] = true;
                echo json_encode($info);
                exit;
            }
        }
    }

    public function requisition_not_approved() {
        $requisitionId = $this->request->getVar('requisition_id');
        if (!empty($requisitionId)) {
            $requisition = $this->requisitionModel->not_approved_requsition($requisitionId);
            if (!empty($requisition)) {
                $info['message'] = 'success';
                $info['status'] = true;
                echo json_encode($info);
                exit;
            }
        }
    }

    public function delete_requisition($id) {
        if ($this->requisitionModel->delete_requisition($id)) {
            $this->session->setFlashdata('message', lan('successfully_deleted'));
        } else {
            $this->session->setFlashdata('exception', lan('please_try_again'));
        }

        return redirect()->route('requisition/requisition_list');
    }

    public function requisition_details($id) {


        $data['requisition'] = $this->requisitionModel->singleRequisition($id);
        $data['requisition_details'] = $this->requisitionModel->productWiseRequisitionDetails($id);
        $data['productArr'] = $this->requisitionModel->productList();
        $data['userArr'] = $this->requisitionModel->userList();

        $data['customer_data'] = $this->requisitionModel->customer_details_data($data['requisition']->requisition_for);

        $data['designationName'] = '';
        if (!empty($data['customer_data']->designation)) {
            $data['designationName'] = $this->requisitionModel->designationName($data['customer_data']->designation);
        }
        $data['departmentName'] = '';
        if (!empty($data['customer_data']->department)) {
            $data['departmentName'] = $this->requisitionModel->departmentName($data['customer_data']->department);
        }


        $data['purpose'] = !empty($data['requisition']->purpose) ? json_decode($data['requisition']->purpose) : '';

        $data['pre_requisition_data'] = $this->requisitionModel->pre_requisition($data['requisition']->requisition_for, $data['requisition']->created_at);

//        echo "<pre>";print_r($data['pre_requisition_data']);exit;

        $data['title'] = 'Requisition Details';
        $data['module'] = "Requisition";
        $data['page'] = "requisition_details_main";
        return $this->template->layout($data);
    }

    public function bdtask_CheckRequisitionList() {
        $postData = $this->request->getVar();
        $data = $this->requisitionModel->getRequisitionList($postData);
        echo json_encode($data);
    }

    public function generator($lenth) {
        $number = array("1", "2", "3", "4", "5", "6", "7", "8", "9", "0");

        for ($i = 0; $i < $lenth; $i++) {
            $rand_value = rand(0, 9);
            $rand_number = $number["$rand_value"];

            if (empty($con)) {
                $con = $rand_number;
            } else {
                $con = "$con" . "$rand_number";
            }
        }
        return $con . date('s');
    }

    public function number_generator() {
        $result = $this->db->table('requisition')
                ->select('max(requisition_no) as requisition_no')
                ->get()
                ->getResultArray();

        $requisition_no = $result[0]['requisition_no'];
//         echo "<pre>";print_r($requisition_no);exit;
        if ($requisition_no != '') {
            $requisition_no = $requisition_no + 1;
        } else {
            $requisition_no = 1000;
        }
        return $requisition_no;
    }

    public function invoice_number_generator() {
        $result = $this->db->table('invoice')
                ->select('max(invoice) as invoice_no')
                ->get()
                ->getResultArray();
        $invoice_no = $result[0]['invoice_no'];
        if ($invoice_no != '') {
            $invoice_no = $invoice_no + 1;
        } else {
            $invoice_no = 1000;
        }
        return $invoice_no;
    }

    public function requisition_customer_data() {
        if ($this->request->getMethod() == "post") {
            $id = $this->request->getVar('customer_id');

            $customer_data = $this->requisitionModel->customer_data($id);

            $designationID = $customer_data->designation ?? 0;
            $designationName = $this->requisitionModel->designationName($designationID);

            $departmentID = $customer_data->department ?? 0;
            $departmentName = $this->requisitionModel->departmentName($departmentID);

            $info['message'] = 'success';
            $info['user_id'] = $customer_data->user_id_num ?? '';
            if (!empty($designationName) && !empty($designationName->designation)) {
                $designation = $designationName->details . ' ' . $designationName->designation;
            } else {
                $designation = '';
            }
            $info['designation'] = $designation;
            $info['department'] = $departmentName->department_name ?? '';
            $info['cus_id'] = $customer_data->customer_id ?? '';
            echo json_encode($info);
            exit;
//            return $this->response->setJSON($customer_data);
            // echo "<pre>";print_r($this->request->getVar("name"));exit;
        }
    }

    public function autocompleteproductsearch() {

        $product_name = $this->request->getVar('product_name');
        $product_info = $this->requisitionModel->autocompletproductdata($product_name);

        if (!empty($product_info)) {
            $json_product[''] = '';
            foreach ($product_info as $value) {
                $json_product[] = array('label' => $value['product_id'] . '--' . $value['product_name'] . '(' . $value['strength'] . ')', 'value' => $value['product_id']);
            }
        } else {
            $json_product[] = 'No Medicine Found';
        }
        echo json_encode($json_product);
    }

    public function retrieve_product_data_inv() {
        $product_id = $this->request->getVar('product_id', FILTER_SANITIZE_STRING);
        $product_info = $this->requisitionModel->get_total_product_invoic($product_id);
        echo json_encode($product_info);
    }

    public function retrieve_product_batchid() {
        $batch_id = $this->request->getVar('batch_id');
        $product_id = $this->request->getVar('product_id');
//        echo "<pre>";print_r($this->request->getVar());exit;
        $product_info = $this->requisitionModel->get_total_product_batch($batch_id, $product_id);

        echo json_encode($product_info);
    }

}
