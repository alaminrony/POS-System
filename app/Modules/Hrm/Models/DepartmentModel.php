<?php

namespace App\Modules\Hrm\Models;

class DepartmentModel {

    public function __construct() {
        $this->db = db_connect();
        $this->session = \Config\Services::session();
        helper(['form', 'url']);
        $this->request = \Config\Services::request();
    }

    public function findAll() {
        $builder = $this->db->table('department');
        $builder->select("*");
        $query = $builder->get();
        return $query->getResult();
    }

    public function singledata($id) {
        $builder = $this->db->table('department')
                ->where('id', $id)
                ->get()
                ->getRow();
        return $builder;
    }

    public function save_department($data = []) {
        $builder = $this->db->table('department');
        return $add_manufacturer = $builder->insert($data);
    }

    public function update_department($data = []) {
        $query = $this->db->table('department');
        $query->where('id', $data['id']);
        return $cus_up = $query->update($data);
    }

    public function delete_department($id) {
        $builder = $this->db->table('department');
        $builder->where('id', $id);
        return $builder->delete();
    }
    
    public function departmentList() {
        $builder = $this->db->table('department');
        $builder->select("*");
        $query = $builder->get();
        return $query->getResult();
    }

}
