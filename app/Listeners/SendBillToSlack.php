<?php

namespace App\Listeners;

use App\Events\BillCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;


class SendBillToSlack
{
    use InteractsWithQueue;
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
     public function handle(BillCreated $event) : void
    {
        $bill = $event->bill->load(['services','products','pets','hotels']);

        $servicesText = "";
        foreach ($bill->services as $s) {
            $servicesText .= "• {$s->name} - " . number_format($s->price, 0, ',', '.') . "đ\n";
        }

        $hotelsText = "";
        foreach ($bill->hotels as $h) {
            $hotelsText .= "• {$h->name} - " . number_format($h->price, 0, ',', '.') . "đ\n";
        }

        $petsText = "";
        foreach ($bill->pets as $p) {
            $petsText .= "• {$p->name} x{$p->pivot->quantity} - " 
                       . number_format($p->price, 0, ',', '.') . "đ\n";
        }

        $productsText = "";
        foreach ($bill->products as $pr) {
            $productsText .= "• {$pr->name} x{$pr->pivot->quantity} - " 
                           . number_format($pr->price, 0, ',', '.') . "đ\n";
        }
        $webhookUrl = env('SLACK_WEBHOOK_URL');
        Http::post($webhookUrl, [
            "text" => "🐾 Đơn hàng mới từ *PetShop* \n"
                . "👤 Họ tên khách hàng: {$bill->customer_name}\n"
                . "📧 Email khách hàng: {$bill->customer_email}\n"
                . "📱 SĐT: {$bill->phone}\n"
                . "🏠 Địa chỉ: {$bill->ward}, {$bill->district}, {$bill->city}\n"
                . "💰 Tổng tiền: " . number_format($bill->total_price, 0, ',', '.') . " VNĐ\n"
                . "📝 Địa chỉ cụ thể: {$bill->description}\n\n"
                . "📋 *Chi tiết đơn hàng:*\n"
                . ($servicesText ? "🔹 Dịch vụ:\n$servicesText" : "")
                . ($hotelsText   ? "🏨 Khách sạn:\n$hotelsText" : "")
                . ($petsText     ? "🐕 Thú cưng:\n$petsText" : "")
                . ($productsText ? "🛒 Sản phẩm:\n$productsText" : "")
        ]);
    }
}
