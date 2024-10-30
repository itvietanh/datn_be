<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Services\Api\TransitionStatisticsService;
use Illuminate\Http\Request;

class TransitionStatisticsController extends BaseController
{
    protected $transitionStatisticsService;

    public function __construct(TransitionStatisticsService $transitionStatisticsService)
    {
        $this->transitionStatisticsService = $transitionStatisticsService;
    }

    // Phương thức để lấy tổng số giao dịch
    public function totalTransactions()
    {
        $totalTransactions = $this->transitionStatisticsService->getTotalTransactions();
        return response()->json(['total_transactions' => $totalTransactions]);
    }

    // Phương thức để lấy số giao dịch mới trong tháng
    public function newTransactionsThisMonth()
    {
        $newTransactions = $this->transitionStatisticsService->getNewTransactionsThisMonth();
        return response()->json(['new_transactions_this_month' => $newTransactions]);
    }

    // Phương thức để lấy số giao dịch đang hoạt động
    public function activeTransactions()
    {
        $activeTransactions = $this->transitionStatisticsService->getActiveTransactions();
        return response()->json(['active_transactions' => $activeTransactions]);
    }

    // Phương thức để lấy tất cả thống kê giao dịch
    public function allStatistics()
    {
        $statistics = $this->transitionStatisticsService->getAllStatistics();
        return response()->json($statistics);
    }

    public function transactionsByGuest(Request $req)
    {
        $transactions = $this->transitionStatisticsService->getTransactionsByGuest($req->guest_id);
        if (!$transactions) {
            $this->response404();
        }
        return $this->responseSuccess($transactions);
    }

    // Phương thức để lấy số giao dịch theo ngày
    public function transactionsByDate($date)
    {
        $transactions = $this->transitionStatisticsService->getTransactionsByDate($date);
        return response()->json(['transactions' => $transactions]);
    }

    // Phương thức để lấy tổng số tiền theo ngày
    public function totalAmountByDate($date)
    {
        $totalAmount = $this->transitionStatisticsService->getTotalAmountByDate($date);
        return response()->json(['total_amount' => $totalAmount]);
    }
}
