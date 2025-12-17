<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Models\Organization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class MelakukanLoginController extends Controller
{
    /**
     * Tampilkan halaman login organisasi.
     * Use Case: Melakukan login (Pengunjung)
     */
    public function index(Request $request)
    {
        if ($request->session()->has('organization_id')) {
            if ($request->session()->get('is_admin') === true) {
                return redirect()->route('admin.mengelola-data.index');
            }
            if ($request->session()->get('is_donor') === true) {
                return redirect()->route('inventory.index');
            }
            return redirect()->route('beranda');
        }

        return view('login');
    }

    /**
     * Proses autentikasi organisasi.
     */
    public function authenticate(Request $request): RedirectResponse
    {
        $credentials = $request->validate(
            [
                'email' => ['required', 'email:rfc,dns', 'max:255'],
                'password' => ['required', 'string'],
            ],
            [],
            [
                'email' => 'email organisasi',
                'password' => 'password',
            ]
        );

        $organization = Organization::where('email', $credentials['email'])->first();

        if (! $organization) {
            throw ValidationException::withMessages([
                'email' => 'Email atau password tidak sesuai.',
            ]);
        }

        // Check password - handle both hashed and unhashed passwords (for migration)
        $passwordValid = false;
        $storedPassword = $organization->password;
        
        // Check if stored password is already hashed (bcrypt format)
        $isHashed = strlen($storedPassword) === 60 && str_starts_with($storedPassword, '$2y$');
        
        if ($isHashed) {
            // Password is hashed, use Hash::check
            $passwordValid = Hash::check($credentials['password'], $storedPassword);
        } else {
            // Password is not hashed (legacy data), compare directly
            $passwordValid = $credentials['password'] === $storedPassword;
            
            // If password matches and is not hashed, re-hash it for security
            if ($passwordValid) {
                $organization->password = $credentials['password']; // Will be hashed by mutator
                $organization->save();
            }
        }

        if (! $passwordValid) {
            throw ValidationException::withMessages([
                'email' => 'Email atau password tidak sesuai.',
            ]);
        }

        // Check if account is active
        if (!$organization->is_active) {
            throw ValidationException::withMessages([
                'email' => 'Akun organisasi Anda belum aktif. Silakan hubungi administrator.',
            ]);
        }

        $request->session()->regenerate();
        $request->session()->put('organization_id', $organization->id);
        $request->session()->put('organization_name', $organization->organization_name);
        $request->session()->put('is_admin', (bool) $organization->is_admin);
        $request->session()->put('is_donor', (bool) ($organization->is_donor ?? false));
        $request->session()->put('is_receiver', (bool) ($organization->is_receiver ?? false));

        if ($organization->is_admin) {
            return redirect()
                ->route('admin.mengelola-data.index')
                ->with('status', 'Berhasil masuk sebagai admin.');
        }

        if ((bool) $organization->is_donor) {
            return redirect()
                ->route('inventory.index')
                ->with('status', 'Berhasil masuk sebagai '.$organization->organization_name.'.');
        }

        return redirect()
            ->route('beranda')
            ->with('status', 'Berhasil masuk sebagai '.$organization->organization_name.'.');
    }
}
