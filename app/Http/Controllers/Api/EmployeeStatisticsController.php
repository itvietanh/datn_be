<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Services\Api\EmployeeStatisticsService;
use App\Exports\StatisticalExport;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class EmployeeStatisticsController extends BaseController
{
    protected $employeeStatisticsService;

    public function __construct(EmployeeStatisticsService $employeeStatisticsService)
    {
        $this->employeeStatisticsService = $employeeStatisticsService;
    }

    /**
     * Lấy danh sách nhân viên theo khoảng ngày
     */
    public function getEmployeesByDate(Request $request)
    {
        try {
            $start_date = $request->input('dateFrom');
            $end_date = $request->input('dateTo');

            $employees = DB::table('employee')
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as total_employees'))
                ->whereBetween('created_at', [$start_date, $end_date])
                ->groupBy('date')
                ->orderBy('date', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $employees
            ]);
        } catch (\Exception $e) {
            // Log lỗi nếu có vấn đề trong quá trình truy vấn
            // \Log::error('Error fetching employee data: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching data.'
            ], 500);
        }
    }

    /**
     * Thống kê nhân viên
     */
    public function employeesStatistical(Request $req)
    {
        $response = $this->employeeStatisticsService->renderDataStatisticalEmployees($req);
        return $this->responseSuccess($response);
    }

    /**
     * Xuất thống kê nhân viên ra file Excel
     */
    public function exportExcelStatistical(Request $req)
    {
        $data = $this->employeeStatisticsService->renderDataStatisticalEmployees($req);
        $data['dateFrom'] = \DateTime::createFromFormat('Ymd', $req->dateFrom)->format('d-m-Y');
        $data['dateTo'] = \DateTime::createFromFormat('Ymd', $req->dateTo)->format('d-m-Y');
        $export = new StatisticalExport($data);

        $fileContent = $export->template();

        // Trả về file Excel
        return response($fileContent)
            ->header('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->header('Content-Disposition', 'attachment; filename="thong-ke-nhan-vien.xlsx"');
    }
}
