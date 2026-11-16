<?php

namespace Database\Seeders;

use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Akun untuk pengujian. Role ditetapkan di sini (oleh sistem), bukan lewat formulir registrasi.
     *
     * [MODUL 6 - SOAL 5c] Password admin TIDAK lagi "password". Nilainya diambil dari
     * SEED_ADMIN_PASSWORD pada .env; jika kosong dibuat acak dan ditampilkan sekali di terminal.
     * Akun petugas dan siswa tetap memakai "password" agar skenario uji tetap mudah dijalankan.
     */
    public function run(): void
    {
        $akun = [
            ['name' => 'Admin Perpustakaan',   'email' => 'admin@perpus.test',   'role' => 'admin'],
            ['name' => 'Petugas Perpustakaan', 'email' => 'petugas@perpus.test', 'role' => 'petugas'],
            ['name' => 'Siswa Satu',           'email' => 'siswa1@perpus.test',  'role' => 'siswa'],
            ['name' => 'Siswa Dua',            'email' => 'siswa2@perpus.test',  'role' => 'siswa'],
        ];

        $passwordAdmin = env('SEED_ADMIN_PASSWORD') ?: Str::random(16);

        if (! env('SEED_ADMIN_PASSWORD')) {
            $this->command?->warn("Password admin (dibuat acak, catat sekarang): {$passwordAdmin}");
        }

        foreach ($akun as $a) {
            $password = $a['role'] === 'admin' ? $passwordAdmin : 'password';

            $user = User::updateOrCreate(
                ['email' => $a['email']],
                ['name' => $a['name'], 'password' => $password] // di-hash otomatis oleh cast 'hashed'
            );
            $user->syncRoles($a['role']);
            $user->anggotaProfil();
        }

        // Contoh data peminjaman milik siswa agar halaman status/riwayat tidak kosong
        $buku = Buku::orderBy('id')->take(3)->get();

        if ($buku->count() >= 3) {
            $siswa1 = User::where('email', 'siswa1@perpus.test')->first()->anggotaProfil();
            $siswa2 = User::where('email', 'siswa2@perpus.test')->first()->anggotaProfil();

            Peminjaman::create([
                'anggota_id' => $siswa1->id, 'buku_id' => $buku[0]->id,
                'tanggal_pinjam' => now()->subDays(10)->toDateString(),
                'tanggal_kembali' => now()->subDays(3)->toDateString(),
                'status' => Peminjaman::DIKEMBALIKAN,
            ]);
            Peminjaman::create([
                'anggota_id' => $siswa1->id, 'buku_id' => $buku[1]->id,
                'tanggal_pinjam' => now()->toDateString(),
                'status' => Peminjaman::MENUNGGU,
            ]);
            Peminjaman::create([
                'anggota_id' => $siswa2->id, 'buku_id' => $buku[2]->id,
                'tanggal_pinjam' => now()->subDays(2)->toDateString(),
                'status' => Peminjaman::DISETUJUI,
            ]);
        }
    }
}
