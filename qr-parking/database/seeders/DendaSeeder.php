<?php

namespace Database\Seeders;

use App\Models\Denda;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DendaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $data = [
            [
                'id_users' => 1,
                'nama' => 'Budi Santoso',
                'id_kendaraan' => 101,
                'plat_nomor' => 'B 1234 ABC',
                'jenis_pelanggaran' => 'Parkir Liar',
                'waktu_pelanggaran' => Carbon::now()->subDays(2),
                'denda' => 50000,
                'status' => 'pending',
                'keterangan' => 'Parkir di depan pintu masuk',
            ],
            [
                'id_users' => 2,
                'nama' => 'Siti Aminah',
                'id_kendaraan' => 102,
                'plat_nomor' => 'D 5678 XYZ',
                'jenis_pelanggaran' => 'Melebihi Batas Waktu',
                'waktu_pelanggaran' => Carbon::now()->subDays(1),
                'denda' => 25000,
                'status' => 'dibayar',
                'keterangan' => 'Overstay 3 jam',
            ],
            [
                'id_users' => 3,
                'nama' => 'Andi Wijaya',
                'id_kendaraan' => 103,
                'plat_nomor' => 'F 9101 DEF',
                'jenis_pelanggaran' => 'Tidak Membawa STNK',
                'waktu_pelanggaran' => Carbon::now(),
                'denda' => 100000,
                'status' => 'pending',
                'keterangan' => 'Pemeriksaan rutin',
            ],
            [
                'id_users' => 4,
                'nama' => 'Rina Pratama',
                'id_kendaraan' => 104,
                'plat_nomor' => 'B 2233 GHI',
                'jenis_pelanggaran' => 'Parkir Menutup Akses',
                'waktu_pelanggaran' => Carbon::now()->subHours(5),
                'denda' => 75000,
                'status' => 'pending',
                'keterangan' => 'Menghalangi hidran kebakaran',
            ],
            [
                'id_users' => 5,
                'nama' => 'Eko Prasetyo',
                'id_kendaraan' => 105,
                'plat_nomor' => 'L 4455 JKL',
                'jenis_pelanggaran' => 'Tiket Hilang',
                'waktu_pelanggaran' => Carbon::now()->subDays(3),
                'denda' => 50000,
                'status' => 'dibayar',
                'keterangan' => 'Denda penggantian tiket',
            ],
            [
                'id_users' => 6,
                'nama' => 'Dewi Lestari',
                'id_kendaraan' => 106,
                'plat_nomor' => 'B 7788 MNO',
                'jenis_pelanggaran' => 'Parkir di Area Disabilitas',
                'waktu_pelanggaran' => Carbon::now()->subDays(1),
                'denda' => 150000,
                'status' => 'pending',
                'keterangan' => 'Pelanggaran area khusus',
            ],
            [
                'id_users' => 7,
                'nama' => 'Hendra Kurnia',
                'id_kendaraan' => 107,
                'plat_nomor' => 'Z 9900 PQR',
                'jenis_pelanggaran' => 'Melawan Arus di Parkiran',
                'waktu_pelanggaran' => Carbon::now()->subHours(2),
                'denda' => 30000,
                'status' => 'dibayar',
                'keterangan' => 'Membahayakan pengendara lain',
            ],
            [
                'id_users' => 8,
                'nama' => 'Maya Sari',
                'id_kendaraan' => 108,
                'plat_nomor' => 'H 1122 STU',
                'jenis_pelanggaran' => 'Parkir Tidak Rapi',
                'waktu_pelanggaran' => Carbon::now()->subDays(4),
                'denda' => 20000,
                'status' => 'pending',
                'keterangan' => 'Memakan dua slot parkir',
            ],
            [
                'id_users' => 9,
                'nama' => 'Rizky Fauzi',
                'id_kendaraan' => 109,
                'plat_nomor' => 'AA 3344 VWX',
                'jenis_pelanggaran' => 'Knalpot Brong',
                'waktu_pelanggaran' => Carbon::now()->subHours(10),
                'denda' => 200000,
                'status' => 'pending',
                'keterangan' => 'Mengganggu kenyamanan',
            ],
            [
                'id_users' => 10,
                'nama' => 'Siska Putri',
                'id_kendaraan' => 110,
                'plat_nomor' => 'AB 5566 YZA',
                'jenis_pelanggaran' => 'Melebihi Batas Waktu',
                'waktu_pelanggaran' => Carbon::now()->subDays(5),
                'denda' => 25000,
                'status' => 'dibayar',
                'keterangan' => 'Lupa melakukan tap out',
            ],
        ];

        foreach ($data as $item) {
            Denda::create($item);
        }


        //

    }
}
