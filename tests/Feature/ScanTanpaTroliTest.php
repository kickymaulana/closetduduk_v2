<?php

namespace Tests\Feature;

use App\Models\Departemen;
use App\Models\Kualitas;
use App\Models\PengerjaanProduk;
use App\Models\Proses;
use App\Models\SesiKerja;
use App\Models\Shift;
use App\Models\User;
use App\Models\Warna;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScanTanpaTroliTest extends TestCase
{
    use RefreshDatabase;

    private function setupData(): array
    {
        $departemen = Departemen::create(['departemen' => 'Casting']);
        $proses = Proses::create(['departemen_id' => $departemen->id, 'urutan' => 1, 'proses' => 'Casting']);
        $shift = Shift::create(['shift' => 'Pagi']);

        $leader = User::create([
            'name' => 'Leader',
            'username' => 'leader',
            'email' => 'leader@example.com',
            'password' => bcrypt('password'),
            'departemen_id' => $departemen->id,
        ]);
        $anggota = User::create([
            'name' => 'Anggota',
            'username' => 'anggota',
            'email' => 'anggota@example.com',
            'password' => bcrypt('password'),
            'departemen_id' => $departemen->id,
        ]);

        $sesi = SesiKerja::create([
            'leader_id' => $leader->id,
            'shift_id' => $shift->id,
            'proses_id' => $proses->id,
            'jenis' => 'Body',
        ]);
        $sesi->sesi_kerja_members()->create(['user_id' => $anggota->id]);

        Kualitas::create(['kualitas' => 'A']);
        Warna::create(['warna' => 'Putih']);

        return compact('leader', 'anggota', 'sesi', 'proses');
    }

    public function test_scan_awal_membuat_produk_dan_mencatat_pengerjaan()
    {
        ['leader' => $leader, 'anggota' => $anggota, 'sesi' => $sesi] = $this->setUpData();

        $this->actingAs($leader)
            ->withSession(['sesi_kerja_id' => $sesi->id])
            ->post('/scan/awal', [
                'qr' => 'DN0001234',
                'nomor_mesin' => 'Mesin 01',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('produk', ['qrcode' => 'DN0001234', 'proses_id' => $sesi->proses_id, 'sudah_scan' => 'Sudah']);
        $this->assertDatabaseHas('pengerjaan_produk', ['produk_id' => 1, 'user_id' => $leader->id]);
        $this->assertDatabaseHas('pengerjaan_produk', ['produk_id' => 1, 'user_id' => $anggota->id]);
    }

    public function test_scan_awal_ditolak_tanpa_sesi_active(): void
    {
        $data = $this->setUpData();

        $user = $data['leader'];

        $this->actingAs($user)
            ->post('/scan/awal', ['qr' => 'DN0009999'])
            ->assertSessionHasErrors('error');

        $this->assertDatabaseMissing('produk', ['qrcode' => 'DN0009999']);
    }

    public function test_scan_validasi_mencatat_untuk_leader_dan_anggota(): void
    {
        $data = $this->setUpData();

        $this->actingAs($data['leader'])
            ->withSession(['sesi_kerja_id' => $data['sesi']->id])
            ->post('/scan/awal', ['qr' => 'DN0005678'])
            ->assertRedirect();

        // Reset sudah_scan agar bisa di-scan lagi sebagai validasi (simulasi pindah proses tidak di sini)
        \App\Models\Produk::where('qrcode', 'DN0005678')->update(['sudah_scan' => 'Belum']);

        $this->actingAs($data['leader'])
            ->withSession(['sesi_kerja_id' => $data['sesi']->id])
            ->post('/scan/validasi', ['qr' => 'DN0005678'])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('pengerjaan_produk', [
            'produk_id' => 1,
            'user_id' => $data['leader']->id,
            'status_kondisi' => 'OK',
        ]);
        $this->assertDatabaseHas('pengerjaan_produk', [
            'produk_id' => 1,
            'user_id' => $data['anggota']->id,
            'status_kondisi' => 'OK',
        ]);
    }

    public function test_scan_validasi_menolak_produk_yang_tidak_exist(): void
    {
        $data = $this->setUpData();

        $this->actingAs($data['leader'])
            ->withSession(['sesi_kerja_id' => $data['sesi']->id])
            ->post('/scan/validasi', ['qr' => 'DN0000000'])
            ->assertSessionHasErrors('qr');
    }

    public function test_pengerjaan_tidak_bergantung_pada_troli(): void
    {
        // Tidak ada tabel troli yang dipakai — memastikan semua data scan tetap tercatat di pengerjaan_produk
        $data = $this->setUpData();

        $this->actingAs($data['leader'])
            ->withSession(['sesi_kerja_id' => $data['sesi']->id])
            ->post('/scan/awal', ['qr' => 'DN0007777']);

        $this->assertTrue(PengerjaanProduk::where('produk_id', 1)->count() >= 2);
    }
}