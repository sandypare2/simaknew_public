
@extends('partials.layouts.master3')

@section('title', 'Rekap Talenta | SIMAK')
@section('sub-title', 'Rekap Talenta ' )
@section('pagetitle', 'Dashboard')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/libs/@yaireo/tagify/tagify.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/css/bootstrap-datepicker.min.css"/>
@endsection

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="card card-h-100">
            <div class="card-body">
                <div class="card border border-light">
                    <div class="card-body">
                        <div class="row flex-grow-1 mb-2">
                            <div class="col-md-2 grid-margin">
                                <select id="tahuncari" name="tahuncari" class="select2 form-select form-select-sm" data-allow-clear="true">
                                    @foreach ($datatahun as $data)
                                        <option value="{{ $data }}">{{ $data }}</option>
                                    @endforeach
                                </select>                
                            </div>
                            <div class="col-md-2 grid-margin">
                                <select id="kd_areacari" name="kd_areacari" class="form-control select2">
                                    <option value="semua" selected>SEMUA</option>
                                    @foreach ($masteraream as $data)
                                        <option value="{{ $data->kd_area }}">{{ $data->nama_area }}</option>                                    
                                    @endforeach
                                </select>                            
                            </div>
                            <div class="col-md-2 grid-margin">
                                <select id="kd_jeniscari" name="kd_jeniscari" class="form-control select2">
                                    <option value="semua" selected>SEMUA</option>
                                    @foreach ($masterjenispegawai as $data)
                                        <option value="{{ $data->kd_jenis }}">{{ $data->nama_jenis }}</option>                                    
                                    @endforeach
                                </select>                            
                            </div>
                            <div class="col-md-2 grid-margin">
                                <select id="semestercari" name="semestercari" class="form-control select2">
                                    <option value="1">Semester 1</option>
                                    <option value="2">Semester 2</option>
                                </select>                            
                            </div>
                            <div class="col-md-4 grid-margin">
                                <button type="button" id="filternya" class="btn btn-info btn-sm"><i class="ri-search-line me-1"></i>Filter Data</button>
                            </div>
                        </div>
                        <div class="row flex-grow-1">
                            <div class="col-md-12 column-gap-2 d-flex flex-wrap align-items-center justify-content-start">
                                <button type="button" id="cetaknya" class="btn btn-primary btn-sm"><i class="ri-file-pdf-2-line me-1"></i>Cetak</button>
                                <div>
                                    <form action="{{ route('export-laptalentam') }}" target="_blank">
                                    <input type="hidden" class="form-control" name="tahuncarinya" id="tahuncarinya">
                                    <input type="hidden" class="form-control" name="kd_areacarinya" id="kd_areacarinya">
                                    <input type="hidden" class="form-control" name="kd_jeniscarinya" id="kd_jeniscarinya">
                                    <input type="hidden" class="form-control" name="semestercarinya" id="semestercarinya">
                                    <button type="submit" class="btn btn-success btn-sm"><i class="ri-file-excel-line me-1"></i>Download</button>
                                    </form>
                                </div>
                            </div>
                        </div> 
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="tbl_list" class="table data-table-responsive table-hover align-middle table table-nowrap w-100 custom-table">
                        <thead>
                        <tr>
                            <!-- <th rowspan="2">Aksi</th> -->
                            <th rowspan="2">Nomor Induk</th>
                            <th rowspan="2">Nama</th>
                            <th rowspan="2">Jabatan</th>
                            <th rowspan="2">Unit Kerja</th>
                            <th colspan="11">Semester 1</th>
                            <th colspan="11">Semester 2</th>
                        </tr>
                        <tr>
                            <th>Kuantitas (A)</th>
                            <th>PDP</th>
                            <th>Kuantitas</th>
                            <th>Kualitas</th>
                            <th>Waktu</th>
                            <th>NSK<br/>(Angka)</th>
                            <th>NSK<br/>(Huruf)</th>
                            <th>NKI<br/>(Angka)</th>
                            <th>NKI<br/>(Huruf)</th>
                            <th>Nilai<br/>Talenta</th>
                            <th>Nama<br/>Talenta</th>

                            <th>Kuantitas (A)</th>
                            <th>PDP</th>
                            <th>Kuantitas</th>
                            <th>Kualitas</th>
                            <th>Waktu</th>
                            <th>NSK<br/>(Angka)</th>
                            <th>NSK<br/>(Huruf)</th>
                            <th>NKI<br/>(Angka)</th>
                            <th>NKI<br/>(Huruf)</th>
                            <th>Nilai<br/>Talenta</th>
                            <th>Nama<br/>Talenta</th>
                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="datatable-loader" style="display:none;position:fixed;top:25px;left:50%;transform:translateX(-50%);z-index:1055;">
    <div class="spinner-border spinner-border-sm text-primary"></div>
        <span class="ms-2">Loading data...</span>
    </div>
</div>  

<!-- Input Data Modal -->
<div class="modal fade" id="ModalForm" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="modal-title" id="modelHeading"></h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="card-body">            
                <form id="dataForm" name="dataForm" class="form-horizontal">
                    <input type="hidden" name="id" id="id">
                    <input type="hidden" name="bulan_periode" id="bulan_periode">
                    <input type="hidden" name="batas_awal_individu" id="batas_awal_individu">
                    <input type="hidden" name="batas_akhir_individu" id="batas_akhir_individu">
                    @csrf
                    <div class="mb-2">
                        <label class="form-label-custom text-muted fs-11" for="basic-icon-default-fullname">Tahun</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control" id="tahun_periode" name="tahun_periode" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" readonly />
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label-custom text-muted fs-11" for="basic-icon-default-fullname">Nomor Induk</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control" id="nip" name="nip" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" readonly />
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label-custom text-muted fs-11" for="basic-icon-default-fullname">Nama Pegawai</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control" id="nama" name="nama" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" readonly />
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label-custom text-muted fs-11" for="basic-icon-default-fullname">Jabatan</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control" id="jabatan" name="jabatan" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" readonly />
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label-custom text-muted fs-11" for="basic-icon-default-fullname">Semester</label>
                        <div class="input-group input-group-merge">
                            <select id="semester" name="semester" class="form-control select2" readonly>
                                <option value="" selected>-</option>
                                <option value="semester1">Semester 1</option>                                    
                                <option value="semester2">Semester 2</option>                                    
                            </select>                            
                        </div>
                    </div>
                    <div class="mb-2">
                        <div class="row flex-grow-1">
                            <div class="col-md-6">
                                <label class="form-label-custom text-muted fs-11" for="basic-icon-default-fullname">Nilai Kuantitas-A (%)</label>
                                <div class="input-group input-group-merge">
                                    <input type="text" class="form-control" id="nilai_semester" name="nilai_semester" onkeypress="return isNumberKey2(event)" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-custom text-muted fs-11" for="basic-icon-default-fullname">Nilai PDP (%)</label>
                                <div class="input-group input-group-merge">
                                    <input type="text" class="form-control" id="pdp_semester" name="pdp_semester" onkeypress="return isNumberKey2(event)" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-2">
                        <div class="row flex-grow-1">
                            <div class="col-md-6">
                                <label class="form-label-custom text-muted fs-11" for="basic-icon-default-fullname">Nilai Kuantitas (%) => Positif</label>
                                <div class="input-group input-group-merge">
                                    <input type="text" class="form-control" id="kuantitas_semester" name="kuantitas_semester" onkeypress="return isNumberKey2(event)" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" readonly />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-custom text-muted fs-11" for="basic-icon-default-fullname">Skor Kuantitas</label>
                                <div class="input-group input-group-merge">
                                    <input type="text" class="form-control" id="skor_kuantitas_semester" name="skor_kuantitas_semester" onkeypress="return isNumberKey2(event)" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" readonly />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-2">
                        <div class="row flex-grow-1">
                            <div class="col-md-6">
                                <label class="form-label-custom text-muted fs-11" for="basic-icon-default-fullname">Nilai Kualitas (%) => Positif</label>
                                <div class="input-group input-group-merge">
                                    <input type="text" class="form-control" id="kualitas_semester" name="kualitas_semester" onkeypress="return isNumberKey2(event)" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-custom text-muted fs-11" for="basic-icon-default-fullname">Skor Kualitas</label>
                                <div class="input-group input-group-merge">
                                    <input type="text" class="form-control" id="skor_kualitas_semester" name="skor_kualitas_semester" onkeypress="return isNumberKey2(event)" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" readonly />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-2">
                        <div class="row flex-grow-1">
                            <div class="col-md-6">
                                <label class="form-label-custom text-muted fs-11" for="basic-icon-default-fullname">Nilai Waktu (%) => Positif</label>
                                <div class="input-group input-group-merge">
                                    <input type="text" class="form-control" id="waktu_semester" name="waktu_semester" onkeypress="return isNumberKey2(event)" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-custom text-muted fs-11" for="basic-icon-default-fullname">Skor Waktu</label>
                                <div class="input-group input-group-merge">
                                    <input type="text" class="form-control" id="skor_waktu_semester" name="skor_waktu_semester" onkeypress="return isNumberKey2(event)" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" readonly />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-2">
                        <div class="row flex-grow-1">
                            <div class="col-md-6">
                                <label class="form-label-custom text-muted fs-11" for="basic-icon-default-fullname">NSK (Skor)</label>
                                <div class="input-group input-group-merge">
                                    <input type="text" class="form-control" id="skor_kinerja_semester" name="skor_kinerja_semester" onkeypress="return isNumberKey2(event)" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-custom text-muted fs-11" for="basic-icon-default-fullname">NSK (Huruf)</label>
                                <div class="input-group input-group-merge">
                                    <input type="text" class="form-control" id="huruf_kinerja_semester" name="huruf_kinerja_semester" onkeypress="return isNumberKey2(event)" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" readonly />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-2">
                        <div class="row flex-grow-1">
                            <div class="col-md-6">
                                <label class="form-label-custom text-muted fs-11" for="basic-icon-default-fullname">NKI (Skor) <label id="lblrange" style="color:blue"></label></label>
                                <div class="input-group input-group-merge">
                                    <input type="text" class="form-control" id="skor_individu_semester" name="skor_individu_semester" onkeypress="return isNumberKey2(event)" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-custom text-muted fs-11" for="basic-icon-default-fullname">NKI (Huruf)</label>
                                <div class="input-group input-group-merge">
                                    <input type="text" class="form-control" id="huruf_individu_semester" name="huruf_individu_semester" onkeypress="return isNumberKey2(event)" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" readonly />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-2">
                        <div class="row flex-grow-1">
                            <div class="col-md-6">
                                <label class="form-label-custom text-muted fs-11" for="basic-icon-default-fullname">Nilai Talenta</label>
                                <div class="input-group input-group-merge">
                                    <input type="text" class="form-control" id="nilai_talenta_semester" name="nilai_talenta_semester" onkeypress="return isNumberKey2(event)" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" readonly />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-custom text-muted fs-11" for="basic-icon-default-fullname">Nama Talenta</label>
                                <div class="input-group input-group-merge">
                                    <input type="text" class="form-control" id="nama_talenta_semester" name="nama_talenta_semester" onkeypress="return isNumberKey2(event)" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" readonly />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-2">
                    </div>
                </form>
            </div>
            <div class="card-footer">
                <button type="button" id="saveBtn" class="btn btn-primary"><span class="ti ti-device-floppy ti-sm" style="margin-right:5px;"></span>Simpan</button>
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal"><span class="ti ti-circle-x ti-sm" style="margin-right:5px;"></span>Cancel</button>
            </div>            
        </div>
    </div>
</div>

@endsection
@push('scripts')
<script>
"use strict";
$(function() {
    $('#datatable-loader').show();
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        }
    });  

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });    

    var bulan_ini = "{{ $bulan_ini }}";
    var tanggal_ini = "{{ $tanggal_ini }}";
    var batas_tgl_penilaian = "{{ $batas_tgl_penilaian }}";
    var tahun_periode = "{{ $tahun_periode }}";
    var bulan_periode = "{{ $bulan_periode }}";
    $("#tahuncari").val(tahun_periode).trigger('change');
    $("#tahuncarinya").val(tahun_periode).trigger('change');
    $("#kd_areacarinya").val('semua').trigger('change');
    $("#kd_jeniscarinya").val('semua').trigger('change');
    if(parseInt(bulan_periode)<=6){
        $("#semestercari").val("1");
        $("#semestercarinya").val("1");
    } else {
        $("#semestercari").val("2");
        $("#semestercarinya").val("2");
    }
    $("#filternya").trigger('click');

    var table = $('#tbl_list').DataTable({
        initComplete: function() {    
            var api = this.api();
            $('#tbl_list_filter input').unbind();
            $('#tbl_list_filter input').bind('keyup', function(e) {
                if(e.keyCode == 13) {
                    api.search(this.value).draw();
                }
            });
        },
        dom: '<"card-header dt-head d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3"' +
            '<"head-label_tbl_list">' +
            '<"d-flex flex-column flex-sm-row align-items-center justify-content-sm-end gap-5 w-100"lf>' +
            '>' +
            '<"table-responsive"t>' +
            '<"card-footer d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2"i' +
            '<"d-flex align-items-sm-center justify-content-end gap-4"p>' +
            '>',        
        language: {
            processing: "",
            paginate: {
                next: '<i class="ri-arrow-right-s-line"></i>',
                previous: '<i class="ri-arrow-left-s-line"></i>'
            }
        },            
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('laptalentam') }}",
            data: function (d) {
                d.tahuncari = $('#tahuncari').val(),
                d.kd_areacari = $('#kd_areacari').val(),
                d.kd_jeniscari = $('#kd_jeniscari').val(),
                d.search = $('#tbl_list_filter input').val()
            }
        },            
        columns: [
            // {data: 'aksi', name:'aksi',width:'50px', orderable: false, searchable: false},
            {data: 'nip',name:'nip',width:'100px',className: 'dt-center'},
            {data: 'nama',name:'nama',width:'160px',className: 'dt-left wrap'},
            {data: 'jabatan',name:'jabatan',width:'200px',className: 'dt-left wrap'},
            {data: 'nama_area',name:'nama_area',width:'160px',className: 'dt-left wrap'},
            {data: 'nilai_semester1',name:'nilai_semester1',width:'50px',className: 'dt-center'},
            {data: 'pdp_semester1',name:'pdp_semester1',width:'50px',className: 'dt-center'},
            {data: 'kuantitas_semester1',name:'kuantitas_semester1',width:'50px',className: 'dt-center'},
            {data: 'kualitas_semester1',name:'kualitas_semester1',width:'50px',className: 'dt-center'},
            {data: 'waktu_semester1',name:'waktu_semester1',width:'50px',className: 'dt-center'},
            {data: 'skor_kinerja_semester1',name:'skor_kinerja_semester1',width:'50px',className: 'dt-center'},
            {data: 'huruf_kinerja_semester1',name:'huruf_kinerja_semester1',width:'50px',className: 'dt-center'},
            {data: 'skor_individu_semester1',name:'skor_individu_semester1',width:'50px',className: 'dt-center'},
            {data: 'huruf_individu_semester1',name:'huruf_individu_semester1',width:'50px',className: 'dt-center'},
            {data: 'nilai_talenta_semester1',name:'nilai_talenta_semester1',width:'50px',className: 'dt-center'},
            {data: 'nama_talenta_semester1',name:'nama_talenta_semester1',width:'50px',className: 'dt-center'},
            {data: 'nilai_semester2',name:'nilai_semester2',width:'50px',className: 'dt-center'},
            {data: 'pdp_semester2',name:'pdp_semester2',width:'50px',className: 'dt-center'},
            {data: 'kuantitas_semester2',name:'kuantitas_semester2',width:'50px',className: 'dt-center'},
            {data: 'kualitas_semester2',name:'kualitas_semester2',width:'50px',className: 'dt-center'},
            {data: 'waktu_semester2',name:'waktu_semester2',width:'50px',className: 'dt-center'},
            {data: 'skor_kinerja_semester2',name:'skor_kinerja_semester2',width:'50px',className: 'dt-center'},
            {data: 'huruf_kinerja_semester2',name:'huruf_kinerja_semester2',width:'50px',className: 'dt-center'},
            {data: 'skor_individu_semester2',name:'skor_individu_semester2',width:'50px',className: 'dt-center'},
            {data: 'huruf_individu_semester2',name:'huruf_individu_semester2',width:'50px',className: 'dt-center'},
            {data: 'nilai_talenta_semester2',name:'nilai_talenta_semester2',width:'50px',className: 'dt-center'},
            {data: 'nama_talenta_semester2',name:'nama_talenta_semester2',width:'50px',className: 'dt-center'},
        ],
        columnDefs: [
        {
            targets: [1], render: function (a, b, data, d) { 
                var a = '<div style="width:160px;">';
                a += '<span>'+data.nama+'</span>';
                a += '</div>';
                return a;
            }  
        },
        {
            targets: [2], render: function (a, b, data, d) { 
                var a = '<div style="width:200px;">';
                a += '<span>'+data.jabatan+'</span>';
                a += '</div>';
                return a;
            }  
        },
        {
            targets: [3], render: function (a, b, data, d) { 
                var a = '<div style="width:160px;">';
                a += '<span>'+data.nama_area+'</span>';
                a += '</div>';
                return a;
            }  
        },
        ],
        "ordering": false,
        "stateSave": true,
        "scrollX": true,
        "ScrollXInner": true,
        "autoWidth": false,
        "pagingType": 'simple_numbers'
    });
    // document.querySelector('div.head-label_tbl_list').innerHTML = '<button type="button" class="btn btn-sm btn-success add_row nowrap" style="width:100px;"><i class="ri-add-line me-1"></i>Input Data</button>';
    document.querySelector('div.head-label_tbl_list').innerHTML = '<h5 class="card-title text-nowrap mb-0">Rekap Talenta</h5>';

    table.on('preXhr.dt', function() {
        $('.dataTables_wrapper').addClass('loading');
        $('#datatable-loader').fadeIn(200);
    });
    table.on('xhr.dt', function() {
        $('.dataTables_wrapper').removeClass('loading');
        $('#datatable-loader').fadeOut(300);
    });

    table.search('').draw();
        
    if(parseInt(bulan_periode)<=6){
        $("#semestercari").val("1");
        table.column(4).visible(true);
        table.column(5).visible(true);
        table.column(6).visible(true);
        table.column(7).visible(true);
        table.column(8).visible(true);
        table.column(9).visible(true);
        table.column(10).visible(true);
        table.column(11).visible(true);
        table.column(12).visible(true);
        table.column(13).visible(true);
        table.column(14).visible(true);
        table.column(15).visible(false);
        table.column(16).visible(false);
        table.column(17).visible(false);
        table.column(18).visible(false);
        table.column(19).visible(false);
        table.column(20).visible(false);
        table.column(21).visible(false);
        table.column(22).visible(false);
        table.column(23).visible(false);
        table.column(24).visible(false);
        table.column(25).visible(false);
    } else {
        $("#semestercari").val("2");
        table.column(4).visible(false);
        table.column(5).visible(false);
        table.column(6).visible(false);
        table.column(7).visible(false);
        table.column(8).visible(false);
        table.column(9).visible(false);
        table.column(10).visible(false);
        table.column(11).visible(false);
        table.column(12).visible(false);
        table.column(13).visible(false);
        table.column(14).visible(false);
        table.column(15).visible(true);
        table.column(16).visible(true);
        table.column(17).visible(true);
        table.column(18).visible(true);
        table.column(19).visible(true);
        table.column(20).visible(true);
        table.column(21).visible(true);
        table.column(22).visible(true);
        table.column(23).visible(true);
        table.column(24).visible(true);
        table.column(25).visible(true);
    }

    $('#filternya').on("click", function() {
        $("#tahuncarinya").val($("#tahuncari").val());
        $("#kd_areacarinya").val($("#kd_areacari").val());
        $("#kd_jeniscarinya").val($("#kd_jeniscari").val());
        $("#semestercarinya").val($("#semestercari").val());
        table.draw();
        var semester = $("#semestercari").val();
        if(semester==="2"){
            table.column(4).visible(false);
            table.column(5).visible(false);
            table.column(6).visible(false);
            table.column(7).visible(false);
            table.column(8).visible(false);
            table.column(9).visible(false);
            table.column(10).visible(false);
            table.column(11).visible(false);
            table.column(12).visible(false);
            table.column(13).visible(false);
            table.column(14).visible(false);
            table.column(15).visible(true);
            table.column(16).visible(true);
            table.column(17).visible(true);
            table.column(18).visible(true);
            table.column(19).visible(true);
            table.column(20).visible(true);
            table.column(21).visible(true);
            table.column(22).visible(true);
            table.column(23).visible(true);
            table.column(24).visible(true);
            table.column(25).visible(true);
        } else {
            table.column(4).visible(true);
            table.column(5).visible(true);
            table.column(6).visible(true);
            table.column(7).visible(true);
            table.column(8).visible(true);
            table.column(9).visible(true);
            table.column(10).visible(true);
            table.column(11).visible(true);
            table.column(12).visible(true);
            table.column(13).visible(true);
            table.column(14).visible(true);
            table.column(15).visible(false);
            table.column(16).visible(false);
            table.column(17).visible(false);
            table.column(18).visible(false);
            table.column(19).visible(false);
            table.column(20).visible(false);
            table.column(21).visible(false);
            table.column(22).visible(false);
            table.column(23).visible(false);
            table.column(24).visible(false);
            table.column(25).visible(false);
        }
    });         
    
    $('#cetaknya').on("click", function() {
        var tahuncari = $("#tahuncari").val();
        var kd_areacari = $("#kd_areacari").val();
        var kd_jeniscari = $("#kd_jeniscari").val();
        var semestercari = $("#semestercari").val();
        // alert(tahuncari+" "+kd_areacari+" "+semestercari);
        window.open("cetaktalentam?tahuncari="+tahuncari+"&kd_areacari="+kd_areacari+"&kd_jeniscari="+kd_jeniscari+"&semestercari="+semestercari);
    }); 
    
    $('body').on('click', '.edit_row', function () {
        var id = $(this).data('id');
        var tahun_periode = $(this).data('tahun_periode');
        var bulan_periode = $(this).data('bulan_periode');
        var nip = $(this).data('nip');
        var nama = $(this).data('nama');
        var jabatan = $(this).data('jabatan');
        var semestercari = $("#semestercari").val();
        $.get("{{ route('kinerjapegawaim') }}" +'/' + id, function (data) {   
            $('#modelHeading').html("Update Penilaian Semester");
            $('#ModalForm').modal('show');
            $('#dataForm').trigger("reset");
            $('#id').val(id);
            $('#tahun_periode').val(tahun_periode);
            $('#bulan_periode').val(bulan_periode);
            $('#nip').val(nip);
            $('#nama').val(nama);
            $('#jabatan').val(jabatan);
            // if(parseInt(bulan_periode)<=6){
            if(parseInt(semestercari)===1){
                $('#semester').val('semester1').trigger('change');
                $('#nilai_semester').val(data.nilai_semester1).trigger('change');
                $('#pdp_semester').val(data.pdp_semester1);
                $('#kuantitas_semester').val(data.kuantitas_semester1);
                $('#kualitas_semester').val(data.kualitas_semester1);
                $('#waktu_semester').val(data.waktu_semester1);
                $('#skor_kuantitas_semester').val(data.skor_kuantitas_semester1);
                $('#skor_kualitas_semester').val(data.skor_kualitas_semester1);
                $('#skor_waktu_semester').val(data.skor_waktu_semester1);
                $('#skor_kinerja_semester').val(data.skor_kinerja_semester1);
                $('#huruf_kinerja_semester').val(data.huruf_kinerja_semester);
                $('#skor_individu_semester').val(data.skor_individu_semester1);
                $('#huruf_individu_semester').val(data.huruf_individu_semester1);
                $('#nilai_talenta_semester').val(data.nilai_talenta_semester1);
                $('#nama_talenta_semester').val(data.nama_talenta_semester1);
                var batas_awal_individu = 0;
                var batas_akhir_individu = 0;
                if(data.skor_kinerja_semester1>=0 && data.skor_kinerja_semester1<=100){
                    batas_awal_individu = 100;
                    batas_akhir_individu = 300;
                } else if(data.skor_kinerja_semester1>=101 && data.skor_kinerja_semester1<=200){
                    batas_awal_individu = 100;
                    batas_akhir_individu = 400;
                } else if(data.skor_kinerja_semester1>=201 && data.skor_kinerja_semester1<=300){
                    batas_awal_individu = 201;
                    batas_akhir_individu = 400;
                } else if(data.skor_kinerja_semester1>=301 && data.skor_kinerja_semester1<=400){
                    batas_awal_individu = 201;
                    batas_akhir_individu = 500;
                } else if(data.skor_kinerja_semester1>=401 && data.skor_kinerja_semester1<=500){
                    batas_awal_individu = 301;
                    batas_akhir_individu = 500;
                }
                $("#batas_awal_individu").val(batas_awal_individu).trigger('change');
                $("#batas_akhir_individu").val(batas_akhir_individu).trigger('change');
                $("#lblrange").text(batas_awal_individu+" - "+batas_akhir_individu);
                if(data.nilai_talenta_semester1!=="" && data.nilai_talenta_semester1!==null){
                    $("#saveBtn").prop("disabled", false);
                } else {
                    $("#saveBtn").prop("disabled", true);
                }
            // } else if(parseInt(bulan_periode)>=7){
            } else if(parseInt(semestercari)===2){
                $('#semester').val('semester2').trigger('change');
                $('#nilai_semester').val(data.nilai_semester2).trigger('change');
                $('#pdp_semester').val(data.pdp_semester2);
                $('#kuantitas_semester').val(data.kuantitas_semester2);
                $('#kualitas_semester').val(data.kualitas_semester2);
                $('#waktu_semester').val(data.waktu_semester2);
                $('#skor_kuantitas_semester').val(data.skor_kuantitas_semester2);
                $('#skor_kualitas_semester').val(data.skor_kualitas_semester2);
                $('#skor_waktu_semester').val(data.skor_waktu_semester2);
                $('#skor_kinerja_semester').val(data.skor_kinerja_semester2);
                $('#huruf_kinerja_semester').val(data.huruf_kinerja_semester2);
                $('#skor_individu_semester').val(data.skor_individu_semester2);
                $('#huruf_individu_semester').val(data.huruf_individu_semester2);
                $('#nilai_talenta_semester').val(data.nilai_talenta_semester2);
                $('#nama_talenta_semester').val(data.nama_talenta_semester2);
                var batas_awal_individu = 0;
                var batas_akhir_individu = 0;
                if(data.skor_kinerja_semester2>=0 && data.skor_kinerja_semester2<=100){
                    batas_awal_individu = 100;
                    batas_akhir_individu = 300;
                } else if(data.skor_kinerja_semester2>=101 && data.skor_kinerja_semester2<=200){
                    batas_awal_individu = 100;
                    batas_akhir_individu = 400;
                } else if(data.skor_kinerja_semester2>=201 && data.skor_kinerja_semester2<=300){
                    batas_awal_individu = 201;
                    batas_akhir_individu = 400;
                } else if(data.skor_kinerja_semester2>=301 && data.skor_kinerja_semester2<=400){
                    batas_awal_individu = 201;
                    batas_akhir_individu = 500;
                } else if(data.skor_kinerja_semester2>=401 && data.skor_kinerja_semester2<=500){
                    batas_awal_individu = 301;
                    batas_akhir_individu = 500;
                }
                $("#batas_awal_individu").val(batas_awal_individu).trigger('change');
                $("#batas_akhir_individu").val(batas_akhir_individu).trigger('change');
                $("#lblrange").text(batas_awal_individu+" - "+batas_akhir_individu);
                if(data.nilai_talenta_semester2!=="" && data.nilai_talenta_semester2!==null){
                    $("#saveBtn").prop("disabled", false);
                } else {
                    $("#saveBtn").prop("disabled", true);
                }

            }
        })
    });         
    $('#saveBtn').on("click", function(e) {
        e.preventDefault();
        $(this).prepend('<i class="fa fa-spinner fa-spin"></i>');
        var datanya = $('#dataForm').serialize();
        // alert(datanya);
        $.ajax({
            data: $('#dataForm').serialize(),
            url: "{{ route('kinerjapegawaim.store') }}",
            type: "POST",
            dataType: 'json',
            success: function (data) { 
                $('#saveBtn').find(".fa-spinner").remove();
                if(data.status === "sukses"){  
                    $('#dataForm').trigger("reset");
                    $('#ModalForm').modal('hide');
                    // table.draw();
                    table.ajax.reload(null, false);
                } else {
                    swal({
                        title: "Error",
                        text: data.errorMessage,
                        icon: "error",
                        type: "error",
                        dangerMode: true,
                    })
                }                    
            },
            error: function (data) {
                console.log('Error:', data);
            }
        });                 
    });


});
</script>
@endpush
@section('js')
    <!-- Datatable js -->
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/locales/bootstrap-datepicker.id.min.js"></script>
    <script>
    $(function () {
        // 🌐 Global default options for all Bootstrap datepickers
        $.fn.datepicker.defaults.format = 'yyyy-mm-dd';
        $.fn.datepicker.defaults.autoclose = true;
        $.fn.datepicker.defaults.todayHighlight = true;
        $.fn.datepicker.defaults.language = 'id'; // optional
        $.fn.datepicker.defaults.orientation = "bottom auto";

        // 🧠 Universal initializer for ALL datepickers
        $(document).on('focus', '.datepicker', function () {
            const $this = $(this);

            // Prevent duplicate initialization
            if ($this.data('datepicker')) return;

            // If inside a modal, use the modal body as container
            const modalParent = $this.closest('.modal');
            const container = modalParent.length ? modalParent : 'body';

            // Initialize the datepicker with container auto-detection
            $this.datepicker({
            container: container
            });
        });

        $('#blthcari').datepicker({
            autoclose: true,
            format: 'yyyy-mm',
            formatSubmit: 'yyyy-mm'
        });
        $('#tgl_lahir').datepicker({
            autoclose: true,
            format: 'dd/mm/yyyy',
            formatSubmit: 'yyyy-mm-dd',
            todayHighlight: true
        });
        $('#nilai_semester').on('change', function () {
            var nilai_semester = this.value;
            var pdp_semester = $("#pdp_semester").val();
            if((nilai_semester!=="" && nilai_semester!==null) || (pdp_semester!=="" && pdp_semester!==null)){
                var kuantitas_semester = filterFloat(nilai_semester)+filterFloat(pdp_semester);
                if(kuantitas_semester>110){
                    kuantitas_semester = 110;
                }
                $("#kuantitas_semester").val(kuantitas_semester+"%").trigger('change');
            } else {
                $("#kuantitas_semester").val("").trigger('change');
            }
        });
        $('#pdp_semester').on('change', function () {
            var pdp_semester = this.value;
            var nilai_semester = $("#nilai_semester").val();
            if((nilai_semester!=="" && nilai_semester!==null) || (pdp_semester!=="" && pdp_semester!==null)){
                var kuantitas_semester = filterFloat(nilai_semester)+filterFloat(pdp_semester);
                if(kuantitas_semester>110){
                    kuantitas_semester = 110;
                }
                $("#kuantitas_semester").val(kuantitas_semester+"%").trigger('change');
            } else {
                $("#kuantitas_semester").val("").trigger('change');
            }
        });
        $('#kuantitas_semester').on('change', function () {
            var nilai2 = this.value;
            if(nilai2!=="" && nilai2!==null){
                var nilai = filterFloat(this.value);
                $.ajax({
                    url: "{{url('api/get-skorkuantitas-kinerjapegawaim')}}",
                    type: "POST",
                    data: {
                        nilai: nilai,
                        _token: '{{csrf_token()}}'
                    },
                    dataType: 'json',
                    success: function (result) {
                        // alert(result);
                        $("#skor_kuantitas_semester").val(result).trigger('change');
                    }
                });
            } else {
                $("#skor_kuantitas_semester").val("").trigger('change');
            }
        });
        $('#kualitas_semester').on('change', function () {
            var nilai2 = this.value;
            if(nilai2!=="" && nilai2!==null){
                var nilai = filterFloat(this.value);
                $.ajax({
                    url: "{{url('api/get-skorkualitas-kinerjapegawaim')}}",
                    type: "POST",
                    data: {
                        nilai: nilai,
                        _token: '{{csrf_token()}}'
                    },
                    dataType: 'json',
                    success: function (result) {
                        // alert(result);
                        $("#skor_kualitas_semester").val(result).trigger('change');
                    }
                });
            } else {
                $("#skor_kualitas_semester").val("").trigger('change');
            }
        });
        $('#waktu_semester').on('change', function () {
            var nilai2 = this.value;
            if(nilai2!=="" && nilai2!==null){
                var nilai = filterFloat(this.value);
                $.ajax({
                    url: "{{url('api/get-skorwaktu-kinerjapegawaim')}}",
                    type: "POST",
                    data: {
                        nilai: nilai,
                        _token: '{{csrf_token()}}'
                    },
                    dataType: 'json',
                    success: function (result) {
                        // alert(result);
                        $("#skor_waktu_semester").val(result).trigger('change');
                    }
                });
            } else {
                $("#skor_waktu_semester").val("").trigger('change');
            }
        });
        $('#skor_kuantitas_semester').on('change', function () {
            var nilai1 = $("#skor_kuantitas_semester").val();
            var nilai2 = $("#skor_kualitas_semester").val();
            var nilai3 = $("#skor_waktu_semester").val();
            var pembagi = 0;
            if(nilai1!=="" && nilai1!==null){
                pembagi++;
            }
            if(nilai2!=="" && nilai2!==null){
                pembagi++;
            }
            if(nilai3!=="" && nilai3!==null){
                pembagi++;
            }
            var skor_kinerja = Math.round((filterFloat(nilai1)+filterFloat(nilai2)+filterFloat(nilai3))/pembagi);
            if(!isNaN(skor_kinerja)){
                $("#skor_kinerja_semester").val(skor_kinerja).trigger('change');
            } else {
                $("#skor_kinerja_semester").val("").trigger('change');
            }
            var batas_awal_individu = 0;
            var batas_akhir_individu = 0;
            if(skor_kinerja>=0 && skor_kinerja<=100){
                batas_awal_individu = 100;
                batas_akhir_individu = 300;
            } else if(skor_kinerja>=101 && skor_kinerja<=200){
                batas_awal_individu = 100;
                batas_akhir_individu = 400;
            } else if(skor_kinerja>=201 && skor_kinerja<=300){
                batas_awal_individu = 201;
                batas_akhir_individu = 400;
            } else if(skor_kinerja>=301 && skor_kinerja<=400){
                batas_awal_individu = 201;
                batas_akhir_individu = 500;
            } else if(skor_kinerja>=401 && skor_kinerja<=500){
                batas_awal_individu = 301;
                batas_akhir_individu = 500;
            }
            $("#batas_awal_individu").val(batas_awal_individu).trigger('change');
            $("#batas_akhir_individu").val(batas_akhir_individu).trigger('change');
            $("#lblrange").text(batas_awal_individu+" - "+batas_akhir_individu);
        });
        $('#skor_kualitas_semester').on('change', function () {
            var nilai1 = $("#skor_kuantitas_semester").val();
            var nilai2 = $("#skor_kualitas_semester").val();
            var nilai3 = $("#skor_waktu_semester").val();
            var pembagi = 0;
            if(nilai1!=="" && nilai1!==null){
                pembagi++;
            }
            if(nilai2!=="" && nilai2!==null){
                pembagi++;
            }
            if(nilai3!=="" && nilai3!==null){
                pembagi++;
            }
            var skor_kinerja = Math.round((filterFloat(nilai1)+filterFloat(nilai2)+filterFloat(nilai3))/pembagi);
            if(!isNaN(skor_kinerja)){
                $("#skor_kinerja_semester").val(skor_kinerja).trigger('change');
            } else {
                $("#skor_kinerja_semester").val("").trigger('change');
            }
            var batas_awal_individu = 0;
            var batas_akhir_individu = 0;
            if(skor_kinerja>=0 && skor_kinerja<=100){
                batas_awal_individu = 100;
                batas_akhir_individu = 300;
            } else if(skor_kinerja>=101 && skor_kinerja<=200){
                batas_awal_individu = 100;
                batas_akhir_individu = 400;
            } else if(skor_kinerja>=201 && skor_kinerja<=300){
                batas_awal_individu = 201;
                batas_akhir_individu = 400;
            } else if(skor_kinerja>=301 && skor_kinerja<=400){
                batas_awal_individu = 201;
                batas_akhir_individu = 500;
            } else if(skor_kinerja>=401 && skor_kinerja<=500){
                batas_awal_individu = 301;
                batas_akhir_individu = 500;
            }
            $("#batas_awal_individu").val(batas_awal_individu).trigger('change');
            $("#batas_akhir_individu").val(batas_akhir_individu).trigger('change');
            $("#lblrange").text(batas_awal_individu+" - "+batas_akhir_individu);
        });
        $('#skor_waktu_semester').on('change', function () {
            var nilai1 = $("#skor_kuantitas_semester").val();
            var nilai2 = $("#skor_kualitas_semester").val();
            var nilai3 = $("#skor_waktu_semester").val();
            var pembagi = 0;
            if(nilai1!=="" && nilai1!==null){
                pembagi++;
            }
            if(nilai2!=="" && nilai2!==null){
                pembagi++;
            }
            if(nilai3!=="" && nilai3!==null){
                pembagi++;
            }
            var skor_kinerja = Math.round((filterFloat(nilai1)+filterFloat(nilai2)+filterFloat(nilai3))/pembagi);
            if(!isNaN(skor_kinerja)){
                $("#skor_kinerja_semester").val(skor_kinerja).trigger('change');
            } else {
                $("#skor_kinerja_semester").val("").trigger('change');
            }
            var batas_awal_individu = 0;
            var batas_akhir_individu = 0;
            if(skor_kinerja>=0 && skor_kinerja<=100){
                batas_awal_individu = 100;
                batas_akhir_individu = 300;
            } else if(skor_kinerja>=101 && skor_kinerja<=200){
                batas_awal_individu = 100;
                batas_akhir_individu = 400;
            } else if(skor_kinerja>=201 && skor_kinerja<=300){
                batas_awal_individu = 201;
                batas_akhir_individu = 400;
            } else if(skor_kinerja>=301 && skor_kinerja<=400){
                batas_awal_individu = 201;
                batas_akhir_individu = 500;
            } else if(skor_kinerja>=401 && skor_kinerja<=500){
                batas_awal_individu = 301;
                batas_akhir_individu = 500;
            }
            $("#batas_awal_individu").val(batas_awal_individu).trigger('change');
            $("#batas_akhir_individu").val(batas_akhir_individu).trigger('change');
            $("#lblrange").text(batas_awal_individu+" - "+batas_akhir_individu);
        });
        $('#skor_kinerja_semester').on('change', function () {
            var nilai2 = this.value;
            if(nilai2!=="" && nilai2!==null){
                var nilai = filterFloat(this.value);
                $.ajax({
                    url: "{{url('api/get-skorkinerja-kinerjapegawaim')}}",
                    type: "POST",
                    data: {
                        nilai: nilai,
                        _token: '{{csrf_token()}}'
                    },
                    dataType: 'json',
                    success: function (result) {
                        // alert(result);
                        $("#huruf_kinerja_semester").val(result).trigger('change');
                    }
                });
            } else {
                $("#huruf_kinerja_semester").val("").trigger('change');
            }
        });
        $('#skor_individu_semester').on('change', function () {
            var nilai2 = this.value;
            if(nilai2!=="" && nilai2!==null){
                var nilai = filterFloat(this.value);
                var batas_awal = $("#batas_awal_individu").val();
                var batas_akhir = $("#batas_akhir_individu").val();
                if(nilai<batas_awal){
                    nilai = batas_awal;
                } else if(nilai>batas_akhir){
                    nilai = batas_akhir;
                }
                $('#skor_individu_semester').val(nilai);
                $.ajax({
                    url: "{{url('api/get-skorindividu-kinerjapegawaim')}}",
                    type: "POST",
                    data: {
                        nilai: nilai,
                        _token: '{{csrf_token()}}'
                    },
                    dataType: 'json',
                    success: function (result) {
                        // alert(result);
                        $("#huruf_individu_semester").val(result).trigger('change');
                        $("#saveBtn").prop("disabled", false);
                    }
                });
            } else {
                $("#huruf_individu_semester").val('').trigger('change');
                $("#nilai_talenta_semester").val('').trigger('change');
                $("#nama_talenta_semester").val('').trigger('change');
                $("#saveBtn").prop("disabled", true);
            }
        });
        $('#huruf_kinerja_semester').on('change', function () {
            var huruf_kinerja_semester = this.value;
            var huruf_individu_semester = $("#huruf_individu_semester").val();
            if(huruf_kinerja_semester!=="" && huruf_kinerja_semester!==null && huruf_individu_semester!=="" && huruf_individu_semester!==null){
                $("#nilai_talenta_semester").val(huruf_kinerja_semester+"/"+huruf_individu_semester).trigger('change');
            } else {
                $("#nilai_talenta_semester").val("").trigger('change');
            }
        });
        $('#huruf_individu_semester').on('change', function () {
            var huruf_individu_semester = this.value;
            var huruf_kinerja_semester = $("#huruf_kinerja_semester").val();
            if(huruf_kinerja_semester!=="" && huruf_kinerja_semester!==null && huruf_individu_semester!=="" && huruf_individu_semester!==null){
                $("#nilai_talenta_semester").val(huruf_kinerja_semester+"/"+huruf_individu_semester).trigger('change');
            } else {
                $("#nilai_talenta_semester").val("").trigger('change');
            }
        });
        $('#nilai_talenta_semester').on('change', function () {
            var nilai = this.value;
            if(nilai!=="" && nilai!==null){
                $.ajax({
                    url: "{{url('api/get-talenta-kinerjapegawaim')}}",
                    type: "POST",
                    data: {
                        nilai: nilai,
                        _token: '{{csrf_token()}}'
                    },
                    dataType: 'json',
                    success: function (result) {
                        // alert(result);
                        $("#nama_talenta_semester").val(result).trigger('change');
                    }
                });
            } else {
                $("#nama_talenta_semester").val('').trigger('change');
            }
        });
        $('#nama_talenta_semester').on('change', function () {
            var skor_kinerja_semester = $("#skor_kinerja_semester").val();
            var nilai_semester = $("#nilai_semester").val();
            var kualitas_semester = $("#kualitas_semester").val();
            var waktu_semester = $("#waktu_semester").val();
            var nama_talenta = $("#nama_talenta_semester").val();
            // if(nilai_semester!=="" && nilai_semester!==null && kualitas_semester!=="" && kualitas_semester!==null && waktu_semester!=="" && waktu_semester!==null && nama_talenta!=="" && nama_talenta!==null){
            if(skor_kinerja_semester!=="" && skor_kinerja_semester!==null && !isNaN(skor_kinerja_semester) && nama_talenta!=="" && nama_talenta!==null){
                $("#saveBtn").prop("disabled", false);
            } else {
                $("#saveBtn").prop("disabled", true);
            }
        });


    });
    </script>

    <script>
      $(function () {
        $.fn.datepicker.defaults.format = 'yyyy-mm-dd';
        $.fn.datepicker.defaults.autoclose = true;
        $.fn.datepicker.defaults.todayHighlight = true;
        $.fn.datepicker.defaults.language = 'id'; // optional
        $.fn.datepicker.defaults.orientation = "bottom auto";

        // 🧠 Universal initializer for ALL datepickers
        $(document).on('focus', '.datepicker', function () {
            const $this = $(this);

            // Prevent duplicate initialization
            if ($this.data('datepicker')) return;

            // If inside a modal, use the modal body as container
            const modalParent = $this.closest('.modal');
            const container = modalParent.length ? modalParent : 'body';

            // Initialize the datepicker with container auto-detection
            $this.datepicker({
            container: container
            });
        });

        $('.select2').select2({
            allowClear: false
        });     
        $('#ModalForm .select2').each(function() {  
            var $p1 = $(this).parent(); 
            $(this).select2({  
                dropdownParent: $p1
            });  
        }); 
        $('#ModalForm2 .select2').each(function() {  
            var $p1 = $(this).parent(); 
            $(this).select2({  
                dropdownParent: $p1
            });  
        }); 
        $('#ModalForm3 .select2').each(function() {  
            var $p1 = $(this).parent(); 
            $(this).select2({  
                dropdownParent: $p1
            });  
        }); 
        $('#ModalFormbiaya .select2').each(function() {  
            var $p1 = $(this).parent(); 
            $(this).select2({  
                dropdownParent: $p1
            });  
        }); 
        $('#ModalFormrincianbiaya .select2').each(function() {  
            var $p1 = $(this).parent(); 
            $(this).select2({  
                dropdownParent: $p1
            });  
        }); 
        $('#ModalFormrinciantransportasi .select2').each(function() {  
            var $p1 = $(this).parent(); 
            $(this).select2({  
                dropdownParent: $p1
            });  
        }); 
        $('#ModalFormstatus .select2').each(function() {  
            var $p1 = $(this).parent(); 
            $(this).select2({  
                dropdownParent: $p1
            });  
        }); 
        $('#ModalFilter .select2').each(function() {  
            var $p1 = $(this).parent(); 
            $(this).select2({  
                dropdownParent: $p1
            });  
        }); 
        $('#ModalFilter1 .select2').each(function() {  
            var $p1 = $(this).parent(); 
            $(this).select2({  
                dropdownParent: $p1
            });  
        }); 
        $('#ModalFilter2 .select2').each(function() {  
            var $p1 = $(this).parent(); 
            $(this).select2({  
                dropdownParent: $p1
            });  
        }); 
        $('#ModalAdd .select2').each(function() {  
            var $p2 = $(this).parent(); 
            $(this).select2({  
                dropdownParent: $p2
            });  
        }); 
        $('#ModalUpdate .select2').each(function() {  
            var $p3 = $(this).parent(); 
            $(this).select2({  
                dropdownParent: $p3
            });  
        }); 
        $('#dynamic_field .select2').each(function() {  
            var $p4 = $(this).parent(); 
            $(this).select2({  
                dropdownParent: $p4
            });  
        }); 
      });
  </script>
    
    <!-- App js -->
    <script type="module" src="{{ asset('assets/js/app.js') }}"></script>
@endsection
