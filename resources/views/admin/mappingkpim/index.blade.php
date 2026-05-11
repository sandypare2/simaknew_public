
@extends('partials.layouts.master3')

@section('title', 'Mapping KPI | SIMAK')
@section('sub-title', 'Mapping KPI ' )
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
                            <i class="ri-user-settings-line fs-15 me-2"></i>Mapping KPI
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="javascript:void(0)" class="nav-link fs-13" id="tabrincian" data-bs-toggle="tab" data-bs-target="#navs-top-rincian" role="tab" aria-selected="false" tabindex="-1">
                            <i class="ri-price-tag-line fs-15 me-2"></i>Target KPI
                        </a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active show" id="navs-top-data" aria-labelledby="tabdata" role="tabpanel">
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
                                    <div class="col-md-3 grid-margin">
                                        <select id="kd_areacari" name="kd_areacari" class="form-control select2">
                                            <option value="semua" selected>SEMUA</option>
                                            @foreach ($masteraream as $data)
                                                <option value="{{ $data->kd_area }}">{{ $data->nama_area }}</option>                                    
                                            @endforeach
                                        </select>                            

                                    </div>
                                    <div class="col-md-7 grid-margin">
                                        <button type="button" id="filternya" class="btn btn-info btn-sm"><i class="ri-search-line me-1"></i>Filter Data</button>                            
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-datatable text-nowrap">
                        <table id="tbl_list" class="table data-table-responsive table-hover align-middle table table-nowrap w-100 custom-table">
                            <thead>
                            <tr>
                                <th>Aksi</th>
                                <th>Nomor Induk</th>
                                <th>Nama</th>
                                <th>Jabatan</th>
                                <th>Nama Area</th>
                                <th>Jenis KPI</th>
                                <th>Level KPI</th>
                                <th>Divisi</th>
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
                                <input type="hidden" name="kd_areacari2" id="kd_areacari2">
                                <input type="hidden" name="jenis_kpicari2" id="jenis_kpicari2">
                                <input type="hidden" name="level_kpicari2" id="level_kpicari2">
                                <input type="hidden" name="kd_divisicari2" id="kd_divisicari2">
                                <h5 id="lbljudul" class="card-title font-size-12">No data selected.</h5>
                                <div id="rinciandata" class="card-body column-gap-8 d-flex flex-wrap align-items-center justify-content-start">
                                    <label class="font-size-12" id="lbltahun" style="font-weight:bold;"></label>
                                    <label class="font-size-12" id="lblnip" style="font-weight:bold;"></label>
                                    <label class="font-size-12" id="lblnama" style="font-weight:bold;"></label>
                                    <label class="font-size-12" id="lblnama_area" style="font-weight:bold;"></label>
                                    <label class="font-size-12" id="lblnama_level_kpi" style="font-weight:bold;"></label>
                                    <label class="font-size-12" id="lblnama_divisi" style="font-weight:bold;"></label>
                                    <label class="font-size-12" id="lbljabatan" style="font-weight:bold;"></label>
                                </div>
                            </div>
                        </div>

                        <div class="card-datatable text-nowrap">
                        <table id="tbl_list2" class="table data-table-responsive table-hover align-middle table table-nowrap w-100 custom-table">
                            <thead>
                            <tr>
                                <th>Aksi</th>
                                <th>Divisi</th>
                                <!-- <th>Kode Cascading</th> -->
                                <th>Uraian KPI</th>
                                <th>Pilih</th>
                            </tr>
                            </thead>
                        </table>
                        </div>  
                    </div>
                    <div class="tab-pane" id="navs-top-rincian" aria-labelledby="tabrincian" role="tabpanel">
                        <input type="hidden" name="tahuncari3" id="tahuncari3">
                        <input type="hidden" name="nipcari3" id="nipcari3">
                        <input type="hidden" name="kd_areacari3" id="kd_areacari3">
                        <input type="hidden" name="jenis_kpicari3" id="jenis_kpicari3">
                        <input type="hidden" name="level_kpicari3" id="level_kpicari3">
                        <div class="card-datatable text-nowrap">
                        <table id="tbl_list3" class="table data-table-responsive table-hover align-middle table table-nowrap w-100 custom-table">
                            <thead>
                            <tr>
                                <!-- <th rowspan="2">Aksi</th> -->
                                <th rowspan="2">Tahun</th>
                                <!-- <th rowspan="2">Kode Cascading</th> -->
                                <th rowspan="2">Uraian KPI</th>
                                <th rowspan="2">Ket</th>
                                <th rowspan="2">Satuan</th>
                                <th colspan="6" class="center">Semester 1</th>
                                <th colspan="6" class="center">Semester 2</th>
                            </tr>
                            <tr>
                                <th>Jan</th>
                                <th>Feb</th>
                                <th>Mar</th>
                                <th>Apr</th>
                                <th>Mei</th>
                                <th>Jun</th>
                                <th>Jul</th>
                                <th>Agt</th>
                                <th>Sep</th>
                                <th>Okt</th>
                                <th>Nop</th>
                                <th>Des</th>
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


<div class="modal" id="ModalForm" tabindex="-1" aria-hidden="true">
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
                    @csrf
                    <div class="mb-2">
                        <label class="form-label-custom text-muted font-size-12" for="basic-icon-default-fullname">Kode Cascading</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control" id="kode_cascading" name="kode_cascading" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" readonly />
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label-custom text-muted font-size-12" for="basic-icon-default-fullname">Uraian KPI</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control" id="uraian" name="uraian" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" readonly />
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label-custom text-muted font-size-12" for="basic-icon-default-fullname">Januari</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control" id="target01" name="target01" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label-custom text-muted font-size-12" for="basic-icon-default-fullname">Februari</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control" id="target02" name="target02" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label-custom text-muted font-size-12" for="basic-icon-default-fullname">Maret</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control" id="target03" name="target03" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label-custom text-muted font-size-12" for="basic-icon-default-fullname">April</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control" id="target04" name="target04" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label-custom text-muted font-size-12" for="basic-icon-default-fullname">Mei</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control" id="target05" name="target05" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label-custom text-muted font-size-12" for="basic-icon-default-fullname">Juni</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control" id="target06" name="target06" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label-custom text-muted font-size-12" for="basic-icon-default-fullname">Juli</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control" id="target07" name="target07" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label-custom text-muted font-size-12" for="basic-icon-default-fullname">Agustus</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control" id="target08" name="target08" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label-custom text-muted font-size-12" for="basic-icon-default-fullname">September</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control" id="target09" name="target09" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label-custom text-muted font-size-12" for="basic-icon-default-fullname">Oktober</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control" id="target10" name="target10" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label-custom text-muted font-size-12" for="basic-icon-default-fullname">Nopember</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control" id="target11" name="target11" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label-custom text-muted font-size-12" for="basic-icon-default-fullname">Desember</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control" id="target12" name="target12" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
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
            url: "{{ route('mappingkpim') }}",
            data: function (d) {
                d.tahuncari = $('#tahuncari').val(),
                d.kd_areacari = $('#kd_areacari').val(),
                d.search = $('#tbl_list_filter input').val()
            }
        },  
        columns: [
            {data: 'aksi', name:'aksi',width:'50px', orderable: false, searchable: false},
            {data: 'nip',name:'nip',width:'100px',className: 'dt-center'},
            {data: 'nama',name:'nama',width:'160px',className: 'dt-left wrap'},
            {data: 'jabatan',name:'jabatan',width:'200px',className: 'dt-left wrap'},
            {data: 'nama_area',name:'nama_area',width:'160px',className: 'dt-left wrap'},
            {data: 'jenis_kpi',name:'jenis_kpi',width:'100px',className: 'dt-center'},
            {data: 'nama_level_kpi',name:'nama_level_kpi',width:'140px',className: 'dt-left wrap'},
            {data: 'nama_divisi',name:'nama_divisi',width:'100px',className: 'dt-center'},
        ],
        columnDefs: [
        {
            targets: [2], render: function (a, b, data, d) { 
                var a = '<div style="width:160px;">';
                a += '<span>'+data.nama+'</span>';
                a += '</div>';
                return a;
            }  
        },
        {
            targets: [3], render: function (a, b, data, d) { 
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
        {
            targets: [5], render: function (a, b, data, d) { 
                if(data.jenis_kpi!=="" && data.jenis_kpi!==null && data.jenis_kpi!==undefined){
                    return data.jenis_kpi.toUpperCase();
                } else {
                    return data.jenis_kpi;
                }
            }  
        },
        {
            targets: [6], render: function (a, b, data, d) { 
                var a = '<div style="width:140px;">';
                a += '<span>'+data.nama_level_kpi+'</span>';
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
            url: "{{ url('api/fetch-detail-mappingkpim') }}",
            data: function (d) {
                d.tahuncari = $('#tahuncari2').val(),
                d.nipcari = $('#nipcari2').val(),
                d.kd_areacari = $('#kd_areacari2').val(),
                d.jenis_kpicari = $('#jenis_kpicari2').val(),
                d.level_kpicari = $('#level_kpicari2').val(),
                d.kd_divisicari = $('#kd_divisicari2').val(),
                d.search = $('#tbl_list2_filter input').val()
            }
        },  
        columns: [
            {data: 'aksi', name:'aksi',width:'50px', orderable: false, searchable: false},
            {data: 'nama_divisi',name:'nama_divisi',width:'160px',className: 'dt-left wrap'},
            // {data: 'kd_urut',name:'kd_urut',width:'100px',className: 'dt-center'},
            {data: 'uraian',name:'uraian',width:'300px',className: 'dt-left wrap'},
            {data: 'pilihan',name:'pilihan',width:'50px',className: 'dt-center'},
        ],
        columnDefs: [
        {
            targets: [1], render: function (a, b, data, d) { 
                var a = '<div style="width:160px;">';
                a += '<span>'+data.nama_divisi+'</span>';
                a += '</div>';
                return a;
            }  
        },
        {
            targets: [2], render: function (a, b, data, d) { 
                var a = '<div style="width:300px;">';
                a += '<span>'+data.uraian+'</span>';
                a += '</div>';
                return a;
            }  
        },
        {
            targets: [3], render: function (a, b, data, d) { 
                if(parseInt(data.pilihan)===1){
                    return '<i class="ri-checkbox-circle-fill text-success fs-22"></i>';
                } else {
                    return '<i class="ri-close-circle-fill text-danger fs-22"></i>';
                }
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
    document.querySelector('div.head-label_tbl_list2').innerHTML = '<h5 class="card-title text-nowrap mb-0">Rincian KPI</h5>';

    var table3 = $('#tbl_list3').DataTable({
        initComplete: function() {    
            var api = this.api();
            $('#tbl_list3_filter input').unbind();
            $('#tbl_list3_filter input').bind('keyup', function(e) {
                if(e.keyCode == 13) {
                    api.search(this.value).draw();
                }
            });
        },
        dom: '<"card-header dt-head d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3"' +
            '<"head-label_tbl_list3">' +
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
            url: "{{ url('api/fetch-rincian-mappingkpim') }}",
            data: function (d) {
                d.tahuncari = $('#tahuncari3').val(),
                d.nipcari = $('#nipcari3').val(),
                d.kd_areacari = $('#kd_areacari3').val(),
                d.search = $('#tbl_list3_filter input').val()
            }
        },  
        columns: [
            // {data: 'aksi', name:'aksi',width:'50px', orderable: false, searchable: false},
            {data: 'tahun',name:'tahun',width:'80px',className: 'dt-center'},
            {data: 'uraian',name:'uraian',width:'250px',className: 'dt-left wrap'},
            {data: 'ket',name:'ket',width:'50px',className: 'dt-center'},
            {data: 'satuan',name:'satuan',width:'50px',className: 'dt-center'},
            {data: 'jan',name:'jan',width:'50px',className: 'dt-center'},
            {data: 'feb',name:'feb',width:'50px',className: 'dt-center'},
            {data: 'mar',name:'mar',width:'50px',className: 'dt-center'},
            {data: 'apr',name:'apr',width:'50px',className: 'dt-center'},
            {data: 'mei',name:'mei',width:'50px',className: 'dt-center'},
            {data: 'jun',name:'jun',width:'50px',className: 'dt-center'},
            {data: 'jul',name:'jul',width:'50px',className: 'dt-center'},
            {data: 'agt',name:'agt',width:'50px',className: 'dt-center'},
            {data: 'sep',name:'sep',width:'50px',className: 'dt-center'},
            {data: 'okt',name:'okt',width:'50px',className: 'dt-center'},
            {data: 'nop',name:'nop',width:'50px',className: 'dt-center'},
            {data: 'des',name:'des',width:'50px',className: 'dt-center'},
        ],
        columnDefs: [
        {
            targets: [1], render: function (a, b, data, d) { 
                var a = '<div style="width:250px;">';
                a += '<span>'+data.uraian+'</span>';
                a += '</div>';
                return a;
            }  
        },
        {
            targets: [2], render: function (a, b, data, d) { 
                var a = '<span class="badge bg-primary mb-1" style="width:100%;">Kuantitas</span>';
                a += '<br/><span class="badge bg-success mb-1" style="width:100%;">Kualitas</span>';
                a += '<br/><span class="badge bg-warning mb-1" style="width:100%;">Waktu</span>';
                a += '<br/><span class="badge bg-info mb-1" style="width:100%;">Prosentase</span>';
                return a;
            }  
        },
        {
            targets: [3], render: function (a, b, data, d) {
                if(data.satuan_kuantitas!=="" && data.satuan_kuantitas!==null){
                    var nilai1 = data.satuan_kuantitas;
                } else {
                    var nilai1 = "&nbsp;";
                }
                if(data.satuan_kualitas!=="" && data.satuan_kualitas!==null){
                    var nilai2 = data.satuan_kualitas;
                } else {
                    var nilai2 = "&nbsp;";
                }
                if(data.satuan_waktu!=="" && data.satuan_waktu!==null){
                    var nilai3 = data.satuan_waktu;
                } else {
                    var nilai3 = "&nbsp;";
                }
                var nilai4 = "Persen";
                var a = '<span class="badge bg-primary mb-1" style="width:100%;">'+nilai1+'</span>';
                a += '<br/><span class="badge bg-success mb-1" style="width:100%;">'+nilai2+'</span>';
                a += '<br/><span class="badge bg-warning mb-1" style="width:100%;">'+nilai3+'</span>';
                a += '<br/><span class="badge bg-info mb-1" style="width:100%;">'+nilai4+'</span>';
                return a;
            }  
        },
        {
            targets: [4], render: function (a, b, data, d) { 
                if(data.target01kn!=="" && data.target01kn!==null){
                    var nilai1 = data.target01kn;
                } else {
                    var nilai1 = "&nbsp;";
                }
                if(data.target01kl!=="" && data.target01kl!==null){
                    var nilai2 = data.target01kl;
                } else {
                    var nilai2 = "&nbsp;";
                }
                if(data.target01wk!=="" && data.target01wk!==null){
                    var nilai3 = data.target01wk;
                } else {
                    var nilai3 = "&nbsp;";
                }
                if(data.target01!=="" && data.target01!==null){
                    var nilai4 = data.target01;
                } else {
                    var nilai4 = "&nbsp;";
                }
                var a = '<span class="badge bg-primary mb-1" style="width:100%;">'+nilai1+'</span>';
                a += '<br/><span class="badge bg-success mb-1" style="width:100%;">'+nilai2+'</span>';
                a += '<br/><span class="badge bg-warning mb-1" style="width:100%;">'+nilai3+'</span>';
                a += '<br/><span class="badge bg-info mb-1" style="width:100%;">'+nilai4+'</span>';
                return a;
            }  
        },
        {
            targets: [5], render: function (a, b, data, d) { 
                if(data.target02kn!=="" && data.target02kn!==null){
                    var nilai1 = data.target02kn;
                } else {
                    var nilai1 = "&nbsp;";
                }
                if(data.target02kl!=="" && data.target02kl!==null){
                    var nilai2 = data.target02kl;
                } else {
                    var nilai2 = "&nbsp;";
                }
                if(data.target02wk!=="" && data.target02wk!==null){
                    var nilai3 = data.target02wk;
                } else {
                    var nilai3 = "&nbsp;";
                }
                if(data.target02!=="" && data.target02!==null){
                    var nilai4 = data.target02;
                } else {
                    var nilai4 = "&nbsp;";
                }
                var a = '<span class="badge bg-primary mb-1" style="width:100%;">'+nilai1+'</span>';
                a += '<br/><span class="badge bg-success mb-1" style="width:100%;">'+nilai2+'</span>';
                a += '<br/><span class="badge bg-warning mb-1" style="width:100%;">'+nilai3+'</span>';
                a += '<br/><span class="badge bg-info mb-1" style="width:100%;">'+nilai4+'</span>';
                return a;
            }  
        },
        {
            targets: [6], render: function (a, b, data, d) { 
                if(data.target03kn!=="" && data.target03kn!==null){
                    var nilai1 = data.target03kn;
                } else {
                    var nilai1 = "&nbsp;";
                }
                if(data.target03kl!=="" && data.target03kl!==null){
                    var nilai2 = data.target03kl;
                } else {
                    var nilai2 = "&nbsp;";
                }
                if(data.target03wk!=="" && data.target03wk!==null){
                    var nilai3 = data.target03wk;
                } else {
                    var nilai3 = "&nbsp;";
                }
                if(data.target03!=="" && data.target03!==null){
                    var nilai4 = data.target03;
                } else {
                    var nilai4 = "&nbsp;";
                }
                var a = '<span class="badge bg-primary mb-1" style="width:100%;">'+nilai1+'</span>';
                a += '<br/><span class="badge bg-success mb-1" style="width:100%;">'+nilai2+'</span>';
                a += '<br/><span class="badge bg-warning mb-1" style="width:100%;">'+nilai3+'</span>';
                a += '<br/><span class="badge bg-info mb-1" style="width:100%;">'+nilai4+'</span>';
                return a;
            }  
        },
        {
            targets: [7], render: function (a, b, data, d) { 
                if(data.target04kn!=="" && data.target04kn!==null){
                    var nilai1 = data.target04kn;
                } else {
                    var nilai1 = "&nbsp;";
                }
                if(data.target04kl!=="" && data.target04kl!==null){
                    var nilai2 = data.target04kl;
                } else {
                    var nilai2 = "&nbsp;";
                }
                if(data.target04wk!=="" && data.target04wk!==null){
                    var nilai3 = data.target04wk;
                } else {
                    var nilai3 = "&nbsp;";
                }
                if(data.target04!=="" && data.target04!==null){
                    var nilai4 = data.target04;
                } else {
                    var nilai4 = "&nbsp;";
                }
                var a = '<span class="badge bg-primary mb-1" style="width:100%;">'+nilai1+'</span>';
                a += '<br/><span class="badge bg-success mb-1" style="width:100%;">'+nilai2+'</span>';
                a += '<br/><span class="badge bg-warning mb-1" style="width:100%;">'+nilai3+'</span>';
                a += '<br/><span class="badge bg-info mb-1" style="width:100%;">'+nilai4+'</span>';
                return a;
            }  
        },
        {
            targets: [8], render: function (a, b, data, d) { 
                if(data.target05kn!=="" && data.target05kn!==null){
                    var nilai1 = data.target05kn;
                } else {
                    var nilai1 = "&nbsp;";
                }
                if(data.target05kl!=="" && data.target05kl!==null){
                    var nilai2 = data.target05kl;
                } else {
                    var nilai2 = "&nbsp;";
                }
                if(data.target05wk!=="" && data.target05wk!==null){
                    var nilai3 = data.target05wk;
                } else {
                    var nilai3 = "&nbsp;";
                }
                if(data.target05!=="" && data.target05!==null){
                    var nilai4 = data.target05;
                } else {
                    var nilai4 = "&nbsp;";
                }
                var a = '<span class="badge bg-primary mb-1" style="width:100%;">'+nilai1+'</span>';
                a += '<br/><span class="badge bg-success mb-1" style="width:100%;">'+nilai2+'</span>';
                a += '<br/><span class="badge bg-warning mb-1" style="width:100%;">'+nilai3+'</span>';
                a += '<br/><span class="badge bg-info mb-1" style="width:100%;">'+nilai4+'</span>';
                return a;
            }  
        },
        {
            targets: [9], render: function (a, b, data, d) { 
                if(data.target06kn!=="" && data.target06kn!==null){
                    var nilai1 = data.target06kn;
                } else {
                    var nilai1 = "&nbsp;";
                }
                if(data.target06kl!=="" && data.target06kl!==null){
                    var nilai2 = data.target06kl;
                } else {
                    var nilai2 = "&nbsp;";
                }
                if(data.target06wk!=="" && data.target06wk!==null){
                    var nilai3 = data.target06wk;
                } else {
                    var nilai3 = "&nbsp;";
                }
                if(data.target06!=="" && data.target06!==null){
                    var nilai4 = data.target06;
                } else {
                    var nilai4 = "&nbsp;";
                }
                var a = '<span class="badge bg-primary mb-1" style="width:100%;">'+nilai1+'</span>';
                a += '<br/><span class="badge bg-success mb-1" style="width:100%;">'+nilai2+'</span>';
                a += '<br/><span class="badge bg-warning mb-1" style="width:100%;">'+nilai3+'</span>';
                a += '<br/><span class="badge bg-info mb-1" style="width:100%;">'+nilai4+'</span>';
                return a;
            }  
        },
        {
            targets: [10], render: function (a, b, data, d) { 
                if(data.target07kn!=="" && data.target07kn!==null){
                    var nilai1 = data.target07kn;
                } else {
                    var nilai1 = "&nbsp;";
                }
                if(data.target07kl!=="" && data.target07kl!==null){
                    var nilai2 = data.target07kl;
                } else {
                    var nilai2 = "&nbsp;";
                }
                if(data.target07wk!=="" && data.target07wk!==null){
                    var nilai3 = data.target07wk;
                } else {
                    var nilai3 = "&nbsp;";
                }
                if(data.target07!=="" && data.target07!==null){
                    var nilai4 = data.target07;
                } else {
                    var nilai4 = "&nbsp;";
                }
                var a = '<span class="badge bg-primary mb-1" style="width:100%;">'+nilai1+'</span>';
                a += '<br/><span class="badge bg-success mb-1" style="width:100%;">'+nilai2+'</span>';
                a += '<br/><span class="badge bg-warning mb-1" style="width:100%;">'+nilai3+'</span>';
                a += '<br/><span class="badge bg-info mb-1" style="width:100%;">'+nilai4+'</span>';
                return a;
            }  
        },
        {
            targets: [11], render: function (a, b, data, d) { 
                if(data.target08kn!=="" && data.target08kn!==null){
                    var nilai1 = data.target08kn;
                } else {
                    var nilai1 = "&nbsp;";
                }
                if(data.target08kl!=="" && data.target08kl!==null){
                    var nilai2 = data.target08kl;
                } else {
                    var nilai2 = "&nbsp;";
                }
                if(data.target08wk!=="" && data.target08wk!==null){
                    var nilai3 = data.target08wk;
                } else {
                    var nilai3 = "&nbsp;";
                }
                if(data.target08!=="" && data.target08!==null){
                    var nilai4 = data.target08;
                } else {
                    var nilai4 = "&nbsp;";
                }
                var a = '<span class="badge bg-primary mb-1" style="width:100%;">'+nilai1+'</span>';
                a += '<br/><span class="badge bg-success mb-1" style="width:100%;">'+nilai2+'</span>';
                a += '<br/><span class="badge bg-warning mb-1" style="width:100%;">'+nilai3+'</span>';
                a += '<br/><span class="badge bg-info mb-1" style="width:100%;">'+nilai4+'</span>';
                return a;
            }  
        },
        {
            targets: [12], render: function (a, b, data, d) { 
                if(data.target09kn!=="" && data.target09kn!==null){
                    var nilai1 = data.target09kn;
                } else {
                    var nilai1 = "&nbsp;";
                }
                if(data.target09kl!=="" && data.target09kl!==null){
                    var nilai2 = data.target09kl;
                } else {
                    var nilai2 = "&nbsp;";
                }
                if(data.target09wk!=="" && data.target09wk!==null){
                    var nilai3 = data.target09wk;
                } else {
                    var nilai3 = "&nbsp;";
                }
                if(data.target09!=="" && data.target09!==null){
                    var nilai4 = data.target09;
                } else {
                    var nilai4 = "&nbsp;";
                }
                var a = '<span class="badge bg-primary mb-1" style="width:100%;">'+nilai1+'</span>';
                a += '<br/><span class="badge bg-success mb-1" style="width:100%;">'+nilai2+'</span>';
                a += '<br/><span class="badge bg-warning mb-1" style="width:100%;">'+nilai3+'</span>';
                a += '<br/><span class="badge bg-info mb-1" style="width:100%;">'+nilai4+'</span>';
                return a;
            }  
        },
        {
            targets: [13], render: function (a, b, data, d) { 
                if(data.target10kn!=="" && data.target10kn!==null){
                    var nilai1 = data.target10kn;
                } else {
                    var nilai1 = "&nbsp;";
                }
                if(data.target10kl!=="" && data.target10kl!==null){
                    var nilai2 = data.target10kl;
                } else {
                    var nilai2 = "&nbsp;";
                }
                if(data.target10wk!=="" && data.target10wk!==null){
                    var nilai3 = data.target10wk;
                } else {
                    var nilai3 = "&nbsp;";
                }
                if(data.target10!=="" && data.target10!==null){
                    var nilai4 = data.target10;
                } else {
                    var nilai4 = "&nbsp;";
                }
                var a = '<span class="badge bg-primary mb-1" style="width:100%;">'+nilai1+'</span>';
                a += '<br/><span class="badge bg-success mb-1" style="width:100%;">'+nilai2+'</span>';
                a += '<br/><span class="badge bg-warning mb-1" style="width:100%;">'+nilai3+'</span>';
                a += '<br/><span class="badge bg-info mb-1" style="width:100%;">'+nilai4+'</span>';
                return a;
            }  
        },
        {
            targets: [14], render: function (a, b, data, d) { 
                if(data.target11kn!=="" && data.target11kn!==null){
                    var nilai1 = data.target11kn;
                } else {
                    var nilai1 = "&nbsp;";
                }
                if(data.target11kl!=="" && data.target11kl!==null){
                    var nilai2 = data.target11kl;
                } else {
                    var nilai2 = "&nbsp;";
                }
                if(data.target11wk!=="" && data.target11wk!==null){
                    var nilai3 = data.target11wk;
                } else {
                    var nilai3 = "&nbsp;";
                }
                if(data.target11!=="" && data.target11!==null){
                    var nilai4 = data.target11;
                } else {
                    var nilai4 = "&nbsp;";
                }
                var a = '<span class="badge bg-primary mb-1" style="width:100%;">'+nilai1+'</span>';
                a += '<br/><span class="badge bg-success mb-1" style="width:100%;">'+nilai2+'</span>';
                a += '<br/><span class="badge bg-warning mb-1" style="width:100%;">'+nilai3+'</span>';
                a += '<br/><span class="badge bg-info mb-1" style="width:100%;">'+nilai4+'</span>';
                return a;
            }  
        },
        {
            targets: [15], render: function (a, b, data, d) { 
                if(data.target12kn!=="" && data.target12kn!==null){
                    var nilai1 = data.target12kn;
                } else {
                    var nilai1 = "&nbsp;";
                }
                if(data.target12kl!=="" && data.target12kl!==null){
                    var nilai2 = data.target12kl;
                } else {
                    var nilai2 = "&nbsp;";
                }
                if(data.target12wk!=="" && data.target12wk!==null){
                    var nilai3 = data.target12wk;
                } else {
                    var nilai3 = "&nbsp;";
                }
                if(data.target12!=="" && data.target12!==null){
                    var nilai4 = data.target12;
                } else {
                    var nilai4 = "&nbsp;";
                }
                var a = '<span class="badge bg-primary mb-1" style="width:100%;">'+nilai1+'</span>';
                a += '<br/><span class="badge bg-success mb-1" style="width:100%;">'+nilai2+'</span>';
                a += '<br/><span class="badge bg-warning mb-1" style="width:100%;">'+nilai3+'</span>';
                a += '<br/><span class="badge bg-info mb-1" style="width:100%;">'+nilai4+'</span>';
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
    document.querySelector('div.head-label_tbl_list3').innerHTML = '<h5 class="card-title text-nowrap mb-0">Target KPI</h5>';


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

    table3.on('preXhr.dt', function() {
        $('.dataTables_wrapper').addClass('loading');
        $('#datatable-loader').fadeIn(200);
    });
    table3.on('xhr.dt', function() {
        $('.dataTables_wrapper').removeClass('loading');
        $('#datatable-loader').fadeOut(300);
    });

    table.search('').draw();

    $('#filternya').on("click", function() {
        table.draw();
    });         
    
    $('body').on('click', '.detail_row', function () {
        $("#lbljudul").hide();
        $("#rinciandata").show();
        // var tahuncari = $("#tahuncari").val();
        var tahuncari = $(this).data('tahun');
        var kd_areacari = $(this).data('kd_area');
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
        $('#kd_areacari2').val(kd_areacari);
        $('#jenis_kpicari2').val(jenis_kpicari);
        $('#level_kpicari2').val(level_kpicari);
        $('#kd_divisicari2').val(kd_divisicari);
        $('#tahuncari3').val(tahuncari);
        $('#nipcari3').val(nipcari);
        $('#kd_areacari3').val(kd_areacari);
        $('#jenis_kpicari3').val(jenis_kpicari);
        $('#level_kpicari3').val(level_kpicari);
        // $('#kd_divisicari3').val(kd_divisicari);
        $("#tabdata").removeClass('active');
        $("#navs-top-data").removeClass('show active');
        $("#tabdetail").addClass('active');
        $("#navs-top-detail").addClass('show active');
        $("#tabrincian").removeClass('active');
        $("#navs-top-rincian").removeClass('show active');
        $("#lbltahun").text("Tahun : "+tahuncari);
        $("#lblnip").text("Nip : "+nipcari);
        $("#lblnama").text("Nama : "+namacari);
        $("#lbljabatan").text("Jabatan : "+jabatancari);
        $("#lblnama_level_kpi").text("Level KPI : "+nama_level_kpicari);
        table2.columns.adjust().draw();
        table3.columns.adjust().draw();
    });    

    $('#tabdata').click(function(e){ 
        $('#tahuncari2').val('');
        $('#nipcari2').val('');
        $('#kd_areacari2').val('');
        $('#jenis_kpicari2').val('');
        $('#level_kpicari2').val('');
        $('#kd_divisicari2').val('');
        $('#tahuncari3').val('');
        $('#nipcari3').val('');
        $('#kd_areacari3').val('');
        $('#jenis_kpicari3').val('');
        $('#level_kpicari3').val('');
        $("#tabdata").addClass('active');
        $("#navs-top-data").addClass('show active');
        $("#tabdetail").removeClass('active');
        $("#navs-top-detail").removeClass('show active');
        $("#tabrincian").removeClass('active');
        $("#navs-top-rincian").removeClass('show active');
        // table.draw(); 
        table.ajax.reload(null, false);
    });

    $('#tabdetail').click(function(e){ 
        var tahuncari2 = $('#tahuncari2').val();
        var nipcari2 = $('#nipcari2').val();
        var jenis_kpicari2 = $('#jenis_kpicari2').val();
        var level_kpicari2 = $('#level_kpicari2').val();
        var kd_divisicari2 = $('#kd_divisicari2').val();
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
        $("#tabrincian").removeClass('active');
        $("#navs-top-rincian").removeClass('show active');
        // table2.columns.adjust().draw();
        // table3.columns.adjust().draw();
        table2.ajax.reload(null, false);
    });

    $('#tabrincian').click(function(e){ 
        $("#tabdata").removeClass('active');
        $("#navs-top-data").removeClass('show active');
        $("#tabdetail").removeClass('active');

        $("#navs-top-detail").removeClass('show active');
        $("#tabrincian").addClass('active');
        $("#navs-top-rincian").addClass('show active');
        // table2.columns.adjust().draw();
        // table3.columns.adjust().draw();
        table3.ajax.reload(null, false);
    });

    $('body').on('click', '.pilih_row', function () {
        var id = $(this).data("id");
        var nip = $(this).data("nip");
        var tahun = $(this).data("tahun");
        var jenis_kpi = $(this).data("jenis_kpi");
        var kd_divisi = $(this).data("kd_divisi");
        var level_kpi = $(this).data("level_kpi");
        var kode_cascading = $(this).data("kode_cascading");
        var kode_cascading2 = $(this).data("kode_cascading2");
        event.preventDefault();
        $.ajax({
            type: "POST",
            data: {
                nip: nip,
                tahun: tahun,
                jenis_kpi: jenis_kpi,
                kd_divisi: kd_divisi,
                level_kpi: level_kpi,
                kode_cascading: kode_cascading,
                kode_cascading2: kode_cascading2,
                _token: '{{csrf_token()}}'
            },
            url: "{{url('api/pilih-mappingkpim')}}",
            success: function (data) {
                // table2.draw();
                table2.ajax.reload(null, false);
            },
            error: function (data) {
                console.log('Error:', data);
            }
        });
        
    });

    $('body').on('click', '.batal_row', function () {
        var id2 = $(this).data("id2");
        var kode_kpi2 = $(this).data("kode_kpi2");
        event.preventDefault();
        $.ajax({
            type: "POST",
            data: {
                id2: id2,
                kode_kpi2: kode_kpi2,
                _token: '{{csrf_token()}}'
            },
            url: "{{url('api/batal-mappingkpim')}}",
            success: function (data) {
                // table2.draw();
                table2.ajax.reload(null, false);
            },
            error: function (data) {
                console.log('Error:', data);
            }
        });
        
    });
    
    $('body').on('click', '.edit_row', function () {
        var id = $(this).data('id');
        $.get("{{ route('mappingkpim') }}" +'/' + id, function (data) {   
            $('#modelHeading').html("Edit Target KPI");
            $('#ModalForm').modal('show');
            $('#id').val(data.id);
            $('#kode_cascading').val(data.kode_cascading);
            $('#uraian').val(data.uraian);
            $('#target01').val(data.target01);
            $('#target02').val(data.target02);
            $('#target03').val(data.target03);
            $('#target04').val(data.target04);
            $('#target05').val(data.target05);
            $('#target06').val(data.target06);
            $('#target07').val(data.target07);
            $('#target08').val(data.target08);
            $('#target09').val(data.target09);
            $('#target10').val(data.target10);
            $('#target11').val(data.target11);
            $('#target12').val(data.target12);
        })
    }); 
    
    $('#saveBtn').on("click", function(e) {
        e.preventDefault();
        $(this).prepend('<i class="fa fa-spinner fa-spin"></i>');
        var datanya = $('#dataForm').serialize();
        // alert(datanya);
        $.ajax({
            data: $('#dataForm').serialize(),
            url: "{{ route('mappingkpim.store') }}",
            type: "POST",
            dataType: 'json',
            success: function (data) { 
                $('#saveBtn').find(".fa-spinner").remove();
                if(data.status === "sukses"){  
                    $('#dataForm').trigger("reset");
                    $('#ModalForm').modal('hide');
                    table3.draw();
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
    $('#cancelBtn').on("click", function(e) {
        e.preventDefault();
        $('#dataForm').trigger("reset");
        $('#ModalForm').modal('hide');
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
        $('#kd_area').on('select2:select', function () {
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
