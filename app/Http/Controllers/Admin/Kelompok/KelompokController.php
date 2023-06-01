<?php

namespace App\Http\Controllers\Admin\Kelompok;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Admin\BaseController;
use App\Models\Admin\Kelompok\KelompokModel;
use Illuminate\Support\Facades\Hash;


class KelompokController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    //coba comment

    public function index()
    {
        // authorize
        KelompokModel::authorize('R');
        // dd(KelompokModel::getData());

        // get data with pagination
        $rs_kelompok = KelompokModel::getDataWithPagination();
        // dd($rs_kelompok);
        // data
        $data = ['rs_kelompok' => $rs_kelompok];
        // view
        return view('admin.kelompok.index', $data);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function addMahasiswaKelompok(Request $request)
    {
        // authorize
        KelompokModel::authorize('U');

        // params
        $params = [
            'id_kelompok' => $request->id_kelompok,
            'modified_by'   => Auth::user()->user_id,
            'modified_date'  => date('Y-m-d H:i:s')
        ];
        // dd($params);
        // process
        if (KelompokModel::updateKelompokMHS($request->id_mahasiswa_nokel, $params)) {
            // flash message
            session()->flash('success', 'Data berhasil disimpan.');
            return back();
        } else {
            // flash message
            session()->flash('danger', 'Data gagal disimpan.');
            return back();
        }
    }
    public function addDosenKelompok(Request $request)
    {
        // authorize
        KelompokModel::authorize('U');
        $cekStatusDosen = KelompokModel::checkStatusDosen( $request->id_kelompok, $request->status_dosen);
        // dd($cekStatusDosen);
        if ($cekStatusDosen) {
            session()->flash('danger', 'Pembimbing Sudah ada');
            return back();
        }
        // params
        $params = [
            'id_kelompok' => $request->id_kelompok,
            'id_dosen' => $request->id_dosen,
            'status_dosen' => $request->status_dosen,
            'status_persetujuan' => 'menunggu persetujuan',
        ];
        // dd($params);
        // process
        if (KelompokModel::insertDosenKelompok($params)) {
            // flash message
            session()->flash('success', 'Data berhasil disimpan.');
            return back();
        } else {
            // flash message
            session()->flash('danger', 'Data gagal disimpan.');
            return back();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function detailKelompok($id)
    {
        // authorize
        KelompokModel::authorize('R');

        // get data with pagination
        $kelompok = KelompokModel::getDataById($id);
        $rs_mahasiswa = KelompokModel::listKelompokMahasiswa($id);
        $rs_mahasiswa_nokel = KelompokModel::listKelompokMahasiswaNokel($kelompok->id_topik);
        $rs_dosbing = KelompokModel::listDosbing($id);
        $rs_dosbing_avail = KelompokModel::listDosbingAvail();
        $rs_dosbing_avail_arr = [];

        foreach ($rs_dosbing_avail as $key => $dosbing) {
            $check_dosen_kelompok = KelompokModel::checkDosbing($id, $dosbing->user_id);
            if ($check_dosen_kelompok) {
            }
            else {
                $rs_dosbing_avail_arr[] = $dosbing;
            }
        }
        // dd($rs_dosbing_avail, $rs_dosbing_avail_arr);


        // check
        if (empty($kelompok)) {
            // flash message
            session()->flash('danger', 'Data tidak ditemukan.');
            return redirect('/admin/kelompok');
        }

        // data
        $data = [
            'kelompok' => $kelompok,
            'rs_mahasiswa' => $rs_mahasiswa,
            'rs_dosbing' => $rs_dosbing,
            'rs_mahasiswa_nokel' => $rs_mahasiswa_nokel,
            'rs_dosbing_avail' =>  $rs_dosbing_avail_arr
        ];
        // dd($data);

        // view
        return view('admin.kelompok.detail', $data);
    }

    public function deleteKelompokProcess($id)
    {
        // authorize
        KelompokModel::authorize('D');

        // get data
        $kelompok = KelompokModel::getDataById($id);

        // if exist
        if (!empty($kelompok)) {
            // process
            if (KelompokModel::deleteKelompok($kelompok->id)) {
                // flash message
                session()->flash('success', 'Data berhasil dihapus.');
                return back();
            } else {
                // flash message
                session()->flash('danger', 'Data gagal dihapus.');
                return back();
            }
        } else {
            // flash message
            session()->flash('danger', 'Data tidak ditemukan.');
            return back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteKelompokMahasiswaProcess($id_mahasiswa, $id)
    {
        // authorize
        KelompokModel::authorize('D');

        // get data
        $mahasiswa = KelompokModel::getKelompokMhs($id_mahasiswa, $id);

        // if exist
        if (!empty($mahasiswa)) {
            // process
            if (KelompokModel::deleteKelompokMhs($mahasiswa->id)) {
                // flash message
                session()->flash('success', 'Data berhasil dihapus.');
                return back();
            } else {
                // flash message
                session()->flash('danger', 'Data gagal dihapus.');
                return back();
            }
        } else {
            // flash message
            session()->flash('danger', 'Data tidak ditemukan.');
            return back();
        }
    }

    public function deleteKelompokDosenProcess($id_dosen, $id)
    {
        // authorize
        KelompokModel::authorize('D');

        // get data
        $dosen = KelompokModel::deleteDosenMhs($id_dosen, $id);

        // if exist
        if (!empty($dosen)) {
            // process
                // flash message
                session()->flash('success', 'Data berhasil dihapus.');
                return back();
        } else {
            // flash message
            session()->flash('danger', 'Data tidak ditemukan.');
            return back();
        }
    }

    /**
     * Search data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        // authorize
        KelompokModel::authorize('R');

        // data request
        $nama = $request->nama;

        // new search or reset
        if ($request->action == 'search') {
            // get data with pagination
            $rs_kelompok = KelompokModel::getDataSearch($nama);
            // data
            $data = ['rs_kelompok' => $rs_kelompok, 'nama' => $nama];
            // view
            return view('admin.kelompok.index', $data);
        } else {
            return redirect('/admin/kelompok');
        }
    }
}
