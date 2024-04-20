<?php

namespace App\Http\Controllers\TimCapstone\SidangTA\PenjadwalanSidangTA;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\TimCapstone\BaseController;
use App\Models\TimCapstone\SidangTA\PenjadwalanSidangTA\PenjadwalanSidangTAModel;
use Illuminate\Support\Facades\Hash;

class PenjadwalanSidangTAController extends BaseController
{
    public function index()
    {

        $rs_kelompok = PenjadwalanSidangProposalModel::getDataWithPagination();

        foreach ($rs_kelompok as $kelompok) {
            $kelompok -> status_dokumen_color = $this->getStatusColor($kelompok->file_status_c100);
            $kelompok -> status_penguji1_color = $this->getStatusColor($kelompok->status_dosen_penguji_1);
            $kelompok -> status_penguji2_color = $this->getStatusColor($kelompok->status_dosen_penguji_2);
            $kelompok -> status_pembimbing2_color = $this->getStatusColor($kelompok->status_dosen_pembimbing_2);
            $kelompok -> status_sidang_color = $this->getStatusColor($kelompok->status_sidang_proposal);

        }
        // data
        $data = ['rs_kelompok' => $rs_kelompok];
        // view
        return view('tim_capstone.sidang-ta.penjadwalan-sidang-ta.index', $data);
    }

    public function detailKelompok($id)
    {

        // get data with pagination
        $kelompok = PenjadwalanSidangProposalModel::getDataById($id);
        $rs_topik = PenjadwalanSidangProposalModel::getTopik();
        $rs_mahasiswa = PenjadwalanSidangProposalModel::listKelompokMahasiswa($id);
        $rs_dosbing = PenjadwalanSidangProposalModel::getAkunDosbingKelompok($id);
        $rs_penguji_proposal = PenjadwalanSidangProposalModel::getAkunPengujiProposalKelompok($id);

        // get jadwal sidang
        $jadwal_sidang = PenjadwalanSidangProposalModel::getJadwalSidangProposal($id);
        if($jadwal_sidang != null){
            $waktuSidang = strtotime($jadwal_sidang->waktu);

            $jadwal_sidang->hari_sidang = strftime('%A', $waktuSidang);
            $jadwal_sidang->hari_sidang = $this->convertDayToIndonesian($jadwal_sidang->hari_sidang);
            $jadwal_sidang->tanggal_sidang = date('d-m-Y', $waktuSidang);
            $jadwal_sidang->waktu_sidang = date('H:i:s', $waktuSidang);

            $waktuSelesai = strtotime($jadwal_sidang->waktu_selesai);
            $jadwal_sidang->waktu_selesai = date('H:i:s', $waktuSelesai);

        }

        // penguji avaliable
        $rs_penguji = PenjadwalanSidangProposalModel::getDosenPengujiProposal($id);

        $rs_ruang_sidang = PenjadwalanSidangProposalModel::getRuangSidang();


        // dd($rs_penguji);


        foreach ($rs_dosbing as $dosbing) {

            if ($dosbing->user_id == $kelompok->id_dosen_pembimbing_1) {
                $dosbing->jenis_dosen = 'Pembimbing 1';
                $dosbing->status_dosen = $kelompok->status_dosen_pembimbing_1;
            } else if ($dosbing->user_id == $kelompok->id_dosen_pembimbing_2) {
                $dosbing->jenis_dosen = 'Pembimbing 2';
                $dosbing->status_dosen = $kelompok->status_dosen_pembimbing_2;
            }

        }

        foreach ($rs_penguji_proposal as $penguji_proposal) {

            if ($penguji_proposal->user_id == $kelompok->id_dosen_penguji_1) {
                $penguji_proposal->jenis_dosen = 'Penguji 1';
                $penguji_proposal->status_dosen = $kelompok->status_dosen_penguji_1;
            } else if ($penguji_proposal->user_id == $kelompok->id_dosen_penguji_2) {
                $penguji_proposal->jenis_dosen = 'Penguji 2';
                $penguji_proposal->status_dosen = $kelompok->status_dosen_penguji_2;
            }

        }

        // check
        if (empty($kelompok)) {
            // flash message
            session()->flash('danger', 'Data tidak ditemukan.');
            return redirect('/admin/kelompok');
        }

        $kelompok -> status_kelompok_color = $this->getStatusColor($kelompok->status_kelompok);
        $kelompok -> status_dokumen_color = $this->getStatusColor($kelompok->file_status_c100);
        $kelompok -> status_sidang_color = $this->getStatusColor($kelompok->status_sidang_proposal);

        $kelompok -> status_penguji1_color = $this->getStatusColor($kelompok->status_dosen_penguji_1);
        $kelompok -> status_penguji2_color = $this->getStatusColor($kelompok->status_dosen_penguji_2);
        $kelompok -> status_pembimbing1_color = $this->getStatusColor($kelompok->status_dosen_pembimbing_1);
        $kelompok -> status_pembimbing2_color = $this->getStatusColor($kelompok->status_dosen_pembimbing_2);

        // data
        $data = [
            'kelompok' => $kelompok,
            'rs_topik' => $rs_topik,
            'rs_mahasiswa' => $rs_mahasiswa,
            'rs_dosbing' => $rs_dosbing,
            'rs_penguji_proposal' => $rs_penguji_proposal,
            'rs_penguji' => $rs_penguji,
            'rs_ruang_sidang' => $rs_ruang_sidang,
            'jadwal_sidang' => $jadwal_sidang,

        ];
        // dd($data);

        // view
        return view('tim_capstone.sidang-proposal.penjadwalan-sidang-proposal.detail', $data);
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


    public function addDosenKelompok(Request $request)
    {
        // get kelompok
        $id_kelompok = $request->id_kelompok;
        $kelompok = PenjadwalanSidangProposalModel::getKelompokById($id_kelompok);

        // check if the selected position is 'penguji 1'
        if ($request->status_dosen == "penguji 1") {
            // check if penguji 1 slot is available and not the same as the selected dosen
            if ($kelompok->id_dosen_penguji_1 == null && $kelompok->id_dosen_penguji_2 != $request->id_dosen) {
                $params = [
                    'id_dosen_penguji_1' => $request->id_dosen,
                    'status_dosen_penguji_1' => 'Menunggu Persetujuan Penguji!',
                ];

                if ($request->id_dosen == $kelompok -> id_dosen_pembimbing_1 || $request->id_dosen == $kelompok -> id_dosen_pembimbing_2) {
                    session()->flash('danger', 'Dosen penguji tidak boleh sama dengan dosen pembimbing!');
                    return back();
                }

            } else {
                session()->flash('danger', 'Posisi/dosen sudah terisi!');
                return back();
            }
        }

        // check if the selected position is 'penguji 2'
        if ($request->status_dosen == "penguji 2") {
            // check if penguji 2 slot is available and not the same as the selected dosen
            if ($kelompok->id_dosen_penguji_2 == null && $kelompok->id_dosen_penguji_1 != $request->id_dosen) {
                $params = [
                    'id_dosen_penguji_2' => $request->id_dosen,
                    'status_dosen_penguji_2' => 'Menunggu Persetujuan Penguji!',
                ];

                if ($request->id_dosen == $kelompok -> id_dosen_pembimbing_1 || $request->id_dosen == $kelompok -> id_dosen_pembimbing_2) {
                    session()->flash('danger', 'Dosen penguji tidak boleh sama dengan dosen pembimbing!');
                    return back();
                }
            } else {
                session()->flash('danger', 'Posisi/dosen sudah terisi!');
                return back();
            }
        }


        if (PenjadwalanSidangProposalModel::updateKelompok($id_kelompok, $params)) {
            // update status kelompok if both pembimbing slots are filled

            $kelompok_updated = PenjadwalanSidangProposalModel::getKelompokById($id_kelompok);

            if ($kelompok_updated->id_dosen_penguji_1 != null && $kelompok_updated->id_dosen_penguji_2 != null) {
                $paramsStatusKelompok = [
                    'status_sidang_proposal' => "Penguji Proposal Ditetapkan!",
                    'status_dosen_pembimbing_2' => "Menunggu Persetujuan Pembimbing!"
                ];

                PenjadwalanSidangProposalModel::updateKelompok($id_kelompok, $paramsStatusKelompok);
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

        $kelompok = PenjadwalanSidangProposalModel::getKelompokById($id_kelompok);

        $params ="";

        if ($id_dosen == $kelompok -> id_dosen_penguji_1) {
            $params = [
                'id_dosen_penguji_1' => null,
                'status_dosen_penguji_1' => null,
                'status_sidang_proposal' => "Menunggu Dijadwalkan Sidang!",
                'status_dosen_pembimbing_2' => "Menunggu Persetujuan C100!"

            ];
        } else if ($id_dosen == $kelompok -> id_dosen_penguji_2) {
            $params = [
                'id_dosen_penguji_2' => null,
                'status_dosen_penguji_2' => null,
                'status_sidang_proposal' => "Menunggu Dijadwalkan Sidang!",
                'status_dosen_pembimbing_2' => "Menunggu Persetujuan C100!"
            ];
        } else {
            $params = [

            ];
        }

        $dosen = PenjadwalanSidangProposalModel::updateKelompok($id_kelompok, $params);

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

    public function addJadwalProcess(Request $request)
    {

        // Memasukkan atau memperbarui jadwal sidang proposal
        $params = [
            'siklus_id' => $request->siklus_id,
            'id_kelompok' => $request->id_kelompok,
            'waktu' => $request->waktu,
            'waktu_selesai' => $request->waktu_selesai,
            'id_dosen_pembimbing_2' => $request->id_dosen_pembimbing_2,
            'id_dosen_penguji_1' => $request->id_dosen_penguji_1,
            'id_dosen_penguji_2' => $request->id_dosen_penguji_2,
            'ruangan_id' => $request->ruangan_id,
            'created_by' => Auth::user()->user_id,
            'created_date' => now()
        ];

        // Mendapatkan data jadwal sidang proposal berdasarkan id_kelompok
        $existingJadwal = PenjadwalanSidangProposalModel::getJadwalSidangProposal($request->id_kelompok);

        // Jika data sudah ada, lakukan update; jika tidak, lakukan insert
        if ($existingJadwal != null) {
            // Melakukan update jadwal sidang proposal
            $update = PenjadwalanSidangProposalModel::updateJadwalSidangProposal($existingJadwal->id, $params);
            if ($update) {
                session()->flash('success', 'Data berhasil diperbarui.');
            } else {
                session()->flash('danger', 'Gagal memperbarui data.');
            }
        } else {
             // Validasi pilihan dosen penguji
            if ($request->id_dosen_penguji_1 == null || $request->id_dosen_penguji_2 == null) {
                session()->flash('danger', 'Dosen penguji 1 dan penguji 2 harus dipilih.');
                return back()->withInput();
            }

            // Validasi overlapping schedule
            $overlap = PenjadwalanSidangProposalModel::checkOverlap($request->waktu, $request->waktu_selesai, $request->ruangan_id);
            if ($overlap) {
                session()->flash('danger', 'Ruangan tersebut sudah terjadwal pada waktu yang sama.');
                return back()->withInput();
            }

            // Validasi waktu mulai dan selesai
            if ($request->waktu >= $request->waktu_selesai) {
                session()->flash('danger', 'Waktu mulai harus lebih awal dari waktu selesai.');
                return back()->withInput();
            }
            // Melakukan insert jadwal sidang proposal baru
            $insert = PenjadwalanSidangProposalModel::insertJadwalSidangProposal($params);
            if ($insert) {
                session()->flash('success', 'Data berhasil disimpan.');
            } else {
                session()->flash('danger', 'Data gagal disimpan.');
            }
        }

        // Update status kelompok
        $paramsStatusKelompok = [
            'status_sidang_proposal' => 'Menunggu Persetujuan Penguji!',
            'status_dosen_pembimbing_2' => 'Menunggu Persetujuan Pembimbing!',
            'status_dosen_penguji_1' => 'Menunggu Persetujuan Penguji!',
            'status_dosen_penguji_2' => 'Menunggu Persetujuan Penguji!'
        ];
        PenjadwalanSidangProposalModel::updateKelompok($request->id_kelompok, $paramsStatusKelompok);

        return redirect('/admin/penjadwalan-sidang-proposal');
    }
}