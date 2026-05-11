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
            <div class="col-md-9 grid-margin">
                <div class="row flex-grow-1">
                    <input type="text" class="form-control" id="tahuncari" name="tahuncari" value="{{ date('Y') }}" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" onkeypress="return isNumberKey(event)" style="width:120px;margin-right:5px;" />
                    <button type="button" id="filternya" class="btn btn-info" style="width:140px;margin-right:3px;"><span class="ti ti-search ti-sm" style="margin-right:5px;"></span>Filter Data</button>
                    <a class="add_row btn btn-primary tombol" href="javascript:void(0)" id="addrow" style="width:160px;"><i class="fa fa-plus" style="margin-right:5px;"></i>Tambah Data</a>
                </div>
            </div>
            <div class="col-md-3 grid-margin">
                <span class="float-end" style="font-weight:bold;">LIBUR NASIONAL</span>
            </div>
        </div>    

        <div class="card-datatable text-nowrap">
        <table id="tbl_list" class="dt-scrollableTable table">
            <thead>
            <tr>
                <th>Aksi</th>
                <th>Tanggal</th>
                <th>Keterangan</th>
            </tr>
            </thead>
        </table>
        </div>
    </div>
</div>

<div class="modal fade" id="ModalForm" tabindex="-1" aria-hidden="true">
<!-- <div class="modal fade" id="ModalForm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"> -->
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="modal-title" id="modelHeading"></h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="card-body">            
                <form id="dataForm" name="dataForm" class="form-horizontal">
                    @csrf
                    <input type="hidden" name="id" id="id">
                    <div class="mb-6">
                        <label class="form-label" for="basic-icon-default-fullname">Tanggal</label>
                        <div class="input-group input-group-merge">
                            <input type="text" autocomplete="off" class="form-control" name="tanggal" id="tanggal" placeholder="">
                        </div>
                    </div>                    
                    <div class="mb-6">
                        <label class="form-label" for="basic-icon-default-fullname">Keterangan</label>
                        <div class="input-group input-group-merge">
                            <textarea id="keterangan" name="keterangan" class="form-control" placeholder="" aria-describedby="basic-icon-default-message2" rows="2"></textarea>
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
    font-size: 13px;
    color:red;
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
                    if(e.keyCode == 13) {
                        api.search(this.value).draw();
                    }
                });
            },
            oLanguage: {
                sProcessing: "Loading data...",
                sSearch: "Search :",
            },            
            processing: true,
            serverSide: true,
            ajax: {
            url: "{{ route('liburm') }}",
                data: function (d) {
                    d.tahuncari = $('#tahuncari').val(),
                    d.search = $('#tbl_list_filter input').val()
                }
            },            
            columns: [
                {data: 'aksi', name:'aksi',width:'80px', orderable: false, searchable: false},
                {data: 'tanggalnya',name:'tanggalnya',width:'100px',className: 'dt-center'},
                {data: 'keterangan',name:'keterangan',className: 'dt-left wrap'},
            ],
            columnDefs: [
            {
                targets: [1], render: function (a, b, data, d) { 
                    if(data.tanggal!=="" && data.tanggal!==null){
                        var tgl1 = data.tanggal.split('-');
                        return tgl1[2]+"."+tgl1[1]+"."+tgl1[0];
                    } else {
                        return "";
                    }
                }  
            },
            ],
            "stateSave": false,
            "scrollX": true,
            "ScrollXInner": true,
            "autoWidth": false
        }); 

        $('#filternya').on("click", function() {
            table.draw(); 
        });         

        $('.add_row').click(function () {
            // $('#saveBtn').val("create-product");
            $('#id').val('');
            $('#dataForm').trigger("reset");
            $('#modelHeading').html("Input Libur Nasional");
            $('#ModalForm').modal('show');
        });        
        
        $('body').on('click', '.edit_row', function () {
            var id = $(this).data('id');
            $.get("{{ route('liburm') }}" +'/' + id, function (data) {
                $('#modelHeading').html("Edit Libur Nasional");
                $('#ModalForm').modal('show');
                if(data.tanggal!==""){
                    var tgl = data.tanggal.split('-');
                    var tahun = tgl[0];
                    var bulan = tgl[1];
                    var hari = tgl[2];
                    var tanggal = hari + '/' + bulan + '/' + tahun;
                } else {
                    var tanggal = "";
                }

                $('#id').val(data.id);
                $('#tanggal').val(tanggal);
                $('#keterangan').val(data.keterangan);
            })
        }); 
        
        $('#saveBtn').on("click", function(e) {
            e.preventDefault();
            // $('.loading-spinner').toggleClass('active');
            $(this).prepend('<i class="fa fa-spinner fa-spin"></i>');
            var datanya = $('#dataForm').serialize();
            // alert(datanya);
            $.ajax({
                data: $('#dataForm').serialize(),
                // url: "{{ url('api/simpan-lokasi-datakendaraan') }}",
                url: "{{ route('liburm.store') }}",
                type: "POST",
                dataType: 'json',
                success: function (data) {  
                    $('#saveBtn').find(".fa-spinner").remove();
                    $('#dataForm').trigger("reset");
                    $('#ModalForm').modal('hide');
                    table.draw();            
                },
                error: function (data) {
                    console.log('Error:', data);
                }
            });                 
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
                        url: "{{url('api/hapus-liburm')}}",
                        type: "POST",
                        data: {
                            id: id,
                            _token: '{{csrf_token()}}'
                        },
                        dataType: 'json',
                        success: function (result) {
                            table.draw();
                        }
                    });
                }
            });
        });

    });
</script>
<script>
$('#tanggal').datepicker({
    autoclose: true,
    format: 'dd/mm/yyyy',
    formatSubmit: 'yyyy-mm-dd',
    todayHighlight: true,
    enableOnReadonly: false
});
</script>
@endpush
