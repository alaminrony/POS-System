<?php

namespace App\Modules\Hrm\Controllers;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 *
 * @package CodeIgniter
 */
use CodeIgniter\Controller;
use App\Modules\Hrm\Models\EmployeeModel;
use App\Modules\Hrm\Models\AttendanceModel;
use App\Modules\Hrm\Models\DesignationModel;
use App\Modules\Hrm\Models\DepartmentModel;
use App\Modules\Hrm\Models\Payroll_model;
use App\Modules\Hrm\Models\Expense_model;
use App\Modules\Hrm\Models\Personalloan_model;

class BaseController extends Controller {

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var array
     */
    protected $helpers = ['form', 'url', 'lang_helper'];

    /**
     * Constructor.
     */
    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger) {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        //--------------------------------------------------------------------
        // Preload any models, libraries, etc, here.
        //--------------------------------------------------------------------
        // E.g.:
        $this->employeeModel = new EmployeeModel();
        $this->attendanceModel = new AttendanceModel();
        $this->designationModel = new DesignationModel();
        $this->departmentModel = new DepartmentModel();
        $this->payroll_model = new Payroll_model();
        $this->expense_model = new Expense_model();
        $this->personalloan_model = new Personalloan_model();
        if ($this->session->get('isLogIn') == false) {
            echo header("Location:" . base_url('login'));
            exit();
        }
    }

}
