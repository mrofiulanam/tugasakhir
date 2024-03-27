<?php

namespace App\Models\Api\Mahasiswa\Dokumen;

use App\Models\Api\ApiBaseModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ApiDokumenModel extends ApiBaseModel
{
    public static function getById($id) {
        return DB::table('app_user')->where('user_id', $id)->first();
    }

    public static function pengecekan_kelompok_mahasiswa($user_id)
    {
        return DB::table('kelompok_mhs as a')
            ->select('a.id_kelompok', 'b.*', 'c.nama as nama_topik', 'd.user_name as pengusul_kelompok')
            ->leftJoin('kelompok as b', 'a.id_kelompok', 'b.id')
            ->leftJoin('topik as c', 'a.id_topik_mhs', 'c.id')
            ->leftJoin('app_user as d', 'd.user_id', 'b.created_by')
            ->where('a.id_mahasiswa', $user_id)
            ->orderBy('a.created_date', 'desc') // Urutkan berdasarkan created_date secara descending
            ->first();
    }

    public static function getKelompokFile($id_kelompok)
    {
        return DB::table('kelompok as a')
            ->where('a.id', $id_kelompok)
            ->first();
    }
    // get all data
    public static function fileMHS($user_id)
    {
        return DB::table('kelompok_mhs as a')
            ->select('a.id as id_kel_mhs', 'a.id_mahasiswa', 'a.file_name_makalah', 'a.file_path_makalah','a.file_name_laporan_ta', 'a.file_path_laporan_ta','b.*')
            ->join('kelompok as b','a.id_kelompok','b.id')
            ->join('siklus as c' ,'a.id_siklus', 'c.id')
            ->where('c.status','aktif')
            ->where('a.id_mahasiswa', $user_id)
            ->first();
    }

    public static function uploadFileMHS($id_mahasiswa, $params)
    {
        return DB::table('kelompok_mhs')->where('id_mahasiswa', $id_mahasiswa)->update($params);
    }

    public static function uploadFileKel($id_kelompok, $params)
    {
        return DB::table('kelompok')->where('id', $id_kelompok)->update($params);
    }

}
