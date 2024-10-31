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
        $data = [
            'total_transactions' => $totalTransactions
        ];
        return $this->responseSuccess($data);
        
    }

    // Phương thức để lấy số giao dịch mới trong tháng
    public function newTransactionsThisMonth()
    {
        $newTransactions = $this->transitionStatisticsService->getNewTransactionsThisMonth();
        $data = [
            'new_transactions_this_month' => $newTransactions
        ];
        return $this->responseSuccess($data);
      
    }

    // Phương thức để lấy số giao dịch đang hoạt động
    public function activeTransactions()
    {
        $activeTransactions = $this->transitionStatisticsService->getActiveTransactions();
        $data = [
            'active_transactions' => $activeTransactions
        ];
        return $this->responseSuccess($data);
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
        $data = [
            'transactions' => $transactions
        ];
        return $this->responseSuccess($data);
    }

    // Phương thức để lấy tổng số tiền theo ngày
    public function totalAmountByDate($date)
    {
        $totalAmount = $this->transitionStatisticsService->getTotalAmountByDate($date);
        $data = [
            'total_amount' => $totalAmount
        ];
        return $this->responseSuccess($data);
    }
}
