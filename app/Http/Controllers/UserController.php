<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    private const SUPER_ADMIN_EMAIL = 'admin@asesco.com';

    public function index(Request $request)
    {
        $query = User::with('roles');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($role = $request->get('role')) {
            $query->whereHas('roles', fn($q) => $q->where('name', $role));
        }

        $users = $query->orderByDesc('created_at')->paginate(15)->withQueryString();
        $roles = Role::orderBy('name')->get();
        $superAdminEmail = self::SUPER_ADMIN_EMAIL;

        return view('usuarios.index', compact('users', 'roles', 'superAdminEmail'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role'     => 'required|exists:roles,name',
        ]);

        if (strtolower($validated['email']) === self::SUPER_ADMIN_EMAIL) {
            return redirect()->route('usuarios.index')
                ->with('error', 'No se puede crear un usuario con ese correo.');
        }

        $user = User::create([
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'password'  => Hash::make($validated['password']),
            'is_active' => true,
        ]);

        $user->assignRole($validated['role']);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario creado exitosamente.');
    }

    public function update(Request $request, User $user)
    {
        if ($user->email === self::SUPER_ADMIN_EMAIL) {
            return redirect()->route('usuarios.index')
                ->with('error', 'Este usuario no puede ser modificado.');
        }

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:6|confirmed',
            'role'     => 'required|exists:roles,name',
            'is_active'=> 'required|boolean',
        ]);

        if (strtolower($validated['email']) === self::SUPER_ADMIN_EMAIL) {
            return redirect()->route('usuarios.index')
                ->with('error', 'No se puede asignar ese correo a otro usuario.');
        }

        $user->update([
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'is_active' => $validated['is_active'],
        ]);

        if (! empty($validated['password'])) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        $user->syncRoles([$validated['role']]);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario actualizado exitosamente.');
    }

    public function destroy(User $user)
    {
        if ($user->email === self::SUPER_ADMIN_EMAIL) {
            return redirect()->route('usuarios.index')
                ->with('error', 'Este usuario no puede ser eliminado.');
        }

        if ($user->id === auth()->id()) {
            return redirect()->route('usuarios.index')
                ->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        $user->delete();

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario eliminado exitosamente.');
    }
}
