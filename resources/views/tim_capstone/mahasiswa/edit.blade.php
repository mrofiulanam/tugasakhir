
@extends('tim_capstone.base.app')

@section('title')
    Mahasiswa
@endsection

@section('content')
            <div class="container-xxl flex-grow-1 container-p-y">
                <h5 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Admin /</span> Mahasiswa</h5>
                <!-- notification -->
                @include("template.notification")

                <!-- Bordered Table -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Ubah Mahasiswa</h5>
                        <small class="text-muted float-end">
                            <a href="{{ url('/admin/mahasiswa') }}" class="btn btn-secondary btn-xs float-right"><i class="bx bx-chevron-left"></i> Kembali</a>
                        </small>
                    </div>
                    <div class="card-body">
                        <form action="{{ url('/admin/mahasiswa/edit-process') }}" method="post" autocomplete="off">
                            {{ csrf_field()}}
                            <input type="hidden" name="user_id" value="{{ $mahasiswa->user_id }}">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label >Nama<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="nama" value="{{ $mahasiswa->user_name }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label>NIM<span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" name="nim" value="{{ $mahasiswa->nomor_induk }}" required>
                                    </div>
                                </div>
                            </div>
                            {{-- <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label >Angkatan<span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" name="angkatan" value="{{ $mahasiswa->angkatan }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label >IPK<span class="text-danger">*</span></label>
                                        <input type="number" step='any' class="form-control" name="ipk" value="{{ $mahasiswa->ipk }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label>SKS<span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" name="sks" value="{{ $mahasiswa->sks }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label>Jenis Kelamin <span class="text-danger">*</span></label>
                                        <select class="form-select" name="jenis_kelamin" required>
                                            <option value="" disabled selected>-- Pilih --</option>
                                            <option value="Laki - laki" @if( $mahasiswa->jenis_kelamin == 'Laki - laki' ) selected @endif>Laki - laki</option>
                                            <option value="Perempuan" @if( $mahasiswa->jenis_kelamin == 'Perempuan' ) selected @endif>Perempuan</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label>Email<span class="text-danger">*</span></label>
                                        <input type="email" class="form-control" name="email" value="{{ $mahasiswa->user_email }}" required>
                                    </div>
                                </div>
                            </div> --}}

                            <br>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </form>
                    </div>
                </div>
            </div>
@endsection
