@extends('admin.base.app')

@section('title')
Dosen
@endsection

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <h5 class="fw-bold py-3 mb-4">Dosen</h5>
    <!-- notification -->
    @include("template.notification")

    <!-- Bordered Table -->
    <div class="card">
        <h5 class="card-header">Data Dosen</h5>

        <div class="card-body">
            
            <br>
            <div class="row justify-content-end mb-2">
                <div class="col-auto ">
                    <a href="{{ url('/admin/dosen/add') }}" class="btn btn-primary btn-xs float-right"><i class="fas fa-plus"></i> Tambah Data</a>
                </div>
            </div>

            <div class="table-responsive text-nowrap">
                <table class="table table-bordered">
                    <thead class="thead-light">
                        <tr class="text-center">
                            <th width="5%">No</th>
                            <th>Nama</th>
                            <th>Jabatan</th>
                            <th width="18%">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($dt_dosen->count() > 0)
                        @foreach($dt_dosen as $index => $dosen)
                        <tr>
                            <td class="text-center">{{ $index + $dt_dosen->firstItem() }}.</td>
                            <td>{{ $dosen->user_name }}</td>
                            <td>{{ $dosen->role_name }}</td>
                            <td class="text-center">
                                <a href="{{ url('/admin/dosen/detail') }}/{{ $dosen->user_id }}" class="btn btn-outline-secondary btn-xs m-1 "> Detail</a>
                                <a href="{{ url('/admin/dosen/edit') }}/{{ $dosen->user_id }}" class="btn btn-outline-warning btn-xs m-1 "> Ubah</a>
                                <a href="{{ url('/admin/dosen/delete-process') }}/{{ $dosen->user_id }}" class="btn btn-outline-danger btn-xs m-1 " onclick="return confirm('Apakah anda ingin menghapus {{ $dosen->user_name }} ?')"> Hapus</a>
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
                    <p>Menampilkan {{ $dt_dosen->count() }} dari total {{ $dt_dosen->total() }} data.</p>
                </div>
                <div class="col-auto ">
                    {{ $dt_dosen->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@endsection