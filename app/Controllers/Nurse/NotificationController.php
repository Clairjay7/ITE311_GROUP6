<?php

namespace App\Controllers\Nurse;

use App\Controllers\BaseController;
use App\Models\NurseNotificationModel;

class NotificationController extends BaseController
{
    public function markAsRead($id)
    {
        // Check if user is logged in and is a nurse
        if (!session()->get('logged_in') || session()->get('role') !== 'nurse') {
            return $this->response->setJSON(['error' => 'Unauthorized'])->setStatusCode(401);
        }

        $nurseId = session()->get('user_id');
        $notificationModel = new NurseNotificationModel();

        if ($notificationModel->markAsRead($id, $nurseId)) {
            return $this->response->setJSON(['success' => true]);
        }

        return $this->response->setJSON(['error' => 'Failed to mark as read'])->setStatusCode(400);
    }

    public function markAllAsRead()
    {
        // Check if user is logged in and is a nurse
        if (!session()->get('logged_in') || session()->get('role') !== 'nurse') {
            return $this->response->setJSON(['error' => 'Unauthorized'])->setStatusCode(401);
        }

        $nurseId = session()->get('user_id');
        $notificationModel = new NurseNotificationModel();

        if ($notificationModel->markAllAsRead($nurseId)) {
            return $this->response->setJSON(['success' => true]);
        }

        return $this->response->setJSON(['error' => 'Failed to mark all as read'])->setStatusCode(400);
    }
}

