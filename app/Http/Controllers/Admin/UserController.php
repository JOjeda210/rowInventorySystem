<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::orderBy('name')->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    public function create(): View
    {
        $roles = [
            'admin' => 'Administrador',
            'warehouse_manager' => 'Gerente de Almacen',
            'warehouse_clerk' => 'Operario de Almacen',
            'production' => 'Produccion',
            'quality' => 'Calidad',
            'purchasing' => 'Compras',
        ];
        return view('admin.users.create', compact('roles'));
    }

    public function store(UserStoreRequest $request): RedirectResponse
    {
        User::create($request->validated());
        return redirect()->route('users.index')
            ->with('success', 'Usuario creado exitosamente.');
    }

    public function edit(User $user): View
    {
        $roles = [
            'admin' => 'Administrador',
            'warehouse_manager' => 'Gerente de Almacen',
            'warehouse_clerk' => 'Operario de Almacen',
            'production' => 'Produccion',
            'quality' => 'Calidad',
            'purchasing' => 'Compras',
        ];
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(UserUpdateRequest $request, User $user): RedirectResponse
    {
        $user->update($request->validated());
        return redirect()->route('users.index')
            ->with('success', 'Usuario actualizado exitosamente.');
    }

    public function toggle(User $user): RedirectResponse
    {
        $user->update(['is_active' => !$user->is_active]);
        $status = $user->is_active ? 'activado' : 'desactivado';
        return redirect()->route('users.index')
            ->with('success', "Usuario {$status} exitosamente.");
    }
}
