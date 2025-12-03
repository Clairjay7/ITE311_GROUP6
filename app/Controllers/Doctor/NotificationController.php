<?php

namespace App\Controllers\Doctor;

use App\Controllers\BaseController;
use App\Models\DoctorNotificationModel;

class NotificationController extends BaseController
{
    protected $notificationModel;

    public function __construct()
    {
        $this->notificationModel = new DoctorNotificationModel();
    }

    /**
     * Mark a single notification as read
     */
    public function markRead($notificationId)
    {
        // Check if user is logged in and is a doctor
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized'
            ])->setStatusCode(401);
        }

        $doctorId = session()->get('user_id');
        
        // Verify the notification belongs to this doctor
        $notification = $this->notificationModel->find($notificationId);
        if (!$notification || $notification['doctor_id'] != $doctorId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Notification not found or unauthorized'
            ])->setStatusCode(404);
        }

        // Mark as read
        if ($this->notificationModel->markAsRead($notificationId, $doctorId)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Notification marked as read'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to mark notification as read'
        ])->setStatusCode(500);
    }

    /**
     * Mark all notifications as read for the current doctor
     */
    public function markAllRead()
    {
        // Check if user is logged in and is a doctor
        if (!session()->get('logged_in') || session()->get('role') !== 'doctor') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized'
            ])->setStatusCode(401);
        }

        $doctorId = session()->get('user_id');
        
        // Mark all as read
        if ($this->notificationModel->markAllAsRead($doctorId)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'All notifications marked as read'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to mark notifications as read'
        ])->setStatusCode(500);
    }
}

