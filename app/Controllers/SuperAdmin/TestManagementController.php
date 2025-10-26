<?php

namespace App\Controllers\SuperAdmin;

use App\Controllers\BaseController;
use App\Models\TestModel;
use App\Models\PatientModel;

class TestManagementController extends BaseController
{
    protected TestModel $testModel;
    protected PatientModel $patientModel;

    protected array $statusOptions = [
        'pending' => 'Pending',
        'in_progress' => 'In Progress',
        'completed' => 'Completed',
    ];

    protected array $qualityOptions = [
        'Not Checked',
        'Passed',
        'Requires Review',
        'Failed',
    ];

    public function __construct()
    {
        $this->testModel = new TestModel();
        $this->patientModel = new PatientModel();
    }

    protected function ensureSuperAdmin()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'superadmin') {
            return redirect()->to('/login');
        }

        return null;
    }

    public function index()
    {
        if ($redirect = $this->ensureSuperAdmin()) {
            return $redirect;
        }

        $filters = [
            'status' => $this->request->getGet('status'),
            'quality_check' => $this->request->getGet('quality'),
            'search' => $this->request->getGet('search'),
        ];

        $tests = $this->testModel->getTests(array_filter($filters));
        $patients = $this->patientModel
            ->select('id, patient_id, first_name, last_name')
            ->orderBy('last_name', 'ASC')
            ->findAll();

        return view('SuperAdmin/tests/index', [
            'title' => 'Test Management',
            'tests' => $tests,
            'patients' => $patients,
            'statusOptions' => $this->statusOptions,
            'qualityOptions' => $this->qualityOptions,
            'filters' => $filters,
        ]);
    }

    public function store()
    {
        $isAjax = $this->request->isAJAX() || str_contains($this->request->getHeaderLine('accept'), 'application/json');

        if ($redirect = $this->ensureSuperAdmin()) {
            if ($isAjax) {
                return $this->response->setStatusCode(401)->setJSON($this->formatError('Unauthorized access.'));
            }
            return $redirect;
        }

        if (strtolower($this->request->getMethod()) !== 'post') {
            if ($isAjax) {
                return $this->response->setStatusCode(405)->setJSON($this->formatError('Invalid request method.'));
            }
            return redirect()->to('super-admin/tests');
        }

        $validationRules = [
            'patient_id' => 'required|is_natural_no_zero',
            'test_name' => 'required|min_length[3]',
            'test_type' => 'permit_empty|max_length[100]',
            'sample_id' => 'permit_empty|max_length[100]',
            'requested_by' => 'permit_empty|max_length[150]',
            'status' => 'required|in_list[pending,in_progress,completed]',
            'quality_check' => 'permit_empty|max_length[100]',
            'result' => 'permit_empty',
        ];

        if (!$this->validate($validationRules)) {
            $errors = $this->validator?->getErrors() ?? ['validation' => 'Invalid input'];
            if ($isAjax) {
                return $this->response->setStatusCode(422)->setJSON($this->formatError('Please correct the highlighted fields.', $errors));
            }
            return redirect()->back()->withInput()->with('error', 'Please correct the highlighted fields.');
        }

        $data = $this->request->getPost([
            'patient_id',
            'test_name',
            'test_type',
            'sample_id',
            'requested_by',
            'status',
            'quality_check',
            'result',
        ]);

        $data['quality_check'] = $data['quality_check'] ?: 'Not Checked';

        $this->testModel->insert($data);
        $newId = $this->testModel->getInsertID();
        $test = $this->fetchTestWithPatient($newId);

        if ($isAjax) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Test created successfully.',
                'test' => $test,
                'formattedTest' => $this->formatTestForResponse($test),
                'csrfToken' => csrf_token(),
                'csrfHash' => csrf_hash(),
            ]);
        }

        return redirect()->to('super-admin/tests')->with('message', 'Test created successfully.');
    }

    public function update($id = null)
    {
        $isAjax = $this->request->isAJAX() || str_contains($this->request->getHeaderLine('accept'), 'application/json');

        if ($redirect = $this->ensureSuperAdmin()) {
            if ($isAjax) {
                return $this->response->setStatusCode(401)->setJSON($this->formatError('Unauthorized access.'));
            }
            return $redirect;
        }

        if (!$id || !($existing = $this->testModel->find($id))) {
            if ($isAjax) {
                return $this->response->setStatusCode(404)->setJSON($this->formatError('Test record not found.'));
            }
            return redirect()->to('super-admin/tests')->with('error', 'Test record not found.');
        }

        if (strtolower($this->request->getMethod()) !== 'post') {
            if ($isAjax) {
                return $this->response->setStatusCode(405)->setJSON($this->formatError('Invalid request method.'));
            }
            return redirect()->to('super-admin/tests');
        }

        $validationRules = [
            'status' => 'required|in_list[pending,in_progress,completed]',
            'quality_check' => 'permit_empty|max_length[100]',
            'result' => 'permit_empty',
        ];

        if (!$this->validate($validationRules)) {
            $errors = $this->validator?->getErrors() ?? ['validation' => 'Invalid input'];
            if ($isAjax) {
                return $this->response->setStatusCode(422)->setJSON($this->formatError('Please correct the highlighted fields.', $errors));
            }
            return redirect()->back()->withInput()->with('error', 'Please correct the highlighted fields.');
        }

        $payload = $this->request->getPost([
            'status',
            'quality_check',
            'result',
        ]);

        $payload['quality_check'] = $payload['quality_check'] ?: 'Not Checked';

        $this->testModel->update($id, $payload);
        $test = $this->fetchTestWithPatient($id);

        if ($isAjax) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Test updated successfully.',
                'test' => $test,
                'formattedTest' => $this->formatTestForResponse($test),
                'csrfToken' => csrf_token(),
                'csrfHash' => csrf_hash(),
            ]);
        }

        return redirect()->to('super-admin/tests')->with('message', 'Test updated successfully.');
    }

    private function fetchTestWithPatient(int $id): ?array
    {
        return $this->testModel
            ->select('tests.*, patients.first_name, patients.last_name')
            ->join('patients', 'patients.id = tests.patient_id', 'left')
            ->where('tests.id', $id)
            ->first();
    }

    private function formatTestForResponse(?array $test): array
    {
        if (!$test) {
            return [];
        }

        $patientName = trim(($test['first_name'] ?? '') . ' ' . ($test['last_name'] ?? ''));
        $patientName = $patientName !== '' ? $patientName : 'Unknown Patient';

        return [
            'id' => $test['id'],
            'patient_name' => $patientName,
            'test_name' => $test['test_name'],
            'test_type' => $test['test_type'] ?? '—',
            'sample_id' => $test['sample_id'] ?? '—',
            'requested_by' => $test['requested_by'] ?? '—',
            'status' => $test['status'],
            'status_label' => $this->statusOptions[$test['status']] ?? ucfirst($test['status']),
            'quality_check' => $test['quality_check'] ?? 'Not Checked',
            'result' => $test['result'] ?? '',
            'created_at' => $test['created_at'] ?? null,
            'created_at_formatted' => !empty($test['created_at']) ? date('M d, Y h:i A', strtotime($test['created_at'])) : '—',
            'updated_at_formatted' => !empty($test['updated_at']) ? date('M d, Y h:i A', strtotime($test['updated_at'])) : '—',
        ];
    }

    private function formatError(string $message, array $errors = []): array
    {
        return [
            'success' => false,
            'error' => $message,
            'errors' => $errors,
            'csrfToken' => csrf_token(),
            'csrfHash' => csrf_hash(),
        ];
    }
}
