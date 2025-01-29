<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Exports\UsersExport;
use App\Imports\UsersImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    // Ambil daftar user, supaya bisa difilter pakai start_date & end_date
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->has(['start_date', 'end_date'])) {
            $query->whereBetween('created_at', [
                $request->start_date,
                $request->end_date
            ]);
        }

        return $query->paginate(10);
    }

    // Tambah user baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'avatar' => 'nullable|image|max:1024'
        ]);

        // Simpan avatar kalau ada
        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $path;
        }

        // Hash password sebelum disimpan
        $validated['password'] = Hash::make($validated['password']);

        return User::create($validated);
    }

    // Ambil detail user berdasarkan ID
    public function show(User $user)
    {
        return $user;
    }

    // Update data user
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:8',
            'avatar' => 'nullable|image|max:1024'
        ]);

        // Update avatar kalau ada
        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $path;
        }

        // Hash password kalau di-update
        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);
        return $user;
    }

    // Hapus user (soft delete)
    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }

    // Ambil daftar user yang udah dihapus
    public function deleted()
    {
        return User::onlyTrashed()->get();
    }

    // Restore user yang udah dihapus
    public function restore($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore();
        return $user;
    }

    // Import data user dari file Excel
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        Excel::import(new UsersImport, $request->file('file'));
        return response()->json(['message' => 'Users imported successfully']);
    }

    // Export data user ke file Excel
    public function export(Request $request)
    {
        return Excel::download(new UsersExport($request->start_date, $request->end_date), 'users.xlsx');
    }
}
