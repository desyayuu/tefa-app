<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $now = Carbon::now();
        $koorUserId = (string) Str::uuid();
        $koorId = (string) Str::uuid();

        $dosenUserId1 = (string) Str::uuid();
        $dosenUserId2 = (string) Str::uuid();
        $dosenUserId3 = (string) Str::uuid();
        $dosenUserId4 = (string) Str::uuid();
        $dosenUserId5 = (string) Str::uuid();
        $dosenId1 = (string) Str::uuid();
        $dosenId2 = (string) Str::uuid();
        $dosenId3 = (string) Str::uuid();
        $dosenId4 = (string) Str::uuid();
        $dosenId5 = (string) Str::uuid();

        $mahasiswaUserId1 = (string) Str::uuid();
        $mahasiswaUserId2 = (string) Str::uuid();
        $mahasiswaUserId3 = (string) Str::uuid();
        $mahasiswaUserId4 = (string) Str::uuid();
        $mahasiswaUserId5 = (string) Str::uuid();
        $mahasiswaId1 = (string) Str::uuid();
        $mahasiswaId2 = (string) Str::uuid();
        $mahasiswaId3 = (string) Str::uuid();   
        $mahasiswaId4 = (string) Str::uuid();
        $mahasiswaId5 = (string) Str::uuid();


        $profesionalUserId1 = (string) Str::uuid();
        $profesionalUserId2 = (string) Str::uuid();
        $profesionalUserId3 = (string) Str::uuid();
        $profesionalUserId4 = (string) Str::uuid();
        $profesionalUserId5 = (string) Str::uuid();
        $profesionalId1 = (string) Str::uuid();
        $profesionalId2 = (string) Str::uuid();
        $profesionalId3 = (string) Str::uuid();
        $profesionalId4 = (string) Str::uuid();
        $profesionalId5 = (string) Str::uuid();

        
        DB::table('d_user')->insert([
            'user_id' => $koorUserId,
            'email'=> 'koordinator@example.com',
            'password' => Hash::make('password123'),
            'role' => 'Koordinator',
            'status' => 'Active',
            'created_at' => now(),
            'created_by' => 0,
        ]);
        
        DB::table('d_koordinator')->insert([
            'koordinator_id' => $koorId,
            'user_id' => $koorUserId,
            'nama_koordinator' => 'Koordinator Test',
            'jenis_kelamin_koordinator' => 'Laki-Laki',
            'tanggal_lahir_koordinator' => '1980-01-01',
            'telepon_koordinator' => '081234567890',
            'profile_img_koordinator' => null,
            'nidn_koordinator' => '1234567890',
            'created_at' => $now,
            'created_by' => 0
        ]);

        DB::table('d_user')->insert([
            [
                'user_id' =>$dosenUserId1,
                'email'=> 'dosen_a@example.com',
                'password' => Hash::make('password123'),
                'role' => 'Dosen',
                'status' => 'Active',
                'created_at' => now(),
                'created_by' => 0,
            ], 
            [
                'user_id' => $dosenUserId2,
                'email'=> 'dosen_b@example.com',
                'password' => Hash::make('password123'),
                'role' => 'Dosen',
                'status' => 'Active',
                'created_at' => now(),
                'created_by' => 0,
            ], 
            [
                'user_id' => $dosenUserId3,
                'email'=> 'dosen_c@example.com',
                'password' => Hash::make('password123'),
                'role' => 'Dosen',
                'status' => 'Active',
                'created_at' => now(),
                'created_by' => 0,
            ], 
            [
                'user_id' => $dosenUserId4,
                'email'=> 'dosen_d@example.com',
                'password' => Hash::make('password123'),
                'role' => 'Dosen',
                'status' => 'Active',
                'created_at' => now(),
                'created_by' => 0,
            ], 
            [
                'user_id' => $dosenUserId5,
                'email'=> 'dosen_e@example.com',
                'password' => Hash::make('password123'),
                'role' => 'Dosen',
                'status' => 'Active',
                'created_at' => now(),
                'created_by' => 0,
            ]
        ]);

        DB::table('d_dosen')->insert([
            [
                'dosen_id' => $dosenId1,
                'user_id' => $dosenUserId1,
                'nama_dosen' => 'Dosen Test A',
                'jenis_kelamin_dosen' => 'Laki-Laki',
                'tanggal_lahir_dosen' => '1980-01-01',
                'telepon_dosen' => '081234567890',
                'profile_img_dosen' => null,
                'nidn_dosen' => '1234567890',
                'created_at' => $now,
                'created_by' => 0
            ], 
            [
                'dosen_id' => $dosenId2,
                'user_id' => $dosenUserId2,
                'nama_dosen' => 'Dosen Test B',
                'jenis_kelamin_dosen' => 'Perempuan',
                'tanggal_lahir_dosen' => '1980-01-01',
                'telepon_dosen' => '081234567891',
                'profile_img_dosen' => null,
                'nidn_dosen' => '1234567891',
                'created_at' => $now,
                'created_by' => 0
            ], 
            [
                'dosen_id' => $dosenId3,
                'user_id' => $dosenUserId3,
                'nama_dosen' => 'Dosen Test C',
                'jenis_kelamin_dosen' => 'Laki-Laki',
                'tanggal_lahir_dosen' => '1980-01-02',
                'telepon_dosen' => '081234567892',
                'profile_img_dosen' => null,
                'nidn_dosen' => '1234567892',
                'created_at' => $now,
                'created_by' => 0
            ],
            [
                'dosen_id' =>$dosenId4,
                'user_id'=> $dosenUserId4,
                'nama_dosen'=> "Dosen Test D",
                "jenis_kelamin_dosen"=> "Perempuan",
                "tanggal_lahir_dosen"=> "1980-01-03",
                "telepon_dosen"=> "081234567893",
                "profile_img_dosen"=> null,
                "nidn_dosen"=> "1234567893",
                "created_at"=> $now,
                'created_by' => 0
            ], 
            [
                'dosen_id' => $dosenId5,
                'user_id' =>$dosenUserId5,
                'nama_dosen' => 'Dosen Test E',
                'jenis_kelamin_dosen' => 'Laki-Laki',
                'tanggal_lahir_dosen' => '1980-01-04',
                'telepon_dosen' => '081234567894',
                'profile_img_dosen' => null,
                'nidn_dosen' => '1234567894',
                'created_at' => $now,
                'created_by' => 0
            ]
        ]);

        DB::table('d_user')->insert([
            [
                'user_id' => $mahasiswaUserId1,
                'email'=> 'mahasiswa_a@example.com', 
                'password' => Hash::make('password123'),
                'status' => 'Active',
                'role' => 'Mahasiswa',
                'created_at' => now(),
                'created_by' => 0,
            ], 
            [
                'user_id' => $mahasiswaUserId2,
                'email'=> 'mahasiswa_b@example.com',
                'password' => Hash::make('password123'),
                'status' => 'Active',
                'role' => 'Mahasiswa',
                'created_at' => now(),
                'created_by' => 0,
            ], [
                'user_id' => $mahasiswaUserId3,
                'email'=> 'mahasiswa_c@example.com',
                'password' => Hash::make('password123'),
                'status' => 'Active',
                'role' => 'Mahasiswa',
                'created_at' => now(),
                'created_by' => 0,
            ],[
                'user_id' => $mahasiswaUserId4,
                'email'=> 'mahasiswa_d@example.com',
                'password' => Hash::make('password123'),
                'status' => 'Active',
                'role' => 'Mahasiswa',
                'created_at' => now(),
                'created_by' => 0,
            ],
            [
                'user_id' => $mahasiswaUserId5,
                'email'=> 'mahasiswa_e@example.com',
                'password' => Hash::make('password123'),
                'status' => 'Active',
                'role' => 'Mahasiswa',
                'created_at' => now(),
                'created_by' => 0,
            ]
        ]);

        DB::table('d_mahasiswa')->insert([
            [
                'mahasiswa_id' => $mahasiswaId1,
                'user_id' => $mahasiswaUserId1,
                'nama_mahasiswa' => 'Mahasiswa Test A',
                'jenis_kelamin_mahasiswa' => 'Laki-Laki',
                'tanggal_lahir_mahasiswa' => '2000-01-01',
                'telepon_mahasiswa' => '081234567890',
                'profile_img_mahasiswa' => null,
                'nim_mahasiswa' => '2141720119',
                'created_at' => $now,
                'created_by' => 0
            ], 
            [
                'mahasiswa_id' => $mahasiswaId2,
                'user_id' => $mahasiswaUserId2,
                'nama_mahasiswa' => 'Mahasiswa Test B',
                'jenis_kelamin_mahasiswa' => 'Perempuan',
                'tanggal_lahir_mahasiswa' => '2000-01-01',
                'telepon_mahasiswa' => '081234567891',
                'profile_img_mahasiswa' => null,
                'nim_mahasiswa' => '2141720118',
                'created_at' => $now,
                'created_by' => 0
            ], 
            [
                'mahasiswa_id' => $mahasiswaId3,
                'user_id' => $mahasiswaUserId3,
                'nama_mahasiswa' => 'Mahasiswa Test C',
                'jenis_kelamin_mahasiswa' => 'Laki-Laki',
                'tanggal_lahir_mahasiswa' => '2000-01-02',
                'telepon_mahasiswa' => '081234567892',
                'profile_img_mahasiswa' => null,
                'nim_mahasiswa' => '2141720117',
                'created_at' => $now,
                'created_by' => 0
            ],
            [
                'mahasiswa_id' => $mahasiswaId4,
                'user_id' => $mahasiswaUserId4,
                'nama_mahasiswa' => 'Mahasiswa Test D',
                'jenis_kelamin_mahasiswa' => 'Perempuan',
                'tanggal_lahir_mahasiswa' => '2000-01-03',
                'telepon_mahasiswa' => '081234567893',
                'profile_img_mahasiswa' => null,
                'nim_mahasiswa' => '2141720116',
                'created_at' => $now,
                'created_by' => 0
            ],
            [
                'mahasiswa_id' => $mahasiswaId5,
                'user_id' => $mahasiswaUserId5,
                'nama_mahasiswa' => 'Mahasiswa Test E',
                'jenis_kelamin_mahasiswa' => 'Laki-Laki',
                'tanggal_lahir_mahasiswa' => '2000-01-04',
                'telepon_mahasiswa' => '081234567894',
                'profile_img_mahasiswa' => null,
                'nim_mahasiswa' => '2141720115',
                'created_at' => $now,
                'created_by' => 0
            ]
        ]);



        DB::table('d_user')->insert([
            [
                'user_id' => $profesionalUserId1,
                'email'=> 'profesional_a@example.com', 
                'password' => Hash::make('password123'),
                'status' => 'Active',
                'role' => 'Profesional',
                'created_at' => now(),
                'created_by' => 0,
            ],
            [
                'user_id' => $profesionalUserId2,
                'email'=> 'profesional_b@example.com',
                'password' => Hash::make('password123'),
                'status' => 'Active',
                'role' => 'Profesional',
                'created_at' => now(),
                'created_by' => 0,
            ],
            [
                'user_id' => $profesionalUserId3,
                'email'=> 'profesioanl_c@example.com',
                'password' => Hash::make('password123'),
                'status' => 'Active',
                'role' => 'Profesional',
                'created_at' => now(),
                'created_by' => 0,
            ],
            [
                'user_id' => $profesionalUserId4,
                'email'=> 'profesional_d@example.com',
                'password' => Hash::make('password123'),
                'status' => 'Active',
                'role' => 'Profesional',
                'created_at' => now(),
                'created_by' => 0,
            ],
            [
                'user_id' => $profesionalUserId5,
                'email'=> 'profesional_e@example.com',
                'password' => Hash::make('password123'),
                'status' => 'Active',
                'role' => 'Profesional',
                'created_at' => now(),
                'created_by' => 0,
            ],
        ]);

        DB::table('d_profesional')->insert([
            [
                'profesional_id' => $profesionalId1,
                'user_id' => $profesionalUserId1,
                'nama_profesional' => 'Profesional Test A',
                'jenis_kelamin_profesional' => 'Laki-Laki',
                'tanggal_lahir_profesional' => '1995-01-01',
                'telepon_profesional' => '081234567891',
                'profile_img_profesional' => null,
                'created_at' => $now,
                'created_by' => 0
            ], 
            [
                'profesional_id' => $profesionalId2,
                'user_id' => $profesionalUserId2,
                'nama_profesional' => 'Profesional Test B',
                'jenis_kelamin_profesional' => 'Perempuan',
                'tanggal_lahir_profesional' => '1995-01-01',
                'telepon_profesional' => '081234567892',
                'profile_img_profesional' => null,
                'created_at' => $now,
                'created_by' => 0
            ], 
            [
                'profesional_id' => $profesionalId3,
                'user_id' => $profesionalUserId3,
                'nama_profesional' => 'Profesional Test C',
                'jenis_kelamin_profesional' => 'Laki-Laki',
                'tanggal_lahir_profesional' => '1995-01-02',
                'telepon_profesional' => '081234567893',
                'profile_img_profesional' => null,
                'created_at' => $now,
                'created_by' => 0
            ],
            [
                'profesional_id' => $profesionalId4,
                'user_id' => $profesionalUserId4,
                'nama_profesional' => 'Profesional Test D',
                'jenis_kelamin_profesional' => 'Perempuan',
                'tanggal_lahir_profesional' => '1995-01-03',
                'telepon_profesional' => '081234567894',
                'profile_img_profesional' => null,
                'created_at' => $now,
                'created_by' => 0
            ],
            [
                'profesional_id' => $profesionalId5,
                'user_id' => $profesionalUserId5,
                'nama_profesional' => 'Profesional Test E',
                'jenis_kelamin_profesional' => 'Laki-Laki',
                'tanggal_lahir_profesional' => '1995-01-04',
                'telepon_profesional' => '081234567895',
                'profile_img_profesional' => null,
                'created_at' => $now,
                'created_by' => 0
            ],
        ]);
    }
}
