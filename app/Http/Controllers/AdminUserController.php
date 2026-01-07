<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminUserController extends Controller
{
    private function ensureAdmin(): void
    {
        $user = Auth::user();
        $role = DB::table('roles')->where('id', $user->role_id)->value('role_name');
        // accept both legacy 'admin' and new 'ADMIN'
        if ($role !== 'admin' && $role !== 'ADMIN') {
            abort(403);
        }
    }

    public function create()
    {
        $this->ensureAdmin();

        return view('admin.users.create', $this->dropdownData() + [
            // for edit view compatibility
            'row' => null,
        ]);
    }

    public function index(Request $request)
    {
        $this->ensureAdmin();

        $q = trim((string) $request->query('q', ''));

        $users = User::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('username', 'ilike', "%{$q}%")
                        ->orWhere('nama', 'ilike', "%{$q}%")
                        ->orWhere('nip', 'ilike', "%{$q}%");
                });
            })
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        // enrich role_name & biro_name for table display
        $roleMap = DB::table('roles')->pluck('role_name', 'id');
        $biroMap = DB::table('biro')->pluck('biro_name', 'id');

        foreach ($users as $u) {
            $u->role_name = $roleMap[$u->role_id] ?? null;
            $u->biro_name = $biroMap[$u->biro_id] ?? null;
        }

        return view('admin.users.index', [
            'users' => $users,
            'q' => $q,
        ]);
    }

    public function edit(int $id)
    {
        $this->ensureAdmin();

        $row = User::query()->findOrFail($id);

        // map current jabatan string -> id for dropdown
        $jabatanId = null;
        if (!empty($row->jabatan)) {
            $jabatanId = DB::table('jabatan')->where('jabatan', $row->jabatan)->value('id');
        }

        return view('admin.users.edit', $this->dropdownData() + [
            'row' => $row,
            'jabatanId' => $jabatanId,
        ]);
    }

    public function update(Request $request, int $id)
    {
        $this->ensureAdmin();

        $row = User::query()->findOrFail($id);

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:50'],
            'username' => ['required', 'string', 'max:35', 'unique:users,username,' . $row->id],
            'password' => ['nullable', 'string', 'min:8', 'max:255'],
            'role_id' => ['required', 'integer', 'exists:roles,id'],

            'nip' => ['required', 'string', 'max:35'],
            'email' => ['required', 'string', 'max:50'],
            'telp' => ['required', 'string', 'max:20'],
            'biro_id' => ['required', 'integer', 'exists:biro,id'],
            'nip_atasan' => ['required', 'string', 'max:10'],
            'tgl_lahir' => ['required', 'date_format:Y-m-d'],

            'isdel' => ['required', 'boolean'],
            'is_kirim' => ['required', 'boolean'],
            'is_pulang' => ['required', 'boolean'],

            'jabatan_id' => ['required', 'integer', 'exists:jabatan,id'],
        ], [
            'username.unique' => 'Username sudah digunakan.',
        ]);

        // jabatan_id -> users.jabatan (string)
        $validated['jabatan'] = DB::table('jabatan')->where('id', $validated['jabatan_id'])->value('jabatan');
        unset($validated['jabatan_id']);

        // If password empty on edit: don't change it
        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $row->fill($validated);
        $row->save();

        return redirect()->route('settings.index')->with('status', 'User berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        $this->ensureAdmin();

        $row = User::query()->findOrFail($id);
        $row->delete();

        return redirect()->route('settings.index')->with('status', 'User berhasil dihapus.');
    }

    private function dropdownData(): array
    {
        $roles = DB::table('roles')
            ->select(['id', 'role_name'])
            ->orderBy('role_name')
            ->get();

        $biros = DB::table('biro')
            ->select(['id', 'biro_name'])
            ->orderBy('biro_name')
            ->get();

        $jabatans = DB::table('jabatan')
            ->select(['id', 'jabatan'])
            ->orderBy('jabatan')
            ->get();

        $nipAtasanOptions = DB::table('users')
            ->whereNotNull('nip')
            ->where('nip', '!=', '')
            ->distinct()
            ->orderBy('nip')
            ->pluck('nip');

        return [
            'roles' => $roles,
            'biros' => $biros,
            'jabatans' => $jabatans,
            'nipAtasanOptions' => $nipAtasanOptions,
        ];
    }

    public function store(Request $request)
    {
        $this->ensureAdmin();

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:50'],
            'username' => ['required', 'string', 'max:35', 'unique:users,username'],
            'password' => ['required', 'string', 'min:8', 'max:255'],
            'role_id' => ['required', 'integer', 'exists:roles,id'],

            'nip' => ['required', 'string', 'max:35'],
            'email' => ['required', 'string', 'max:50'],
            'telp' => ['required', 'string', 'max:20'],
            'biro_id' => ['required', 'integer', 'exists:biro,id'],
            'nip_atasan' => ['required', 'string', 'max:10'],
            'tgl_lahir' => ['required', 'date_format:Y-m-d'],

            // boolean options
            'isdel' => ['required', 'boolean'],
            'is_kirim' => ['required', 'boolean'],
            'is_pulang' => ['required', 'boolean'],

            // jabatan dropdown (tabel jabatan)
            'jabatan_id' => ['required', 'integer', 'exists:jabatan,id'],
        ], [
            'username.unique' => 'Username sudah digunakan.',
        ]);

        // Map jabatan dropdown -> kolom users.jabatan (string)
        // (kolom di tabel users adalah string, bukan jabatan_id)
        if (!empty($validated['jabatan_id'])) {
            $validated['jabatan'] = DB::table('jabatan')->where('id', $validated['jabatan_id'])->value('jabatan');
        }
        unset($validated['jabatan_id']);

        // Kolom-kolom ini diminta kosong (tidak muncul di form)
        $validated['id_kel'] = null;
        $validated['id_lokasi_car_pooling'] = null;
        $validated['is_covid_ranger'] = null;
        $validated['is_tim_covid'] = null;
        $validated['is_satgas_covid'] = false;
        $validated['is_hc'] = false;
        $validated['is_umum'] = null;
        $validated['is_crot'] = false;

        // With cast password=hashed di model, password akan otomatis di-hash saat disimpan.
        User::query()->create($validated);

        return redirect()->route('settings.index')->with('status', 'User berhasil ditambahkan.');
    }
}
