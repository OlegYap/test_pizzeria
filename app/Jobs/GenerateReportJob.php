<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class GenerateReportJob implements ShouldQueue
{
    use Queueable;
    use InteractsWithQueue;
    use SerializesModels;
    use Dispatchable;

    public string $reportId;

    /**
     * Create a new job instance.
     */
    public function __construct(string $reportId)
    {
        $this->reportId = $reportId;
    }

    /**
     * Execute the job.
     * @throws \JsonException
     */
    public function handle(): void
    {
        Storage::disk('local')->makeDirectory('reports');
        $filePath = "reports/{$this->reportId}.jsonl";

        $orders = Order::with(['user', 'orderProducts.product'])->get();

        $lines = [];

        foreach ($orders as $order) {
            foreach ($order->orderProducts as $orderProduct) {
                $lines[] = json_encode([
                    'product_name' => $orderProduct->product->name,
                    'price' => $orderProduct->price,
                    'amount' => $orderProduct->quantity,
                    'user' => [
                        'id' => $order->user->id,
                        'email' => $order->user->email
                    ]
                ], JSON_THROW_ON_ERROR);
            }
        }

        Storage::disk('local')->put($filePath, implode("\n", $lines));
    }
}
