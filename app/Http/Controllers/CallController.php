<?php

namespace App\Http\Controllers;

use App\Http\Requests\CallRequest;
use App\Models\Call;
use App\Services\FileStorageService;
use Illuminate\Http\Request;

class CallController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Call::query();

        if ($request->filled('callid')) {
            $query->where('callid', $request->callid);
        }

        if ($request->filled('client_phone')) {
            $query->where('client_phone', 'like', '%' . $request->client_phone . '%');
        }

        return $query->orderByDesc('datetime')->paginate(20);
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(CallRequest $request, FileStorageService $fileService)
    {
        $validated = $request->validated();

        $validated['link_record_crm'] = $fileService->saveFromUrl(
            $validated['link_record_pbx'] ?? null,
            $validated['callid'] ?? null,
            'call_records'
        );

        $call = Call::create($validated);

        return response()->json([
            'message' => 'Звонок успешно сохранен!',
            'call'    => $call,
        ], 201);
    }


    // Остальные методы пока не нужны
    public function create()
    {
        //
    }
    public function show(string $id)
    {
        //
    }
    public function edit(string $id)
    {
        //
    }
    public function update(Request $request, string $id)
    {
        //
    }
    public function destroy(string $id)
    {
        //
    }
}
