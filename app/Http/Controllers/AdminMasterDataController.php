<?php

namespace App\Http\Controllers;

use App\Models\Biro;
use App\Models\Jabatan;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminMasterDataController extends Controller
{
    private function ensureAdmin(): void
    {
        $user = Auth::user();
        $role = DB::table('roles')->where('id', $user->role_id)->value('role_name');
        if ($role !== 'admin' && $role !== 'ADMIN') {
            abort(403);
        }
    }

    // ---------------------- BIRO ----------------------
    public function biroIndex()
    {
        $this->ensureAdmin();

        $rows = Biro::query()->orderBy('biro_name')->get();

        return view('admin.biro.index', [
            'rows' => $rows,
        ]);
    }

    public function biroCreate()
    {
        $this->ensureAdmin();

        return view('admin.biro.create');
    }

    public function biroStore(Request $request)
    {
        $this->ensureAdmin();

        $validated = $request->validate([
            'biro_name' => ['required', 'string', 'max:255'],
            'divisi' => ['required', 'string', 'max:100'],
            'is_proyek' => ['required', 'boolean'],
        ], [
            'biro_name.required' => 'Nama biro wajib diisi.',
            'divisi.required' => 'Divisi wajib diisi.',
        ]);

        Biro::query()->create($validated);

        return redirect()->route('settings.index')->with('status', 'Biro berhasil ditambahkan.');
    }

    public function biroEdit(int $id)
    {
        $this->ensureAdmin();

        $row = Biro::query()->findOrFail($id);

        return view('admin.biro.edit', [
            'row' => $row,
        ]);
    }

    public function biroUpdate(Request $request, int $id)
    {
        $this->ensureAdmin();

        $row = Biro::query()->findOrFail($id);

        $validated = $request->validate([
            'biro_name' => ['required', 'string', 'max:255'],
            'divisi' => ['required', 'string', 'max:100'],
            'is_proyek' => ['required', 'boolean'],
        ], [
            'biro_name.required' => 'Nama biro wajib diisi.',
            'divisi.required' => 'Divisi wajib diisi.',
        ]);

        $row->fill($validated);
        $row->save();

        return redirect()->route('settings.index')->with('status', 'Biro berhasil diperbarui.');
    }

    public function biroDestroy(int $id)
    {
        $this->ensureAdmin();

        $row = Biro::query()->findOrFail($id);
        $row->delete();

        return redirect()->route('settings.index')->with('status', 'Biro berhasil dihapus.');
    }

    // ---------------------- JABATAN ----------------------
    public function jabatanIndex()
    {
        $this->ensureAdmin();

        $rows = Jabatan::query()->orderBy('jabatan')->get();

        return view('admin.jabatan.index', [
            'rows' => $rows,
        ]);
    }

    public function jabatanCreate()
    {
        $this->ensureAdmin();

        return view('admin.jabatan.create');
    }

    public function jabatanStore(Request $request)
    {
        $this->ensureAdmin();

        $validated = $request->validate([
            'jabatan' => ['required', 'string', 'max:255'],
            'is_proyek' => ['required', 'boolean'],
        ], [
            'jabatan.required' => 'Nama jabatan wajib diisi.',
        ]);

        Jabatan::query()->create($validated);

        return redirect()->route('settings.index')->with('status', 'Jabatan berhasil ditambahkan.');
    }

    public function jabatanEdit(int $id)
    {
        $this->ensureAdmin();

        $row = Jabatan::query()->findOrFail($id);

        return view('admin.jabatan.edit', [
            'row' => $row,
        ]);
    }

    public function jabatanUpdate(Request $request, int $id)
    {
        $this->ensureAdmin();

        $row = Jabatan::query()->findOrFail($id);

        $validated = $request->validate([
            'jabatan' => ['required', 'string', 'max:255'],
            'is_proyek' => ['required', 'boolean'],
        ], [
            'jabatan.required' => 'Nama jabatan wajib diisi.',
        ]);

        $row->fill($validated);
        $row->save();

        return redirect()->route('settings.index')->with('status', 'Jabatan berhasil diperbarui.');
    }

    public function jabatanDestroy(int $id)
    {
        $this->ensureAdmin();

        $row = Jabatan::query()->findOrFail($id);
        $row->delete();

        return redirect()->route('settings.index')->with('status', 'Jabatan berhasil dihapus.');
    }

    // ---------------------- ROLE ----------------------
    public function roleIndex()
    {
        $this->ensureAdmin();

        $rows = Role::query()->orderBy('role_name')->get();

        return view('admin.role.index', [
            'rows' => $rows,
        ]);
    }

    public function roleCreate()
    {
        $this->ensureAdmin();

        return view('admin.role.create');
    }

    public function roleStore(Request $request)
    {
        $this->ensureAdmin();

        $validated = $request->validate([
            'role_name' => ['required', 'string', 'max:255'],
        ], [
            'role_name.required' => 'Nama role wajib diisi.',
        ]);

        Role::query()->create($validated);

        return redirect()->route('settings.index')->with('status', 'Role berhasil ditambahkan.');
    }

    public function roleEdit(int $id)
    {
        $this->ensureAdmin();

        $row = Role::query()->findOrFail($id);

        return view('admin.role.edit', [
            'row' => $row,
        ]);
    }

    public function roleUpdate(Request $request, int $id)
    {
        $this->ensureAdmin();

        $row = Role::query()->findOrFail($id);

        $validated = $request->validate([
            'role_name' => ['required', 'string', 'max:255'],
        ], [
            'role_name.required' => 'Nama role wajib diisi.',
        ]);

        $row->fill($validated);
        $row->save();

        return redirect()->route('settings.index')->with('status', 'Role berhasil diperbarui.');
    }

    public function roleDestroy(int $id)
    {
        $this->ensureAdmin();

        $row = Role::query()->findOrFail($id);
        $row->delete();

        return redirect()->route('settings.index')->with('status', 'Role berhasil dihapus.');
    }
}
