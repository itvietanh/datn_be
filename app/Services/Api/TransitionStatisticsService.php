<?php

namespace App\Services\Api;

use App\Models\Transition;
use App\Services\BaseService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TransitionStatisticsService extends BaseService
{
    public function __construct()
    {
        $this->model = new Transition();
    }

    // Lấy tất cả thống kê giao dịch
    public function getAllStatistics()
    {
        $totalTransactions = $this->getTotalTransactions();
        $newTransactionsThisMonth = $this->getNewTransactionsThisMonth();
        $activeTransactions = $this->getActiveTransactions();
        $inactiveTransactions = $this->getInactiveTransactions();
        $totalAmount = $this->getTotalAmount(); // Tổng số tiền

        return [
            'total_transactions' => $totalTransactions,
            'new_transactions_this_month' => $newTransactionsThisMonth,
            'active_transactions' => $activeTransactions,
            'inactive_transactions' => $inactiveTransactions,
            'total_amount' => $totalAmount // Thêm tổng số tiền vào thống kê
        ];
    }

    // Tổng số giao dịch
    public function getTotalTransactions()
    {
        return $this->model->count();
    }

    // Giao dịch mới trong tháng hiện tại
    public function getNewTransactionsThisMonth()
    {
        return $this->model->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();
    }

    // Giao dịch đang hoạt động (theo payment_status = 1)
    public function getActiveTransactions()
    {
        return $this->model->where('payment_status', 1)->count();
    }

    // Giao dịch không hoạt động (theo payment_status = 0)
    public function getInactiveTransactions()
    {
        return $this->model->where('payment_status', 0)->count();
    }

    // Tổng số tiền của tất cả giao dịch
    public function getTotalAmount()
    {
        return $this->model->sum('total_amout');
    }
    // Lấy giao dịch theo guest_id
    public function getTransactionsByGuest($guest_id)
    {
        return $this->model->where('guest_id', $guest_id)->get();
    }

    // Lấy giao dịch theo ngày
    public function getTransactionsByDate($date)
    {
        return $this->model->whereDate('transition_date', $date)->get();
    }

    // Tổng số tiền của giao dịch theo ngày
    public function getTotalAmountByDate($date)
    {
        return $this->model->whereDate('transition_date', $date)->sum('total_amout'); // Sửa thành 'total_amount' nếu cần
    }

    /**Mẫu */

    public function renderDataStatisticalTrans($req)
    {
        $totalTrans = $this->getTotalTransactions();
        $statistical = $this->getTransDate($req);
        $data = [
            'statistical' => $statistical,
            'totalTrans' => $totalTrans
        ];
        return $data;
    }

    public function getTransDate($req)
    {
        $params = $req->all();
        $dateFrom = \DateTime::createFromFormat('Ymd', $params['dateFrom'])->format('Y-m-d');
        $dateTo = \DateTime::createFromFormat('Ymd', $params['dateTo'])->format('Y-m-d');
        $query = DB::table('transition')
            ->select(
                'guest.name as fullName',
                'guest.phone_number as phoneNumber',
                'guest.id_number as idNumber',
                'hotel.province_code as provinceCode',
                'hotel.district_code as districtCode',
                'hotel.name as hotelName',
                DB::raw("concat('Phòng ', room.room_number) as roomNumber"),
                'transition.transition_date as transitionDate',
                'room_using.total_amount as totalAmount'
            )
            ->join('guest', 'guest.id', '=', 'transition.guest_id')
            ->join('room_using', 'room_using.trans_id', '=', 'transition.id')
            ->leftJoin('room', 'room.id', '=', 'room_using.room_id')
            ->join('hotel', 'hotel.id', '=', 'room.hotel_id')
            ->whereBetween(DB::raw("transition.transition_date::date"), [$dateFrom, $dateTo]);
        $data = $query->get();
        return $data;
    }
}
