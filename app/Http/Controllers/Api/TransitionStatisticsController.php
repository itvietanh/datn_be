<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Services\Api\TransitionStatisticsService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StatisticalExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class TransitionStatisticsController extends BaseController
{
    protected $transitionStatisticsService;

    public function __construct(TransitionStatisticsService $transitionStatisticsService)
    {
        $this->transitionStatisticsService = $transitionStatisticsService;
    }

    // Phương thức để lấy tổng số giao dịch
    // public function totalTransactions()
    // {
    //     $totalTransactions = $this->transitionStatisticsService->getTotalTransactions();
    //     $data = [
    //         'total_transactions' => $totalTransactions
    //     ];
    //     return $this->responseSuccess($data);
    // }

    // // Phương thức để lấy số giao dịch mới trong tháng
    // public function newTransactionsThisMonth()
    // {
    //     $newTransactions = $this->transitionStatisticsService->getNewTransactionsThisMonth();
    //     $data = [
    //         'new_transactions_this_month' => $newTransactions
    //     ];
    //     return $this->responseSuccess($data);
    // }

    // // Phương thức để lấy số giao dịch đang hoạt động
    // public function activeTransactions()
    // {
    //     $activeTransactions = $this->transitionStatisticsService->getActiveTransactions();
    //     $data = [
    //         'active_transactions' => $activeTransactions
    //     ];
    //     return $this->responseSuccess($data);
    // }

    // // Phương thức để lấy tất cả thống kê giao dịch
    // public function allStatistics()
    // {
    //     $statistics = $this->transitionStatisticsService->getAllStatistics();

    //     return response()->json($statistics);
    // }

    // public function transactionsByGuest(Request $req)
    // {
    //     $transactions = $this->transitionStatisticsService->getTransactionsByGuest($req->guest_id);
    //     if (!$transactions) {
    //         $this->response404();
    //     }
    //     return $this->responseSuccess($transactions);
    // }

    // // Phương thức để lấy số giao dịch theo ngày
    // public function transactionsByDate($date)
    // {
    //     $transactions = $this->transitionStatisticsService->getTransactionsByDate($date);
    //     $data = [
    //         'transactions' => $transactions
    //     ];
    //     return $this->responseSuccess($data);
    // }

    // // Phương thức để lấy tổng số tiền theo ngày
    // public function totalAmountByDate($date)
    // {
    //     $totalAmount = $this->transitionStatisticsService->getTotalAmountByDate($date);
    //     $data = [
    //         'total_amount' => $totalAmount
    //     ];
    //     return $this->responseSuccess($data);
    // }

    //////////////////
    // public function getRoomUsingTotalByDate()
    // {
    //     try {
    //         $roomUsings = DB::table('room_using')
    //             ->select(
    //                 DB::raw('DATE(check_in) as date'),
    //                 DB::raw('SUM(total_amount) as total_amount')
    //             )
    //             ->groupBy('date')
    //             ->orderBy('date', 'desc')
    //             ->get();

    //         // Kiểm tra xem dữ liệu có được trả về không
    //         if ($roomUsings->isEmpty()) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'No data found for the given date range.'
    //             ], 404);
    //         }

    //         // Chuyển dữ liệu thành dạng cần thiết
    //         $data = $roomUsings->map(function ($roomUsing) {
    //             return [
    //                 'date' => $roomUsing->date,
    //                 'total_amount' => $roomUsing->total_amount,
    //             ];
    //         });

    //         return response()->json([
    //             'success' => true,
    //             'data' => $data
    //         ]);
    //     } catch (\Exception $e) {
    //         // Log lỗi nếu có vấn đề trong quá trình truy vấn
    //         // \Log::error('Error fetching room using data: ' . $e->getMessage());

    //         return response()->json([
    //             'success' => false,
    //             'message' => 'An error occurred while fetching data.'
    //         ], 500);
    //     }
    // }
    public function getTransactionsByDate(Request $request)
    {
        try {
            $start_date = $request->input('dateFrom');
            $end_date = $request->input('dateTo');

            $transactions = DB::table('room_using')
                ->select(DB::raw('DATE(check_in) as date'), DB::raw('SUM(total_amount) as total_amount'))
                ->whereBetween('check_in', [$start_date, $end_date])
                ->groupBy('date')
                ->orderBy('date', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $transactions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'errors' => [
                    'message' => 'Lỗi hệ thống, vui lòng thử lại'
                ]
            ], 500);
        }
    }

    /**
     * Mẫu
     */

    public function transactionsStatistical(Request $req)
    {
        $response = $this->transitionStatisticsService->renderDataStatisticalTrans($req);
        return $this->responseSuccess($response);
    }

    public function exportExcelStatistical(Request $req)
    {
        $data = $this->transitionStatisticsService->renderDataStatisticalTrans($req);
        $data['dateFrom'] = \DateTime::createFromFormat('Ymd', $req->dateFrom)->format('d-m-Y');
        $data['dateTo'] = \DateTime::createFromFormat('Ymd', $req->dateTo)->format('d-m-Y');
        $export = new StatisticalExport($data);

        $fileContent = $export->template();

        // Trả về file Excel
        return response($fileContent)
            ->header('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->header('Content-Disposition', 'attachment; filename="thong-ke-giao-dich.xlsx"');
    }
}
