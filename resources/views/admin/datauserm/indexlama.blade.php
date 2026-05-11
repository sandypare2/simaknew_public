
@extends('partials.layouts.master3')

@section('title', 'User Pengguna | SIMAK')
@section('sub-title', 'User Pengguna ' )
@section('pagetitle', 'Dashboard')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/libs/@yaireo/tagify/tagify.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/css/bootstrap-datepicker.min.css"/>
@endsection


@section('content')
<div class="row">
    <div class="col-12">
        <div class="card card-h-100">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tbl_list" class="table data-table-responsive table-hover align-middle table table-nowrap w-100 custom-table">
                        <thead>
                        <tr>
                            <th>Aksi</th>
                            <th>Username</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Jabatan</th>
                            <th>Status</th>
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

<!-- Input Data Modal -->
<div id="ModalForm" class="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
                    <input type="hidden" name="id" id="id">
                    @csrf
                    <div class="mb-2">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Username</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control form-control-sm" id="username" name="username" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Password</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control form-control-sm" id="password" name="password" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Nama Pengguna</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control form-control-sm" id="nama" name="nama" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div>                    
                    <div class="mb-2">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Email</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control form-control-sm" id="email" name="email" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div>                    
                    <div class="mb-2">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Hak Akses</label>
                        <div class="input-group input-group-merge">
                            <select id="role" name="role" class="select2 form-control form-control-sm" data-allow-clear="true" style="width:100%;">
                                <option value="" selected>-- Pilih Role --</option>
                                <option value="superadmin">Superadmin</option>
                                <option value="user">User</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Jabatan</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control form-control-sm" id="jabatan" name="jabatan" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Status</label>
                        <div class="input-group input-group-merge">
                            <select id="aktif" name="aktif" class="select2 form-control form-control-sm" data-allow-clear="true" style="width:100%;">
                                <option value="" selected>-- Pilih Status --</option>
                                <option value="1">Aktif</option>
                                <option value="0">Non Aktif</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-footer">
                <button type="button" id="saveBtn" class="btn btn-primary"><i class="ri-save-3-line me-1"></i>Simpan</button>
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
            url: "{{ route('datauserm') }}",
            // data: function (d) {
            //     d.kd_areacari = $('#kd_areacari').val(),
            //     d.search = $('#tbl_list_filter input').val()
            // }
        },            
        columns: [
            {data: 'aksi', name:'aksi',width:'50px', orderable: false, searchable: false},
            {data: 'username',name:'username',width:'100px',className: 'dt-left'},
            {data: 'nama',name:'nama',width:'160px',className: 'dt-left wrap'},
            {data: 'email',name:'email',width:'160px',className: 'dt-left wrap'},
            {data: 'role',name:'role',width:'160px',className: 'dt-center'},
            {data: 'jabatan',name:'jabatan',width:'200px',className: 'dt-left wrap'},
            {data: 'aktif',name:'aktif',width:'100px',className: 'dt-center'},
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
                var a = '<div style="width:160px;">';
                a += '<span>'+data.email+'</span>';
                a += '</div>';
                return a;
            }  
        },
        {
            targets: [4], render: function (a, b, data, d) { 
                if(data.role!=="" && data.role!==null){
                    return data.role.toUpperCase();
                } else {
                    return '';
                }
            }  
        },
        {
            targets: [5], render: function (a, b, data, d) {
                var a = '<div style="width:200px;">';
                a += '<span>'+data.jabatan+'</span>';
                a += '</div>';
                return a;
            }  
        },
        {
            targets: [6], render: function (a, b, data, d) { 
                if(data.aktif=="1"){
                    return "Aktif";
                } else {
                    return "Non Aktif";
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
    // document.querySelector('div.head-label_tbl_list').innerHTML = '<button type="button" class="btn btn-sm btn-success add_row nowrap" style="width:100px;"><i class="ri-add-line me-1"></i>Input Data</button>';
    document.querySelector('div.head-label_tbl_list').innerHTML = '<a title="Input Data" class="add_row"><button type="button" class="btn btn-primary btn-sm" style="width:110px;"><i class="ri-user-add-line me-1"></i>Create User</button></a>';

    table.on('preXhr.dt', function() {
        $('.dataTables_wrapper').addClass('loading');
        $('#datatable-loader').fadeIn(200);
    });
    table.on('xhr.dt', function() {
        $('.dataTables_wrapper').removeClass('loading');
        $('#datatable-loader').fadeOut(300);
    });

    $('.add_row').click(function () {
        $('#id').val('').trigger('change');
        $("#role").val('').trigger('change');
        $("#aktif").val('').trigger('change');
        $('#dataForm').trigger("reset");
        $('#modelHeading').html("Input Pengguna");
        $('#ModalForm').modal('show');
    });        
    
    $('body').on('click', '.edit_row', function () {
        var id = $(this).data('id');
        $.get("{{ route('datauserm') }}" +'/' + id, function (data) {
            $('#modelHeading2').html("Edit Pengguna");
            $('#ModalForm2').modal('show');
            $('#id').val(data.id);
            $('#username').val(data.username);
            $('#password').val(data.user_pass);
            $('#nama').val(data.nama);
            $('#email').val(data.email);
            $('#role').val(data.role).trigger('change');
            $('#jabatan').val(data.jabatan);
            $('#aktif').val(data.aktif).trigger('change');
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
                    url: "{{url('api/hapus-datauserm')}}",
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
        var datanya = $('#dataForm').serialize();
        // alert(datanya);
        $.ajax({
            data: $('#dataForm').serialize(),
            url: "{{ route('datauserm.store') }}",
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
                Swal.fire('Error', 'Gagal simpan data. '+data, 'error');
            }
        });                 
    });
    
    $('#saveBtn2').on("click", function(e) {
        e.preventDefault();
        var datanya = $('#dataForm2').serialize();
        // alert(datanya);
        $.ajax({
            data: $('#dataForm2').serialize(),
            url: "{{url('api/update-user-datauserm')}}",
            type: "POST",
            dataType: 'json',
            success: function (data) { 
                $('#saveBtn2').find(".fa-spinner").remove();
                if(data.status === "sukses"){  
                    $('#dataForm2').trigger("reset");
                    $('#ModalForm2').modal('hide');
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
