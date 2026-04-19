<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $roles = Role::query()
            // Kita hitung jumlah user yang punya role ini secara otomatis
            ->withCount('users')
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('guard_name', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Master/Roles/Index', [
            'roles' => $roles,
            'filters' => $request->only(['search'])
        ]);
    }

    public function create()
    {
        return Inertia::render('Master/Roles/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            // Nama role harus unik di tabel roles
            'name' => 'required|string|max:255|unique:roles,name',
        ]);

        Role::create([
            'name' => $request->name,
            'guard_name' => 'web' // Default guard untuk aplikasi web
        ]);

        return redirect()->route('roles.index')->with('success', 'Jabatan baru berhasil ditambahkan.');
    }

    public function edit(Role $role)
    {
        // 1. Load relasi permissions
        $role->load('permissions');

        return Inertia::render('Master/Roles/Edit', [
            // 2. Kirim $role secara utuh agar relasi 'permissions' terbawa ke props
            'role' => $role,
            'all_permissions' => Permission::all(),
        ]);
    }



    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'array',
        ]);

        $role->update(['name' => $request->name]);

        // Spatie syncPermissions bisa menerima array ID [1, 2, 3]
        // atau array nama ['user-list', 'user-create']
        $role->syncPermissions($request->permissions);

        return redirect()->route('roles.index')->with('success', 'Perubahan disimpan.');
    }

    public function destroy(Role $role)
    {
        // Proteksi: Jangan hapus role admin utama
        if ($role->name === 'admin') {
            return back()->with('error', 'Role Administrator tidak dapat dihapus.');
        }

        $role->delete();

        return redirect()->route('roles.index')->with('success', 'Jabatan berhasil dihapus.');
    }
}
