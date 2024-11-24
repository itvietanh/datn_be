<?php

namespace App\Exports;

use App\Models\Districts;
use App\Models\Provinces;
use Maatwebsite\Excel\Concerns\FromCollection;
use PhpOffice\PhpSpreadsheet\IOFactory;

class StatisticalExport implements FromCollection
{
    protected $data;

    // Constructor nhận dữ liệu
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // Không cần thực hiện việc này nếu dùng file Excel mẫu.
        return collect([]);
    }

    public function template()
    {
        $templatePath = storage_path('app/private/thong-ke-template.xlsx');

        // Load file Excel mẫu
        $spreadsheet = IOFactory::load($templatePath);

        // Lấy sheet đầu tiên
        $sheet = $spreadsheet->getActiveSheet();

        // Tạo bản sao của sheet gốc
        $originalSheet = clone $sheet;

        // Thay thế các biến {{variable}} bằng dữ liệu thực tế
        $currentRow = 8; // Giả sử dòng đầu tiên là tiêu đề

        $dateFrom = $this->data['dateFrom'];
        $dateTo = $this->data['dateTo'];

        // Thay thế các key trước khi bắt đầu từ dòng thứ 8
        $replacements = [
            '{{dateFrom}}' => $dateFrom != null ? $dateFrom : '',
            '{{dateTo}}' => $dateTo != null ? $dateTo : '',
        ];

        foreach ($sheet->getRowIterator() as $row) {
            foreach ($row->getCellIterator() as $cell) {
                $cellValue = $cell->getValue();
                if ($cellValue) {
                    foreach ($replacements as $key => $replacement) {
                        if (strpos($cellValue, $key) !== false) {
                            $newCellValue = str_replace($key, $replacement, $cellValue);
                            $cell->setValue($newCellValue);
                        }
                    }
                }
            }
        }

        // Thay thế các biến {{variable}} bằng dữ liệu thực tế từ dòng thứ 8
        foreach ($this->data['statistical'] as $index => $value) {
            $replacements = [
                '{{stt}}' => $index + 1,
                '{{fullName}}' => $value->fullName,
                '{{phoneNumber}}' => $value->phoneNumber,
                '{{idNumber}}' => $value->idNumber,
                '{{hotelName}}' => $value->hotelName,
                '{{roomNumber}}' => $value->roomnumber,
                '{{transitionDate}}' => \DateTime::createFromFormat('Y-m-d H:i:s', $value->transitionDate)->format('d-m-Y H:i:s'),
                '{{totalAmount}}' => number_format($value->totalAmount, 0, ',', '.') . ' VNĐ',
            ];

            foreach ($originalSheet->getRowIterator() as $row) {
                foreach ($row->getCellIterator() as $cell) {
                    $cellValue = $cell->getValue();
                    if ($cellValue) {
                        foreach ($replacements as $key => $replacement) {
                            if (strpos($cellValue, $key) !== false) {
                                $newCellValue = str_replace($key, $replacement, $cellValue);
                                $sheet->getCell($cell->getColumn() . $currentRow)->setValue($newCellValue);
                            }
                        }
                    }
                }
            }

            // Sao chép định dạng từ dòng thứ 8 sang dòng hiện tại
            foreach ($sheet->getRowIterator(8)->current()->getCellIterator() as $cell) {
                $column = $cell->getColumn();
                $sheet->duplicateStyle($sheet->getStyle($column . '8'), $column . $currentRow);
            }

            // Sao chép độ rộng cột
            foreach ($sheet->getColumnIterator() as $column) {
                $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
            }

            // Sao chép chiều cao hàng
            $sheet->getRowDimension($currentRow)->setRowHeight($sheet->getRowDimension(8)->getRowHeight());

            // Sao chép các ô đã merge
            foreach ($sheet->getMergeCells() as $mergeCell) {
                if (strpos($mergeCell, '8') !== false) {
                    $newMergeCell = str_replace('8', $currentRow, $mergeCell);
                    $sheet->mergeCells($newMergeCell);
                }
            }

            $currentRow++;
        }

        // Ghi file ra bộ nhớ tạm
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        ob_start();
        $writer->save('php://output');
        return ob_get_clean();
    }
}
