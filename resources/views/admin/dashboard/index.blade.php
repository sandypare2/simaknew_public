
@extends('partials.layouts.master3')

    @section('title', 'Dashboard | SIMAK')
    @section('sub-title', 'Dashboard Talenta ' )
    @section('pagetitle', 'Dashboard')
    <!-- @section('buttonTitle', 'Add Product') -->
    <!-- @section('link', 'apps-product-create') -->
  
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
    @section('content')
    <div id="datatable-loader" style="display:none;position:fixed;top:25px;left:50%;transform:translateX(-50%);z-index:1055;">
        <div class="spinner-border spinner-border-sm text-primary"></div>
            <span class="ms-2">Loading data...</span>
        </div>
    </div>  

    <div class="row">
        <div class="col-12 col-md-6 col-lg-3 project-stat">
            <div class="card card-hover card-h-100 overflow-hidden border-primary border-3 border-bottom">
                <div class="card-body p-4 d-flex align-items-start gap-3 h-100">
                    <div class="flex-fill h-100 d-flex flex-column justify-content-between">
                        <div>
                            <h6 class="fw-semibold mb-1">Total Pegawai</h6>
                            <h4 class="mb-0 text-primary"><span data-target="{{ number_format($jumlah_pegawai,0,',','.') }}" data-duration="5" data-prefix="">{{ number_format($jumlah_pegawai,0,',','.') }}</span></h4>
                        </div>
                        <div class="text-muted fs-13 mt-2">
                            <!-- <span class="text-body fw-semibold me-1"><i class="ri-arrow-right-up-line fs-16 me-1"></i>6%</span>Up this month -->
                        </div>
                    </div>
                    <div><i class="ri-group-line display-6 fw-medium text-primary opacity-80"></i></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-3 project-stat">
            <div class="card card-hover card-h-100 overflow-hidden border-success border-3 border-bottom">
                <div class="card-body p-4 d-flex align-items-start gap-3 h-100">
                    <div class="flex-fill h-100 d-flex flex-column justify-content-between">
                        <div>
                            <h6 class="fw-semibold mb-1">Kantor Pusat</h6>
                            <h4 class="mb-0 text-success"><span data-target="{{ number_format($jumlah_pusat,0,',','.') }}" data-duration="5" data-prefix="">{{ number_format($jumlah_pusat,0,',','.') }}</span></h4>
                        </div>
                        <div class="text-muted fs-13 mt-2">
                            <div class="hstack gap-2">
                                <span class="fs-12">{{ $persen_pusat }}%</span>
                                <div class="progress progress-xs min-w-100px">
                                    <div class="progress-bar {{ $persen_pusat }} bg-success" role="progressbar" style="width: {{ $persen_pusat }}%" aria-valuenow="{{ $persen_pusat }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div><i class="ri-building-line display-6 fw-medium text-success opacity-80"></i></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-3 project-stat">
            <div class="card card-hover card-h-100 overflow-hidden border-warning border-3 border-bottom">
                <div class="card-body p-4 d-flex align-items-start gap-3 h-100">
                    <div class="flex-fill h-100 d-flex flex-column justify-content-between">
                        <div>
                            <h6 class="fw-semibold mb-1">Cabang / Site</h6>
                            <h4 class="mb-0 text-warning"><span data-target="{{ number_format($jumlah_cabang,0,',','.') }}" data-duration="5" data-prefix="">{{ number_format($jumlah_cabang,0,',','.') }}</span></h4>
                        </div>
                        <div class="text-muted fs-13 mt-2">
                            <div class="hstack gap-2">
                                <span class="fs-12">{{ $persen_cabang }}%</span>
                                <div class="progress progress-xs min-w-100px">
                                    <div class="progress-bar {{ $persen_cabang }} bg-warning" role="progressbar" style="width: {{ $persen_cabang }}%" aria-valuenow="{{ $persen_cabang }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div><i class="ri-community-line display-6 fw-medium text-warning opacity-80"></i></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-3 project-stat">
            <div class="card card-hover card-h-100 overflow-hidden border-info border-3 border-bottom">
                <div class="card-body p-4 d-flex align-items-start gap-3 h-100">
                    <div class="flex-fill h-100 d-flex flex-column justify-content-between">
                        <div>
                            <h6 class="fw-semibold mb-1">Direksi</h6>
                            <h4 class="mb-0 text-info"><span data-target="{{ number_format($jumlah_direksi,0,',','.') }}" data-duration="5" data-prefix="">{{ number_format($jumlah_direksi,0,',','.') }}</span></h4>
                        </div>
                        <div class="text-muted fs-13 mt-2">
                            <!-- <div class="hstack gap-2">
                                <span class="fs-12">60%</span>
                                <div class="progress progress-xs min-w-100px">
                                    <div class="progress-bar 60 bg-info" role="progressbar" style="width: 60%" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div> -->
                        </div>
                    </div>
                    <div><i class="ri-user-line display-6 fw-medium text-info opacity-80"></i></div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row g-4">
        <div class="col-lg-">
            <div class="card mb-0">
                <div class="card-body">
                    <div class="hstack justify-content-between gap-3 mb-6">
                        <h6 class="flex-grow-1 mb-0 text-truncate">Penilaian Terakhir</h6>
                        <div class="hstack gap-2 flex-shrink-0">
                            @if($tahun_talenta!="" && $semester_talenta!="")
                            <p class="fw-semibold text-muted fs-14 mb-0">Tahun : {{ $tahun_talenta }}, Semester : {{ $semester_talenta }}</p>                            
                            <!-- <span class="d-inline-flex align-items-center gap-2 text-body"><i class="ri-refresh-line fs-16 text-muted"></i>Refresh</span> -->
                            @endif
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-4 mb-1">
                        @foreach($rows5 as $row5)
                        @php 
                        $classnya = 'success';
                        if($row5->nama_talenta=='Perlu Perhatian' || $row5->nama_talenta=='Sangat Perlu Perhatian'){
                            $classnya = 'danger';
                        } else if($row5->nama_talenta=='Kandidat Potensial' || $row5->nama_talenta=='Perlu Penyesuaian'){
                            $classnya = 'warning';
                        } else if($row5->nama_talenta=='Potensial'){
                            $classnya = 'primary';
                        }
                        @endphp
                        <div class="col" style="min-width: 220px;">
                            <div class="card card-body mb-0 border border-dashed shadow-none bg-light bg-opacity-20 h-100">
                                <div class="hstack gap-2 flex-grow-1 mb-3">
                                    <div class="bg-{{ $classnya }}-subtle text-{{ $classnya }} avatar avatar-item rounded-2">
                                        <i class="ri-medal-2-line fs-16 fw-medium"></i>
                                    </div>
                                    <a href="#!" class="text-body fw-medium text-truncate">
                                        {{ $row5->nama_talenta }}
                                    </a>
                                </div>
                                <div class="mb-1">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <h4 class="mb-0 fw-semibold">{{ $row5->jumlah_talenta }}</h4>
                                        <span class="badge bg-{{ $classnya }}-subtle text-dark">pegawai</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach

                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="row g-4">
        <div class="col-12">
            <div class="card mb-0">
                <div class="card-body">
                    <div class="hstack justify-content-between gap-3 mb-6">
                        <h6 class="flex-grow-1 mb-0 text-truncate">History Penilaian Talenta</h6>
                        <div class="flex-shrink-0">
                            <div>
                                <span>Jenis Chart : </span>
                                <button id="btnArea" class="btn btn-primary btn-sm">AREA</button>
                                <button id="btnBar" class="btn btn-outline-primary btn-sm">BAR</button>
                            </div>
                        </div>
                    </div>
                    <div class="w-100">
                        <div id="chartTalenta" class="w-100"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .chart-container {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        justify-content: center;
        }
        .chart-card {
        flex: 1 1 45%;
        min-width: 350px;
        background: #fff;
        border-radius: 14px;
        padding: 16px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .chart-card h3 {
        text-align: center;
        margin-bottom: 8px;
        }
    </style>
@endsection

@section('js')
    <!-- Countup init -->
    <script type="module" src="{{ asset('assets/js/pages/countup.init.js') }}"></script>

    <!-- ApexChat js -->
    <script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>
    <!-- Include Highcharts -->
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/highcharts-3d.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>
    <script src="https://code.highcharts.com/modules/cylinder.js"></script>  
    
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const btnArea = document.getElementById('btnArea');
        const btnBar  = document.getElementById('btnBar');
        let options = {
            chart: {
                id: 'chartTalenta',
                type: 'area',
                height: 350,
                toolbar: { show: false },
                zoom: { enabled: false }
            },

            series: @json($series),

            xaxis: {
                categories: @json($categories),
                title: { text: 'Periode' }
            },

            yaxis: {
                title: { text: 'Total Talenta' }
            },
            dataLabels: {
                enabled: true,
                formatter: val => val === 0 ? '' : val,
                offsetY: -6,
                style: {
                    fontSize: '11px',
                    fontWeight: 'bold'
                }
            },
            stroke: {
                curve: 'smooth',
                width: 2
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.6,
                    opacityTo: 0.1
                }
            },
            legend: { position: 'top' }
        };

        let chart = new ApexCharts(
            document.querySelector("#chartTalenta"),
            options
        );
        chart.render();

        function setActive(activeBtn, inactiveBtn) {
            activeBtn.classList.add('btn-primary');
            activeBtn.classList.remove('btn-outline-primary');

            inactiveBtn.classList.add('btn-outline-primary');
            inactiveBtn.classList.remove('btn-primary');
        }

        setActive(btnArea, btnBar);
        btnArea.addEventListener('click', function () {
            chart.updateOptions({
                chart: { type: 'area' },
                stroke: { curve: 'smooth' },
                fill: { opacity: 0.6 },
                dataLabels: { enabled: true }
            });

            setActive(btnArea, btnBar);
        });

        btnBar.addEventListener('click', function () {
            chart.updateOptions({
                chart: { type: 'bar' },
                stroke: { curve: 'straight' },
                fill: { opacity: 1 },
                dataLabels: {
                    enabled: true,
                    position: 'top'
                }
            });

            setActive(btnBar, btnArea);
        });

    });
    </script>

    <script>
    var formatterID = new Intl.NumberFormat('id-ID');
    const data = {!! json_encode($datachart) !!};
    const charts = {};

    function createChart(id, cfg) {
        const options = {
            chart: { type: 'area', height: 50, toolbar: { show: false }, sparkline: { enabled: true} },
            colors: [cfg.color],
            series: cfg.series,
            xaxis: { categories: cfg.labels },
            stroke: { curve: 'smooth', width: 2 },
            dataLabels: { enabled: false },
            fill: {
            type: 'gradient',
            gradient: {
                // shadeIntensity: 1,
                // opacityFrom: 0.5,
                // opacityTo: 0.1,
                // stops: [0, 100]
                shade: 'light',
                shadeIntensity: 0.8,
                opacityFrom: 0.6,
                opacityTo: 0.5
            }
            },
            tooltip: { enabled: false, shared: true },
        };
        const chart = new ApexCharts(document.querySelector(`#${id}`), options);
        chart.render();
        charts[id] = chart;
    }

    // Create charts individually
    createChart('chart1', data.chart1);
    createChart('chart2', data.chart2);
    createChart('chart3', data.chart3);
    createChart('chart4', data.chart4);
    // createChart('chart5', data.chart5);

  </script>

    <!-- Ecommerce dashboard init -->
    <!-- <script src="{{ asset('assets/js/charts/apexcharts-config.init.js') }}"></script> -->
    <!-- <script src="{{ asset('assets/js/dashboards/dashboard-ecommerce.init.js') }}"></script> -->

    <!-- App js -->
    <script type="module" src="{{ asset('assets/js/app.js') }}"></script>
    @endsection 

    @push('scripts')
    <script>
    "use strict";
    $(function() {

        // $('#datatable-loader').show();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            }
        });  

        // var table = $('#tbl_list').DataTable({
        //     initComplete: function() {    
        //         var api = this.api();
        //         $('#tbl_list_filter input').unbind();
        //         $('#tbl_list_filter input').bind('keyup', function(e) {
        //             if(e.keyCode == 13) {
        //                 api.search(this.value).draw();
        //             }
        //         });
        //     },            
        //     dom: '<"card-header dt-head d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3"' +
        //         '<"head-label_tbl_list">' +
        //         '<"d-flex flex-column flex-sm-row align-items-center justify-content-sm-end gap-5 w-100"lf>' +
        //         '>' +
        //         '<"table-responsive"t>' +
        //         '<"card-footer d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2"i' +
        //         '<"d-flex align-items-sm-center justify-content-end gap-4"p>' +
        //         '>',        
        //     language: {
        //         processing: "",
        //         paginate: {
        //             next: '<i class="ri-arrow-right-s-line"></i>',
        //             previous: '<i class="ri-arrow-left-s-line"></i>'
        //         }
        //     },            
        //     processing: true,
        //     serverSide: true,
        //     deferRender: true,
        //     ajax: {
        //         url: "{{ url('api/get-dinas-dashboard') }}",
        //         data: function (d) {
        //             d.search = $('#tbl_list_filter input').val()
        //         }
        //     },  
        //     columns: [
        //         {data: 'pegawainya',name:'pegawainya',width:'140px',className: 'dt-left wrap'},
        //         {data: 'nomornya',name:'nomornya',width:'200px',className: 'dt-left wrap'},
        //         {data: 'maksud',name:'maksud',width:'250px',className: 'dt-left wrap'},
        //         {data: 'kedudukan_tujuan',name:'kedudukan_tujuan',width:'120px',className: 'dt-left wrap'},
        //         {data: 'periodenya',name:'periodenya',width:'100px',className: 'dt-center'},
        //     ],
        //     columnDefs: [
        //     {
        //         targets: [0], render: function (a, b, data, d) { 
        //             var a ='<div style="width:140px;">'
        //             a += '<span class="badge bg-info-subtle text-dark fs-10 h-100 mb-0" style="font-weight:500;">'+data.bagian_dinas+'</span>';
        //             a += '<br/><span class="fs-11">'+data.nama+'</span>';
        //             a += '<br/><span class="text-muted fs-11">'+data.nip+'</span>';
        //             a += '</div>';
        //             return a;
        //         }  
        //     },
        //     {
        //         targets: [1], render: function (a, b, data, d) { 
        //             var a ='<div style="width:200px;">'
        //             a += '<span class="fs-10">'+data.no_surat_tugas+'</span>';
        //             a += '<br/><span class="fs-10">'+data.no_surat_sppd+'</span>';
        //             a += '</div>';
        //             return a;
        //         }  
        //     },
        //     {
        //         targets: [2], render: function (a, b, data, d) { 
        //             var a ='<div style="width:250px;">'
        //             a += '<span class="fs-11">'+data.maksud+'</span>';
        //             a += '</div>';
        //             return a;
        //         }  
        //     },
        //     {
        //         targets: [3], render: function (a, b, data, d) { 
        //             var a ='<div style="width:120px;">'
        //             a += '<span class="fs-10">'+data.kedudukan+'</span>';
        //             a += '<br/><span class="text-primary fs-10">'+data.tujuan+'</span>';
        //             a += '</div>';
        //             return a;
        //         }  
        //     },
        //     {
        //         targets: [4], render: function (a, b, data, d) { 
        //             let tgl_berangkat = new Date(data.tgl_berangkat);
        //             let tgl_berangkatnya = tgl_berangkat.toLocaleDateString("id-ID", {
        //                 day: 'numeric',
        //                 month: 'long',
        //                 year: 'numeric'
        //             });
        //             var a ='<div style="width:100px;">'
        //             a += '<span class="badge bg-primary-subtle text-dark h-100 mb-1" style="font-weight:500;">'+tgl_berangkatnya+'</span>';
        //             a += '<br/><span class="badge bg-danger-subtle text-dark" h-100 style="font-weight:500;">'+tgl_berangkatnya+'</span>';
        //             a += '</div>';
        //             return a;
        //         }  
        //     },
        //     ],
        //     "ordering": false,
        //     "stateSave": true,
        //     "scrollX": true,
        //     "ScrollXInner": true,
        //     "autoWidth": false,
        //     "pagingType": 'simple_numbers'

        // });
        // document.querySelector('div.head-label_tbl_list').innerHTML = '<h5 class="card-title text-nowrap mb-0">Pegawai Sedang Dinas</h5>';
        // table.on('preXhr.dt', function() {
        //     $('.dataTables_wrapper').addClass('loading');
        //     $('#datatable-loader').fadeIn(200);
        // });
        // table.on('xhr.dt', function() {
        //     $('.dataTables_wrapper').removeClass('loading');
        //     $('#datatable-loader').fadeOut(300);
        // });

    });
    </script>    
    @endpush