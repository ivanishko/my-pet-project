<?php

namespace App\Http\Controllers;

use App\Models\Federation;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FederationController extends Controller
{
    // Список всех федераций
    public function index()
    {
        return Inertia::render('Federations/Index', [
            'federations' => Federation::latest()->get(),
            'status' => session('status'),
        ]);
    }

    // Сохранение новой федерации
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Federation::create($validated);

        return redirect()
            ->route('federations.index')
            ->with('status', 'Федерация успешно создана');
    }

    public function show(Federation $federation)
    {
        return Inertia::render('Federations/Show', [
            'federation' => $federation->load('tournaments'),
            'status' => session('status'),
        ]);
    }

    // Форма редактирования
    public function edit(Federation $federation)
    {
        return Inertia::render('Federations/Edit', [
            'federation' => $federation,
            'status' => session('status'),
        ]);
    }

    // Обновление федерации
    public function update(Request $request, Federation $federation)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $federation->update($validated);

        return redirect()
            ->route('federations.index');
            // ->with('status', 'Федерация успешно обновлена');
    }

    // Удаление федерации
    public function destroy(Federation $federation)
    {
        $federation->delete();

        return redirect()
            ->route('federations.index')
            ->with('status', 'Федерация успешно удалена');
    }
}
