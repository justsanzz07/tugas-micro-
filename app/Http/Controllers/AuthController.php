<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return $this->redirectBasedOnRole(Auth::user());
        }

        return back()->withErrors([
            'email' => 'Kombinasi email dan password yang Anda masukkan tidak sesuai.',
        ])->onlyInput('email');
    }

    /**
     * Demo login cepat langsung berganti role (Mahasiswa, Dosen, Dekan, Warek).
     */
    public function demoLogin(string $role)
    {
        $roleQueryMap = [
            'mahasiswa' => ['mahasiswa'],
            'kaprodi' => ['kaprodi'],
            'dosen' => ['kaprodi', 'dosen', 'dosen_pembimbing'],
            'dekan' => ['dekan'],
            'keuangan' => ['keuangan'],
            'warek2' => ['warek_ii', 'warek', 'pimpinan'],
            'warek' => ['warek_ii', 'warek', 'pimpinan'],
            'admin' => ['admin'],
        ];

        $targetRoles = $roleQueryMap[$role] ?? [$role];

        $user = User::whereIn('role', $targetRoles)->first();
        if (! $user) {
            return redirect()->route('login')->with('error', "User demo dengan role [{$role}] belum tersedia.");
        }

        Auth::login($user);
        request()->session()->regenerate();

        return $this->redirectBasedOnRole($user);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar.');
    }

    protected function redirectBasedOnRole(User $user)
    {
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        if ($user->isMahasiswa()) {
            return redirect()->route('mahasiswa.dashboard');
        }
        if ($user->isKaprodi()) {
            return redirect()->route('kaprodi.dashboard');
        }
        if ($user->isDosen()) {
            return redirect()->route('kaprodi.dashboard');
        }
        if ($user->isDekan()) {
            return redirect()->route('dekan.dashboard');
        }
        if ($user->isKeuangan()) {
            return redirect()->route('keuangan.dashboard');
        }
        if ($user->isWarek() || $user->isWarekII()) {
            return redirect()->route('warek2.dashboard');
        }

        return redirect()->route('login');
    }
}
