
@extends('partials.layouts.master3')

@section('title', 'Kenaikan Grade | SIMAK')
@section('sub-title', 'Kenaikan Grade ' )
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
                        <div class="row flex-grow-1">
                            <div class="col-md-4 grid-margin">
                                <select id="kd_areacari" name="kd_areacari" class="form-control select2">
                                    <option value="semua" selected>SEMUA</option>
                                    @foreach ($masteraream as $data)
                                        <option value="{{ $data->kd_area }}">{{ $data->nama_area }}</option>                                    
                                    @endforeach
                                </select>                            
                            </div>
                            <div class="col-md-8 grid-margin d-flex justify-content:start gap-2">
                                <button type="button" id="filternya" class="btn btn-primary btn-sm"><i class="ri-search-line me-1"></i>Filter Data</button>
                                <button type="button" id="cetaknya" class="btn btn-info btn-sm"><i class="ri-printer-line me-1"></i>Cetak</button>
                                <div>
                                    <form action="{{ route('export-kenaikangradem') }}" target="_blank">
                                    <input type="hidden" class="form-control" name="kd_areacarinya" id="kd_areacarinya" value="semua">
                                    <button type="submit" class="btn btn-success btn-sm"><i class="ri-file-excel-line me-1"></i>Export</button>
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
                            <th rowspan="2">NIP/Nama</th>
                            <th rowspan="2">Unit/Jabatan</th>
                            <th colspan="2">Grade Terakhir</th>
                            <th rowspan="2">Data Talenta</th>
                            <th colspan="2">Rencana Kenaikan</th>
                        </tr>
                        <tr>
                            <th>Grade</th>
                            <th>Tgl.Kenaikan</th>
                            <th>Grade</th>
                            <th>Tanggal</th>
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
                        <label class="form-label" for="basic-icon-default-fullname">Nomor Induk</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control" id="nip" name="nip" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" readonly />
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label" for="basic-icon-default-fullname">Nama Pegawai</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control" id="nama" name="nama" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" readonly />
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label" for="basic-icon-default-fullname">Jabatan</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control" id="jabatan" name="jabatan" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" readonly />
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label" for="basic-icon-default-fullname">Grade</label>
                        <div class="input-group input-group-merge">
                            <select id="grade" name="grade" class="form-control select2">
                                <option value="" selected>-</option>
                                @foreach ($gradem as $data)
                                    <option value="{{ $data->grade }}">{{ $data->grade }}</option>                                    
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label" for="basic-icon-default-fullname">Tanggal Kenaikan</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control" id="tgl_kenaikan" name="tgl_kenaikan" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
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
            url: "{{ route('kenaikangradem') }}",
            data: function (d) {
                d.kd_areacari = $('#kd_areacari').val(),
                d.search = $('#tbl_list_filter input').val()
            }
        },            
        columns: [
            // {data: 'aksi', name:'aksi',width:'50px', orderable: false, searchable: false},
            {data: 'nip',name:'nip',width:'160px',className: 'dt-left wrap'},
            {data: 'nama',name:'nama',width:'220px',className: 'dt-left wrap'},
            {data: 'grade2',name:'grade2',width:'50px',className: 'dt-center'},
            {data: 'tgl_kenaikan2',name:'tgl_kenaikan2',width:'100px',className: 'dt-center'},
            {data: 'data_talenta',name:'data_talenta',width:'100px',className: 'dt-center'},
            {data: 'rencana_kenaikan',name:'rencana_kenaikan',width:'100px',className: 'dt-center'},
            {data: 'rencana_tgl_kenaikan',name:'rencana_tgl_kenaikan',width:'100px',className: 'dt-center'},
        ],
        columnDefs: [
        {
            targets: [0], render: function (a, b, data, d) { 
                var a = '<div style="width:160px;">';
                a += '<span>'+data.nip+'</span>';
                a += '<br/><span style="color:#4169E1;">'+data.nama+'</span>';
                a += '</div>';
                return a;
            }  
        },
        {
            targets: [1], render: function (a, b, data, d) { 
                var a = '<div style="width:220px;">';
                a += '<span>'+data.nama_area+'</span>';
                a += '<br/><span style="color:#4169E1;">'+data.jabatan+'</span>';
                a += '</div>';
                return a;
            }  
        },
        {
            targets: [3], render: function (a, b, data, d) { 
                if (!data.tgl_kenaikan2) return ""; 
                let date = new Date(data.tgl_kenaikan2);
                if (isNaN(date)) return data.tgl_kenaikan2;
                return new Intl.DateTimeFormat("id-ID", {
                    day: "numeric",
                    month: "long",
                    year: "numeric"
                }).format(date);
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
    document.querySelector('div.head-label_tbl_list').innerHTML = '<h5 class="card-title text-nowrap mb-0">Kenaikan Grade</h5>';

    table.on('preXhr.dt', function() {
        $('.dataTables_wrapper').addClass('loading');
        $('#datatable-loader').fadeIn(200);
    });
    table.on('xhr.dt', function() {
        $('.dataTables_wrapper').removeClass('loading');
        $('#datatable-loader').fadeOut(300);
    });

    $('#filternya').on("click", function() {
        var kd_areacari = $("#kd_areacari").val();
        $("#kd_areacarinya").val(kd_areacari);
        table.draw();
    });     
    
    $('#cetaknya').on("click", function() {
        var kd_areacari = $("#kd_areacari").val();
        window.open("cetakkenaikanm?kd_areacari="+kd_areacari);
    }); 
    
    $('body').on('click', '.edit_row', function () {
        var id = $(this).data('id');
        var nip = $(this).data('nip');
        var nama = $(this).data('nama');
        var jabatan = $(this).data('jabatan');
        var grade = $(this).data('grade');
        var tgl_kenaikan = $(this).data('tgl_kenaikan');
        if(tgl_kenaikan!=="" && tgl_kenaikan!==null && tgl_kenaikan!==undefined){
            var date = new Date(tgl_kenaikan);
            // var formatted = new Intl.DateTimeFormat("id-ID").format(date);
            var formatted = new Intl.DateTimeFormat("id-ID", {
                day: "2-digit",
                month: "2-digit",
                year: "numeric"
            }).format(date);
        } else {
            var formatted = "";
        }
        // alert(formatted);

        $('#modelHeading').html("Update Kenaikan Grade");
        $('#ModalForm').modal('show');
        $('#dataForm').trigger("reset");
        $('#id').val(id);
        $('#nip').val(nip);
        $('#nama').val(nama);
        $('#jabatan').val(jabatan);
        $('#grade').val(grade).trigger('change');
        $('#tgl_kenaikan').val(formatted).trigger('change');
    });         
    $('#saveBtn').on("click", function(e) {
        e.preventDefault();
        $(this).prepend('<i class="fa fa-spinner fa-spin"></i>');
        var datanya = $('#dataForm').serialize();
        // alert(datanya);
        $.ajax({
            data: $('#dataForm').serialize(),
            url: "{{ route('riwayatgradem.store') }}",
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
        $('#tgl_kenaikan').datepicker({
            autoclose: true,
            format: 'dd/mm/yyyy',
            formatSubmit: 'yyyy-mm-dd',
            todayHighlight: true
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
