@extends('layouts.master')

@push('plugin-styles')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-select-bs5/select.bootstrap5.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-fixedcolumns-bs5/fixedcolumns.bootstrap5.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-fixedheader-bs5/fixedheader.bootstrap5.css') }}" />
@endpush

@push('style')
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="row flex-grow-1" style="margin-left:10px;margin-right:10px;margin-top:20px;">
            <div class="col-md-12 grid-margin">
                <a title="Input Data" class="add_row"><button type="button" class="btn btn-primary"><span class="ti-xs ti ti-plus"></span>Input Data</button></a></p>
            </div>
            <!-- <div class="col-md-3 grid-margin">
                <span class="float-end" style="font-weight:bold;">RINCIAN SPPD</span>
            </div> -->
        </div>    

        <div class="card-datatable text-nowrap">
        <table id="tbl_list" class="dt-scrollableTable table">
            <thead>
            <tr>
                <th>Aksi</th>
                <th>Bulan Awal</th>
                <th>Bulan Akhir</th>
                <th>Tanggal Awal</th>
                <th>Tanggal Akhir</th>
                <th>Status</th>
            </tr>
            </thead>
        </table>
        </div>
    </div>
</div>

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
                        <label class="form-label" for="basic-icon-default-fullname">Bulan Awal</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control" id="blth_awal" name="blth_awal" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label" for="basic-icon-default-fullname">Bulan Akhir</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control" id="blth_akhir" name="blth_akhir" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label" for="basic-icon-default-fullname">Tanggal Awal</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control" id="tgl_awal" name="tgl_awal" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label" for="basic-icon-default-fullname">Tanggal Akhir</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control" id="tgl_akhir" name="tgl_akhir" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label" for="basic-icon-default-fullname">Status Periode</label>
                        <div class="input-group input-group-merge">
                            <select id="status" name="status" class="select2 form-select" data-allow-clear="true">
                                <option value="" selected>--Pilih Status--</option>
                                <option value="1">Open</option>
                                <option value="0">Close</option>
                            </select>                
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
<style>
.swal-title {
    font-family: Courier New, monospace;
    font-size: 16px;
}    
.swal-text {
    font-family: Courier New, monospace;
    font-size: 15px;
    text-align:center;
}    
.container img {
    height: 100%;
    width: 100%;
    object-fit: cover;
}

</style>

@endsection

@push('plugin-scripts')
<script src="{{ asset('assets/vendor/libs/moment/moment.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>

@endpush

@push('custom-scripts')
<script src="{{ asset('assets/js/tables-datatables-advanced.js') }}"></script>
<script type="text/javascript">
    $(function () {                 
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
                    api.search(this.value).draw();
                });
            },
            oLanguage: {
                sProcessing: "loading data...",
                sSearch: "Search:",
            }, 
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('prealisasim') }}",
                data: function (d) {
                    d.search = $('#tbl_list_filter input').val()
                }
            },            
            columns: [
                {data: 'aksi', name:'aksi',width:'50px', orderable: false, searchable: false},
                {data: 'blth_awal',name:'blth_awal',width:'50px',className: 'dt-center'},
                {data: 'blth_akhir',name:'blth_akhir',width:'50px',className: 'dt-center'},
                {data: 'tgl_awal',name:'tgl_awal',width:'80px',className: 'dt-center'},
                {data: 'tgl_akhir',name:'tgl_akhir',width:'80px',className: 'dt-center'},
                {data: 'status',name:'status',width:'100px',className: 'dt-center'},
            ],
            columnDefs: [
            {
                targets: [3], render: function (a, b, data, d) { 
                    if(data.tgl_awal!=="" && data.tgl_awal!==null && data.tgl_awal!==undefined){
                        var hsl1 = data.tgl_awal.split('-');
                        return hsl1[2]+"."+hsl1[1]+"."+hsl1[0];
                    } else {
                        return '';
                    }
                }  
            },
            {
                targets: [4], render: function (a, b, data, d) { 
                    if(data.tgl_akhir!=="" && data.tgl_akhir!==null && data.tgl_akhir!==undefined){
                        var hsl1 = data.tgl_akhir.split('-');
                        return hsl1[2]+"."+hsl1[1]+"."+hsl1[0];
                    } else {
                        return '';
                    }
                }  
            },
            {
                targets: [5], render: function (a, b, data, d) { 
                    if(parseInt(data.status)===1){
                        return '<span class="badge bg-success" style="padding:5px;">Open</span>';
                    } else {
                        return '<span class="badge bg-danger" style="padding:5px;">Close</span>';
                    }
                }  
            },
            ],
            "stateSave": true,
            "scrollX": true,
            "ScrollXInner": true,
            "autoWidth": false
        });

        // $('#filternya').on("click", function() {
        //     table.draw();
        // });         

        $('.add_row').click(function () {
            $('#dataForm').trigger("reset");
            $('#modelHeading').html("Input Periode Pengisian Realisasi");
            $('#ModalForm').modal('show');
            $('#id').val('').trigger('change');
            $('#blth_awal').val('').trigger('change');
            $('#blth_akhir').val('').trigger('change');
            $('#tgl_awal').val('').trigger('change');
            $('#tgl_akhir').val('').trigger('change');
            $('#status').val('').trigger('change');
        });        
        
        $('body').on('click', '.edit_row', function () {
            var id = $(this).data('id');
            $.get("{{ route('prealisasim') }}" +'/' + id, function (data) {                
                $('#modelHeading').html("Edit Periode Pengisian Realisasi");
                $('#ModalForm').modal('show');
                if(data.tgl_awal!==""){
                    var tgl = data.tgl_awal.split('-');
                    var tahun = tgl[0];
                    var bulan = tgl[1];
                    var hari = tgl[2];
                    var tgl_awal2 = hari + '/' + bulan + '/' + tahun;
                } else {
                    var tgl_awal2 = "";
                }
                if(data.tgl_akhir!==""){
                    var tgl = data.tgl_akhir.split('-');
                    var tahun = tgl[0];
                    var bulan = tgl[1];
                    var hari = tgl[2];
                    var tgl_akhir2 = hari + '/' + bulan + '/' + tahun;
                } else {
                    var tgl_akhir2 = "";
                }

                $('#id').val(data.id);
                $('#blth_awal').val(data.blth_awal).trigger('change');
                $('#blth_akhir').val(data.blth_akhir).trigger('change');
                $('#tgl_awal').val(tgl_awal2).trigger('change');
                $('#tgl_akhir').val(tgl_akhir2).trigger('change');
                $('#status').val(data.status).trigger('change');
            })
        }); 

        $('body').on('click', '.delete_row', function () {
            var id = $(this).data("id");
            event.preventDefault();
            swal({
                title: "Anda yakin akan menghapus data ini?",
                text: "Data yang sudah terhapus tidak dapat dikembalikan.",
                icon: "warning",
                type: "warning",
                buttons: ["Batal","Hapus"],
                confirmButtonColor: '#d9534f',
                cancelButtonColor: '#d33',
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    $.ajax({
                        type: "POST",
                        data: {
                            id: id,
                            _token: '{{csrf_token()}}'
                        },
                        url: "{{url('api/hapus-prealisasim')}}",
                        success: function (data) {
                            swal({
                                title: "Result",
                                text: 'Sukses hapus data periode realisasi',
                                icon: "info",
                                type: "success",
                                dangerMode: false,
                            })                            
                            table.draw();
                        },
                        error: function (data) {
                            console.log('Error:', data);
                        }
                    });
                }
            });
        });
        
        $('#saveBtn').on("click", function(e) {
            e.preventDefault();
            $(this).prepend('<i class="fa fa-spinner fa-spin"></i>');
            var datanya = $('#dataForm').serialize();
            // alert(datanya);
            $.ajax({
                data: $('#dataForm').serialize(),
                url: "{{ route('prealisasim.store') }}",
                type: "POST",
                dataType: 'json',
                success: function (data) { 
                    $('#saveBtn').find(".fa-spinner").remove();
                    if(data.status === "sukses"){  
                        $('#dataForm').trigger("reset");
                        $('#ModalForm').modal('hide');
                        table.draw();
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
        $('#blth_awal').datepicker({
            autoclose: true,
            format: 'yyyy-mm',
            formatSubmit: 'yyyy-mm'
        });
        $('#blth_akhir').datepicker({
            autoclose: true,
            format: 'yyyy-mm',
            formatSubmit: 'yyyy-mm'
        });
        $('#tgl_awal').datepicker({
            autoclose: true,
            format: 'dd/mm/yyyy',
            formatSubmit: 'yyyy-mm-dd',
            todayHighlight: true,
        }).on('changeDate', function (selected) {
            var minDate = new Date(selected.date.valueOf());
            $('#tgl_akhir').datepicker('setStartDate', minDate);
        });
        $('#tgl_lahir').datepicker({
            autoclose: true,
            format: 'dd/mm/yyyy',
            formatSubmit: 'yyyy-mm-dd',
            todayHighlight: true
        });
    </script>   

    <style>
    .flex-container {
        display: flex;
    }
    </style>
@endpush
