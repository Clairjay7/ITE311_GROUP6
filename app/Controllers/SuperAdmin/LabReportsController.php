<?php

namespace App\Controllers\SuperAdmin;

use App\Controllers\BaseController;
use App\Models\LabReportModel;
use App\Models\TestModel;
use App\Models\EquipmentModel;

class LabReportsController extends BaseController
{
    protected LabReportModel $labReportModel;
    protected TestModel $testModel;
    protected EquipmentModel $equipmentModel;

    protected array $reportTypes = [
        'test_summary' => 'Test Result Summary',
        'performance' => 'Performance Analytics',
        'quality' => 'Quality Metrics',
        'turnaround' => 'Turnaround Analysis',
    ];

    public function __construct()
    {
        $this->labReportModel = new LabReportModel();
        $this->testModel = new TestModel();
        $this->equipmentModel = new EquipmentModel();
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
            'type' => $this->request->getGet('type'),
            'period_start' => $this->request->getGet('period_start'),
            'period_end' => $this->request->getGet('period_end'),
            'search' => $this->request->getGet('search'),
        ];

        $reports = $this->labReportModel->getReports(array_filter($filters));
        $enrichedReports = array_map(function (array $report) {
            $report['report_type_label'] = $this->reportTypes[$report['report_type']] ?? ucfirst(str_replace('_', ' ', $report['report_type']));
            $report['period_label'] = $this->formatPeriod($report['report_period_start'], $report['report_period_end']);
            $report['created_at_formatted'] = !empty($report['created_at']) ? date('M d, Y h:i A', strtotime($report['created_at'])) : '—';
            $report['generated_by_label'] = $report['generated_by'] ?: 'Super Admin';
            return $report;
        }, $reports);

        return view('SuperAdmin/lab_reports/index', [
            'title' => 'Lab Reports & Analytics',
            'reports' => $enrichedReports,
            'filters' => $filters,
            'reportTypes' => $this->reportTypes,
        ]);
    }

    public function generate($type = null)
    {
        $isAjax = $this->request->isAJAX() || str_contains($this->request->getHeaderLine('accept'), 'application/json');

        if ($redirect = $this->ensureSuperAdmin()) {
            if ($isAjax) {
                return $this->respondError('Unauthorized access.', 401);
            }
            return $redirect;
        }

        $reportType = $type ?? $this->request->getPost('report_type');
        if (!$reportType || !isset($this->reportTypes[$reportType])) {
            if ($isAjax) {
                return $this->respondError('Invalid report type selected.', 422);
            }
            return redirect()->back()->with('error', 'Invalid report type selected.');
        }

        $periodStart = $this->request->getPost('period_start');
        $periodEnd = $this->request->getPost('period_end');

        $metrics = $this->generateMetrics($reportType, $periodStart, $periodEnd);
        $description = $this->reportTypes[$reportType] . ' for ' . ($periodStart && $periodEnd ? $this->formatPeriod($periodStart, $periodEnd) : 'entire period');
        $generatedBy = session()->get('name') ?: 'Super Admin';

        $data = [
            'report_type' => $reportType,
            'description' => $description,
            'generated_by' => $generatedBy,
            'report_data' => $metrics,
            'report_period_start' => $periodStart ?: null,
            'report_period_end' => $periodEnd ?: null,
        ];

        $this->labReportModel->insert($data);
        $reportId = $this->labReportModel->getInsertID();
        $record = $this->labReportModel->find($reportId);
        $record['report_type_label'] = $this->reportTypes[$record['report_type']] ?? ucfirst(str_replace('_', ' ', $record['report_type']));
        $record['period_label'] = $this->formatPeriod($record['report_period_start'], $record['report_period_end']);
        $record['created_at_formatted'] = !empty($record['created_at']) ? date('M d, Y h:i A', strtotime($record['created_at'])) : '—';
        $record['generated_by_label'] = $record['generated_by'] ?: 'Super Admin';

        if ($isAjax) {
            return $this->respondSuccess('Report generated successfully.', $record);
        }

        return redirect()->to('super-admin/lab-reports')->with('message', 'Report generated successfully.');
    }

    private function generateMetrics(string $type, ?string $periodStart, ?string $periodEnd): array
    {
        $tests = $this->collectTests($periodStart, $periodEnd);
        $equipment = $this->collectEquipment();

        switch ($type) {
            case 'test_summary':
                return $this->buildTestSummary($tests);
            case 'performance':
                return $this->buildPerformanceMetrics($tests);
            case 'quality':
                return $this->buildQualityMetrics($tests, $equipment);
            case 'turnaround':
                return $this->buildTurnaroundMetrics($tests);
            default:
                return [];
        }
    }

    private function collectTests(?string $start, ?string $end): array
    {
        $filters = [];
        if ($start) {
            $filters['start_date'] = $start;
        }
        if ($end) {
            $filters['end_date'] = $end;
        }

        $builder = $this->testModel->builder();
        if ($start) {
            $builder->where('tests.created_at >=', $start . ' 00:00:00');
        }
        if ($end) {
            $builder->where('tests.created_at <=', $end . ' 23:59:59');
        }

        return $builder->select('tests.*, patients.first_name, patients.last_name')
            ->join('patients', 'patients.id = tests.patient_id', 'left')
            ->get()
            ->getResultArray();
    }

    private function collectEquipment(): array
    {
        return $this->equipmentModel->findAll();
    }

    private function buildTestSummary(array $tests): array
    {
        $total = count($tests);
        $byStatus = [];
        $byType = [];

        foreach ($tests as $test) {
            $status = $test['status'] ?? 'pending';
            $type = $test['test_type'] ?? 'Unspecified';
            $byStatus[$status] = ($byStatus[$status] ?? 0) + 1;
            $byType[$type] = ($byType[$type] ?? 0) + 1;
        }

        return [
            'total_tests' => $total,
            'status_breakdown' => $byStatus,
            'type_distribution' => $byType,
            'generated_at' => date('c'),
        ];
    }

    private function buildPerformanceMetrics(array $tests): array
    {
        $completed = array_filter($tests, fn($test) => ($test['status'] ?? '') === 'completed');
        $inProgress = array_filter($tests, fn($test) => ($test['status'] ?? '') === 'in_progress');
        $pending = array_filter($tests, fn($test) => ($test['status'] ?? '') === 'pending');

        return [
            'counts' => [
                'completed' => count($completed),
                'in_progress' => count($inProgress),
                'pending' => count($pending),
            ],
            'completion_rate' => count($tests) > 0 ? round((count($completed) / count($tests)) * 100, 2) : 0,
            'generated_at' => date('c'),
        ];
    }

    private function buildQualityMetrics(array $tests, array $equipment): array
    {
        $qualityChecks = array_map(fn($test) => $test['quality_check'] ?? 'Not Checked', $tests);
        $qcCounts = array_count_values($qualityChecks);

        $equipmentStatus = [];
        foreach ($equipment as $item) {
            $status = $item['status'] ?? 'available';
            $equipmentStatus[$status] = ($equipmentStatus[$status] ?? 0) + 1;
        }

        return [
            'quality_checks' => $qcCounts,
            'equipment_status' => $equipmentStatus,
            'generated_at' => date('c'),
        ];
    }

    private function buildTurnaroundMetrics(array $tests): array
    {
        $turnaroundTimes = [];
        foreach ($tests as $test) {
            if (!empty($test['created_at']) && !empty($test['updated_at'])) {
                $start = strtotime($test['created_at']);
                $end = strtotime($test['updated_at']);
                if ($end && $start && $end >= $start) {
                    $turnaroundTimes[] = ($end - $start) / 3600; // hours
                }
            }
        }

        $average = !empty($turnaroundTimes) ? round(array_sum($turnaroundTimes) / count($turnaroundTimes), 2) : 0;
        sort($turnaroundTimes);
        $median = 0;
        $count = count($turnaroundTimes);
        if ($count > 0) {
            $middle = (int) floor(($count - 1) / 2);
            if ($count % 2) {
                $median = $turnaroundTimes[$middle];
            } else {
                $median = ($turnaroundTimes[$middle] + $turnaroundTimes[$middle + 1]) / 2;
            }
        }

        return [
            'average_turnaround_hours' => $average,
            'median_turnaround_hours' => round($median, 2),
            'sample_size' => $count,
            'generated_at' => date('c'),
        ];
    }

    private function formatPeriod(?string $start, ?string $end): string
    {
        if (!$start && !$end) {
            return 'All Time';
        }

        if ($start && $end) {
            return date('M d, Y', strtotime($start)) . ' — ' . date('M d, Y', strtotime($end));
        }

        if ($start) {
            return 'From ' . date('M d, Y', strtotime($start));
        }

        return 'Until ' . date('M d, Y', strtotime($end));
    }

    private function respondSuccess(string $message, ?array $record = null)
    {
        return $this->response->setJSON([
            'success' => true,
            'message' => $message,
            'report' => $record,
            'csrfToken' => csrf_token(),
            'csrfHash' => csrf_hash(),
        ]);
    }

    private function respondError(string $message, int $status = 400, array $errors = [])
    {
        return $this->response->setStatusCode($status)->setJSON([
            'success' => false,
            'error' => $message,
            'errors' => $errors,
            'csrfToken' => csrf_token(),
            'csrfHash' => csrf_hash(),
        ]);
    }
}
