<?php

namespace App\Http\Controllers;

use App\Models\Federation;
use Illuminate\Http\Request;

class FederationController extends Controller
{
    public function index()
    {
        return Federation::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        return Federation::create($request->all());
    }

    public function show(Federation $federation)
    {
        return $federation;
    }

    public function update(Request $request, Federation $federation)
    {
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $federation->update($request->all());

        return $federation;
    }

    public function destroy(Federation $federation)
    {
        $federation->delete();

        return response()->noContent();
    }
}
