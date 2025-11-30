<?php

namespace App\Models;

use CodeIgniter\Model;

class NurseNotificationModel extends Model
{
    protected $table = 'nurse_notifications';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'nurse_id',
        'type',
        'title',
        'message',
        'related_id',
        'related_type',
        'is_read',
        'read_at',
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'nurse_id' => 'required|integer',
        'type' => 'required|in_list[lab_request_approved,lab_result_ready,new_doctor_order,appointment_reminder,system]',
        'title' => 'required|max_length[255]',
        'message' => 'required',
    ];

    /**
     * Get unread notifications for a nurse
     */
    public function getUnreadNotifications($nurseId, $limit = 10)
    {
        return $this->where('nurse_id', $nurseId)
            ->where('is_read', 0)
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id, $nurseId)
    {
        return $this->where('id', $id)
            ->where('nurse_id', $nurseId)
            ->update([
                'is_read' => 1,
                'read_at' => date('Y-m-d H:i:s')
            ]);
    }

    /**
     * Mark all notifications as read for a nurse
     */
    public function markAllAsRead($nurseId)
    {
        return $this->where('nurse_id', $nurseId)
            ->where('is_read', 0)
            ->update([
                'is_read' => 1,
                'read_at' => date('Y-m-d H:i:s')
            ]);
    }
}

