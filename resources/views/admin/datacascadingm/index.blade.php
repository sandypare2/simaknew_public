
@extends('partials.layouts.master3')

@section('title', 'Cascading KPI | SIMAK')
@section('sub-title', 'Cascading KPI' )
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
                        <div class="row flex-grow-1 mb-1">
                            <div class="col-md-2 grid-margin">
                                <select id="tahuncari" name="tahuncari" class="select2 form-control form-control-sm form-control form-control-sm-sm" data-allow-clear="true" style="width:100%;">
                                    @foreach ($datatahun as $data)
                                        <option value="{{ $data }}">{{ $data }}</option>
                                    @endforeach
                                </select>                
                            </div>
                            <div class="col-md-3 grid-margin">
                                <!-- <select id="jenis_kpicari" name="jenis_kpicari" class="select2 form-select" data-allow-clear="true">
                                    <option value="semua" selected>SEMUA</option>
                                    <option value="pusat">PUSAT</option>
                                    <option value="cabang">CABANG</option>
                                </select> -->
                                <select id="kd_areacari" name="kd_areacari" class="select2 form-control form-control-sm form-control form-control-sm-sm" data-allow-clear="true" style="width:100%;>
                                    <option value="semua" selected>SEMUA</option>
                                    @foreach ($masteraream as $data)
                                        <option value="{{ $data->kd_area }}">{{ $data->nama_area }}</option>
                                    @endforeach
                                </select>                
                            </div>
                            <div class="col-md-7 grid-margin">
                                <button type="button" id="filternya" class="btn btn-info btn-sm"><i class="ri-search-line font-size-14 me-1"></i>Filter Data</button>
                            </div>
                        </div>   
                        @if(Auth::user()->role=="superadmin") 
                        <div class="row flex-grow-1" style="margin-top:10px;">
                            <div class="col-md-12 grid-margin">
                                <a href="javascript:void(0)" title="Import Cascading" class="import_row"><button type="button" class="btn btn-success btn-sm"><i class="ri-upload-cloud-line me-1 font-size-14"></i>Import</button></a>
                                <a href="javascript:void(0)" title="Reset Cascading" class="reset_row"><button type="button" class="btn btn-danger btn-sm"><i class="ri-refresh-line me-1 font-size-14"></i>Reset</button></a>
                                <a href="javascript:void(0)" title="Mapping KPI" class="proses_mapping"><button type="button" class="btn btn-primary btn-sm"><i class="ri-settings-3-line me-1 font-size-14"></i>Proses Mapping KPI</button></a>
                                <a href="javascript:void(0)" title="Reset Mapping KPI" class="reset_mapping"><button type="button" class="btn btn-danger btn-sm"><i class="ri-refresh-line me-1 font-size-14"></i>Reset Mapping KPI</button></a>
                            </div>    
                        </div>    
                        @endif   
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="tbl_list" class="table data-table-responsive table-hover align-middle table table-nowrap w-100 custom-table">
                        <thead>
                        <tr>
                            <th>Aksi</th>
                            <th>Tahun</th>
                            <th>Jenis KPI</th>
                            <th>Cabang/Site</th>
                            <th>Divisi</th>
                            <th>Uraian</th>
                            <th>Level KPI</th>
                            <th>Prioritas</th>
                            <th>Type Target</th>
                            <th>Polarisasi</th>
                            <th>Ket</th>
                            <th>Satuan</th>
                            <th>Jan</th>
                            <th>Feb</th>
                            <th>Mar</th>
                            <th>Apr</th>
                            <th>Mei</th>
                            <th>Jun</th>
                            <th>Jul</th>
                            <th>Agt</th>
                            <th>Sep</th>
                            <th>Okt</th>
                            <th>Nop</th>
                            <th>Des</th>
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
<div class="modal" id="ModalForm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="modelHeading"></h6>   
                <button type="button" class="btn btn-close btn-sm" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="dataForm" name="dataForm" class="form-horizontal" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <input type="hidden" name="id" id="id">
                    <div class="mb-2">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Tahun</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control form-control-sm form-control form-control-sm-sm" id="tahun" name="tahun" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" style="width:100%;" readonly />
                        </div>
                    </div>   
                    <div class="mb-2">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Jenis KPI</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control form-control-sm" id="jenis_kpi" name="jenis_kpi" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" readonly />
                        </div>
                    </div>   
                    <div class="mb-2">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Unit kerja</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control form-control-sm" id="nama_area" name="nama_area" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" readonly />
                        </div>
                    </div>   
                    <div class="mb-2">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Divisi</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control form-control-sm" id="nama_divisi" name="nama_divisi" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" readonly />
                        </div>
                    </div>   
                    <div class="mb-2">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Uraian KPI</label>
                        <div class="input-group input-group-merge">
                            <!-- <input type="text" class="form-control form-control-sm" id="uraian" name="uraian" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" readonly /> -->
                            <textarea name="uraian" id="uraian" class="form-control form-control-sm textarea" rows="2" style="height:auto !important;" readonly></textarea>
                        </div>
                    </div>   
                    <div class="mb-2">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Type Target</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control form-control-sm" id="type_target" name="type_target" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" readonly />
                        </div>
                    </div>   
                    <div class="mb-2">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Polarisasi</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control form-control-sm" id="polarisasi" name="polarisasi" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" readonly />
                        </div>
                    </div>   

                    <div class="divider-colored divider-primary my-3">
                        <span class="divider-label">Satuan</span>
                    </div>
                    <div class="mb-1">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Kuantitas</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control form-control-sm" id="satuan_kuantitas" name="satuan_kuantitas" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div>   
                    <div class="mb-1">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Kualitas</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control form-control-sm" id="satuan_kualitas" name="satuan_kualitas" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div>   
                    <div class="mb-1">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Waktu</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control form-control-sm form-control form-control-sm-sm" id="satuan_waktu" name="satuan_waktu" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div>   
                    <div class="mb-2">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Level KPI</label>
                        <div class="input-group input-group-merge">
                            <select id="level_kpi" name="level_kpi" class="form-control form-control-sm form-control form-control-sm-sm select2" style="width:100%;">
                                <option value="" selected>-- Pilih level KPI --</option>
                            </select>                            
                        </div>
                    </div>   
                    
                    <div class="divider-colored divider-primary my-3">
                        <span class="divider-label">Target Januari</span>
                    </div>
                    <div class="mb-1">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Kuantitas</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control form-control-sm" id="target01kn" name="target01kn" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div>   
                    <div class="mb-1">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Kualitas</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control form-control-sm" id="target01kl" name="target01kl" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div>   
                    <div class="mb-1">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Waktu</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control form-control-sm" id="target01wk" name="target01wk" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div>   
                    <div class="mb-1">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Persen</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control form-control-sm" id="target01" name="target01" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div>   
                    
                    <div class="divider-colored divider-primary my-3">
                        <span class="divider-label">Target Februari</span>
                    </div>
                    <div class="mb-1">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Kuantitas</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control form-control-sm" id="target02kn" name="target02kn" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div>   
                    <div class="mb-1">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Kualitas</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control form-control-sm" id="target02kl" name="target02kl" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div>   
                    <div class="mb-1">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Waktu</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control form-control-sm" id="target02wk" name="target02wk" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div>   
                    <div class="mb-1">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Persen</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control form-control-sm" id="target02" name="target02" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div>   
                    
                    <div class="divider-colored divider-primary my-3">
                        <span class="divider-label">Target Maret</span>
                    </div>
                    <div class="mb-1">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Kuantitas</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control form-control-sm" id="target03kn" name="target03kn" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div>   
                    <div class="mb-1">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Kualitas</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control form-control-sm" id="target03kl" name="target03kl" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div>   
                    <div class="mb-1">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Waktu</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control form-control-sm" id="target03wk" name="target03wk" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div>   
                    <div class="mb-1">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Persen</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control form-control-sm" id="target03" name="target03" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div>   
                    
                    <div class="divider-colored divider-primary my-3">
                        <span class="divider-label">Target April</span>
                    </div>
                    <div class="mb-1">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Kuantitas</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control form-control-sm" id="target04kn" name="target04kn" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div>   
                    <div class="mb-1">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Kualitas</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control form-control-sm" id="target04kl" name="target04kl" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div>   
                    <div class="mb-1">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Waktu</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control form-control-sm" id="target04wk" name="target04wk" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div>   
                    <div class="mb-1">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Persen</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control form-control-sm" id="target04" name="target04" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div>   
                    
                    <div class="divider-colored divider-primary my-3">
                        <span class="divider-label">Target Mei</span>
                    </div>
                    <div class="mb-1">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Kuantitas</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control form-control-sm" id="target05kn" name="target05kn" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div>   
                    <div class="mb-1">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Kualitas</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control form-control-sm" id="target05kl" name="target05kl" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div>   
                    <div class="mb-1">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Waktu</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control form-control-sm" id="target05wk" name="target05wk" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div>   
                    <div class="mb-1">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Persen</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control form-control-sm" id="target05" name="target05" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div>   
                    
                    <div class="divider-colored divider-primary my-3">
                        <span class="divider-label">Target Juni</span>
                    </div>
                    <div class="mb-1">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Kuantitas</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control form-control-sm" id="target06kn" name="target06kn" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div>   
                    <div class="mb-1">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Kualitas</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control form-control-sm" id="target06kl" name="target06kl" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div>   
                    <div class="mb-1">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Waktu</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control form-control-sm" id="target06wk" name="target06wk" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div>   
                    <div class="mb-1">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Persen</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control form-control-sm" id="target06" name="target06" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div>   
                    
                    <div class="divider-colored divider-primary my-3">
                        <span class="divider-label">Target Juli</span>
                    </div>
                    <div class="mb-1">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Kuantitas</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control form-control-sm" id="target07kn" name="target07kn" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div>   
                    <div class="mb-1">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Kualitas</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control form-control-sm" id="target07kl" name="target07kl" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div>   
                    <div class="mb-1">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Waktu</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control form-control-sm" id="target07wk" name="target07wk" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div>   
                    <div class="mb-1">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Persen</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control form-control-sm" id="target07" name="target07" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div>   
                    
                    <div class="divider-colored divider-primary my-3">
                        <span class="divider-label">Target Agustus</span>
                    </div>
                    <div class="mb-1">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Kuantitas</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control form-control-sm" id="target08kn" name="target08kn" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div>   
                    <div class="mb-1">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Kualitas</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control form-control-sm" id="target08kl" name="target08kl" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div>   
                    <div class="mb-1">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Waktu</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control form-control-sm" id="target08wk" name="target08wk" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div>   
                    <div class="mb-1">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Persen</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control form-control-sm" id="target08" name="target08" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div>   
                    
                    <div class="divider-colored divider-primary my-3">
                        <span class="divider-label">Target September</span>
                    </div>
                    <div class="mb-1">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Kuantitas</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control form-control-sm" id="target09kn" name="target09kn" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div>   
                    <div class="mb-1">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Kualitas</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control form-control-sm" id="target09kl" name="target09kl" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div>   
                    <div class="mb-1">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Waktu</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control form-control-sm" id="target09wk" name="target09wk" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div>   
                    <div class="mb-1">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Persen</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control form-control-sm" id="target09" name="target09" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div>   
                    
                    <div class="divider-colored divider-primary my-3">
                        <span class="divider-label">Target Oktober</span>
                    </div>
                    <div class="mb-1">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Kuantitas</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control form-control-sm" id="target10kn" name="target10kn" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div>   
                    <div class="mb-1">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Kualitas</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control form-control-sm" id="target10kl" name="target10kl" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div>   
                    <div class="mb-1">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Waktu</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control form-control-sm" id="target10wk" name="target10wk" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div>   
                    <div class="mb-1">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Persen</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control form-control-sm" id="target10" name="target10" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div>   
                    
                    <div class="divider-colored divider-primary my-3">
                        <span class="divider-label">Target Nopember</span>
                    </div>
                    <div class="mb-1">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Kuantitas</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control form-control-sm" id="target11kn" name="target11kn" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div>   
                    <div class="mb-1">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Kualitas</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control form-control-sm" id="target11kl" name="target11kl" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div>   
                    <div class="mb-1">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Waktu</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control form-control-sm" id="target11wk" name="target11wk" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div>   
                    <div class="mb-1">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Persen</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control form-control-sm" id="target11" name="target11" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div>   
                    
                    <div class="divider-colored divider-primary my-3">
                        <span class="divider-label">Target Desember</span>
                    </div>
                    <div class="mb-1">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Kuantitas</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control form-control-sm" id="target12kn" name="target12kn" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div>   
                    <div class="mb-1">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Kualitas</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control form-control-sm" id="target12kl" name="target12kl" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div>   
                    <div class="mb-1">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Waktu</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control form-control-sm" id="target12wk" name="target12wk" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div> 
                    <div class="mb-1">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Persen</label>
                        <div class="input-group input-group-merge">
                            <input type="text" class="form-control form-control-sm" id="target12" name="target12" placeholder="" aria-label="" aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div>   

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="saveBtn" class="btn btn-primary"><i class="ri-save-2-line me-1"></i>Simpan</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="ri-close-line me-1"></i>Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="ModalForm3" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="modelHeading3"></h6>      
                <button type="button" class="btn btn-close btn-sm" data-bs-dismiss="modal" aria-label="Close"></button>              
            </div>
            <div class="modal-body">
                <form id="dataForm3" name="dataForm3" class="form-horizontal" enctype="multipart/form-data">
                    {{ csrf_field() }}                    
                    <div class="mb-2">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Tahun</label>
                        <div class="input-group input-group-merge">
                            <select id="tahun3" name="tahun3" class="select2 form-select form-select-sm" data-allow-clear="true" style="width:100%;">
                                @foreach ($datatahun as $data)
                                    <option value="{{ $data }}">{{ $data }}</option>
                                @endforeach
                            </select>                
                        </div>
                    </div>   
                    <div class="mb-2">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Jenis</label>
                        <div class="input-group input-group-merge">
                            <!-- <select id="jenis_kpi3" name="jenis_kpi3" class="select2 form-select" data-allow-clear="true">
                                <option value="">--Pilih Jenis--</option>
                                <option value="pusat">PUSAT</option>
                                <option value="cabang">CABANG</option>
                            </select> -->
                            <select id="kd_area3" name="kd_area3" class="select2 form-select form-select-sm" data-allow-clear="true" style="width:100%;">
                                <option value="">--Pilih Jenis--</option>
                                @foreach ($masteraream as $data)
                                    <option value="{{ $data->kd_area }}">{{ $data->nama_area }}</option>
                                @endforeach
                            </select>                
                        </div>
                    </div>   
                    <div class="mb-2">
                        <label for="filecascading3" class="form-label-custom text-muted fs-13">File Cascading</label>
                        <div class="input-group input-group-merge">
                            <input type="file" class="form-control form-control-sm" autocomplete="off" name="filecascading3" id="filecascading3" placeholder="" accept=".xlsx">
                        </div>
                    </div>                    
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="saveBtn3" class="btn btn-primary"><i class="ri-save-2-line me-1"></i>Simpan</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="ri-close-line me-1"></i>Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="ModalForm4" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="modelHeading4"></h6>   
                <button type="button" class="btn btn-close btn-sm" data-bs-dismiss="modal" aria-label="Close"></button>                 
            </div>
            <div class="modal-body">
                <form id="dataForm4" name="dataForm4" class="form-horizontal" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="mb-2">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Tahun</label>
                        <div class="input-group input-group-merge">
                            <select id="tahun4" name="tahun4" class="select2 form-select form-select-sm" data-allow-clear="true" style="width:100%;">
                                @foreach ($datatahun as $data)
                                    <option value="{{ $data }}">{{ $data }}</option>
                                @endforeach
                            </select>                
                        </div>
                    </div>   
                    <div class="mb-2">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Jenis</label>
                        <div class="input-group input-group-merge">
                            <!-- <select id="jenis_kpi4" name="jenis_kpi4" class="select2 form-select" data-allow-clear="true">
                                <option value="">--Pilih Jenis--</option>
                                <option value="pusat">PUSAT</option>
                                <option value="cabang">CABANG</option>
                            </select> -->
                            <select id="kd_area4" name="kd_area4" class="select2 form-select form-select-sm" data-allow-clear="true" style="width:100%;">
                                <option value="">--Pilih Jenis--</option>
                                @foreach ($masteraream as $data)
                                    <option value="{{ $data->kd_area }}">{{ $data->nama_area }}</option>
                                @endforeach
                            </select>                
                        </div>
                    </div>   
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="saveBtn4" class="btn btn-danger"><i class="ri-refresh-line me-1"></i>Reset</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="ri-close-line me-1"></i>Cancel</button>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="ModalForm5" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="modelHeading5"></h6>  
                <button type="button" class="btn btn-close btn-sm" data-bs-dismiss="modal" aria-label="Close"></button>                  
            </div>
            <div class="modal-body">
                <form id="dataForm5" name="dataForm5" class="form-horizontal" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="mb-2">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Tahun</label>
                        <div class="input-group input-group-merge">
                            <select id="tahun5" name="tahun5" class="select2 form-select form-select-sm" data-allow-clear="true" style="width:100%;">
                                @foreach ($datatahun as $data)
                                    <option value="{{ $data }}">{{ $data }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>   
                    <div class="mb-2">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Jenis</label>
                        <div class="input-group input-group-merge">
                            <!-- <select id="jenis_kpi5" name="jenis_kpi5" class="select2 form-select" data-allow-clear="true">
                                <option value="">--Pilih Jenis--</option>
                                <option value="pusat">PUSAT</option>
                                <option value="cabang">CABANG</option>
                            </select> -->
                            <select id="kd_area5" name="kd_area5" class="select2 form-select form-select-sm" data-allow-clear="true" style="width:100%;">
                                <option value="">--Pilih Jenis--</option>
                                @foreach ($masteraream as $data)
                                    <option value="{{ $data->kd_area }}">{{ $data->nama_area }}</option>
                                @endforeach
                            </select>                
                        </div>
                    </div>   
                    <div class="mb-2">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Divisi</label>
                        <div class="input-group input-group-merge">
                            <select id="kd_divisi5" name="kd_divisi5" class="select2 form-select form-select-sm" data-allow-clear="true" style="width:100%;">
                                <option value="semua" selected>SEMUA DIVISI</option>
                                @foreach ($divisim as $data)
                                    <option value="{{ $data->kd_divisi }}">{{ $data->nama_divisi }}</option>
                                @endforeach
                            </select>                
                        </div>
                    </div>   
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="saveBtn5" class="btn btn-primary"><i class="ri-settings-3-line"></i>Proses</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="ri-close-line me-1"></i>Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="ModalForm6" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="modelHeading6"></h6>   
                <button type="button" class="btn btn-close btn-sm" data-bs-dismiss="modal" aria-label="Close"></button>                 
            </div>
            <div class="modal-body">
                <form id="dataForm6" name="dataForm6" class="form-horizontal" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="mb-2">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Tahun</label>
                        <div class="input-group input-group-merge">
                            <select id="tahun6" name="tahun6" class="select2 form-select form-select-sm" data-allow-clear="true" style="width:100%;">
                                @foreach ($datatahun as $data)
                                    <option value="{{ $data }}">{{ $data }}</option>
                                @endforeach
                            </select>                
                        </div>
                    </div>   
                    <div class="mb-2">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Jenis</label>
                        <div class="input-group input-group-merge">
                            <!-- <select id="jenis_kpi6" name="jenis_kpi6" class="select2 form-select" data-allow-clear="true">
                                <option value="">--Pilih Jenis--</option>
                                <option value="pusat">PUSAT</option>
                                <option value="cabang">CABANG</option>
                            </select> -->
                            <select id="kd_area6" name="kd_area6" class="select2 form-select form-select-sm" data-allow-clear="true" style="width:100%;">
                                <option value="">--Pilih Jenis--</option>
                                @foreach ($masteraream as $data)
                                    <option value="{{ $data->kd_area }}">{{ $data->nama_area }}</option>
                                @endforeach
                            </select>                
                        </div>
                    </div>   
                    <div class="mb-2">
                        <label class="form-label-custom text-muted fs-13" for="basic-icon-default-fullname">Divisi</label>
                        <div class="input-group input-group-merge">
                            <select id="kd_divisi6" name="kd_divisi6" class="select2 form-select form-select-sm" data-allow-clear="true" style="width:100%;">
                                <option value="semua" selected>SEMUA DIVISI</option>
                                @foreach ($divisim as $data)
                                    <option value="{{ $data->kd_divisi }}">{{ $data->nama_divisi }}</option>
                                @endforeach
                            </select>                
                        </div>
                    </div>   
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="saveBtn6" class="btn btn-danger"><i class="ri-refresh-line me-1"></i>Reset</button>
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
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        }
    });  

    $("#tahuncari").val("{{ date('Y') }}").trigger('change');

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
            url: "{{ route('datacascadingm') }}",
            data: function (d) {
                d.tahuncari = $('#tahuncari').val(),
                d.kd_areacari = $('#kd_areacari').val(),
                d.search = $('#tbl_list_filter input').val()
            }
        },            
        columns: [
            {data: 'aksi', name:'aksi',width:'50px', orderable: false, searchable: false},
            {data: 'tahun',name:'tahun',width:'50px',className: 'dt-center'},
            {data: 'jenis_kpi',name:'jenis_kpi',width:'100px',className: 'dt-center'},
            {data: 'nama_area',name:'nama_area',width:'120px',className: 'dt-center wrap'},
            {data: 'nama_divisi',name:'nama_divisi',width:'120px',className: 'dt-center wrap'},
            {data: 'uraian',name:'uraian',width:'250px',className: 'dt-left wrap'},
            {data: 'nama_level_kpi',name:'nama_level_kpi',width:'160px',className: 'dt-left wrap'},
            {data: 'prioritas',name:'prioritas',width:'80px',className: 'dt-center'},
            {data: 'type_target',name:'type_target',width:'80px',className: 'dt-center'},
            {data: 'polarisasi',name:'polarisasi',width:'80px',className: 'dt-center'},
            {data: 'ket',name:'ket',width:'50px',className: 'dt-center'},
            {data: 'satuan',name:'satuan',width:'50px',className: 'dt-center'},
            {data: 'jan',name:'jan',width:'50px',className: 'dt-center'},
            {data: 'feb',name:'feb',width:'50px',className: 'dt-center'},
            {data: 'mar',name:'mar',width:'50px',className: 'dt-center'},
            {data: 'apr',name:'apr',width:'50px',className: 'dt-center'},
            {data: 'mei',name:'mei',width:'50px',className: 'dt-center'},
            {data: 'jun',name:'jun',width:'50px',className: 'dt-center'},
            {data: 'jul',name:'jul',width:'50px',className: 'dt-center'},
            {data: 'agt',name:'agt',width:'50px',className: 'dt-center'},
            {data: 'sep',name:'sep',width:'50px',className: 'dt-center'},
            {data: 'okt',name:'okt',width:'50px',className: 'dt-center'},
            {data: 'nop',name:'nop',width:'50px',className: 'dt-center'},
            {data: 'des',name:'des',width:'50px',className: 'dt-center'},
        ],
        columnDefs: [
        {
            targets: [2], render: function (a, b, data, d) { 
                if(data.jenis_kpi!=="" && data.jenis_kpi!==null){
                    return data.jenis_kpi.toUpperCase();
                } else {
                    return '';
                }
            }  
        },
        {
            targets: [3], render: function (a, b, data, d) { 
                var a = '<div style="width:120px;">';
                a += '<span>'+data.nama_area+'</span>';
                a += '</div>';
                return a;
            }  
        },
        {
            targets: [4], render: function (a, b, data, d) { 
                var a = '<div style="width:120px;">';
                a += '<span>'+data.nama_divisi+'</span>';
                a += '</div>';
                return a;
            }  
        },
        {
            targets: [5], render: function (a, b, data, d) { 
                var a = '<div style="width:250px;">';
                a += '<span>'+data.uraian+'</span>';
                a += '</div>';
                return a;
            }  
        },
        {
            targets: [6], render: function (a, b, data, d) { 
                var a = '<div style="width:160px;">';
                a += '<span>'+data.nama_level_kpi+'</span>';
                a += '</div>';
                return a;
            }  
        },
        {
            targets: [7], render: function (a, b, data, d) { 
                if(data.prioritas!=="" && data.prioritas!==null && data.prioritas!==undefined){
                    return "Prioritas "+data.prioritas;
                } else {
                    return "";
                }
            }  
        },
        {
            targets: [9], render: function (a, b, data, d) { 
                if(data.polarisasi!=="" && data.polarisasi!==null && data.polarisasi!==undefined){
                    return data.polarisasi;
                } else {
                    return "";
                }
            }  
        },
        {
            targets: [10], render: function (a, b, data, d) { 
                var a = '<span class="badge bg-primary mb-1" style="width:100%;">Kuantitas</span>';
                a += '<br/><span class="badge bg-success mb-1" style="width:100%;">Kualitas</span>';
                a += '<br/><span class="badge bg-warning mb-1" style="width:100%;">Waktu</span>';
                a += '<br/><span class="badge bg-info mb-1" style="width:100%;">Prosentase</span>';
                return a;
            }  
        },
        {
            targets: [11], render: function (a, b, data, d) {
                if(data.satuan_kuantitas!=="" && data.satuan_kuantitas!==null){
                    var nilai1 = data.satuan_kuantitas;
                } else {
                    var nilai1 = "&nbsp;";
                }
                if(data.satuan_kualitas!=="" && data.satuan_kualitas!==null){
                    var nilai2 = data.satuan_kualitas;
                } else {
                    var nilai2 = "&nbsp;";
                }
                if(data.satuan_waktu!=="" && data.satuan_waktu!==null){
                    var nilai3 = data.satuan_waktu;
                } else {
                    var nilai3 = "&nbsp;";
                }
                var nilai4 = "Persen";
                var a = '<span class="badge bg-primary mb-1" style="width:100%;">'+nilai1+'</span>';
                a += '<br/><span class="badge bg-success mb-1" style="width:100%;">'+nilai2+'</span>';
                a += '<br/><span class="badge bg-warning mb-1" style="width:100%;">'+nilai3+'</span>';
                a += '<br/><span class="badge bg-info mb-1" style="width:100%;">'+nilai4+'</span>';
                return a;
            }  
        },
        {
            targets: [12], render: function (a, b, data, d) { 
                if(data.target01kn!=="" && data.target01kn!==null){
                    var nilai1 = data.target01kn;
                } else {
                    var nilai1 = "&nbsp;";
                }
                if(data.target01kl!=="" && data.target01kl!==null){
                    var nilai2 = data.target01kl;
                } else {
                    var nilai2 = "&nbsp;";
                }
                if(data.target01wk!=="" && data.target01wk!==null){
                    var nilai3 = data.target01wk;
                } else {
                    var nilai3 = "&nbsp;";
                }
                if(data.target01!=="" && data.target01!==null){
                    var nilai4 = data.target01;
                } else {
                    var nilai4 = "&nbsp;";
                }
                var a = '<span class="badge bg-primary mb-1" style="width:100%;">'+nilai1+'</span>';
                a += '<br/><span class="badge bg-success mb-1" style="width:100%;">'+nilai2+'</span>';
                a += '<br/><span class="badge bg-warning mb-1" style="width:100%;">'+nilai3+'</span>';
                a += '<br/><span class="badge bg-info mb-1" style="width:100%;">'+nilai4+'</span>';
                return a;
            }  
        },
        {
            targets: [13], render: function (a, b, data, d) { 
                if(data.target02kn!=="" && data.target02kn!==null){
                    var nilai1 = data.target02kn;
                } else {
                    var nilai1 = "&nbsp;";
                }
                if(data.target02kl!=="" && data.target02kl!==null){
                    var nilai2 = data.target02kl;
                } else {
                    var nilai2 = "&nbsp;";
                }
                if(data.target02wk!=="" && data.target02wk!==null){
                    var nilai3 = data.target02wk;
                } else {
                    var nilai3 = "&nbsp;";
                }
                if(data.target02!=="" && data.target02!==null){
                    var nilai4 = data.target02;
                } else {
                    var nilai4 = "&nbsp;";
                }
                var a = '<span class="badge bg-primary mb-1" style="width:100%;">'+nilai1+'</span>';
                a += '<br/><span class="badge bg-success mb-1" style="width:100%;">'+nilai2+'</span>';
                a += '<br/><span class="badge bg-warning mb-1" style="width:100%;">'+nilai3+'</span>';
                a += '<br/><span class="badge bg-info mb-1" style="width:100%;">'+nilai4+'</span>';
                return a;
            }  
        },
        {
            targets: [14], render: function (a, b, data, d) { 
                if(data.target03kn!=="" && data.target03kn!==null){
                    var nilai1 = data.target03kn;
                } else {
                    var nilai1 = "&nbsp;";
                }
                if(data.target03kl!=="" && data.target03kl!==null){
                    var nilai2 = data.target03kl;
                } else {
                    var nilai2 = "&nbsp;";
                }
                if(data.target03wk!=="" && data.target03wk!==null){
                    var nilai3 = data.target03wk;
                } else {
                    var nilai3 = "&nbsp;";
                }
                if(data.target03!=="" && data.target03!==null){
                    var nilai4 = data.target03;
                } else {
                    var nilai4 = "&nbsp;";
                }
                var a = '<span class="badge bg-primary mb-1" style="width:100%;">'+nilai1+'</span>';
                a += '<br/><span class="badge bg-success mb-1" style="width:100%;">'+nilai2+'</span>';
                a += '<br/><span class="badge bg-warning mb-1" style="width:100%;">'+nilai3+'</span>';
                a += '<br/><span class="badge bg-info mb-1" style="width:100%;">'+nilai4+'</span>';
                return a;
            }  
        },
        {
            targets: [15], render: function (a, b, data, d) { 
                if(data.target04kn!=="" && data.target04kn!==null){
                    var nilai1 = data.target04kn;
                } else {
                    var nilai1 = "&nbsp;";
                }
                if(data.target04kl!=="" && data.target04kl!==null){
                    var nilai2 = data.target04kl;
                } else {
                    var nilai2 = "&nbsp;";
                }
                if(data.target04wk!=="" && data.target04wk!==null){
                    var nilai3 = data.target04wk;
                } else {
                    var nilai3 = "&nbsp;";
                }
                if(data.target04!=="" && data.target04!==null){
                    var nilai4 = data.target04;
                } else {
                    var nilai4 = "&nbsp;";
                }
                var a = '<span class="badge bg-primary mb-1" style="width:100%;">'+nilai1+'</span>';
                a += '<br/><span class="badge bg-success mb-1" style="width:100%;">'+nilai2+'</span>';
                a += '<br/><span class="badge bg-warning mb-1" style="width:100%;">'+nilai3+'</span>';
                a += '<br/><span class="badge bg-info mb-1" style="width:100%;">'+nilai4+'</span>';
                return a;
            }  
        },
        {
            targets: [16], render: function (a, b, data, d) { 
                if(data.target05kn!=="" && data.target05kn!==null){
                    var nilai1 = data.target05kn;
                } else {
                    var nilai1 = "&nbsp;";
                }
                if(data.target05kl!=="" && data.target05kl!==null){
                    var nilai2 = data.target05kl;
                } else {
                    var nilai2 = "&nbsp;";
                }
                if(data.target05wk!=="" && data.target05wk!==null){
                    var nilai3 = data.target05wk;
                } else {
                    var nilai3 = "&nbsp;";
                }
                if(data.target05!=="" && data.target05!==null){
                    var nilai4 = data.target05;
                } else {
                    var nilai4 = "&nbsp;";
                }
                var a = '<span class="badge bg-primary mb-1" style="width:100%;">'+nilai1+'</span>';
                a += '<br/><span class="badge bg-success mb-1" style="width:100%;">'+nilai2+'</span>';
                a += '<br/><span class="badge bg-warning mb-1" style="width:100%;">'+nilai3+'</span>';
                a += '<br/><span class="badge bg-info mb-1" style="width:100%;">'+nilai4+'</span>';
                return a;
            }  
        },
        {
            targets: [17], render: function (a, b, data, d) { 
                if(data.target06kn!=="" && data.target06kn!==null){
                    var nilai1 = data.target06kn;
                } else {
                    var nilai1 = "&nbsp;";
                }
                if(data.target06kl!=="" && data.target06kl!==null){
                    var nilai2 = data.target06kl;
                } else {
                    var nilai2 = "&nbsp;";
                }
                if(data.target06wk!=="" && data.target06wk!==null){
                    var nilai3 = data.target06wk;
                } else {
                    var nilai3 = "&nbsp;";
                }
                if(data.target06!=="" && data.target06!==null){
                    var nilai4 = data.target06;
                } else {
                    var nilai4 = "&nbsp;";
                }
                var a = '<span class="badge bg-primary mb-1" style="width:100%;">'+nilai1+'</span>';
                a += '<br/><span class="badge bg-success mb-1" style="width:100%;">'+nilai2+'</span>';
                a += '<br/><span class="badge bg-warning mb-1" style="width:100%;">'+nilai3+'</span>';
                a += '<br/><span class="badge bg-info mb-1" style="width:100%;">'+nilai4+'</span>';
                return a;
            }  
        },
        {
            targets: [18], render: function (a, b, data, d) { 
                if(data.target07kn!=="" && data.target07kn!==null){
                    var nilai1 = data.target07kn;
                } else {
                    var nilai1 = "&nbsp;";
                }
                if(data.target07kl!=="" && data.target07kl!==null){
                    var nilai2 = data.target07kl;
                } else {
                    var nilai2 = "&nbsp;";
                }
                if(data.target07wk!=="" && data.target07wk!==null){
                    var nilai3 = data.target07wk;
                } else {
                    var nilai3 = "&nbsp;";
                }
                if(data.target07!=="" && data.target07!==null){
                    var nilai4 = data.target07;
                } else {
                    var nilai4 = "&nbsp;";
                }
                var a = '<span class="badge bg-primary mb-1" style="width:100%;">'+nilai1+'</span>';
                a += '<br/><span class="badge bg-success mb-1" style="width:100%;">'+nilai2+'</span>';
                a += '<br/><span class="badge bg-warning mb-1" style="width:100%;">'+nilai3+'</span>';
                a += '<br/><span class="badge bg-info mb-1" style="width:100%;">'+nilai4+'</span>';
                return a;
            }  
        },
        {
            targets: [19], render: function (a, b, data, d) { 
                if(data.target08kn!=="" && data.target08kn!==null){
                    var nilai1 = data.target08kn;
                } else {
                    var nilai1 = "&nbsp;";
                }
                if(data.target08kl!=="" && data.target08kl!==null){
                    var nilai2 = data.target08kl;
                } else {
                    var nilai2 = "&nbsp;";
                }
                if(data.target08wk!=="" && data.target08wk!==null){
                    var nilai3 = data.target08wk;
                } else {
                    var nilai3 = "&nbsp;";
                }
                if(data.target08!=="" && data.target08!==null){
                    var nilai4 = data.target08;
                } else {
                    var nilai4 = "&nbsp;";
                }
                var a = '<span class="badge bg-primary mb-1" style="width:100%;">'+nilai1+'</span>';
                a += '<br/><span class="badge bg-success mb-1" style="width:100%;">'+nilai2+'</span>';
                a += '<br/><span class="badge bg-warning mb-1" style="width:100%;">'+nilai3+'</span>';
                a += '<br/><span class="badge bg-info mb-1" style="width:100%;">'+nilai4+'</span>';
                return a;
            }  
        },
        {
            targets: [20], render: function (a, b, data, d) { 
                if(data.target09kn!=="" && data.target09kn!==null){
                    var nilai1 = data.target09kn;
                } else {
                    var nilai1 = "&nbsp;";
                }
                if(data.target09kl!=="" && data.target09kl!==null){
                    var nilai2 = data.target09kl;
                } else {
                    var nilai2 = "&nbsp;";
                }
                if(data.target09wk!=="" && data.target09wk!==null){
                    var nilai3 = data.target09wk;
                } else {
                    var nilai3 = "&nbsp;";
                }
                if(data.target09!=="" && data.target09!==null){
                    var nilai4 = data.target09;
                } else {
                    var nilai4 = "&nbsp;";
                }
                var a = '<span class="badge bg-primary mb-1" style="width:100%;">'+nilai1+'</span>';
                a += '<br/><span class="badge bg-success mb-1" style="width:100%;">'+nilai2+'</span>';
                a += '<br/><span class="badge bg-warning mb-1" style="width:100%;">'+nilai3+'</span>';
                a += '<br/><span class="badge bg-info mb-1" style="width:100%;">'+nilai4+'</span>';
                return a;
            }  
        },
        {
            targets: [21], render: function (a, b, data, d) { 
                if(data.target10kn!=="" && data.target10kn!==null){
                    var nilai1 = data.target10kn;
                } else {
                    var nilai1 = "&nbsp;";
                }
                if(data.target10kl!=="" && data.target10kl!==null){
                    var nilai2 = data.target10kl;
                } else {
                    var nilai2 = "&nbsp;";
                }
                if(data.target10wk!=="" && data.target10wk!==null){
                    var nilai3 = data.target10wk;
                } else {
                    var nilai3 = "&nbsp;";
                }
                if(data.target10!=="" && data.target10!==null){
                    var nilai4 = data.target10;
                } else {
                    var nilai4 = "&nbsp;";
                }
                var a = '<span class="badge bg-primary mb-1" style="width:100%;">'+nilai1+'</span>';
                a += '<br/><span class="badge bg-success mb-1" style="width:100%;">'+nilai2+'</span>';
                a += '<br/><span class="badge bg-warning mb-1" style="width:100%;">'+nilai3+'</span>';
                a += '<br/><span class="badge bg-info mb-1" style="width:100%;">'+nilai4+'</span>';
                return a;
            }  
        },
        {
            targets: [22], render: function (a, b, data, d) { 
                if(data.target11kn!=="" && data.target11kn!==null){
                    var nilai1 = data.target11kn;
                } else {
                    var nilai1 = "&nbsp;";
                }
                if(data.target11kl!=="" && data.target11kl!==null){
                    var nilai2 = data.target11kl;
                } else {
                    var nilai2 = "&nbsp;";
                }
                if(data.target11wk!=="" && data.target11wk!==null){
                    var nilai3 = data.target11wk;
                } else {
                    var nilai3 = "&nbsp;";
                }
                if(data.target11!=="" && data.target11!==null){
                    var nilai4 = data.target11;
                } else {
                    var nilai4 = "&nbsp;";
                }
                var a = '<span class="badge bg-primary mb-1" style="width:100%;">'+nilai1+'</span>';
                a += '<br/><span class="badge bg-success mb-1" style="width:100%;">'+nilai2+'</span>';
                a += '<br/><span class="badge bg-warning mb-1" style="width:100%;">'+nilai3+'</span>';
                a += '<br/><span class="badge bg-info mb-1" style="width:100%;">'+nilai4+'</span>';
                return a;
            }  
        },
        {
            targets: [23], render: function (a, b, data, d) { 
                if(data.target12kn!=="" && data.target12kn!==null){
                    var nilai1 = data.target12kn;
                } else {
                    var nilai1 = "&nbsp;";
                }
                if(data.target12kl!=="" && data.target12kl!==null){
                    var nilai2 = data.target12kl;
                } else {
                    var nilai2 = "&nbsp;";
                }
                if(data.target12wk!=="" && data.target12wk!==null){
                    var nilai3 = data.target12wk;
                } else {
                    var nilai3 = "&nbsp;";
                }
                if(data.target12!=="" && data.target12!==null){
                    var nilai4 = data.target12;
                } else {
                    var nilai4 = "&nbsp;";
                }
                var a = '<span class="badge bg-primary mb-1" style="width:100%;">'+nilai1+'</span>';
                a += '<br/><span class="badge bg-success mb-1" style="width:100%;">'+nilai2+'</span>';
                a += '<br/><span class="badge bg-warning mb-1" style="width:100%;">'+nilai3+'</span>';
                a += '<br/><span class="badge bg-info mb-1" style="width:100%;">'+nilai4+'</span>';
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
    document.querySelector('div.head-label_tbl_list').innerHTML = '<h5 class="card-title text-nowrap mb-0">Cascading KPI</h5>';

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
    
    $('.import_row').click(function () {
        $('#dataForm3').trigger("reset");
        $('#modelHeading3').html("Import Cascading");
        $('#ModalForm3').modal('show'); 
        var tahun = $("#tahuncari").val();
        var kd_area = $("#kd_areacari").val();
        $("#tahun3").val(tahun).trigger('change');
        if(kd_area!==""){
            $("#kd_area3").val(kd_area).trigger('change');
        }
    });        
    $('#saveBtn3').on("click", function(e) {
        e.preventDefault();
        let $btn = $(this);
        if ($btn.data('loading')) return;
        $btn.data('loading', true).prop('disabled', true);
        $btn.data('orig-html', $btn.html());
        var text = $btn.find('.btn-text').text() || $btn.text().trim();
        var spinner = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>';
        $btn.html(spinner + '<span class="btn-text">' + text + '</span>');

        var formElement = document.getElementById('dataForm3');
        var formData = new FormData(formElement); 
        var jenis_kpinya = $("#jenis_kpi3").val();
        $.ajax({
            data: formData,
            url: "{{ url('api/import-datacascadingm') }}",
            type: "POST",
            cache: false,
            contentType: false,
            processData: false,                
            success: function (data) { 
                $btn.html($btn.data('orig-html'));
                $btn.prop('disabled', false).data('loading', false); 
                if(data.status === "sukses"){  
                    Swal.fire('Sukses','Sukses simpan data.', 'success').then(() => {
                        $('#dataForm3').trigger("reset");
                        $('#ModalForm3').modal('hide');
                        table.ajax.reload(null, false);
                    });
                } else {
                    Swal.fire('Error', 'Gagal simpan data.', 'error');
                }
            },
            error: function (data) {
                // console.log('Error:', data.pesan);
                Swal.fire('Error', 'Gagal simpan data. '+data, 'error');
            }
        });                 
    });
    
    $('.reset_row').click(function () {
        $('#dataForm4').trigger("reset");
        $('#modelHeading4').html("Reset Cascading");
        $('#ModalForm4').modal('show'); 
        var tahun = $("#tahuncari").val();
        var kd_area = $("#kd_areacari").val();
        $("#tahun4").val(tahun).trigger('change');
        if(kd_area!==""){
            $("#kd_area4").val(kd_area).trigger('change');
        }
    });        
    $('#saveBtn4').on("click", function(e) {
        e.preventDefault();
        let $btn = $(this);
        if ($btn.data('loading')) return;
        $btn.data('loading', true).prop('disabled', true);
        $btn.data('orig-html', $btn.html());
        var text = $btn.find('.btn-text').text() || $btn.text().trim();
        var spinner = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>';
        $btn.html(spinner + '<span class="btn-text">' + text + '</span>');
        var formElement = document.getElementById('dataForm4');
        var formData = new FormData(formElement);    
        $.ajax({
            data: formData,
            url: "{{ url('api/reset-datacascadingm') }}",
            type: "POST",
            cache: false,
            contentType: false,
            processData: false,                
            success: function (data) {  
                $btn.html($btn.data('orig-html'));
                $btn.prop('disabled', false).data('loading', false);
                if(data.status === "sukses"){  
                    Swal.fire('Sukses','Sukses simpan data.', 'success').then(() => {
                        $('#dataForm4').trigger("reset");
                        $('#ModalForm4').modal('hide');
                        table.ajax.reload(null, false);
                    });
                } else {
                    Swal.fire('Error', 'Gagal simpan data.', 'error');
                }
            },
            error: function (data) {
                // console.log('Error:', data.pesan);
                Swal.fire('Error', 'Gagal simpan data. '+data, 'error');
            }
        });                 
    });
    
    $('.proses_mapping').click(function () {
        $('#dataForm5').trigger("reset");
        $('#modelHeading5').html("Proses Mapping KPI Pegawai");
        $('#ModalForm5').modal('show'); 
        var tahun = $("#tahuncari").val();
        var kd_area = $("#kd_areacari").val();
        $("#tahun5").val(tahun).trigger('change');
        if(kd_area!==""){
            $("#kd_area5").val(kd_area).trigger('change');
        }
    });        
    $('#saveBtn5').on("click", function(e) {
        e.preventDefault();
        let $btn = $(this);
        if ($btn.data('loading')) return;
        $btn.data('loading', true).prop('disabled', true);
        $btn.data('orig-html', $btn.html());
        var text = $btn.find('.btn-text').text() || $btn.text().trim();
        var spinner = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>';
        $btn.html(spinner + '<span class="btn-text">' + text + '</span>');
        
        var formElement = document.getElementById('dataForm5');
        var formData = new FormData(formElement);    
        $.ajax({
            data: formData,
            url: "{{ url('api/proses-datacascadingm') }}",
            type: "POST",
            cache: false,
            contentType: false,
            processData: false,                
            success: function (data) {  
                $btn.html($btn.data('orig-html'));
                $btn.prop('disabled', false).data('loading', false);
                if(data.status === "sukses"){  
                    Swal.fire('Sukses','Sukses mapping kpi pegawai.', 'success').then(() => {
                        $('#dataForm5').trigger("reset");
                        $('#ModalForm5').modal('hide');
                        table.ajax.reload(null, false);
                    });
                } else {
                    Swal.fire('Error', 'Gagal simpan data.', 'error');
                }
            },
            error: function (data) {
                // console.log('Error:', data.pesan);
                Swal.fire('Error', 'Gagal simpan data. '+data, 'error');
            }
        });                 
    });
    
    $('.reset_mapping').click(function () {
        $('#dataForm6').trigger("reset");
        $('#modelHeading6').html("Reset Mapping KPI Pegawai");
        $('#ModalForm6').modal('show'); 
        var tahun = $("#tahuncari").val();
        var kd_area = $("#kd_areacari").val();
        $("#tahun6").val(tahun).trigger('change');
        if(kd_area!==""){
            $("#kd_area6").val(kd_area).trigger('change');
        }
    });        
    $('#saveBtn6').on("click", function(e) {
        e.preventDefault();
        let $btn = $(this);
        if ($btn.data('loading')) return;
        $btn.data('loading', true).prop('disabled', true);
        $btn.data('orig-html', $btn.html());
        var text = $btn.find('.btn-text').text() || $btn.text().trim();
        var spinner = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>';
        $btn.html(spinner + '<span class="btn-text">' + text + '</span>');
        
        var formElement = document.getElementById('dataForm6');
        var formData = new FormData(formElement);    
        $.ajax({
            data: formData,
            url: "{{ url('api/resetmapping-datacascadingm') }}",
            type: "POST",
            cache: false,
            contentType: false,
            processData: false,                
            success: function (data) {  
                $btn.html($btn.data('orig-html'));
                $btn.prop('disabled', false).data('loading', false);
                if(data.status === "sukses"){ 
                    Swal.fire('Sukses','Sukses reset mapping kpi pegawai.', 'success').then(() => {
                        $('#dataForm6').trigger("reset");
                        $('#ModalForm6').modal('hide');
                        table.ajax.reload(null, false);
                    }); 
                } else {
                    Swal.fire('Error', 'Gagal simpan data.', 'error');
                }
            },
            error: function (data) {
                // console.log('Error:', data.pesan);
                Swal.fire('Error', 'Gagal simpan data. '+data, 'error');
            }
        });                 
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
        $.get("{{ route('datacascadingm') }}" +'/' + id, function (data) {
            $('#modelHeading').html("Update Data Cascading");
            $('#ModalForm').modal('show');
            $('#id').val(data.id);
            $('#tahun').val(data.tahun);
            $('#jenis_kpi').val(data.jenis_kpi);
            $('#nama_area').val(data.nama_area);
            $('#nama_divisi').val(data.nama_divisi);
            // $('#nama_level_kpi').val(data.nama_level_kpi);
            $('#uraian').val(data.uraian);
            $('#type_target').val(data.type_target);
            $('#polarisasi').val(data.polarisasi);
            $('#satuan_kuantitas').val(data.satuan_kuantitas);
            $('#satuan_kualitas').val(data.satuan_kualitas);
            $('#satuan_waktu').val(data.satuan_waktu);
            $('#target01kn').val(data.target01kn);
            $('#target01kl').val(data.target01kl);
            $('#target01wk').val(data.target01wk);
            $('#target01').val(data.target01);

            $('#target02kn').val(data.target02kn);
            $('#target02kl').val(data.target02kl);
            $('#target02wk').val(data.target02wk);
            $('#target02').val(data.target02);

            $('#target03kn').val(data.target03kn);
            $('#target03kl').val(data.target03kl);
            $('#target03wk').val(data.target03wk);
            $('#target03').val(data.target03);

            $('#target04kn').val(data.target04kn);
            $('#target04kl').val(data.target04kl);
            $('#target04wk').val(data.target04wk);
            $('#target04').val(data.target04);

            $('#target05kn').val(data.target05kn);
            $('#target05kl').val(data.target05kl);
            $('#target05wk').val(data.target05wk);
            $('#target05').val(data.target05);

            $('#target06kn').val(data.target06kn);
            $('#target06kl').val(data.target06kl);
            $('#target06wk').val(data.target06wk);
            $('#target06').val(data.target06);

            $('#target07kn').val(data.target07kn);
            $('#target07kl').val(data.target07kl);
            $('#target07wk').val(data.target07wk);
            $('#target07').val(data.target07);

            $('#target08kn').val(data.target08kn);
            $('#target08kl').val(data.target08kl);
            $('#target08wk').val(data.target08wk);
            $('#target08').val(data.target08);

            $('#target09kn').val(data.target09kn);
            $('#target09kl').val(data.target09kl);
            $('#target09wk').val(data.target09wk);
            $('#target09').val(data.target09);

            $('#target10kn').val(data.target10kn);
            $('#target10kl').val(data.target10kl);
            $('#target10wk').val(data.target10wk);
            $('#target10').val(data.target10);

            $('#target11kn').val(data.target11kn);
            $('#target11kl').val(data.target11kl);
            $('#target11wk').val(data.target11wk);
            $('#target11').val(data.target11);

            $('#target12kn').val(data.target12kn);
            $('#target12kl').val(data.target12kl);
            $('#target12wk').val(data.target12wk);
            $('#target12').val(data.target12);

            $("#level_kpi").html('');
            $.ajax({
                url: "{{url('api/fetch-level-datacascadingm')}}",
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
                        if(value.level_kpi===data.level_kpi){
                            $("#level_kpi").append('<option value="' + value.level_kpi + '" selected>' + value.nama_level_kpi + '</option>');
                        } else {
                            $("#level_kpi").append('<option value="' + value.level_kpi + '">' + value.nama_level_kpi + '</option>');
                        }
                    });
                }
            });

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
                    url: "{{url('api/hapus-datacascadingm')}}",
                    success: function (data) {
                        $btn.html($btn.data('orig-html'));
                        $btn.prop('disabled', false).data('loading', false);
                        Swal.fire('Sukses','Sukses hapus data.', 'success').then(() => table.ajax.reload(null, false));
                    },
                    error: function (data) {
                        // console.log('Error:', data);
                        Swal.fire('Error', 'Gagal hapus data: '+data, 'error');
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
            url: "{{ route('datacascadingm.store') }}",
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
                console.log('Error:', data);
            }
        });                 
    });
    
    $('#saveBtn2').on("click", function(e) {
        e.preventDefault();
        let $btn = $(this);
        if ($btn.data('loading')) return;
        $btn.data('loading', true).prop('disabled', true);
        $btn.data('orig-html', $btn.html());
        var text = $btn.find('.btn-text').text() || $btn.text().trim();
        var spinner = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>';
        $btn.html(spinner + '<span class="btn-text">' + text + '</span>');
        var datanya = $('#dataForm2').serialize();
        // alert(datanya);
        $.ajax({
            data: $('#dataForm2').serialize(),
            url: "{{url('api/update-user-datacascadingm')}}",
            type: "POST",
            dataType: 'json',
            success: function (data) { 
                $btn.html($btn.data('orig-html'));
                $btn.prop('disabled', false).data('loading', false);
                if(data.status === "sukses"){  
                    Swal.fire('Sukses','Sukses simpan data.', 'success').then(() => {
                        $('#dataForm2').trigger("reset");
                        $('#ModalForm2').modal('hide');
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
        $('#kd_area').on('select2:select', function () {
            var kd_area = this.value;
            // alert(kd_area);
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
