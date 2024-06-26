@extends('tim_capstone.base.app')

@section('title')
    Balancing Dosbing Kelompok
@endsection

@section('content')

    <div class="container-xxl flex-grow-1 container-p-y">
        <h5 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Admin /</span> Balancing Dosbing Kelompok</h5>
        <!-- notification -->
        @include('template.notification')

        <!-- Bordered Table -->
        <div class="card">
            <h5 class="card-header">Data Balancing Dosbing Kelompok</h5>

            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-md-12">
                        <form class="form-inline" action="{{ url('/tim-capstone/balancing-dosbing-kelompok/search') }}"
                            method="get" autocomplete="off">
                            <div class="row">
                                <div class="col-auto mt-1">
                                    <input class="form-control mr-sm-2" type="search" name="nama"
                                        value="{{ !empty($nama) ? $nama : '' }}" placeholder="Nama" minlength="1" required>
                                </div>
                                <div class="col-auto mt-1">
                                    <button class="btn btn-outline-secondary ml-1" type="submit" name="action"
                                        value="search">
                                        <i class="bx bx-search-alt-2"></i>
                                    </button>

                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <br>
                <div class="row">
                    <form action="{{ url('/tim-capstone/balancing-dosbing-kelompok/filter-siklus') }}" method="get"
                        autocomplete="off">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-md-8"> <!-- Menyesuaikan dengan lebar yang diinginkan -->
                                <div class="mb-3">
                                    <select class="form-select select-2" name="id_siklus" required>
                                        <option value="" disabled selected> -- Filter Berdasarkan Siklus -- </option>
                                        @foreach ($rs_siklus as $s)
                                            <option value="{{ $s->id }}"
                                                {{ isset($siklus) && $siklus->id == $s->id ? 'selected' : '' }}>
                                                {{ $s->nama_siklus }}
                                            </option>
                                        @endforeach
                                    </select>

                                </div>
                            </div>
                            <div class="col-md-4"> <!-- Menyesuaikan dengan lebar yang diinginkan -->
                                <button type="submit" class="btn btn-primary float-end" name="action"
                                    value="filter">Terapkan Filter</button>
                            </div>
                        </div>
                    </form>

                </div>

                <div class="table-responsive text-nowrap">
                    <table class="table table-bordered">
                        <thead class="thead-light">
                            <tr class="text-center">
                                <th width="5%">No</th>
                                <th>Nama</th>
                                <th>Belum Selesai</th>
                                <th>Sudah Selesai</th>
                                <th width="18%">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($dt_dosen->count() > 0)
                                @foreach ($dt_dosen as $index => $dosen)
                                    <tr>
                                        <td class="text-center">{{ $index + $dt_dosen->firstItem() }}.</td>
                                        <td>{{ $dosen->user_name }}</td>
                                        <td>{{ $dosen->jumlah_kelompok_aktif_dibimbing }} kelompok</td>
                                        <td>{{ $dosen->jumlah_kelompok_tidak_aktif_dibimbing }} kelompok</td>
                                        <td class="text-center">
                                            <a href="{{ url('/tim-capstone/balancing-dosbing-kelompok/detail') }}/{{ $dosen->user_id }}"
                                                class="btn btn-outline-secondary btn-xs m-1 "> Detail</a>

                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td class="text-center" colspan="5">Tidak ada data.</td>
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleCheckboxes = document.querySelectorAll('.toggle-status');

            toggleCheckboxes.forEach(function(checkbox) {
                checkbox.addEventListener('change', function() {
                    const id = this.getAttribute('data-id');
                    const status = this.checked ? 1 : 0;

                    // Kirim permintaan menggunakan fetch API
                    fetch(`/toggle-status/${id}/${status}`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                            },
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            console.log(data);
                            // Tindakan setelah permintaan berhasil
                        })
                        .catch(error => {
                            console.error('There was an error!', error);
                        });
                });
            });
        });
    </script>

@endsection
