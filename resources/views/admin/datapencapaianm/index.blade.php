
@extends('partials.layouts.master3')

@section('title', 'Pencapaian KPI | SIMAK')
@section('sub-title', 'Pencapaian KPI ' )
@section('pagetitle', 'Dashboard')
<!-- @section('buttonTitle', 'Input Anggota')
@section('link', 'add_row') -->

@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/css/bootstrap-datepicker.min.css"/>
<!-- <link rel="stylesheet" href="{{ asset('assets/libs/air-datepicker/air-datepicker.css') }}"> -->
<link rel="stylesheet" href="{{ asset('assets/libs/@yaireo/tagify/tagify.css') }}">
<style>
.nav-link {
    padding: 3px 8px !important;
}
/* .label-fixed {
    display: inline-block;
    width: 60px !important;
}    
.custom-label {
    padding: 5px !important;
}      
.form-label-custom {
    margin-bottom: 0.2rem !important;        
} */
</style>
@endsection


@section('content')
<script src="https://cdnjs.cloudflare.com/ajax/libs/highcharts/11.2.0/highcharts.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/highcharts/11.2.0/highcharts-more.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/highcharts/11.2.0/modules/solid-gauge.min.js"></script>

<div class="row g-4">
    <div class="col-12">
        <div class="card card-h-100">
            <div class="card-body">
                <ul class="nav gap-2 custom-verti-nav-pills text-center mb-2" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a href="javascript:void(0)" class="nav-link fs-13 active" id="tabdata" data-bs-toggle="tab" data-bs-target="#navs-top-data" role="tab" aria-selected="false" tabindex="-1">
                            <i class="ri-group-line fs-15 me-2"></i>Data Pegawai
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="javascript:void(0)" class="nav-link fs-13" id="tabdetail" data-bs-toggle="tab" data-bs-target="#navs-top-detail" role="tab" aria-selected="true">
                            <i class="ri-user-settings-line fs-15 me-2"></i>Pencapaian KPI
                        </a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active show" id="navs-top-data" aria-labelledby="tabdata" role="tabpanel">
                        <div class="card border border-light">
                            <div class="card-body">
                                <div class="row flex-grow-1">
                                    <div class="col-md-2 grid-margin">
                                        <select id="tahuncari" name="tahuncari" class="select2 form-select form-select-sm" data-allow-clear="true">
                                            @foreach ($datatahun as $data)
                                                <option value="{{ $data }}">{{ $data }}</option>
                                            @endforeach
                                        </select>                
                                    </div>
                                    <div class="col-md-3 grid-margin">
                                        <!-- <select id="kd_areacari" name="kd_areacari" class="form-control form-control-sm select2">
                                            <option value="semua" selected>SEMUA</option>
                                            @foreach ($masteraream as $data)
                                                <option value="{{ $data->kd_area }}">{{ $data->nama_area }}</option>                                    
                                            @endforeach
                                        </select>                             -->
                                        <select id="jenis_kpicari" name="jenis_kpicari" class="form-control form-control-sm select2">
                                            <!-- <option value="semua" selected>SEMUA</option> -->
                                            @foreach ($jenisKpi as $row)
                                                <option value="{{ $row->jenis_kpi }}">{{ strtoupper($row->jenis_kpi) }}</option>                                    
                                            @endforeach
                                        </select>                            

                                    </div>
                                    <div class="col-md-7 grid-margin">
                                        <div class="column-gap-2 d-flex flex-wrap align-items-center justify-content-start">
                                            <button type="button" id="filternya" class="btn btn-primary btn-sm"><i class="ri-search-line me-1 font-size-13"></i>Filter Data</button>
                                            <div>
                                                <form action="{{ route('export-datapencapaianm') }}" target="_blank">
                                                <input type="hidden" class="form-control form-control-sm" name="tahuncarinya" id="tahuncarinya">
                                                <input type="hidden" class="form-control form-control-sm" name="kd_areacarinya" id="kd_areacarinya">
                                                <button type="submit" class="btn btn-success btn-sm"><i class="ri-download-2-line me-1 font-size-13"></i>Download</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- <div class="row flex-grow-1 mt-1">
                                    <div class="col-md-12 grid-margin">
                                        <button type="button" class="hitung_all_atasan btn btn-warning btn-sm"><i class="ri-calculator-line me-1"></i>Hitung KPI Semua Atasan</button>
                                    </div>
                                </div> -->
                            </div>
                        </div>

                        <div class="card-datatable text-nowrap">
                        <table id="tbl_list" class="table data-table-responsive table-hover align-middle table table-nowrap w-100 custom-table">
                            <thead>
                            <tr>
                                <th>Aksi</th>
                                <th>Export</th>
                                <th>Nomor Induk</th>
                                <th>Nama</th>
                                <th>Jabatan</th>
                                <th>Nama Area</th>
                            </tr>
                            </thead>
                        </table>
                        </div>
                    </div>
                    <div class="tab-pane" id="navs-top-detail" aria-labelledby="tabdetail" role="tabpanel">
                        <div class="card border border-light">
                            <div class="card-body">
                                <input type="hidden" name="tahuncari2" id="tahuncari2">
                                <input type="hidden" name="nipcari2" id="nipcari2">
                                <h5 id="lbljudul" class="font-size-13">No data selected.</h5>
                                <div id="rinciandata" class="d-flex flex-wrap align-items-center justify-content-start gap-3">
                                    <!-- <a title="Input Data" class="add_row"><button type="button" class="btn btn-primary"><span class="ti-xs ti ti-plus"></span>Input Data</button></a> -->
                                    <div><label class="font-size-12" id="lbltahun" style="font-weight:bold;"></label></div>
                                    <div><label class="font-size-12" id="lblnip" style="font-weight:bold;"></label></div>
                                    <div><label class="font-size-12" id="lblnama" style="font-weight:bold;"></label></div>
                                    <div><label class="font-size-12" id="lbljabatan" style="font-weight:bold;"></label></div>
                                </div> 
                            </div>
                        </div>

                        <div class="card-datatable text-nowrap">
                        <table id="tbl_list2" class="table data-table-responsive table-hover align-middle table table-nowrap w-100 custom-table">
                            <thead>
                            <tr>
                                <th rowspan="2">Tahun</th>
                                <th rowspan="2">Uraian KPI</th>
                                <th rowspan="2">Keterangan</th>
                                <th colspan="2" class="center">Visualisasi Pencapaian</th>
                                <th colspan="7" class="center">Semester 1</th>
                                <th colspan="7" class="center">Semester 2</th>
                            </tr>
                            <tr>
                                <th>Semester-1</th>
                                <th>Semester-2</th>
                                <th>Jan</th>
                                <th>Feb</th>
                                <th>Mat</th>
                                <th>Apr</th>
                                <th>Mei</th>
                                <th>Jun</th>
                                <th>Pencapaian</th>
                                <th>Jul</th>
                                <th>Agt</th>
                                <th>Sep</th>
                                <th>Okt</th>
                                <th>Nop</th>
                                <th>Des</th>
                                <th>Pencapaian</th>
                            </tr>
                            </thead>
                        </table>
                        </div>  
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- <div id="datatable-loader" style="display:none;position:fixed;top:20px;left:50%;transform:translateX(-50%);z-index:1055;">
    <div class="spinner-border text-primary" role="status" style="width:1.5rem;height:1.5rem;">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>   -->
<div id="datatable-loader" style="display:none;position:fixed;top:25px;left:50%;transform:translateX(-50%);z-index:1055;">
    <div class="spinner-border spinner-border-sm text-primary"></div>
        <span class="ms-2">Loading data...</span>
    </div>
</div>  


<div class="modal" id="ModalForm2" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-between align-items-center">
                <h5 class="modal-title font-size-14" id="modelHeading2"></h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">            
                <form id="dataForm2" name="dataForm2" class="form-horizontal">
                    <input type="hidden" name="id2" id="id2">
                    <input type="hidden" name="kode_kpi2" id="kode_kpi2">
                    <input type="hidden" name="nama_target2" id="nama_target2">
                    <input type="hidden" name="satuan_kuantitas2" id="satuan_kuantitas2">
                    <input type="hidden" name="satuan_kualitas2" id="satuan_kualitas2">
                    <input type="hidden" name="satuan_waktu2" id="satuan_waktu2">
                    @csrf
                    <div class="mb-2">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Tahun</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control form-control-sm" id="tahun2" name="tahun2" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" readonly />
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Bulan</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control form-control-sm" id="nama_bulan2" name="nama_bulan2" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" readonly />
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Uraian KPI</label>
                        <div class="input-group input-group-merge">
                            <textarea id="uraian2" name="uraian2" class="form-control form-control-sm" placeholder="" aria-describedby="basic-icon-default-message2" rows="2" readonly></textarea>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Polarisasi</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control form-control-sm" id="polarisasi2" name="polarisasi2" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" readonly />
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Type Target</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control form-control-sm" id="type_target2" name="type_target2" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" readonly />
                        </div>
                    </div>
                    <div class="mb-2">
                        <div class="row flex-grow-1">
                            <div class="col-md-4">
                                <label id="lblsatuan_kuantitas" style="font-weight:bold;"></label>
                            </div>
                            <div class="col-md-4">
                                <label id="lblsatuan_kualitas" style="font-weight:bold;"></label>
                            </div>
                            <div class="col-md-4">
                                <label id="lblsatuan_waktu" style="font-weight:bold;"></label>
                            </div>
                        </div>
                    <div class="mb-2">
                        <div class="row flex-grow-1">
                            <div class="col-md-4">
                                <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Target Kuantitas</label>
                                <div class="input-group input-group-merge">
                                    <input type="text" class="form-control form-control-sm" id="targetkn2" name="targetkn2" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" readonly />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Target Kualitas</label>
                                <div class="input-group input-group-merge">
                                    <input type="text" class="form-control form-control-sm" id="targetkl2" name="targetkl2" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" readonly />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Target Waktu</label>
                                <div class="input-group input-group-merge">
                                    <input type="text" class="form-control form-control-sm" id="targetwk2" name="targetwk2" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" readonly />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-2">
                        <div class="row flex-grow-1">
                            <div class="col-md-4">
                                <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Realisasi Kuantitas</label>
                                <div class="input-group input-group-merge">
                                    <input type="text" class="form-control form-control-sm" id="realisasikn2" name="realisasikn2" onkeypress="return isNumberKey(event)" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" readonly />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Realisasi Kualitas</label>
                                <div class="input-group input-group-merge">
                                    <input type="text" class="form-control form-control-sm" id="realisasikl2" name="realisasikl2" onkeypress="return isNumberKey(event)" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" readonly />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Realisasi Waktu</label>
                                <div class="input-group input-group-merge">
                                    <input type="text" class="form-control form-control-sm" id="realisasiwk2" name="realisasiwk2" onkeypress="return isNumberKey(event)" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" readonly />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-2">
                        <div class="row flex-grow-1">
                            <div class="col-md-4">
                                <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Finalisasi Kuantitas</label>
                                <div class="input-group input-group-merge">
                                    <input type="text" class="form-control form-control-sm" id="realisasibkn2" name="realisasibkn2" onkeypress="return isNumberKey(event)" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Finalisasi Kualitas</label>
                                <div class="input-group input-group-merge">
                                    <input type="text" class="form-control form-control-sm" id="realisasibkl2" name="realisasibkl2" onkeypress="return isNumberKey(event)" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Finalisasi Waktu</label>
                                <div class="input-group input-group-merge">
                                    <input type="text" class="form-control form-control-sm" id="realisasibwk2" name="realisasibwk2" onkeypress="return isNumberKey(event)" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Eviden Realisasi</label>
                        <div>
                            <div id="div_eviden_realisasi2"></div>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">BA Kesepakatan Finalisasi</label>
                        <div>
                            <div id="div_eviden2"></div>
                        </div>
                    </div>

                    <!-- <div class="mb-2">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Realisasi</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control form-control-sm" id="realisasi2" name="realisasi2" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div> -->
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="saveBtn2" class="btn btn-primary"><i class="ri-save-3-line me-1"></i>Simpan</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="ri-close-line me-1"></i>Close</button>
            </div>
        </div>
    </div>
</div>  
@endsection
@push('scripts')
<script>
"use strict";
$(function() {
    // var url_evidenkpi = "https://localhost/simkppcn/public/evidenkpi";
    var url_evidenkpi = "https://simak-pcn.paguntaka.co.id/evidenkpi";
    var bulan_ini = "{{ $bulan_ini }}";
    var tanggal_ini = "{{ $tanggal_ini }}";
    var batas_tgl_finalisasi = "{{ $batas_tgl_finalisasi }}";
    var tahun_periode = "{{ $tahun_periode }}";
    var bulan_periode = "{{ $bulan_periode }}";
    // $("#tahuncari").val("{{ date('Y') }}").trigger('change');
    $('#jenis_kpicari').val("pusat").trigger('change');
    $('#nipcari2').val("");
    $("#lbljudul").show();
    $("#rinciandata").hide();
    $('#tbl_list_filter input').val("");

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
        deferRender: true,
        ajax: {
            url: "{{ route('datapencapaianm') }}",
            data: function (d) {
                d.tahuncari = $('#tahuncari').val(),
                // d.kd_areacari = $('#kd_areacari').val(),
                d.jenis_kpicari = $('#jenis_kpicari').val(),
                d.search = $('#tbl_list_filter input').val()
            }
        },  
        columns: [
            {data: 'aksi', name:'aksi',width:'50px',className: 'dt-center', orderable: false, searchable: false},
            {data: 'download',name:'download',width:'50px',className: 'dt-center'},
            {data: 'nip',name:'nip',width:'100px',className: 'dt-center'},
            {data: 'nama',name:'nama',width:'160px',className: 'dt-left wrap'},
            {data: 'jabatan',name:'jabatan',width:'200px',className: 'dt-left wrap'},
            {data: 'nama_area',name:'nama_area',width:'160px',className: 'dt-left wrap'},
            // {data: 'nilai_semester1',name:'nilai_semester1',width:'80px',className: 'dt-center'},
            // {data: 'nilai_semester2',name:'nilai_semester2',width:'80px',className: 'dt-center'},
        ],
        columnDefs: [
        {
            targets: [1], render: function (a, b, data, d) { 
                var a = '<div>';
                a += '<form action="{{ route("export-datapencapaianm") }}" target="_blank">';
                a += '<input type="hidden" class="form-control form-control-sm" name="tahuncarinya" id="tahuncarinya" value="'+data.tahun+'">';
                a += '<input type="hidden" class="form-control form-control-sm" name="nipcarinya" id="nipcarinya" value="'+data.nip+'">';
                a += '<input type="hidden" class="form-control form-control-sm" name="namacarinya" id="namacarinya" value="'+data.nama+'">';
                a += '<button type="submit" class="btn btn-light-success icon-btn-sm"><i class="ri-file-excel-line fs-14"></i></button>';
                a += '</form>';
                a += '</div>';
                return a;
            }  
        },
        {
            targets: [3], render: function (a, b, data, d) { 
                var a = '<div style="width:160px;">';
                a += '<span>'+data.nama+'</span>';
                // a += '<br/><span>'+data.jumlah_bawahan+'</span>';
                a += '</div>';
                return a;
            }  
        },
        {
            targets: [4], render: function (a, b, data, d) { 
                var a = '<div style="width:200px;">';
                a += '<span>'+data.jabatan+'</span>';
                a += '</div>';
                return a;
            }  
        },
        {
            targets: [4], render: function (a, b, data, d) { 
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
    document.querySelector('div.head-label_tbl_list').innerHTML = '<h5 class="card-title text-nowrap mb-0">Data Pegawai</h5>';

    var table2 = $('#tbl_list2').DataTable({
        initComplete: function() {    
            var api = this.api();
            $('#tbl_list2_filter input').unbind();
            $('#tbl_list2_filter input').bind('keyup', function(e) {
                if(e.keyCode == 13) {
                    api.search(this.value).draw();
                }
            });
        },
        dom: '<"card-header dt-head d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3"' +
            '<"head-label_tbl_list2">' +
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
        deferRender: true,
        deferLoading: 0,
        ajax: {
            url: "{{ url('api/fetch-detail-datapencapaianm') }}",
            data: function (d) {
                d.tahuncari = $('#tahuncari2').val(),
                d.nipcari = $('#nipcari2').val(),
                d.search = $('#tbl_list2_filter input').val()
            }
        },  
        columns: [
            // {data: 'aksi', name:'aksi',width:'50px', orderable: false, searchable: false},
            {data: 'tahun',name:'tahun',width:'80px',className: 'dt-center'},
            {data: 'uraian',name:'uraian',width:'250px',className: 'dt-left wrap'},
            {data: 'keterangan',name:'keterangan',width:'100px',className: 'dt-left'},
            {data: 'sem1',name:'sem1',width:'50px',className: 'dt-center no-padding'},
            {data: 'sem2',name:'sem2',width:'300px',className: 'dt-center no-padding'},
            {data: 'target01',name:'target01',width:'50px',className: 'dt-center'},
            {data: 'target02',name:'target02',width:'50px',className: 'dt-center'},
            {data: 'target03',name:'target03',width:'50px',className: 'dt-center'},
            {data: 'target04',name:'target04',width:'50px',className: 'dt-center'},
            {data: 'target05',name:'target05',width:'50px',className: 'dt-center'},
            {data: 'target06',name:'target06',width:'50px',className: 'dt-center'},
            {data: 'semester1',name:'semester1',width:'50px',className: 'dt-center'},
            {data: 'target07',name:'target07',width:'50px',className: 'dt-center'},
            {data: 'target08',name:'target08',width:'50px',className: 'dt-center'},
            {data: 'target09',name:'target09',width:'50px',className: 'dt-center'},
            {data: 'target10',name:'target10',width:'50px',className: 'dt-center'},
            {data: 'target11',name:'target11',width:'50px',className: 'dt-center'},
            {data: 'target12',name:'target12',width:'50px',className: 'dt-center'},
            {data: 'semester2',name:'semester2',width:'50px',className: 'dt-center'},
        ],
        columnDefs: [
        {
            targets: [1], render: function (a, b, data, d) { 
                var a = '<div style="width:250px;">';
                a += '<span>'+data.uraian+'</span>';
                a += '<br/><span style="color:blue;">'+data.type_target+'</span>';
                a += '</div>';
                return a;
            }  
        },
        {
            targets: [2], render: function (a, b, data, d) { 
                var a = '<span class="badge bg-label-secondary mb-1" style="width:100%;">Target</span>';
                a += '<br/><span class="badge bg-label-secondary mb-1" style="width:100%;">Realisasi</span>';
                a += '<br/><span class="badge bg-label-secondary mb-1" style="width:100%;">Finalisasi</span>';
                return a;
            }  
        },
        {
            targets: [5], render: function (a, b, data, d) { 
                if(data.target01!=="" && data.target01!==null){
                    var nilai1 = data.target01; 
                } else {
                    var nilai1 = "&nbsp;"; 
                }
                if(data.realisasi01!==""){
                    var nilai2 = data.realisasi01; 
                } else {
                    var nilai2 = "&nbsp;"; 
                }
                if(data.realisasi01b!==""){
                    var nilai3 = data.realisasi01b; 
                } else {
                    var nilai3 = "&nbsp;"; 
                }                       
                var a = '<span class="badge bg-info mb-1" style="width:100%;">'+nilai1+'</span>';
                a += '<br/><span class="badge bg-success mb-1" style="width:100%;">'+nilai2+'</span>';
                a += '<br/><a href="javascript:void(0)" data-nama_bulan="JANUARI" data-nama_target="target01" data-target="'+data.target01+'" data-realisasi="'+data.realisasi01+'" data-satuan_kuantitas="'+data.satuan_kuantitas+'" data-satuan_kualitas="'+data.satuan_kualitas+'" data-satuan_waktu="'+data.satuan_waktu+'" data-targetkn="'+data.target01kn+'" data-targetkl="'+data.target01kl+'" data-targetwk="'+data.target01wk+'" data-realisasikn="'+data.realisasi01kn+'" data-realisasikl="'+data.realisasi01kl+'" data-realisasiwk="'+data.realisasi01wk+'" data-realisasibkn="'+data.realisasi01bkn+'" data-realisasibkl="'+data.realisasi01bkl+'" data-realisasibwk="'+data.realisasi01bwk+'" data-id="'+data.id+'" data-kode_kpi="'+data.kode_kpi+'" data-tahun="'+data.tahun+'" data-polarisasi="'+data.polarisasi+'" data-uraian="'+data.uraian+'"  data-type_target="'+data.type_target+'" data-eviden="'+data.eviden01+'" data-eviden_realisasi="'+data.eviden_realisasi01+'" class="edit_row2" style="pointer:cursor;"><span class="badge bg-warning mb-1" style="width:100%;">'+nilai3+'</span></a>';
                return a;
            }  
        },
        {
            targets: [6], render: function (a, b, data, d) { 
                if(data.target02!=="" && data.target02!==null){
                    var nilai1 = data.target02; 
                } else {
                    var nilai1 = "&nbsp;"; 
                }
                if(data.realisasi02!==""){
                    var nilai2 = data.realisasi02; 
                } else {
                    var nilai2 = "&nbsp;"; 
                }
                if(data.realisasi02b!==""){
                    var nilai3 = data.realisasi02b; 
                } else {
                    var nilai3 = "&nbsp;"; 
                }
                var a = '<span class="badge bg-info mb-1" style="width:100%;">'+nilai1+'</span>';
                a += '<br/><span class="badge bg-success mb-1" style="width:100%;">'+nilai2+'</span>';
                a += '<br/><a href="javascript:void(0)" data-nama_bulan="FEBRUARI" data-nama_target="target02" data-target="'+data.target02+'" data-realisasi="'+data.realisasi02+'" data-satuan_kuantitas="'+data.satuan_kuantitas+'" data-satuan_kualitas="'+data.satuan_kualitas+'" data-satuan_waktu="'+data.satuan_waktu+'" data-targetkn="'+data.target02kn+'" data-targetkl="'+data.target02kl+'" data-targetwk="'+data.target02wk+'" data-realisasikn="'+data.realisasi02kn+'" data-realisasikl="'+data.realisasi02kl+'" data-realisasiwk="'+data.realisasi02wk+'" data-realisasibkn="'+data.realisasi02bkn+'" data-realisasibkl="'+data.realisasi02bkl+'" data-realisasibwk="'+data.realisasi02bwk+'" data-id="'+data.id+'" data-kode_kpi="'+data.kode_kpi+'" data-tahun="'+data.tahun+'" data-polarisasi="'+data.polarisasi+'" data-uraian="'+data.uraian+'"  data-type_target="'+data.type_target+'" data-eviden="'+data.eviden02+'" data-eviden_realisasi="'+data.eviden_realisasi02+'" class="edit_row2" style="pointer:cursor;"><span class="badge bg-warning mb-1" style="width:100%;">'+nilai3+'</span></a>';
                return a;
            }  
        },
        {
            targets: [7], render: function (a, b, data, d) { 
                if(data.target03!=="" && data.target03!==null){
                    var nilai1 = data.target03; 
                } else {
                    var nilai1 = "&nbsp;"; 
                }
                if(data.realisasi03!==""){
                    var nilai2 = data.realisasi03; 
                } else {
                    var nilai2 = "&nbsp;"; 
                }
                if(data.realisasi03b!==""){
                    var nilai3 = data.realisasi03b; 
                } else {
                    var nilai3 = "&nbsp;"; 
                }
                var a = '<span class="badge bg-info mb-1" style="width:100%;">'+nilai1+'</span>';
                a += '<br/><span class="badge bg-success mb-1" style="width:100%;">'+nilai2+'</span>';
                a += '<br/><a href="javascript:void(0)" data-nama_bulan="MARET" data-nama_target="target03" data-target="'+data.target03+'" data-realisasi="'+data.realisasi03+'" data-satuan_kuantitas="'+data.satuan_kuantitas+'" data-satuan_kualitas="'+data.satuan_kualitas+'" data-satuan_waktu="'+data.satuan_waktu+'" data-targetkn="'+data.target03kn+'" data-targetkl="'+data.target03kl+'" data-targetwk="'+data.target03wk+'" data-realisasikn="'+data.realisasi03kn+'" data-realisasikl="'+data.realisasi03kl+'" data-realisasiwk="'+data.realisasi03wk+'" data-realisasibkn="'+data.realisasi03bkn+'" data-realisasibkl="'+data.realisasi03bkl+'" data-realisasibwk="'+data.realisasi03bwk+'" data-id="'+data.id+'" data-kode_kpi="'+data.kode_kpi+'" data-tahun="'+data.tahun+'" data-polarisasi="'+data.polarisasi+'" data-uraian="'+data.uraian+'"  data-type_target="'+data.type_target+'" data-eviden="'+data.eviden03+'" data-eviden_realisasi="'+data.eviden_realisasi03+'" class="edit_row2" style="pointer:cursor;"><span class="badge bg-warning mb-1" style="width:100%;">'+nilai3+'</span></a>';
                return a;
            }  
        },
        {
            targets: [8], render: function (a, b, data, d) { 
                if(data.target04!=="" && data.target04!==null){
                    var nilai1 = data.target04; 
                } else {
                    var nilai1 = "&nbsp;"; 
                }
                if(data.realisasi04!==""){
                    var nilai2 = data.realisasi04;
                } else {
                    var nilai2 = "&nbsp;";
                }
                if(data.realisasi04b!==""){
                    var nilai3 = data.realisasi04b;
                } else {
                    var nilai3 = "&nbsp;"; 
                }
                var a = '<span class="badge bg-info mb-1" style="width:100%;">'+nilai1+'</span>';
                a += '<br/><span class="badge bg-success mb-1" style="width:100%;">'+nilai2+'</span>';
                a += '<br/><a href="javascript:void(0)" data-nama_bulan="APRIL" data-nama_target="target04" data-target="'+data.target04+'" data-realisasi="'+data.realisasi04+'" data-satuan_kuantitas="'+data.satuan_kuantitas+'" data-satuan_kualitas="'+data.satuan_kualitas+'" data-satuan_waktu="'+data.satuan_waktu+'" data-targetkn="'+data.target04kn+'" data-targetkl="'+data.target04kl+'" data-targetwk="'+data.target04wk+'" data-realisasikn="'+data.realisasi04kn+'" data-realisasikl="'+data.realisasi04kl+'" data-realisasiwk="'+data.realisasi04wk+'" data-realisasibkn="'+data.realisasi04bkn+'" data-realisasibkl="'+data.realisasi04bkl+'" data-realisasibwk="'+data.realisasi04bwk+'" data-id="'+data.id+'" data-kode_kpi="'+data.kode_kpi+'" data-tahun="'+data.tahun+'" data-polarisasi="'+data.polarisasi+'" data-uraian="'+data.uraian+'"  data-type_target="'+data.type_target+'" data-eviden="'+data.eviden04+'" data-eviden_realisasi="'+data.eviden_realisasi04+'" class="edit_row2" style="pointer:cursor;"><span class="badge bg-warning mb-1" style="width:100%;">'+nilai3+'</span></a>';
                return a;
            }  
        },
        {
            targets: [9], render: function (a, b, data, d) { 
                if(data.target05!=="" && data.target05!==null){
                    var nilai1 = data.target05;
                } else {
                    var nilai1 = "&nbsp;"; 
                }
                if(data.realisasi05!==""){
                    var nilai2 = data.realisasi05;
                } else {
                    var nilai2 = "&nbsp;";
                }
                if(data.realisasi05b!==""){
                    var nilai3 = data.realisasi05b;
                } else {
                    var nilai3 = "&nbsp;"; 
                }
                var a = '<span class="badge bg-info mb-1" style="width:100%;">'+nilai1+'</span>';
                a += '<br/><span class="badge bg-success mb-1" style="width:100%;">'+nilai2+'</span>';
                a += '<br/><a href="javascript:void(0)" data-nama_bulan="MEI" data-nama_target="target05" data-target="'+data.target05+'" data-satuan_kuantitas="'+data.satuan_kuantitas+'" data-satuan_kualitas="'+data.satuan_kualitas+'" data-satuan_waktu="'+data.satuan_waktu+'" data-targetkn="'+data.target05kn+'" data-targetkl="'+data.target05kl+'" data-targetwk="'+data.target05wk+'" data-realisasikn="'+data.realisasi05kn+'" data-realisasikl="'+data.realisasi05kl+'" data-realisasiwk="'+data.realisasi05wk+'" data-realisasi="'+data.realisasi05+'" data-realisasibkn="'+data.realisasi05bkn+'" data-realisasibkl="'+data.realisasi05bkl+'" data-realisasibwk="'+data.realisasi05bwk+'" data-id="'+data.id+'" data-kode_kpi="'+data.kode_kpi+'" data-tahun="'+data.tahun+'" data-polarisasi="'+data.polarisasi+'" data-uraian="'+data.uraian+'"  data-type_target="'+data.type_target+'" data-eviden="'+data.eviden05+'" data-eviden_realisasi="'+data.eviden_realisasi05+'" class="edit_row2" style="pointer:cursor;"><span class="badge bg-warning mb-1" style="width:100%;">'+nilai3+'</span></a>';
                return a;
            }  
        },
        {
            targets: [10], render: function (a, b, data, d) { 
                if(data.target06!=="" && data.target06!==null){
                    var nilai1 = data.target06;
                } else {
                    var nilai1 = "&nbsp;"; 
                }
                if(data.realisasi06!==""){
                    var nilai2 = data.realisasi06;
                } else {
                    var nilai2 = "&nbsp;";
                }
                if(data.realisasi06b!==""){
                    var nilai3 = data.realisasi06b;
                } else {
                    var nilai3 = "&nbsp;"; 
                }
                var a = '<span class="badge bg-info mb-1" style="width:100%;">'+nilai1+'</span>';
                a += '<br/><span class="badge bg-success mb-1" style="width:100%;">'+nilai2+'</span>';
                a += '<br/><a href="javascript:void(0)" data-nama_bulan="JUNI" data-nama_target="target06" data-target="'+data.target06+'" data-realisasi="'+data.realisasi06+'" data-satuan_kuantitas="'+data.satuan_kuantitas+'" data-satuan_kualitas="'+data.satuan_kualitas+'" data-satuan_waktu="'+data.satuan_waktu+'" data-targetkn="'+data.target06kn+'" data-targetkl="'+data.target06kl+'" data-targetwk="'+data.target06wk+'" data-realisasikn="'+data.realisasi06kn+'" data-realisasikl="'+data.realisasi06kl+'" data-realisasiwk="'+data.realisasi06wk+'" data-realisasibkn="'+data.realisasi06bkn+'" data-realisasibkl="'+data.realisasi06bkl+'" data-realisasibwk="'+data.realisasi06bwk+'" data-id="'+data.id+'" data-kode_kpi="'+data.kode_kpi+'" data-tahun="'+data.tahun+'" data-polarisasi="'+data.polarisasi+'" data-uraian="'+data.uraian+'"  data-type_target="'+data.type_target+'" data-eviden="'+data.eviden06+'" data-eviden_realisasi="'+data.eviden_realisasi06+'" class="edit_row2" style="pointer:cursor;"><span class="badge bg-warning mb-1" style="width:100%;">'+nilai3+'</span></a>';
                return a;
            }  
        },
        {
            targets: [11], render: function (a, b, data, d) { 
                if(data.nilai_semester1!=="" && data.nilai_semester1!==null){
                    var nilai1 = data.nilai_semester1;
                } else {
                    var nilai1 = "&nbsp;"; 
                }
                if(data.nilaib_semester1!==""){
                    var nilai2 = data.nilaib_semester1;
                } else {
                    var nilai2 = "&nbsp;";
                }
                var a = '<span class="badge bg-secondary mb-1" style="width:100%;">R : '+nilai1+'</span>';
                a += '<br/><span class="badge bg-info mb-1" style="width:100%;">F : '+nilai2+'</span>';
                return a;
            }  
        },
        {
            targets: [12], render: function (a, b, data, d) { 
                if(data.target07!=="" && data.target07!==null){
                    var nilai1 = data.target07;
                } else {
                    var nilai1 = "&nbsp;"; 
                }
                if(data.realisasi07!==""){
                    var nilai2 = data.realisasi07;
                } else {
                    var nilai2 = "&nbsp;";
                }
                if(data.realisasi07b!==""){
                    var nilai3 = data.realisasi07b;
                } else {
                    var nilai3 = "&nbsp;"; 
                }
                var a = '<span class="badge bg-info mb-1" style="width:100%;">'+nilai1+'</span>';
                a += '<br/><span class="badge bg-success mb-1" style="width:100%;">'+nilai2+'</span>';
                a += '<br/><a href="javascript:void(0)" data-nama_bulan="JULI" data-nama_target="target07" data-target="'+data.target07+'" data-realisasi="'+data.realisasi07+'" data-satuan_kuantitas="'+data.satuan_kuantitas+'" data-satuan_kualitas="'+data.satuan_kualitas+'" data-satuan_waktu="'+data.satuan_waktu+'" data-targetkn="'+data.target07kn+'" data-targetkl="'+data.target07kl+'" data-targetwk="'+data.target07wk+'" data-realisasikn="'+data.realisasi07kn+'" data-realisasikl="'+data.realisasi07kl+'" data-realisasiwk="'+data.realisasi07wk+'" data-realisasibkn="'+data.realisasi07bkn+'" data-realisasibkl="'+data.realisasi07bkl+'" data-realisasibwk="'+data.realisasi07bwk+'" data-id="'+data.id+'" data-kode_kpi="'+data.kode_kpi+'" data-tahun="'+data.tahun+'" data-polarisasi="'+data.polarisasi+'" data-uraian="'+data.uraian+'"  data-type_target="'+data.type_target+'" data-eviden="'+data.eviden07+'" data-eviden_realisasi="'+data.eviden_realisasi07+'" class="edit_row2" style="pointer:cursor;"><span class="badge bg-warning mb-1" style="width:100%;">'+nilai3+'</span></a>';
                return a;
            }  
        },
        {
            targets: [13], render: function (a, b, data, d) { 
                if(data.target08!=="" && data.target08!==null){
                    var nilai1 = data.target08;
                } else {
                    var nilai1 = "&nbsp;"; 
                }
                if(data.realisasi08!==""){
                    var nilai2 = data.realisasi08;
                } else {
                    var nilai2 = "&nbsp;";
                }
                if(data.realisasi08b!==""){
                    var nilai3 = data.realisasi08b;
                } else {
                    var nilai3 = "&nbsp;"; 
                }
                var a = '<span class="badge bg-info mb-1" style="width:100%;">'+nilai1+'</span>';
                a += '<br/><span class="badge bg-success mb-1" style="width:100%;">'+nilai2+'</span>';
                a += '<br/><a href="javascript:void(0)" data-nama_bulan="AGUSTUS" data-nama_target="target08" data-target="'+data.target08+'" data-realisasi="'+data.realisasi08+'" data-satuan_kuantitas="'+data.satuan_kuantitas+'" data-satuan_kualitas="'+data.satuan_kualitas+'" data-satuan_waktu="'+data.satuan_waktu+'" data-targetkn="'+data.target08kn+'" data-targetkl="'+data.target08kl+'" data-targetwk="'+data.target08wk+'" data-realisasikn="'+data.realisasi08kn+'" data-realisasikl="'+data.realisasi08kl+'" data-realisasiwk="'+data.realisasi08wk+'" data-realisasibkn="'+data.realisasi08bkn+'" data-realisasibkl="'+data.realisasi08bkl+'" data-realisasibwk="'+data.realisasi08bwk+'" data-id="'+data.id+'" data-kode_kpi="'+data.kode_kpi+'" data-tahun="'+data.tahun+'" data-polarisasi="'+data.polarisasi+'" data-uraian="'+data.uraian+'"  data-type_target="'+data.type_target+'" data-eviden="'+data.eviden08+'" data-eviden_realisasi="'+data.eviden_realisasi08+'" class="edit_row2" style="pointer:cursor;"><span class="badge bg-warning mb-1" style="width:100%;">'+nilai3+'</span></a>';
                return a;
            }  
        },
        {
            targets: [14], render: function (a, b, data, d) { 
                if(data.target09!=="" && data.target09!==null){
                    var nilai1 = data.target09;
                } else {
                    var nilai1 = "&nbsp;"; 
                }
                if(data.realisasi09!==""){
                    var nilai2 = data.realisasi09;
                } else {
                    var nilai2 = "&nbsp;";
                }
                if(data.realisasi09b!==""){
                    var nilai3 = data.realisasi09b;
                } else {
                    var nilai3 = "&nbsp;"; 
                }
                var a = '<span class="badge bg-info mb-1" style="width:100%;">'+nilai1+'</span>';
                a += '<br/><span class="badge bg-success mb-1" style="width:100%;">'+nilai2+'</span>';
                a += '<br/><a href="javascript:void(0)" data-nama_bulan="SEPTEMBER" data-nama_target="target09" data-target="'+data.target09+'" data-realisasi="'+data.realisasi09+'" data-satuan_kuantitas="'+data.satuan_kuantitas+'" data-satuan_kualitas="'+data.satuan_kualitas+'" data-satuan_waktu="'+data.satuan_waktu+'" data-targetkn="'+data.target09kn+'" data-targetkl="'+data.target09kl+'" data-targetwk="'+data.target09wk+'" data-realisasikn="'+data.realisasi09kn+'" data-realisasikl="'+data.realisasi09kl+'" data-realisasiwk="'+data.realisasi09wk+'" data-realisasibkn="'+data.realisasi09bkn+'" data-realisasibkl="'+data.realisasi09bkl+'" data-realisasibwk="'+data.realisasi09bwk+'" data-id="'+data.id+'" data-kode_kpi="'+data.kode_kpi+'" data-tahun="'+data.tahun+'" data-polarisasi="'+data.polarisasi+'" data-uraian="'+data.uraian+'"  data-type_target="'+data.type_target+'" data-eviden="'+data.eviden09+'" data-eviden_realisasi="'+data.eviden_realisasi09+'" class="edit_row2" style="pointer:cursor;"><span class="badge bg-warning mb-1" style="width:100%;">'+nilai3+'</span></a>';
                return a;
            }  
        },
        {
            targets: [15], render: function (a, b, data, d) { 
                if(data.target10!=="" && data.target10!==null){
                    var nilai1 = data.target10;
                } else {
                    var nilai1 = "&nbsp;"; 
                }
                if(data.realisasi10!==""){
                    var nilai2 = data.realisasi10;
                } else {
                    var nilai2 = "&nbsp;";
                }
                if(data.realisasi10b!==""){
                    var nilai3 = data.realisasi10b;
                } else {
                    var nilai3 = "&nbsp;"; 
                }
                var a = '<span class="badge bg-info mb-1" style="width:100%;">'+nilai1+'</span>';
                a += '<br/><span class="badge bg-success mb-1" style="width:100%;">'+nilai3+'</span>';
                a += '<br/><a href="javascript:void(0)" data-nama_bulan="OKTOBER" data-nama_target="target10" data-target="'+data.target10+'" data-realisasi="'+data.realisasi10+'" data-satuan_kuantitas="'+data.satuan_kuantitas+'" data-satuan_kualitas="'+data.satuan_kualitas+'" data-satuan_waktu="'+data.satuan_waktu+'" data-targetkn="'+data.target10kn+'" data-targetkl="'+data.target10kl+'" data-targetwk="'+data.target10wk+'" data-realisasikn="'+data.realisasi10kn+'" data-realisasikl="'+data.realisasi10kl+'" data-realisasiwk="'+data.realisasi10wk+'" data-realisasibkn="'+data.realisasi10bkn+'" data-realisasibkl="'+data.realisasi10bkl+'" data-realisasibwk="'+data.realisasi10bwk+'" data-id="'+data.id+'" data-kode_kpi="'+data.kode_kpi+'" data-tahun="'+data.tahun+'" data-polarisasi="'+data.polarisasi+'" data-uraian="'+data.uraian+'"  data-type_target="'+data.type_target+'" data-eviden="'+data.eviden10+'" data-eviden_realisasi="'+data.eviden_realisasi10+'" class="edit_row2" style="pointer:cursor;"><span class="badge bg-warning mb-1" style="width:100%;">'+nilai3+'</span></a>';
                return a;
            }  
        },
        {
            targets: [16], render: function (a, b, data, d) { 
                if(data.target11!=="" && data.target11!==null){
                    var nilai1 = data.target11;
                } else {
                    var nilai1 = "&nbsp;"; 
                }
                if(data.realisasi11!==""){
                    var nilai2 = data.realisasi11;
                } else {
                    var nilai2 = "&nbsp;";
                }
                if(data.realisasi11b!==""){
                    var nilai3 = data.realisasi11b;
                } else {
                    var nilai3 = "&nbsp;"; 
                }
                var a = '<span class="badge bg-info mb-1" style="width:100%;">'+nilai1+'</span>';
                a += '<br/><span class="badge bg-success mb-1" style="width:100%;">'+nilai2+'</span>';
                a += '<br/><a href="javascript:void(0)" data-nama_bulan="NOPEMBER" data-nama_target="target11" data-target="'+data.target11+'" data-realisasi="'+data.realisasi11+'" data-satuan_kuantitas="'+data.satuan_kuantitas+'" data-satuan_kualitas="'+data.satuan_kualitas+'" data-satuan_waktu="'+data.satuan_waktu+'" data-targetkn="'+data.target11kn+'" data-targetkl="'+data.target11kl+'" data-targetwk="'+data.target11wk+'" data-realisasikn="'+data.realisasi11kn+'" data-realisasikl="'+data.realisasi11kl+'" data-realisasiwk="'+data.realisasi11wk+'" data-realisasibkn="'+data.realisasi11bkn+'" data-realisasibkl="'+data.realisasi11bkl+'" data-realisasibwk="'+data.realisasi11bwk+'" data-id="'+data.id+'" data-kode_kpi="'+data.kode_kpi+'" data-tahun="'+data.tahun+'" data-polarisasi="'+data.polarisasi+'" data-uraian="'+data.uraian+'"  data-type_target="'+data.type_target+'" data-eviden="'+data.eviden11+'" data-eviden_realisasi="'+data.eviden_realisasi11+'" class="edit_row2" style="pointer:cursor;"><span class="badge bg-warning mb-1" style="width:100%;">'+nilai3+'</span></a>';
                return a;
            }  
        },
        {
            targets: [17], render: function (a, b, data, d) { 
                if(data.target12!=="" && data.target12!==null){
                    var nilai1 = data.target12;
                } else {
                    var nilai1 = "&nbsp;"; 
                }
                if(data.realisasi12!==""){
                    var nilai2 = data.realisasi12;
                } else {
                    var nilai2 = "&nbsp;";
                }
                if(data.realisasi12b!==""){
                    var nilai3 = data.realisasi12b;
                } else {
                    var nilai3 = "&nbsp;"; 
                }
                var a = '<span class="badge bg-info mb-1" style="width:100%;">'+nilai1+'</span>';
                a += '<br/><span class="badge bg-success mb-1" style="width:100%;">'+nilai2+'</span>';
                a += '<br/><a href="javascript:void(0)" data-nama_bulan="DESEMBER" data-nama_target="target12" data-target="'+data.target12+'" data-realisasi="'+data.realisasi12+'" data-satuan_kuantitas="'+data.satuan_kuantitas+'" data-satuan_kualitas="'+data.satuan_kualitas+'" data-satuan_waktu="'+data.satuan_waktu+'" data-targetkn="'+data.target12kn+'" data-targetkl="'+data.target12kl+'" data-targetwk="'+data.target12wk+'" data-realisasikn="'+data.realisasi12kn+'" data-realisasikl="'+data.realisasi12kl+'" data-realisasiwk="'+data.realisasi12wk+'" data-realisasibkn="'+data.realisasi12bkn+'" data-realisasibkl="'+data.realisasi12bkl+'" data-realisasibwk="'+data.realisasi12bwk+'" data-id="'+data.id+'" data-kode_kpi="'+data.kode_kpi+'" data-tahun="'+data.tahun+'" data-polarisasi="'+data.polarisasi+'" data-uraian="'+data.uraian+'"  data-type_target="'+data.type_target+'" data-eviden="'+data.eviden12+'" data-eviden_realisasi="'+data.eviden_realisasi12+'" class="edit_row2" style="pointer:cursor;"><span class="badge bg-warning mb-1" style="width:100%;">'+nilai3+'</span></a>';
                return a;
            }  
        },
        {
            targets: [18], render: function (a, b, data, d) { 
                if(data.nilai_semester2!=="" && data.nilai_semester2!==null){
                    var nilai1 = data.nilai_semester2;
                } else {
                    var nilai1 = "&nbsp;"; 
                }
                if(data.nilaib_semester2!==""){
                    var nilai2 = data.nilaib_semester2;
                } else {
                    var nilai2 = "&nbsp;";
                }
                var a = '<span class="badge bg-secondary mb-1" style="width:100%;">R : '+nilai1+'</span>';
                a += '<br/><span class="badge bg-info mb-1" style="width:100%;">F : '+nilai2+'</span>';
                return a;
            }  
        },
        ],
        "ordering": false,
        "stateSave": true,
        "scrollX": true,
        "ScrollXInner": true,
        "autoWidth": false,
        "pagingType": 'simple_numbers',
        drawCallback: function(settings) {
            initializeGaugeCharts();
            initializeGaugeCharts2();
        }
    });
    document.querySelector('div.head-label_tbl_list2').innerHTML = '<h5 class="card-title text-nowrap mb-0">Pencapaian KPI</h5>';

    table.on('preXhr.dt', function() {
        $('.dataTables_wrapper').addClass('loading');
        $('#datatable-loader').fadeIn(200);
    });
    table.on('xhr.dt', function() {
        $('.dataTables_wrapper').removeClass('loading');
        $('#datatable-loader').fadeOut(300);
    });

    table2.on('preXhr.dt', function() {
        $('.dataTables_wrapper').addClass('loading');
        $('#datatable-loader').fadeIn(200);
    });
    table2.on('xhr.dt', function() {
        $('.dataTables_wrapper').removeClass('loading');
        $('#datatable-loader').fadeOut(300);
    });

    table.search('').draw();

    $('#filternya').on("click", function() {
        table.draw();
    });         
    
    $('#filternya').on("click", function() {
        table.draw();
    });         
    
    $('body').on('click', '.detail_row', function () {
        $("#lbljudul").hide();
        $("#rinciandata").show();
        // var tahuncari = $("#tahuncari").val();
        // var tahuncari = $(this).data('tahuncari');
        // alert(tahuncari);
        var tahuncari = $(this).data('tahun');
        var nipcari = $(this).data('nip');
        var namacari = $(this).data('nama');
        var jabatancari = $(this).data('jabatan');
        var jenis_kpicari = $(this).data('jenis_kpi');
        var level_kpicari = $(this).data('level_kpi');
        var kd_divisicari = $(this).data('kd_divisi');
        var nama_level_kpicari = $(this).data('nama_level_kpi');
        // alert(tahuncari);
        $('#tahuncari2').val(tahuncari);
        $('#nipcari2').val(nipcari);
        $("#tabdata").removeClass('active');
        $("#navs-top-data").removeClass('show active');
        $("#tabdetail").addClass('active');
        $("#navs-top-detail").addClass('show active');
        $("#lbltahun").text("Tahun : "+tahuncari);
        $("#lblnip").text("Nip : "+nipcari);
        $("#lblnama").text("Nama : "+namacari);
        $("#lbljabatan").text("Jabatan : "+jabatancari);
        $("#lblnama_level_kpi").text("Level KPI : "+nama_level_kpicari);
        table2.columns.adjust().draw();
    });    

    $('#tabdata').click(function(e){ 
        $('#tahuncari2').val('');
        $('#nipcari2').val('');
        $('#jenis_kpicari2').val('');
        $('#level_kpicari2').val('');
        $('#kd_divisicari2').val('');
        $("#tabdata").addClass('active');
        $("#navs-top-data").addClass('show active');
        $("#tabdetail").removeClass('active');
        $("#navs-top-detail").removeClass('show active');
        // table.draw(); 
        // table.ajax.reload(null, false);
    });

    $('#tabdetail').click(function(e){ 
        var tahuncari2 = $('#tahuncari2').val();
        var nipcari2 = $('#nipcari2').val();
        if(tahuncari2==="" || tahuncari2===null || tahuncari2===undefined){
            $("#lbljudul").show();
            $("#rinciandata").hide();
            $("#lbltahun").text("");
            $("#lblnip").text("");
            $("#lblnama").text("");
            $("#lbljabatan").text("");
            $("#lblnama_level_kpi").text("");
        } else {
            $("#lbljudul").hide();
            $("#rinciandata").show();
        }
        $("#tabdata").removeClass('active');
        $("#navs-top-data").removeClass('show active');
        $("#tabdetail").addClass('active');
        $("#navs-top-detail").addClass('show active');
        table2.columns.adjust().draw();
    });

    $('body').on('click', '.pilih_row', function () {
        var id = $(this).data("id");
        var nip = $(this).data("nip");
        var tahun = $(this).data("tahun");
        var kode_cascading = $(this).data("kode_cascading");
        event.preventDefault();
        $.ajax({
            type: "POST",
            data: {
                nip: nip,
                tahun: tahun,
                kode_cascading: kode_cascading,
                _token: '{{csrf_token()}}'
            },
            url: "{{url('api/pilih-datapencapaianm')}}",
            success: function (data) {
                table2.draw();
            },
            error: function (data) {
                console.log('Error:', data);
            }
        });
        
    });

    $('body').on('click', '.batal_row', function () {
        var id2 = $(this).data("id2");
        event.preventDefault();
        $.ajax({
            type: "POST",
            data: {
                id2: id2,
                _token: '{{csrf_token()}}'
            },
            url: "{{url('api/batal-datapencapaianm')}}",
            success: function (data) {
                table2.draw();
            },
            error: function (data) {
                console.log('Error:', data);
            }
        });
        
    });

    $('body').on('click', '.edit_row2', function () {
        var nama_bulan = $(this).data('nama_bulan');
        var nama_target = $(this).data('nama_target');
        var id = $(this).data('id');
        var kode_kpi = $(this).data('kode_kpi');
        var tahun = $(this).data('tahun');
        var uraian = $(this).data('uraian');
        var polarisasi = $(this).data('polarisasi');
        var type_target = $(this).data('type_target');
        var satuan_kuantitas = $(this).data('satuan_kuantitas');
        var satuan_kualitas = $(this).data('satuan_kualitas');
        var satuan_waktu = $(this).data('satuan_waktu');
        var target = $(this).data('target');
        var targetkn = $(this).data('targetkn');
        var targetkl = $(this).data('targetkl');
        var targetwk = $(this).data('targetwk');
        var realisasi = $(this).data('realisasi');
        var realisasikn = $(this).data('realisasikn');
        var realisasikl = $(this).data('realisasikl');
        var realisasiwk = $(this).data('realisasiwk');
        var realisasibkn = $(this).data('realisasibkn');
        var realisasibkl = $(this).data('realisasibkl');
        var realisasibwk = $(this).data('realisasibwk');
        var eviden_realisasi = $(this).data('eviden_realisasi');
        var eviden = $(this).data('eviden');
        // alert(eviden_realisasi+" "+eviden);
        // var id = $(this).data('id');
        $('#modelHeading2').html("Update Finalisasi KPI");
        $('#ModalForm2').modal('show');
        $('#nama_bulan2').val(nama_bulan);
        $('#nama_target2').val(nama_target);
        $('#id2').val(id);
        $('#kode_kpi2').val(kode_kpi);
        $('#satuan_kuantitas2').val(satuan_kuantitas);
        $('#satuan_kualitas2').val(satuan_kualitas);
        $('#satuan_waktu2').val(satuan_waktu);
        $('#tahun2').val(tahun);
        $('#uraian2').val(uraian);                
        $('#polarisasi2').val(polarisasi);
        $('#type_target2').val(type_target);
        if(satuan_kuantitas!=="" && satuan_kuantitas!==null && satuan_kuantitas!==undefined){
            $('#lblsatuan_kuantitas').text(satuan_kuantitas);
        } else {
            $('#lblsatuan_kuantitas').text("");
        }
        if(satuan_kualitas!=="" && satuan_kualitas!==null && satuan_kualitas!==undefined){
            $('#lblsatuan_kualitas').text(satuan_kualitas);
        } else {
            $('#lblsatuan_kualitas').text("");
        }
        if(satuan_waktu!=="" && satuan_waktu!==null && satuan_waktu!==undefined){
            $('#lblsatuan_waktu').text(satuan_waktu);
        } else {
            $('#lblsatuan_waktu').text("");
        }
        $('#targetkn2').val(targetkn);
        $('#targetkl2').val(targetkl);
        $('#targetwk2').val(targetwk);
        $('#realisasikn2').val(realisasikn);
        $('#realisasikl2').val(realisasikl);
        $('#realisasiwk2').val(realisasiwk);
        $('#realisasibkn2').val(realisasibkn);
        $('#realisasibkl2').val(realisasibkl);
        $('#realisasibwk2').val(realisasibwk);
        if(eviden_realisasi!=="" && eviden_realisasi!==null && eviden_realisasi!==undefined){
            $("#div_eviden_realisasi2").html('<span style="font-size:12px;"></span><a target="_blank" href="'+url_evidenkpi+'/'+eviden_realisasi+'"><span style="font-size:12px;">'+eviden_realisasi+'</span></a>');
        } else {
            $("#div_eviden_realisasi2").html('<span class="text-danger" style="font-size:12px;">-</span>');
        }
        if(eviden!=="" && eviden!==null && eviden!==undefined){
            $("#div_eviden2").html('<span style="font-size:12px;"></span><a target="_blank" href="'+url_evidenkpi+'/'+eviden+'"><span style="font-size:12px;">'+eviden+'</span></a>');
        } else {
            $("#div_eviden2").html('<span class="text-danger" style="font-size:12px;">-</span>');
        }

        if(targetkn!=="" && targetkn!==null && targetkn!==undefined && targetkn.trim()!==""){
            $('#realisasibkn2').removeAttr('readonly');
        } else {
            // $('#realisasibkn2').attr('readonly','readonly');
        }
        if(targetkl!=="" && targetkl!==null && targetkl!==undefined && targetkl.trim()!==""){
            $('#realisasibkl2').removeAttr('readonly');
        } else {
            // $('#realisasibkl2').attr('readonly','readonly');
        }
        if(targetwk!=="" && targetwk!==null && targetwk!==undefined && targetwk.trim()!==""){
            $('#realisasibwk2').removeAttr('readonly');
        } else {
            // $('#realisasibwk2').attr('readonly','readonly');
        }
    });         
    $('#saveBtn2').on("click", function(e) {
        e.preventDefault();
        $(this).prepend('<i class="fa fa-spinner fa-spin"></i>');
        var datanya = $('#dataForm2').serialize();
        // alert(datanya);
        $.ajax({
            data: $('#dataForm2').serialize(),
            url: "{{ route('datapencapaianm.store') }}",
            type: "POST",
            dataType: 'json',
            success: function (data) { 
                $('#saveBtn2').find(".fa-spinner").remove();
                if(data.status === "sukses"){  
                    $('#dataForm2').trigger("reset");
                    $('#ModalForm2').modal('hide');
                    // table2.draw();
                    table2.ajax.reload(null, false);
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

    $('body').on('click', '.hitung_atasan', function (e) {
        var nip = $(this).data('nip');
        var nama = $(this).data('nama');
        // alert(nip);
        e.preventDefault();
        Swal.fire({
            title: 'Hitung KPI atasan AN.'+nama+', Lanjutkan?',
            // text: "Data yang sudah terhapus tidak dapat dikembalikan.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hitung!',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            customClass: {
                confirmButton: 'btn btn-primary',
                cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                let $btn = $(this);
                if ($btn.data('loading')) return;
                $btn.data('loading', true).prop('disabled', true);
                $btn.data('orig-html', $btn.html());
                var text = $btn.find('.btn-text').text() || $btn.text().trim();
                var spinner = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>';
                $btn.html(spinner + '<span class="btn-text">' + text + '</span>');
                // $btn.html(spinner);
                $.ajax({
                    type: "POST",
                    data: {
                        nip: nip,
                        _token: '{{csrf_token()}}'
                    },
                    url: "{{route('datapencapaianm.hitung-kpi-atasan')}}",
                    success: function (data) {   
                        $btn.html($btn.data('orig-html'));
                        $btn.prop('disabled', false).data('loading', false);
                        Swal.fire('Sukses','Sukses hitung KPI atasan.', 'success').then(() => table.draw());
                        // table.draw();
                    },
                    error: function (data) {
                        // console.log('Error:', data);
                        $btn.html($btn.data('orig-html'));
                        $btn.prop('disabled', false).data('loading', false);
                        Swal.fire('Error', 'Gagal hitung KPI atasan.', 'error');
                    }
                });
            }
        });   
             
    }); 

    $('.hitung_all_atasan').on("click", function(e) {
        // var tahuncari = $("#tahuncari").val();
        var jenis_kpicari = $("#jenis_kpicari").val();
        e.preventDefault();
        Swal.fire({
            title: 'Hitung KPI Semua Atasan, Lanjutkan?',
            // text: "Data yang sudah terhapus tidak dapat dikembalikan.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hitung!',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            customClass: {
                confirmButton: 'btn btn-primary',
                cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                let $btn = $(this);
                if ($btn.data('loading')) return;
                $btn.data('loading', true).prop('disabled', true);
                $btn.data('orig-html', $btn.html());
                var text = $btn.find('.btn-text').text() || $btn.text().trim();
                var spinner = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>';
                $btn.html(spinner + '<span class="btn-text">' + text + '</span>');
                // $btn.html(spinner);
                $.ajax({
                    type: "POST",
                    data: {
                        jenis_kpicari: jenis_kpicari,
                        _token: '{{csrf_token()}}'
                    },
                    url: "{{route('datapencapaianm.hitung-all-atasan')}}",
                    success: function (data) {   
                        $btn.html($btn.data('orig-html'));
                        $btn.prop('disabled', false).data('loading', false);
                        Swal.fire('Sukses','Sukses hitung KPI semua atasan.', 'success').then(() => table.draw());
                        // table.draw();
                    },
                    error: function (data) {
                        // console.log('Error:', data);
                        $btn.html($btn.data('orig-html'));
                        $btn.prop('disabled', false).data('loading', false);
                        Swal.fire('Error', 'Gagal hitung KPI semua atasan.', 'error');
                    }
                });
            }
        });   
             
    }); 

});
</script>
<script>
    
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

    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script> -->

    <!-- <script src="{{ asset('assets/libs/air-datepicker/air-datepicker.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/air-datepicker.init.js') }}"></script> -->
    <!-- <script>
    window.datepickerLocaleID = {
        days: ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
        daysShort: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
        daysMin: ['Mg', 'Sn', 'Sl', 'Rb', 'Km', 'Jm', 'Sb'],
        months: [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ],
        monthsShort: [
            'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
            'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'
        ],
        today: 'Hari ini',
        clear: 'Hapus',
        dateFormat: 'dd/MM/yyyy',
        firstDay: 1
    };
    </script> -->
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
        $('#kd_area').on('change', function () {
            var kd_area = this.value;
            // alert(kd_region);
            $("#level_kpi").html('');
            $.ajax({
                url: "{{url('api/fetch-level-mappingpegawaim')}}",
                type: "POST",
                data: {
                    kd_area: kd_area,
                    _token: '{{csrf_token()}}'
                },
                dataType: 'json',
                success: function (result) {
                    // alert(JSON.stringify(result));
                    $('#level_kpi').html('<option value="">-- Pilih Level --</option>');
                    $.each(result.filter_level, function (key, value) {
                        $("#level_kpi").append('<option value="' + value.level_kpi + '">' + value.nama_level_kpi + '</option>');
                    });
                }
            });
        });


    });
    </script>
    <script>
        function initializeGaugeCharts() {
            $('[id^="chart-"]').each(function() {
                const chartElement = $(this);
                const chartId = chartElement.attr('id');
                const score = parseFloat(chartElement.data('score'));
                const target = parseFloat(chartElement.data('target'));
                const percentage = parseFloat(chartElement.data('percentage'));
                
                // Skip if chart already exists
                if (chartElement.hasClass('chart-initialized')) {
                    return;
                }
                
                createSolidGauge(chartId, score, target, percentage);
                chartElement.addClass('chart-initialized');
            });
        }

        function initializeGaugeCharts2() {
            $('[id^="chart2-"]').each(function() {
                const chartElement = $(this);
                const chartId = chartElement.attr('id');
                const score = parseFloat(chartElement.data('score'));
                const target = parseFloat(chartElement.data('target'));
                const percentage = parseFloat(chartElement.data('percentage'));
                
                // Skip if chart already exists
                if (chartElement.hasClass('chart-initialized')) {
                    return;
                }
                
                createSolidGauge(chartId, score, target, percentage);
                chartElement.addClass('chart-initialized');
            });
        }

        function createSolidGauge(containerId, score, target, percentage) {
            const gaugeColor = getGaugeColor(percentage);

            Highcharts.chart(containerId, {
                chart: {
                    type: 'gauge',
                    backgroundColor: null,
                    plotBackgroundColor: null,
                    plotBackgroundImage: null,
                    plotBorderWidth: 0,
                    plotShadow: false,
                    height: '80px',
                    spacing: [0, 0, 0, 0],
                    margin: [0, 0, 0, 0]
                },

                title: {
                    text: ''
                },
                credits: {
                    enabled: false
                },
                tooltip: {
                    enabled: false
                },
                pane: {
                    startAngle: -90,
                    endAngle: 89.9,
                    background: null,
                    center: ['50%', '65%'],
                    size: '110%'
                },
                yAxis: {
                    min: 0,
                    max: 200,
                    tickPixelInterval: 10,
                    tickPosition: 'inside',
                    tickColor: Highcharts.defaultOptions.chart.backgroundColor || '#FFFFFF',
                    tickLength: 10,
                    tickWidth: 0,
                    minorTickInterval: null,
                    labels: {
                        // enabled:false,
                        distance: -17,
                        style: {
                            fontSize: '8px'
                        }
                    },
                    lineWidth: 0,
                    plotBands: [{
                        from: 0,
                        to: 94.9,
                        color: '#DF5353', // red                        
                        thickness: 10,
                        borderRadius: '50%'
                    }, {
                        from: 95,
                        to: 99.9,
                        color: '#DDDF0D', // yellow                        
                        thickness: 10,
                        borderRadius: '50%'
                    }, {
                        from: 100,
                        to: 110,
                        color: '#55BF3B', // green
                        thickness: 10
                    }, {
                        from: 110.01,
                        to: 200,
                        color: '#444444', // darkgrey
                        thickness: 10
                    }]
                },

                series: [{
                    name: 'Pencapaian',
                    data: [score],
                    tooltip: {                        
                        valueSuffix: '%'
                    },
                    dataLabels: {                        
                        format: '{y}%',
                        borderWidth: 0,
                        color: (
                            Highcharts.defaultOptions.title &&
                            Highcharts.defaultOptions.title.style &&
                            Highcharts.defaultOptions.title.style.color
                        ) || '',
                        style: {
                            fontSize: '13px'
                        }
                    },
                    dial: {
                        radius: '80%',
                        backgroundColor: 'gray',
                        baseWidth: 12,
                        baseLength: '0%',
                        rearLength: '0%'
                    },
                    pivot: {
                        backgroundColor: 'gray',
                        radius: 6
                    }
                }]
            });            
        }

        // Function to determine gauge color based on percentage
        function getGaugeColor(percentage) {
            if (percentage >= 100) return '#55BF3B';
            if (percentage >= 90) return '#F2C464';
            // if (percentage >= 60) return '#ffc107';
            return '#DF5353';
        }        
    </script> 
    <script>
      $(function () {
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
          
          // $('#ModalUpdate2 .select2').each(function() {  
          //     var $p5 = $(this).parent(); 
          //     $(this).select2({  
          //         dropdownParent: $p5
          //     });  
          // }); 
          // $('#ModalProses .select2').each(function() {  
          //     var $p6 = $(this).parent(); 
          //     $(this).select2({  
          //         dropdownParent: $p6
          //     });  
          // }); 
      });
  </script>

    <!-- App js -->
    <script type="module" src="{{ asset('assets/js/app.js') }}"></script>
@endsection
