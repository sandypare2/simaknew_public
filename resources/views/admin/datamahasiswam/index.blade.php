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
            <div class="col-md-3 grid-margin">
                <select id="kd_prodicari" name="kd_prodicari" class="form-control select2">
                    <option value="semua" selected>Semua Program Studi</option>
                    @foreach ($prodim as $data)
                        <option value="{{ $data->kd_prodi }}">{{ $data->nama_prodi }}</option>                                    
                    @endforeach
                </select>                            

            </div>
            <div class="col-md-3 grid-margin">
                <select id="tahuncari" name="tahuncari" class="form-control select2">
                    <option value="semua" selected>Semua</option>
                    @foreach ($tahunmasukm as $data)
                        <option value="{{ $data->tahun_masuk }}">{{ $data->tahun_masuk }}</option>                                    
                    @endforeach
                </select>                            
            </div>
            <div class="col-md-6 grid-margin">
                <button type="button" id="filternya" class="btn btn-info"><span class="ti ti-search ti-sm" style="margin-right:5px;"></span>Filter Data</button>
                <a title="Input Data" class="add_row"><button type="button" class="btn btn-primary"><span class="ti-xs ti ti-plus"></span>Input Data</button></a></p>
            </div>
        </div>    

        <div class="card-datatable text-nowrap">
        <table id="tbl_list" class="dt-scrollableTable table">
            <thead>
            <tr>
                <th>Aksi</th>
                <th>Program Studi</th>
                <th>Tahun</th>
                <th>NIM</th>
                <th>Nama</th>
                <th>Tempat Lahir</th>
                <th>Tgl.Lahir</th>
                <th>L/P</th>
                <th>Tahun Masuk</th>
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
                        <label class="form-label" for="basic-icon-default-fullname">Program Studi</label>
                        <div class="input-group input-group-merge">
                            <select id="kd_prodi" name="kd_prodi" class="form-control select2">
                                <option value="" selected>-- Pilih Program Studi --</option>
                                @foreach ($prodim as $data)
                                    <option value="{{ $data->kd_prodi }}">{{ $data->nama_prodi }}</option>                                    
                                @endforeach
                            </select>                            
                        </div>
                    </div>  
                    <div class="mb-2">
                        <label class="form-label" for="basic-icon-default-fullname">Nomor Induk</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control" id="nim" name="nim" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label" for="basic-icon-default-fullname">Nama</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control" id="nama" name="nama" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label" for="basic-icon-default-fullname">Tempat Lahir</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control" id="tempat_lahir" name="tempat_lahir" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label" for="basic-icon-default-fullname">Tanggal Lahir</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control" id="tgl_lahir" name="tgl_lahir" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label" for="basic-icon-default-fullname">Jenis Kelamin</label>
                        <div class="input-group input-group-merge">
                            <select id="jenis_kelamin" name="jenis_kelamin" class="select2 form-select" data-allow-clear="true">
                                <option value="" selected>--Jenis Kelamin--</option>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label" for="basic-icon-default-fullname">Tahun Masuk</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control" id="tahun_masuk" name="tahun_masuk" onkeypress="return isNumberKey(event)" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
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
                url: "{{ route('datamahasiswam') }}",
                data: function (d) {
                    d.kd_prodicari = $('#kd_prodicari').val(),
                    d.tahuncari = $('#tahuncari').val(),
                    d.search = $('#tbl_list_filter input').val()
                }
            },            
            columns: [
                {data: 'aksi', name:'aksi',width:'50px', orderable: false, searchable: false},
                {data: 'nama_prodi',name:'nama_prodi',width:'200px',className: 'dt-center'},
                {data: 'tahun_masuk',name:'tahun_masuk',width:'50px',className: 'dt-center'},
                {data: 'nim',name:'nim',width:'50px',className: 'dt-center'},
                {data: 'nama',name:'nama',width:'160px',className: 'dt-left wrap'},
                {data: 'tempat_lahir',name:'tempat_lahir',width:'160px',className: 'dt-left wrap'},
                {data: 'tgl_lahir',name:'tgl_lahir',width:'100px',className: 'dt-center'},
                {data: 'jenis_kelamin',name:'jenis_kelamin',width:'50px',className: 'dt-center'},
                {data: 'tahun_masuk',name:'tahun_masuk',width:'80px',className: 'dt-center'},
            ],
            columnDefs: [
            {
                targets: [4], render: function (a, b, data, d) { 
                    var a = '<div style="width:200px;">';
                    a += '<span>'+data.nama+'</span>';
                    a += '</div>';
                    return a;
                }  
            },
            {
                targets: [5], render: function (a, b, data, d) { 
                    var a = '<div style="width:200px;">';
                    a += '<span>'+data.tempat_lahir+'</span>';
                    a += '</div>';
                    return a;
                }  
            },
            {
                targets: [6], render: function (a, b, data, d) { 
                    if(data.tgl_lahir!=="" && data.tgl_lahir!==null && data.tgl_lahir!==undefined){
                        var hsl1 = data.tgl_lahir.split('-');
                        var tgl_lahir = hsl1[2]+"."+hsl1[1]+"."+hsl1[0];
                    } else {
                        var tgl_lahir = "";
                    }
                    return tgl_lahir;
                }  
            },
            ],
            "stateSave": true,
            "scrollX": true,
            "ScrollXInner": true,
            "autoWidth": false
        });

        $('#filternya').on("click", function() {
            table.draw();
        });         

        $('.add_row').click(function () {
            $('#dataForm').trigger("reset");
            $('#modelHeading').html("Input Data Mahasiswa");
            $('#ModalForm').modal('show');
            var kd_prodicari = $('#kd_prodicari').val();
            $('#id').val('').trigger('change');
            if(kd_prodicari!=="" && kd_prodicari!=="semua"){
                $('#kd_prodi').val(kd_prodicari).trigger('change');
            } else {
                $('#kd_prodi').val('').trigger('change');
            }
            $('#nim').val('').trigger('change');
            $('#nama').val('').trigger('change');
            $('#tempat_lahir').val('').trigger('change');
        });        
        
        $('body').on('click', '.edit_row', function () {
            var id = $(this).data('id');
            $.get("{{ route('datamahasiswam') }}" +'/' + id, function (data) {   
                if(data.tgl_lahir!==""){
                    var tgl = data.tgl_lahir.split('-');
                    var tahun = tgl[0];
                    var bulan = tgl[1];
                    var hari = tgl[2];
                    var tgl_lahir2 = hari + '/' + bulan + '/' + tahun;
                } else {
                    var tgl_lahir2 = "";
                }

                $('#modelHeading').html("Edit Data Mahasiswa");
                $('#ModalForm').modal('show');
                $('#id').val(data.id);
                $('#kd_prodi').val(data.kd_prodi).trigger('change');
                $('#nim').val(data.nim);
                $('#nama').val(data.nama);
                $('#tempat_lahir').val(data.tempat_lahir);
                $('#tgl_lahir').val(tgl_lahir2).trigger('change');
                $('#jenis_kelamin').val(data.jenis_kelamin).trigger('change');
                $('#tahun_masuk').val(data.tahun_masuk);
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
                        url: "{{url('api/hapus-datamahasiswam')}}",
                        success: function (data) {
                            swal({
                                title: "Result",
                                text: 'Sukses hapus data mahasiswa',
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
                url: "{{ route('datamahasiswam.store') }}",
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
