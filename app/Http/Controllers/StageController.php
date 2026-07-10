<?php

namespace App\Http\Controllers;

use App\Models\Stage;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StageController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'season_id' => 'required|exists:tournament_seasons,id',
            'type' => 'required|in:championship,group,playoff',
            'description' => 'nullable|string',
        ]);

        // Проверка: чемпионат может быть только один
        if ($validated['type'] === 'championship') {
            $existing = Stage::where('season_id', $validated['season_id'])
                ->where('type', 'championship')
                ->exists();

            if ($existing) {
                return redirect()
                    ->back()
                    ->withErrors(['type' => 'В сезоне уже есть этап "Чемпионат". Можно создать только один.'])
                    ->withInput();
            }
        }

        // Автоматическая генерация названия
        $name = Stage::generateName($validated['type']);

        // Автоматический порядок
        $maxOrder = Stage::where('season_id', $validated['season_id'])->max('order') ?? -1;
        $order = $maxOrder + 1;

        $stage = Stage::create([
            'season_id' => $validated['season_id'],
            'name' => $name,
            'type' => $validated['type'],
            'order' => $order,
            'description' => $validated['description'] ?? null,
            'status' => 'upcoming',
        ]);

        return redirect()
            ->route('seasons.show', $validated['season_id'])
            ->with('status', 'Этап "' . $name . '" успешно создан');
    }

    public function update(Request $request, Stage $stage)
        {
            $validated = $request->validate([
                'type' => 'required|in:championship,group,playoff',
                'description' => 'nullable|string',
            ]);

            // Проверка: если меняем тип на чемпионат
            if ($validated['type'] === 'championship' && $stage->type !== 'championship') {
                $existing = Stage::where('season_id', $stage->season_id)
                    ->where('type', 'championship')
                    ->where('id', '!=', $stage->id) // Исключаем текущий этап
                    ->exists();

                if ($existing) {
                    return redirect()
                        ->back()
                        ->withErrors(['type' => 'В сезоне уже есть этап "Чемпионат".'])
                        ->withInput();
                }
            }

            // Автоматическое обновление названия при смене типа
            $name = Stage::generateName($validated['type']);

            $stage->update([
                'name' => $name,
                'type' => $validated['type'],
                'description' => $validated['description'] ?? null,
            ]);

            return redirect()
                ->route('seasons.show', $stage->season_id)
                ->with('status', 'Этап успешно обновлен');
        }

    public function destroy(Stage $stage)
    {
        $seasonId = $stage->season_id;
        $stage->delete();

        return redirect()
            ->route('seasons.show', $seasonId)
            ->with('status', 'Этап успешно удален');
    }
}
