<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Complaint;

class MobileComplaintController extends Controller
{
    public function index(Request $request)
    {
        $complaints = Complaint::where('tenant_id', $request->user()->tenant->id)
                        ->orderBy('created_at', 'desc')
                        ->get();

        return response()->json([
            'success' => true,
            'data' => $complaints
        ]);
    }

    public function store(Request $request) 
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        $user = $request->user();
        $tenant = \App\Models\Tenant::where('user_id', $user->id)->first();

        if (!$tenant) {
            return response()->json(['message' => 'Data tenant tidak ditemukan'], 404);
        }

        $complaint = new Complaint();
        $complaint->tenant_id = $request->user()->tenant->id;
        $complaint->room_id = $request->user()->tenant->room_id;
        $complaint->title = $request->title;
        $complaint->description = $request->description;
        $complaint->category = $request->category;
        $complaint->priority = $request->priority;
        $complaint->status = 'open';

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('complaints', 'public');
            $complaint->images = [$path]; 
        }

        $complaint->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Keluhan berhasil dikirim!'
        ]);
    }
}