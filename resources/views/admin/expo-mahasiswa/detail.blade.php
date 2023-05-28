
@extends('admin.base.app')

@section('title')
    Expo Mahasiswa
@endsection

@section('content')
            <div class="container-xxl flex-grow-1 container-p-y">
                <h5 class="fw-bold py-3 mb-4"> Expo Saya</h5>
                <!-- notification -->
                @include("template.notification")

                <!-- Bordered Table -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Expo</h5>
                    </div>

                    <div class="card-body">
                    @if ($cekExpo == null)
                        {{-- list expo  --}}
                        <br>
                        <h5 class="mb-0">List Expo</h5>
                        <br>
                        <div class="table-responsive text-nowrap">
                            <table class="table table-bordered">
                                <thead class="thead-light">
                                    <tr class="text-center">
                                        <th width="5%">No</th>
                                        <th>Siklus</th>
                                        <th>Tanggal Mulai</th>
                                        <th>Tanggal Selesai</th>
                                        <th>Tindakan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($rs_expo->count() > 0)
                                    @foreach($rs_expo as $index => $expo)
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}.</td>
                                        <td>{{ $expo->tahun_ajaran }}</td>
                                        <td>{{ $expo->tanggal_mulai }}</td>
                                        <td>{{ $expo->tanggal_selesai }}</td>
                                        <td class="text-center">
                                            @if ($id_kelompok != null)    
                                            <form action="{{ url('/mahasiswa/expo/daftar-process') }}/{{ $expo->id }}" method="post" autocomplete="off">
                                            {{ csrf_field()}}
                                            <input type="hidden" name="id_kelompok" value="{{$id_kelompok}}">
                                            <button type="submit" class="btn btn-outline-primary btn-xs m-1 "> Daftar</button>
                                            </form>
                                            @else
                                            Untuk Mendaftar Expo, Silahkan daftar Kelompok terlebih dahulu.
                                            @endif
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
                    @else
                    <br>
                        <h5 class="mb-0">Expo Terdaftar</h5>
                        <br>
                        <div class="table-responsive text-nowrap">
                            <table class="table table-bordered">
                                <thead class="thead-light">
                                    <tr class="text-center">
                                        <th width="5%">No</th>
                                        <th>Siklus</th>
                                        <th>Tanggal Mulai</th>
                                        <th>Tanggal Selesai</th>
                                        <th>Status Pendaftaran</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($rs_expo->count() > 0)
                                    @foreach($rs_expo as $index => $expo)
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}.</td>
                                        <td>{{ $expo->tahun_ajaran }}</td>
                                        <td>{{ $expo->tanggal_mulai }}</td>
                                        <td>{{ $expo->tanggal_selesai }}</td>
                                        <td class="text-center">
                                            {{$cekExpo->status_expo}}
                                            @if ($cekExpo->status_expo == 'tidak disetujui')
                                            <a href="{{ url('/mahasiswa/expo/hapus') }}/{{ $expo->id }}" class="btn btn-outline-primary btn-xs m-1 "> Hapus</a>
                                            @endif
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
                    @endif
                    </div>
                </div>
            </div>
@endsection