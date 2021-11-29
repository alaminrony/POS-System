<?php

namespace App\Modules\Dashboard\Models;

class UserModel {

    public function __construct() {
        $this->db = db_connect();
    }

    public function findAll() {
        $builder = $this->db->table('user');
        $builder->select("*,CONCAT_WS(' ',firstname, lastname) AS fullname");
        $query = $builder->get();
        return $query->getResult();
    }

    public function singledata($id) {
        $builder = $this->db->table('user')
                ->where('id', $id)
                ->get()
                ->getRow();
        return $builder;
    }

    public function get_company_info() {
        $builder = $this->db->table('setting')
                ->get()
                ->getRow();
        return $builder;
    }

    public function save_user($data = []) {
        $builder = $this->db->table('user');
        return $builder->insert($data);
    }

    public function update_user($data = []) {
        $query = $this->db->table('user');
        $query->where('id', $data['id']);
        return $query->update($data);
    }

    public function delete_user($id) {
        $builder = $this->db->table('user');
        $builder->where('id', $id);
        return $builder->delete();
    }

    public function check_email($email) {
        return $exitstdata = $this->db->table('user')
                ->where('email', $email)
                ->countAllResults();
    }

    public function token_set($email, $token) {
        $data = array(
            'password_reset_token' => $token,
        );
        $query = $this->db->table('user');
        $query->where('email', $email);
        return $query->update($data);
    }

    public function check_token($email, $token) {
        return $exitstdata = $this->db->table('user')
                ->where('email', $email)
                ->where('password_reset_token', $token)
                ->countAllResults();
    }

    public function reset_password($email, $password) {
        $data = array(
            'password_reset_token' => 'NULL',
            'password' => md5($password),
        );
        $query = $this->db->table('user');
        $query->where('email', $email);
        return $query->update($data);
    }

    public function designationList() {
        $designations = $this->db->table('designation')->select("*")->get()->getResult();

        return $designations;
    }

    public function departmentList() {
        $departments = $this->db->table('department')->select("*")->where('parent_id', 0)->get()->getResult();

        if (!empty($departments)) {
            $newDepArr = [];

//            foreach ($departments as $department) {
//                $i = 0;
//                foreach ($departments as $department2) {
//                    if ($department1->id == $department2->parent_id) {
////                        echo "<pre>";
////                        print_r($department2);
////                        exit;
//                        $newDepArr[$i]['id'] = $department1->id;
//                        $newDepArr[$i]['department_name'] = $department1->department_name;
////                        $newDepArr[$i]['parent_id'] = $department2->parent_id;
////                        $newDepArr[$i]['parent_dep_name'] = $department2->department_name;
//                        $newDepArr[$i]['sub_dep_arr'][] = $department2->department_name;
//                    
//                        $i++;
//                    }
//                }
//            }

            foreach ($departments as $department) {
                $newDepArr[$department->id] = $department->department_name;
                $childDepts = $this->db->table('department')->select("*")->where('parent_id', $department->id)->get()->getResult();
                foreach ($childDepts as $childDept) {
                    $newDepArr[$childDept->id] = '--' . $childDept->department_name;
                }
            }
        }

        return $newDepArr;
    }

    public function updateUserToCustomer($userLevelData, $id) {
//         echo "<pre>";print_r($id);exit;
        $cus_info = $this->db->table('customer_information')->where('user_id', $id)->get()->getRow();
        if (!empty($cus_info)) {
            $data = [
                'user_id' => $userLevelData['id'],
                'customer_name' => $userLevelData['firstname'] . ' ' . $userLevelData['lastname'],
                'customer_email' => $userLevelData['email'],
                'email_address' => $userLevelData['email'],
                'department' => $userLevelData['department'],
                'designation' => $userLevelData['designation'],
                'user_id_num' => $userLevelData['user_id'],
                'is_management' => $userLevelData['is_management'],
                'status' => 1,
//                'create_by' => '1',
            ];

            $query = $this->db->table('customer_information');
            $query->where('user_id', $id);
            $cus_up = $query->update($data);

            $c_acc = $cus_info->customer_id . '-' . $userLevelData['firstname'] . ' ' . $userLevelData['lastname'];
            $customer_coa = [
                'HeadName' => $c_acc
            ];
            if ($cus_up) {
                $coa_up = $this->db->table('acc_coa');
                $coa_up->where('customer_id', $cus_info->customer_id);
                return $coa_up->update($customer_coa);
            } else {
                return false;
            }
        }
    }

}
