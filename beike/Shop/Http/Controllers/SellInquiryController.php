<?php

namespace Beike\Shop\Http\Controllers;

use Beike\Models\SellInquiry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SellInquiryController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|max:191',
            'brand' => 'nullable|string|max:100',
        ]);

        SellInquiry::create([
            'email'      => $request->input('email'),
            'brand'      => $request->input('brand'),
            'ip_address' => $request->ip(),
        ]);

        return response()->json(['success' => true]);
    }
}
