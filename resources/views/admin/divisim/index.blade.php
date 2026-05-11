
@extends('partials.layouts.master3')

@section('title', 'Master Divisi | SIMAK')
@section('sub-title', 'Master Divisi ' )
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
                <div class="table-responsive">
                    <table id="tbl_list" class="table data-table-responsive table-hover align-middle table table-nowrap w-100 custom-table">
                        <thead>
                        <tr>
                            <th class="center">Aksi</th>
                            <th>Kode</th>
                            <th>Nama Divisi</th>
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
                    <input type="hidden" name="kd_divisi2" id="kd_divisi2">
                    @csrf
                    <div class="mb-2">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Nama Divisi</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control form-control-sm" id="nama_divisi" name="nama_divisi" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
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
            url: "{{ route('divisim') }}",
            data: function (d) {
                d.search = $('#tbl_list_filter input').val()
            }
        },            
        columns: [
            {data: 'aksi', name:'aksi',width:'50px', orderable: false, searchable: false},
            {data: 'kd_divisi',name:'kd_divisi',width:'50px',className: 'dt-center'},
            {data: 'nama_divisi',name:'nama_divisi',width:'300px',className: 'dt-left wrap'},
        ],
        columnDefs: [
        {
            targets: [2], render: function (a, b, data, d) { 
                var a = '<div style="width:300px;">';
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
    document.querySelector('div.head-label_tbl_list').innerHTML = '<a title="Input Data" class="add_row"><button type="button" class="btn btn-success btn-sm" style="width:100px;"><i class="ri-add-line me-1"></i>Input Data</button></a>';

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
        $('#id').val('').trigger('change');
        $('#kd_divisi2').val('').trigger('change');
        $('#dataForm').trigger("reset");
        $('#modelHeading').html("Input Divisi");
        $('#ModalForm').modal('show');
    });        
    
    $('body').on('click', '.edit_row', function () {
        var id = $(this).data('id');
        $.get("{{ route('divisim') }}" +'/' + id, function (data) {                
            $('#modelHeading').html("Edit Divisi");
            $('#ModalForm').modal('show');
            $('#id').val(data.id);
            $('#kd_divisi2').val(data.kd_divisi);
            $('#nama_divisi').val(data.nama_divisi);
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
                    url: "{{url('api/hapus-prodim')}}",
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
        if ($btn.data('loading')) return;
        $btn.data('loading', true).prop('disabled', true);
        $btn.data('orig-html', $btn.html());
        var text = $btn.find('.btn-text').text() || $btn.text().trim();
        var spinner = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>';
        $btn.html(spinner + '<span class="btn-text">' + text + '</span>');
        $.ajax({
            data: $('#dataForm').serialize(),
            url: "{{ route('divisim.store') }}",
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

        $('#tanggal1').datepicker({
            autoclose: true,
            format: 'dd/mm/yyyy',
            formatSubmit: 'yyyy-mm-dd',
            todayHighlight: true,
            enableOnReadonly: false
        });
        $('#tanggal11').datepicker({
            autoclose: true,
            format: 'dd/mm/yyyy',
            formatSubmit: 'yyyy-mm-dd',
            todayHighlight: true,
            enableOnReadonly: false
        });
        $('#tanggal2').datepicker({
            autoclose: true,
            format: 'dd/mm/yyyy',
            formatSubmit: 'yyyy-mm-dd',
            todayHighlight: true,
            enableOnReadonly: false
        });
        $('#tanggal21').datepicker({
            autoclose: true,
            format: 'dd/mm/yyyy',
            formatSubmit: 'yyyy-mm-dd',
            todayHighlight: true,
            enableOnReadonly: false
        });
        $('#tanggal3').datepicker({
            autoclose: true,
            format: 'dd/mm/yyyy',
            formatSubmit: 'yyyy-mm-dd',
            todayHighlight: true,
            enableOnReadonly: false
        });
        $('#tanggal31').datepicker({
            autoclose: true,
            format: 'dd/mm/yyyy',
            formatSubmit: 'yyyy-mm-dd',
            todayHighlight: true,
            enableOnReadonly: false
        });
        $('#tanggal4').datepicker({
            autoclose: true,
            format: 'dd/mm/yyyy',
            formatSubmit: 'yyyy-mm-dd',
            todayHighlight: true,
            enableOnReadonly: false
        });
        $('#tanggal41').datepicker({
            autoclose: true,
            format: 'dd/mm/yyyy',
            formatSubmit: 'yyyy-mm-dd',
            todayHighlight: true,
            enableOnReadonly: false
        });
        $('#tanggal1').datepicker().on('changeDate', function (selected) {
            var minDate = new Date(selected.date.valueOf());
            $('#tanggal11').datepicker('setStartDate', minDate);
        });
        $('#tanggal11').datepicker().on('changeDate', function (selected) {
            var minDate = new Date(selected.date.valueOf());
            minDate = minDate.setDate(minDate.getDate() + 1);
            minDate = new Date(minDate);
            $('#tanggal2').datepicker('setStartDate', minDate);
            $('#tanggal3').datepicker('setStartDate', minDate);
            $('#tanggal4').datepicker('setStartDate', minDate);
        });
        $('#tanggal2').datepicker().on('changeDate', function (selected) {
            var minDate = new Date(selected.date.valueOf());
            $('#tanggal21').datepicker('setStartDate', minDate);
        });
        $('#tanggal21').datepicker().on('changeDate', function (selected) {
            var minDate = new Date(selected.date.valueOf());
            minDate = minDate.setDate(minDate.getDate() + 1);
            minDate = new Date(minDate);
            $('#tanggal3').datepicker('setStartDate', minDate);
            $('#tanggal4').datepicker('setStartDate', minDate);
        });
        $('#tanggal3').datepicker().on('changeDate', function (selected) {
            var minDate = new Date(selected.date.valueOf());
            $('#tanggal31').datepicker('setStartDate', minDate);
        });
        $('#tanggal31').datepicker().on('changeDate', function (selected) {
            var minDate = new Date(selected.date.valueOf());
            minDate = minDate.setDate(minDate.getDate() + 1);
            minDate = new Date(minDate);
            $('#tanggal4').datepicker('setStartDate', minDate);
        });
        $('#tanggal4').datepicker().on('changeDate', function (selected) {
            var minDate = new Date(selected.date.valueOf());
            $('#tanggal41').datepicker('setStartDate', minDate);
        });
        $("#tanggal1").on("change",function(){
            var tgl_awal=$("#tanggal1").val();
            var tgl_akhir=$("#tanggal11").val();
            if(tgl_awal!=="" && tgl_awal!==""){
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
                var hari1 = filterFloat(date_diff_indays(tgl_awalnya, tgl_akhirnya)+1);
                if(hari1<0){
                    hari1 = 0;
                }
            } else {
                var hari1 = 0;
            }
            $("#hari1").val(hari1);
        });        
        $("#tanggal11").on("change",function(){
            var tgl_awal=$("#tanggal1").val();
            var tgl_akhir=$("#tanggal11").val();
            if(tgl_awal!=="" && tgl_awal!==""){
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
                var hari1 = filterFloat(date_diff_indays(tgl_awalnya, tgl_akhirnya)+1);
                if(hari1<0){
                    hari1 = 0;
                }
            } else {
                var hari1 = "";
            }            
            $("#hari1").val(hari1);
        });      
        $("#tanggal2").on("change",function(){
            var tgl_awal=$("#tanggal2").val();
            var tgl_akhir=$("#tanggal21").val();
            if(tgl_awal!=="" && tgl_awal!==""){
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
                var hari2 = filterFloat(date_diff_indays(tgl_awalnya, tgl_akhirnya)+1);
                if(hari2<0){
                    hari2 = 0;
                }
            } else {
                var hari2 = 0;
            }
            $("#hari2").val(hari2);
        });        
        $("#tanggal21").on("change",function(){
            var tgl_awal=$("#tanggal2").val();
            var tgl_akhir=$("#tanggal21").val();
            if(tgl_awal!=="" && tgl_awal!==""){
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
                var hari2 = filterFloat(date_diff_indays(tgl_awalnya, tgl_akhirnya)+1);
                if(hari2<0){
                    hari2 = 0;
                }
            } else {
                var hari2 = 0;
            }
            $("#hari2").val(hari2);
        });        
        
        $("#tanggal3").on("change",function(){
            var tgl_awal=$("#tanggal3").val();
            var tgl_akhir=$("#tanggal31").val();
            if(tgl_awal!=="" && tgl_awal!==""){
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
                var hari3 = filterFloat(date_diff_indays(tgl_awalnya, tgl_akhirnya)+1);
                if(hari3<0){
                    hari3 = 0;
                }
            } else {
                var hari3 = 0;
            }
            $("#hari3").val(hari3);
        });        
        $("#tanggal31").on("change",function(){
            var tgl_awal=$("#tanggal3").val();
            var tgl_akhir=$("#tanggal31").val();
            if(tgl_awal!=="" && tgl_awal!==""){
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
                var hari3 = filterFloat(date_diff_indays(tgl_awalnya, tgl_akhirnya)+1);
                if(hari3<0){
                    hari3 = 0;
                }
            } else {
                var hari3 = 0;
            }
            $("#hari3").val(hari3);
        });    
        
        $("#tanggal4").on("change",function(){
            var tgl_awal=$("#tanggal4").val();
            var tgl_akhir=$("#tanggal41").val();
            if(tgl_awal!=="" && tgl_awal!==""){
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
                var hari4 = filterFloat(date_diff_indays(tgl_awalnya, tgl_akhirnya)+1);
                if(hari4<0){
                    hari4 = 0;
                }
            } else {
                var hari4 = 0;
            }
            $("#hari4").val(hari4);
        });        
        $("#tanggal41").on("change",function(){
            var tgl_awal=$("#tanggal4").val();
            var tgl_akhir=$("#tanggal41").val();
            if(tgl_awal!=="" && tgl_awal!==""){
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
                var hari4 = filterFloat(date_diff_indays(tgl_awalnya, tgl_akhirnya)+1);
                if(hari4<0){
                    hari4 = 0;
                }
            } else {
                var hari4 = 0;
            }
            $("#hari4").val(hari4);
        });   

        function aktifkan1(){
            $('#kota_asal1').removeAttr('readonly');
            $('#kota_tujuan1').removeAttr('readonly');
            $('#transportasi1').removeAttr('readonly');
            $('#daerah1').removeAttr('readonly');
            $('#tanggal1').removeAttr('readonly');
            $('#tanggal11').removeAttr('readonly');
        }
        function aktifkan2(){
            // $('#kota_asal2').removeAttr('readonly');
            $('#kota_asal2').attr("readonly", "readonly");
            $('#kota_tujuan2').removeAttr('readonly');
            $('#transportasi2').removeAttr('readonly');
            $('#daerah2').removeAttr('readonly');
            $('#tanggal2').removeAttr('readonly');
            $('#tanggal21').removeAttr('readonly');
        }
        function aktifkan3(){
            // $('#kota_asal3').removeAttr('readonly');
            $('#kota_asal3').attr("readonly", "readonly");
            $('#kota_tujuan3').removeAttr('readonly');
            $('#transportasi3').removeAttr('readonly');
            $('#daerah3').removeAttr('readonly');
            $('#tanggal3').removeAttr('readonly');
            $('#tanggal31').removeAttr('readonly');
        }
        function aktifkan4(){
            // $('#kota_asal4').removeAttr('readonly');
            $('#kota_asal4').attr("readonly", "readonly");
            $('#kota_tujuan4').removeAttr('readonly');
            $('#transportasi4').removeAttr('readonly');
            $('#daerah4').removeAttr('readonly');
            $('#tanggal4').removeAttr('readonly');
            $('#tanggal41').removeAttr('readonly');
        }

        function matikan1(){
            $('#kota_asal1').attr("readonly", "readonly");
            $('#kota_tujuan1').attr("readonly", "readonly");
            $('#transportasi1').attr("readonly", "readonly");
            $('#daerah1').attr("readonly", "readonly");
            $('#tanggal1').attr("readonly", "readonly");
            $('#tanggal11').attr("readonly", "readonly");
        }
        function matikan2(){
            $('#kota_asal2').attr("readonly", "readonly");
            $('#kota_tujuan2').attr("readonly", "readonly");
            $('#transportasi2').attr("readonly", "readonly");
            $('#daerah2').attr("readonly", "readonly");
            $('#tanggal2').attr("readonly", "readonly");
            $('#tanggal21').attr("readonly", "readonly");

            $("#kota_asal2").val("").trigger('change');
            $("#kota_tujuan2").val("").trigger('change');
            $("#transportasi2").val("").trigger('change');
            $("#daerah2").val("").trigger('change');
            $("#tanggal2").val("");
            $("#tanggal21").val("");
            $("#hari2").val("");

        }
        function matikan3(){
            $('#kota_asal3').attr("readonly", "readonly");
            $('#kota_tujuan3').attr("readonly", "readonly");
            $('#transportasi3').attr("readonly", "readonly");
            $('#daerah3').attr("readonly", "readonly");
            $('#tanggal3').attr("readonly", "readonly");
            $('#tanggal31').attr("readonly", "readonly");
            
            $("#kota_asal3").val("").trigger('change');
            $("#kota_tujuan3").val("").trigger('change');
            $("#transportasi3").val("").trigger('change');
            $("#daerah3").val("").trigger('change');
            $("#tanggal3").val("");
            $("#tanggal31").val("");
            $("#hari3").val("");
        }
        function matikan4(){
            $('#kota_asal4').attr("readonly", "readonly");
            $('#kota_tujuan4').attr("readonly", "readonly");
            $('#transportasi4').attr("readonly", "readonly");
            $('#daerah4').attr("readonly", "readonly");
            $('#tanggal4').attr("readonly", "readonly");
            $('#tanggal41').attr("readonly", "readonly");
            
            $("#kota_asal4").val("").trigger('change');
            $("#kota_tujuan4").val("").trigger('change');
            $("#transportasi4").val("").trigger('change');
            $("#daerah4").val("").trigger('change');
            $("#tanggal4").val("");
            $("#tanggal41").val("");
            $("#hari4").val("");
        }
        
        $('#daerah1').on('change', function () {
            var daerah1 = this.value;
            if(daerah1!==""){
                aktifkan2();
                $("#kota_asal2").val($("#kota_tujuan1").val()).trigger('change');
            } else {
                matikan2();
            }
        });
        
        $('#daerah2').on('change', function () {
            var daerah2 = this.value;
            if(daerah2!==""){
                aktifkan3();
                $("#kota_asal3").val($("#kota_tujuan2").val()).trigger('change');
            } else {
                matikan3();
            }
        });
        
        $('#daerah3').on('change', function () {
            var daerah3 = this.value;
            if(daerah3!==""){
                aktifkan4();
                $("#kota_asal4").val($("#kota_tujuan3").val()).trigger('change');
            } else {
                matikan4();
            }
        });

        $('#dinas').on('select2:select', function () {
            var dinas = this.value;
            $("#kode_kegiatan").html('');
            $.ajax({
                url: "{{url('api/fetch-kegiatan-anggaranm')}}",
                type: "POST",
                data: {
                    dinas: dinas,
                    _token: '{{csrf_token()}}'
                },
                dataType: 'json',
                success: function (result) {
                    $('#kode_kegiatan').html('<option value="" selected>-</option>');
                    $.each(result.filter_kegiatan, function (key, value) {
                        $("#kode_kegiatan").append('<option value="' + value.kode_kegiatan + '">' + value.kode_kegiatan+" ("+value.nama_kegiatan+")" + '</option>');
                    });
                }
            });

            $("#nip_pptk").html('');
            $.ajax({
                url: "{{url('api/fetch-pptk-anggaranm')}}",
                type: "POST",
                data: {
                    dinas: dinas,
                    _token: '{{csrf_token()}}'
                },
                dataType: 'json',
                success: function (result) {
                    $('#nip_pptk').html('<option value="" selected>-</option>');
                    $.each(result.filter_pptk, function (key, value) {
                        $("#nip_pptk").append('<option value="' + value.nip + '">' + value.nip+" ("+value.nama+")" + '</option>');
                    });
                }
            });

            $("#nip_ppk").html('');
            $.ajax({
                url: "{{url('api/fetch-ppk-anggaranm')}}",
                type: "POST",
                data: {
                    dinas: dinas,
                    _token: '{{csrf_token()}}'
                },
                dataType: 'json',
                success: function (result) {
                    $('#nip_ppk').html('<option value="" selected>-</option>');
                    $.each(result.filter_ppk, function (key, value) {
                        $("#nip_ppk").append('<option value="' + value.nip + '">' + value.nip+" ("+value.nama+")" + '</option>');
                    });
                }
            });
        });
                
        $('#kode_kegiatan').on('select2:select', function () {
            var dinas = $("#dinas").val();
            var kode_kegiatan = this.value;
            $("#kode_rekening").html('');
            $.ajax({
                url: "{{url('api/fetch-rekening2-datasppd')}}",
                type: "POST",
                data: {
                    dinas: dinas,
                    kode_kegiatan: kode_kegiatan,
                    _token: '{{csrf_token()}}'
                },
                dataType: 'json',
                success: function (result) {
                    $('#kode_rekening').html('<option value="">-</option>');
                    $.each(result.filter_rekening, function (key, value) {
                        $("#kode_rekening").append('<option value="' + value.kode_rekening + '">' + value.kode_rekening+' ('+ value.nama_rekening+')' + '</option>');
                    });
                }
            });
        });

        $('#kode_rekening').on('select2:select', function () {
            var dinas = $("#dinas").val();
            var kode_kegiatan = $("#kode_kegiatan").val();
            var kode_rekening = this.value;

            $("#pptk").html('');
            $.ajax({
                url: "{{url('api/fetch-pptk2-datasppd')}}",
                type: "POST",
                data: {
                    dinas: dinas,
                    kode_kegiatan: kode_kegiatan,
                    kode_rekening: kode_rekening,
                    _token: '{{csrf_token()}}'
                },
                dataType: 'json',
                success: function (result) {
                    $('#pptk').html('<option value="">-</option>');
                    $.each(result.filter_pptk, function (key, value) {
                        $("#pptk").append('<option value="' + value.pptk + '">' + value.nip_pptk+' ('+ value.nama_pptk+')' + '</option>');
                    });
                }
            });
            
            $("#ppk").html('');
            $.ajax({
                url: "{{url('api/fetch-ppk2-datasppd')}}",
                type: "POST",
                data: {
                    dinas: dinas,
                    kode_kegiatan: kode_kegiatan,
                    kode_rekening: kode_rekening,
                    _token: '{{csrf_token()}}'
                },
                dataType: 'json',
                success: function (result) {
                    $('#ppk').html('<option value="">-</option>');
                    $.each(result.filter_ppk, function (key, value) {
                        $("#ppk").append('<option value="' + value.ppk + '">' + value.nip_ppk+' ('+ value.nama_ppk+')' + '</option>');
                    });
                }
            });
        });
        
        $('#beban_anggaran').on('select2:select', function () {
            var beban_anggaran = this.value;
            $("#anspt").html('');
            $("#namaspt").html('');
            $.ajax({
                url: "{{url('api/fetch-pejabat1-datasppd')}}",
                type: "POST",
                data: {
                    beban_anggaran: beban_anggaran,
                    _token: '{{csrf_token()}}'
                },
                dataType: 'json',
                success: function (result) {
                    // alert(JSON.stringify(result));
                    $('#anspt').html('<option value="">-</option>');
                    // $('#namaspt').html('<option value="">-</option>');
                    $.each(result.filter_pejabat1, function (key, value) {
                        $("#anspt").append('<option value="' + value.jabatan + '">' + value.jabatan+ '</option>');
                        // $("#namaspt").append('<option value="' + value.nama + '">' + value.nama+ '</option>');
                    });
                    $('#namaspt2').html('<option value="">-</option>');
                    $.each(result.filter_pejabat1, function (key, value) {
                        // $("#namaspt2").append('<option value="' + value.nama+'|'+value.nip+'|'+value.pangkat+'|'+value.jabatan+'|'+value.muspida+'|'+value.eselon+'|'+value.golongan+'|'+value.kepala_daerah + '">' + value.nama+" ("+value.nip+")"+ '</option>');
                        $("#namaspt2").append('<option value="' + value.nama+'|'+value.nip+'|'+value.pangkat+'|'+value.jabatan+'|'+value.beban_anggaran + '">' + value.nama+ '</option>');
                    });
                }
            });
        });
        
        $('#namaspt2').on('select2:select', function () {
            var namaspt2 = this.value;
            const myArray = namaspt2.split("|");
            var nama = myArray[0];
            var nip = myArray[1];
            var pangkat = myArray[2];
            var jabatan = myArray[3];
            var beban_anggaran = myArray[4];
            $("#namaspt").val(nama);
            $("#nipspt").val(nip);
            $("#pangkatspt").val(pangkat);
            $("#jabatanspt").val(jabatan);
            $("#beban_anggaranspt").val(beban_anggaran);
        });
        
        $('#pegawai2').on('select2:select', function () {
            var pegawai2 = this.value;
            const myArray = pegawai2.split("|");
            var nip = myArray[0];
            var nama = myArray[1];
            var pangkat = myArray[2];
            var jabatan = myArray[3];
            var muspida = myArray[4];
            var eselon = myArray[5];
            var golongan = myArray[6];
            var kepala_daerah = myArray[7];
            $("#nama2").val(nama);
            $("#nip2").val(nip);
            $("#pangkat2").val(pangkat);
            $("#jabatan2").val(jabatan);
            $("#muspida2").val(muspida);
            $("#eselon2").val(eselon);
            $("#golongan2").val(golongan);
            $("#kepala_daerah2").val(kepala_daerah);
        });

        $("#qtyrincianbiaya").on("change",function(){
            var qty = $("#qtyrincianbiaya").val();
            var rupiah = $("#rupiahrincianbiaya").val();
            qty = qty.replaceAll(",","");
            rupiah = rupiah.replaceAll(",","");
            if(parseFloat(qty)>0 && parseFloat(rupiah)>0){
                var jumlahnya = parseFloat(qty)*parseFloat(rupiah);                
            } else {
                var jumlahnya = 0;
            }
            $('#jumlahrincianbiaya').val(jumlahnya.toLocaleString("en"));
        });

        $("#rupiahrincianbiaya").on("change",function(){
            var qty = $("#qtyrincianbiaya").val();
            var rupiah = $("#rupiahrincianbiaya").val();
            qty = qty.replaceAll(",","");
            rupiah = rupiah.replaceAll(",","");
            if(parseFloat(qty)>0 && parseFloat(rupiah)>0){
                var jumlahnya = parseFloat(qty)*parseFloat(rupiah);                
            } else {
                var jumlahnya = 0;
            }
            $('#jumlahrincianbiaya').val(jumlahnya.toLocaleString("en"));
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
