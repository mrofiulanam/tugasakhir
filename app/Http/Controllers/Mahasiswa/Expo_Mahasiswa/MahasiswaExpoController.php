<?php

namespace App\Http\Controllers\Mahasiswa\Expo_Mahasiswa;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\TimCapstone\BaseController;
use App\Models\Mahasiswa\Expo_Mahasiswa\MahasiswaExpoModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MahasiswaExpoController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    //coba comment

    public function index()
    {

        $cekExpo = MahasiswaExpoModel::cekExpo();

        $kelompok = MahasiswaExpoModel::pengecekan_kelompok_mahasiswa(Auth::user()->user_id);

        if ($kelompok != null) {
            $akun_mahasiswa = MahasiswaExpoModel::getAkunByID(Auth::user()->user_id);
            $siklusSudahPunyaKelompok = MahasiswaExpoModel::checkApakahSiklusMasihAktif($akun_mahasiswa ->id_siklus);
            $id_kelompok = MahasiswaExpoModel::idKelompok(Auth::user()->user_id);
            // get data expo
            $rs_expo = MahasiswaExpoModel::getDataExpo();

            if ($rs_expo != null) {
                $waktuExpo = strtotime($rs_expo->waktu);

                $rs_expo->hari_expo = strftime('%A', $waktuExpo);
                $rs_expo->hari_expo = $this->convertDayToIndonesian($rs_expo->hari_expo);
                $rs_expo->tanggal_expo = date('d-m-Y', $waktuExpo);
                $rs_expo->waktu_expo = date('H:i:s', $waktuExpo);

                $tanggalSelesai = strtotime($rs_expo->tanggal_selesai);

                $rs_expo->hari_batas = strftime('%A', $tanggalSelesai);
                $rs_expo->hari_batas = $this->convertDayToIndonesian($rs_expo->hari_batas);
                $rs_expo->tanggal_batas = date('d-m-Y', $tanggalSelesai);
                $rs_expo->waktu_batas = date('H:i:s', $tanggalSelesai);
            }

            $kelengkapanExpo = MahasiswaExpoModel::kelengkapanExpo();

            // data
            $data = [
                'rs_expo' => $rs_expo,
                'kelompok' => $kelompok,
                'kelengkapan'=>$kelengkapanExpo,
                'siklus_sudah_punya_kelompok' => $siklusSudahPunyaKelompok,

            ];
        } else{
            $data = [

                'kelompok' => $kelompok,

            ];
        }



        // view
        return view('mahasiswa.expo-mahasiswa.detail', $data);
    }

    public function daftarExpo(Request $request)
    {
        // Validasi user
        if (!$request->user()) {
            return redirect()->back()->with('danger', 'Gagal mendapatkan data user!');
        }

        // Validasi kelompok mahasiswa
        $kelompok = MahasiswaExpoModel::pengecekan_kelompok_mahasiswa($request->user()->user_id);
        if (!$kelompok) {
            return redirect()->back()->with('danger', 'Anda belum memiliki kelompok!');
        }

        // Validasi berkas Capstone
        if (!$kelompok->file_name_c500) {
            return redirect()->back()->with('danger', 'Lengkapi Dokumen Capstone!');
        }

        // Persiapan data pendaftaran expo
        $params = [
            'id_kelompok' => $kelompok->id,
            'id_expo' => $request -> id_expo,
            'status' => 'Menunggu Validasi Expo!',
            'created_by' => $request->user()->user_id,
            'created_date' => now(),
        ];

        // Proses penyimpanan data pendaftaran expo
        if (DB::table('pendaftaran_expo')->updateOrInsert(['id_kelompok' => $kelompok->id], $params)) {
            // Update status kelompok dan mahasiswa
            $kelompokParams = [
                'link_berkas_expo' => $request->link_berkas_expo,
                'status_kelompok' => "Menunggu Validasi Expo!"
            ];
            MahasiswaExpoModel::updateKelompokById($kelompok->id_kelompok, $kelompokParams);

            $kelompokMHSParams = [
                'judul_ta_mhs' => $request->judul_ta_mhs,
                'status_individu' => "Menunggu Validasi Expo!"
            ];
            $statusDaftar = MahasiswaExpoModel::updateKelompokMHS($request->user()->user_id, $kelompokMHSParams);

            return redirect()->back()->with('success', 'Berhasil mendaftarkan expo!');
        } else {
            return redirect()->back()->with('danger', 'Gagal menyimpan data pendaftaran expo!');
        }
    }


    private function convertDayToIndonesian($day)
    {
        // Mapping nama hari ke bahasa Indonesia
        $dayMappings = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
        ];

        // Cek apakah nama hari ada di dalam mapping
        return array_key_exists($day, $dayMappings) ? $dayMappings[$day] : $day;
    }
}
