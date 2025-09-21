<?php

namespace App\Http\Controllers;

use App\Http\Requests\Megafon\HistoryRequest;
use App\Models\Call;
use App\Services\FileStorageService;


class MegafonController extends Controller
{
    public function history(HistoryRequest $request, FileStorageService $fileService)
    {
        $validated = $request->validated();


        // сохраняем файл у себя, кладём в call_records, называем по callid
        $validated['link_record_crm'] = $fileService->saveFromUrl(
            $validated['link_record_pbx'] ?? null,
            $validated['callid'] ?? null,
            'megafon'
        );

        $call = Call::create($validated);

        return response()->json([
            'message' => 'Звонок от мегафон успешно сохранен!',
            'call'    => $call,
        ], 201);
    }
}
