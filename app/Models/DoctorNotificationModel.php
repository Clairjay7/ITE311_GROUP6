<?php

namespace App\Models;

use CodeIgniter\Model;

class DoctorNotificationModel extends Model
{
    protected $table = 'doctor_notifications';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'doctor_id',
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
        'doctor_id' => 'required|integer',
        'type' => 'required|in_list[order_completed,order_updated,lab_request_pending,lab_result_ready,patient_assigned,vitals_recorded,system]',
        'title' => 'required|max_length[255]',
        'message' => 'required',
    ];

    /**
     * Get unread notifications for a doctor
     */
    public function getUnreadNotifications($doctorId, $limit = 10)
    {
        return $this->where('doctor_id', $doctorId)
            ->where('is_read', 0)
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id, $doctorId)
    {
        return $this->where('id', $id)
            ->where('doctor_id', $doctorId)
            ->update([
                'is_read' => 1,
                'read_at' => date('Y-m-d H:i:s')
            ]);
    }

    /**
     * Mark all notifications as read for a doctor
     */
    public function markAllAsRead($doctorId)
    {
        return $this->where('doctor_id', $doctorId)
            ->where('is_read', 0)
            ->update([
                'is_read' => 1,
                'read_at' => date('Y-m-d H:i:s')
            ]);
    }
}

