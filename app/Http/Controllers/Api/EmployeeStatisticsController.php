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
    $data = DB::table('hotel as h')
        ->leftJoin('employee as e', 'h.id', '=', 'e.hotel_id')
        ->select('h.name as hotel_name', DB::raw('COUNT(e.id) as total_employees'))
        ->groupBy('h.name')
        ->get();

    return response()->json([
        'success' => true,
        'data' => $data
    ]);
}

    ///////
    // public function getEmployeeCountByHotel()
    // {
    //     try {
    //         // Thống kê số lượng nhân viên theo khách sạn
    //         $statistics = DB::table('employee')
    //             ->select('hotel_id', DB::raw('COUNT(*) as total_employees'))
    //             ->whereNull('deleted_at') // Chỉ tính nhân viên chưa bị xóa
    //             ->groupBy('hotel_id')
    //             ->get();

    //         return response()->json([
    //             'success' => true,
    //             'data' => $statistics,
    //         ], 200);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Error fetching data',
    //             'error' => $e->getMessage(),
    //         ], 500);
    //     }
    // }


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
