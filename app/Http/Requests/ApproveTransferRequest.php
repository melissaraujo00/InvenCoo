<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApproveTransferRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->hasRole('Administrador') &&
               $this->transfer->status === \App\Enums\StatusEnum::PENDING;
    }

    public function rules()
    {
        return [
            'details' => 'required|array',
            'details.*.id' => 'required|exists:transfer_details,id',
            'details.*.quantity_sent' => 'required|integer|min:0',
        ];
    }
}
