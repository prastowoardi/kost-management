<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Models\Facility;
use Illuminate\Http\Request;

class FacilityController extends Controller
{
    public function index(Request $request)
    {
        $facilities = Facility::when($request->search, fn ($q) => $q->where('name', 'like', '%'.$request->search.'%'))
            ->when($request->type, fn ($q) => $q->where('type', $request->type))
            ->when($request->condition, fn ($q) => $q->where('condition', $request->condition))
            ->orderBy('type')->orderBy('name')->paginate(15);

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
            'description' => 'nullable|string',
        ]);

        $facility = Facility::create($validated);

        LogHelper::log('CREATE_FACILITY', "Menambah fasilitas {$facility->name}", $facility);

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
            'description' => 'nullable|string',
        ]);

        $before = $facility->toArray();
        $facility->update($validated);
        $after = $facility->fresh()->toArray();

        LogHelper::log('UPDATE_FACILITY', "Mengubah fasilitas {$facility->name}", $facility, [
            'before' => $before,
            'after' => $after,
        ]);

        return redirect()->route('facilities.index')
            ->with('success', 'Fasilitas berhasil diupdate');
    }

    public function destroy(Facility $facility)
    {
        $deletedData = $facility->toArray();
        $facility->delete();

        LogHelper::log('DELETE_FACILITY', "Menghapus fasilitas {$deletedData['name']}", null, [
            'deleted' => $deletedData,
        ]);

        return redirect()->route('facilities.index')
            ->with('success', 'Fasilitas berhasil dihapus');
    }
}
