<?php

namespace App\Http\Controllers;

use App\Models\Federation;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FederationController extends Controller
{
    /**
     * Список всех федераций
     */
    public function index()
    {
        return Inertia::render('Federations/Index', [
            'federations' => Federation::withCount('tournaments')
                ->latest()
                ->get(),
            'status' => session('status'),
        ]);
    }

    /**
     * Форма создания федерации
     */
    public function create()
    {
        return Inertia::render('Federations/Create', [
            'status' => session('status'),
        ]);
    }

    /**
     * Сохранение новой федерации
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|max:2048', // если есть логотип
            'website' => 'nullable|url|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        $federation = Federation::create($validated);

        return redirect()
            ->route('federations.index')
            ->with('status', 'Федерация успешно создана');
    }

    /**
     * Просмотр конкретной федерации
     */
    public function show(Federation $federation)
    {
        // Загружаем федерацию с турнирами и сезонами
        $federation->load(['tournaments' => function ($query) {
            $query->latest()->limit(5); // Последние 5 турниров
        }]);

        return Inertia::render('Federations/Show', [
            'federation' => $federation,
            'tournamentsCount' => $federation->tournaments()->count(),
            'status' => session('status'),
        ]);
    }

    /**
     * Форма редактирования федерации
     */
    public function edit(Federation $federation)
    {
        return Inertia::render('Federations/Edit', [
            'federation' => $federation,
            'status' => session('status'),
        ]);
    }

    /**
     * Обновление федерации
     */
    public function update(Request $request, Federation $federation)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
            'website' => 'nullable|url|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        $federation->update($validated);

        return redirect()
            ->route('federations.index')
            ->with('status', 'Федерация успешно обновлена');
    }

    /**
     * Удаление федерации
     */
    public function destroy(Federation $federation)
    {
        // Проверяем, есть ли у федерации турниры
        if ($federation->tournaments()->count() > 0) {
            return redirect()
                ->route('federations.index')
                ->with('error', 'Нельзя удалить федерацию, у которой есть турниры');
        }

        $federation->delete();

        return redirect()
            ->route('federations.index')
            ->with('status', 'Федерация успешно удалена');
    }
}
