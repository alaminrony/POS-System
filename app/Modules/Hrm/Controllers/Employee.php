<?php

namespace App\Modules\Hrm\Controllers;

class Employee extends BaseController {
    #------------------------------------    
    # Author: Bdtask Ltd
    # Author link: https://www.bdtask.com/
    # Dynamic style php file
    # Developed by :Isahaq
    #------------------------------------    

    public function index() {


        $data['title'] = 'employee List';
        $data['module'] = "Hrm";
        $data['page'] = "employee/employee_list";
        return $this->template->layout($data);
    }

    public function bdtask_CheckemployeeList() {
        $postData = $this->request->getVar();
        $data = $this->employeeModel->getemployeeList($postData);
        echo json_encode($data);
    }

    public function bdtask_0001_employee_form($id = null) {


        helper(['form', 'url']);
        $this->validation = \Config\Services::validation();
        if (!empty($this->request->getFile('image'))) {
            $this->validation->setRule('image', lan('image'), 'ext_in[image,png,jpg,jpeg,gif,ico]|is_image[image]');
        }

        if ($this->validation->withRequest($this->request)->run() && $this->request->getFile('image')) {

            $image_path = $this->imageupload->upload_image($this->request->getFile('image'), 'assets/dist/img/employee/', $this->request->getVar('image'), 60, 70);
        } else {
            $image_path = "";
        }
        $old_image = $this->request->getVar('old_image');
        $data = [];
        $data['employee'] = (object) $userLevelData = array(
            'id' => ($this->request->getVar('employee_id') ? $this->request->getVar('employee_id') : null),
            'first_name' => $this->request->getVar('firstname', FILTER_SANITIZE_STRING),
            'last_name' => $this->request->getVar('lastname', FILTER_SANITIZE_STRING),
            'designation' => $this->request->getVar('designation_id', FILTER_SANITIZE_STRING),
            'rate_type' => $this->request->getVar('rate_type', FILTER_SANITIZE_STRING),
            'phone' => $this->request->getVar('phone', FILTER_SANITIZE_STRING),
            'hrate' => $this->request->getVar('hrate', FILTER_SANITIZE_STRING),
            'email' => $this->request->getVar('email', FILTER_SANITIZE_STRING),
            'blood_group' => $this->request->getVar('blood_group', FILTER_SANITIZE_STRING),
            'address_line_1' => $this->request->getVar('address_line_1', FILTER_SANITIZE_STRING),
            'address_line_2' => $this->request->getVar('address_line_2', FILTER_SANITIZE_STRING),
            'country' => $this->request->getVar('country', FILTER_SANITIZE_STRING),
            'city' => $this->request->getVar('city', FILTER_SANITIZE_STRING),
            'zip' => $this->request->getVar('zip', FILTER_SANITIZE_STRING),
            'image' => (($image_path != '/') ? $image_path : $old_image),
            'status' => $this->request->getVar('status'),
        );

        if ($this->request->getMethod() == 'post') {

            $rules = [
                'firstname' => ['label' => lan('firstname'), 'rules' => 'required|min_length[2]|max_length[20]'],
                'designation_id' => ['label' => lan('designation'), 'rules' => 'required'],
                'rate_type' => ['label' => lan('rate_type'), 'rules' => 'required'],
                'hrate' => ['label' => lan('hour_rate'), 'rules' => 'required'],
                'phone' => ['label' => lan('phone'), 'rules' => 'required'],
                'status' => ['label' => lan('status'), 'rules' => 'required'],
            ];

            if (!$this->validate($rules)) {
                $this->session->setFlashdata(array('exception' => $this->validator->listErrors()));
                return redirect()->to(base_url('employee/add_employee'));
            } else {
                if (empty($id)) {
                    if ($this->employeeModel->save_employee($userLevelData)) {
                        $this->session->setFlashdata('message', lan('save_successfully'));
                        return redirect()->to(base_url('/employee/employee_list/'));
                    } else {
                        $this->session->setFlashdata('exception', lan('please_try_again'));
                        return redirect()->to(base_url('/employee/employee_list/'));
                    }
                } else {
                    $this->employeeModel->update_employee($userLevelData);
                    $this->session->setFlashdata('message', lan('successfully_updated'));

                    return redirect()->to(base_url('/employee/employee_list/'));
                }
            }
        }

        $data['module'] = "Hrm";
        if (!empty($id)) {
            $data['employee'] = $this->employeeModel->singledata($id);
        }
        $data['title'] = 'Employee';
        $data['designation_list'] = $this->employeeModel->designation_list();
        $data['page'] = "employee/employee_form";
        return $this->template->layout($data);
    }

    public function delete_employee($id = null) {
        if ($this->employeeModel->delete_employee($id)) {
            $this->session->setFlashdata('message', lan('successfully_deleted'));
        } else {
            $this->session->setFlashdata('exception', lan('please_try_again'));
        }

        return redirect()->route('employee/employee_list');
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
        return $con;
    }

    public function designation_list() {


        $data['title'] = 'Designation List';
        $data['module'] = "Hrm";
        $data['designation_list'] = $this->designationModel->findAll();
        $data['page'] = "employee/designation_list";
        return $this->template->layout($data);
    }

    public function bdtask_0002_designation_form($id = null) {

        $id = (!empty($id) ? $id : $this->request->getVar('designation_id'));
        $data = [];
        $data['designation'] = (object) $designationdata = array(
            'id' => ($this->request->getVar('designation_id') ? $this->request->getVar('designation_id') : null),
            'designation' => $this->request->getVar('designation_name', FILTER_SANITIZE_STRING),
            'details' => $this->request->getVar('details', FILTER_SANITIZE_STRING),
        );

        if ($this->request->getMethod() == 'post') {

            $rules = [
                'designation_name' => ['label' => lan('designation_name'), 'rules' => 'required'],
            ];

            if (!$this->validate($rules)) {
                $data['validation'] = $this->validator;
            } else {
                if (empty($id)) {
                    $this->designationModel->save_designation($designationdata);
                    $this->session->setFlashdata('message', 'Successful Save');
                    return redirect()->to(base_url('/employee/designation_list/'));
                } else {
                    $this->designationModel->update_designation($designationdata);
                    $this->session->setFlashdata('message', 'Successful Updated');

                    return redirect()->to(base_url('/employee/designation_list/'));
                }
            }
        }

        $data['module'] = "Hrm";
        if (!empty($id)) {
            $data['designation'] = $this->designationModel->singledata($id);
        }
        $data['title'] = 'Designation';
        $data['page'] = "employee/designation_form";
        return $this->template->layout($data);
    }

    public function delete_designation($id = null) {
        if ($this->designationModel->delete_designation($id)) {
            $this->session->setFlashdata('message', lan('successfully_deleted'));
        } else {
            $this->session->setFlashdata('exception', lan('please_try_again'));
        }

        return redirect()->route('employee/designation_list');
    }

    public function bdtask_0002_department_form($id = null) {
//       echo "<pre>";print_r($this->request->getVar());exit;

        $id = (!empty($id) ? $id : $this->request->getVar('department_id'));

        $data = [];
        $data['department'] = (object) $departmentdata = array(
            'id' => ($this->request->getVar('department_id') ? $this->request->getVar('department_id') : null),
            'department_name' => $this->request->getVar('department_name', FILTER_SANITIZE_STRING),
            'parent_id' => $this->request->getVar('parent_id', FILTER_SANITIZE_STRING),
        );

//        echo "<pre>";print_r($data['department']);exit;

        if ($this->request->getMethod() == 'post') {

            $rules = [
                'department_name' => ['label' => lan('department_name'), 'rules' => 'required'],
            ];

            if (!$this->validate($rules)) {
                $data['validation'] = $this->validator;
            } else {
                if (empty($id)) {
                    $this->departmentModel->save_department($departmentdata);
                    $this->session->setFlashdata('message', 'Successful Save');
                    return redirect()->to(base_url('/employee/department_list/'));
                } else {
                    $this->departmentModel->update_department($departmentdata);
                    $this->session->setFlashdata('message', 'Successful Updated');

                    return redirect()->to(base_url('/employee/department_list/'));
                }
            }
        }

        $departmentList = $this->departmentModel->departmentList();

//        echo "<pre>";print_r($departmentList);exit;

        $data['module'] = "Hrm";
        if (!empty($id)) {
            $data['department'] = $this->departmentModel->singledata($id);
        }

        $data['title'] = 'Department';
        $data['page'] = "employee/department_form";
        $data['departmentList'] = $departmentList;
        return $this->template->layout($data);
    }

    public function department_list() {


        $data['title'] = 'Department List';
        $data['module'] = "Hrm";
        $data['department_list'] = $this->departmentModel->findAll();

        if (!empty($data['department_list'])) {
            $parentDepArr = [];
            $mainDepArr = [];
            foreach ($data['department_list'] as $pdept) {
                if($pdept->parent_id != 0){
                    $parentDepArr[$pdept->id][$pdept->parent_id] = $pdept->parent_id;
                }
                $mainDepArr[$pdept->id] = $pdept->department_name;
            }
        }
        
//        echo "<pre>";
//        print_r($parentDepArr[10][1]);
//        echo "<br>";
//        print_r($mainDepArr);
//        exit;

        $data['mainDepArr'] = $mainDepArr;
        $data['parentDepArr'] = $parentDepArr;

//        echo "<pre>";print_r($parentDepArr);exit;


        $data['page'] = "employee/department_list";
        return $this->template->layout($data);
    }

    public function delete_department($id = null) {
        if ($this->departmentModel->delete_department($id)) {
            $this->session->setFlashdata('message', lan('successfully_deleted'));
        } else {
            $this->session->setFlashdata('exception', lan('please_try_again'));
        }

        return redirect()->route('employee/department_list');
    }

}
