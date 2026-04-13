<?php

namespace App\Http\Controllers;

use App\Models\EpsSystem;
use Illuminate\Http\Request;

class EpsSystemController extends Controller
{
    public function index()
    {
        $systems = EpsSystem::orderBy('order')->get();
        return view('sistemas.index', compact('systems'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'base_url'      => 'required|url|max:500',
            'api_token'     => 'required|string',
            'endpoint_path' => 'nullable|string|max:500',
            'timeout'       => 'nullable|integer|min:5|max:60',
        ]);

        EpsSystem::create([
            'name'          => $validated['name'],
            'base_url'      => $validated['base_url'],
            'api_token'     => $validated['api_token'],
            'endpoint_path' => $validated['endpoint_path'] ?? '/api/consulta/cedula/{cedula}',
            'timeout'       => $validated['timeout'] ?? 15,
            'is_active'     => true,
            'order'         => EpsSystem::max('order') + 1,
        ]);

        return redirect()->route('sistemas.index')->with('success', 'Sistema EPS agregado exitosamente.');
    }

    public function update(Request $request, EpsSystem $system)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'base_url'      => 'required|url|max:500',
            'api_token'     => 'nullable|string',
            'endpoint_path' => 'nullable|string|max:500',
            'timeout'       => 'nullable|integer|min:5|max:60',
            'is_active'     => 'required|boolean',
        ]);

        $data = [
            'name'          => $validated['name'],
            'base_url'      => $validated['base_url'],
            'endpoint_path' => $validated['endpoint_path'] ?? $system->endpoint_path,
            'timeout'       => $validated['timeout'] ?? $system->timeout,
            'is_active'     => $validated['is_active'],
        ];

        if (! empty($validated['api_token'])) {
            $data['api_token'] = $validated['api_token'];
        }

        $system->update($data);

        return redirect()->route('sistemas.index')->with('success', 'Sistema actualizado.');
    }

    public function destroy(EpsSystem $system)
    {
        $system->delete();
        return redirect()->route('sistemas.index')->with('success', 'Sistema eliminado.');
    }

    public function toggle(EpsSystem $system)
    {
        $system->update(['is_active' => ! $system->is_active]);
        return redirect()->route('sistemas.index')
            ->with('success', $system->name . ($system->is_active ? ' activado.' : ' desactivado.'));
    }
}
