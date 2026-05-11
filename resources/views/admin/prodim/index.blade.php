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
                <th>Kode</th>
                <th>Program Studi</th>
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
                    <input type="hidden" name="kd_prodi2" id="kd_prodi2">
                    @csrf
                    <div class="mb-2">
                        <label class="form-label" for="basic-icon-default-fullname">Nama Program Studi</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control" id="nama_prodi" name="nama_prodi" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
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
                url: "{{ route('prodim') }}",
                data: function (d) {
                    d.search = $('#tbl_list_filter input').val()
                }
            },            
            columns: [
                {data: 'aksi', name:'aksi',width:'50px', orderable: false, searchable: false},
                {data: 'kd_prodi',name:'kd_prodi',width:'50px',className: 'dt-center'},
                {data: 'nama_prodi',name:'nama_prodi',width:'300px',className: 'dt-left wrap'},
            ],
            columnDefs: [
            {
                targets: [2], render: function (a, b, data, d) { 
                    var a = '<div style="width:300px;">';
                    a += '<span>'+data.nama_prodi+'</span>';
                    a += '</div>';
                    return a;
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
            $('#id').val('').trigger('change');
            $('#kd_prodi2').val('').trigger('change');
            $('#dataForm').trigger("reset");
            $('#modelHeading').html("Input Program Studi");
            $('#ModalForm').modal('show');
        });        
        
        $('body').on('click', '.edit_row', function () {
            var id = $(this).data('id');
            $.get("{{ route('prodim') }}" +'/' + id, function (data) {                
                $('#modelHeading').html("Edit Program Studi");
                $('#ModalForm').modal('show');
                $('#id').val(data.id);
                $('#kd_prodi2').val(data.kd_prodi);
                $('#nama_prodi').val(data.nama_prodi);
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
                        url: "{{url('api/hapus-prodim')}}",
                        success: function (data) {
                            swal({
                                title: "Result",
                                text: 'Sukses hapus data program studi',
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
                url: "{{ route('prodim.store') }}",
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
        $('#blthcari').datepicker({
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
        $("#eviden").change(function () {
            if (this.files && this.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('#img_eviden').attr('src', e.target.result);
                }
                reader.readAsDataURL(this.files[0]);
            }
        });
        var date_diff_indays = function(date1, date2) {
            dt1 = new Date(date1);
            dt2 = new Date(date2);
            return Math.floor((Date.UTC(dt2.getFullYear(), dt2.getMonth(), dt2.getDate()) - Date.UTC(dt1.getFullYear(), dt1.getMonth(), dt1.getDate()) ) /(1000 * 60 * 60 * 24));
        }        
        $("#tgl_awal").on("change",function(){
            var tgl_awal=$("#tgl_awal").val();
            var tgl_akhir=$("#tgl_akhir").val();
            if(tgl_awal!=="" && tgl_akhir!==""){
                var tgl1 = tgl_awal.split('/');
                var hari1 = tgl1[0];
                var bulan1 = tgl1[1];
                var tahun1 = tgl1[2];
                var tgl_awalnya = bulan1 + '/' + hari1 + '/' + tahun1;
                var tgl2 = tgl_akhir.split('/');
                var hari2 = tgl2[0];
                var bulan2 = tgl2[1];
                var tahun2 = tgl2[2];
                var tgl_akhirnya = bulan2 + '/' + hari2 + '/' + tahun2;
                var hari = date_diff_indays(tgl_awalnya, tgl_akhirnya)+1;
            } else {
                var hari = 0;
            }
            $("#hari").val(hari);
        });        
        $("#tgl_akhir").on("change",function(){
            var tgl_awal=$("#tgl_awal").val();
            var tgl_akhir=$("#tgl_akhir").val();
            if(tgl_awal!=="" && tgl_akhir!==""){
                var tgl1 = tgl_awal.split('/');
                var hari1 = tgl1[0];
                var bulan1 = tgl1[1];
                var tahun1 = tgl1[2];
                var tgl_awalnya = bulan1 + '/' + hari1 + '/' + tahun1;
                var tgl2 = tgl_akhir.split('/');
                var hari2 = tgl2[0];
                var bulan2 = tgl2[1];
                var tahun2 = tgl2[2];
                var tgl_akhirnya = bulan2 + '/' + hari2 + '/' + tahun2;
                var hari = date_diff_indays(tgl_awalnya, tgl_akhirnya)+1;
            } else {
                var hari = "";
            }            
            $("#hari").val(hari);
        });        
    </script>   

    <style>
    .flex-container {
        display: flex;
    }
    </style>
@endpush
