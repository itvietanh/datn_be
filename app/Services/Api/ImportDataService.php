<?php

namespace App\Services\Api;

use App\Services\BaseService;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

use App\Models\Guest;
use Carbon\Carbon;

use Ramsey\Uuid\Uuid;

class ImportDataService extends BaseService implements ToModel
{
    public function __construct() {}

    public function model(array $row)
    {
        if ($row[0] === "STT") {
            return null;
        }
        return $this->handleImportDataGuest($row);
    }

    public function handleImportDataGuest(array $row)
    {
        if (
            empty(trim($row[1])) &&
            empty(trim($row[2])) &&
            empty(trim($row[3])) &&
            empty(trim($row[4])) &&
            empty(trim($row[5])) &&
            empty(trim($row[6])) &&
            empty(trim($row[7])) &&
            empty(trim($row[8])) &&
            empty(trim($row[9])) &&
            empty(trim($row[10]))
        ) {
            return null;
        }

        return new Guest([
            'uuid' => str_replace('-', '', Uuid::uuid4()->toString()),
            'name'             => $row[1],         // Cột 1: Họ và tên
            'birth_date'       => $row[2],           // Cột 2: Ngày, tháng, năm sinh
            'gender'           => $row[3],                              // Cột 3: Giới tính
            'id_number'        => $row[4],                              // Cột 4: Số giấy tờ
            'phone_number'     => $row[5],        // Cột 5: Số điện thoại
            'province_id'      => (int) $row[6],                              // Cột 6: Tỉnh/TP
            'district_id'      => (int) $row[7],                              // Cột 7: Quận/Huyện
            'ward_id'          => (int) $row[8],                              // Cột 8: Phường/Xã
            'contact_details'  => $row[9],                              // Cột 9: Địa chỉ chi tiết
            'representative'   => !empty(trim($row[10])) && $row[10] !== '0',                              // Cột 10: Người đại diện (nếu có)
        ]);
    }

    private function validateName($name)
    {
        return preg_match("/^[\p{L}\s'-]+$/u", $name) ? $name : null;
    }

    private function formatDate($date)
    {
        return Carbon::createFromFormat('d/m/Y', $date) ?: null;
    }

    private function validatePhone($phone)
    {
        return preg_match('/^[0-9]{10,20}$/', $phone) ? $phone : null;
    }
}
