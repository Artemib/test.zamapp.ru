<?php

namespace App\Http\Requests\Megafon;

use App\Enums\CallConstants;
use App\Http\Requests\CallRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;

class HistoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return (new CallRequest())->rules(); // используем те же правила, что и в базовом запросе
    }

    protected function prepareForValidation(): void
    {
        $status = $this->input('status');
        $type   = $this->input('type');
        $datetime  = $this->input('start');

        $this->merge([
            'callid'          => $this->input('callid'),
            'datetime'        => is_string($datetime)
                ? Carbon::createFromFormat('Ymd\THis\Z', $datetime, 'UTC')
                : $datetime,
            'type'            => is_string($type) ? strtolower($type) : $type,
            'status'          => is_string($status) ? camel_to_snake($status) : $status,
            'client_phone'    => normalize_phone($this->input('phone')),
            'user_pbx'        => $this->input('user'),
            'diversion_phone' => normalize_phone($this->input('diversion')),
            'duration'        => $this->input('duration') ?? 0,
            'wait'            => $this->input('wait') ?? 0,
            'link_record_pbx' => $this->input('link'),
            'from_source_name'=> 'megafon',
        ]);
    }
}
