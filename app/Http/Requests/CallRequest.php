<?php

namespace App\Http\Requests;

use App\Enums\CallConstants;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class CallRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'callid' => ['required', 'string', 'unique:calls'], // Уникальный ID звонка в ВАТС
            'datetime' => ['required', 'date'], // Дата и время звонка
            'type' => ['required', Rule::in(CallConstants::typeKeys())], // Тип звонка
            'status' => ['required', Rule::in(CallConstants::statusKeys())], // Статус звонка
            'client_phone' => ['required', 'regex:/^\d{10}$/'], // Номер телефона клиента
            'user_pbx' => ['required', 'string'], // Идентификатор пользователя ВАТС (необходим для сопоставления на стороне CRM)
            'diversion_phone' => ['required', 'regex:/^\d{10}$/'], // Номер телефона ВАТС, через который прошел вызов
            'duration' => 'integer', // Общая длительность звонка в секундах
            'wait' => 'integer', // Время ожидания ответа
            'link_record_pbx' => ['nullable', 'url'], // Ссылка на запись разговора в ВАТС
            'link_record_crm' => ['nullable', 'url'], // Ссылка на запись разговора на стороне CRM
            'transcribation' => ['nullable', 'string'], // Расшифровка разговора аудио в текст
            'from_source_name' => ['required', 'string'], // Название источника откуда пришёл звонок в CRM
        ];
    }


    protected function prepareForValidation()
    {
        $status = $this->input('status');
        $type = $this->input('type');
        $datetime = $this->input('datetime');

        $this->merge([
            'status'          => is_string($status) ? strtolower($status) : $status,
            'type'            => is_string($type) ? strtolower($type) : $type,
            'datetime'        => is_string($datetime)
                ? Carbon::createFromFormat('Ymd\THis\Z', $datetime, 'UTC')
                : $datetime,
            'client_phone'    => normalize_phone($this->input('client_phone')),
            'diversion_phone' => normalize_phone($this->input('diversion_phone')),
            'duration' => $this->input('duration') ?? 0,
            'wait' => $this->input('wait') ?? 0,
            'from_source_name'=> $this->input('from_source_name') ?? 'api',
        ]);
    }


    public function attributes(): array
    {
        return [
            'callid' => 'ID звонка',
            'datetime' => 'дата и время',
            'type' => 'тип звонка',
            'status' => 'статус звонка',
            'client_phone' => 'номер клиента',
            'user_pbx' => 'идентификатор пользователя ВАТС',
            'diversion_phone' => 'номер ВАТС',
            'duration' => 'длительность',
            'wait' => 'время ожидания',
            'link_record_pbx' => 'ссылка на запись в ВАТС',
            'link_record_crm' => 'ссылка на запись в CRM',
            'transcribation' => 'расшифровка разговора',
            'from_source_name' => 'источник звонка',
        ];
    }

    //    public function messages(): array
//    {
//        return [
//            'callid.required' => 'Поле ID звонка обязательно для заполнения.',
//            'callid.unique' => 'Звонок с таким ID уже существует.',
//            'datetime.required' => 'Поле дата и время обязательно.',
//            'datetime.date' => 'Поле дата и время должно быть корректной датой.',
//            'type.required' => 'Поле тип звонка обязательно.',
//            'type.in' => 'Указан недопустимый тип звонка.',
//            'status.required' => 'Поле статус звонка обязательно.',
//            'status.in' => 'Указан недопустимый статус звонка.',
//            'client_phone.required' => 'Поле номер клиента обязательно.',
//            'user_pbx.required' => 'Поле идентификатора пользователя ВАТС обязательно.',
//            'diversion_phone.required' => 'Поле номера ВАТС обязательно.',
//            'duration.integer' => 'Поле длительность должно быть числом.',
//            'wait.integer' => 'Поле ожидание должно быть числом.',
//            'link_record_pbx.url' => 'Ссылка на запись в ВАТС должна быть валидным URL.',
//            'link_record_crm.url' => 'Ссылка на запись в CRM должна быть валидным URL.',
//            'transcribation.string' => 'Поле расшифровка должно быть строкой.',
//            'from_source_name.required' => 'Поле источник звонка обязательно.',
//        ];
//    }

}
