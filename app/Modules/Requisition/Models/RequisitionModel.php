<?php

namespace App\Modules\Requisition\Models;

use App\Libraries\Permission;

class RequisitionModel {

    public function __construct() {
        $this->db = db_connect();
        $this->session = \Config\Services::session();
        helper(['form', 'url', 'array']);
        $this->permission = new Permission();
        $this->request = \Config\Services::request();
    }

    public function pos_customer_setup() {
        return $query = $this->db->table('customer_information')
                ->select('*')
                ->like('customer_name', 'Walking', 'after')
                ->get()
                ->getResultArray();
    }

    public function save_requsition($requsitionData) {
        $query = $this->db->table('requisition')->insert($requsitionData);
        $requsitionId = $this->db->insertID();
        return $requsitionId;
    }

    public function check_exists_requsition($req_no) {

        $query = $this->db->table('requisition')->where('requisition_no', $req_no)->get()->getRow();
        if (!empty($query)) {
            return true;
        }
        return false;
    }

    public function save_requsition_details($requsitionDetailsData) {
        $saveDetailsData = $this->db->table('requisition_item')->insertBatch($requsitionDetailsData);
        return $saveDetailsData;
    }

    public function update_requsition($requisition_id, $requsitionData) {
        $requisition = $this->db->table('requisition')->where('id', $requisition_id)->update($requsitionData);
        return $requisition;
    }

    public function not_approved_requsition($requisitionId) {
        $requisition = $this->db->table('requisition')->where('id', $requisitionId)->update(['status' => '3']);
        return $requisition;
    }

    public function update_requsition_details($requisition_id, $requsitionDetailsData) {

        $deletePreviousData = $this->db->table('requisition_item')->where('requisition_id', $requisition_id)->delete();
        if ($deletePreviousData) {
            $saveDetailsData = $this->db->table('requisition_item')->insertBatch($requsitionDetailsData);
            return $saveDetailsData;
        }
    }

    public function getRequisitionList($postData = null) {

        $userId = $this->session->get('id');
        $findRole = $this->db->table('sec_userrole')->where('user_id', $userId)->get()->getRow();

//        echo "<pre>";print_r($findRole->roleid);exit;

        $customerArr = $this->userList();

        $response = array();
        if ($this->request->getVar('fromdate')) {
            $fromdate = $this->request->getVar('fromdate') . " 00:00:00";
        }
        if ($this->request->getVar('todate')) {
            $todate = $this->request->getVar('todate') . " 23:59:59";
        }

        if ($this->request->getVar('status')) {
            $status = $this->request->getVar('status');
        }

        if (!empty($fromdate)) {
            $datbetween = "(a.created_at BETWEEN '$fromdate' AND '$todate')";
        } else {
            $datbetween = "";
        }
        $draw = $postData['draw'];
        $start = $postData['start'];
        $rowperpage = $postData['length']; // Rows display per page
        $columnIndex = $postData['order'][0]['column']; // Column index
        $columnName = $postData['columns'][$columnIndex]['data']; // 
        $columnSortOrder = $postData['order'][0]['dir']; // asc or desc
        $searchValue = $postData['search']['value']; // Search value
        $searchQuery = "";
        if ($searchValue != '') {
            $searchQuery = " (a.requisition_no like '%" . $searchValue . "%' or a.information like '%" . $searchValue . "%' or a.delivery_date like'%" . $searchValue . "%')";
        }
        ## Total number of records without filtering
        $builder1 = $this->db->table('requisition a');
        $builder1->select("count(*) as allcount");
//        $builder1->join('user b', 'b.id = a.requested_by', 'left');
        $builder1->join('customer_information b', 'b.customer_id = a.requisition_for', 'left');
        if ($searchValue != '') {
            $builder1->where($searchQuery);
        }
        if (!empty($fromdate) && !empty($todate)) {
            $builder1->where($datbetween);
        }

        if (!empty($findRole->roleid) && $findRole->roleid == '4') {
            $builder1->where('b.user_id', $findRole->user_id);
            $builder1->orWhere('a.requested_by', $findRole->user_id);
        }

        if (!empty($status)) {
            $builder1->where('a.status', $status);
        }

        $query1 = $builder1->get();
        $records = $query1->getRow();
        $totalRecords = $records->allcount;

        ## Total number of record with filtering
        $builder2 = $this->db->table('requisition a');
        $builder2->select("count(*) as allcount");
//        $builder2->join('user b', 'b.id = a.requested_by', 'left');
        $builder2->join('customer_information b', 'b.customer_id = a.requisition_for', 'left');
        if ($searchValue != '') {
            $builder2->where($searchQuery);
        }
        if (!empty($fromdate) && !empty($todate)) {
            $builder2->where($datbetween);
        }

        if (!empty($findRole->roleid) && $findRole->roleid == '4') {
            $builder2->where('b.user_id', $findRole->user_id);
            $builder2->orWhere('a.requested_by', $findRole->user_id);
        }

        if (!empty($status)) {
            $builder2->where('a.status', $status);
        }
        $query2 = $builder2->get();
        $records = $query2->getRow();
        $totalRecordwithFilter = $records->allcount;
        ## Fetch records
        $builder3 = $this->db->table('requisition a');
        $builder3->select("a.*,b.customer_id,b.user_id,b.customer_name");
//        $builder3->join('user b', 'b.id = a.requested_by', 'left');
        $builder3->join('customer_information b', 'b.customer_id = a.requisition_for', 'left');

        if ($searchValue != '') {
            $builder3->where($searchQuery);
        }
        if (!empty($fromdate) && !empty($todate)) {
            $builder3->where($datbetween);
        }

        if (!empty($findRole->roleid) && $findRole->roleid == '4') {
            $builder3->where('b.user_id', $findRole->user_id);
            $builder3->orWhere('a.requested_by', $findRole->user_id);
        }

        if (!empty($status)) {
            $builder3->where('a.status', $status);
        }

        $builder3->orderBy($columnName, $columnSortOrder);
        $builder3->limit($rowperpage, $start);
        $query3 = $builder3->get();
        $records = $query3->getResult();

        $data = array();
        $sl = 1;

        foreach ($records as $record) {

            $requisition_iteams = $this->db->table('requisition_item')
                    ->select("requisition_item.*")
                    ->where('requisition_id', $record->id)
                    ->limit(2)
                    ->orderBy('id', 'DESC')
                    ->get()
                    ->getResult();

            $requisition_products = $this->db->table('product_information')
                            ->select("product_id,product_name")
                            ->get()->getResult();

            $productArr = [];
            foreach ($requisition_products as $product) {
                $productArr[$product->product_id] = $product->product_name;
            }

            $requisition_iteamsArr = [];
            $i = 0;
            foreach ($requisition_iteams as $requisition_iteam) {
                $requisition_iteamsArr[$requisition_iteam->requisition_id][$i] = "Item Name: " . $productArr[$requisition_iteam->product_id] . " Qty: {$requisition_iteam->quantity}";

                $i++;
            }

            $button = '';
            $base_url = base_url();
            $jsaction = "return confirm('Are You Sure ?')";
            $jsactionApprove = "return confirm('Are You Sure?, you want to approved this!!')";

            $button .= ' <a href="' . $base_url . '/requisition/requisition_details/' . $record->id . '" class="btn btn-success-soft btn-sm" data-toggle="tooltip" data-placement="left" title="Details"><i class="fas fa-eye" aria-hidden="true"></i></a>';
//            $button .= ' <a href="' . $base_url . '/requisition/pos_print/' . $record->invoice_id . '" class="btn btn-warning-soft btn-sm" data-toggle="tooltip" data-placement="left" title="POS Print"><i class="fas fa-fax" aria-hidden="true"></i></a>';
//            if ($this->permission->method('requisition_list', 'update')->access()) {
            if ($record->status == '1' && $findRole->roleid != '4') {
                $button .= ' <a onclick="' . $jsactionApprove . '"   data-id="' . $record->id . '" class="btn btn-primary-soft btn-sm" id="notApproved" data-toggle="tooltip" data-placement="left" title="Not Approved"><i class="fa fa-ban" aria-hidden="true"></i></a>';
            }
            if ($this->permission->method('requisition_list', 'delete')->access()) {
                $button .= ' <a onclick="' . $jsaction . '" href="' . $base_url . '/requisition/delete_requisition/' . $record->id . '"  class="btn btn-danger-soft btn-sm" data-toggle="tooltip" data-placement="right" title="Delete "><i class="far fa-trash-alt" aria-hidden="true"></i></a>';
            }

            if ($record->status == '1') {
                $status = 'Pending';
            } elseif ($record->status == '2') {
                $status = 'Approved';
            } else {
                $status = 'Not Approved';
            }

            $checkedReqList = [];

            if (!empty($findRole->roleid) && $findRole->roleid == '4') {
                $check = '';
            } else {
                $check = '<input type="checkbox" class="requisition_id" data-requisition_id="' . $record->id . '" id="requisitionId' . $record->id . '"  name="requisitionIdArr[]" value="' . $record->id . '" ' . (($record->status == '2') ? 'checked' : '') . '>';
            }

//            $check = '<input type="checkbox" class="requisition_id" data-requisition_id="' . $record->id . '" id="requisitionId' . $record->id . '"  name="requisitionIdArr[]" value="' . $record->id . '" ' . (in_array($record->id, $checkedReqList) ? 'checked' : '') . '>';



            $data[] = array(
                'check' => $check,
                'sl_no' => $sl,
                'requisition_no' => $record->requisition_no,
                'details' => $record->information,
                'status' => $status,
                'created_by' => $customerArr[$record->requested_by] ?? '',
                'requisition_for' => $customerArr[$record->requisition_for] ?? '',
//                'product_details' => implode("<br>", $requisition_iteamsArr[$record->id]) . '<br>' . ' <a href="' . $base_url . '/requisition/requisition_details/' . $record->id . '" class="btn btn-success-soft btn-sm" data-toggle="tooltip" data-placement="left" title="View Details">View More...</a>',
                'product_details' => '<a href="' . $base_url . '/requisition/requisition_details/' . $record->id . '" class="btn btn-success-soft btn-sm" data-toggle="tooltip" data-placement="left" title="View Details">View More...</a>',
                'delivery_date' => date('d F Y', strtotime($record->delivery_date)),
                'created_at' => date('d F Y', strtotime($record->created_at)),
                'button' => $button,
            );
            $sl++;
        }

//        echo "<pre>";
//        print_r($data);
//        exit;
        ## Response
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecordwithFilter,
            "iTotalDisplayRecords" => $totalRecords,
            "aaData" => $data
        );

        return $response;
    }

    public function customerArr() {
        $customerInfo = $this->db->table('customer_information')
                ->select('customer_id,customer_name,user_id,user_id_num')
                ->groupBy('user_id')
                ->orderBy('customer_id', 'desc')
                ->get()
                ->getResult();

//        echo "<pre>";print_r($customerInfo);exit;

        $customerArr = ['' => '--Select Employee--'];
        if ($customerInfo) {
            foreach ($customerInfo as $cusId) {
                $ID = !empty($cusId->user_id_num) ? '(' . $cusId->user_id_num . ')' : '';
                $customerArr[$cusId->customer_id] = $cusId->customer_name . ' ' . $ID;
            }
        }

        return $customerArr;
    }

    public function singleRequisition($id) {

        $builder = $this->db->table('requisition')
                ->select('requisition.*')
                ->where('id', $id)
                ->get()
                ->getRow();

        return $builder;
    }

    public function singleRequisitionDetails($id) {
        $requisition_iteams = $this->db->table('requisition_item')
                ->select("requisition_item.*")
                ->where('requisition_id', $id)
                ->get()
                ->getResult();

        return $requisition_iteams;
    }

    public function productWiseRequisitionDetails($id) {
        $requisition_iteams = $this->db->table('requisition_item')
                ->select("requisition_item.*")
                ->where('requisition_id', $id)
                ->get()
                ->getResult();

        $requisition_iteamsArr = [];
        if (!empty($requisition_iteams)) {
            foreach ($requisition_iteams as $item) {
                $requisition_iteamsArr[$item->product_id]['product_id'] = $item->product_id;
                $requisition_iteamsArr[$item->product_id]['quantity'][] = $item->quantity;
            }
        }
        
        $finalData = [];
        if(!empty($requisition_iteamsArr)){
           foreach($requisition_iteamsArr as $proID => $final){
               $finalData[$proID] = $final['product_id'];
               $finalData[$proID] = array_sum($final['quantity']);
           } 
        }
        return $finalData;
    }

    public function productList() {
        $requisition_products = $this->db->table('product_information')
                        ->select("product_id,product_name")
                        ->get()->getResult();

        $productArr = [];
        foreach ($requisition_products as $product) {
            $productArr[$product->product_id] = $product->product_name;
        }

        return $productArr;
    }

    public function userList() {
        $users = $this->db->table('user')
                ->select("id,firstname,lastname")
                ->get()
                ->getResult();

        $usersArr = [];
        foreach ($users as $user) {
            $usersArr[$user->id] = $user->firstname . ' ' . $user->lastname;
        }

        return $usersArr;
    }

    public function get_all_requisition_data() {
        $builder = $this->db->table('requisition')
                ->select('*')
                ->get()
                ->getResultArray();

        return $builder;
    }

    public function manufacturer_rate($product_id) {
        $builder = $this->db->table('product_information')
                ->select('manufacturer_price')
                ->where('product_id', $product_id)
                ->get()
                ->getResultArray();
        return $builder;
    }

    public function delete_requisition($id) {
        $requisition = $this->db->table('requisition')->where('id', $id)->delete();
        $requisition_details = $this->db->table('requisition_item')->where('requisition_id', $id);
        if ($requisition) {
            $requisition_details->delete();
            return true;
        } else {
            return false;
        }
    }

    public function manufacturer_list() {
        $builder = $this->db->table('manufacturer_information');
        $builder->select('*');
        $query = $builder->get();
        $data = $query->getResult();

        $list = array('' => 'Select Manufacturer');
        if (!empty($data)) {
            foreach ($data as $value) {
                $list[$value->manufacturer_id] = $value->manufacturer_name;
            }
        }
        return $list;
    }

    public function get_total_product($product_id, $manufacturer_id) {

        $total_purchase = $this->db->table('product_purchase_details')
                ->select("SUM(quantity) as total_purchase")
                ->where('product_id', $product_id)
                ->get()
                ->getRow();

        $total_sale = $this->db->table('invoice_details')
                ->select("SUM(quantity) as total_sale")
                ->where('product_id', $product_id)
                ->get()
                ->getRow();

        $product_information = $this->db->table('product_information')
                ->select("*")
                ->where('product_id', $product_id)
                ->where('manufacturer_id', $manufacturer_id)
                ->get()
                ->getRow();
        $available_quantity = ($total_purchase->total_purchase - $total_sale->total_sale);
        $data2 = array(
            'total_product' => (!empty($available_quantity) ? $available_quantity : 0) / $product_information->box_size,
            'manufacturer_price' => $product_information->m_b_price,
            'price' => $product_information->price,
            'manufacturer_id' => $product_information->manufacturer_id,
            'unit' => $product_information->unit,
            'box_qty' => $product_information->box_size,
        );

        return $data2;
    }

    public function search_customers($customer_name) {
        $customer_information = $this->db->table('customer_information')
                ->select("*")
                ->like('customer_name', $customer_name, 'both')
                ->orderBy('customer_name', 'asc')
                ->limit(15)
                ->get()
                ->getResultArray();
        return $customer_information;
    }

    public function product_search_item($manufacturer_id, $product_name) {

        $product_information = $this->db->table('product_information')
                ->select("*")
                ->where('manufacturer_id', $manufacturer_id)
                ->like('product_name', $product_name, 'both')
                ->orderBy('product_name', 'asc')
                ->limit(15)
                ->get()
                ->getResultArray();
        return $product_information;
    }

    public function bank_list() {
        $builder = $this->db->table('bank_information');
        $builder->select('*');
        $builder->where('status', 1);
        $query = $builder->get();
        $data = $query->getResult();

        $list = array('' => 'Select Bank');
        if (!empty($data)) {
            foreach ($data as $value) {
                $list[$value->bank_id] = $value->bank_name;
            }
        }
        return $list;
    }

    public function medicine_list() {
        $builder = $this->db->table('product_information');
        $builder->select('*');
        $builder->where('status', 1);
        $query = $builder->get();
        return $data = $query->getResult();
    }

    public function category_list() {
        $builder = $this->db->table('product_category');
        $builder->select('*');
        $builder->where('status', 1);
        $query = $builder->get();
        return $data = $query->getResultArray();
    }

    public function invoice_details($invoice_id) {
        return $details_info = $this->db->table('invoice_details a')
                ->select('a.*,c.product_name,c.strength')
                ->join('product_information c', 'a.product_id=c.product_id', 'left')
                ->where('a.invoice_id', $invoice_id)
                ->get()
                ->getResultArray();
    }

    public function pos_invoice_setup($product_id) {

        $product_information = $this->db->table('product_information a')
                ->select('a.*,c.*')
                ->join('product_purchase_details c', 'a.product_id=c.product_id', 'left')
                ->where('a.product_id', $product_id)
                ->get()
                ->getRow();

        if ($product_information != null) {

            $total_purchase = $this->db->table('product_purchase_details a')
                    ->select('SUM(a.quantity) as total_purchase')
                    ->where('a.product_id', $product_id)
                    ->get()
                    ->getRow();

            $total_sale = $this->db->table('invoice_details b')
                    ->select('SUM(b.quantity) as total_sale')
                    ->where('b.product_id', $product_id)
                    ->get()
                    ->getRow();

            $available_quantity = ($total_purchase->total_purchase - $total_sale->total_sale);

            $medicineinfo = (object) array(
                        'total_product' => $available_quantity,
                        'manufacturer_price' => $product_information->manufacturer_price,
                        'price' => $product_information->price,
                        'batch_id' => $product_information->batch_id,
                        'strength' => $product_information->strength,
                        'expeire_date' => $product_information->expeire_date,
                        'manufacturer_id' => $product_information->manufacturer_id,
                        'product_id' => $product_information->product_id,
                        'discount' => $product_information->product_id,
                        'product_name' => $product_information->product_name,
                        'unit' => $product_information->unit,
                        'image' => $product_information->image
            );

            return $medicineinfo;
        } else {
            return false;
        }
    }

    public function batch_search_item($product_id) {

        $batchdata = $this->db->table('product_purchase_details a')
                ->select("a.*, m.product_name, m.strength")
                ->join('product_information m', 'm.product_id = a.product_id', 'left')
                ->where('a.product_id', $product_id)
                ->groupBy('a.batch_id')
                ->limit(15)
                ->get()
                ->getResultArray();
        return $batchdata;
    }

    public function tax_fields() {
        return $tax_data = $this->db->table('tax_settings')
                ->select('*')
                ->get()
                ->getResultArray();
    }

    public function purchase_batch_data($product_id, $batch_id) {
        return $batch_stocks = $this->db->table('product_purchase_details a')
                ->select('a.expeire_date,SUM(a.quantity) as total_purchase')
                ->where('a.batch_id', $batch_id)
                ->where('a.product_id', $product_id)
                ->get()
                ->getRow();
    }

    public function invoice_batch_data($product_id, $batch_id) {
        return $batch_stocks = $this->db->table('invoice_details b')
                ->select('SUM(b.quantity) as total_sale')
                ->where('b.batch_id', $batch_id)
                ->where('b.product_id', $product_id)
                ->get()
                ->getRow();
    }

    public function getitemlist($cid) {
        $builder = $this->db->table('product_information');
        $builder->select('*');
        $builder->where('status', 1);
        if ($cid != 'all') {
            $builder->where('category_id', $cid);
        }
        $query = $builder->get();
        $data = $query->getResult();

        if ($data) {
            return $data;
        } else {
            return false;
        }
    }

    public function searchprod_byname($pname = null) {
        $itemlist = $this->db->table('product_information')
                ->select('*')
                ->like('product_name', $pname, 'both')
                ->where('status', 1)
                ->orderBy('product_name', 'asc')
                ->limit(30)
                ->get()
                ->getResult();
        return $itemlist;
    }

    public function todays_saleList() {
        $date = date('Y-m-d');
        return $details_info = $this->db->table('invoice a')
                ->select('a.*,c.customer_name,d.firstname,d.lastname')
                ->join('customer_information c', 'a.customer_id=c.customer_id', 'left')
                ->join('user d', 'a.sales_by=d.id')
                ->where('a.date', $date)
                ->get()
                ->getResultArray();
    }

    public function selected_batch_edit($product_id) {
        $builder = $this->db->table('product_purchase_details');
        $builder->select('*');
        $builder->where('product_id', $product_id);
        $builder->groupBy('batch_id');
        $query = $builder->get();
        $data = $query->getResult();

        $batchList = array('' => 'Select Batch');
        if (!empty($data)) {
            foreach ($data as $value) {
                $batchList[$value->batch_id] = $value->batch_id;
            }
        }

        return $batchList;
    }

    public function batch_expiry($product_id, $batch_id) {
        $batch = $this->db->table('product_purchase_details')
                ->select('expeire_date')
                ->where('batch_id', $batch_id)
                ->get()
                ->getRow();

        $total_purchase_batch = $this->db->table('product_purchase_details a')
                ->select('a.expeire_date,SUM(a.quantity) as total_purchase')
                ->where('a.batch_id', $batch_id)
                ->where('a.product_id', $product_id)
                ->get()
                ->getRow();

        $total_sale_batch = $this->db->table('invoice_details b')
                ->select('SUM(b.quantity) as total_sale')
                ->where('b.batch_id', $batch_id)
                ->where('b.product_id', $product_id)
                ->get()
                ->getRow();

        $salesreturn = $this->db->table('product_return')
                ->select("SUM(ret_qty) as total_sales_return")
                ->where('product_id', $product_id)
                ->where('batch_id', $batch_id)
                ->where('usablity', 1)
                ->get()
                ->getRow();

        $purchasereturn = $this->db->table('product_return')
                ->select("SUM(ret_qty) as total_purchase_return")
                ->where('product_id', $product_id)
                ->where('batch_id', $batch_id)
                ->where('usablity', 2)
                ->get()
                ->getRow();

        $batch_stock = (($total_purchase_batch->total_purchase ? $total_purchase_batch->total_purchase : 0) - ($total_sale_batch->total_sale ? $total_sale_batch->total_sale : 0) + ($salesreturn->total_sales_return ? $salesreturn->total_sales_return : 0) - ($purchasereturn->total_purchase_return ? $purchasereturn->total_purchase_return : 0));

        $data['total_stock'] = $batch_stock;
        $data['expeire_date'] = $batch->expeire_date;

        return $data;
    }

    public function invoice_taxinfo($invoice_id) {
        $collected_tax = $this->db->table('tax_collection')
                ->select('*')
                ->where('relation_id', $invoice_id)
                ->get()
                ->getResultArray();
        return $collected_tax;
    }

    public function setting_data() {

        $setting = $this->db->table('setting')
                ->get()
                ->getRow();
        return $setting;
    }

    public function save_invoice($data = [], $requisition_details) {
        $setting_data = $this->setting_data();

//        echo "<pre>";print_r($requisition_details);exit;

        $productIdArr = [];
        $productQtyArr = [];
        $batchIdArr = [];
        if ($requisition_details) {
            foreach ($requisition_details as $details) {
                $productIdArr[] = $details->product_id;
                $productQtyArr[] = $details->quantity;
                $batchIdArr[] = $details->batch_id;
            }
        }

//           echo "<pre>";print_r($productIdArr);
        //   echo "<br>";
        //   echo "<pre>";print_r($productQtyArr);exit;

        date_default_timezone_set($setting_data->timezone);
        $builder = $this->db->table('invoice');
        $add_invoice = $builder->insert($data);

//        echo "<pre>";print_r($add_invoice);
        $receive_by = $this->session->get('id');
        $createdate = date('Y-m-d H:i:s');
        ;
        $invoice_id = $data['invoice_id'];
        $gtotal = $data['total_amount'];
        $customer_id = $data['customer_id'];
        $p_amount = $data['paid_amount'];
        $date = date('Y-m-d');
        $quantity = $productQtyArr;
        $product_id = $productIdArr;
        if ($p_amount > $gtotal) {
            $paid_amount = $gtotal;
        } else {
            $paid_amount = $data['paid_amount'];
        }

        $payment_type = $data['payment_type'];
        $cutomerinfo = $this->db->table('customer_information')
                ->select("*")
                ->where('customer_id', $data['customer_id'])
                ->get()
                ->getRow();

        $customer_coa = $this->db->table('acc_coa')
                ->select("*")
                ->where('customer_id', $data['customer_id'])
                ->get()
                ->getRow();
        $customer = $cutomerinfo->customer_name;

        $bank_id = $data['bank_id'];
        if (!empty($bank_id)) {
            $bankinfo = $this->db->table('bank_information')
                    ->select("*")
                    ->where('bank_id', $bank_id)
                    ->get()
                    ->getRow();
            $bank_coa_i = $this->db->table('acc_coa')
                    ->select("*")
                    ->where('bank_id', $bank_id)
                    ->get()
                    ->getRow();

            $bankcoaid = $bank_coa_i->HeadCode;
        } else {
            $bankcoaid = '';
        }

        $customerdebit = array(
            'VNo' => $invoice_id,
            'Vtype' => 'Invoice',
            'VDate' => $data['date'],
            'COAID' => $customer_coa->HeadCode,
            'Narration' => 'Customer debit for  .' . $customer,
            'Debit' => $data['total_amount'] - (!empty($data['prevous_due']) ? $data['prevous_due'] : 0),
            'Credit' => 0,
            'IsPosted' => 1,
            'CreateBy' => $receive_by,
            'CreateDate' => $data['date'],
            'IsAppove' => 1
        );

        $customer_credit = array(
            'VNo' => $invoice_id,
            'Vtype' => 'Invoice',
            'VDate' => $data['date'],
            'COAID' => $customer_coa->HeadCode,
            'Narration' => 'Customer .' . $customer,
            'Debit' => 0,
            'Credit' => $paid_amount,
            'IsPosted' => 1,
            'CreateBy' => $receive_by,
            'CreateDate' => $data['date'],
            'IsAppove' => 1
        );

        $prinfo = $this->db->table('product_purchase_details')
                ->select("product_id,Avg(rate) as product_rate")
                ->whereIn('product_id', $product_id)
                ->groupBy('product_id')
                ->get()
                ->getResult();
        $purchase_ave = [];
        $i = 0;
        foreach ($prinfo as $avg) {
            $purchase_ave [] = $avg->product_rate * $quantity[$i];
            $i++;
        }
        $sumval = array_sum($purchase_ave);

        ///Inventory Credit
        $inventory = array(
            'VNo' => $invoice_id,
            'Vtype' => 'Invoice',
            'VDate' => $data['date'],
            'COAID' => 10107,
            'Narration' => 'Inventory Credit For Sale to ' . $customer,
            'Debit' => 0,
            'Credit' => $sumval,
            'IsPosted' => 1,
            'CreateBy' => $receive_by,
            'CreateDate' => $data['date'],
            'IsAppove' => 1
        );

        $cashinhand = array(
            'VNo' => $invoice_id,
            'Vtype' => 'Invoice',
            'VDate' => $data['date'],
            'COAID' => 1020101,
            'Narration' => 'Cash in Hand For Sale to ' . $customer,
            'Debit' => $paid_amount,
            'Credit' => 0,
            'IsPosted' => 1,
            'CreateBy' => $receive_by,
            'CreateDate' => $data['date'],
            'IsAppove' => 1
        );

        $pro_sale_income = array(
            'VNo' => $invoice_id,
            'Vtype' => 'INVOICE',
            'VDate' => $data['date'],
            'COAID' => 304,
            'Narration' => 'Customer debit For Invoice No' . $invoice_id,
            'Debit' => 0,
            'Credit' => $data['total_amount'] - (!empty($data['prevous_due']) ? $data['prevous_due'] : 0),
            'IsPosted' => 1,
            'CreateBy' => $receive_by,
            'CreateDate' => $createdate,
            'IsAppove' => 1
        );

        // bank ledger debit
        $bankd = array(
            'VNo' => $invoice_id,
            'Vtype' => 'Invoice',
            'VDate' => $data['date'],
            'COAID' => $bankcoaid,
            'Narration' => 'Paid amount for Sale to  ' . $customer,
            'Debit' => $paid_amount,
            'Credit' => 0,
            'IsPosted' => 1,
            'CreateBy' => $receive_by,
            'CreateDate' => $createdate,
            'IsAppove' => 1
        );

        if ($add_invoice) {
            $acc_coatbl = $this->db->table('acc_transaction');
            $acc_coatbl->insert($inventory);
            $acc_coatbl->insert($customerdebit);
            $acc_coatbl->insert($pro_sale_income);
            if ($paid_amount > 0) {
                $acc_coatbl->insert($customer_credit);
                if ($payment_type == 1) {
                    $acc_coatbl->insert($cashinhand);
                }
                if ($payment_type == 2) {
                    $acc_coatbl->insert($bankd);
                }
                $tablecolumn = $this->db->getFieldData('tax_collection');
                $num_column = count($tablecolumn) - 4;
                $tax_v = 0;
                for ($j = 0; $j < $num_column; $j++) {
                    $taxfield = 'tax' . $j;
                    $taxvalue = 'total_tax' . $j;
                    $taxdata[$taxfield] = $this->request->getVar($taxvalue);
                }
                $taxdata['customer_id'] = $customer_id;
                $taxdata['date'] = (!empty($data['date']) ? $data['date'] : date('Y-m-d'));
                $taxdata['relation_id'] = $invoice_id;
                $tax_table = $this->db->table('tax_collection');
                $tax_table->insert($taxdata);
            }

            $customerinfo = $this->db->table('customer_information')
                    ->select('*')
                    ->where('customer_id', $customer_id)
                    ->get()
                    ->getRow();

            foreach ($requisition_details as $details) {

                $manufacturer_rate = $this->manufacturer_rate($details->product_id);

                $details = array(
                    'invoice_id' => $invoice_id,
                    'product_id' => $details->product_id,
                    'batch_id' => $details->batch_id ?? 0,
                    'quantity' => ($details->quantity ? $details->quantity : 0),
                    'rate' => $this->productPrice($details->product_id) ?? 0,
                    'discount' => $discount ?? 0,
                    'manufacturer_rate' => ($manufacturer_rate[0]['manufacturer_price'] ? $manufacturer_rate[0]['manufacturer_price'] : 0),
                    'total_price' => $this->productPrice($details->product_id) * $details->quantity,
                    'status' => 1
                );

                $invoice_details = $this->db->table('invoice_details')->insert($details);
            }
            return $invoice_id;
        } else {
            return false;
        }
    }

    public function productPrice($id) {
        $productPrice = $this->db->table('product_information')->where('product_id', $id)->get()->getRow();
        return $productPrice->price ?? 0;
    }

    public function company_details() {
        return $details_info = $this->db->table('setting')
                ->select('*')
                ->get()
                ->getRow();
    }

    public function invoice_main($invoice_id) {
        return $details_info = $this->db->table('invoice a')
                ->select('a.*,c.customer_name,d.firstname,d.lastname')
                ->join('customer_information c', 'a.customer_id=c.customer_id', 'left')
                ->join('user d', 'a.sales_by=d.id')
                ->where('a.invoice_id', $invoice_id)
                ->get()
                ->getRow();
    }

    public function singledata($id) {
        $builder = $this->db->table('invoice a')
                ->select('a.*,b.*')
                ->join('customer_information b', 'b.customer_id = a.customer_id')
                ->where('a.invoice_id', $id)
                ->get()
                ->getRow();
        return $builder;
    }

    public function detailsdata($id) {
        $builder = $this->db->table('invoice_details a')
                ->select('a.*,b.*,c.unit_name')
                ->join('product_information b', 'b.product_id = a.product_id')
                ->join('unit c', 'c.id = b.unit')
                ->where('a.invoice_id', $id)
                ->where('a.quantity >', 0)
                ->get()
                ->getResultArray();
        return $builder;
    }

    public function designationList() {
        $designations = $this->db->table('designation')->select("*")->get()->getResult();
        return $designations;
    }

    public function departmentList() {
        $departments = $this->db->table('department')->select("*")->get()->getResult();
        return $departments;
    }

    public function designationName($id) {
        $designations = $this->db->table('designation')->select("*")->where('id', $id)->get()->getRow();
        return $designations;
    }

    public function departmentName($id) {
        $departments = $this->db->table('department')->select("*")->where('id', $id)->get()->getRow();
        return $departments;
    }

    public function customer_data($id) {
        $customer = $this->db->table('customer_information')->select('*')->where('customer_id', $id)->get()->getRow();
        return $customer;
    }

    public function customer_details_data($id) {
        $customer = $this->db->table('customer_information')->select('*')->where('user_id', $id)->get()->getRow();
        return $customer;
    }

    public function previous($customer_id) {
        $result = $this->db->table('customer_information a')
                ->select('a.*,b.HeadCode,((select ifnull(sum(Debit),0) from acc_transaction where COAID= `b`.`HeadCode` AND IsAppove = 1)-(select ifnull(sum(Credit),0) from acc_transaction where COAID= `b`.`HeadCode` AND IsAppove = 1)) as balance')
                ->join('acc_coa b', 'a.customer_id = b.customer_id', 'left')
                ->where('a.customer_id', $customer_id)
                ->get()
                ->getResultArray();
        $balance = $result[0]['balance'];
        $b = (!empty($balance) ? $balance : 0);
//        echo "<pre>";print_r();exit;
        return $b;
    }

    public function pre_requisition($customer_id, $date_time) {
        $pre_requisition = $this->db->table('requisition')->select('*')->where('requisition_for', $customer_id)->where('created_at <', $date_time)->orderBy('created_at', 'DESC')->get()->getRow();

        if (!empty($pre_requisition)) {
            $preSingleReq = $this->singleRequisition($pre_requisition->id);

            $preSingleReqDetails = $this->singleRequisitionDetails($pre_requisition->id);

            $preData = [
                'preSingleReq' => $preSingleReq,
                'preSingleReqDetails' => $preSingleReqDetails,
                'preReqDate' => date('d-F-Y', strtotime($pre_requisition->created_at))
            ];

            return $preData;
        } else {
            return false;
        }
    }

    public function autocompletproductdata($product_name = null) {
        return $query = $this->db->table('product_information')
                ->select('*')
                ->like('product_name', $product_name, 'both')
                ->orLike('product_id', $product_name)
                ->where('status', 1)
                ->orderBy('product_name', 'asc')
                ->limit(15)
                ->get()
                ->getResultArray();
    }

    public function get_total_product_invoic($product_id) {
//        echo "<pre>";
//        print_r($product_id);
//        exit;
        $total_purchase = $this->db->table('product_purchase_details a')
                ->select('a.expeire_date,SUM(a.quantity) as total_purchase')
                ->where('a.product_id', $product_id)
                ->where('a.expeire_date >=', date('Y-m-d'))
                ->get()
                ->getRow();

        $total_sale = $this->db->table('invoice_details b')
                ->select('SUM(b.quantity) as total_sale')
                ->where('b.product_id', $product_id)
                ->get()
                ->getRow();

        $salesreturn = $this->db->table('product_return')
                ->select("SUM(ret_qty) as total_sales_return")
                ->where('product_id', $product_id)
                ->where('usablity', 1)
                ->get()
                ->getRow();

        $purchasereturn = $this->db->table('product_return')
                ->select("SUM(ret_qty) as total_purchase_return")
                ->where('product_id', $product_id)
                ->where('usablity', 2)
                ->get()
                ->getRow();

        $product_information = $this->db->table('product_information a')
                ->select('a.*,b.unit_name')
                ->join('unit b', 'b.id = a.unit')
                ->where('a.product_id', $product_id)
                ->where('a.status', 1)
                ->get()
                ->getRow();

        $available_quantity = ((!empty($total_purchase->total_purchase) ? $total_purchase->total_purchase : 0) - (!empty($total_sale->total_sale) ? $total_sale->total_sale : 0) + ($salesreturn->total_sales_return ? $salesreturn->total_sales_return : 0) - ($purchasereturn->total_purchase_return ? $purchasereturn->total_purchase_return : 0));

//        echo "<pre>";print_r($available_quantity);exit;
        $content = $this->batch_search_item($product_id);
        $html = "";
        if (empty($content)) {
            $html .= "No Product Found !";
        } else {


            // Select option created for product
            $html .= "<select name=\"batch_id[]\"   class=\"batch_id_1 form-control select2\" id=\"batch_id_1\">";
            $html .= "<option>" . lan('select_batch') . "</option>";
            foreach ($content as $product) {
                $total_purchase_batch = $this->db->table('product_purchase_details a')
                        ->select('a.expeire_date,SUM(a.quantity) as total_purchase')
                        ->where('a.batch_id', $product['batch_id'])
                        ->where('a.product_id', $product_id)
                        ->get()
                        ->getRow();

                $total_sale_batch = $this->db->table('invoice_details b')
                        ->select('SUM(b.quantity) as total_sale')
                        ->where('b.batch_id', $product['batch_id'])
                        ->where('b.product_id', $product_id)
                        ->get()
                        ->getRow();

                $salesreturn = $this->db->table('product_return')
                        ->select("SUM(ret_qty) as total_sales_return")
                        ->where('product_id', $product['product_id'])
                        ->where('batch_id', $product['batch_id'])
                        ->where('usablity', 1)
                        ->get()
                        ->getRow();

                $purchasereturn = $this->db->table('product_return')
                        ->select("SUM(ret_qty) as total_purchase_return")
                        ->where('product_id', $product['product_id'])
                        ->where('batch_id', $product['batch_id'])
                        ->where('usablity', 2)
                        ->get()
                        ->getRow();

                $batch_stock = (($total_purchase_batch->total_purchase ? $total_purchase_batch->total_purchase : 0) - ($total_sale_batch->total_sale ? $total_sale_batch->total_sale : 0) + ($salesreturn->total_sales_return ? $salesreturn->total_sales_return : 0) - ($purchasereturn->total_purchase_return ? $purchasereturn->total_purchase_return : 0));
                if ($batch_stock > 0) {
                    $html .= "<option value=" . $product['batch_id'] . ">" . $product['batch_id'] . "</option>";
                }
            }
            $html .= "</select>";
        }
        $tablecolumn = $this->db->getFieldData('tax_collection');
        $num_column = count($tablecolumn) - 4;
        if ($num_column > 0) {
            $taxfield = '';
            $taxvar = [];
            for ($i = 0; $i < $num_column; $i++) {
                $taxfield = 'tax' . $i;
                $data2[$taxfield] = $product_information->$taxfield;
                $taxvar[$i] = $product_information->$taxfield;
                $data2['taxdta'] = $taxvar;
            }
        }

        $stripLists = $this->db->table('medicine_leaf_setting')->get()->getResult();
        $stripArr = [];
        if (!empty($stripLists)) {
            foreach ($stripLists as $sizeList) {
//               $stripArr[$sizeList->total_number] = explode('*',$sizeList->leaf_type)[1];
                $stripArr[$sizeList->total_number] = $sizeList->leaf_type;
            }
        }

//        echo "<pre>";print_r($stripArr);exit;


        $data2['total_product'] = $available_quantity;
        $data2['manufacturer_price'] = $product_information->manufacturer_price;
        $data2['price'] = $product_information->price;
        $data2['manufacturer_id'] = $product_information->manufacturer_id;
        $data2['unit'] = $product_information->unit_name;
        $data2['box_qty'] = $product_information->box_size;
        $data2['strip'] = $stripArr[$product_information->box_size];
        $data2['batch'] = $html;
        $data2['txnmber'] = $num_column;

        return $data2;
    }

    public function quantity_check($productIdArr, $requisitionQty) {

        $productAvailableQuantityArr = [];
        $productRequisitionQty = [];
        $productValidationArr = [];
        if (!empty($productIdArr)) {
            foreach ($productIdArr as $key => $product_id) {
                $productRequisitionQty[$product_id] = $requisitionQty[$key];

                $pendingReqQty = $this->db->table('requisition')
                        ->join('requisition_item', 'requisition_item.requisition_id = requisition.id')
                        ->select('requisition.id,requisition.status,requisition_item.product_id,SUM(requisition_item.quantity) as pending_qty')
                        ->where('requisition_item.product_id', $product_id)
                        ->where('requisition.status', 1)
                        ->get()
                        ->getRow();

//                echo "<pre>";print_r($pendingReqQty);exit;

                $total_purchase = $this->db->table('product_purchase_details a')
                        ->select('a.expeire_date,SUM(a.quantity) as total_purchase')
                        ->where('a.product_id', $product_id)
                        ->where('a.expeire_date >=', date('Y-m-d'))
                        ->get()
                        ->getRow();

                $total_sale = $this->db->table('invoice_details b')
                        ->select('SUM(b.quantity) as total_sale')
                        ->where('b.product_id', $product_id)
                        ->get()
                        ->getRow();

                $salesreturn = $this->db->table('product_return')
                        ->select("SUM(ret_qty) as total_sales_return")
                        ->where('product_id', $product_id)
                        ->where('usablity', 1)
                        ->get()
                        ->getRow();

                $purchasereturn = $this->db->table('product_return')
                        ->select("SUM(ret_qty) as total_purchase_return")
                        ->where('product_id', $product_id)
                        ->where('usablity', 2)
                        ->get()
                        ->getRow();

                $product_information = $this->db->table('product_information a')
                        ->select('a.*,b.unit_name')
                        ->join('unit b', 'b.id = a.unit')
                        ->where('a.product_id', $product_id)
                        ->where('a.status', 1)
                        ->get()
                        ->getRow();

                $available_quantity = ((!empty($total_purchase->total_purchase) ? $total_purchase->total_purchase : 0) - (!empty($total_sale->total_sale) ? $total_sale->total_sale : 0) + ($salesreturn->total_sales_return ? $salesreturn->total_sales_return : 0) - ($purchasereturn->total_purchase_return ? $purchasereturn->total_purchase_return : 0));

                $pendingQty = !empty($pendingReqQty->pending_qty) ? $pendingReqQty->pending_qty : 0;
                $productAvailableQuantityArr[$product_id] = $available_quantity - $pendingQty;

                if ($productRequisitionQty[$product_id] > $productAvailableQuantityArr[$product_id]) {
                    $productValidationArr[$product_id] = "Item Id-{$product_id} Pending Qty-{$pendingQty} Available Qty-{$productAvailableQuantityArr[$product_id]}";
                }
            }
        }

//        echo "<pre>";print_r($productValidationArr);exit;

        return $productValidationArr;
    }

    public function get_total_product_batch($batch_id, $product_id) {

        $total_purchase = $this->db->table('product_purchase_details a')
                ->select('a.expeire_date,SUM(a.quantity) as total_purchase')
                ->where('a.batch_id', $batch_id)
                ->where('a.product_id', $product_id)
                ->get()
                ->getRow();

        $total_sale = $this->db->table('invoice_details b')
                ->select('SUM(b.quantity) as total_sale')
                ->where('b.batch_id', $batch_id)
                ->where('b.product_id', $product_id)
                ->get()
                ->getRow();

        $available_quantity = (($total_purchase->total_purchase ? $total_purchase->total_purchase : 0) - ($total_sale->total_sale ? $total_sale->total_sale : 0));

        $data['total_product'] = $available_quantity;
        $data['expire_date'] = $total_purchase->expeire_date;

        return $data;
    }

}
