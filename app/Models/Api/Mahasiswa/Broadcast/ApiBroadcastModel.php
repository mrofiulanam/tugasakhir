<?php

namespace App\Models\Api\Mahasiswa\Broadcast;

use App\Models\Api\ApiBaseModel;
use Illuminate\Support\Facades\DB;

class ApiBroadcastModel extends ApiBaseModel
{
    // get all data
    public static function getData()
    {
        return DB::table('broadcast')
            ->get();
    }

    public static function getDataWithPagination()
    {
        return DB::table('broadcast')->orderBy('created_date', 'desc')->paginate(10);
    }

    public static function getDataWithHomePagination()
    {
        return DB::table('broadcast')->orderBy('created_date', 'desc')->paginate(3);
    }



    // get search
    public static function getDataSearch($search)
    {
        return DB::table('app_user as a')
            ->select('a.*', 'c.role_name')
            ->join('app_role as c', 'a.role_id', 'c.role_id')
            ->where('a.role_id', '03') // Filter berdasarkan role_id di tabel app_user
            ->where('a.user_name', 'LIKE', "%" . $search . "%")
            ->paginate(20)->withQueryString();

    }

    // get data by id
    public static function getDataById($id)
    {
        return DB::table('broadcast')->where('id', $id)->first();
    }

}