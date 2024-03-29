<?php

namespace App\Models\Api\Mahasiswa\TugasAkhir;

use Illuminate\Support\Facades\DB;
use App\Models\Api\ApiBaseModel;

class ApiTugasAkhirModel extends ApiBaseModel
{

    public static function getData()
    {
        return DB::table('jadwal_sidang_ta as a')
            ->select('a.*','b.status_individu', 'b.id_dosen_penguji_ta1', 'b.status_dosen_penguji_ta1','b.id_dosen_penguji_ta2','b.status_dosen_penguji_ta2','b.judul_ta_mhs', 'b.link_upload','c.*', 'd.nama_ruang')
            ->join('kelompok_mhs as b', 'a.id_mahasiswa', 'b.id_mahasiswa')
            ->leftjoin('app_user as c','a.id_mahasiswa','c.user_id')
            ->leftjoin('ruang_sidangs as d','a.id_ruangan','d.id')
            ->get();
    }

    public static function sidangTugasAkhirByMahasiswa($id_mahasiswa)
    {
        return DB::table('jadwal_sidang_ta as a')
            ->select('a.*','b.status_individu', 'b.id_dosen_penguji_ta1', 'b.status_dosen_penguji_ta1','b.id_dosen_penguji_ta2','b.status_dosen_penguji_ta2','b.judul_ta_mhs', 'b.link_upload','c.*', 'd.nama_ruang')
            ->join('kelompok_mhs as b', 'a.id_mahasiswa', 'b.id_mahasiswa')
            ->leftjoin('app_user as c','a.id_mahasiswa','c.user_id')
            ->leftjoin('ruang_sidangs as d','a.id_ruangan','d.id')
            ->where('a.id_mahasiswa', $id_mahasiswa)
            ->first();
    }

    public static function updateKelompokMHS($id_mahasiswa, $params)
    {
        return DB::table('kelompok_mhs')
            ->where('id_mahasiswa', $id_mahasiswa)
            ->update($params);
    }

    public static function cekStatusPendaftaranSidangTA($user_id)
   {
       return DB::table('pendaftaran_sidang_ta as a')
            ->select('a.status as status_pendaftaran')
            ->where('a.id_mahasiswa', $user_id)
            ->first();
   }

   public static function getPeriodeAvailable()
    {
        return DB::table('jadwal_periode_sidang_ta as a')
        ->select('a.*')
        ->where('a.tanggal_selesai', '>', now()) // Menambahkan kondisi a.tanggal_selesai > waktu sekarang
        ->orderBy('a.tanggal_mulai', 'asc')
        ->first();
    }

    public static function getStatusPendaftaran($id_mahasiswa)
    {
        return DB::table('pendaftaran_sidang_ta as a')
            ->select('a.*')
            ->where('a.id_mahasiswa', $id_mahasiswa)
            ->first();
    }

    public static function pengecekan_kelompok_mahasiswa($user_id)
    {
       return DB::table('kelompok_mhs as a')
           ->select('a.id_kelompok', 'a.judul_ta_mhs', 'a.link_upload','b.*','c.nama as nama_topik')
           ->leftjoin('kelompok as b','a.id_kelompok','b.id')
           ->leftjoin('topik as c', 'a.id_topik_mhs', 'c.id')
           ->where('a.id_mahasiswa', $user_id)
           ->first();
    }
}
