<?php

namespace App\Http\Controllers\TimCapstone\Kelompok\PenetapanDosbing;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\TimCapstone\BaseController;
use App\Models\TimCapstone\Kelompok\PenetapanDosbing\PenetapanDosbingModel;
use Illuminate\Support\Facades\Hash;


class PenetapanDosbingController extends BaseController
{
    public function index()
    {

        // get data with pagination
        $rs_kelompok = PenetapanDosbingModel::getDataWithPagination();
        // dd($rs_kelompok);
        // data
        $data = ['rs_kelompok' => $rs_kelompok];
        // view
        return view('tim_capstone.kelompok.penetapan-dosbing.index', $data);
    }

    public function detailKelompok($id)
    {

        // get data with pagination
        $kelompok = PenetapanDosbingModel::getDataById($id);
        $rs_topik = PenetapanDosbingModel::getTopik();
        $rs_mahasiswa = PenetapanDosbingModel::listKelompokMahasiswa($id);
        $rs_dosbing = PenetapanDosbingModel::getAkunDosbingKelompok($id);
        $rs_dosbing1 = PenetapanDosbingModel::getDataDosbing1();
        $rs_dosbing2 = PenetapanDosbingModel::getDataDosbing2();

        foreach ($rs_dosbing as $dosbing) {

            if ($dosbing->user_id == $kelompok->id_dosen_pembimbing_1) {
                $dosbing->jenis_dosen = 'Pembimbing 1';
                $dosbing->status_dosen = $kelompok->status_dosen_pembimbing_1;
            } else if ($dosbing->user_id == $kelompok->id_dosen_pembimbing_2) {
                $dosbing->jenis_dosen = 'Pembimbing 2';
                $dosbing->status_dosen = $kelompok->status_dosen_pembimbing_2;
            }

        }

        // check
        if (empty($kelompok)) {
            // flash message
            session()->flash('danger', 'Data tidak ditemukan.');
            return redirect('/admin/kelompok');
        }

        // data
        $data = [
            'kelompok' => $kelompok,
            'rs_topik' => $rs_topik,
            'rs_mahasiswa' => $rs_mahasiswa,
            'rs_dosbing' => $rs_dosbing,
            'rs_dosbing1' => $rs_dosbing1,
            'rs_dosbing2' => $rs_dosbing2,
        ];
        // dd($data);

        // view
        return view('tim_capstone.kelompok.penetapan-dosbing.detail', $data);
    }


    public function addDosenKelompok(Request $request)
    {
        // get kelompok
        $id_kelompok = $request->id_kelompok;
        $kelompok = PenetapanDosbingModel::getKelompokById($id_kelompok);

        // check if the selected position is 'pembimbing 1'
        if ($request->status_dosen == "pembimbing 1") {
            // check if pembimbing 1 slot is available and not the same as the selected dosen
            if ($kelompok->id_dosen_pembimbing_1 == null && $kelompok->id_dosen_pembimbing_2 != $request->id_dosen) {
                $params = [
                    'id_dosen_pembimbing_1' => $request->id_dosen,
                    'status_dosen_pembimbing_1' => 'Persetujuan Dosbing Berhasil!',
                ];
            } else {
                session()->flash('danger', 'Posisi/dosen sudah terisi!');
                return back();
            }
        }

        // check if the selected position is 'pembimbing 2'
        if ($request->status_dosen == "pembimbing 2") {
            // check if pembimbing 2 slot is available and not the same as the selected dosen
            if ($kelompok->id_dosen_pembimbing_2 == null && $kelompok->id_dosen_pembimbing_1 != $request->id_dosen) {
                $params = [
                    'id_dosen_pembimbing_2' => $request->id_dosen,
                    'status_dosen_pembimbing_2' => 'Persetujuan Dosbing Berhasil!',
                ];
            } else {
                session()->flash('danger', 'Posisi/dosen sudah terisi!');
                return back();
            }
        }

        if (PenetapanDosbingModel::updateKelompok($id_kelompok, $params)) {
            // update status kelompok if both pembimbing slots are filled

            $kelompok_updated = PenetapanDosbingModel::getKelompokById($id_kelompok);

            if ($kelompok_updated->status_dosen_pembimbing_1 == "Persetujuan Dosbing Berhasil!" && $kelompok_updated->status_dosen_pembimbing_2 == "Persetujuan Dosbing Berhasil!") {
                $paramsStatusKelompok = [
                    'status_kelompok' => "Menunggu Validasi Kelompok!"
                ];

                PenetapanDosbingModel::updateKelompok($id_kelompok, $paramsStatusKelompok);
            }
            // flash message
            session()->flash('success', 'Data berhasil disimpan.');
            return back();
        } else {
            // flash message
            session()->flash('danger', 'Data gagal disimpan.');
            return back();
        }
    }

    public function deleteDosenKelompok($id_dosen, $id_kelompok)
    {

        $kelompok = PenetapanDosbingModel::getKelompokById($id_kelompok);

        $params ="";

        if ($id_dosen == $kelompok -> id_dosen_pembimbing_1) {
            $params = [
                'id_dosen_pembimbing_1' => null,
                'status_dosen_pembimbing_1' => null,
                'status_kelompok' => "Menunggu Persetujuan Dosbing!"
            ];
        } else if ($id_dosen == $kelompok -> id_dosen_pembimbing_2) {
            $params = [
                'id_dosen_pembimbing_2' => null,
                'status_dosen_pembimbing_2' => null,
                'status_kelompok' => "Menunggu Persetujuan Dosbing!"
            ];
        } else {
            $params = [

            ];

        }

        // dd($params);
        // get data
        $dosen = PenetapanDosbingModel::updateKelompok($id_kelompok, $params);

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



    public function deleteKelompokProcess($id)
    {

        // get data
        $kelompok = PenetapanDosbingModel::getDataById($id);

        // if exist
        if (!empty($kelompok)) {
            $cekMhs=PenetapanDosbingModel::getKelompokMhsAll($kelompok->id);
            foreach ($cekMhs as $key => $mhs) {
                PenetapanDosbingModel::deleteKelompokMhs($mhs->id_mahasiswa);
            }

            if (PenetapanDosbingModel::deleteJadwalSidangProposal($kelompok->id)) {
                if (PenetapanDosbingModel::deleteKelompok($kelompok->id)) {
                    // flash message
                    session()->flash('success', 'Data berhasil dihapus.');
                    return back();
                } else {
                    // flash message
                    session()->flash('danger', 'Data gagal dihapus.');
                    return back();
                }
            } else {
                if (PenetapanDosbingModel::deleteKelompok($kelompok->id)) {
                    // flash message
                    session()->flash('success', 'Data berhasil dihapus.');
                    return back();
                } else {
                    // flash message
                    session()->flash('danger', 'Data gagal dihapus.');
                    return back();
                }
            }
            // process

        } else {
            // flash message
            session()->flash('danger', 'Data tidak ditemukan.');
            return back();
        }
    }


    public function editKelompokProcess(Request $request)
    {

        // Validate & auto redirect when fail
        $rules = [
            'id' => 'required',
        ];

        $this->validate($request, $rules);

        // params
        $params = [
            "id_topik" => $request->topik,
            'modified_by'   => Auth::user()->user_id,
            'modified_date'  => date('Y-m-d H:i:s')
        ];

        // process
        if (PenetapanDosbingModel::updateKelompok($request->id, $params)) {
            // flash message
            session()->flash('success', 'Data berhasil disimpan.');
            return back();
        } else {
            // flash message
            session()->flash('danger', 'Data gagal disimpan.');
            return back();
        }
    }

}
