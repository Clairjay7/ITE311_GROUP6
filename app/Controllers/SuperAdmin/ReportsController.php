<?php

namespace App\Controllers\SuperAdmin;

use App\Controllers\BaseController;
use App\Models\ReportModel;
use App\Models\InventoryModel;
use App\Models\PrescriptionModel;
use CodeIgniter\I18n\Time;

class ReportsController extends BaseController
{
    protected ReportModel $reportModel;
    protected InventoryModel $inventoryModel;
    protected PrescriptionModel $prescriptionModel;
    protected array $reportTypes = [
        'dispensing' => 'Dispensing Performance',
        'usage' => 'Medication Usage',
        'inventory' => 'Inventory Status',
        'compliance' => 'Compliance Tracking',
    ];

    public function __construct()
    {
        $this->reportModel = new ReportModel();
        $this->inventoryModel = new InventoryModel();
        $this->prescriptionModel = new PrescriptionModel();
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
            'from' => $this->request->getGet('from'),
            'to' => $this->request->getGet('to'),
        ];

        $reports = $this->reportModel->getReports(array_filter($filters));

        return view('SuperAdmin/reports/index', [
            'title' => 'Reports & Analytics',
            'reports' => $reports,
            'reportTypes' => $this->reportTypes,
            'filters' => $filters,
        ]);
    }

    public function generate()
    {
        $method = strtolower($this->request->getMethod());
        $isAjax = $this->request->isAJAX() || str_contains($this->request->getHeaderLine('accept'), 'application/json');

        if ($redirect = $this->ensureSuperAdmin()) {
            if ($isAjax) {
                return $this->response->setStatusCode(401)->setJSON([
                    'success' => false,
                    'error' => 'Unauthorized access.',
                    'csrfToken' => csrf_token(),
                    'csrfHash' => csrf_hash(),
                ]);
            }
            return $redirect;
        }

        if (!in_array($method, ['post'], true)) {
            if ($isAjax) {
                return $this->response->setStatusCode(405)->setJSON([
                    'success' => false,
                    'error' => 'Invalid request method.',
                    'csrfToken' => csrf_token(),
                    'csrfHash' => csrf_hash(),
                ]);
            }
            return redirect()->to('super-admin/reports');
        }

        $type = $this->request->getPost('report_type');
        $dateFrom = $this->request->getPost('from_date');
        $dateTo = $this->request->getPost('to_date');

        if (!isset($this->reportTypes[$type])) {
            $message = 'Invalid report type selected.';
            if ($isAjax) {
                return $this->response->setStatusCode(422)->setJSON([
                    'success' => false,
                    'error' => $message,
                    'csrfToken' => csrf_token(),
                    'csrfHash' => csrf_hash(),
                ]);
            }
            return redirect()->back()->with('error', $message);
        }

        $filters = array_filter([
            'from' => $dateFrom,
            'to' => $dateTo,
        ]);

        $reportData = $this->buildReportData($type, $filters);
        $summary = $this->buildSummary($type, $reportData);

        $this->reportModel->insert([
            'report_type' => $type,
            'title' => $this->reportTypes[$type] . ' Report',
            'description' => $summary,
            'generated_by' => session()->get('username') ?? 'Super Admin',
            'filters' => $filters,
            'report_data' => $reportData,
            'summary' => $summary,
        ]);

        $newReportId = $this->reportModel->getInsertID();
        $newReport = $this->reportModel->find($newReportId);

        if ($isAjax) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Report generated successfully.',
                'report' => $newReport,
                'reportTypeLabel' => $this->reportTypes[$type] ?? ucfirst($type),
                'csrfToken' => csrf_token(),
                'csrfHash' => csrf_hash(),
            ]);
        }

        return redirect()->to('super-admin/reports')->with('message', 'Report generated successfully.');
    }

    protected function buildReportData(string $type, array $filters = []): array
    {
        $data = [];
        $from = $filters['from'] ?? null;
        $to = $filters['to'] ?? null;

        switch ($type) {
            case 'dispensing':
                $builder = $this->prescriptionModel->builder();
                if ($from) {
                    $builder->where('DATE(created_at) >=', $from);
                }
                if ($to) {
                    $builder->where('DATE(created_at) <=', $to);
                }
                $prescriptions = $builder->select('status, COUNT(*) as total')->groupBy('status')->get()->getResultArray();

                $topMedicines = $this->prescriptionModel
                    ->select('medication_name, SUM(quantity) as total_quantity')
                    ->groupBy('medication_name')
                    ->orderBy('total_quantity', 'DESC')
                    ->limit(5)
                    ->findAll();

                $data = [
                    'status_breakdown' => $prescriptions,
                    'top_medicines' => $topMedicines,
                ];
                break;

            case 'usage':
                $usageBuilder = $this->prescriptionModel->builder();
                if ($from) {
                    $usageBuilder->where('DATE(created_at) >=', $from);
                }
                if ($to) {
                    $usageBuilder->where('DATE(created_at) <=', $to);
                }

                $data = [
                    'total_prescriptions' => (int) $usageBuilder->countAllResults(false),
                    'quantity_per_medicine' => $this->prescriptionModel
                        ->select('medication_name, SUM(quantity) as total_quantity')
                        ->groupBy('medication_name')
                        ->orderBy('total_quantity', 'DESC')
                        ->findAll(),
                ];
                break;

            case 'inventory':
                $inventoryItems = $this->inventoryModel
                    ->select('status, COUNT(*) as total_items')
                    ->groupBy('status')
                    ->findAll();

                $lowStock = $this->inventoryModel
                    ->where('quantity <=', 5)
                    ->orderBy('quantity', 'ASC')
                    ->findAll(10);

                $data = [
                    'status_breakdown' => $inventoryItems,
                    'low_stock_items' => $lowStock,
                    'total_items' => $this->inventoryModel->countAll(),
                ];
                break;

            case 'compliance':
                $complianceBuilder = $this->prescriptionModel->builder();
                if ($from) {
                    $complianceBuilder->where('DATE(created_at) >=', $from);
                }
                if ($to) {
                    $complianceBuilder->where('DATE(created_at) <=', $to);
                }

                $total = (int) $complianceBuilder->countAllResults(false);
                $dispensed = (int) $this->prescriptionModel
                    ->where('status', 'dispensed')
                    ->countAllResults();
                $pending = (int) $this->prescriptionModel
                    ->where('status', 'pending')
                    ->countAllResults();

                $complianceRate = $total > 0 ? round(($dispensed / $total) * 100, 2) : 0;

                $data = [
                    'total_prescriptions' => $total,
                    'dispensed' => $dispensed,
                    'pending' => $pending,
                    'compliance_rate' => $complianceRate,
                    'generated_at' => Time::now()->toDateTimeString(),
                ];
                break;

            default:
                $data = ['message' => 'No data available for this report type yet.'];
        }

        return $data;
    }

    protected function buildSummary(string $type, array $data): string
    {
        return match ($type) {
            'dispensing' => 'Dispensing report generated with status breakdown and top medicines.',
            'usage' => 'Usage report summarizing prescription counts and medicine demand.',
            'inventory' => 'Inventory report highlighting stock status and low inventory items.',
            'compliance' => 'Compliance report showing prescription fulfillment status.',
            default => 'Report generated.',
        };
    }
}
