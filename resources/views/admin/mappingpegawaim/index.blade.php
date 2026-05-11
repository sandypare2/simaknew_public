
@extends('partials.layouts.master3')

@section('title', 'History Talenta | SIMAK')
@section('sub-title', 'History Talenta ' )
@section('pagetitle', 'Admin Simkp')

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
                        <div class="row flex-grow-1">
                            <div class="col-md-3 grid-margin">
                                <select id="kd_areacari" name="kd_areacari" class="form-control form-control-sm select2">
                                    <option value="semua" selected>SEMUA</option>
                                    @foreach ($masteraream as $data)
                                        <option value="{{ $data->kd_area }}">{{ $data->nama_area }}</option>                                    
                                    @endforeach
                                </select>                            
                            </div>
                            <div class="col-md-6 grid-margin">
                                <button type="button" id="filternya" class="btn btn-info btn-sm"><i class="ri-search-line me-1"></i>Filter Data</button>
                                <!-- <a title="Pembaharuan Data" class="pembaharuan_row"><button type="button" class="btn btn-primary btn-sm"><i class="ri-refresh-line me-1"></i>Pembaharuan Data</button></a> -->
                                <a title="Tambah Data" class="add_row"><button type="button" class="btn btn-primary btn-sm"><i class="ri-add-line me-1"></i>Tambah Pegawai</button></a>
                            </div>
                        </div>    
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="tbl_list" class="table data-table-responsive table-hover align-middle table table-nowrap w-100 custom-table">
                        <thead>
                        <tr>
                            <th class="center">Aksi</th>
                            <th>Nip</th>
                            <th>Nama</th>
                            <th>Jabatan</th>
                            <th>Jenis Pegawi</th>
                            <th>Jenjang Jabatan</th>
                            <th class="center">Grade</th>
                            <th class="center">PeG</th>
                            <th>Approval KPI</th>
                            <th>Finalisasi KPI</th>
                            <th>Area/Site</th>
                            <th class="center">Jenis KPI</th>
                            <th>Level KPI</th>
                            <th>Divisi</th>
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
                    @csrf
                    <div class="mb-2">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Nomor Induk</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control form-control-sm" id="nip" name="nip" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Nama</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control form-control-sm" id="nama" name="nama" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Jabatan</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control form-control-sm" id="jabatan" name="jabatan" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Jenis Pegawai</label>
                        <div class="input-group input-group-merge">
                            <select id="kd_jenis" name="kd_jenis" class="form-control form-control-sm select2" style="width:100%;">
                                <option value="" selected>--Pilih Jenis Pegawai--</option>
                                @foreach ($masterjenispegawai as $row)
                                    <option value="{{ $row->kd_jenis }}">{{ $row->nama_jenis }}</option>                                    
                                @endforeach
                            </select>                            
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Jenjang Jabatan</label>
                        <div class="input-group input-group-merge">
                            <select id="jenjang_jabatan" name="jenjang_jabatan" class="form-control form-control-sm select2" style="width:100%;">
                                <option value="" selected>--Pilih Jenjang Jabatan--</option>
                                @foreach ($jenjangjabatanm as $data)
                                    <option value="{{ $data->jenjang_jabatan }}">{{ $data->jenjang_jabatan }}</option>                                    
                                @endforeach
                            </select>                            
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Grade</label>
                        <div class="input-group input-group-merge">
                            <select id="grade" name="grade" class="form-control form-control-sm select2" style="width:100%;">
                                <option value="" selected>--Pilih Grade--</option>
                                @foreach ($gradem as $data)
                                    <option value="{{ $data->grade }}">{{ $data->grade }}</option>                                    
                                @endforeach
                            </select>                            
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">PeG</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control form-control-sm" id="peg" name="peg" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Cabang/Site</label>
                        <div class="input-group input-group-merge">
                            <select id="kd_area" name="kd_area" class="form-control form-control-sm select2" style="width:100%;">
                                <option value="" selected>--Pilih Cabang/Site--</option>
                                @foreach ($masteraream as $data)
                                    <option value="{{ $data->kd_area }}">{{ $data->nama_area }}</option>                                    
                                @endforeach
                            </select>                            
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Level KPI</label>
                        <div class="input-group input-group-merge">
                            <select id="level_kpi" name="level_kpi" class="form-control form-control-sm select2" style="width:100%;">
                            </select>                            
                        </div>
                    </div>  
                    <div class="mb-2">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Divisi</label>
                        <div class="input-group input-group-merge">
                            <select id="kd_divisi" name="kd_divisi" class="form-control form-control-sm select2" style="width:100%;">
                                <option value="" selected>-</option>
                                @foreach ($masterdivisim as $data)
                                    <option value="{{ $data->kd_divisi }}">{{ $data->nama_divisi }}</option>                                    
                                @endforeach
                            </select>                            
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Approval KPI</label>
                        <div class="input-group input-group-merge">
                            <select id="approval" name="approval" class="form-control form-control-sm select2" style="width:100%;">
                                <option value="" selected>-</option>
                                @foreach ($mappingpegawaim as $data)
                                    <option value="{{ $data->nip }}">{{ $data->nama }}</option>                                    
                                @endforeach
                            </select>                            
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Finalisasi KPI</label>
                        <div class="input-group input-group-merge">
                            <select id="finalisasi" name="finalisasi" class="form-control form-control-sm select2" style="width:100%;">
                                <option value="" selected>-</option>
                                @foreach ($mappingpegawaim as $data)
                                    <option value="{{ $data->nip }}">{{ $data->nama }}</option>                                    
                                @endforeach
                            </select>                            
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-footer">
                <button type="button" id="saveBtn" class="btn btn-primary"><i class="ri-save-2-line me-1"></i>Simpan</button>
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
    $('#datatable-loader').show();
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

    var tgl_awalcari = $("#tgl_awalcari").val();
    var tgl_akhircari = $("#tgl_akhircari").val();
    if(tgl_awalcari==="" || tgl_akhircari===""){
        $("#tgl_awalcari").val("<?=date('d/m/Y');?>");
        $("#tgl_akhircari").val("<?=date('d/m/Y');?>");
    }

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
            url: "{{ route('mappingpegawaim') }}",
            data: function (d) {
                d.kd_areacari = $('#kd_areacari').val(),
                d.search = $('#tbl_list_filter input').val()
            }
        },            
        columns: [
            {data: 'aksi', name:'aksi',width:'50px',className: 'dt-center', orderable: false, searchable: false},
            {data: 'nip',name:'nip',width:'90px',className: 'dt-center'},
            {data: 'nama',name:'nama',width:'160px',className: 'dt-left wrap'},
            {data: 'jabatan',name:'jabatan',width:'200px',className: 'dt-left wrap'},
            {data: 'nama_jenis',name:'nama_jenis',width:'80px',className: 'dt-center'},
            {data: 'jenjang_jabatan',name:'jenjang_jabatan',width:'160px',className: 'dt-left wrap'},
            {data: 'grade',name:'grade',width:'100px',className: 'dt-center'},
            {data: 'peg',name:'peg',width:'60px',className: 'dt-center'},
            {data: 'nama_approval',name:'nama_approval',width:'200px',className: 'dt-left wrap'},
            {data: 'nama_finalisasi',name:'nama_finalisasi',width:'200px',className: 'dt-left wrap'},
            {data: 'nama_area',name:'nama_area',width:'160px',className: 'dt-left wrap'},
            {data: 'jenis_kpi',name:'jenis_kpi',width:'100px',className: 'dt-center'},
            {data: 'nama_level_kpi',name:'nama_level_kpi',width:'140px',className: 'dt-left wrap'},
            {data: 'nama_divisi',name:'nama_divisi',width:'160px',className: 'dt-left wrap'},
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
            targets: [5], render: function (a, b, data, d) { 
                var a = '<div style="width:160px;">';
                if(data.jenjang_jabatan!==null && data.jenjang_jabatan!==undefined){
                    a += '<span>'+data.jenjang_jabatan+'</span>';
                }
                a += '</div>';
                return a;
            }  
        },
        {
            targets: [8], render: function (a, b, data, d) { 
                var a = '<div style="width:200px;">';
                a += '<span>'+data.nama_approval+'</span>';
                a += '</div>';
                return a;
            }  
        },
        {
            targets: [9], render: function (a, b, data, d) { 
                var a = '<div style="width:200px;">';
                a += '<span>'+data.nama_finalisasi+'</span>';
                a += '</div>';
                return a;
            }  
        },
        {
            targets: [10], render: function (a, b, data, d) { 
                var a = '<div style="width:160px;">';
                a += '<span>'+data.nama_area+'</span>';
                a += '</div>';
                return a;
            }  
        },
        {
            targets: [11], render: function (a, b, data, d) { 
                if(data.jenis_kpi!=="" && data.jenis_kpi!==null && data.jenis_kpi!==undefined){
                    return data.jenis_kpi.toUpperCase();
                } else {
                    return data.jenis_kpi;
                }
            }  
        },
        {
            targets: [12], render: function (a, b, data, d) { 
                var a = '<div style="width:140px;">';
                a += '<span>'+data.nama_level_kpi+'</span>';
                a += '</div>';
                return a;
            }  
        },
        {
            targets: [13], render: function (a, b, data, d) { 
                var a = '<div style="width:160px;">';
                a += '<span>'+data.nama_divisi+'</span>';
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
    document.querySelector('div.head-label_tbl_list').innerHTML = '<h5 class="card-title text-nowrap mb-0">Mapping Pegawai</h5>';

    table.on('preXhr.dt', function() {
        $('.dataTables_wrapper').addClass('loading');
        $('#datatable-loader').fadeIn(200);
    });
    table.on('xhr.dt', function() {
        $('.dataTables_wrapper').removeClass('loading');
        $('#datatable-loader').fadeOut(300);
    });

    $('#filternya').on("click", function() {
        table.draw();
    });         

    // $(document).ajaxStart(function () {
    //     $("#datatable-loader").fadeIn(150);
    // });

    // $(document).ajaxStop(function () {
    //     $("#datatable-loader").fadeOut(150);
    // });        

    $('.add_row').click(function () {
        $('#id').val('');
        $('#dataForm').trigger("reset");
        $('#modelHeading').html("Input Data Pegawai");
        $('#ModalForm').modal('show');
        $('#kd_area').val('').trigger('change');
        $('#kd_divisi').val('').trigger('change');
        $('#approval').val('').trigger('change');
        $('#finalisasi').val('').trigger('change');

    });        

    $('body').on('click', '.edit_row', function () {
        var id = $(this).data('id');
        $.get("{{ route('mappingpegawaim') }}" +'/' + id, function (data) {   
            $('#modelHeading').html("Edit Mapping Pegawai");
            $('#ModalForm').modal('show');
            $('#id').val(data.id);
            $('#nip').val(data.nip);
            $('#nama').val(data.nama);
            $('#jabatan').val(data.jabatan);
            $('#kd_jenis').val(data.kd_jenis).trigger('change');
            $('#grade').val(data.grade).trigger('change');
            $('#peg').val(data.peg);
            $('#kd_area').val(data.kd_area).trigger('change');
            $('#kd_divisi').val(data.kd_divisi).trigger('change');
            $('#approval').val(data.approval).trigger('change');
            $('#finalisasi').val(data.finalisasi).trigger('change');
            // alert(data.level_kpi);
            $("#level_kpi").html('');
            $.ajax({
                url: "{{url('api/fetch-level-mappingpegawaim')}}",
                type: "POST",
                data: {
                    kd_area: data.kd_area,
                    _token: '{{csrf_token()}}'
                },
                dataType: 'json',
                success: function (result) {
                    // alert(JSON.stringify(result));
                    $('#level_kpi').html('<option value="">-- Pilih Level --</option>');
                    $.each(result.filter_level, function (key, value) {
                        if(value.level_kpi==data.level_kpi){
                            $("#level_kpi").append('<option value="' + value.level_kpi + '" selected>' + value.nama_level_kpi + '</option>');
                        } else {
                            $("#level_kpi").append('<option value="' + value.level_kpi + '">' + value.nama_level_kpi + '</option>');
                        }
                    });
                }
            });
            $('#level_kpi').val(data.level_kpi).trigger('change');
        })
    }); 

    $('body').on('click', '.delete_row', function () {
        var id = $(this).data("id");
        event.preventDefault();
        Swal.fire({
            title: 'Anda yakin akan menghapus data ini?',
            text: "Data yang sudah terhapus tidak dapat dikembalikan.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            customClass: {
                confirmButton: 'btn btn-danger',
                cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                let $btn = $(this);
                if ($btn.data('loading')) return;
                $btn.data('loading', true).prop('disabled', true);
                $btn.data('orig-html', $btn.html());
                // var text = $btn.find('.btn-text').text() || $btn.text().trim();
                var spinner = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>';
                // $btn.html(spinner + '<span class="btn-text">' + text + '</span>');
                $btn.html(spinner);
                $.ajax({
                    type: "POST",
                    data: {
                        id: id,
                        _token: '{{csrf_token()}}'
                    },
                    url: "{{url('api/hapus-mappingpegawaim')}}",
                    success: function (data) {
                        $btn.html($btn.data('orig-html'));
                        $btn.prop('disabled', false).data('loading', false);
                        Swal.fire('Sukses','Sukses hapus data.', 'success').then(() => table.ajax.reload(null, false));
                    },
                    error: function (data) {
                        // console.log('Error:', data);
                        Swal.fire('Error', 'Gagal hapus data.', 'error');
                    }
                });
            }
        });
    });
    
    $('#saveBtn').on("click", function(e) {
        e.preventDefault();
        let $btn = $(this);
        if ($btn.data('loading')) return;
        $btn.data('loading', true).prop('disabled', true);
        $btn.data('orig-html', $btn.html());
        var text = $btn.find('.btn-text').text() || $btn.text().trim();
        var spinner = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>';
        $btn.html(spinner + '<span class="btn-text">' + text + '</span>');
        $.ajax({
            data: $('#dataForm').serialize(),
            url: "{{ route('mappingpegawaim.store') }}",
            type: "POST",
            dataType: 'json',
            success: function (data) { 
                $btn.html($btn.data('orig-html'));
                $btn.prop('disabled', false).data('loading', false);
                if(data.status === "sukses"){  
                    Swal.fire('Sukses','Sukses simpan data.', 'success').then(() => {
                        $('#dataForm').trigger("reset");
                        $('#ModalForm').modal('hide');
                        table.ajax.reload(null, false);
                    });
                } else {
                    Swal.fire('Error', 'Gagal simpan data.', 'error');
                }                    
            },
            error: function (data) {
                // console.log('Error:', data);
                $btn.html($btn.data('orig-html'));
                $btn.prop('disabled', false).data('loading', false);
                Swal.fire('Error', 'Gagal simpan data. '+data.pesan, 'error');
            }
        });                 
    });
    $('#cancelBtn').on("click", function(e) {
        e.preventDefault();
        $('#dataForm').trigger("reset");
        $('#ModalForm').modal('hide');
    });

    $('.pembaharuan_row').click(function () {
        event.preventDefault();
        Swal.fire({
            title: 'Pembaharuan Data',
            text: "Download data pegawai dari aplikasi hris?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, proses!',
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
                        _token: '{{csrf_token()}}'
                    },
                    url: "{{url('api/hapus-mappingskorm')}}",
                    success: function (data) {
                        $btn.html($btn.data('orig-html'));
                        $btn.prop('disabled', false).data('loading', false);
                        Swal.fire('Sukses','Sukses memperbaharui data pegawai.', 'success').then(() => table.draw());
                    },
                    error: function (data) {
                        // console.log('Error:', data);
                        $btn.html($btn.data('orig-html'));
                        $btn.prop('disabled', false).data('loading', false);
                        Swal.fire('Error', 'Gagal memperbaharui data pegawai.', 'error');
                    }
                });
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

        $('#tgl_awalcari').datepicker({
            autoclose: true,
            format: 'dd/mm/yyyy',
            formatSubmit: 'yyyy-mm-dd',
            todayHighlight: true,
            enableOnReadonly: false
        });
        $('#tgl_akhircari').datepicker({
            autoclose: true,
            format: 'dd/mm/yyyy',
            formatSubmit: 'yyyy-mm-dd',
            todayHighlight: true,
            enableOnReadonly: false
        });
        $('#tanggal_buat').datepicker({
            autoclose: true,
            format: 'dd/mm/yyyy',
            formatSubmit: 'yyyy-mm-dd',
            todayHighlight: true,
            enableOnReadonly: false
        });
        $('#tgl_kwitansibiaya2').datepicker({
            autoclose: true,
            format: 'dd/mm/yyyy',
            formatSubmit: 'yyyy-mm-dd',
            todayHighlight: true,
            enableOnReadonly: false
        });
        $('#tanggalrinciantransportasi').datepicker({
            autoclose: true,
            format: 'dd/mm/yyyy',
            formatSubmit: 'yyyy-mm-dd',
            todayHighlight: true,
            enableOnReadonly: false
        });

        function filterFloat(val) {
            if (isNaN(val)) {
                return 0;
            }
            return val;
        }

        var date_diff_indays = function(date1, date2) {
            dt1 = new Date(date1);
            dt2 = new Date(date2);
            return Math.floor((Date.UTC(dt2.getFullYear(), dt2.getMonth(), dt2.getDate()) - Date.UTC(dt1.getFullYear(), dt1.getMonth(), dt1.getDate()) ) /(1000 * 60 * 60 * 24));
        }       

        $('#kd_area').on('select2:select', function () {
            var kd_area = this.value;
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
