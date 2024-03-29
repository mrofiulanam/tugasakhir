@extends('tim_capstone.base.app')

@section('title')
    Dokumen
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h5 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Mahasiswa /</span> Dokumen</h5>

        <!-- notification -->
        @include('template.notification')

        <!-- Bordered Table -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Detail Dokumen</h5>
            </div>

            @if ($akun_mahasiswa == null)
                <div class="card-body">
                    <h6>
                        Anda belum mendaftar capstone!
                    </h6>
                </div>
            @elseif ($kelompok == null)
                <div class="card-body">
                    <h6>
                        Anda belum memiliki kelompok!
                    </h6>
                </div>
            @elseif ($kelompok->nomor_kelompok == null)
                <div class="card-body">
                    <h6>
                        Kelompok Anda belum valid!
                    </h6>
                </div>
            @else
                <div class="row">
                    <!-- upload makalah -->
                    <div class="col-md-6">
                        <div class="card-body">
                            <div class="card">
                                <h5 class="card-header">Upload Makalah</h5>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <i class='bx bxs-file-doc bx-lg'></i>
                                        </div>
                                        <div class="col-md-10">
                                            <form action="{{ url('/mahasiswa/dokumen/upload-makalah') }}" method="post"
                                                autocomplete="off" enctype="multipart/form-data">
                                                {{ csrf_field() }}
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="mb-1">
                                                            <input type="hidden" class="form-control" name="id_kel_mhs"
                                                                value="{{ $file_mhs->id_kel_mhs }}" required>
                                                            <input type="file" class="form-control" name="makalah"
                                                                value="{{ old('makalah', $file_mhs->file_name_makalah) }}"
                                                                required>
                                                        </div>
                                                        @if ($file_mhs->file_name_makalah)
                                                            <input type="text" class="form-control"
                                                                value="{{ $file_mhs->file_name_makalah }}" readonly>
                                                            <!-- <button type="submit" class="btn btn-primary float-end m-1 btn-sm" onclick="return confirm('Apakah anda ingin mengubahnya?')">Ubah</button> -->
                                                            <button type="button"
                                                                class="btn btn-primary float-end m-1 btn-sm"
                                                                onclick="confirmUpdate1()">Ubah</button>
                                                            <script>
                                                                function confirmUpdate1() {
                                                                    // Use SweetAlert
                                                                    Swal.fire({
                                                                        title: 'Apakah Anda yakin ingin mengubah file makalah?',
                                                                        icon: 'question',
                                                                        showCancelButton: true,
                                                                        confirmButtonColor: '#3085d6',
                                                                        cancelButtonColor: '#d33',
                                                                        confirmButtonText: 'Ya, ubah',
                                                                        cancelButtonText: 'Batal'
                                                                    }).then((result) => {
                                                                        if (result.isConfirmed) {
                                                                            // Continue with the update process
                                                                            document.querySelector('form').submit();
                                                                        }
                                                                    });
                                                                }
                                                            </script>
                                                            <a href="{{ url('/file/mahasiswa/makalah') }}/{{ $file_mhs->file_name_makalah }}"
                                                                class="btn btn-primary float-end m-1 btn-sm">Download</a>
                                                        @else
                                                            <button type="submit"
                                                                class="btn btn-primary float-end m-1 btn-sm">Simpan</button>
                                                        @endif
                                                    </div>
                                                </div>
                                                <br>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- upload laporan TA -->
                    <div class="col-md-6">
                        <div class="card-body">
                            <div class="card">
                                <h5 class="card-header">Upload Laporan TA</h5>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <i class='bx bxs-file-doc bx-lg'></i>
                                        </div>
                                        <div class="col-md-10">
                                            <form action="{{ url('/mahasiswa/dokumen/upload-laporan') }}" method="post"
                                                autocomplete="off" enctype="multipart/form-data">
                                                {{ csrf_field() }}
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="mb-1">
                                                            <input type="hidden" class="form-control" name="id_kel_mhs"
                                                                value="{{ $file_mhs->id_kel_mhs }}" required>
                                                            <input type="file" class="form-control" name="laporan_ta"
                                                                value="{{ old('laporan_ta', $file_mhs->file_name_laporan_ta) }}"
                                                                required>
                                                        </div>
                                                        @if ($file_mhs->file_name_laporan_ta)
                                                            <input type="text" class="form-control"
                                                                value="{{ $file_mhs->file_name_laporan_ta }}" readonly>
                                                            <!-- <button type="submit" class="btn btn-primary float-end m-1 btn-sm" onclick="return confirm('Apakah anda ingin mengubahnya?')">Ubah</button> -->
                                                            <button type="button"
                                                                class="btn btn-primary float-end m-1 btn-sm"
                                                                onclick="confirmUpdate()">Ubah</button>
                                                            <script>
                                                                function confirmUpdate() {
                                                                    // Use SweetAlert
                                                                    Swal.fire({
                                                                        title: 'Apakah Anda yakin ingin mengubah file laporan TA?',
                                                                        icon: 'question',
                                                                        showCancelButton: true,
                                                                        confirmButtonColor: '#3085d6',
                                                                        cancelButtonColor: '#d33',
                                                                        confirmButtonText: 'Ya, ubah',
                                                                        cancelButtonText: 'Batal'
                                                                    }).then((result) => {
                                                                        if (result.isConfirmed) {
                                                                            // Continue with the update process
                                                                            document.querySelector('form').submit();
                                                                        }
                                                                    });
                                                                }
                                                            </script>
                                                            <a href="{{ url('/file/mahasiswa/laporan-ta') }}/{{ $file_mhs->file_name_laporan_ta }}"
                                                                class="btn btn-primary float-end m-1 btn-sm">Download</a>
                                                        @else
                                                            <button type="submit"
                                                                class="btn btn-primary float-end m-1 btn-sm">Simpan</button>
                                                        @endif
                                                    </div>
                                                </div>
                                                <br>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- file c series  --}}

                <div class="row">
                    <div class="col-md-6">
                        <div class="card-body">
                            <div class="card">
                                <h5 class="card-header">Upload C100</h5>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <i class='bx bxs-file-doc bx-lg'></i>
                                        </div>
                                        <div class="col-md-10">
                                            <form action="{{ url('/mahasiswa/dokumen/upload-c100') }}" method="post"
                                                autocomplete="off" enctype="multipart/form-data">
                                                {{ csrf_field() }}
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="mb-1">
                                                            <input type="hidden" class="form-control" name="id_kelompok"
                                                                value="{{ $file_mhs->id }}" required>
                                                            <input type="file" class="form-control" name="c100"
                                                                value="{{ old('c100', $file_mhs->file_name_c100) }}"
                                                                required>
                                                        </div>
                                                        @if ($file_mhs->file_name_c100)
                                                            <input type="text" class="form-control"
                                                                value="{{ $file_mhs->file_name_c100 }}" readonly>
                                                            <button type="submit"
                                                                class="btn btn-primary float-end m-1 btn-sm"
                                                                onclick="return confirm('Apakah anda ingin mengubahnya?')">Ubah</button>
                                                            <!-- <button type="button" class="btn btn-primary float-end m-1 btn-sm" onclick="confirmUpdate()">Ubah</button>
                                                                                                                                                        <!-- <script>
                                                                                                                                                            function confirmUpdate() {
                                                                                                                                                                // Use SweetAlert
                                                                                                                                                                Swal.fire({
                                                                                                                                                                    title: 'Apakah Anda yakin ingin mengubah file C100?',
                                                                                                                                                                    icon: 'question',
                                                                                                                                                                    showCancelButton: true,
                                                                                                                                                                    confirmButtonColor: '#3085d6',
                                                                                                                                                                    cancelButtonColor: '#d33',
                                                                                                                                                                    confirmButtonText: 'Ya, ubah',
                                                                                                                                                                    cancelButtonText: 'Batal'
                                                                                                                                                                }).then((result) => {
                                                                                                                                                                    if (result.isConfirmed) {
                                                                                                                                                                        // Continue with the update process
                                                                                                                                                                        document.querySelector('form').submit();
                                                                                                                                                                    }
                                                                                                                                                                });
                                                                                                                                                            }
                                                                                                                                                        </script> -->
                                                            <a href="{{ url('/file/kelompok/c100') }}/{{ $file_mhs->file_name_c100 }}"
                                                                class="btn btn-primary float-end m-1 btn-sm">Download</a>
                                                        @else
                                                            <button type="submit"
                                                                class="btn btn-primary float-end m-1 btn-sm">Simpan</button>
                                                        @endif
                                                    </div>
                                                </div>
                                                <br>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card-body">
                            <div class="card">
                                <h5 class="card-header">Upload C200</h5>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <i class='bx bxs-file-doc bx-lg'></i>
                                        </div>
                                        <div class="col-md-10">
                                            <form action="{{ url('/mahasiswa/dokumen/upload-c200') }}" method="post"
                                                autocomplete="off" enctype="multipart/form-data">
                                                {{ csrf_field() }}
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="mb-1">
                                                            <input type="hidden" class="form-control" name="id_kelompok"
                                                                value="{{ $file_mhs->id }}" required>
                                                            <input type="file" class="form-control" name="c200"
                                                                value="{{ old('c200', $file_mhs->file_name_c200) }}"
                                                                required>
                                                        </div>
                                                        @if ($file_mhs->file_name_c200)
                                                            <input type="text" class="form-control"
                                                                value="{{ $file_mhs->file_name_c200 }}" readonly>
                                                            <button type="submit"
                                                                class="btn btn-primary float-end m-1 btn-sm"
                                                                onclick="return confirm('Apakah anda ingin mengubahnya?')">Ubah</button>
                                                            <a href="{{ url('/file/kelompok/c200') }}/{{ $file_mhs->file_name_c200 }}"
                                                                class="btn btn-primary float-end m-1 btn-sm">Download</a>
                                                        @else
                                                            <button type="submit"
                                                                class="btn btn-primary float-end m-1 btn-sm">Simpan</button>
                                                        @endif
                                                    </div>
                                                </div>
                                                <br>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="card-body">
                            <div class="card">
                                <h5 class="card-header">Upload C300</h5>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <i class='bx bxs-file-doc bx-lg'></i>
                                        </div>
                                        <div class="col-md-10">
                                            <form action="{{ url('/mahasiswa/dokumen/upload-c300') }}" method="post"
                                                autocomplete="off" enctype="multipart/form-data">
                                                {{ csrf_field() }}
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="mb-1">
                                                            <input type="hidden" class="form-control" name="id_kelompok"
                                                                value="{{ $file_mhs->id }}" required>
                                                            <input type="file" class="form-control" name="c300"
                                                                value="{{ old('c300', $file_mhs->file_name_c300) }}"
                                                                required>
                                                        </div>
                                                        @if ($file_mhs->file_name_c300)
                                                            <input type="text" class="form-control"
                                                                value="{{ $file_mhs->file_name_c300 }}" readonly>
                                                            <button type="submit"
                                                                class="btn btn-primary float-end m-1 btn-sm"
                                                                onclick="return confirm('Apakah anda ingin mengubahnya?')">Ubah</button>
                                                            <a href="{{ url('/file/kelompok/c300') }}/{{ $file_mhs->file_name_c300 }}"
                                                                class="btn btn-primary float-end m-1 btn-sm">Download</a>
                                                        @else
                                                            <button type="submit"
                                                                class="btn btn-primary float-end m-1 btn-sm">Simpan</button>
                                                        @endif
                                                    </div>
                                                </div>
                                                <br>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card-body">
                            <div class="card">
                                <h5 class="card-header">Upload C400</h5>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <i class='bx bxs-file-doc bx-lg'></i>
                                        </div>
                                        <div class="col-md-10">
                                            <form action="{{ url('/mahasiswa/dokumen/upload-c400') }}" method="post"
                                                autocomplete="off" enctype="multipart/form-data">
                                                {{ csrf_field() }}
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="mb-1">
                                                            <input type="hidden" class="form-control" name="id_kelompok"
                                                                value="{{ $file_mhs->id }}" required>
                                                            <input type="file" class="form-control" name="c400"
                                                                value="{{ old('c400', $file_mhs->file_name_c400) }}"
                                                                required>
                                                        </div>
                                                        @if ($file_mhs->file_name_c400)
                                                            <input type="text" class="form-control"
                                                                value="{{ $file_mhs->file_name_c400 }}" readonly>
                                                            <button type="submit"
                                                                class="btn btn-primary float-end m-1 btn-sm"
                                                                onclick="return confirm('Apakah anda ingin mengubahnya?')">Ubah</button>
                                                            <a href="{{ url('/file/kelompok/c400') }}/{{ $file_mhs->file_name_c400 }}"
                                                                class="btn btn-primary float-end m-1 btn-sm">Download</a>
                                                        @else
                                                            <button type="submit"
                                                                class="btn btn-primary float-end m-1 btn-sm">Simpan</button>
                                                        @endif
                                                    </div>
                                                </div>
                                                <br>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="card-body">
                            <div class="card">
                                <h5 class="card-header">Upload C500</h5>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <i class='bx bxs-file-doc bx-lg'></i>
                                        </div>
                                        <div class="col-md-10">
                                            <form action="{{ url('/mahasiswa/dokumen/upload-c500') }}" method="post"
                                                autocomplete="off" enctype="multipart/form-data">
                                                {{ csrf_field() }}
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="mb-1">
                                                            <input type="hidden" class="form-control" name="id_kelompok"
                                                                value="{{ $file_mhs->id }}" required>
                                                            <input type="file" class="form-control" name="c500"
                                                                value="{{ old('c500', $file_mhs->file_name_c500) }}"
                                                                required>
                                                        </div>
                                                        @if ($file_mhs->file_name_c500)
                                                            <input type="text" class="form-control"
                                                                value="{{ $file_mhs->file_name_c500 }}" readonly>
                                                            <button type="submit"
                                                                class="btn btn-primary float-end m-1 btn-sm"
                                                                onclick="return confirm('Apakah anda ingin mengubahnya?')">Ubah</button>
                                                            <a href="{{ url('/file/kelompok/c500') }}/{{ $file_mhs->file_name_c500 }}"
                                                                class="btn btn-primary float-end m-1 btn-sm">Download</a>
                                                        @else
                                                            <button type="submit"
                                                                class="btn btn-primary float-end m-1 btn-sm">Simpan</button>
                                                        @endif
                                                    </div>
                                                </div>
                                                <br>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            @endif
        </div>
    </div>
@endsection
