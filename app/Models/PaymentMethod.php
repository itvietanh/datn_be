<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PaymentMethod extends Model
{
    // Đảm bảo rằng 'id' là khóa chính tự động tăng
    protected $primaryKey = 'id';
    public $incrementing = true;

    // Cập nhật tên bảng nếu bảng trong cơ sở dữ liệu của bạn là 'payment_method'
    protected $table = 'payment_method'; // Thêm dòng này nếu bảng là 'payment_method'

    // Tạo UUID tự động khi bản ghi được tạo
    protected static function booted()
    {
        static::creating(function ($paymentMethod) {
            if (!$paymentMethod->uuid) {
                $paymentMethod->uuid = (string) Str::uuid(); // Tạo UUID nếu chưa có
            }
        });
    }

    protected $fillable = ['name', 'pr_code', 'description', 'uuid'];
}
