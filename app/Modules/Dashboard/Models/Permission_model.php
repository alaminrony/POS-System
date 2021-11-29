<?php

namespace App\Modules\Dashboard\Models;

class Permission_model {

    public function __construct() {
        $this->db = db_connect();
        $this->session = \Config\Services::session();
        helper(['form', 'url']);
        $this->request = \Config\Services::request();
    }

    public function permission_list() {
        return $this->db->table('module')
                        ->select("*")
                        ->where('status', 1)
                        ->get()
                        ->getResultArray();
    }

    public function module_list2() {
        return $this->db->table('module')
                        ->select("*")
                        ->where('status', 1)
                        ->get()
                        ->getResult();
    }

    public function user_count() {
        $role_number = $this->db->table('sec_role')
                ->select("count(id) as total_role")
                ->get()
                ->getRow();

        return $role_number->total_role;
    }

    public function role_list() {
        return $this->db->table('sec_role')
                        ->select("*")
                        ->get()
                        ->getResultArray();
    }

    public function user() {
        return $this->db->table('user')
                        ->select("*")
                        ->get()
                        ->getResultArray();
    }

    public function singleUser($id) {
        return $this->db->table('user')
                        ->select("*")
                        ->where('id', $id)
                        ->get()
                        ->getRow();
    }
    
    public function existsCustomer($user_id){
        $cus_info = $this->db->table('customer_information')->where('user_id', $user_id)->get()->getRow();
        if(!empty($cus_info)){
           return true; 
        }
        return false;
    }

    public function insertUserToCustomer($userData) {
        
        
        $cusData = [
            'user_id' => $userData->id,
            'customer_name' => $userData->firstname . ' ' . $userData->lastname,
            'customer_email' => $userData->email,
            'email_address' => $userData->email,
            'department' => $userData->department,
            'designation' => $userData->designation,
            'user_id_num' => $userData->user_id,
            'is_management' => $userData->is_management,
            'status' => 1,
            'create_by' => $this->session->get('id'),
        ];

        $builder = $this->db->table('customer_information');
        $add_customer = $builder->insert($cusData);

        $customer_id = $this->db->insertID();
        $coa = $this->headcode();
        if ($coa->HeadCode != NULL) {
            $headcode = $coa->HeadCode + 1;
        } else {
            $headcode = "102030000001";
        }
        $c_acc = $customer_id . '-' . $cusData['customer_name'];
        $createby = $this->session->get('id');
//        echo "<pre>";print_r($createby);exit;
        $createdate = date('Y-m-d H:i:s');

        $customer_coa = [
            'HeadCode' => $headcode,
            'HeadName' => $c_acc,
            'PHeadName' => 'Customer Receivable',
            'HeadLevel' => '4',
            'IsActive' => '1',
            'IsTransaction' => '1',
            'IsGL' => '0',
            'HeadType' => 'A',
            'IsBudget' => '0',
            'IsDepreciation' => '0',
            'DepreciationRate' => '0',
            'customer_id' => $customer_id,
            'CreateBy' => $createby,
            'CreateDate' => $createdate,
        ];

        if ($add_customer) {
            $acc_coatbl = $this->db->table('acc_coa');
            $acc_coatbl->insert($customer_coa);
        }
        if ($add_customer && !empty($this->request->getVar('previous_balance'))) {
            $this->previous_balance_add($this->request->getVar('previous_balance'), $customer_id);
        }
        if ($add_customer) {
            return true;
        } else {
            return false;
        }
    }

    public function headcode() {
        $query = $this->db->query("SELECT MAX(HeadCode) as HeadCode FROM acc_coa WHERE HeadLevel='4' And HeadCode LIKE '1020300%'");
        return $query->getRow();
    }

    public function create($data = array()) {
        $dexitrole = $this->db->table('role_permission');
        $dexitrole->where('role_id', $data[0]['role_id']);
        $dexitrole->delete();
        $role = $this->db->table('role_permission');
        return $role->insertBatch($data);
    }

    public function role_create($postData = array()) {
        $add_role = $this->db->table('sec_userrole');
        return $add_role->insert($postData);
    }

    public function insert_user_entry($data = array()) {

        $role = $this->db->table('sec_role');
        $role->insert($data);
        return $insert_id = $this->db->insertID();
    }

    public function userdata_editdata($id) {
        return $this->db->table('sec_role')
                        ->select("*")
                        ->where('id', $id)
                        ->get()
                        ->getResultArray();
    }

    public function update_role($data, $id) {
        $query = $this->db->table('sec_role');
        $query->where('id', $id);
        return $query->update($data);
    }

    public function delete_role($id) {
        $dlt_role = $this->db->table('sec_role');
        $dlt_role->where('id', $id);
        return $dlt_role->delete();
    }

    public function delete_role_permission($id) {
        $dlt_permission = $this->db->table('role_permission');
        $dlt_permission->where('role_id', $id);
        return $dlt_permission->delete();
    }

    public function module() {
        return $this->db->table('module')
                        ->select("*")
                        ->get()
                        ->getResult();
    }

    public function role($id = null) {
        return $this->db->table('sec_role')
                        ->select("*")
                        ->where('id', $id)
                        ->get()
                        ->getResult();
    }

    public function role_edit($id = null) {
        return $this->db->table('role_permission')
                        ->select("role_permission.*,sub_module.name")
                        ->join('sub_module', 'sub_module.id=role_permission.fk_module_id')
                        ->where('role_permission.role_id', $id)
                        ->get()
                        ->getResult();
    }

    public function role_update($data, $id) {
        $query = $this->db->table('sec_role');
        $query->where('id', $id);
        return $query->update($data);
    }

    public function moduleinfo($id) {
        return $this->db->table('module')
                        ->select("*")
                        ->where('id', $id)
                        ->where('status', 1)
                        ->get()
                        ->getRow();
    }

    //module list
    public function module_list() {
        return $this->db->table('module')
                        ->select("*")
                        ->get()
                        ->getResult();
    }

    // menu info id wise
    public function menuinfo($id) {
        return $this->db->table('sub_module')
                        ->select("*")
                        ->where('id', $id)
                        ->where('status', 1)
                        ->get()
                        ->getRow();
    }

    public function insert_module($data = array()) {

        $module = $this->db->table('module');
        return $module->insert($data);
    }

    public function update_menu($data) {
        $query = $this->db->table('sub_module');
        $query->where('id', $data['id']);
        return $query->update($data);
    }

    public function insert_menu($data = array()) {

        $module = $this->db->table('sub_module');
        return $module->insert($data);
    }

}
