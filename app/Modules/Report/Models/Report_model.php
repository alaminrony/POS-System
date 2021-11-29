<?php

namespace App\Modules\Report\Models;

class Report_model {

    public function __construct() {
        $this->db = db_connect();
        $this->session = \Config\Services::session();
        helper(['form', 'url']);
        $this->request = \Config\Services::request();
    }

    public function accounts_closing_data() {
        $last_closing_amount = $this->get_last_closing_amount();
        $cash_in = $this->cash_data_receipt();
        $cash_out = $this->cash_data();
        if ($last_closing_amount != null) {
            $last_closing_amount = $last_closing_amount[0]['amount'];
            $cash_in_hand = ($last_closing_amount + $cash_in) - $cash_out;
        } else {
            $last_closing_amount = 0;
            $cash_in_hand = $cash_in - $cash_out;
        }


        return array(
            "last_day_closing" => number_format($last_closing_amount, 2, '.', ','),
            "cash_in" => number_format($cash_in, 2, '.', ','),
            "cash_out" => number_format($cash_out, 2, '.', ','),
            "cash_in_hand" => number_format($cash_in_hand, 2, '.', ',')
        );
    }

    public function get_last_closing_amount() {
        $sql = "SELECT amount FROM daily_closing WHERE date = (SELECT MAX(date) FROM daily_closing)";
        $query = $this->db->query($sql);
        $result = $query->getResultArray();
        if ($result) {
            return $result;
        } else {
            return FALSE;
        }
    }

    public function cash_data_receipt() {
//-----------
        $sql = $this->db->table('daily_closing')
                ->select('MAX(date) as date')
                ->get()
                ->getRow();

        $from_date = ($sql->date ? $sql->date : '');

        $cash = 0;
        $datse = date('Y-m-d');
        $result_amount = $this->db->table('acc_transaction')
                ->select('sum(Debit) as amount')
                ->where('COAID', 1020101)
                ->where('VDate >=', $from_date)
                ->where('VDate <=', $datse)
                ->get()
                ->getResultArray();
        $amount = $result_amount;
        $cash = 0;
        foreach ($amount as $amounts) {
            $cash += $amounts['amount'];
        }
        return $cash;
    }

    public function cash_data() {
//-----------
        $sql = $this->db->table('daily_closing')
                ->select('MAX(date) as date')
                ->get()
                ->getRow();
        $from_date = ($sql->date ? $sql->date : '');
        $cash = 0;
        $datse = date('Y-m-d');
        $result_amount = $this->db->table('acc_transaction')
                ->select('sum(Credit) as amount')
                ->where('COAID', 1020101)
                ->where('VDate >=', $from_date)
                ->where('VDate <=', $datse)
                ->get()
                ->getResultArray();

        $amount = $result_amount;
        $cash = 0;
        foreach ($amount as $amounts) {
            $cash += $amounts['amount'];
        }
        return $cash;
    }

    public function daily_closing_entry($data) {

        $closing = $this->db->table('daily_closing');
        return $closing->insert($data);
    }

    public function check_exist_closing() {
        $date = date('Y-m-d');
        $exitstdata = $this->db->table('daily_closing')
                ->where('date', $date)
                ->countAllResults();

        if ($exitstdata > 0) {
            return 1;
        } else {
            return 0;
        }
    }

    public function getclosingList($postData = null) {
        $response = array();
        $fromdate = $this->request->getVar('fromdate');
        $todate = $this->request->getVar('todate');
        if (!empty($fromdate)) {
            $datbetween = "(a.date BETWEEN '$fromdate' AND '$todate')";
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
            $searchQuery = " (c.firstname like '%" . $searchValue . "%' or c.lastname like'%" . $searchValue . "%' or a.date like'%" . $searchValue . "%') ";
        }
## Total number of records without filtering
        $builder1 = $this->db->table('daily_closing a');
        $builder1->select("count(*) as allcount");
        $builder1->join('user c', 'c.id = a.closed_by', 'left');
        if ($searchValue != '') {
            $builder1->where($searchQuery);
        }
        if (!empty($fromdate) && !empty($todate)) {
            $builder1->where($datbetween);
        }
        $query1 = $builder1->countAllResults();
        $totalRecords = $query1;

## Total number of record with filtering
        $builder2 = $this->db->table('daily_closing a');
        $builder2->select("count(*) as allcount");
        $builder2->join('user c', 'c.id = a.closed_by', 'left');
        if ($searchValue != '') {
            $builder2->where($searchQuery);
        }

        if (!empty($fromdate) && !empty($todate)) {
            $builder2->where($datbetween);
        }
        $query2 = $builder2->countAllResults();
        $totalRecordwithFilter = $query2;
## Fetch records
        $builder3 = $this->db->table('daily_closing a');
        $builder3->select("a.*,c.firstname,c.lastname");
        $builder3->join('user c', 'c.id = a.closed_by', 'left');
        if ($searchValue != '') {
            $builder3->where($searchQuery);
        }
        if (!empty($fromdate) && !empty($todate)) {
            $builder3->where($datbetween);
        }
        $builder3->orderBy($columnName, $columnSortOrder);
        $builder3->limit($rowperpage, $start);
        $query3 = $builder3->get();
        $records = $query3->getResult();
        $data = array();
        $sl = 1;

        foreach ($records as $record) {

            $data[] = array(
                'sl' => $sl,
                'close_by' => $record->firstname . ' ' . $record->lastname,
                'last_day_closing' => $record->last_day_closing,
                'date' => $record->date,
                'cash_in' => $record->cash_in,
                'cash_out' => $record->cash_out,
                'amount' => $record->amount,
            );
            $sl++;
        }

## Response
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecordwithFilter,
            "iTotalDisplayRecords" => $totalRecords,
            "aaData" => $data
        );

        return $response;
    }

    public function getsalesreportList($postData = null) {
        $response = array();
        $fromdate = ($this->request->getVar('fromdate') ? $this->request->getVar('fromdate') : date('Y-m-d'));
        $todate = ($this->request->getVar('todate') ? $this->request->getVar('todate') : date('Y-m-d'));

        if (!empty($fromdate)) {
            $datbetween = "(a.date BETWEEN '$fromdate' AND '$todate')";
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
            $searchQuery = " (b.customer_name like '%" . $searchValue . "%' or a.invoice like '%" . $searchValue . "%' or a.date like'%" . $searchValue . "%')";
        }
## Total number of records without filtering
        $builder1 = $this->db->table('invoice a');
        $builder1->select("count(*) as allcount");
        $builder1->join('customer_information b', 'b.customer_id = a.customer_id', 'left');
        if ($searchValue != '') {
            $builder1->where($searchQuery);
        }
        if (!empty($fromdate) && !empty($todate)) {
            $builder1->where($datbetween);
        }


        $query1 = $builder1->get();
        $records = $query1->getRow();
        $totalRecords = $records->allcount;

## Total number of record with filtering
        $builder2 = $this->db->table('invoice a');
        $builder2->select("count(*) as allcount");
        $builder2->join('customer_information b', 'b.customer_id = a.customer_id', 'left');
        if ($searchValue != '') {
            $builder2->where($searchQuery);
        }
        if (!empty($fromdate) && !empty($todate)) {
            $builder2->where($datbetween);
        }

        $query2 = $builder2->get();
        $records = $query2->getRow();
        $totalRecordwithFilter = $records->allcount;
## Fetch records
        $builder3 = $this->db->table('invoice a');
        $builder3->select("a.*,b.customer_name,c.firstname,c.lastname");
        $builder3->join('customer_information b', 'b.customer_id = a.customer_id', 'left');
        $builder3->join('user c', 'c.id = a.sales_by');
        if ($searchValue != '') {
            $builder3->where($searchQuery);
        }
        if (!empty($fromdate) && !empty($todate)) {
            $builder3->where($datbetween);
        }

        $builder3->orderBy($columnName, $columnSortOrder);
        $builder3->limit($rowperpage, $start);
        $query3 = $builder3->get();
        $records = $query3->getResult();
        $data = array();
        $sl = 1;

        foreach ($records as $record) {

            $data[] = array(
                'sl' => $sl,
                'invoice_no' => $record->invoice,
                'invoice_id' => $record->invoice_id,
                'customer_name' => $record->customer_name,
                'date' => $record->date,
//                'total_amount' => $record->total_amount,
                'total_amount' => $this->totalSUMByInvoiceId($record->invoice_id),
                'sales_by' => $record->firstname . ' ' . $record->lastname,
            );
            $sl++;
        }

## Response
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecordwithFilter,
            "iTotalDisplayRecords" => $totalRecords,
            "aaData" => $data
        );

        return $response;
    }

    public function user_list() {
        $builder = $this->db->table('customer_information');
        $builder->select('*');
        $query = $builder->get();
        $data = $query->getResult();

        $list = array('' => 'Select Customer');
        if (!empty($data)) {
            foreach ($data as $value) {
                $list[$value->customer_id] = $value->customer_name . ' (' . $value->user_id_num . ')';
            }
        }
        return $list;
    }

    public function dep_list() {
        $builder = $this->db->table('department');
        $builder->select('*');
        $query = $builder->get();
        $data = $query->getResult();

        $list = array('' => '--Select Department--');
        if (!empty($data)) {
            foreach ($data as $value) {
                $list[$value->id] = $value->department_name;
            }
        }
        return $list;
    }

    public function getuserwise_salesreportList($postData = null) {

        $response = array();
        $fromdate = ($this->request->getVar('fromdate') ? $this->request->getVar('fromdate') : '');
        $todate = ($this->request->getVar('todate') ? $this->request->getVar('todate') : '');
        $user_id = $this->request->getVar('user_id');
        if (!empty($fromdate)) {
            $datbetween = "(a.date BETWEEN '$fromdate' AND '$todate')";
        } else {
            $datbetween = "";
        }
        if (!empty($user_id)) {
            $user_id = $user_id;
        } else {
            $user_id = "";
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
            $searchQuery = " (b.customer_name like '%" . $searchValue . "%' or a.invoice like '%" . $searchValue . "%' or a.date like'%" . $searchValue . "%')";
        }
## Total number of records without filtering
        $builder1 = $this->db->table('invoice a');
        $builder1->select("count(*) as allcount");
        $builder1->join('customer_information b', 'b.customer_id = a.customer_id', 'left');
        if ($searchValue != '') {
            $builder1->where($searchQuery);
        }
        if (!empty($fromdate) && !empty($todate)) {
            $builder1->where($datbetween);
        }
        if ($user_id != '') {
            $builder1->where('a.customer_id', $user_id);
        }

        $query1 = $builder1->get();
        $records = $query1->getRow();
        $totalRecords = $records->allcount;

## Total number of record with filtering
        $builder2 = $this->db->table('invoice a');
        $builder2->select("count(*) as allcount");
        $builder2->join('customer_information b', 'b.customer_id = a.customer_id', 'left');
        if ($searchValue != '') {
            $builder2->where($searchQuery);
        }
        if (!empty($fromdate) && !empty($todate)) {
            $builder2->where($datbetween);
        }
        if ($user_id != '') {
            $builder2->where('a.customer_id', $user_id);
        }
        $query2 = $builder2->get();
        $records = $query2->getRow();
        $totalRecordwithFilter = $records->allcount;

## Fetch records
        $builder3 = $this->db->table('invoice a');
        $builder3->select("a.*,b.customer_name,b.user_id_num,c.firstname,c.lastname");
        $builder3->join('customer_information b', 'b.customer_id = a.customer_id', 'left');
        $builder3->join('user c', 'c.id = a.sales_by');
        if ($searchValue != '') {
            $builder3->where($searchQuery);
        }
        if (!empty($fromdate) && !empty($todate)) {
            $builder3->where($datbetween);
        }
        if ($user_id != '') {
            $builder3->where('a.customer_id', $user_id);
        }
        $builder3->orderBy($columnName, $columnSortOrder);
//        $builder3->limit($rowperpage, $start);
        if ($rowperpage == -1) {
            $builder3->limit(0, $start);
        } else {
            $builder3->limit($rowperpage, $start);
        }
        $query3 = $builder3->get();
        $records = $query3->getResult();

        $data = array();
        $sl = 1;

        foreach ($records as $record) {
            $data[] = array(
                'sl' => $sl,
                'invoice_no' => $record->invoice,
                'invoice_id' => $record->invoice_id,
                'customer_name' => $record->customer_name . ' (' . $record->user_id_num . ')',
                'customer_id' => $record->user_id_num,
                'date' => $record->date,
                'quantity' => $this->totalQuantityByInvoiceId($record->invoice_id),
                'total_amount' => $this->totalSUMByInvoiceId($record->invoice_id),
//                'total_amount' => $record->total_amount,
                'sales_by' => $record->firstname . ' ' . $record->lastname,
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

    public function employee_wise_cumulative_report($postData = null) {

        $response = array();
        $fromdate = ($this->request->getVar('fromdate') ? $this->request->getVar('fromdate') : '');
        $todate = ($this->request->getVar('todate') ? $this->request->getVar('todate') : '');
        $user_id = $this->request->getVar('user_id');
        if (!empty($fromdate)) {
            $datbetween = "(a.date BETWEEN '$fromdate' AND '$todate')";
        } else {
            $datbetween = "";
        }
        if (!empty($user_id)) {
            $user_id = $user_id;
        } else {
            $user_id = "";
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
            $searchQuery = " (b.customer_name like '%" . $searchValue . "%' or b.customer_name like '%" . $searchValue . "%' or b.user_id_num like '%" . $searchValue . "%' or a.date like'%" . $searchValue . "%')";
        }
## Total number of records without filtering
        $builder1 = $this->db->table('invoice a');
        $builder1->select("count(*) as allcount");
        $builder1->join('customer_information b', 'b.customer_id = a.customer_id', 'left');
        if ($searchValue != '') {
            $builder1->where($searchQuery);
        }
        if (!empty($fromdate) && !empty($todate)) {
            $builder1->where($datbetween);
        }
        if ($user_id != '') {
            $builder1->where('a.customer_id', $user_id);
        }

        $query1 = $builder1->get();
        $records = $query1->getRow();
        $totalRecords = $records->allcount;

## Total number of record with filtering
        $builder2 = $this->db->table('invoice a');
        $builder2->select("count(*) as allcount");
        $builder2->join('customer_information b', 'b.customer_id = a.customer_id', 'left');
        if ($searchValue != '') {
            $builder2->where($searchQuery);
        }
        if (!empty($fromdate) && !empty($todate)) {
            $builder2->where($datbetween);
        }
        if ($user_id != '') {
            $builder2->where('a.customer_id', $user_id);
        }
        $query2 = $builder2->get();
        $records = $query2->getRow();
        $totalRecordwithFilter = $records->allcount;

## Fetch records
        $builder3 = $this->db->table('invoice a');
        $builder3->select("a.*,b.customer_name,b.user_id_num,c.firstname,c.lastname");
        $builder3->join('customer_information b', 'b.customer_id = a.customer_id', 'left');
        $builder3->join('user c', 'c.id = a.sales_by');

        if ($searchValue != '') {
            $builder3->where($searchQuery);
        }
        if (!empty($fromdate) && !empty($todate)) {
            $builder3->where($datbetween);
        }


        if ($user_id != '') {
            $builder3->where('b.customer_id', $user_id);
        }

        $builder3->orderBy($columnName, $columnSortOrder);

//        $builder3->limit($rowperpage, $start);
//        if ($rowperpage == -1) {
//            $builder3->limit(0, $start);
//        } else {
//            $builder3->limit($rowperpage, $start);
//        }

        $query3 = $builder3->get();
        $records = $query3->getResult();

        $data = array();
        $sl = 1;

        foreach ($records as $record) {
            
            $data[] = array(
                'sl' => $sl,
                'invoice_no' => $record->invoice,
                'invoice_id' => $record->invoice_id,
                'customer_name' => $record->customer_name . ' (' . $record->user_id_num . ')',
                'customer_id' => $record->user_id_num,
                'date' => $record->date,
                'quantity' => $this->totalQuantityByInvoiceId($record->invoice_id),
                'total_amount' => $this->totalSUMByInvoiceId($record->invoice_id),
//                'total_amount' => $record->total_amount,
                'sales_by' => $record->firstname . ' ' . $record->lastname,
            );
            $sl++;
        }



        $quantityAndAmountArr = [];
        if (!empty($data)) {
            foreach ($data as $target) {
                $quantityAndAmountArr[$target['customer_id']]['quantity'][] = $target['quantity'];
                $quantityAndAmountArr[$target['customer_id']]['total_amount'][] = $target['total_amount'];
            }
        }


        $quantitySumArr = [];
        $amountSumArr = [];
        if (!empty($quantityAndAmountArr)) {
            foreach ($quantityAndAmountArr as $empID => $quantityAndAmount) {
                $quantitySumArr[$empID] = array_sum($quantityAndAmount['quantity']);
                $amountSumArr[$empID] = array_sum($quantityAndAmount['total_amount']);
            }
        }

        $employeeLists = $this->db->table('customer_information')
                ->select('customer_id,customer_name,user_id_num')
                ->get()
                ->getResult();

        if (!empty($employeeLists)) {
            $empArr = [];
            foreach ($employeeLists as $empData) {
                $empArr[$empData->user_id_num] = $empData->customer_name . ' (' . $empData->user_id_num . ')';
            }
        }



        $targetArr = [];
        if (!empty($quantitySumArr)) {
            $i = 0;
            $sl = 1;
            foreach ($quantitySumArr as $empID => $qtyValue) {
                $targetArr[$i]['sl'] = $sl;
                $targetArr[$i]['customer_name'] = $empArr[$empID];
                $targetArr[$i]['customer_id'] = $empID;
                $targetArr[$i]['quantity'] = $quantitySumArr[$empID];
                $targetArr[$i]['total_amount'] = $amountSumArr[$empID];
                $i++;
                $sl++;
            }
        }
        
//        echo "<pre>";print_r($targetArr);exit;

        if ($rowperpage == -1) {
            $pagination = array_slice($targetArr, $start, count($targetArr));
        } else {
            $pagination = array_slice($targetArr, $start, $rowperpage);
        }

        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => count($targetArr),
            "iTotalDisplayRecords" => count($targetArr),
            "aaData" => $pagination
        );
        return $response;
    }

    public function totalQuantityByInvoiceId($invoice_id) {
        $totalQty = $this->db->table('invoice_details')
                ->select('SUM(quantity) as total_quantity')
                ->where('invoice_id', $invoice_id)
                ->get()
                ->getRow();

        if ($totalQty->total_quantity) {
            return $totalQty->total_quantity;
        }
        return 0;
    }
    
    public function totalSUMByInvoiceId($invoice_id) {
        $totalPrice = $this->db->table('invoice_details')
                ->select('SUM(total_price) as total_price')
                ->where('invoice_id', $invoice_id)
                ->get()
                ->getRow();

        if ($totalPrice->total_price) {
            return $totalPrice->total_price;
        }
        return 0;
    }

    public function get_dept_wise_salesreportList($postData = null) {
        $draw = $postData['draw'];
        $start = $postData['start'];
        $rowperpage = $postData['length']; // Rows display per page
        $columnIndex = $postData['order'][0]['column']; // Column index
        $columnName = $postData['columns'][$columnIndex]['data']; // 
        $columnSortOrder = $postData['order'][0]['dir']; // asc or desc
        $searchValue = $postData['search']['value']; // Search value


        $response = array();
        $fromdate = ($this->request->getVar('fromdate') ? $this->request->getVar('fromdate') . ' 00:00:00' : '');
        $todate = ($this->request->getVar('todate') ? $this->request->getVar('todate') : '') . ' 23:59:59';
        $dept_id = $this->request->getVar('department_id') ?? '';

//        echo "<pre>";print_r($dept_id);exit;


        $thisDeptUsers = $this->db->table('customer_information')
                ->select('customer_id,department')
                ->where('department', $dept_id)
                ->get()
                ->getResult();

        $deptName = $this->db->table('department')
                ->where('id', $dept_id)
                ->get()
                ->getRow();

        if (!empty($thisDeptUsers)) {
            $depUserArr = [];
            foreach ($thisDeptUsers as $depUser) {
                if ($depUser->department != 0) {
                    $depUserArr[] = $depUser->customer_id;
                }
            }
        }

        $employeeLists = $this->db->table('customer_information')
                ->select('customer_id,customer_name,user_id_num')
                ->where('department', $dept_id)
                ->get()
                ->getResult();

        if (!empty($employeeLists)) {
            $empArr = [];
            foreach ($employeeLists as $empData) {
                $empArr[$empData->customer_id] = $empData->customer_name . ' (' . $empData->user_id_num . ')';
            }
        }

//        echo "<pre>";print_r($rowperpage);exit;
        $datas = [];
        if ($depUserArr) {
            $datbetween = "(invoice.date BETWEEN '$fromdate' AND '$todate')";

            $datas = $this->db->table('invoice')
                    ->select('invoice.id,invoice.invoice_id,invoice.customer_id,invoice.total_amount,invoice.date,invoice_details.product_id,invoice_details.quantity,invoice_details.total_price')
                    ->join('invoice_details', 'invoice_details.invoice_id  = invoice.invoice_id')
                    ->whereIn('invoice.customer_id', $depUserArr);
            if (!empty($fromdate) && !empty($todate)) {
                $datas = $datas->where($datbetween);
            }

            $datas = $datas->get()->getResult();
        }

        if (!empty($datas)) {
            $total_amount = 0;
            $quantity = 0;
            foreach ($datas as $data) {
                $total_amount = $total_amount + $data->total_price;
                $quantity = $quantity + $data->quantity;
            }
        }

        if (!empty($datas)) {
            $employeeTotal = 0;
            $employeeQuantity = 0;
            $employeeWiseData = [];
            $i = 0;
            foreach ($datas as $data) {
                $employeeWiseData[$data->customer_id]['total_price'][$i] = $employeeTotal + $data->total_price;
                $employeeWiseData[$data->customer_id]['quantity'][$i] = $employeeQuantity + $data->quantity;
                $i++;
            }
        }

        $finalEmployeeArr = [];
        if (!empty($employeeWiseData)) {
            foreach ($employeeWiseData as $empId => $empData) {
                $finalEmployeeArr[$empId]['name'] = $empArr[$empId] ?? '';
                $finalEmployeeArr[$empId]['total_price'] = array_sum($empData['total_price']);
                $finalEmployeeArr[$empId]['quantity'] = array_sum($empData['quantity']);
            }
        }

        $totalByDepArr = [];
        if (!empty($total_amount) && !empty($quantity)) {
            $totalByDepArr[0] = [
                'name' => $deptName->department_name,
                'total_price' => $total_amount,
                'quantity' => $quantity,
            ];
        }


        $targetArr = $totalByDepArr + $finalEmployeeArr;


        $totalRecords = count($targetArr);
        $totalRecordwithFilter = count($targetArr);



        $data = array();
        $sl = 1;
        foreach ($targetArr as $record) {
            $data[] = array(
                'sl' => $sl,
                'name' => $record['name'],
                'quantity' => $record['quantity'],
                'total_price' => number_format($record['total_price'], 2),
            );
            $sl++;
        }

        if ($rowperpage == -1) {
            $pagination = array_slice($data, $start, count($targetArr));
        } else {
            $pagination = array_slice($data, $start, $rowperpage);
        }

        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecordwithFilter,
            "iTotalDisplayRecords" => $totalRecords,
            "aaData" => $pagination
        );

        return $response;
    }

    public function department_wise_sales_report_print() {
        $response = array();
        $fromdate = ($this->request->getVar('FilterfromDate') ? $this->request->getVar('FilterfromDate') . ' 00:00:00' : '');
        $todate = ($this->request->getVar('FiltertoDate') ? $this->request->getVar('FiltertoDate') : '') . ' 23:59:59';
        $dept_id = $this->request->getVar('FilterDepId') ?? '';

        $thisDeptUsers = $this->db->table('customer_information')
                ->select('customer_id,department')
                ->where('department', $dept_id)
                ->get()
                ->getResult();

        $deptName = $this->db->table('department')
                ->where('id', $dept_id)
                ->get()
                ->getRow();

        if (!empty($thisDeptUsers)) {
            $depUserArr = [];
            foreach ($thisDeptUsers as $depUser) {
                if ($depUser->department != 0) {
                    $depUserArr[] = $depUser->customer_id;
                }
            }
        }

        $employeeLists = $this->db->table('customer_information')
                ->select('customer_id,customer_name,user_id_num')
                ->where('department', $dept_id)
                ->get()
                ->getResult();

        if (!empty($employeeLists)) {
            $empArr = [];
            foreach ($employeeLists as $empData) {
                $empArr[$empData->customer_id] = $empData->customer_name . ' (' . $empData->user_id_num . ')';
            }
        }


        $datas = [];
        if ($depUserArr) {
            $datbetween = "(invoice.created_at BETWEEN '$fromdate' AND '$todate')";

            $datas = $this->db->table('invoice')
                    ->select('invoice.id,invoice.invoice_id,invoice.customer_id,invoice.total_amount,invoice.created_at,invoice_details.product_id,invoice_details.quantity,invoice_details.total_price')
                    ->join('invoice_details', 'invoice_details.invoice_id  = invoice.invoice_id')
                    ->whereIn('invoice.customer_id', $depUserArr);
            if (!empty($fromdate) && !empty($todate)) {
                $datas = $datas->where($datbetween);
            }

            $datas = $datas->get()->getResult();
        }





        if (!empty($datas)) {
            $total_amount = 0;
            $quantity = 0;
            foreach ($datas as $data) {
                $total_amount = $total_amount + $data->total_price;
                $quantity = $quantity + $data->quantity;
            }
        }

        if (!empty($datas)) {
            $employeeTotal = 0;
            $employeeQuantity = 0;
            $employeeWiseData = [];
            $i = 0;
            foreach ($datas as $data) {
                $employeeWiseData[$data->customer_id]['total_price'][$i] = $employeeTotal + $data->total_price;
                $employeeWiseData[$data->customer_id]['quantity'][$i] = $employeeQuantity + $data->quantity;
                $i++;
            }
        }

        $finalEmployeeArr = [];
        if (!empty($employeeWiseData)) {
            foreach ($employeeWiseData as $empId => $empData) {
                $finalEmployeeArr[$empId]['name'] = $empArr[$empId] ?? '';
                $finalEmployeeArr[$empId]['total_price'] = array_sum($empData['total_price']);
                $finalEmployeeArr[$empId]['quantity'] = array_sum($empData['quantity']);
            }
        }



        $totalByDepArr = [];
        if (!empty($total_amount) && !empty($quantity)) {
            $totalByDepArr[0] = [
                'name' => $deptName->department_name,
                'total_price' => $total_amount,
                'quantity' => $quantity,
            ];
        }


        $targetArr = $totalByDepArr + $finalEmployeeArr;

        $finalData = [];
        $sl = 1;
        if ($targetArr) {
            foreach ($targetArr as $record) {
                $finalData[] = array(
                    'sl' => $sl,
                    'name' => $record['name'],
                    'quantity' => $record['quantity'],
                    'total_price' => number_format($record['total_price'], 2),
                );
                $sl++;
            }
        }

        return $finalData;
    }

    public function departmentName($dept_id) {
        $deptName = $this->db->table('department')
                ->where('id', $dept_id)
                ->get()
                ->getRow();
        return $deptName;
    }

    public function getCustomerwise_salesreportList($postData = null) {
//        echo "<pre>";
//        print_r($postData);
//        exit;
        $response = array();
        $fromdate = ($this->request->getVar('FilterfromDate') ? $this->request->getVar('FilterfromDate') : '');
        $todate = ($this->request->getVar('FiltertoDate') ? $this->request->getVar('FiltertoDate') : '');
        $user_id = ($this->request->getVar('FilteruserId') ? $this->request->getVar('FilteruserId') : '');
        if (!empty($fromdate)) {
            $datbetween = "(a.date BETWEEN '$fromdate' AND '$todate')";
        } else {
            $datbetween = "";
        }
        if (!empty($user_id)) {
            $user_id = $user_id;
        } else {
            $user_id = "";
        }

## Total number of records without filtering
        $builder1 = $this->db->table('invoice a');
        $builder1->select("count(*) as allcount");
        $builder1->join('customer_information b', 'b.customer_id = a.customer_id', 'left');

        if (!empty($fromdate) && !empty($todate)) {
            $builder1->where($datbetween);
        }
        if ($user_id != '') {
            $builder1->where('b.customer_id', $user_id);
        }

        $query1 = $builder1->get();
        $records = $query1->getRow();
        $totalRecords = $records->allcount;

## Total number of record with filtering
        $builder2 = $this->db->table('invoice a');
        $builder2->select("count(*) as allcount");
        $builder2->join('customer_information b', 'b.customer_id = a.customer_id', 'left');

        if (!empty($fromdate) && !empty($todate)) {
            $builder2->where($datbetween);
        }
        if ($user_id != '') {
            $builder2->where('b.customer_id', $user_id);
        }
        $query2 = $builder2->get();
        $records = $query2->getRow();
        $totalRecordwithFilter = $records->allcount;
## Fetch records
        $builder3 = $this->db->table('invoice a');
        $builder3->select("a.*,b.customer_name,b.user_id_num,c.firstname,c.lastname");
        $builder3->join('customer_information b', 'b.customer_id = a.customer_id', 'left');
        $builder3->join('user c', 'c.id = a.sales_by');

        if (!empty($fromdate) && !empty($todate)) {
            $builder3->where($datbetween);
        }
        if ($user_id != '') {
            $builder3->where('b.customer_id', $user_id);
        }
//        $builder3->orderBy($columnName, $columnSortOrder);
//        $builder3->limit($rowperpage, $start);
        $query3 = $builder3->get();
        $records = $query3->getResult();

//        echo "<pre>";print_r($records);exit;
        $data = array();
        $sl = 1;

        foreach ($records as $record) {

            $data[] = array(
                'sl' => $sl,
                'invoice_no' => $record->invoice,
                'invoice_id' => $record->invoice_id,
                'invoice_discount' => $record->invoice_discount,
                'total_tax' => $record->total_tax,
                'customer_name' => $record->customer_name . ' (' . $record->user_id_num . ')',
                'customer_id' => $record->user_id_num,
                'date' => $record->date,
                'quantity' => $this->totalQuantityByInvoiceId($record->invoice_id),
                'total_amount' => $this->totalSUMByInvoiceId($record->invoice_id),
//                'total_amount' => $record->total_amount,
                'sales_by' => $record->firstname . ' ' . $record->lastname,
            );
            $sl++;
        }

//        echo "<pre>";print_r($data);exit;

        return $data;
    }

    public function medicine_list() {
        $builder = $this->db->table('product_information');
        $builder->select('*');
        $query = $builder->get();
        $data = $query->getResult();

        $list = array('' => 'Select Medicine');
        if (!empty($data)) {
            foreach ($data as $value) {
                $list[$value->product_id] = $value->product_name . '(' . $value->strength . ')';
            }
        }
        return $list;
    }

    public function getproductwise_salesreportList($postData = null) {
        $response = array();
        $fromdate = ($this->request->getVar('fromdate') ? $this->request->getVar('fromdate') : date('Y-m-d'));
        $todate = ($this->request->getVar('todate') ? $this->request->getVar('todate') : date('Y-m-d'));
        $product_id = $this->request->getVar('product_id');
        if (!empty($fromdate)) {
            $datbetween = "(c.date BETWEEN '$fromdate' AND '$todate')";
        } else {
            $datbetween = "";
        }
        if (!empty($product_id)) {
            $product_id = $product_id;
        } else {
            $product_id = "";
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
            $searchQuery = " (d.customer_name like '%" . $searchValue . "%' or c.invoice like '%" . $searchValue . "%' or c.date like'%" . $searchValue . "%')";
        }
## Total number of records without filtering
        $builder1 = $this->db->table('invoice_details a');
        $builder1->select("count(*) as allcount");
        $builder1->join('product_information b', 'b.product_id = a.product_id');
        $builder1->join('invoice c', 'c.invoice_id = a.invoice_id');
        $builder1->join('customer_information d', 'd.customer_id = c.customer_id');
        if ($searchValue != '') {
            $builder1->where($searchQuery);
        }
        if (!empty($fromdate) && !empty($todate)) {
            $builder1->where($datbetween);
        }
        if ($product_id != '') {
            $builder1->where('a.product_id', $product_id);
        }
        $builder1->where('a.quantity >', 0);

        $query1 = $builder1->get();
        $records = $query1->getRow();
        $totalRecords = $records->allcount;

## Total number of record with filtering
        $builder2 = $this->db->table('invoice_details a');
        $builder2->select("count(*) as allcount");
        $builder2->join('product_information b', 'b.product_id = a.product_id');
        $builder2->join('invoice c', 'c.invoice_id = a.invoice_id');
        $builder2->join('customer_information d', 'd.customer_id = c.customer_id');
        if ($searchValue != '') {
            $builder2->where($searchQuery);
        }
        if (!empty($fromdate) && !empty($todate)) {
            $builder2->where($datbetween);
        }
        if ($product_id != '') {
            $builder2->where('a.product_id', $product_id);
        }
        $builder2->where('a.quantity >', 0);
        $query2 = $builder2->get();
        $records = $query2->getRow();
        $totalRecordwithFilter = $records->allcount;
## Fetch records
        $builder3 = $this->db->table('invoice_details a');
        $builder3->select("a.*,b.product_name,b.strength,b.product_id,c.date,c.invoice,c.total_amount,d.customer_name,d.user_id_num");
        $builder3->join('product_information b', 'b.product_id = a.product_id');
        $builder3->join('invoice c', 'c.invoice_id = a.invoice_id');
        $builder3->join('customer_information d', 'd.customer_id = c.customer_id');
        if ($searchValue != '') {
            $builder3->where($searchQuery);
        }
        if (!empty($fromdate) && !empty($todate)) {
            $builder3->where($datbetween);
        }
        if ($product_id != '') {
            $builder3->where('a.product_id', $product_id);
        }
        $builder3->where('a.quantity >', 0);
        $builder3->orderBy($columnName, $columnSortOrder);
        if ($rowperpage == -1) {
            $builder3->limit(0, $start);
        } else {
            $builder3->limit($rowperpage, $start);
        }
//        $builder3->limit($rowperpage, $start);


        $query3 = $builder3->get();
        $records = $query3->getResult();
        $data = array();
        $sl = 1;

        $setting_data = $this->setting_info();
        foreach ($records as $record) {
            $dis_amount = 0;
            if ($setting_data->discount_type == 1) {
                $dis_amount = ($record->quantity * $record->rate * $record->discount) / 100;
            }
            if ($setting_data->discount_type == 2) {
                $dis_amount = ($record->quantity * $record->discount);
            }

            if ($setting_data->discount_type == 3) {
                $dis_amount = ($record->discount);
            }
            $total = $record->quantity * $record->rate;
            $data[] = array(
                'sl' => $sl,
                'date' => $record->date,
                'product_name' => $record->product_name . '(' . $record->strength . ')',
                'product_id' => $record->product_id,
                'invoice' => $record->invoice,
                'customer_name' => $record->customer_name,
                'customer_id' => $record->user_id_num,
                'quantity' => $record->quantity,
                'discount' => $dis_amount,
                'rate' => $record->rate,
                'total' => number_format(($total ? $total : 0) - ($dis_amount ? $dis_amount : 0), 2),
            );
            $sl++;
        }


//        echo "<pre>";print_r($data);exit;
## Response
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecordwithFilter,
            "iTotalDisplayRecords" => $totalRecords,
            "aaData" => $data
        );

        return $response;
    }

    public function getproductwise_cumulative_report($postData = null) {

        $response = array();
        $fromdate = ($this->request->getVar('fromdate') ? $this->request->getVar('fromdate') : date('Y-m-d'));
        $todate = ($this->request->getVar('todate') ? $this->request->getVar('todate') : date('Y-m-d'));
        $product_id = $this->request->getVar('product_id');
        if (!empty($fromdate)) {
            $datbetween = "(c.date BETWEEN '$fromdate' AND '$todate')";
        } else {
            $datbetween = "";
        }
        if (!empty($product_id)) {
            $product_id = $product_id;
        } else {
            $product_id = "";
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
            $searchQuery = " (b.product_id like '%" . $searchValue . "%' or b.product_name like '%" . $searchValue . "%' or c.date like'%" . $searchValue . "%')";
        }
## Total number of records without filtering
        $builder1 = $this->db->table('invoice_details a');
        $builder1->select("count(*) as allcount");
        $builder1->join('product_information b', 'b.product_id = a.product_id');
        $builder1->join('invoice c', 'c.invoice_id = a.invoice_id');
        $builder1->join('customer_information d', 'd.customer_id = c.customer_id');
        if ($searchValue != '') {
            $builder1->where($searchQuery);
        }
        if (!empty($fromdate) && !empty($todate)) {
            $builder1->where($datbetween);
        }
        if ($product_id != '') {
            $builder1->where('a.product_id', $product_id);
        }
        $builder1->where('a.quantity >', 0);

        $query1 = $builder1->get();
        $records = $query1->getRow();
        $totalRecords = $records->allcount;

## Total number of record with filtering
        $builder2 = $this->db->table('invoice_details a');
        $builder2->select("count(*) as allcount");
        $builder2->join('product_information b', 'b.product_id = a.product_id');
        $builder2->join('invoice c', 'c.invoice_id = a.invoice_id');
        $builder2->join('customer_information d', 'd.customer_id = c.customer_id');
        if ($searchValue != '') {
            $builder2->where($searchQuery);
        }
        if (!empty($fromdate) && !empty($todate)) {
            $builder2->where($datbetween);
        }
        if ($product_id != '') {
            $builder2->where('a.product_id', $product_id);
        }
        $builder2->where('a.quantity >', 0);
        $query2 = $builder2->get();
        $records = $query2->getRow();
        $totalRecordwithFilter = $records->allcount;
## Fetch records
        $builder3 = $this->db->table('invoice_details a');
        $builder3->select("a.*,b.product_name,b.strength,b.product_id,c.date,c.invoice,c.total_amount,d.customer_name,d.user_id_num");
        $builder3->join('product_information b', 'b.product_id = a.product_id');
        $builder3->join('invoice c', 'c.invoice_id = a.invoice_id');
        $builder3->join('customer_information d', 'd.customer_id = c.customer_id');
        if ($searchValue != '') {
            $builder3->where($searchQuery);
        }
        if (!empty($fromdate) && !empty($todate)) {
            $builder3->where($datbetween);
        }
        if ($product_id != '') {
            $builder3->where('a.product_id', $product_id);
        }
        $builder3->where('a.quantity >', 0);
//        $builder3->orderBy("a.{$columnName}", $columnSortOrder);
//        if ($rowperpage == -1) {
//            $builder3->limit(0, $start);
//        } else {
//            $builder3->limit($rowperpage, $start);
//        }
//        $builder3->limit($rowperpage, $start);
        $query3 = $builder3->get();
        $records = $query3->getResult();
        $data = array();
        $sl = 1;

        $setting_data = $this->setting_info();
        foreach ($records as $record) {
            
            $dis_amount = 0;
            if ($setting_data->discount_type == 1) {
                $dis_amount = ($record->quantity * $record->rate * $record->discount) / 100;
            }
            if ($setting_data->discount_type == 2) {
                $dis_amount = ($record->quantity * $record->discount);
            }

            if ($setting_data->discount_type == 3) {
                $dis_amount = ($record->discount);
            }
            $total = $record->quantity * $record->rate;
            $data[] = array(
                'sl' => $sl,
                'date' => $record->date,
                'product_name' => $record->product_name . '(' . $record->strength . ')',
                'product_id' => $record->product_id,
                'invoice' => $record->invoice,
                'customer_name' => $record->customer_name,
                'customer_id' => $record->user_id_num,
                'quantity' => $record->quantity,
                'discount' => $dis_amount,
                'rate' => $record->rate,
                'total' => ($total ? $total : 0) - ($dis_amount ? $dis_amount : 0),
            );
            $sl++;
        }

        $quantityAndAmountArr = [];
        if (!empty($data)) {
            foreach ($data as $target) {
                $quantityAndAmountArr[$target['product_id']]['quantity'][] = $target['quantity'];
                $quantityAndAmountArr[$target['product_id']]['total_amount'][] = $target['total'];
            }
        }


        $quantitySumArr = [];
        $amountSumArr = [];
        if (!empty($quantityAndAmountArr)) {
            foreach ($quantityAndAmountArr as $itemID => $quantityAndAmount) {
                
                $quantitySumArr[$itemID] = array_sum($quantityAndAmount['quantity']);
                $amountSumArr[$itemID] = array_sum($quantityAndAmount['total_amount']);
            }
        }
        


        $medicineLists = $this->db->table('product_information')
                ->select('id,product_id,product_name')
                ->get()
                ->getResult();

        if (!empty($medicineLists)) {
            $medicineArr = [];
            foreach ($medicineLists as $mdnData) {
                $medicineArr[$mdnData->product_id] = $mdnData->product_name . ' (' . $mdnData->product_id . ')';
            }
        }

        $targetArr = [];
        if (!empty($quantitySumArr)) {
            $i = 0;
            $sl = 1;
            foreach ($quantitySumArr as $medicineID => $qtyValue) {
                $targetArr[$i]['sl'] = $sl;
                $targetArr[$i]['medicine_name'] = $medicineArr[$medicineID];
                $targetArr[$i]['medicine_id'] = $medicineID;
                $targetArr[$i]['quantity'] = $quantitySumArr[$medicineID];
                $targetArr[$i]['total_amount'] = number_format($amountSumArr[$medicineID],2);
                $i++;
                $sl++;
            }
        }
        

        if ($rowperpage == -1) {
            $pagination = array_slice($targetArr, $start, count($targetArr));
        } else {
            $pagination = array_slice($targetArr, $start, $rowperpage);
        }
        
        


        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => count($targetArr),
            "iTotalDisplayRecords" => count($targetArr),
            "aaData" => $pagination
        );
        
//        echo "<pre>";
//        print_r($response);
//        exit;

        return $response;
    }

    public function category_list() {
        $builder = $this->db->table('product_category');
        $builder->select('*');
        $query = $builder->get();
        $data = $query->getResult();

        $list = array('' => 'Select Category');
        if (!empty($data)) {
            foreach ($data as $value) {
                $list[$value->category_id] = $value->category_name;
            }
        }
        return $list;
    }

    public function getcategorywise_salesreportList($postData = null) {
        $response = array();
        $fromdate = ($this->request->getVar('fromdate') ? $this->request->getVar('fromdate') : date('Y-m-d'));
        $todate = ($this->request->getVar('todate') ? $this->request->getVar('todate') : date('Y-m-d'));
        $category_id = $this->request->getVar('category_id');
        if (!empty($fromdate)) {
            $datbetween = "(c.date BETWEEN '$fromdate' AND '$todate')";
        } else {
            $datbetween = "";
        }
        if (!empty($category_id)) {
            $category_id = $category_id;
        } else {
            $category_id = "";
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
            $searchQuery = " (d.customer_name like '%" . $searchValue . "%' or c.invoice like '%" . $searchValue . "%' or c.date like'%" . $searchValue . "%')";
        }
## Total number of records without filtering
        $builder1 = $this->db->table('invoice_details a');
        $builder1->select("count(*) as allcount");
        $builder1->join('product_information b', 'b.product_id = a.product_id');
        $builder1->join('invoice c', 'c.invoice_id = a.invoice_id');
        $builder1->join('customer_information d', 'd.customer_id = c.customer_id');
        if ($searchValue != '') {
            $builder1->where($searchQuery);
        }
        if (!empty($fromdate) && !empty($todate)) {
            $builder1->where($datbetween);
        }
        if ($category_id != '') {
            $builder1->where('b.category_id', $category_id);
        }
        $builder1->where('a.quantity >', 0);
        $query1 = $builder1->get();
        $records = $query1->getRow();
        $totalRecords = $records->allcount;

## Total number of record with filtering
        $builder2 = $this->db->table('invoice_details a');
        $builder2->select("count(*) as allcount");
        $builder2->join('product_information b', 'b.product_id = a.product_id');
        $builder2->join('invoice c', 'c.invoice_id = a.invoice_id');
        $builder2->join('customer_information d', 'd.customer_id = c.customer_id');
        if ($searchValue != '') {
            $builder2->where($searchQuery);
        }
        if (!empty($fromdate) && !empty($todate)) {
            $builder2->where($datbetween);
        }
        if ($category_id != '') {
            $builder2->where('b.category_id', $category_id);
        }
        $builder2->where('a.quantity >', 0);
        $query2 = $builder2->get();
        $records = $query2->getRow();
        $totalRecordwithFilter = $records->allcount;
## Fetch records
        $builder3 = $this->db->table('invoice_details a');
        $builder3->select("a.*,b.product_name,b.strength,c.date,c.invoice,c.total_amount,d.customer_name");
        $builder3->join('product_information b', 'b.product_id = a.product_id');
        $builder3->join('invoice c', 'c.invoice_id = a.invoice_id');
        $builder3->join('customer_information d', 'd.customer_id = c.customer_id');
        if ($searchValue != '') {
            $builder3->where($searchQuery);
        }
        if (!empty($fromdate) && !empty($todate)) {
            $builder3->where($datbetween);
        }
        if ($category_id != '') {
            $builder3->where('b.category_id', $category_id);
        }
        $builder3->where('a.quantity >', 0);
        $builder3->orderBy($columnName, $columnSortOrder);
        $builder3->limit($rowperpage, $start);
        $query3 = $builder3->get();
        $records = $query3->getResult();
        $data = array();
        $sl = 1;
        $setting_data = $this->setting_info();
        foreach ($records as $record) {
            $dis_amount = 0;
            if ($setting_data->discount_type == 1) {
                $dis_amount = ($record->quantity * $record->rate * $record->discount) / 100;
            }
            if ($setting_data->discount_type == 2) {
                $dis_amount = ($record->quantity * $record->discount);
            }

            if ($setting_data->discount_type == 3) {
                $dis_amount = ($record->discount);
            }
            $total = $record->quantity * $record->rate;
            $total_amount = ($total ? $total : 0) - ($dis_amount ? $dis_amount : 0);

            $data[] = array(
                'sl' => $sl,
                'date' => $record->date,
                'product_name' => $record->product_name . '(' . $record->strength . ')',
                'invoice' => $record->invoice,
                'customer_name' => $record->customer_name,
                'quantity' => $record->quantity,
                'rate' => $record->rate,
                'discount' => $dis_amount,
                'total' => number_format($total_amount, 2),
            );
            $sl++;
        }

## Response
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecordwithFilter,
            "iTotalDisplayRecords" => $totalRecords,
            "aaData" => $data
        );

        return $response;
    }

    public function getpurchasereportList($postData = null) {
        $response = array();
        $fromdate = $this->request->getVar('fromdate');
        $todate = $this->request->getVar('todate');
        if (!empty($fromdate)) {
            $datbetween = "(a.purchase_date BETWEEN '$fromdate' AND '$todate')";
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
            $searchQuery = " (b.manufacturer_name like '%" . $searchValue . "%' or a.chalan_no like '%" . $searchValue . "%' or a.purchase_date like'%" . $searchValue . "%')";
        }
## Total number of records without filtering
        $builder1 = $this->db->table('product_purchase a');
        $builder1->select("count(*) as allcount");
        $builder1->join('manufacturer_information b', 'b.manufacturer_id = a.manufacturer_id', 'left');
        $builder1->join('user c', 'c.id = a.purchase_by', 'left');
        if ($searchValue != '') {
            $builder1->where($searchQuery);
        }
        if (!empty($fromdate) && !empty($todate)) {
            $builder1->where($datbetween);
        }

        $query1 = $builder1->get();
        $records = $query1->getRow();
        $totalRecords = $records->allcount;

## Total number of record with filtering
        $builder2 = $this->db->table('product_purchase a');
        $builder2->select("count(*) as allcount");
        $builder2->join('manufacturer_information b', 'b.manufacturer_id = a.manufacturer_id', 'left');
        $builder2->join('user c', 'c.id = a.purchase_by', 'left');
        if ($searchValue != '') {
            $builder2->where($searchQuery);
        }
        if (!empty($fromdate) && !empty($todate)) {
            $builder2->where($datbetween);
        }
        $query2 = $builder2->get();
        $records = $query2->getRow();
        $totalRecordwithFilter = $records->allcount;
## Fetch records
        $builder3 = $this->db->table('product_purchase a');
        $builder3->select("a.*,b.manufacturer_name,c.firstname,c.lastname");
        $builder3->join('manufacturer_information b', 'b.manufacturer_id = a.manufacturer_id', 'left');
        $builder3->join('user c', 'c.id = a.purchase_by', 'left');
        if ($searchValue != '') {
            $builder3->where($searchQuery);
        }
        if (!empty($fromdate) && !empty($todate)) {
            $builder3->where($datbetween);
        }
        $builder3->orderBy($columnName, $columnSortOrder);
//        $builder3->limit($rowperpage, $start);
        if ($rowperpage == -1) {
            $builder3->limit(0, $start);
        } else {
            $builder3->limit($rowperpage, $start);
        }
        $query3 = $builder3->get();
        $records = $query3->getResult();
        $data = array();
        $sl = 1;

        foreach ($records as $record) {
            $data[] = array(
                'sl' => $sl,
                'chalan_no' => $record->chalan_no,
                'purchase_id' => $record->purchase_id,
                'manufacturer_name' => $record->manufacturer_name,
                'purchase_id' => $record->purchase_id,
                'purchase_date' => $record->purchase_date,
                'total_amount' => $record->grand_total_amount,
                'purchase_by' => $record->firstname . ' ' . $record->lastname,
            );
            $sl++;
        }

## Response
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecordwithFilter,
            "iTotalDisplayRecords" => $totalRecords,
            "aaData" => $data
        );

        return $response;
    }

    public function getcategorywise_purchasereportList($postData = null) {
        $response = array();
        $fromdate = ($this->request->getVar('fromdate') ? $this->request->getVar('fromdate') : date('Y-m-d'));
        $todate = ($this->request->getVar('todate') ? $this->request->getVar('todate') : date('Y-m-d'));
        $category_id = $this->request->getVar('category_id');
        if (!empty($category_id)) {
            $category_id = $category_id;
        } else {
            $category_id = "";
        }
        if (!empty($fromdate)) {
            $datbetween = "(d.purchase_date BETWEEN '$fromdate' AND '$todate')";
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
            $searchQuery = " (b.product_name like '%" . $searchValue . "%' or c.category_name like '%" . $searchValue . "%' or d.purchase_date like'%" . $searchValue . "%')";
        }
## Total number of records without filtering
        $builder1 = $this->db->table('product_purchase_details a');
        $builder1->select("count(*) as allcount");
        $builder1->join('product_information b', 'b.product_id = a.product_id');
        $builder1->join('product_category c', 'c.category_id = b.category_id');
        $builder1->join('product_purchase d', 'd.purchase_id = a.purchase_id');
        $builder1->join('manufacturer_information e', 'e.manufacturer_id = d.manufacturer_id');
        $builder1->join('user f', 'f.id = d.purchase_by', 'left');
        if ($searchValue != '') {
            $builder1->where($searchQuery);
        }
        if (!empty($fromdate) && !empty($todate)) {
            $builder1->where($datbetween);
        }
        if ($category_id != '') {
            $builder1->where('b.category_id', $category_id);
        }
        $builder1->where('a.quantity >', 0);
        $query1 = $builder1->get();
        $records = $query1->getRow();
        $totalRecords = $records->allcount;

## Total number of record with filtering
        $builder2 = $this->db->table('product_purchase_details a');
        $builder2->select("count(*) as allcount");
        $builder2->join('product_information b', 'b.product_id = a.product_id');
        $builder2->join('product_category c', 'c.category_id = b.category_id');
        $builder2->join('product_purchase d', 'd.purchase_id = a.purchase_id');
        $builder2->join('manufacturer_information e', 'e.manufacturer_id = d.manufacturer_id');
        $builder2->join('user f', 'f.id = d.purchase_by', 'left');
        if ($searchValue != '') {
            $builder2->where($searchQuery);
        }
        if (!empty($fromdate) && !empty($todate)) {
            $builder2->where($datbetween);
        }
        if ($category_id != '') {
            $builder2->where('b.category_id', $category_id);
        }
        $builder2->where('a.quantity >', 0);
        $query2 = $builder2->get();
        $records = $query2->getRow();
        $totalRecordwithFilter = $records->allcount;
## Fetch records
        $builder3 = $this->db->table('product_purchase_details a');
        $builder3->select('b.product_name, b.strength,d.manufacturer_id,e.manufacturer_name,d.purchase_by,a.*, d.purchase_date, c.category_name,f.firstname,f.lastname');
        $builder3->join('product_information b', 'b.product_id = a.product_id');
        $builder3->join('product_category c', 'c.category_id = b.category_id');
        $builder3->join('product_purchase d', 'd.purchase_id = a.purchase_id');
        $builder3->join('manufacturer_information e', 'e.manufacturer_id = d.manufacturer_id');
        $builder3->join('user f', 'f.id = d.purchase_by', 'left');
        if ($searchValue != '') {
            $builder3->where($searchQuery);
        }
        if (!empty($fromdate) && !empty($todate)) {
            $builder3->where($datbetween);
        }
        if ($category_id != '') {
            $builder3->where('b.category_id', $category_id);
        }
        $builder3->where('a.quantity >', 0);
        $builder3->orderBy($columnName, $columnSortOrder);
        $builder3->limit($rowperpage, $start);
        $query3 = $builder3->get();
        $records = $query3->getResult();
        $data = array();
        $sl = 1;

        foreach ($records as $record) {
            $dis_amount = ($record->quantity ? $record->quantity : 0) * ($record->discount ? $record->discount : 0);
            $total = $record->quantity * $record->unit_rate;
            $net_total = ($total ? $total : 0) - ($dis_amount ? $dis_amount : 0);
            $data[] = array(
                'sl' => $sl,
                'category_name' => $record->category_name,
                'product_name' => $record->product_name . '(' . $record->strength . ')',
                'manufacturer_name' => $record->manufacturer_name,
                'purchase_date' => $record->purchase_date,
                'quantity' => $record->quantity,
                'rate' => $record->unit_rate,
                'discount' => $dis_amount,
                'total' => $net_total,
                'purchase_by' => $record->firstname . ' ' . $record->lastname,
            );
            $sl++;
        }

## Response
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecordwithFilter,
            "iTotalDisplayRecords" => $totalRecords,
            "aaData" => $data
        );

        return $response;
    }

    public function setting_info() {
        $builder = $this->db->table('setting')
                ->get()
                ->getRow();
        return $builder;
    }

    public function getemployeereportList($postData = null) {
         
        $response = array();
        $fromdate = $this->request->getVar('fromdate');
        $todate = $this->request->getVar('todate');
        $employee_id = $this->request->getVar('customer_id');

        if (!empty($fromdate)) {
            $datbetween = "(invoice.date BETWEEN '$fromdate' AND '$todate')";
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
            $searchQuery = " (product_information.product_name like '%" . $searchValue . "%' or invoice_details.invoice_id like '%" . $searchValue . "%' or invoice.date like'%" . $searchValue . "%' or customer_information.customer_name like'%" . $searchValue . "%')";
        }
## Total number of records without filtering
        $builder1 = $this->db->table('invoice');
        $builder1->select("count(*) as allcount");
        $builder1->join('customer_information', 'customer_information.customer_id = invoice.customer_id', 'left');
        $builder1->join('invoice_details', 'invoice_details.invoice_id = invoice.invoice_id', 'left');
        $builder1->join('product_information', 'invoice_details.product_id = product_information.product_id', 'left');
        if(!empty($employee_id)){
            $builder1->where('invoice.customer_id', $employee_id);
        }
        if ($searchValue != '') {
            $builder1->where($searchQuery);
        }
        if (!empty($fromdate) && !empty($todate)) {
            $builder1->where($datbetween);
        }

        $query1 = $builder1->get();
        $records = $query1->getRow();
        $totalRecords = $records->allcount;

## Total number of record with filtering
        $builder2 = $this->db->table('invoice');
        $builder2->select("count(*) as allcount");
        $builder2->join('customer_information', 'customer_information.customer_id = invoice.customer_id', 'left');
        $builder2->join('invoice_details', 'invoice_details.invoice_id = invoice.invoice_id', 'left');
        $builder2->join('product_information', 'invoice_details.product_id = product_information.product_id', 'left');
        
        if(!empty($employee_id)){
            $builder2->where('invoice.customer_id', $employee_id);
        }

        if ($searchValue != '') {
            $builder2->where($searchQuery);
        }
        if (!empty($fromdate) && !empty($todate)) {
            $builder2->where($datbetween);
        }
        $query2 = $builder2->get();
        $records = $query2->getRow();
        $totalRecordwithFilter = $records->allcount;
## Fetch records
        $builder3 = $this->db->table('invoice');
        $builder3->select("invoice_details.*,customer_information.customer_name, invoice.date, product_information.product_name");
        $builder3->join('customer_information', 'customer_information.customer_id = invoice.customer_id', 'left');
        $builder3->join('invoice_details', 'invoice_details.invoice_id = invoice.invoice_id', 'left');
        $builder3->join('product_information', 'invoice_details.product_id = product_information.product_id', 'left');
//        $builder3->where('invoice.customer_id', $employee_id);
        if(!empty($employee_id)){
            $builder3->where('invoice.customer_id', $employee_id);
        }

        if ($searchValue != '') {
            $builder3->where($searchQuery);
        }
        if (!empty($fromdate) && !empty($todate)) {
            $builder3->where($datbetween);
        }
        $builder3->orderBy($columnName, $columnSortOrder);
//        $builder3->limit($rowperpage, $start);
        if ($rowperpage == -1) {
            $builder3->limit(0, $start);
        } else {
            $builder3->limit($rowperpage, $start);
        }
        $query3 = $builder3->get();
        $records = $query3->getResult();

/////
        $data = array();
        $sl = 1;

        foreach ($records as $record) {
            $data[] = array(
                'sl' => $sl,
                'date' => $record->date,
                'invoice_id' => $record->invoice_id,
                'customer_name' => $record->customer_name,
                'product_name' => $record->product_name,
                'quantity' => $record->quantity,
                'total_amount' => $record->total_price,
            );
            $sl++;
        }

## Response
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecordwithFilter,
            "iTotalDisplayRecords" => $totalRecords,
            "aaData" => $data
        );

//        echo "<pre>";print_r($data);exit;

        return $response;
    }

    public function getemployeereportList_2($postData = null) {

        $response = array();
        $fromdate = $this->request->getVar('fromdate');
        $todate = $this->request->getVar('todate');
        $employee_id = $this->request->getVar('customer_id');
//$employee_id = 5;
        if (!empty($fromdate)) {
            $datbetween = "(invoice.date BETWEEN '$fromdate' AND '$todate')";
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
            $searchQuery = " (product_information.product_name like '%" . $searchValue . "%' or invoice_details.product_id like '%" . $searchValue . "%' or invoice.date like'%" . $searchValue . "%' or customer_information.customer_name like'%" . $searchValue . "%' or customer_information.user_id_num like'%" . $searchValue . "%')";
        }

        
        $builder4 = $this->db->table('invoice');
        $builder4->select("invoice_details.*,customer_information.customer_name,customer_information.user_id_num, invoice.date, product_information.product_name,SUM(invoice_details.quantity) as quantityByProduct,SUM(invoice_details.total_price) as total_priceByProduct");
        $builder4->join('customer_information', 'customer_information.customer_id = invoice.customer_id', 'left');
        $builder4->join('invoice_details', 'invoice_details.invoice_id = invoice.invoice_id', 'left');
        $builder4->join('product_information', 'invoice_details.product_id = product_information.product_id', 'left');
        if(!empty($employee_id)){
            $builder4->where('invoice.customer_id', $employee_id);
        }
        
        $builder4->groupBy('invoice_details.product_id');

        if ($searchValue != '') {
            $builder4->where($searchQuery);
        }
        if (!empty($fromdate) && !empty($todate)) {
            $builder4->where($datbetween);
        }
        $builder4->orderBy($columnName, $columnSortOrder);
//        $builder3->limit($rowperpage, $start);
       
        $query4 = $builder4->get();
        $records4 = $query4->getResult();
        
//        echo "<pre>";print_r($records4);exit;
        
        $builder3 = $this->db->table('invoice');
        $builder3->select("invoice_details.*,customer_information.customer_name,customer_information.user_id_num, invoice.date, product_information.product_name,SUM(invoice_details.quantity) as quantityByProduct,SUM(invoice_details.total_price) as total_priceByProduct");
        $builder3->join('customer_information', 'customer_information.customer_id = invoice.customer_id', 'left');
        $builder3->join('invoice_details', 'invoice_details.invoice_id = invoice.invoice_id', 'left');
        $builder3->join('product_information', 'invoice_details.product_id = product_information.product_id', 'left');
        if(!empty($employee_id)){
            $builder3->where('invoice.customer_id', $employee_id);
        }
        
        $builder3->groupBy('invoice_details.product_id');

        if ($searchValue != '') {
            $builder3->where($searchQuery);
        }
        if (!empty($fromdate) && !empty($todate)) {
            $builder3->where($datbetween);
        }
        $builder3->orderBy($columnName, $columnSortOrder);
//        $builder3->limit($rowperpage, $start);
        if ($rowperpage == -1) {
            $builder3->limit(0, $start);
        } else {
            $builder3->limit($rowperpage, $start);
        }
        $query3 = $builder3->get();
        $records = $query3->getResult();

//       echo "<pre>";print_r($records);exit;
        $data = array();
        $sl = 1;

        foreach ($records as $record) {
            $data[] = array(
                'sl' => $sl,
                'customer_name' => $record->customer_name,
                'customer_id' => $record->user_id_num,
                'product_name' => $record->product_name,
                'product_id' => $record->product_id,
                'quantity' => $record->quantityByProduct,
                'total_amount' => $record->total_priceByProduct,
            );
            $sl++;
        }

       
## Response
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => count($records4),
            "iTotalDisplayRecords" => count($records4),
            "aaData" => $data
        );

//        echo "<pre>";print_r($data);exit;

        return $response;
    }

}
