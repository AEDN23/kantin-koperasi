<?php

namespace Tests\Feature;

use App\Models\Karyawan;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthRoleTest extends TestCase
{
    public function test_guest_can_access_transaksi_baru(): void
    {
        $response = $this->get('/transaksi');
        $response->assertStatus(200);
    }

    public function test_guest_redirected_to_login_when_accessing_dashboard(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_guest_redirected_to_login_when_accessing_master_data(): void
    {
        $response = $this->get('/barang');
        $response->assertRedirect('/login');
    }

    public function test_admin_can_login_and_access_dashboard(): void
    {
        $response = $this->post('/login', [
            'login' => 'admin',
            'password' => 'admin123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();

        $admin = auth()->user();
        $this->assertEquals('admin', $admin->role);

        $dashResponse = $this->get('/dashboard');
        $dashResponse->assertStatus(200);
    }

    public function test_karyawan_can_login_with_full_name_and_nip_password(): void
    {
        $karyawanUser = User::where('role', 'karyawan')->whereNotNull('karyawan_id')->first();
        $this->assertNotNull($karyawanUser);

        $response = $this->post('/login', [
            'login' => $karyawanUser->name,
            'password' => $karyawanUser->karyawan->nip,
        ]);

        $response->assertRedirect('/transaksi/riwayat');
        $this->assertAuthenticatedAs($karyawanUser);
    }

    public function test_karyawan_can_login_with_nip_as_username(): void
    {
        $karyawanUser = User::where('role', 'karyawan')->whereNotNull('karyawan_id')->first();
        $this->assertNotNull($karyawanUser);

        $response = $this->post('/login', [
            'login' => $karyawanUser->karyawan->nip,
            'password' => $karyawanUser->karyawan->nip,
        ]);

        $response->assertRedirect('/transaksi/riwayat');
        $this->assertAuthenticatedAs($karyawanUser);
    }

    public function test_karyawan_blocked_from_admin_areas(): void
    {
        $karyawanUser = User::where('role', 'karyawan')->whereNotNull('karyawan_id')->first();

        // Coba akses dashboard
        $response = $this->actingAs($karyawanUser)->get('/dashboard');
        $response->assertRedirect('/transaksi/riwayat');
        $response->assertSessionHas('error');

        // Coba akses master data barang
        $response2 = $this->actingAs($karyawanUser)->get('/barang');
        $response2->assertRedirect('/transaksi/riwayat');
        $response2->assertSessionHas('error');
    }

    public function test_karyawan_can_access_riwayat_and_laporan(): void
    {
        $karyawanUser = User::where('role', 'karyawan')->whereNotNull('karyawan_id')->first();

        $response = $this->actingAs($karyawanUser)->get('/transaksi/riwayat');
        $response->assertStatus(200);

        $response2 = $this->actingAs($karyawanUser)->get('/laporan');
        $response2->assertStatus(200);
    }

    public function test_admin_can_change_karyawan_password_and_role_via_edit(): void
    {
        $admin = User::where('role', 'admin')->first();
        $karyawan = Karyawan::with('user')->first();

        $response = $this->actingAs($admin)->put(route('karyawan.update', $karyawan), [
            'nama_karyawan' => $karyawan->nama_karyawan,
            'nip' => $karyawan->nip,
            'role' => 'admin',
            'password' => 'passwordBaru123',
        ]);

        $response->assertRedirect(route('karyawan.index'));
        $karyawan->refresh();

        $this->assertEquals('admin', $karyawan->user->role);
        $this->assertTrue(Hash::check('passwordBaru123', $karyawan->user->password));

        // Kembalikan ke role karyawan
        $karyawan->user->update([
            'role' => 'karyawan',
            'password' => Hash::make($karyawan->nip),
        ]);
    }
}
