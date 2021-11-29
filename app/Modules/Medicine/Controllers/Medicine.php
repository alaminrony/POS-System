<?php

namespace App\Modules\Medicine\Controllers;

class Medicine extends BaseController {

    public function index() {
        if (!$this->session->get('isLogIn')) {
            return redirect()->route('login');
        }

        $data['title'] = 'medicine List';
        $data['module'] = "Medicine";
        $data['page'] = "medicine_list";
        return $this->template->layout($data);
    }

    public function bdtask_CheckmedicineList() {
        $postData = $this->request->getVar();
        $data = $this->medicineModel->getmedicineList($postData);
        echo json_encode($data);
    }

    public function bdtask_0001_medicine_form($id = null) {
//        echo "<pre>";print_r($this->request->getVar());exit;
        if (!$this->session->get('isLogIn')) {
            return redirect()->route('login');
        }
//        $post_id = (!empty($this->request->getVar('barcode_id', FILTER_SANITIZE_STRING)) ? $this->request->getVar('barcode_id', FILTER_SANITIZE_STRING) : $this->generator(8));
        $product_id = (!empty($this->request->getVar('product_id', FILTER_SANITIZE_STRING)) ? $this->request->getVar('product_id', FILTER_SANITIZE_STRING) : '');

        helper(['form', 'url']);
        $this->validation = \Config\Services::validation();
        if (!empty($this->request->getFile('image'))) {
            $this->validation->setRule('image', lan('image'), 'ext_in[image,png,jpg,jpeg,gif,ico]|is_image[image]');
        }

        if ($this->validation->withRequest($this->request)->run() && $this->request->getFile('image')) {

            $image_path = $this->imageupload->upload_image($this->request->getFile('image'), 'assets/dist/img/products/', $this->request->getVar('image'), 857, 806);
        } else {
            $image_path = "";
        }

        $boxSize = explode('-', $this->request->getVar('box_size'))[1];
        $leaf_id = explode('-', $this->request->getVar('box_size'))[0];

        $old_image = $this->request->getVar('old_image');
        $price = $this->request->getVar('price', FILTER_SANITIZE_STRING);
        $sup_price = $this->request->getVar('manufacturer_price', FILTER_SANITIZE_STRING);
        $unit_saleprice = (!empty($price) ? $price : 0) / (!empty($boxSize) ? $boxSize : 1);
        $unit_purchaseprice = (!empty($sup_price) ? $sup_price : 0) / (!empty($boxSize) ? $boxSize : 1);

        $data = [];
        $data['medicine'] = (object) $userLevelData = array(
            'product_id' => $product_id,
            'product_name' => $this->request->getVar('medicine_name', FILTER_SANITIZE_STRING),
            'category_id' => $this->request->getVar('category_id', FILTER_SANITIZE_STRING),
            'generic_name' => $this->request->getVar('generic_name', FILTER_SANITIZE_STRING),
            'strength' => $this->request->getVar('strength', FILTER_SANITIZE_STRING),
            'box_size' => $boxSize ?? 0,
            'leaf_id' => $leaf_id ?? 0,
            'product_location' => $this->request->getVar('product_location', FILTER_SANITIZE_STRING),
            'price' => $unit_saleprice,
            'b_price' => $this->request->getVar('price', FILTER_SANITIZE_STRING),
            'm_b_price' => $this->request->getVar('manufacturer_price', FILTER_SANITIZE_STRING),
            'product_type' => $this->request->getVar('product_type', FILTER_SANITIZE_STRING),
            'manufacturer_id' => $this->request->getVar('manufacturer_id', FILTER_SANITIZE_STRING),
//            'manufacturer_price' => $this->rounder($unit_purchaseprice),
            'manufacturer_price' => $unit_purchaseprice,
            'unit' => $this->request->getVar('unit', FILTER_SANITIZE_STRING),
            'product_details' => $this->request->getVar('product_details', FILTER_SANITIZE_STRING),
            'image' => (($image_path != '/') ? $image_path : $old_image),
            'status' => $this->request->getVar('status'),
            'brand_name' => $this->request->getVar('brand_name'),
        );

//        echo "<pre>";print_r($data['medicine']);exit;

        $tablecolumn = $this->db->getFieldNames('tax_collection');
        $num_column = count($tablecolumn) - 4;
        if ($num_column > 0) {
            $txf = [];
            for ($i = 0; $i < $num_column; $i++) {
                $txf[$i] = 'tax' . $i;
            }
            foreach ($txf as $key => $value) {
                $userLevelData[$value] = (!empty($this->request->getVar($value)) ? $this->request->getVar($value) : 0) / 100;
            }
        }

        if ($this->request->getMethod() == 'post') {

            $rules = [
                'product_id' => ['label' => 'Item Id', 'rules' => 'required'],
                'medicine_name' => ['label' => lan('medicine_name'), 'rules' => 'required|min_length[3]|max_length[100]'],
                'box_size' => ['label' => lan('box_size'), 'rules' => 'required'],
                'category_id' => ['label' => lan('category'), 'rules' => 'required'],
                'price' => ['label' => lan('price'), 'rules' => 'required'],
                'manufacturer_id' => ['label' => lan('manufacturer'), 'rules' => 'required'],
                'manufacturer_price' => ['label' => lan('manufacturer_price'), 'rules' => 'required'],
                'status' => ['label' => lan('status'), 'rules' => 'required'],
            ];

            if (!$this->validate($rules)) {
                $this->session->setFlashdata(array('exception' => $this->validator->listErrors()));
                return redirect()->to(base_url('medicine/add_medicine'));
            } else {
                if (empty($id)) {
                    $this->medicineModel->save_medicine($userLevelData);
                    $this->session->setFlashdata('message', lan('save_successfully'));
                    return redirect()->to(base_url('/medicine/medicine_list/'));
                } else {
                    $this->medicineModel->update_medicine($userLevelData);
                    $this->session->setFlashdata('message', lan('successfully_updated'));

                    return redirect()->to(base_url('/medicine/medicine_list/'));
                }
            }
        }

        $data['module'] = "Medicine";
        if (!empty($id)) {
            $data['medicine'] = $this->medicineModel->singledata($id);
        }
        $data['category_list'] = $this->medicineModel->category_list();
        $data['unit_list'] = $this->medicineModel->unit_list();
        $data['type_list'] = $this->medicineModel->type_list();
        $data['taxfield'] = $this->medicineModel->tax_fields();
        $data['leaf'] = $this->medicineModel->leaf_setting_list();
//        echo "<pre>";print_r(count($data['leaf']));exit;
        $data['title'] = 'Medicine';
        $data['manufacturer_list'] = $this->medicineModel->manufacturer_list();
        $data['page'] = "medicine_form";
        return $this->template->layout($data);
    }

    public function check_medicine_id() {

        if (!empty($this->request->getVar('itemId'))) {
            $checkItemId = $this->db->table('product_information')->where('product_id', $this->request->getVar('itemId'))->get()->getRow();
            if (!empty($checkItemId)) {
                echo json_encode('exists');
            } else {
                echo json_encode('not_exists');
            }
        }
    }

    public function delete_medicine($id = null) {
        if ($this->medicineModel->delete_medicine($id)) {
            $this->session->setFlashdata('message', lan('successfully_deleted'));
        } else {
            $this->session->setFlashdata('exception', lan('please_try_again'));
        }

        return redirect()->route('medicine/medicine_list');
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

    public function bdtask_003_barcode_generate($id) {
        $data['module'] = "Medicine";
        $data['title'] = 'Barcode';
        $data['product_info'] = $this->medicineModel->singledata($id);
        $data['product_id'] = $id;
        $data['page'] = "barcode";
        return $this->template->layout($data);
    }

    public function bdtask_004_qrcode_generate($id) {
        $data['module'] = "Medicine";
        $data['title'] = 'QR-code';
        $data['product_info'] = $this->medicineModel->singledata($id);
        $data['product_id'] = $id;
        $data['page'] = "qrcode";
        return $this->template->layout($data);
    }

    public function importFile() {

        // Validation
        $input = $this->validate([
            'file' => 'uploaded[file]|max_size[file,1024]|ext_in[file,csv],'
        ]);

        if (!$input) { // Not valid
            $this->session->setFlashdata(array('exception' => $this->validator->listErrors()));
            return redirect()->to(base_url('medicine/add_medicine'));
        } else { // Valid
            if ($file = $this->request->getFile('file')) {
                if ($file->isValid() && !$file->hasMoved()) {

                    // Get random file name
                    $newName = $file->getRandomName();

                    // Store file in public/csvfile/ folder
                    $file->move('./assets/csvfile/medicine', $newName);

                    // Reading file
                    $file = fopen("./assets/csvfile/medicine/" . $newName, "r");

                    $i = 0;
                    $numberOfFields = 11; // Total number of fields

                    $importData_arr = array();

                    // Initialize $importData_arr Array
                    while (($filedata = fgetcsv($file, 1000, ",")) !== FALSE) {
                        $num = count($filedata);

//                        echo "<pre>";
//                        print_r($filedata);
//                        exit;
                        // Skip first row & check number of fields
                        if ($i > 0 && $num == $numberOfFields) {
                            $importData_arr[$i]['manufacturer_name'] = (!empty($filedata[0]) ? $filedata[0] : null);
                            $importData_arr[$i]['product_id'] = (!empty($filedata[1]) ? $filedata[1] : null);
                            $importData_arr[$i]['product_name'] = (!empty($filedata[2]) ? $filedata[2] : null);
                            $importData_arr[$i]['generic_name'] = (!empty($filedata[3]) ? $filedata[3] : null);
                            $importData_arr[$i]['strength'] = (!empty($filedata[4]) ? $filedata[4] : null);
                            $importData_arr[$i]['category_id'] = (!empty($filedata[5]) ? $filedata[5] : null);
                            $importData_arr[$i]['manufacturer_price'] = (!empty($filedata[6]) ? $filedata[6] : null);
                            $importData_arr[$i]['sale_price'] = (!empty($filedata[7]) ? $filedata[7] : null);
                            $importData_arr[$i]['box_size'] = (!empty($filedata[8]) ? $filedata[8] : null);
                            $importData_arr[$i]['leaf_setting'] = (!empty($filedata[9]) ? $filedata[9] : null);
                            $importData_arr[$i]['unit'] = (!empty($filedata[10]) ? $filedata[10] : null);
                        }

                        $i++;
                    }
                    fclose($file);

                    // Insert data
                    $count = 0;

                    foreach ($importData_arr as $insert_csv) {
                        
//                        echo "<pre>";
//                        print_r($importData_arr);
//                        exit;
                        if ($insert_csv['manufacturer_name'] == '') {
                            $excel_row = $count + 1;
                            session()->setFlashdata('exception', 'Input Manufacturer Name row Number ' . $excel_row);
                            return redirect()->to(base_url('medicine/add_medicine'));
                        }

                        if ($insert_csv['product_name'] == '') {
                            $excel_row = $count + 1;
                            session()->setFlashdata('exception', 'Input Medicine Name row Number ' . $excel_row);
                            return redirect()->to(base_url('medicine/add_medicine'));
                        }
                        if ($insert_csv['manufacturer_price'] == '') {
                            $excel_row = $count + 1;
                            session()->setFlashdata('exception', 'Input Manufactuerer Price row Number ' . $excel_row);
                            return redirect()->to(base_url('medicine/add_medicine'));
                        }

                        if ($insert_csv['sale_price'] == '') {
                            $excel_row = $count + 1;
                            session()->setFlashdata('exception', 'Input Sale Price row Number ' . $excel_row);
                            return redirect()->to(base_url('medicine/add_medicine'));
                        }
//                        $product_id = $this->generator(12);
                        $product_id = $insert_csv['product_id'];
                        $check_manufacturer = $this->db->table('manufacturer_information')
                                ->select('*')
                                ->where('manufacturer_name', $insert_csv['manufacturer_name'])
                                ->get()
                                ->getRow();
                        if (!empty($check_manufacturer)) {
                            $manufacturer_id = $check_manufacturer->manufacturer_id;
                        } else {
                            $manufacinfo = array(
                                'manufacturer_name' => $insert_csv['manufacturer_name'],
                                'address' => '',
                                'mobile' => '',
                                'details' => '',
                                'status' => 1
                            );

                            $minfo = $this->db->table('manufacturer_information');
                            $add_manufacturer = $minfo->insert($manufacinfo);
                            $manufacturer_id = $this->db->insertID();

                            $coa = $this->manufacturerModel->headcode();
                            if ($coa->HeadCode != NULL) {
                                $headcode = $coa->HeadCode + 1;
                            } else {
                                $headcode = "50202000001";
                            }
                            $c_acc = $manufacturer_id . '-' . $insert_csv['manufacturer_name'];
                            $createby = $this->session->get('id');
                            $createdate = date('Y-m-d H:i:s');

                            $manufacturer_coa = [
                                'HeadCode' => $headcode,
                                'HeadName' => $c_acc,
                                'PHeadName' => 'Account Payable',
                                'HeadLevel' => '3',
                                'IsActive' => '1',
                                'IsTransaction' => '1',
                                'IsGL' => '0',
                                'HeadType' => 'L',
                                'IsBudget' => '0',
                                'manufacturer_id' => $manufacturer_id,
                                'IsDepreciation' => '0',
                                'DepreciationRate' => '0',
                                'CreateBy' => $createby,
                                'CreateDate' => $createdate,
                            ];

                            $mcoa = $this->db->table('acc_coa');
                            $add_manufacturercoa = $mcoa->insert($manufacturer_coa);
                        }


                        $check_category = $this->db->table('product_category')->select('*')->where('category_name', $insert_csv['category_id'])->get()->getRow();
                        if (!empty($check_category)) {
                            $category_id = $check_category->category_id;
                        } else {
                            $categorydata = array(
                                'category_name' => $insert_csv['category_id'],
                                'status' => 1
                            );

                            $catinfo = $this->db->table('product_category');
                            $add_cat = $catinfo->insert($categorydata);
                            $category_id = $this->db->insertID();
                        }

                        $unit_saleprice = (!empty($insert_csv['sale_price']) ? $insert_csv['sale_price'] : 0) / (!empty($insert_csv['box_size']) ? $insert_csv['box_size'] : 1);
                        $unit_purchaseprice = (!empty($insert_csv['manufacturer_price']) ? $insert_csv['manufacturer_price'] : 0) / (!empty($insert_csv['box_size']) ? $insert_csv['box_size'] : 1);

                        if ($insert_csv['unit'] == 'ml') {
                            $unit = 1;
                        } elseif ($insert_csv['unit'] == 'mg') {
                            $unit = 2;
                        } elseif ($insert_csv['unit'] == 'pcs') {
                            $unit = 3;
                        } else {
                            $unit = '';
                        }
                        
                        $leafLists = $this->db->table('medicine_leaf_setting')->get()->getResult();
                        $leafArr = [];
                        if (!empty($leafLists)) {
                            foreach ($leafLists as $leaf) {
                                $leafArr[$leaf->id] = $leaf->leaf_type . '(' . $leaf->total_number . ')';
                            }
                        }
                        
                        $leaf_id = array_search($insert_csv['leaf_setting'], $leafArr);
                        
                        
                        $data = array(
                            'product_id' => $product_id,
                            'product_name' => $insert_csv['product_name'],
                            'category_id' => $category_id,
                            'generic_name' => $insert_csv['generic_name'],
                            'strength' => $insert_csv['strength'] ?? 0,
                            'box_size' => $insert_csv['box_size'],
                            'product_location' => '',
                            'price' => $this->rounder($unit_saleprice),
                            'b_price' => (!empty($insert_csv['sale_price']) ? $insert_csv['sale_price'] : 0),
                            'm_b_price' => $insert_csv['manufacturer_price'],
                            'product_type' => '',
                            'manufacturer_id' => $manufacturer_id,
                            'manufacturer_price' => $this->rounder($unit_purchaseprice),
                            'unit' => $unit,
                            'product_details' => '',
                            'image' => '',
                            'status' => 1,
                            'leaf_id' => $leaf_id,
                        );

                        $result = $this->db->table('product_information')
                                ->select('*')
                                ->where('product_name', $data['product_name'])
                                ->where('strength', $data['strength'])
                                ->where('category_id', $category_id)
                                ->get()
                                ->getRow();
                        if (empty($result)) {
                            $product_info = $this->db->table('product_information');
                            $add_product = $product_info->insert($data);
                            $product_id = $product_id;
                        } else {
                            $product_id = $result->product_id;
                            $udata = array(
                                'product_id' => $product_id,
                                'product_name' => $insert_csv['product_name'],
                                'category_id' => $category_id,
                                'generic_name' => $insert_csv['generic_name'],
                                'strength' => $insert_csv['strength'],
                                'box_size' => $insert_csv['box_size'],
                                'product_location' => '',
                                'price' => $unit_saleprice,
                                'b_price' => (!empty($insert_csv['sale_price']) ? $insert_csv['sale_price'] : 0),
                                'm_b_price' => $insert_csv['manufacturer_price'],
                                'product_type' => '',
                                'manufacturer_id' => $manufacturer_id,
                                'manufacturer_price' => $unit_purchaseprice,
                                'unit' => $unit,
                                'product_details' => '',
                                'image' => '',
                                'status' => 1,
                            );
                            $product_updata = $this->db->table('product_information');
                            $product_updata->where('product_id', $result->product_id);
                            $product_updata->update($udata);
                        }

                        $count++;
                    }


                    // Set Session
                    session()->setFlashdata('message', $count . ' Record inserted successfully!');
                    session()->setFlashdata('alert-class', 'alert-success');
                } else {
                    // Set Session
                    session()->setFlashdata('message', 'File not imported.');
                    session()->setFlashdata('alert-class', 'alert-danger');
                }
            } else {
                // Set Session
                session()->setFlashdata('message', 'File not imported.');
                session()->setFlashdata('alert-class', 'alert-danger');
            }
        }

        return redirect()->to(base_url('medicine/medicine_list'));
    }

    public function rounder($num) {
        $fln = $num - floor($num);
        if ($fln > 0 and $fln < 0.5) {
            return number_format($num, 2, '.', '');
        } else {
            return number_format(round($num), 2, '.', '');
        }
    }

}
