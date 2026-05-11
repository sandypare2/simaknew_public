@extends('layouts.master')

@push('plugin-styles')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-select-bs5/select.bootstrap5.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-fixedcolumns-bs5/fixedcolumns.bootstrap5.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-fixedheader-bs5/fixedheader.bootstrap5.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-auth.css') }}" />
@endpush

@push('style')
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="row flex-grow-1" style="margin-left:10px;margin-right:10px;margin-top:20px;">
            <div class="col-md-9 grid-margin">
                <a class="add_row btn btn-primary tombol" href="javascript:void(0)" id="addrow"><i class="fa fa-plus" style="margin-right:5px;"></i>Tambah Data</a>
            </div>
            <div class="col-md-3 grid-margin">
                <span class="float-end" style="font-weight:bold;">DATA PENGGUNA</span>
            </div>
        </div>    

        <div class="card-datatable text-nowrap">
        <table id="tbl_list" class="dt-scrollableTable table">
            <thead>
            <tr>
                <th>Aksi</th>
                <th>Username</th>
                <th>Nama</th>
                <th>Jabatan</th>
                <th>Role</th>
                <th>Akses Perusahaan</th>
                <th>Status</th>
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
                <!-- <small class="text-muted float-end">Merged input group</small> -->
            </div>
            <div class="card-body"> 
                <form id="dataForm" name="dataForm" class="form-horizontal">
                    @csrf
						@if ($errors->any())
						<div class="alert alert-primary alert-dismissible" role="alert">
							@foreach ($errors->all() as $error)
								<li>{{ $error }}</li>
							@endforeach
							<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
						</div>
						@endif
                    <input type="hidden" name="id" id="id">
                    <div class="mb-6">
                        <label class="form-label" for="basic-icon-default-fullname">Username</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control" id="username" name="username" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div>                    
                    <div class="mb-6">
                        <label class="form-label" for="basic-icon-default-fullname">Password</label>
                        <div class="input-group input-group-merge form-password-toggle">
                            <!-- <input type="text" class="form-control" id="password" name="password" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" /> -->
                            <input type="password" id="password" class="form-control" name="password" aria-describedby="password">
                            <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                        </div>
                    </div>                    
                    <div class="mb-6">
                        <label class="form-label" for="basic-icon-default-fullname">Nama</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control" id="nama" name="nama" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div>                    
                    <div class="mb-6">
                        <label class="form-label" for="basic-icon-default-fullname">Jabatan</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control" id="jabatan" name="jabatan" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div>                    
                    <div class="mb-6">
                        <label class="form-label" for="basic-icon-default-fullname">Email</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control" id="email" name="email" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div>                    
                    <div class="mb-6">
                        <label class="form-label" for="basic-icon-default-fullname">Role</label>
                        <div class="input-group input-group-merge">
                            <select id="role" name="role" class="select2 form-select" data-allow-clear="true">
                                <option value="superadmin">Superadmin</option>
                                <option value="admin_gudang">Admin Gudang</option>
                                <option value="staff_gudang">Staff Gudang</option>
                                <option value="purchasing">Purchasing</option>
                                <option value="viewer" selected>Viewer</option>
                            </select>
                        </div>
                    </div>                    
                    <div class="mb-6">
                        <label class="form-label" for="basic-icon-default-fullname">Akses Perusahaan</label>
                        <div class="input-group input-group-merge">
                            <select id="kd_afiliasi" name="kd_afiliasi" class="select2 form-select" data-allow-clear="true">
                                <option value="" selected>-</option>
                                <option value="semua" selected>Semua</option>
                                @foreach ($data_afiliasi as $data)
                                    <option value="{{ $data->kd_afiliasi }}">{{ $data->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>                    
                    <div class="mb-6">
                        <label class="form-label" for="basic-icon-default-fullname">Status</label>
                        <div class="input-group input-group-merge">
                            <select id="aktif" name="aktif" class="select2 form-select" data-allow-clear="true">
                                <option value="1" selected>Aktif</option>
                                <option value="0">Non Aktif</option>
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
<script src="{{ asset('assets/js/pages-auth.js') }}"></script>
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
            url: "{{ route('userm') }}",
                data: function (d) {
                    d.kd_afiliasicari = $('#kd_afiliasicari').val(),
                    d.search = $('#tbl_list_filter input').val()
                }
            },            
            columns: [
                {data: 'aksi', name:'aksi',width:'80px', orderable: false, searchable: false},
                {data: 'username',name:'username',width:'200px',className: 'dt-left wrap'},
                {data: 'nama',name:'nama',width:'300px',className: 'dt-left wrap'},
                {data: 'jabatan',name:'jabatan',width:'100px',className: 'dt-center'},
                {data: 'role',name:'role',width:'300px',className: 'dt-left wrap'},
                {data: 'afiliasi2',name:'afiliasi2',width:'300px',className: 'dt-left wrap'},
                {data: 'aktif2',name:'aktif2',width:'80px',className: 'dt-center'},
            ],
            columnDefs: [
            {
                targets: [5], render: function (a, b, data, d) { 
                    if(data.kd_afiliasi=="semua"){
                        var a = "Semua";
                    } else {
                        var a = data.nama_afiliasi;
                    }
                    return a;
                }  
            },
            {
                targets: [6], render: function (a, b, data, d) { 
                    if(parseInt(data.aktif)==0){
                        var a = "Non Aktif";
                    } else {
                        var a = "Aktif";
                    }
                    return a;
                }  
            },
            ],
            "stateSave": false,
            "scrollX": true,
            "ScrollXInner": true,
            "autoWidth": true
        }); 

        $('#filternya').on("click", function() {
            table.draw(); 
        });         

        $('.add_row').click(function () {
            // $('#saveBtn').val("create-product");
            $('#id').val('');
            $('#dataForm').trigger("reset");
            $('#modelHeading').html("Input Data Pengguna");
            $('#ModalForm').modal('show');
        });        
        
        $('body').on('click', '.edit_row', function () {
            var id = $(this).data('id');            
            $.get("{{ route('userm') }}" +'/' + id, function (data) {
                $('#modelHeading').html("Edit Data Pengguna");
                $('#ModalForm').modal('show');                
                $('#id').val(data.id);
                $('#username').val(data.username);
                $('#password').val(data.user_pass);
                $('#nama').val(data.nama);
                $('#jabatan').val(data.jabatan);
                $('#email').val(data.email);
                $('#role').val(data.role).trigger('change');
                $('#kd_afiliasi').val(data.kd_afiliasi).trigger('change');
                $('#aktif').val(data.aktif).trigger('change');
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
                url: "{{ route('userm.store') }}",
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
                        url: "{{url('api/hapus-userm')}}",
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

        $('body').on('click', '.reset_pass', function () {
            var id = $(this).data("id");
            event.preventDefault();
            swal({
                title: "Masukkan Password Baru",
                content: {
                    element: "input",
                    attributes: {
                        type: "text",
                    },
                },
                icon: "warning",
                type: "warning",
                buttons: ["Batal","Reset"],
                confirmButtonColor: '#d9534f',
                cancelButtonColor: '#d33',
                dangerMode: true,
            }).then((result) => {
                if (result) {                    
                    $.ajax({
                        url: "{{url('api/reset-pass')}}",
                        type: "POST",
                        data: {
                            id: id,
                            newpass: result,
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
@endpush
