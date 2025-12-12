<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use Illuminate\Http\Request;

class FacilityController extends Controller
{
    public function index()
    {
        $facilities = Facility::orderBy('type')->orderBy('name')->paginate(15);
        return view('facilities.index', compact('facilities'));
    }

    public function create()
    {
        return view('facilities.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:common,room',
            'quantity' => 'required|integer|min:1',
            'condition' => 'required|in:good,fair,poor',
            'description' => 'nullable|string'
        ]);

        Facility::create($validated);

        return redirect()->route('facilities.index')
                        ->with('success', 'Fasilitas berhasil ditambahkan');
    }

    public function edit(Facility $facility)
    {
        return view('facilities.edit', compact('facility'));
    }

    public function update(Request $request, Facility $facility)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:common,room',
            'quantity' => 'required|integer|min:1',
            'condition' => 'required|in:good,fair,poor',
            'description' => 'nullable|string'
        ]);

        $facility->update($validated);

        return redirect()->route('facilities.index')
                        ->with('success', 'Fasilitas berhasil diupdate');
    }

    public function destroy(Facility $facility)
    {
        $facility->delete();

        return redirect()->route('facilities.index')
                        ->with('success', 'Fasilitas berhasil dihapus');
    }
}
