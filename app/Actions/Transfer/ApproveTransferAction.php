<?php
namespace App\Actions\Transfer;

use App\Models\Transfer;
use App\Models\TransferDetail;
use App\Enums\StatusEnum;
use App\Notifications\TransferApproved;
use Illuminate\Support\Facades\DB;

class ApproveTransferAction
{
    public function execute(Transfer $transfer, array $details): void
    {
        DB::transaction(function () use ($transfer, $details) {
            $allApproved = true;
            $anyPartial = false;

            foreach ($details as $item) {
                $detail = TransferDetail::findOrFail($item['id']);
                $sent = $item['quantity_sent'];

                if ($sent > $detail->quantity_requested) {
                    throw new \Exception('Cantidad enviada no puede superar la solicitada.');
                }
                $detail->update(['quantity_sent' => $sent]);

                if ($sent == 0) $allApproved = false;
                if ($sent > 0 && $sent < $detail->quantity_requested) $anyPartial = true;
            }

            $status = StatusEnum::APPROVED;
            if (!$allApproved && !$anyPartial) $status = StatusEnum::REJECTED;
            elseif ($anyPartial) $status = StatusEnum::PARTIALLY_APPROVED;

            $transfer->update([
                'user_authorizes' => auth()->id(),
                'status' => $status,
            ]);

            event(new TransferApproved($transfer)); // Dispara evento
        });
    }
}
