<?php

namespace App\Controllers\Receptionist;

use App\Controllers\BaseController;
use App\Models\DoctorScheduleModel;

class DoctorScheduleController extends BaseController
{
    protected $scheduleModel;

    public function __construct()
    {
        $this->scheduleModel = new DoctorScheduleModel();
    }

    /**
     * Redirect to admin schedule view
     * Doctor schedules are now managed through Admin > Schedule
     */
    public function index()
    {
        // Redirect to admin schedule view
        return redirect()->to('/admin/schedule')->with('info', 'Doctor schedules are now managed through Admin > Schedule. Only schedules created by Admin will be displayed.');
    }
}

