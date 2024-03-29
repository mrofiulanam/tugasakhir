@extends('tim_capstone.base.app')

@section('title')
    Contoh Halaman
@endsection

@section('content')

            <div class="container-xxl flex-grow-1 container-p-y">
                <h5 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Pengaturan /</span> Contoh Halaman</h5>
                <!-- notification -->
                @include("template.notification")

                <!-- Bordered Table -->
                <div class="card">
                    <h5 class="card-header">Data Contoh Halaman</h5>

                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-md-12">
                                <form class="form-inline" action="{{ url('/admin/settings/contoh-halaman/search') }}" method="get" autocomplete="off">
                                    <div class="row">
                                        <div class="col-auto mt-1">
                                            <input class="form-control mr-sm-2" type="search" name="nama" value="{{ !empty($nama) ? $nama : '' }}" placeholder="Nama Role" minlength="3" required>
                                        </div>
                                        <div class="col-auto mt-1">
                                            <button class="btn btn-outline-secondary ml-1" type="submit" name="action" value="search">
                                                <i class="bx bx-search-alt-2"></i>
                                            </button>
                                            <button class="btn btn-outline-secondary ml-1" type="submit" name="action" value="reset">
                                                <i class="bx bx-reset"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <br>
                        <div class="row justify-content-end mb-2">
                            <div class="col-auto ">
                                <a href="{{ url('/admin/settings/contoh-halaman/add') }}" class="btn btn-primary btn-xs float-right"><i class="fas fa-plus"></i> Tambah Data</a>
                            </div>
                        </div>

                        <div class="table-responsive text-nowrap">
                            <table class="table table-bordered">
                                <thead class="thead-light">
                                    <tr class="text-center">
                                        <th width="5%">No</th>
                                        <th>Nama</th>
                                        <th>Jenis Kelamin</th>
                                        <th width="18%">Tindakan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                        @if($rs_ch->count() > 0)
                                            @foreach($rs_ch as $index => $ch)
                                            <tr>
                                                <td class="text-center">{{ $index + $rs_ch->firstItem() }}.</td>
                                                <td>{{ $ch->nama }}</td>
                                                <td>{{ $ch->jenis_kelamin }}</td>
                                                <td class="text-center">
                                                    <a href="{{ url('/admin/settings/contoh-halaman/detail') }}/{{ $ch->id }}" class="btn btn-outline-secondary btn-xs m-1 "> Detail</a>
                                                    <a href="{{ url('/admin/settings/contoh-halaman/edit') }}/{{ $ch->id }}" class="btn btn-outline-warning btn-xs m-1 "> Ubah</a>
                                                    <a href="{{ url('/admin/settings/contoh-halaman/delete-process') }}/{{ $ch->id }}" class="btn btn-outline-danger btn-xs m-1 " onclick="return confirm('Apakah anda ingin menghapus {{ $ch->nama }} ?')"> Hapus</a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td class="text-center" colspan="4">Tidak ada data.</td>
                                            </tr>
                                        @endif
                                </tbody>
                            </table>
                        </div>
                        <!-- pagination -->
                        <div class="row mt-3 justify-content-between">
                            <div class="col-auto mr-auto">
                                <p>Menampilkan {{ $rs_ch->count() }} dari total {{ $rs_ch->total() }} data.</p>
                            </div>
                            <div class="col-auto ">
                                {{ $rs_ch->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

@endsection
