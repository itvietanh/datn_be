<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\IOFactory;

class RoomTypeExport
{
    protected $data;

    // Constructor nhận dữ liệu
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function export()
    {
        // Đường dẫn tới file template Excel
        $templatePath = storage_path('app/private/room-type-template.xlsx');

        // Load file Excel mẫu
        $spreadsheet = IOFactory::load($templatePath);

        // Lấy sheet đầu tiên
        $sheet = $spreadsheet->getActiveSheet();

        // Dòng bắt đầu ghi dữ liệu (giả sử dòng 8 là dòng tiêu đề)
        $currentRow = 8;

        // Ghi dữ liệu loại phòng
        foreach ($this->data as $index => $roomType) {
            $sheet->setCellValue("A$currentRow", $index + 1); // STT
            $sheet->setCellValue("B$currentRow", $roomType['type_name']); // Tên loại phòng
            $sheet->setCellValue("C$currentRow", $roomType['description']); // Mô tả
            $sheet->setCellValue("D$currentRow", number_format($roomType['price_per_hour'], 0, ',', '.')); // Giá/giờ
            $sheet->setCellValue("E$currentRow", number_format($roomType['price_per_day'], 0, ',', '.')); // Giá/ngày
            $sheet->setCellValue("F$currentRow", $roomType['created_at']->format('d-m-Y')); // Ngày tạo

            $currentRow++;
        }

        // Tạo writer để ghi file
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');

        // Ghi dữ liệu vào bộ nhớ tạm
        ob_start();
        $writer->save('php://output');
        return ob_get_clean();
    }
}
