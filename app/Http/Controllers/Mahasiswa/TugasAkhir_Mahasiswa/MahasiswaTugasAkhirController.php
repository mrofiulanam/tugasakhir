<?php

namespace App\Http\Controllers\Mahasiswa\TugasAkhir_Mahasiswa;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\TimCapstone\BaseController;
use App\Models\Mahasiswa\TugasAkhir_Mahasiswa\MahasiswaTugasAkhirModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use PDO;

class MahasiswaTugasAkhirController extends BaseController
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Get data kelompok
        $kelompok = MahasiswaTugasAkhirModel::pengecekan_kelompok_mahasiswa($user->user_id);
        $periodeAvailable = MahasiswaTugasAkhirModel::getPeriodeAvailable();
        $rs_sidang = MahasiswaTugasAkhirModel::sidangTugasAkhirByMahasiswa($user->user_id);
        $statusPendaftaran = MahasiswaTugasAkhirModel::getStatusPendaftaran($user->user_id);

        if ($kelompok != null ) {
            $akun_mahasiswa = MahasiswaTugasAkhirModel::getAkunByID(Auth::user()->user_id);
            $data_mahasiswa = MahasiswaTugasAkhirModel::getDataMahasiswa(Auth::user()->user_id);

            $rs_dosbing = MahasiswaTugasAkhirModel::getAkunDosbingKelompok($kelompok->id_kelompok);
            $rs_dospengta = MahasiswaTugasAkhirModel::getAkunDospengTa($akun_mahasiswa ->user_id);

            foreach ($rs_dosbing as $dosbing) {

                if ($dosbing->user_id == $kelompok->id_dosen_pembimbing_1) {
                    $dosbing->jenis_dosen = 'Pembimbing 1';
                    $dosbing->status_dosen = $kelompok->status_dosen_pembimbing_1;
                } else if ($dosbing->user_id == $kelompok->id_dosen_pembimbing_2) {
                    $dosbing->jenis_dosen = 'Pembimbing 2';
                    $dosbing->status_dosen = $kelompok->status_dosen_pembimbing_2;
                }
            }

            foreach ($rs_dospengta as $dospengta) {

                if ($dospengta->user_id == $data_mahasiswa->id_dosen_penguji_ta1) {
                    $dospengta->jenis_dosen = 'Penguji 1';
                    $dospengta->status_dosen = $data_mahasiswa->status_dosen_penguji_ta1;
                } else if ($dospengta->user_id == $data_mahasiswa->id_dosen_penguji_ta2) {
                    $dospengta->jenis_dosen = 'Penguji 2';
                    $dospengta->status_dosen = $data_mahasiswa->status_dosen_penguji_ta2;
                }
            }

            if ($rs_sidang == null ) {
                if ($periodeAvailable != null) {
                    // BATAS PENDAFTARAN
                    $waktubatas = strtotime($periodeAvailable->tanggal_selesai);

                    $periodeAvailable->hari_batas = strftime('%A', $waktubatas); // Day

                    // Konversi nama hari ke bahasa Indonesia
                    $periodeAvailable->hari_batas = $this->convertDayToIndonesian($periodeAvailable->hari_batas);

                    $periodeAvailable->tanggal_batas = date('d-m-Y', $waktubatas); // Date
                    $periodeAvailable->waktu_batas = date('H:i:s', $waktubatas); // Time
                }
            } else {
                if ($periodeAvailable != null) {
                    $waktubatas = strtotime($periodeAvailable->tanggal_selesai);

                    $periodeAvailable->hari_batas = strftime('%A', $waktubatas); // Day

                    // Konversi nama hari ke bahasa Indonesia
                    $periodeAvailable->hari_batas = $this->convertDayToIndonesian($periodeAvailable->hari_batas);

                    $periodeAvailable->tanggal_batas = date('d-m-Y', $waktubatas); // Date
                    $periodeAvailable->waktu_batas = date('H:i:s', $waktubatas); // Time

                    // Extract day, date, and time from the "waktu" property
                    $waktuSidang = strtotime($rs_sidang->waktu);

                    $rs_sidang->hari_sidang = strftime('%A', $waktuSidang);
                    $rs_sidang->hari_sidang = $this->convertDayToIndonesian($rs_sidang->hari_sidang);
                    $rs_sidang->tanggal_sidang = date('d-m-Y', $waktuSidang);
                    $rs_sidang->waktu_sidang = date('H:i:s', $waktuSidang);
                }

                $statusPendaftaran = MahasiswaTugasAkhirModel::getStatusPendaftaran($user->user_id);
            }

            $data = [
                'kelompok' => $kelompok,
                'rs_sidang' => $rs_sidang,
                'rs_dosbing' => $rs_dosbing,
                'rs_dospengta' => $rs_dospengta,
                'periode' => $periodeAvailable,
                'status_pendaftaran' => $statusPendaftaran,
                'akun_mahasiswa' => $akun_mahasiswa,
                'data_mahasiswa' => $data_mahasiswa

            ];
        } else {
            $data = [
                'kelompok' => $kelompok,

            ];
        }


        return view('mahasiswa.tugas-akhir.detail', $data);

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

     public function daftarTA(Request $request)
     {
        $periodeAvailable = MahasiswaTugasAkhirModel::getPeriodeAvailable();

         $user = $request->user();
         // Registration parameters
         $registrationParams = [
             'id_mahasiswa' => $user->user_id,
             'id_periode' => $periodeAvailable->id,
             'status' => 'Menunggu Validasi Jadwal!',
             'created_by' => $user->user_id,
             'created_date' => now(), // Use Laravel helper function for the current date and time
         ];

         // Use updateOrInsert to handle both insertion and updating
         if (DB::table('pendaftaran_sidang_ta')->updateOrInsert(
             ['id_mahasiswa' => $user->user_id], // The condition to check if the record already exists
             $registrationParams // The data to be updated or inserted
         )) {
             // Update kelompok mhs
             $berkasParams = [
                 'link_upload' => $request->link_upload,
                 'judul_ta_mhs' => $request->judul_ta_mhs,
                 'status_individu' => 'Menunggu Validasi Jadwal!',
             ];
             if (MahasiswaTugasAkhirModel::updateKelompokMHS($user->user_id, $berkasParams)) {

                 return redirect()->back()->with('success', 'Berhasil mendaftarkan sidang Tugas Akhir!');
             } else {
                 return redirect()->back()->with('danger', 'Gagal memperbarui data pendaftaran!');
             }
         } else {
             return redirect()->back()->with('danger', 'Gagal mendaftarkan sidang Tugas Akhir!');
         }
     }



    private function convertDayToIndonesian($day)
    {
        $dayMappings = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
        ];

        return array_key_exists($day, $dayMappings) ? $dayMappings[$day] : $day;
    }


}
