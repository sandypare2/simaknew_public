<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Exports\DatapencapaianExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\Datatables;
use App\models\Datapencapaianm;
use App\models\Mappingkpim;
use App\models\Mappingpegawaim;
use App\models\Masteraream;
use App\models\Kinerjapegawaim;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DatapencapaianmController extends Controller
{
    private $datapencapaianm;
    private $mappingkpim;
    private $mappingpegawaim;
    private $masteraream;
    public function __construct(Datapencapaianm $datapencapaianm, Mappingkpim $mappingkpim, Mappingpegawaim $mappingpegawaim, Masteraream $masteraream)
    {
        $this->datapencapaianm = $datapencapaianm;
        $this->mappingkpim = $mappingkpim;
        $this->mappingpegawaim = $mappingpegawaim;
        $this->masteraream = $masteraream;
    }

    public function index(Request $request)
    {
        $tahun_ini = intval(Carbon::now()->format('Y'));
        $tahun_awal = 2024;
        $datatahun = range($tahun_ini, $tahun_awal); // lebih ringkas

        $nipnya = Auth::user()->nip;
        $bulan_ini = Carbon::now()->format('m');
        $tanggal_ini = Carbon::now()->format('d');
        $batas_tgl_finalisasi = 10;

        if (intval($bulan_ini) == 1) {
            $tahun_periode = $tahun_ini - 1;
            $bulan_periode = "12";
        } else {
            $tahun_periode = $tahun_ini;
            $bulan_periode = str_pad(intval($bulan_ini) - 1, 2, "0", STR_PAD_LEFT);
        }

        if ($request->ajax()) {
            $tahuncari = $request->tahuncari;
            // $jenis_kpicari = $request->jenis_kpicari;

            // $data = Mappingkpim::selectRaw("
            //     kpi_pegawai.nip as nip,
            //     kpi_pegawai.tahun as tahun,
            //     MAX(b.nama) as nama,
            //     MAX(b.jabatan) as jabatan,
            //     MAX(b.kd_divisi) as kd_divisi,
            //     MAX(b.kd_area) as kd_area,
            //     IFNULL(MAX(c.nama_area), '') as nama_area,
            //     IFNULL(MAX(d.nama_level_kpi), '') as nama_level_kpi,
            //     IFNULL(MAX(e.nama_divisi), '') as nama_divisi
            // ")
            $data = Mappingkpim::selectRaw("
                kpi_pegawai.nip as nip,
                kpi_pegawai.tahun as tahun,
                MAX(b.nama) as nama,
                MAX(b.jabatan) as jabatan,
                MAX(b.kd_divisi) as kd_divisi,
                MAX(b.kd_area) as kd_area,
                IFNULL(MAX(c.nama_area), '') as nama_area,
                IFNULL(MAX(d.nama_level_kpi), '') as nama_level_kpi,
                IFNULL(MAX(e.nama_divisi), '') as nama_divisi,
                (
                    SELECT COUNT(DISTINCT dp2.nip)
                    FROM data_pegawai dp2
                    WHERE (dp2.approval = kpi_pegawai.nip OR dp2.finalisasi = kpi_pegawai.nip)
                    AND dp2.jenis_kpi = kpi_pegawai.jenis_kpi
                    AND dp2.aktif = '1'
                    AND dp2.payroll = '1'
                    AND dp2.aktif_simkp = '1'
                ) AS jumlah_bawahan
            ")
            ->leftJoin('data_pegawai as b', 'b.nip', '=', 'kpi_pegawai.nip')
            ->leftJoin('master_area as c', 'c.kd_area', '=', 'kpi_pegawai.kd_area')
            ->leftJoin('master_level_kpi as d', function($join) {
                $join->whereRaw("d.jenis_kpi = kpi_pegawai.jenis_kpi AND d.level_kpi = kpi_pegawai.level_kpi");
            })
            ->leftJoin('master_divisi as e', 'e.kd_divisi', '=', 'kpi_pegawai.kd_divisi')
            ->where('kpi_pegawai.tahun', $tahuncari)
            ->where('b.aktif', '1')
            ->where('b.payroll', '1')
            ->where('b.aktif_simkp', '1')
            ->groupBy('kpi_pegawai.nip', 'kpi_pegawai.tahun')
            // ->orderBy('nip', 'asc');
            ->orderBy('kpi_pegawai.level_kpi', 'desc')
            ->orderBy('kpi_pegawai.id', 'asc');

            return Datatables::eloquent($data)
                ->addIndexColumn()
                ->filter(function ($instance) use ($request) {
                    if ($jenis_kpicari = $request->get('jenis_kpicari')) {
                        if ($jenis_kpicari == "pusat") {
                            $instance->where('kpi_pegawai.jenis_kpi', $jenis_kpicari)
                            ->where('kpi_pegawai.level_kpi','>','2');
                        } else {
                            $instance->where('kpi_pegawai.jenis_kpi', $jenis_kpicari);
                        }
                    }
                    if ($search = $request->get('search')) {
                        $instance->where(function($q) use ($search) {
                            $q->where('kpi_pegawai.nip', $search)
                            ->orWhere('b.nama', 'like', "%{$search}%");
                        });
                    }
                })
                ->addColumn('download', '')
                ->addColumn('aksi', function ($data) {
                    $a = '<div class="acao text-center">
                        <a href="javascript:void(0)" 
                            data-tahun="'.$data->tahun.'" 
                            data-nip="'.$data->nip.'" 
                            data-nama="'.$data->nama.'" 
                            data-jabatan="'.$data->jabatan.'" 
                            data-jenis_kpi="'.$data->jenis_kpi.'" 
                            data-level_kpi="'.$data->level_kpi.'" 
                            data-nama_level_kpi="'.$data->nama_level_kpi.'" 
                            data-kd_divisi="'.$data->kd_divisi.'" 
                            title="Rincian KPI" 
                            class="detail_row">
                            <button type="button" class="btn btn-light-primary icon-btn-sm" style="margin-right:3px;">
                                <i class="ri-crosshair-2-line fs-14"></i>
                            </button>
                        </a>';
                    if($data->jumlah_bawahan>0){
                        $a .= '<a href="javascript:void(0)" 
                                data-nip="'.$data->nip.'" 
                                data-nama="'.$data->nama.'"
                                title="Hitung KPI" 
                                class="hitung_atasan">
                                <button type="button" class="btn btn-light-warning icon-btn-sm" style="margin-right:3px;">
                                    <i class="ri-calculator-line fs-14"></i></i>
                                </button>
                            </a>';
                    } else {
                        $a .= '<a href="javascript:void(0)">
                                <button type="button" class="btn btn-light-secondary icon-btn-sm" style="margin-right:3px;">
                                    <i class="ri-calculator-line fs-14"></i></i>
                                </button>
                            </a>';
                    }
                    $a .= '</div>';
                    return $a;
                })
                ->rawColumns(['aksi'])
                ->make(true);
        }

        $jenisKpi = DB::table('data_pegawai')
        ->select('jenis_kpi')
        ->where('jenis_kpi','<>','')
        ->whereNotNull('jenis_kpi')
        ->GroupBy('jenis_kpi')
        ->get();

        return view('admin.datapencapaianm.index', [
            'masteraream' => $this->masteraream->getAllData(),
        ], compact('bulan_ini','tanggal_ini','batas_tgl_finalisasi','tahun_periode','bulan_periode','datatahun','jenisKpi'));
    }

    public function store(Request $request)
    {
        function nanToZero($value){
            if (is_numeric($value) && is_nan($value)) {
                return 0;
            }            
            return $value;
        }        

        $tahun_ini = intval(Carbon::now()->format('Y'));
        $bulan_ini = Carbon::now()->format('m');
        if(intval($bulan_ini)<=6){
            $tahun_periode = $tahun_ini-1;
            $bulan_periode = "12";
        } else {
            $tahun_periode = $tahun_ini;
            $bulan_periode = str_pad(intval($bulan_ini)-1,2,"0",STR_PAD_LEFT);
        }

        // $nipnya = Auth::user()->nip;
        $id = intval($request->id2);
        $kode_kpi = $request->kode_kpi2;
        $satuan_kuantitas = $request->satuan_kuantitas2;
        $satuan_kualitas = $request->satuan_kualitas2;
        $satuan_waktu = $request->satuan_waktu2;
        $tahun = $request->tahun2;
        $nama_target = $request->nama_target2;
        $realisasibkn = $request->realisasibkn2;
        $realisasibkl = $request->realisasibkl2;
        $realisasibwk = $request->realisasibwk2; 
        
        $realisasibkn = str_replace(",",".",$realisasibkn);
        $realisasibkl = str_replace(",",".",$realisasibkl);
        $realisasibwk = str_replace(",",".",$realisasibwk);
              
        if(is_null($realisasibkn)){
            $realisasibkn = "";
        }
        if(is_null($realisasibkl)){
            $realisasibkl = "";
        }
        if(is_null($realisasibwk)){
            $realisasibwk = "";
        }
        // dd($realisasibkn." ".$realisasibkl." ".$realisasibwk);
        $bulan = substr($nama_target,-2); 
        // dd($nama_target);

        $row1 = DB::table('simkppcn.finalisasi_Kpi')
        ->selectRaw("
            finalisasi_Kpi.kode_cascading as kode_cascading2,
            b.*
        ")
        ->leftJoin('simkppcn.cascading_kpi as b','b.kode_cascading','=','finalisasi_Kpi.kode_cascading')
        ->whereRaw("finalisasi_Kpi.kode_kpi='$kode_kpi'")
        ->first();
        $kode_cascading = $row1->kode_cascading;
        if($nama_target=="target01"){
            $target = $row1->target01;
            $targetkn = $row1->target01kn;
            $targetkl = $row1->target01kl;
            $targetwk = $row1->target01wk;
        } else if($nama_target=="target02"){
            $target = $row1->target02;
            $targetkn = $row1->target02kn;
            $targetkl = $row1->target02kl;
            $targetwk = $row1->target02wk;
        } else if($nama_target=="target03"){
            $target = $row1->target03;
            $targetkn = $row1->target03kn;
            $targetkl = $row1->target03kl;
            $targetwk = $row1->target03wk;
        } else if($nama_target=="target04"){
            $target = $row1->target04;
            $targetkn = $row1->target04kn;
            $targetkl = $row1->target04kl;
            $targetwk = $row1->target04wk;
        } else if($nama_target=="target05"){
            $target = $row1->target05;
            $targetkn = $row1->target05kn;
            $targetkl = $row1->target05kl;
            $targetwk = $row1->target05wk;
        } else if($nama_target=="target06"){
            $target = $row1->target06;
            $targetkn = $row1->target06kn;
            $targetkl = $row1->target06kl;
            $targetwk = $row1->target06wk;
        } else if($nama_target=="target07"){
            $target = $row1->target07;
            $targetkn = $row1->target07kn;
            $targetkl = $row1->target07kl;
            $targetwk = $row1->target07wk;
        } else if($nama_target=="target08"){
            $target = $row1->target08;
            $targetkn = $row1->target08kn;
            $targetkl = $row1->target08kl;
            $targetwk = $row1->target08wk;
        } else if($nama_target=="target09"){
            $target = $row1->target09;
            $targetkn = $row1->target09kn;
            $targetkl = $row1->target09kl;
            $targetwk = $row1->target09wk;
        } else if($nama_target=="target10"){
            $target = $row1->target10;
            $targetkn = $row1->target10kn;
            $targetkl = $row1->target10kl;
            $targetwk = $row1->target10wk;
        } else if($nama_target=="target11"){
            $target = $row1->target11;
            $targetkn = $row1->target11kn;
            $targetkl = $row1->target11kl;
            $targetwk = $row1->target11wk;
        } else if($nama_target=="target12"){
            $target = $row1->target12;
            $targetkn = $row1->target12kn;
            $targetkl = $row1->target12kl;
            $targetwk = $row1->target12wk;
        } else {
            $target = "";
            $targetkn = "";
            $targetkl = "";
            $targetwk = "";
        }
        $target = trim(str_replace(",",".",$target));
        $targetkn = trim(str_replace(",",".",$targetkn));
        $targetkl = trim(str_replace(",",".",$targetkl));
        $targetwk = trim(str_replace(",",".",$targetwk));

        $base_tanggal = $tahun."-".substr($nama_target,-2)."-01";

        // Perhitungan Kuantitas
        if(strpos(strtolower($satuan_kuantitas), "hari") !== false || strpos(strtolower($satuan_kuantitas), "tanggal") !== false){   
            $nilaibkn = round(((2-($realisasibkn/$targetkn))*100),2)."%";
        } else {
            if($realisasibkn!=""){
                $nilaibkn = round(((floatval($realisasibkn)/floatval($targetkn))*100),2)."%";
            } else {
                $nilaibkn = "";
            }
        }
        if(floatval($nilaibkn)<=0 && $nilaibkn!=""){
            $nilaibkn = "0%";
        } else if(floatval($nilaibkn)>130){
            $nilaibkn = "130%";
        } else {
            $nilaibkn = $nilaibkn;
        }

        // Perhitungan Kualitas
        if(strpos(strtolower($satuan_kualitas), "hari") !== false || strpos(strtolower($satuan_kualitas), "tanggal") !== false){   
            $nilaibkl = round(((2-($realisasibkl/$targetkl))*100),2)."%";
        } else {
            if($realisasibkl!=""){
                $nilaibkl = round(((floatval($realisasibkl)/floatval($targetkl))*100),2)."%";
            } else {
                $nilaibkl = "";
            }
        }
        if(floatval($nilaibkl)<=0 && $nilaibkl!=""){
            $nilaibkl = "0%";
        } else if(floatval($nilaibkl)>130){
            $nilaibkl = "130%";
        } else {
            $nilaibkl = $nilaibkl;
        }        

        // Perhitungan Waktu
        if(strpos(strtolower($satuan_waktu), "hari") !== false || strpos(strtolower($satuan_waktu), "tanggal") !== false){   
            $nilaibwk = round(((2-(floatval($realisasibwk)/floatval($targetwk)))*100),2)."%";
        } else {
            if($realisasibwk!=""){
                $nilaibwk = round(((floatval($realisasibwk)/floatval($targetwk))*100),2)."%";
            } else {
                $nilaibwk = "";
            }

        }
        // dd($nilaiwk);
        if(floatval($nilaibwk)<=0 && $nilaibwk!=""){
            $nilaibwk = "0%";
        } else if(floatval($nilaibwk)>130){
            $nilaibwk = "130%";
        } else {
            $nilaibwk = $nilaibwk;
        }  
        
        $jumlahdata = 0;
        if(intval($targetkn)>0){
            $jumlahdata++;
            $nilaibkn2 = $nilaibkn;
        } else {
            $nilaibkn2 = 0;
        }
        if(intval($targetkl)>0){
            $jumlahdata++;
            $nilaibkl2 = $nilaibkl;
        } else {
            $nilaibkl2 = 0;
        }
        if(intval($targetwk)>0){
            $jumlahdata++;
            $nilaibwk2 = $nilaibwk;
        } else {
            $nilaibwk2 = 0;
        }
        
        $total_nilai = floatval($nilaibkn2)+floatval($nilaibkl2)+floatval($nilaibwk2);
        if($total_nilai>0){
            $realisasib = round($total_nilai/$jumlahdata,2);
        } else {
            $realisasib = 100;
        }
        if($realisasib>110){
            $realisasib = 110;
        }
        $realisasib .= "%";        

        try {
            $mappingkpim = Mappingkpim::where('kode_kpi', $kode_kpi);
            $datapencapaianm = Datapencapaianm::where('kode_kpi', $kode_kpi);
            $updateData = [];
            $updateData2 = [];
            if($nama_target=="target01"){
                $updateData2['realisasi01bkn'] = $realisasibkn;
                $updateData2['realisasi01bkl'] = $realisasibkl;
                $updateData2['realisasi01bwk'] = $realisasibwk;
                $updateData2['nilai01bkn'] = $nilaibkn;
                $updateData2['nilai01bkl'] = $nilaibkl;
                $updateData2['nilai01bwk'] = $nilaibwk;
                $updateData2['realisasi01b'] = $realisasib;
            }
            if($nama_target=="target02"){
                $updateData2['realisasi02bkn'] = $realisasibkn;
                $updateData2['realisasi02bkl'] = $realisasibkl;
                $updateData2['realisasi02bwk'] = $realisasibwk;
                $updateData2['nilai02bkn'] = $nilaibkn;
                $updateData2['nilai02bkl'] = $nilaibkl;
                $updateData2['nilai02bwk'] = $nilaibwk;
                $updateData2['realisasi02b'] = $realisasib;
            }
            if($nama_target=="target03"){
                $updateData2['realisasi03bkn'] = $realisasibkn;
                $updateData2['realisasi03bkl'] = $realisasibkl;
                $updateData2['realisasi03bwk'] = $realisasibwk;
                $updateData2['nilai03bkn'] = $nilaibkn;
                $updateData2['nilai03bkl'] = $nilaibkl;
                $updateData2['nilai03bwk'] = $nilaibwk;
                $updateData2['realisasi03b'] = $realisasib;
            }
            if($nama_target=="target04"){
                $updateData2['realisasi04bkn'] = $realisasibkn;
                $updateData2['realisasi04bkl'] = $realisasibkl;
                $updateData2['realisasi04bwk'] = $realisasibwk;
                $updateData2['nilai04bkn'] = $nilaibkn;
                $updateData2['nilai04bkl'] = $nilaibkl;
                $updateData2['nilai04bwk'] = $nilaibwk;
                $updateData2['realisasi04b'] = $realisasib;
            }
            if($nama_target=="target05"){
                $updateData2['realisasi05bkn'] = $realisasibkn;
                $updateData2['realisasi05bkl'] = $realisasibkl;
                $updateData2['realisasi05bwk'] = $realisasibwk;
                $updateData2['nilai05bkn'] = $nilaibkn;
                $updateData2['nilai05bkl'] = $nilaibkl;
                $updateData2['nilai05bwk'] = $nilaibwk;
                $updateData2['realisasi05b'] = $realisasib;
            }
            if($nama_target=="target06"){
                $updateData2['realisasi06bkn'] = $realisasibkn;
                $updateData2['realisasi06bkl'] = $realisasibkl;
                $updateData2['realisasi06bwk'] = $realisasibwk;
                $updateData2['nilai06bkn'] = $nilaibkn;
                $updateData2['nilai06bkl'] = $nilaibkl;
                $updateData2['nilai06bwk'] = $nilaibwk;
                $updateData2['realisasi06b'] = $realisasib;
            }
            if($nama_target=="target07"){
                $updateData2['realisasi07bkn'] = $realisasibkn;
                $updateData2['realisasi07bkl'] = $realisasibkl;
                $updateData2['realisasi07bwk'] = $realisasibwk;
                $updateData2['nilai07bkn'] = $nilaibkn;
                $updateData2['nilai07bkl'] = $nilaibkl;
                $updateData2['nilai07bwk'] = $nilaibwk;
                $updateData2['realisasi07b'] = $realisasib;
            }
            if($nama_target=="target08"){
                $updateData2['realisasi08bkn'] = $realisasibkn;
                $updateData2['realisasi08bkl'] = $realisasibkl;
                $updateData2['realisasi08bwk'] = $realisasibwk;
                $updateData2['nilai08bkn'] = $nilaibkn;
                $updateData2['nilai08bkl'] = $nilaibkl;
                $updateData2['nilai08bwk'] = $nilaibwk;
                $updateData2['realisasi08b'] = $realisasib;
            }
            if($nama_target=="target09"){
                $updateData2['realisasi09bkn'] = $realisasibkn;
                $updateData2['realisasi09bkl'] = $realisasibkl;
                $updateData2['realisasi09bwk'] = $realisasibwk;
                $updateData2['nilai09bkn'] = $nilaibkn;
                $updateData2['nilai09bkl'] = $nilaibkl;
                $updateData2['nilai09bwk'] = $nilaibwk;
                $updateData2['realisasi09b'] = $realisasib;
            }
            if($nama_target=="target10"){
                $updateData2['realisasi10bkn'] = $realisasibkn;
                $updateData2['realisasi10bkl'] = $realisasibkl;
                $updateData2['realisasi10bwk'] = $realisasibwk;
                $updateData2['nilai10bkn'] = $nilaibkn;
                $updateData2['nilai10bkl'] = $nilaibkl;
                $updateData2['nilai10bwk'] = $nilaibwk;
                $updateData2['realisasi10b'] = $realisasib;
            }
            if($nama_target=="target11"){
                $updateData2['realisasi11bkn'] = $realisasibkn;
                $updateData2['realisasi11bkl'] = $realisasibkl;
                $updateData2['realisasi11bwk'] = $realisasibwk;
                $updateData2['nilai11bkn'] = $nilaibkn;
                $updateData2['nilai11bkl'] = $nilaibkl;
                $updateData2['nilai11bwk'] = $nilaibwk;
                $updateData2['realisasi11b'] = $realisasib;
            }
            if($nama_target=="target12"){
                $updateData2['realisasi12bkn'] = $realisasibkn;
                $updateData2['realisasi12bkl'] = $realisasibkl;
                $updateData2['realisasi12bwk'] = $realisasibwk;
                $updateData2['nilai12bkn'] = $nilaibkn;
                $updateData2['nilai12bkl'] = $nilaibkl;
                $updateData2['nilai12bwk'] = $nilaibwk;
                $updateData2['realisasi12b'] = $realisasib;
            }
            // $mappingkpim->update($updateData);  
            $datapencapaianm->update($updateData2);  
            
            $row31 = DB::table('simkppcn.cascading_kpi')
            ->selectRaw("*")
            ->whereRaw("kode_cascading='$kode_cascading'")
            ->first();
            $type_target = $row31->type_target;
            $target01kn = $row31->target01kn;
            $target01kl = $row31->target01kl;
            $target01wk = $row31->target01wk;
            $target01 = $row31->target01;
            $target02kn = $row31->target02kn;
            $target02kl = $row31->target02kl;
            $target02wk = $row31->target02wk;
            $target02 = $row31->target02;
            $target03kn = $row31->target03kn;
            $target03kl = $row31->target03kl;
            $target03wk = $row31->target03wk;
            $target03 = $row31->target03;
            $target04kn = $row31->target04kn;
            $target04kl = $row31->target04kl;
            $target04wk = $row31->target04wk;
            $target04 = $row31->target04;
            $target05kn = $row31->target05kn;
            $target05kl = $row31->target05kl;
            $target05wk = $row31->target05wk;
            $target05 = $row31->target05;
            $target06kn = $row31->target06kn;
            $target06kl = $row31->target06kl;
            $target06wk = $row31->target06wk;
            $target06 = $row31->target06;
            $target07kn = $row31->target07kn;
            $target07kl = $row31->target07kl;
            $target07wk = $row31->target07wk;
            $target07 = $row31->target07;
            $target08kn = $row31->target08kn;
            $target08kl = $row31->target08kl;
            $target08wk = $row31->target08wk;
            $target08 = $row31->target08;
            $target09kn = $row31->target09kn;
            $target09kl = $row31->target09kl;
            $target09wk = $row31->target09wk;
            $target09 = $row31->target09;
            $target10kn = $row31->target10kn;
            $target10kl = $row31->target10kl;
            $target10wk = $row31->target10wk;
            $target10 = $row31->target10;
            $target11kn = $row31->target11kn;
            $target11kl = $row31->target11kl;
            $target11wk = $row31->target11wk;
            $target11 = $row31->target11;
            $target12kn = $row31->target12kn;
            $target12kl = $row31->target12kl;
            $target12wk = $row31->target12wk;
            $target12 = $row31->target12;

            $total_targetkn = floatval($target01kn)+floatval($target02kn)+floatval($target03kn)+floatval($target04kn)+floatval($target05kn)+floatval($target06kn)+floatval($target07kn)+floatval($target08kn)+floatval($target09kn)+floatval($target10kn)+floatval($target11kn)+floatval($target12kn);
            $total_targetkl = floatval($target01kl)+floatval($target02kl)+floatval($target03kl)+floatval($target04kl)+floatval($target05kl)+floatval($target06kl)+floatval($target07kl)+floatval($target08kl)+floatval($target09kl)+floatval($target10kl)+floatval($target11kl)+floatval($target12kl);
            $total_targetwk = floatval($target01wk)+floatval($target02wk)+floatval($target03wk)+floatval($target04wk)+floatval($target05wk)+floatval($target06wk)+floatval($target07wk)+floatval($target08wk)+floatval($target09wk)+floatval($target10wk)+floatval($target11wk)+floatval($target12wk);
            
            $row32 = DB::table('simkppcn.kpi_pegawai')
            ->selectRaw("*")
            ->whereRaw("kode_kpi='$kode_kpi'")
            ->first();
            $realisasi01kn = $row32->realisasi01kn;
            $realisasi01kl = $row32->realisasi01kl;
            $realisasi01wk = $row32->realisasi01wk;
            $nilai01kn = $row32->nilai01kn;
            $nilai01kl = $row32->nilai01kl;
            $nilai01wk = $row32->nilai01wk;
            $realisasi01 = $row32->realisasi01;
            $realisasi02kn = $row32->realisasi02kn;
            $realisasi02kl = $row32->realisasi02kl;
            $realisasi02wk = $row32->realisasi02wk;
            $nilai02kn = $row32->nilai02kn;
            $nilai02kl = $row32->nilai02kl;
            $nilai02wk = $row32->nilai02wk;
            $realisasi02 = $row32->realisasi02;
            $realisasi03kn = $row32->realisasi03kn;
            $realisasi03kl = $row32->realisasi03kl;
            $realisasi03wk = $row32->realisasi03wk;
            $nilai03kn = $row32->nilai03kn;
            $nilai03kl = $row32->nilai03kl;
            $nilai03wk = $row32->nilai03wk;
            $realisasi03 = $row32->realisasi03;
            $realisasi04kn = $row32->realisasi04kn;
            $realisasi04kl = $row32->realisasi04kl;
            $realisasi04wk = $row32->realisasi04wk;
            $nilai04kn = $row32->nilai04kn;
            $nilai04kl = $row32->nilai04kl;
            $nilai04wk = $row32->nilai04wk;
            $realisasi04 = $row32->realisasi04;
            $realisasi05kn = $row32->realisasi05kn;
            $realisasi05kl = $row32->realisasi05kl;
            $realisasi05wk = $row32->realisasi05wk;
            $nilai05kn = $row32->nilai05kn;
            $nilai05kl = $row32->nilai05kl;
            $nilai05wk = $row32->nilai05wk;
            $realisasi05 = $row32->realisasi05;
            $realisasi06kn = $row32->realisasi06kn;
            $realisasi06kl = $row32->realisasi06kl;
            $realisasi06wk = $row32->realisasi06wk;
            $nilai06kn = $row32->nilai06kn;
            $nilai06kl = $row32->nilai06kl;
            $nilai06wk = $row32->nilai06wk;
            $realisasi06 = $row32->realisasi06;
            $realisasi07kn = $row32->realisasi07kn;
            $realisasi07kl = $row32->realisasi07kl;
            $realisasi07wk = $row32->realisasi07wk;
            $nilai07kn = $row32->nilai07kn;
            $nilai07kl = $row32->nilai07kl;
            $nilai07wk = $row32->nilai07wk;
            $realisasi07 = $row32->realisasi07;
            $realisasi08kn = $row32->realisasi08kn;
            $realisasi08kl = $row32->realisasi08kl;
            $realisasi08wk = $row32->realisasi08wk;
            $nilai08kn = $row32->nilai08kn;
            $nilai08kl = $row32->nilai08kl;
            $nilai08wk = $row32->nilai08wk;
            $realisasi08 = $row32->realisasi08;
            $realisasi09kn = $row32->realisasi09kn;
            $realisasi09kl = $row32->realisasi09kl;
            $realisasi09wk = $row32->realisasi09wk;
            $nilai09kn = $row32->nilai09kn;
            $nilai09kl = $row32->nilai09kl;
            $nilai09wk = $row32->nilai09wk;
            $realisasi09 = $row32->realisasi09;
            $realisasi10kn = $row32->realisasi10kn;
            $realisasi10kl = $row32->realisasi10kl;
            $realisasi10wk = $row32->realisasi10wk;
            $nilai10kn = $row32->nilai10kn;
            $nilai10kl = $row32->nilai10kl;
            $nilai10wk = $row32->nilai10wk;
            $realisasi10 = $row32->realisasi10;
            $realisasi11kn = $row32->realisasi11kn;
            $realisasi11kl = $row32->realisasi11kl;
            $realisasi11wk = $row32->realisasi11wk;
            $nilai11kn = $row32->nilai11kn;
            $nilai11kl = $row32->nilai11kl;
            $nilai11wk = $row32->nilai11wk;
            $realisasi11 = $row32->realisasi11;
            $realisasi12kn = $row32->realisasi12kn;
            $realisasi12kl = $row32->realisasi12kl;
            $realisasi12wk = $row32->realisasi12wk;
            $nilai12kn = $row32->nilai12kn;
            $nilai12kl = $row32->nilai12kl;
            $nilai12wk = $row32->nilai12wk;
            $realisasi12 = $row32->realisasi12;
            
            $row33 = DB::table('simkppcn.finalisasi_kpi')
            ->selectRaw("*")
            ->whereRaw("kode_kpi='$kode_kpi'")
            ->first();
            // dd($row3);
            $realisasi01bkn = $row33->realisasi01bkn;
            $realisasi01bkl = $row33->realisasi01bkl;
            $realisasi01bwk = $row33->realisasi01bwk;
            $nilai01bkn = $row33->nilai01bkn;
            $nilai01bkl = $row33->nilai01bkl;
            $nilai01bwk = $row33->nilai01bwk;
            $realisasi01b = $row33->realisasi01b;
            $realisasi02bkn = $row33->realisasi02bkn;
            $realisasi02bkl = $row33->realisasi02bkl;
            $realisasi02bwk = $row33->realisasi02bwk;
            $nilai02bkn = $row33->nilai02bkn;
            $nilai02bkl = $row33->nilai02bkl;
            $nilai02bwk = $row33->nilai02bwk;
            $realisasi02b = $row33->realisasi02b;
            $realisasi03bkn = $row33->realisasi03bkn;
            $realisasi03bkl = $row33->realisasi03bkl;
            $realisasi03bwk = $row33->realisasi03bwk;
            $nilai03bkn = $row33->nilai03bkn;
            $nilai03bkl = $row33->nilai03bkl;
            $nilai03bwk = $row33->nilai03bwk;
            $realisasi03b = $row33->realisasi03b;
            $realisasi04bkn = $row33->realisasi04bkn;
            $realisasi04bkl = $row33->realisasi04bkl;
            $realisasi04bwk = $row33->realisasi04bwk;
            $nilai04bkn = $row33->nilai04bkn;
            $nilai04bkl = $row33->nilai04bkl;
            $nilai04bwk = $row33->nilai04bwk;
            $realisasi04b = $row33->realisasi04b;
            $realisasi05bkn = $row33->realisasi05bkn;
            $realisasi05bkl = $row33->realisasi05bkl;
            $realisasi05bwk = $row33->realisasi05bwk;
            $nilai05bkn = $row33->nilai05bkn;
            $nilai05bkl = $row33->nilai05bkl;
            $nilai05bwk = $row33->nilai05bwk;
            $realisasi05b = $row33->realisasi05b;
            $realisasi06bkn = $row33->realisasi06bkn;
            $realisasi06bkl = $row33->realisasi06bkl;
            $realisasi06bwk = $row33->realisasi06bwk;
            $nilai06bkn = $row33->nilai06bkn;
            $nilai06bkl = $row33->nilai06bkl;
            $nilai06bwk = $row33->nilai06bwk;
            $realisasi06b = $row33->realisasi06b;
            $realisasi07bkn = $row33->realisasi07bkn;
            $realisasi07bkl = $row33->realisasi07bkl;
            $realisasi07bwk = $row33->realisasi07bwk;
            $nilai07bkn = $row33->nilai07bkn;
            $nilai07bkl = $row33->nilai07bkl;
            $nilai07bwk = $row33->nilai07bwk;
            $realisasi07b = $row33->realisasi07b;
            $realisasi08bkn = $row33->realisasi08bkn;
            $realisasi08bkl = $row33->realisasi08bkl;
            $realisasi08bwk = $row33->realisasi08bwk;
            $nilai08bkn = $row33->nilai08bkn;
            $nilai08bkl = $row33->nilai08bkl;
            $nilai08bwk = $row33->nilai08bwk;
            $realisasi08b = $row33->realisasi08b;
            $realisasi09bkn = $row33->realisasi09bkn;
            $realisasi09bkl = $row33->realisasi09bkl;
            $realisasi09bwk = $row33->realisasi09bwk;
            $nilai09bkn = $row33->nilai09bkn;
            $nilai09bkl = $row33->nilai09bkl;
            $nilai09bwk = $row33->nilai09bwk;
            $realisasi09b = $row33->realisasi09b;
            $realisasi10bkn = $row33->realisasi10bkn;
            $realisasi10bkl = $row33->realisasi10bkl;
            $realisasi10bwk = $row33->realisasi10bwk;
            $nilai10bkn = $row33->nilai10bkn;
            $nilai10bkl = $row33->nilai10bkl;
            $nilai10bwk = $row33->nilai10bwk;
            $realisasi10b = $row33->realisasi10b;
            $realisasi11bkn = $row33->realisasi11bkn;
            $realisasi11bkl = $row33->realisasi11bkl;
            $realisasi11bwk = $row33->realisasi11bwk;
            $nilai11bkn = $row33->nilai11bkn;
            $nilai11bkl = $row33->nilai11bkl;
            $nilai11bwk = $row33->nilai11bwk;
            $realisasi11b = $row33->realisasi11b;
            $realisasi12bkn = $row33->realisasi12bkn;
            $realisasi12bkl = $row33->realisasi12bkl;
            $realisasi12bwk = $row33->realisasi12bwk;
            $nilai12bkn = $row33->nilai12bkn;
            $nilai12bkl = $row33->nilai12bkl;
            $nilai12bwk = $row33->nilai12bwk;
            $realisasi12b = $row33->realisasi12b; 

            // dd(floatval($realisasi01)."-".floatval($realisasi02)."-".floatval($realisasi03)."-".floatval($realisasi04)."-".floatval($realisasi05)."-".floatval($realisasi06));
            if(substr($nama_target,-2)<="06"){
                if(strtolower($type_target)=="akumulatif"){
                    $jumlah_datakn = 0;
                    $jumlah_datakl = 0;
                    $jumlah_datawk = 0;
                    if($target01kn!="" && $target01kn!="null"){
                        $jumlah_datakn++;
                    }
                    if($target01kl!="" && $target01kl!="null"){
                        $jumlah_datakl++;
                    }
                    if($target01wk!="" && $target01wk!="null"){
                        $jumlah_datawk++;
                    }
                    if($target02kn!="" && $target02kn!="null"){
                        $jumlah_datakn++;
                    }
                    if($target02kl!="" && $target02kl!="null"){
                        $jumlah_datakl++;
                    }
                    if($target02wk!="" && $target02wk!="null"){
                        $jumlah_datawk++;
                    }
                    
                    if($target03kn!="" && $target03kn!="null"){
                        $jumlah_datakn++;
                    }
                    if($target03kl!="" && $target03kl!="null"){
                        $jumlah_datakl++;
                    }
                    if($target03wk!="" && $target03wk!="null"){
                        $jumlah_datawk++;
                    }
                    
                    if($target04kn!="" && $target04kn!="null"){
                        $jumlah_datakn++;
                    }
                    if($target04kl!="" && $target04kl!="null"){
                        $jumlah_datakl++;
                    }
                    if($target04wk!="" && $target04wk!="null"){
                        $jumlah_datawk++;
                    }
                    
                    if($target05kn!="" && $target05kn!="null"){
                        $jumlah_datakn++;
                    }
                    if($target05kl!="" && $target05kl!="null"){
                        $jumlah_datakl++;
                    }
                    if($target05wk!="" && $target05wk!="null"){
                        $jumlah_datawk++;
                    }
                    
                    if($target06kn!="" && $target06kn!="null"){
                        $jumlah_datakn++;
                    }
                    if($target06kl!="" && $target06kl!="null"){
                        $jumlah_datakl++;
                    }
                    if($target06wk!="" && $target06wk!="null"){
                        $jumlah_datawk++;
                    }

                    if($jumlah_datakn>0 && ($nilai01bkn!="" || $nilai02bkn!="" || $nilai03bkn!="" || $nilai04bkn!="" || $nilai05bkn!="" || $nilai06bkn!="")){
                        $kuantitasb_semester1 = round(((floatval($nilai01bkn)+floatval($nilai02bkn)+floatval($nilai03bkn)+floatval($nilai04bkn)+floatval($nilai05bkn)+floatval($nilai06bkn))/$jumlah_datakn),2)."%";
                    } else {
                        $kuantitasb_semester1 = "";
                    }
                    if($jumlah_datakl>0 && ($nilai01bkl!="" || $nilai02bkl!="" || $nilai03bkl!="" || $nilai04bkl!="" || $nilai05bkl!="" || $nilai06bkl!="")){
                        $kualitasb_semester1 = round(((floatval($nilai01bkl)+floatval($nilai02bkl)+floatval($nilai03bkl)+floatval($nilai04bkl)+floatval($nilai05bkl)+floatval($nilai06bkl))/$jumlah_datakl),2)."%";
                    } else {
                        $kualitasb_semester1 = "";
                    }
                    if($jumlah_datawk>0 && ($nilai01bwk!="" || $nilai02bwk!="" || $nilai03bwk!="" || $nilai04bwk!="" || $nilai05bwk!="" || $nilai06bwk!="")){
                        $waktub_semester1 = round(((floatval($nilai01bwk)+floatval($nilai02bwk)+floatval($nilai03bwk)+floatval($nilai04bwk)+floatval($nilai05bwk)+floatval($nilai06bwk))/$jumlah_datawk),2)."%";
                    } else {
                        $waktub_semester1 = "";
                    }
                    
                    if(floatval($kuantitasb_semester1)>110){
                        $kuantitasb_semester1 = "110%";
                    }
                    if(floatval($kualitasb_semester1)>110){
                        $kualitasb_semester1 = "110%";
                    }
                    if(floatval($waktub_semester1)>110){
                        $waktub_semester1 = "110%";
                    }
                    $jumlah_data_semester = 0;
                    if(floatval($kuantitasb_semester1)>0){
                        $jumlah_data_semester++;
                    }
                    if(floatval($kualitasb_semester1)>0){
                        $jumlah_data_semester++;
                    }
                    if(floatval($waktub_semester1)>0){
                        $jumlah_data_semester++;
                    }
                    $total_nilai = floatval($kuantitasb_semester1)+floatval($kualitasb_semester1)+floatval($waktub_semester1);
                    if($jumlah_data_semester>0){
                        if($total_nilai>0){
                            $nilaib_semester1 = round($total_nilai/$jumlah_data_semester,2)."%";
                        } else {
                            $nilaib_semester1 = "0%";
                        }
                    } else {
                        $nilaib_semester1 = "100%";
                    }
                } else {
                    $kuantitasb_semester1 = 0;
                    $kualitasb_semester1 = 0;
                    $waktub_semester1 = 0;
                    $nilaib_semester1 = 0;
                    if($realisasi01bkn!="" && $realisasi01bkn!="null" && $target01kn!="" && $target01kn!="null"){
                        $kuantitasb_semester1 = round(floatval($nilai01bkn),2)."%";
                    }
                    if($realisasi01bkl!="" && $realisasi01bkl!="null" && $target01kl!="" && $target01kl!="null"){
                        $kualitasb_semester1 = round(floatval($nilai01bkl),2)."%";
                    }
                    if($realisasi01bwk!="" && $realisasi01bwk!="null" && $target01wk!="" && $target01wk!="null"){
                        $waktub_semester1 = round(floatval($nilai01bwk),2)."%";
                    }

                    if($realisasi02bkn!="" && $realisasi02bkn!="null" && $target02kn!="" && $target02kn!="null"){
                        $kuantitasb_semester1 = round(floatval($nilai02bkn),2)."%";
                    }
                    if($realisasi02bkl!="" && $realisasi02bkl!="null" && $target02kl!="" && $target02kl!="null"){
                        $kualitasb_semester1 = round(floatval($nilai02bkl),2)."%";
                    }
                    if($realisasi02bwk!="" && $realisasi02bwk!="null" && $target02wk!="" && $target02wk!="null"){
                        $waktub_semester1 = round(floatval($nilai02bwk),2)."%";
                    }
                    
                    if($realisasi03bkn!="" && $realisasi03bkn!="null" && $target03kn!="" && $target03kn!="null"){
                        $kuantitasb_semester1 = round(floatval($nilai03bkn),2)."%";
                    }
                    if($realisasi03bkl!="" && $realisasi03bkl!="null" && $target03kl!="" && $target03kl!="null"){
                        $kualitasb_semester1 = round(floatval($nilai03bkl),2)."%";
                    }
                    if($realisasi03bwk!="" && $realisasi03bwk!="null" && $target03wk!="" && $target03wk!="null"){
                        $waktub_semester1 = round(floatval($nilai03bwk),2)."%";
                    }
                    
                    if($realisasi04bkn!="" && $realisasi04bkn!="null" && $target04kn!="" && $target04kn!="null"){
                        $kuantitasb_semester1 = round(floatval($nilai04bkn),2)."%";
                    }
                    if($realisasi04bkl!="" && $realisasi04bkl!="null" && $target04kl!="" && $target04kl!="null"){
                        $kualitasb_semester1 = round(floatval($nilai04bkl),2)."%";
                    }
                    if($realisasi04bwk!="" && $realisasi04bwk!="null" && $target04wk!="" && $target04wk!="null"){
                        $waktub_semester1 = round(floatval($nilai04bwk),2)."%";
                    }
                    
                    if($realisasi05bkn!="" && $realisasi05bkn!="null" && $target05kn!="" && $target05kn!="null"){
                        $kuantitasb_semester1 = round(floatval($nilai05bkn),2)."%";
                    }
                    if($realisasi05bkl!="" && $realisasi05bkl!="null" && $target05kl!="" && $target05kl!="null"){
                        $kualitasb_semester1 = round(floatval($nilai05bkl),2)."%";
                    }
                    if($realisasi05bwk!="" && $realisasi05bwk!="null" && $target05wk!="" && $target05wk!="null"){
                        $waktub_semester1 = round(floatval($nilai05bwk),2)."%";
                    }
                    
                    if($realisasi06bkn!="" && $realisasi06bkn!="null" && $target06kn!="" && $target06kn!="null"){
                        $kuantitasb_semester1 = round(floatval($nilai06bkn),2)."%";
                    }
                    if($realisasi06bkl!="" && $realisasi06bkl!="null" && $target06kl!="" && $target06kl!="null"){
                        $kualitasb_semester1 = round(floatval($nilai06bkl),2)."%";
                    }
                    if($realisasi06bwk!="" && $realisasi06bwk!="null" && $target06wk!="" && $target06wk!="null"){
                        $waktub_semester1 = round(floatval($nilai06bwk),2)."%";
                    }
                    
                    if(floatval($kuantitasb_semester1)>110){
                        $kuantitasb_semester1 = "110%";
                    }
                    if(floatval($kualitasb_semester1)>110){
                        $kualitasb_semester1 = "110%";
                    }
                    if(floatval($waktub_semester1)>110){
                        $waktub_semester1 = "110%";
                    }
                    $jumlah_data_semester = 0;
                    if(floatval($kuantitasb_semester1)>0){
                        $jumlah_data_semester++;
                    }
                    if(floatval($kualitasb_semester1)>0){
                        $jumlah_data_semester++;
                    }
                    if(floatval($waktub_semester1)>0){
                        $jumlah_data_semester++;
                    }
                    $total_nilai = floatval($kuantitasb_semester1)+floatval($kualitasb_semester1)+floatval($waktub_semester1);
                    if($jumlah_data_semester>0){
                        if($total_nilai>0){
                            $nilaib_semester1 = round($total_nilai/$jumlah_data_semester,2)."%";
                        } else {
                            $nilaib_semester1 = "0%";
                        }
                    } else {
                        $nilaib_semester1 = "100%";
                    }
                }
                if(floatval($nilaib_semester1)>110){
                    $nilaib_semester1 = "110%";
                }
                $datapencapaianm = Datapencapaianm::where('kode_kpi', $kode_kpi);
                $updateData2 = [];
                $updateData2['kuantitasb_semester1'] = $kuantitasb_semester1;
                $updateData2['kualitasb_semester1'] = $kualitasb_semester1;
                $updateData2['waktub_semester1'] = $waktub_semester1;
                $updateData2['nilaib_semester1'] = $nilaib_semester1;
                $datapencapaianm->update($updateData2); 

                if($nama_target=="target06"){
                    $row30 = DB::table('finalisasi_kpi')
                    ->selectRaw("*")
                    ->whereRaw("kode_kpi='$kode_kpi'")
                    ->first();
                    $tahun = $row30->tahun;
                    $nip = $row30->nip;
                    $jenis_kpi = $row30->jenis_kpi;
                    $level_kpi = $row30->level_kpi;

                    $row31 = DB::table('finalisasi_kpi')
                    ->selectRaw("count(id) as jumlah_kpi1")
                    ->whereRaw("tahun='$tahun' and nip='$nip' and jenis_kpi='$jenis_kpi' and level_kpi='$level_kpi'")
                    ->first();
                    $jumlah_kpi1 = intval($row31->jumlah_kpi1);

                    $row32 = DB::table('finalisasi_kpi')
                    ->selectRaw("count(id) as jumlah_kpi2")
                    ->whereRaw("tahun='$tahun' and nip='$nip' and jenis_kpi='$jenis_kpi' and level_kpi='$level_kpi' and realisasi06b<>''")
                    ->first();
                    if($row32){
                        $jumlah_kpi2 = intval($row32->jumlah_kpi2);
                    } else {
                        $jumlah_kpi2 = 0;
                    }

                    if($jumlah_kpi1==$jumlah_kpi2 && $jumlah_kpi1>0){
                        $rows33 = DB::table('finalisasi_kpi')
                        ->selectRaw("*")
                        ->whereRaw("tahun='$tahun' and nip='$nip' and jenis_kpi='$jenis_kpi' and level_kpi='$level_kpi'")
                        ->orderBy('kode_kpi','asc')
                        ->get();
                        $jumlah_kuantitas = 0;
                        $jumlah_kualitas = 0;
                        $jumlah_waktu = 0;
                        $total_kuantitas = 0;
                        $total_kualitas = 0;
                        $total_waktu = 0;
                        foreach($rows33 as $row33){
                            $tahun = $row33->tahun;
                            $nip = $row33->nip;
                            $kode_penilaian = $tahun."-".$nip;
                            $kuantitasb_semester1 = $row33->kuantitasb_semester1;
                            $kualitasb_semester1 = $row33->kualitasb_semester1;
                            $waktub_semester1 = $row33->waktub_semester1;
                            if(floatval($kuantitasb_semester1)>0){
                                $jumlah_kuantitas++;
                            }
                            if(floatval($kualitasb_semester1)>0){
                                $jumlah_kualitas++;
                            }
                            if(floatval($waktub_semester1)>0){
                                $jumlah_waktu++;
                            }

                            $total_kuantitas = $total_kuantitas+floatval($kuantitasb_semester1);
                            $total_kualitas = $total_kualitas+floatval($kualitasb_semester1);
                            $total_waktu = $total_waktu+floatval($waktub_semester1);
                        }
                        
                        if($jumlah_kuantitas>0){
                            $kuantitas_semester1 = round($total_kuantitas/$jumlah_kuantitas,2)."%";
                        } else {
                            if($total_targetkn>0){
                                $kuantitas_semester1 = "100%";
                            } else {
                                $kuantitas_semester1 = "0%";
                            }
                        }
                        if($jumlah_kualitas>0){
                            $kualitas_semester1 = round($total_kualitas/$jumlah_kualitas,2)."%";
                        } else {
                            if($total_targetkl>0){
                                $kualitas_semester1 = "100%";
                            } else {
                                $kualitas_semester1 = "0%";
                            }
                        }
                        if($jumlah_waktu>0){
                            $waktu_semester1 = round($total_waktu/$jumlah_waktu,2)."%";
                        } else {
                            if($total_targetkl>0){
                                $waktu_semester1 = "100%";
                            } else {
                                $waktu_semester1 = "0%";
                            }
                        }

                        $kinerjapegawaim = Kinerjapegawaim::whereRaw("kode_penilaian='$kode_penilaian'");
                        $updateData3 = [];
                        $updateData3['nilai_semester1'] = $kuantitas_semester1;
                        $updateData3['kuantitas_semester1'] = $kuantitas_semester1;
                        $updateData3['kualitas_semester1'] = $kualitas_semester1;
                        $updateData3['waktu_semester1'] = $waktu_semester1;
                        $kinerjapegawaim->update($updateData3); 
                    }
                }
            } else if(substr($nama_target,-2)>="07" && substr($nama_target,-2)<="12"){
                if(strtolower($type_target)=="akumulatif"){
                    $jumlah_datakn = 0;
                    $jumlah_datakl = 0;
                    $jumlah_datawk = 0;
                    if($target07kn!="" && $target07kn!="null"){
                        $jumlah_datakn++;
                    }
                    if($target07kl!="" && $target07kl!="null"){
                        $jumlah_datakl++;
                    }
                    if($target07wk!="" && $target07wk!="null"){
                        $jumlah_datawk++;
                    }
                    if($target08kn!="" && $target08kn!="null"){
                        $jumlah_datakn++;
                    }
                    if($target08kl!="" && $target08kl!="null"){
                        $jumlah_datakl++;
                    }
                    if($target08wk!="" && $target08wk!="null"){
                        $jumlah_datawk++;
                    }
                    
                    if($target09kn!="" && $target09kn!="null"){
                        $jumlah_datakn++;
                    }
                    if($target09kl!="" && $target09kl!="null"){
                        $jumlah_datakl++;
                    }
                    if($target09wk!="" && $target09wk!="null"){
                        $jumlah_datawk++;
                    }
                    
                    if($target10kn!="" && $target10kn!="null"){
                        $jumlah_datakn++;
                    }
                    if($target10kl!="" && $target10kl!="null"){
                        $jumlah_datakl++;
                    }
                    if($target10wk!="" && $target10wk!="null"){
                        $jumlah_datawk++;
                    }
                    
                    if($target11kn!="" && $target11kn!="null"){
                        $jumlah_datakn++;
                    }
                    if($target11kl!="" && $target11kl!="null"){
                        $jumlah_datakl++;
                    }
                    if($target11wk!="" && $target11wk!="null"){
                        $jumlah_datawk++;
                    }
                    
                    if($target12kn!="" && $target12kn!="null"){
                        $jumlah_datakn++;
                    }
                    if($target12kl!="" && $target12kl!="null"){
                        $jumlah_datakl++;
                    }
                    if($target12wk!="" && $target12wk!="null"){
                        $jumlah_datawk++;
                    }

                    if($jumlah_datakn>0){
                        $kuantitasb_semester2 = round(((floatval($nilai07bkn)+floatval($nilai08bkn)+floatval($nilai09bkn)+floatval($nilai10bkn)+floatval($nilai11bkn)+floatval($nilai12bkn))/$jumlah_datakn),2)."%";
                    } else {
                        $kuantitasb_semester2 = "";
                    }
                    if($jumlah_datakl>0){
                        $kualitasb_semester2 = round(((floatval($nilai07bkl)+floatval($nilai08bkl)+floatval($nilai09bkl)+floatval($nilai10bkl)+floatval($nilai11bkl)+floatval($nilai12bkl))/$jumlah_datakl),2)."%";
                    } else {
                        $kualitasb_semester2 = "";
                    }
                    if($jumlah_datawk>0){
                        $waktub_semester2 = round(((floatval($nilai07bwk)+floatval($nilai08bwk)+floatval($nilai09bwk)+floatval($nilai10bwk)+floatval($nilai11bwk)+floatval($nilai12bwk))/$jumlah_datawk),2)."%";
                    } else {
                        $waktub_semester2 = "";
                    }

                    if(floatval($kuantitasb_semester2)>110){
                        $kuantitas_semester2 = "110%";
                    }
                    if(floatval($kualitasb_semester2)>110){
                        $kualitas_semester2 = "110%";
                    }
                    if(floatval($waktub_semester2)>110){
                        $waktu_semester2 = "110%";
                    }

                    $jumlah_data_semester = 0;
                    if(floatval($kuantitasb_semester2)>0){
                        $jumlah_data_semester++;
                    }
                    if(floatval($kualitasb_semester2)>0){
                        $jumlah_data_semester++;
                    }
                    if(floatval($waktub_semester2)>0){
                        $jumlah_data_semester++;
                    }                    
                    if(floatval($kuantitasb_semester2)>0 || floatval($kualitasb_semester2)>0 || floatval($waktub_semester2)>0){
                        $nilaib_semester2 = round((floatval($kuantitasb_semester2)+floatval($kualitasb_semester2)+floatval($waktub_semester2))/$jumlah_data_semester,2)."%";
                    } else {
                        $nilaib_semester2 = "";
                    }
                } else {
                    $kuantitasb_semester2 = "";
                    $kualitasb_semester2 = "";
                    $waktub_semester2 = "";
                    $nilaib_semester2 = "";
                    if($realisasi07bkn!="" && $realisasi07bkn!="null" && $target07kn!="" && $target07kn!="null"){
                        $kuantitasb_semester2 = round(floatval($nilai07bkn),2)."%";
                    }
                    if($realisasi07bkl!="" && $realisasi07bkl!="null" && $target07kl!="" && $target07kl!="null"){
                        $kualitasb_semester2 = round(floatval($nilai07bkl),2)."%";
                    }
                    if($realisasi07bwk!="" && $realisasi07bwk!="null" && $target07wk!="" && $target07wk!="null"){
                        $waktub_semester2 = round(floatval($nilai07bwk),2)."%";
                    }                    
                    if($realisasi08bkn!="" && $realisasi08bkn!="null" && $target08kn!="" && $target08kn!="null"){
                        $kuantitasb_semester2 = round(floatval($nilai08kn),2)."%";
                    }
                    if($realisasi08bkl!="" && $realisasi08bkl!="null" && $target08kl!="" && $target08kl!="null"){
                        $kualitasb_semester2 = round(floatval($nilai08kl),2)."%";
                    }
                    if($realisasi08bwk!="" && $realisasi08bwk!="null" && $target08wk!="" && $target08wk!="null"){
                        $waktub_semester2 = round(floatval($nilai08wk),2)."%";
                    }
                    
                    if($realisasi09bkn!="" && $realisasi09bkn!="null" && $target09kn!="" && $target09kn!="null"){
                        $kuantitasb_semester2 = round(floatval($nilai09kn),2)."%";
                    }
                    if($realisasi09bkl!="" && $realisasi09bkl!="null" && $target09kl!="" && $target09kl!="null"){
                        $kualitasb_semester2 = round(floatval($nilai09kl),2)."%";
                    }
                    if($realisasi09bwk!="" && $realisasi09bwk!="null" && $target09wk!="" && $target09wk!="null"){
                        $waktub_semester2 = round(floatval($nilai09wk),2)."%";
                    }
                    
                    if($realisasi10bkn!="" && $realisasi10bkn!="null" && $target10kn!="" && $target10kn!="null"){
                        $kuantitasb_semester2 = round(floatval($nilai10kn),2)."%";
                    }
                    if($realisasi10bkl!="" && $realisasi10bkl!="null" && $target10kl!="" && $target10kl!="null"){
                        $kualitasb_semester2 = round(floatval($nilai10kl),2)."%";
                    }
                    if($realisasi10bwk!="" && $realisasi10bwk!="null" && $target10wk!="" && $target10wk!="null"){
                        $waktub_semester2 = round(floatval($nilai10wk),2)."%";
                    }
                    
                    if($realisasi11bkn!="" && $realisasi11bkn!="null" && $target11kn!="" && $target11kn!="null"){
                        $kuantitasb_semester2 = round(floatval($nilai11kn),2)."%";
                    }
                    if($realisasi11bkl!="" && $realisasi11bkl!="null" && $target11kl!="" && $target11kl!="null"){
                        $kualitasb_semester2 = round(floatval($nilai11kl),2)."%";
                    }
                    if($realisasi11bwk!="" && $realisasi11bwk!="null" && $target11wk!="" && $target11wk!="null"){
                        $waktub_semester2 = round(floatval($nilai11wk),2)."%";
                    }
                    
                    if($realisasi12bkn!="" && $realisasi12bkn!="null" && $target12kn!="" && $target12kn!="null"){
                        $kuantitasb_semester2 = round(floatval($nilai12kn),2)."%";
                    }
                    if($realisasi12bkl!="" && $realisasi12bkl!="null" && $target12kl!="" && $target12kl!="null"){
                        $kualitasb_semester2 = round(floatval($nilai12kl),2)."%";
                    }
                    if($realisasi12bwk!="" && $realisasi12bwk!="null" && $target12wk!="" && $target12wk!="null"){
                        $waktub_semester2 = round(floatval($nilai02wk),2)."%";
                    }

                    if(floatval($kuantitasb_semester2)>110){
                        $kuantitasb_semester2 = "110%";
                    }
                    if(floatval($kualitasb_semester2)>110){
                        $kualitasb_semester2 = "110%";
                    }
                    if(floatval($waktub_semester2)>110){
                        $waktub_semester2 = "110%";
                    }
                    $jumlah_data_semester = 0;
                    if(floatval($kuantitasb_semester2)>0){
                        $jumlah_data_semester++;
                    }
                    if(floatval($kualitasb_semester2)>0){
                        $jumlah_data_semester++;
                    }
                    if(floatval($waktub_semester2)>0){
                        $jumlah_data_semester++;
                    }

                    if(floatval($kuantitasb_semester2)>0 || floatval($kualitasb_semester2)>0 || floatval($waktub_semester2)>0){
                        $nilaib_semester2 = round((floatval($kuantitasb_semester2)+floatval($kualitasb_semester2)+floatval($waktub_semester2))/$jumlah_data_semester,2)."%";
                    } else {
                        $nilaib_semester2 = "";
                    }
                }
                if(floatval($nilaib_semester2)>110){
                    $nilaib_semester2 = "110%";
                }
                $datapencapaianm = Datapencapaianm::where('kode_kpi', $kode_kpi);
                $updateData2 = [];
                $updateData2['kuantitasb_semester2'] = $kuantitasb_semester2;
                $updateData2['kualitasb_semester2'] = $kualitasb_semester2;
                $updateData2['waktub_semester2'] = $waktub_semester2;
                $updateData2['nilaib_semester2'] = $nilaib_semester2;
                $datapencapaianm->update($updateData2); 

                if($nama_target=="target12"){
                    $row30 = DB::table('finalisasi_kpi')
                    ->selectRaw("*")
                    ->whereRaw("kode_kpi='$kode_kpi'")
                    ->first();
                    $tahun = $row30->tahun;
                    $nip = $row30->nip;
                    $jenis_kpi = $row30->jenis_kpi;
                    $level_kpi = $row30->level_kpi;

                    $row31 = DB::table('finalisasi_kpi')
                    ->selectRaw("count(id) as jumlah_kpi1")
                    ->whereRaw("tahun='$tahun' and nip='$nip' and jenis_kpi='$jenis_kpi' and level_kpi='$level_kpi'")
                    ->first();
                    $jumlah_kpi1 = intval($row31->jumlah_kpi1);

                    $row32 = DB::table('finalisasi_kpi')
                    ->selectRaw("count(id) as jumlah_kpi2")
                    ->whereRaw("tahun='$tahun' and nip='$nip' and jenis_kpi='$jenis_kpi' and level_kpi='$level_kpi' and realisasi12b<>''")
                    ->first();
                    if($row32){
                        $jumlah_kpi2 = intval($row32->jumlah_kpi2);
                    } else {
                        $jumlah_kpi2 = 0;
                    }

                    if($jumlah_kpi1==$jumlah_kpi2 && $jumlah_kpi1>0){
                        $rows33 = DB::table('finalisasi_kpi')
                        ->selectRaw("*")
                        ->whereRaw("tahun='$tahun' and nip='$nip' and jenis_kpi='$jenis_kpi' and level_kpi='$level_kpi'")
                        ->orderBy('kode_kpi','asc')
                        ->get();
                        $jumlah_kuantitas = 0;
                        $jumlah_kualitas = 0;
                        $jumlah_waktu = 0;
                        $total_kuantitas = 0;
                        $total_kualitas = 0;
                        $total_waktu = 0;
                        foreach($rows33 as $row33){
                            $tahun = $row33->tahun;
                            $nip = $row33->nip;
                            $kode_penilaian = $tahun."-".$nip;
                            $kuantitasb_semester2 = $row33->kuantitasb_semester2;
                            $kualitasb_semester2 = $row33->kualitasb_semester2;
                            $waktub_semester2 = $row33->waktub_semester2;

                            if(floatval($kuantitasb_semester2)>0){
                                $jumlah_kuantitas++;
                            }
                            if(floatval($kualitasb_semester2)>0){
                                $jumlah_kualitas++;
                            }
                            if(floatval($waktub_semester2)>0){
                                $jumlah_waktu++;
                            }

                            $total_kuantitas = $total_kuantitas+floatval($kuantitasb_semester2);
                            $total_kualitas = $total_kualitas+floatval($kualitasb_semester2);
                            $total_waktu = $total_waktu+floatval($waktub_semester2);
                        }
                        if($jumlah_kuantitas>0){
                            $kuantitas_semester2 = round($total_kuantitas/$jumlah_kuantitas,2)."%";
                        } else {
                            if($total_targetkn>0){
                                $kuantitas_semester2 = "100%";
                            } else {
                                $kuantitas_semester2 = "0%";
                            }
                        }
                        if($jumlah_kualitas>0){
                            $kualitas_semester2 = round($total_kualitas/$jumlah_kualitas,2)."%";
                        } else {
                            if($total_targetkl>0){
                                $kualitas_semester2 = "100%";
                            } else {
                                $kualitas_semester2 = "0%";
                            }
                        }
                        if($jumlah_waktu>0){
                            $waktu_semester2 = round($total_waktu/$jumlah_waktu,2)."%";
                        } else {
                            if($total_targetkl>0){
                                $waktu_semester2 = "100%";
                            } else {
                                $waktu_semester2 = "0%";
                            }
                        }

                        $kinerjapegawaim = Kinerjapegawaim::whereRaw("kode_penilaian='$kode_penilaian'");
                        $updateData3 = [];
                        $updateData3['nilai_semester2'] = $kuantitas_semester2;
                        $updateData3['kuantitas_semester2'] = $kuantitas_semester2;
                        $updateData3['kualitas_semester2'] = $kualitas_semester2;
                        $updateData3['waktu_semester2'] = $waktu_semester2;
                        $kinerjapegawaim->update($updateData3); 
                    }
                }


            }
            
            return response()->json(["status" => 'sukses', "pesan" => 'Sukses simpan data']);
        } catch (\Exception $e) {
            return response()->json(["status" => 'error', "pesan" => 'Gagal simpan data ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $finalisasikpim = Finalisasikpim::selectRaw("
            finalisasi_kpi.*,
            b.uraian as uraian
        ")
        ->leftJoin('cascading_kpi as b','b.kode_cascading','=','penilaian_pegawai.kode_cascading')
        ->whereRaw("penilaian_pegawai.id='$id'")
        ->orderBy('kode_cascading','asc')
        ->first();  
        return response()->json($finalisasikpim);      
    }

    public function fetchLevel(Request $request)
    {
        $kd_area = $request->kd_area;
        $row1 = DB::table('master_area')
        ->selectRaw("jenis_kpi")
        ->whereRaw("kd_area='".$kd_area."'")
        ->first();
        if($row1){
            $jenis_kpi = $row1->jenis_kpi;
        } else {
            $jenis_kpi = "";
        }
        $data['filter_level'] = Masterlevelm::whereRaw("jenis_kpi='".$jenis_kpi."'")->get(["level_kpi", "nama_level_kpi"]);
        return response()->json($data);
    }
    
    public function fetchDetail(Request $request)
    {
        $tahuncari = $request->tahuncari;
        $nipcari = $request->nipcari;
        // dd($tahuncari." ".$nipcari);
        $data = Datapencapaianm::selectRaw("
            finalisasi_kpi.*,
            b.type_target as type_target,
            b.kd_urut as kd_urut,
            b.polarisasi as polarisasi,
            b.satuan_kuantitas as satuan_kuantitas,
            b.satuan_kualitas as satuan_kualitas,
            b.satuan_waktu as satuan_waktu,
            b.uraian as uraian,
            b.target01kn as target01kn,
            b.target01kl as target01kl,
            b.target01wk as target01wk,
            b.target01 as target01,
            b.target02kn as target02kn,
            b.target02kl as target02kl,
            b.target02wk as target02wk,
            b.target02 as target02,
            b.target03kn as target03kn,
            b.target03kl as target03kl,
            b.target03wk as target03wk,
            b.target03 as target03,
            b.target04kn as target04kn,
            b.target04kl as target04kl,
            b.target04wk as target04wk,
            b.target04 as target04,
            b.target05kn as target05kn,
            b.target05kl as target05kl,
            b.target05wk as target05wk,
            b.target05 as target05,
            b.target06kn as target06kn,
            b.target06kl as target06kl,
            b.target06wk as target06wk,
            b.target06 as target06,
            b.target07kn as target07kn,
            b.target07kl as target07kl,
            b.target07wk as target07wk,
            b.target07 as target07,
            b.target08kn as target08kn,
            b.target08kl as target08kl,
            b.target08wk as target08wk,
            b.target08 as target08,
            b.target09kn as target09kn,
            b.target09kl as target09kl,
            b.target09wk as target09wk,
            b.target09 as target09,
            b.target10kn as target10kn,
            b.target10kl as target10kl,
            b.target10wk as target10wk,
            b.target10 as target10,
            b.target11kn as target11kn,
            b.target11kl as target11kl,
            b.target11wk as target11wk,
            b.target11 as target11,
            b.target12kn as target12kn,
            b.target12kl as target12kl,
            b.target12wk as target12wk,
            b.target12 as target12,
            c.realisasi01kn as realisasi01kn,
            c.realisasi01kl as realisasi01kl,
            c.realisasi01wk as realisasi01wk,
            c.realisasi01 as realisasi01,
            c.realisasi02kn as realisasi02kn,
            c.realisasi02kl as realisasi02kl,
            c.realisasi02wk as realisasi02wk,
            c.realisasi02 as realisasi02,
            c.realisasi03kn as realisasi03kn,
            c.realisasi03kl as realisasi03kl,
            c.realisasi03wk as realisasi03wk,
            c.realisasi03 as realisasi03,
            c.realisasi04kn as realisasi04kn,
            c.realisasi04kl as realisasi04kl,
            c.realisasi04wk as realisasi04wk,
            c.realisasi04 as realisasi04,
            c.realisasi05kn as realisasi05kn,
            c.realisasi05kl as realisasi05kl,
            c.realisasi05wk as realisasi05wk,
            c.realisasi05 as realisasi05,
            c.realisasi06kn as realisasi06kn,
            c.realisasi06kl as realisasi06kl,
            c.realisasi06wk as realisasi06wk,
            c.realisasi06 as realisasi06,
            c.realisasi07kn as realisasi07kn,
            c.realisasi07kl as realisasi07kl,
            c.realisasi07wk as realisasi07wk,
            c.realisasi07 as realisasi07,
            c.realisasi08kn as realisasi08kn,
            c.realisasi08kl as realisasi08kl,
            c.realisasi08wk as realisasi08wk,
            c.realisasi08 as realisasi08,
            c.realisasi09kn as realisasi09kn,
            c.realisasi09kl as realisasi09kl,
            c.realisasi09wk as realisasi09wk,
            c.realisasi09 as realisasi09,
            c.realisasi10kn as realisasi10kn,
            c.realisasi10kl as realisasi10kl,
            c.realisasi10wk as realisasi10wk,
            c.realisasi10 as realisasi10,
            c.realisasi11kn as realisasi11kn,
            c.realisasi11kl as realisasi11kl,
            c.realisasi11wk as realisasi11wk,
            c.realisasi11 as realisasi11,
            c.realisasi12kn as realisasi12kn,
            c.realisasi12kl as realisasi12kl,
            c.realisasi12wk as realisasi12wk,
            c.realisasi12 as realisasi12,
            c.nilai_semester1 as nilai_semester1,
            c.nilai_semester2 as nilai_semester2,
            c.eviden01 as eviden_realisasi01,
            c.eviden02 as eviden_realisasi02,
            c.eviden03 as eviden_realisasi03,
            c.eviden04 as eviden_realisasi04,
            c.eviden05 as eviden_realisasi05,
            c.eviden06 as eviden_realisasi06,
            c.eviden07 as eviden_realisasi07,
            c.eviden08 as eviden_realisasi08,
            c.eviden09 as eviden_realisasi09,
            c.eviden10 as eviden_realisasi10,
            c.eviden11 as eviden_realisasi11,
            c.eviden12 as eviden_realisasi12
       ")
        ->leftJoin('simkppcn.cascading_kpi as b','b.kode_cascading','=','finalisasi_kpi.kode_cascading')
        ->leftJoin('simkppcn.kpi_pegawai as c','c.kode_kpi','=','finalisasi_kpi.kode_kpi')
        // ->leftJoin('simkppcn.approval_kpi as d','d.kode_kpi','=','finalisasi_kpi.kode_kpi')
        ->whereRaw("finalisasi_kpi.tahun='$tahuncari' and finalisasi_kpi.nip='$nipcari'")
        ->orderBy('finalisasi_kpi.kode_cascading','asc');
        return Datatables::eloquent($data)
            ->addIndexColumn()
            ->filter(function ($instance) use ($request) {
                // if (!empty($request->get('search'))) {
                //     $instance->whereRaw("(jadwal.nama_mata_kuliah like '%" . request('search') . "%')");
                // }
            })
            ->addColumn('semester1','')
            ->addColumn('semester2','')
            ->addColumn('aksi', function ($data) use($nipcari) {
                $a = '<div class="acao text-center">';
                // $a .= '<a href="javascript:void(0)" data-id="'.$data->id.'" title="View Form" class="view_row"><button type="button" class="btn btn-icon btn-sm btn-primary" style="margin-right:3px;"><span class="ti ti-eye-search ti-sm"></span></button></a>';
                $a .= '<a href="javascript:void(0)" data-id="'.$data->id.'" title="Update Finalisasi KPI" class="edit_row"><button type="button" class="btn btn-icon btn-warning" style="margin-right:3px;"><span class="ti ti-pencil-star ti-sm"></span></button></a>';
                $a .= '</div>';
                return $a;
            })
            ->addColumn('sem1', function ($data) {
                $score = round(floatval($data->nilaib_semester1),2);
                $target = 100;
                $percentage = ($score / $target) * 100;
                $chartId = 'chart-' . $data->id;                    
                return '<div id="' . $chartId . '" style="width: 120px; height: 90px; margin: 0 auto;" 
                        data-score="' . $score . '" 
                        data-target="' . $target . '" 
                        data-percentage="' . number_format($percentage, 1) . '"></div>';
            })
            ->addColumn('sem2', function ($data) {
                $score = round(floatval($data->nilaib_semester2),2);
                $target = 100;
                $percentage = ($score / $target) * 100;
                $chartId = 'chart2-' . $data->id;                    
                return '<div id="' . $chartId . '" style="width: 120px; height: 90px; margin: 0 auto;" 
                        data-score="' . $score . '" 
                        data-target="' . $target . '" 
                        data-percentage="' . number_format($percentage, 1) . '"></div>';
            })
            ->rawColumns(['aksi','sem1','sem2'])
            ->make(true);        
    } 
    
    // public function hitungApproval(Request $request)
    // {
    //     date_default_timezone_set('Asia/Jakarta');
    //     // $tahuncari = $request->tahuncari;
    //     $kd_areacari = $request->kd_areacari;

    //     $now = Carbon::now();
    //     $tahunIni = $now->year;
    //     $bulanIni = $now->format('m');
    //     $hariIni  = $now->day;

    //     if ((int)$bulanIni === 1) {
    //         $tahunPeriode = $tahunIni - 1;
    //         $bulanPeriode = '12';
    //     } else {
    //         $tahunPeriode = $tahunIni;
    //         $bulanPeriode = str_pad(((int)$bulanIni - 1), 2, '0', STR_PAD_LEFT);
    //     }

    //     if (
    //         $hariIni <= 31 &&
    //         (in_array((int)$bulanPeriode, [1, 7]))
    //     ) {

    //         $kd_areacari = $request->kd_areacari;

    //         $sqlArea = "";
    //         $params  = [];

    //         // tambahkan filter hanya jika bukan "semua"
    //         if ($kd_areacari !== 'semua') {
    //             $sqlArea = " AND kd_area = ? ";
    //             $params[] = $kd_areacari;
    //             $params[] = $kd_areacari;
    //         }

    //         $atasan = DB::select("
    //             SELECT a.nip, b.jenis_kpi, b.level_kpi
    //             FROM (
    //                 SELECT approval AS nip
    //                 FROM data_pegawai
    //                 WHERE approval <> ''
    //                 AND aktif = '1'
    //                 AND payroll = '1'
    //                 AND aktif_simkp = '1'
    //                 $sqlArea

    //                 UNION

    //                 SELECT finalisasi AS nip
    //                 FROM data_pegawai
    //                 WHERE finalisasi <> ''
    //                 AND aktif = '1'
    //                 AND payroll = '1'
    //                 AND aktif_simkp = '1'
    //                 $sqlArea
    //             ) a
    //             INNER JOIN data_pegawai b
    //                 ON a.nip = b.nip
    //                 AND (
    //                     (b.jenis_kpi = 'pusat' AND b.level_kpi >= '3')
    //                     OR b.jenis_kpi <> 'pusat'
    //                 )
    //             GROUP BY a.nip, b.jenis_kpi, b.level_kpi
    //             ORDER BY b.jenis_kpi, b.level_kpi DESC
    //         ", $params);

    //         // dd($atasan);

    //         foreach ($atasan as $a) {

    //             $nip = $a->nip;
    //             $jenisKpi = $a->jenis_kpi;

    //             $kpiPegawai = DB::table('kpi_pegawai')
    //                 ->where('nip', $nip)
    //                 ->where('tahun', $tahunPeriode)
    //                 ->orderBy('kode_kpi')
    //                 ->get();

    //             foreach ($kpiPegawai as $kp) {

    //                 $kodeKpi = $kp->kode_kpi;
    //                 $kodeCascading = $kp->kode_cascading2;

    //                 $levelKpi2 = DB::table('kpi_pegawai')
    //                     ->select('level_kpi as level_kpi2')
    //                     ->where('jenis_kpi', $jenisKpi)
    //                     ->where('kode_cascading2', '>', $kodeCascading)
    //                     ->where('kode_cascading2', 'like', $kodeCascading . '%')
    //                     ->orderBy('level_kpi')
    //                     ->limit(1)
    //                     ->value('level_kpi2');

    //                 if (!$levelKpi2) {
    //                     continue;
    //                 }

    //                 $bawahan = DB::select("
    //                         SELECT *
    //                         FROM kpi_pegawai
    //                         WHERE jenis_kpi=?
    //                         AND level_kpi=?
    //                         AND kode_cascading2>?
    //                         AND kode_cascading2 LIKE ?
    //                         AND nip IN (
    //                             SELECT nip FROM data_pegawai
    //                             WHERE approval=?
    //                         )
    //                         GROUP BY kode_cascading2
    //                     ", [
    //                         $jenisKpi,
    //                         $levelKpi2,
    //                         $kodeCascading,
    //                         $kodeCascading . '%',
    //                         $nip
    //                     ]);

    //                 $jumlah = [
    //                     'kn' => 0, 'kl' => 0, 'wk' => 0, 'total' => 0
    //                 ];
    //                 $total = [
    //                     'kn' => 0, 'kl' => 0, 'wk' => 0, 'total' => 0
    //                 ];

    //                 foreach ($bawahan as $bw) {

    //                     $suffix = $bulanPeriode;

    //                     $nilaiKn = $bw->{'nilai'.$suffix.'kn'};
    //                     $nilaiKl = $bw->{'nilai'.$suffix.'kl'};
    //                     $nilaiWk = $bw->{'nilai'.$suffix.'wk'};
    //                     $realisasi = $bw->{'realisasi'.$suffix};

    //                     $cascading = DB::table('cascading_kpi')
    //                         ->where('tahun', $tahunPeriode)
    //                         ->where('kode_cascading', $bw->kode_cascading)
    //                         ->first();

    //                     if (!$cascading) continue;

    //                     $targetKn = $cascading->{'target'.$suffix.'kn'};
    //                     $targetKl = $cascading->{'target'.$suffix.'kl'};
    //                     $targetWk = $cascading->{'target'.$suffix.'wk'};
    //                     $target   = $cascading->{'target'.$suffix};

    //                     if ($targetKn !== null && $targetKn !== '') {
    //                         $jumlah['kn']++;
    //                         $total['kn'] += (float)$nilaiKn;
    //                     }
    //                     if ($targetKl !== null && $targetKl !== '') {
    //                         $jumlah['kl']++;
    //                         $total['kl'] += (float)$nilaiKl;
    //                     }
    //                     if ($targetWk !== null && $targetWk !== '') {
    //                         $jumlah['wk']++;
    //                         $total['wk'] += (float)$nilaiWk;
    //                     }
    //                     if ($target !== null && $target !== '') {
    //                         $jumlah['total']++;
    //                         $total['total'] += (float)$realisasi;
    //                     }
    //                 }

    //                 $hasil = [
    //                     'kn' => $jumlah['kn'] ? round($total['kn'] / $jumlah['kn'], 2) . '%' : '',
    //                     'kl' => $jumlah['kl'] ? round($total['kl'] / $jumlah['kl'], 2) . '%' : '',
    //                     'wk' => $jumlah['wk'] ? round($total['wk'] / $jumlah['wk'], 2) . '%' : '',
    //                     'total' => $jumlah['total'] ? round($total['total'] / $jumlah['total'], 2) . '%' : '',
    //                 ];

    //                 DB::table('kpi_pegawai')
    //                     ->where('kode_kpi', $kodeKpi)
    //                     ->update([
    //                         'nilai'.$bulanPeriode.'kn' => $hasil['kn'],
    //                         'nilai'.$bulanPeriode.'kl' => $hasil['kl'],
    //                         'nilai'.$bulanPeriode.'wk' => $hasil['wk'],
    //                         'realisasi'.$bulanPeriode  => $hasil['total'],
    //                     ]);
    //             }
    //         }
    //     }

    //     return response()->json([
    //         'status' => 'ok',
    //         'message' => 'Perhitungan KPI selesai'
    //     ]);
    // }

    // public function hitungApproval(Request $request)
    // {
    //     /* =========================
    //      * 1. PERIODE (TETAP)
    //      * ========================= */
    //     $kd_areacari = $request->kd_areacari;

    //     $sqlArea = "";
    //     $params  = [];

    //     if ($kd_areacari !== 'semua') {
    //         $sqlArea = " AND kd_area = ? ";
    //         $params[] = $kd_areacari;
    //         $params[] = $kd_areacari;
    //     }

    //     /* =====================================================
    //      * 2. AMBIL DAFTAR ATASAN
    //      * ===================================================== */
    //     $atasan = DB::select("
    //         SELECT a.nip, b.jenis_kpi, b.level_kpi
    //         FROM (
    //             SELECT approval AS nip
    //             FROM data_pegawai
    //             WHERE approval <> ''
    //             AND approval = '8815012HPI'
    //             AND aktif = '1'
    //             AND payroll = '1'
    //             AND aktif_simkp = '1'
    //             $sqlArea

    //             UNION

    //             SELECT finalisasi AS nip
    //             FROM data_pegawai
    //             WHERE finalisasi <> ''
    //             AND finalisasi = '8815012HPI'
    //             AND aktif = '1'
    //             AND payroll = '1'
    //             AND aktif_simkp = '1'
    //             $sqlArea
    //         ) a
    //         INNER JOIN data_pegawai b
    //             ON a.nip = b.nip
    //             AND (
    //                 (b.jenis_kpi = 'pusat' AND b.level_kpi >= '3')
    //                 OR b.jenis_kpi <> 'pusat'
    //             )
    //         GROUP BY a.nip, b.jenis_kpi, b.level_kpi
    //     ", $params);

    //     if (empty($atasan)) {
    //         return response()->json(['message' => 'Tidak ada atasan']);
    //     }

    //     $nipAtasan = collect($atasan)->pluck('nip')->toArray();

    //     /* =====================================================
    //      * 3. PERIODE (TETAP)
    //      * ===================================================== */
    //     $now = Carbon::now();
    //     $tahunIni = $now->year;
    //     $bulanIni = (int) $now->format('m');

    //     if ($bulanIni === 1) {
    //         $tahunPeriode = $tahunIni - 1;
    //         $bulanPeriode = 12;
    //     } else {
    //         $tahunPeriode = $tahunIni;
    //         $bulanPeriode = $bulanIni - 1;
    //     }

    //     /* =====================================================
    //      * 4. SEMESTER & RANGE BULAN
    //      * ===================================================== */
    //     if ($bulanPeriode == 6) {
    //         // SEMESTER 1
    //         $semester = 1;
    //         $bulanAwal = 1;
    //         $bulanAkhir = 6;
    //     } elseif ($bulanPeriode == 12) {
    //         // SEMESTER 2
    //         $semester = 2;
    //         $bulanAwal = 7;
    //         $bulanAkhir = 12;
    //     } else {
    //         return response()->json([
    //             'message' => 'Periode bukan akhir semester, tidak dihitung'
    //         ]);
    //     }

    //     /* =====================================================
    //      * 5. RATA-RATA NILAI & REALISASI BULANAN (PER KOLOM)
    //      * ===================================================== */
    //     $selectKolom = [];

    //     for ($i = $bulanAwal; $i <= $bulanAkhir; $i++) {
    //         $bln = str_pad($i, 2, '0', STR_PAD_LEFT);

    //         // NILAI
    //         $selectKolom[] =
    //             "AVG(CAST(IFNULL(kp.nilai{$bln}kn,0) AS DECIMAL(10,2))) AS nilai{$bln}kn";
    //         $selectKolom[] =
    //             "AVG(CAST(IFNULL(kp.nilai{$bln}kl,0) AS DECIMAL(10,2))) AS nilai{$bln}kl";
    //         $selectKolom[] =
    //             "AVG(CAST(IFNULL(kp.nilai{$bln}wk,0) AS DECIMAL(10,2))) AS nilai{$bln}wk";

    //         // REALISASI
    //         $selectKolom[] =
    //             "AVG(CAST(IFNULL(kp.realisasi{$bln}kn,0) AS DECIMAL(10,2))) AS realisasi{$bln}kn";
    //         $selectKolom[] =
    //             "AVG(CAST(IFNULL(kp.realisasi{$bln}kl,0) AS DECIMAL(10,2))) AS realisasi{$bln}kl";
    //         $selectKolom[] =
    //             "AVG(CAST(IFNULL(kp.realisasi{$bln}wk,0) AS DECIMAL(10,2))) AS realisasi{$bln}wk";
    //     }

    //     /* =====================================================
    //      * 6. RATA-RATA KOLOM SEMESTER
    //      * ===================================================== */
    //     if ($semester == 1) {
    //         $selectKolom[] =
    //             "AVG(CAST(IFNULL(kp.kuantitas_semester1,0) AS DECIMAL(10,2))) AS kuantitas_semester1";
    //         $selectKolom[] =
    //             "AVG(CAST(IFNULL(kp.kualitas_semester1,0) AS DECIMAL(10,2))) AS kualitas_semester1";
    //         $selectKolom[] =
    //             "AVG(CAST(IFNULL(kp.waktu_semester1,0) AS DECIMAL(10,2))) AS waktu_semester1";
    //         $selectKolom[] =
    //             "AVG(CAST(IFNULL(kp.nilai_semester1,0) AS DECIMAL(10,2))) AS nilai_semester1";
    //     } else {
    //         $selectKolom[] =
    //             "AVG(CAST(IFNULL(kp.kuantitas_semester2,0) AS DECIMAL(10,2))) AS kuantitas_semester2";
    //         $selectKolom[] =
    //             "AVG(CAST(IFNULL(kp.kualitas_semester2,0) AS DECIMAL(10,2))) AS kualitas_semester2";
    //         $selectKolom[] =
    //             "AVG(CAST(IFNULL(kp.waktu_semester2,0) AS DECIMAL(10,2))) AS waktu_semester2";
    //         $selectKolom[] =
    //             "AVG(CAST(IFNULL(kp.nilai_semester2,0) AS DECIMAL(10,2))) AS nilai_semester2";
    //     }

    //     /* =====================================================
    //      * 7. QUERY FINAL
    //      * ===================================================== */
    //     $hasil = DB::table('data_pegawai as dp')
    //         ->join('kpi_pegawai as kp', 'kp.nip', '=', 'dp.nip')
    //         ->selectRaw("
    //             dp.approval AS nip_atasan,
    //             kp.tahun,
    //             kp.jenis_kpi,
    //             " . implode(",\n", $selectKolom)
    //         )
    //         ->whereIn('dp.approval', $nipAtasan)
    //         ->where('kp.tahun', $tahunPeriode)
    //         ->groupBy(
    //             'dp.approval',
    //             'kp.tahun',
    //             'kp.jenis_kpi'
    //         )
    //         ->get();

    //     /* =========================
    //      * 5. RESPONSE
    //      * ========================= */
    //     return response()->json([
    //         'periode' => [
    //             'tahun' => $tahunPeriode,
    //             'bulan_periode' => $bulanPeriode,
    //             'bulan_dihitung' => $bulanAwal . ' - ' . $bulanAkhir,
    //         ],
    //         'jumlah_atasan' => $dataKpiAtasan->count(),
    //         'data' => $dataKpiAtasan
    //     ]);

    //     // dd(response()->json([
    //     //     'filter' => [
    //     //         'kd_area' => $kd_areacari,
    //     //     ],
    //     //     'periode' => [
    //     //         'tahun' => $tahunPeriode,
    //     //         'bulan_periode' => $bulanPeriode,
    //     //         'bulan_dihitung' => $bulanAwal . ' - ' . $bulanAkhir,
    //     //     ],
    //     //     'jumlah_atasan' => count($nipAtasan),
    //     //     'data' => $hasil
    //     // ]));
    // }

    // public function hitungApproval(Request $request)
    // {
    //     /* =====================================================
    //     * 1. FILTER AREA
    //     * ===================================================== */
    //     $kd_areacari = $request->kd_areacari ?? 'semua';

    //     $sqlArea = "";
    //     $params  = [];

    //     if ($kd_areacari !== 'semua') {
    //         $sqlArea = " AND kd_area = ? ";
    //         $params[] = $kd_areacari;
    //         $params[] = $kd_areacari;
    //     }

    //     /* =====================================================
    //     * 2. AMBIL DAFTAR ATASAN
    //     * ===================================================== */
    //     $atasan = DB::select("
    //         SELECT a.nip
    //         FROM (
    //             SELECT approval AS nip
    //             FROM data_pegawai
    //             WHERE approval <> ''
    //             AND approval = '8815012HPI'
    //             AND aktif='1'
    //             AND payroll='1'
    //             AND aktif_simkp='1'
    //             $sqlArea

    //             UNION

    //             SELECT finalisasi AS nip
    //             FROM data_pegawai
    //             WHERE finalisasi <> ''
    //             AND finalisasi = '8815012HPI'
    //             AND aktif='1'
    //             AND payroll='1'
    //             AND aktif_simkp='1'
    //             $sqlArea
    //         ) a
    //         GROUP BY a.nip
    //     ", $params);

    //     if (empty($atasan)) {
    //         return response()->json(['message' => 'Tidak ada atasan']);
    //     }

    //     $nipAtasan = collect($atasan)->pluck('nip')->toArray();

    //     /* =====================================================
    //     * 3. PERIODE OTOMATIS (BULAN LALU)
    //     * ===================================================== */
    //     $now = now();
    //     $tahunIni = $now->year;
    //     $bulanIni = (int)$now->format('m');

    //     if ($bulanIni == 1) {
    //         $tahunPeriode = $tahunIni - 1;
    //         $bulanPeriode = 12;
    //     } else {
    //         $tahunPeriode = $tahunIni;
    //         $bulanPeriode = $bulanIni - 1;
    //     }

    //     /* =====================================================
    //     * 4. SEMESTER
    //     * ===================================================== */
    //     if ($bulanPeriode == 6) {
    //         $semester = 1;
    //         $bulanAwal = 1;
    //         $bulanAkhir = 6;
    //     } elseif ($bulanPeriode == 12) {
    //         $semester = 2;
    //         $bulanAwal = 7;
    //         $bulanAkhir = 12;
    //     } else {
    //         return response()->json([
    //             'message' => 'Bukan akhir semester, tidak dihitung'
    //         ]);
    //     }

    //     /* =====================================================
    //     * 5. KOLOM DINAMIS BULANAN
    //     * ===================================================== */
    //     $selectKolom = [];

    //     for ($i = $bulanAwal; $i <= $bulanAkhir; $i++) {
    //         $bln = str_pad($i, 2, '0', STR_PAD_LEFT);

    //         // ===== KPI PEGAWAI =====
    //         $selectKolom[] = "AVG(IFNULL(kp.nilai{$bln}kn,0)) AS nilai{$bln}kn";
    //         $selectKolom[] = "AVG(IFNULL(kp.nilai{$bln}kl,0)) AS nilai{$bln}kl";
    //         $selectKolom[] = "AVG(IFNULL(kp.nilai{$bln}wk,0)) AS nilai{$bln}wk";

    //         $selectKolom[] = "AVG(IFNULL(kp.realisasi{$bln}kn,0)) AS realisasi{$bln}kn";
    //         $selectKolom[] = "AVG(IFNULL(kp.realisasi{$bln}kl,0)) AS realisasi{$bln}kl";
    //         $selectKolom[] = "AVG(IFNULL(kp.realisasi{$bln}wk,0)) AS realisasi{$bln}wk";

    //         // ===== FINALISASI KPI =====
    //         $selectKolom[] = "AVG(IFNULL(fk.nilai{$bln}bkn,0)) AS nilai{$bln}bkn";
    //         $selectKolom[] = "AVG(IFNULL(fk.nilai{$bln}bkl,0)) AS nilai{$bln}bkl";
    //         $selectKolom[] = "AVG(IFNULL(fk.nilai{$bln}bwk,0)) AS nilai{$bln}bwk";

    //         $selectKolom[] = "AVG(IFNULL(fk.realisasi{$bln}bkn,0)) AS realisasi{$bln}bkn";
    //         $selectKolom[] = "AVG(IFNULL(fk.realisasi{$bln}bkl,0)) AS realisasi{$bln}bkl";
    //         $selectKolom[] = "AVG(IFNULL(fk.realisasi{$bln}bwk,0)) AS realisasi{$bln}bwk";
    //     }

    //     /* =====================================================
    //     * 6. KOLOM SEMESTER
    //     * ===================================================== */
    //     if ($semester == 1) {

    //         // KPI PEGAWAI
    //         $selectKolom[] = "AVG(IFNULL(kp.kuantitas_semester1,0)) AS kuantitas_semester1";
    //         $selectKolom[] = "AVG(IFNULL(kp.kualitas_semester1,0)) AS kualitas_semester1";
    //         $selectKolom[] = "AVG(IFNULL(kp.waktu_semester1,0)) AS waktu_semester1";
    //         $selectKolom[] = "AVG(IFNULL(kp.nilai_semester1,0)) AS nilai_semester1";

    //         // FINALISASI
    //         $selectKolom[] = "AVG(IFNULL(fk.kuantitasb_semester1,0)) AS kuantitasb_semester1";
    //         $selectKolom[] = "AVG(IFNULL(fk.kualitasb_semester1,0)) AS kualitasb_semester1";
    //         $selectKolom[] = "AVG(IFNULL(fk.waktub_semester1,0)) AS waktub_semester1";
    //         $selectKolom[] = "AVG(IFNULL(fk.nilaib_semester1,0)) AS nilaib_semester1";

    //     } else {

    //         // KPI PEGAWAI
    //         $selectKolom[] = "AVG(IFNULL(kp.kuantitas_semester2,0)) AS kuantitas_semester2";
    //         $selectKolom[] = "AVG(IFNULL(kp.kualitas_semester2,0)) AS kualitas_semester2";
    //         $selectKolom[] = "AVG(IFNULL(kp.waktu_semester2,0)) AS waktu_semester2";
    //         $selectKolom[] = "AVG(IFNULL(kp.nilai_semester2,0)) AS nilai_semester2";

    //         // FINALISASI
    //         $selectKolom[] = "AVG(IFNULL(fk.kuantitasb_semester2,0)) AS kuantitasb_semester2";
    //         $selectKolom[] = "AVG(IFNULL(fk.kualitasb_semester2,0)) AS kualitasb_semester2";
    //         $selectKolom[] = "AVG(IFNULL(fk.waktub_semester2,0)) AS waktub_semester2";
    //         $selectKolom[] = "AVG(IFNULL(fk.nilaib_semester2,0)) AS nilaib_semester2";
    //     }

    //     /* =====================================================
    //     * 7. QUERY FINAL (1 BARIS / ATASAN)
    //     * ===================================================== */
    //     $hasil = DB::table('data_pegawai as dp')
    //     ->join('kpi_pegawai as kp', 'kp.nip', '=', 'dp.nip')
    //     ->leftJoin('finalisasi_kpi as fk', function ($join) use ($tahunPeriode) {
    //         $join->on('fk.nip', '=', 'dp.approval')
    //             ->where('fk.tahun', '=', $tahunPeriode);
    //     })
    //     ->selectRaw("
    //         dp.approval AS nip_atasan,
    //         kp.tahun,
    //         kp.jenis_kpi,
    //         " . implode(",\n", $selectKolom)
    //     )
    //     ->whereIn('dp.approval', $nipAtasan)
    //     ->where('kp.tahun', $tahunPeriode)
    //     ->groupBy(
    //         'dp.approval',
    //         'kp.tahun',
    //         'kp.jenis_kpi'
    //     )
    //     ->get();

    //     /* =====================================================
    //     * 8. RESPONSE
    //     * ===================================================== */
    //     // return response()->json([
    //     //     'periode' => [
    //     //         'tahun' => $tahunPeriode,
    //     //         'semester' => $semester,
    //     //         'bulan_dihitung' => $bulanAwal . ' - ' . $bulanAkhir
    //     //     ],
    //     //     'jumlah_atasan' => $hasil->count(),
    //     //     'data' => $hasil
    //     // ]);
    //     $respon = response()->json([
    //         'periode' => [
    //             'tahun' => $tahunPeriode,
    //             'semester' => $semester,
    //             'bulan_dihitung' => $bulanAwal . ' - ' . $bulanAkhir
    //         ],
    //         'jumlah_atasan' => $hasil->count(),
    //         'data' => $hasil
    //     ]);
    //     dd($respon);
    // }


    // public function hitungApproval(Request $request)
    // {
    //     DB::transaction(function () {

    //         /* ===============================
    //         * 1. PERIODE & SEMESTER
    //         * =============================== */
    //         $now   = \Carbon\Carbon::now();
    //         $tahun = $now->year;
    //         $bulan = (int) $now->format('m');

    //         if ($bulan == 1) {
    //             $tahun--;
    //             $bulan = 12;
    //         } else {
    //             $bulan--;
    //         }

    //         if ($bulan == 6) {
    //             $semester   = 1;
    //             $bulanAwal  = 1;
    //             $bulanAkhir = 6;
    //         } elseif ($bulan == 12) {
    //             $semester   = 2;
    //             $bulanAwal  = 7;
    //             $bulanAkhir = 12;
    //         } else {
    //             return; // bukan akhir semester
    //         }

    //         /* ===============================
    //         * 2. KOLOM BULANAN (DINAMIS)
    //         * =============================== */
    //         $selectBulanan = [];
    //         $setKpBulanan  = [];
    //         $setFkBulanan  = [];

    //         for ($i = $bulanAwal; $i <= $bulanAkhir; $i++) {
    //             $bln = str_pad($i, 2, '0', STR_PAD_LEFT);

    //             $selectBulanan[] = "
    //                 AVG(CAST(IFNULL(fk.nilai{$bln}bkn, kp.nilai{$bln}kn) AS DECIMAL(10,2))) AS nilai{$bln}kn,
    //                 AVG(CAST(IFNULL(fk.nilai{$bln}bkl, kp.nilai{$bln}kl) AS DECIMAL(10,2))) AS nilai{$bln}kl,
    //                 AVG(CAST(IFNULL(fk.nilai{$bln}bwk, kp.nilai{$bln}wk) AS DECIMAL(10,2))) AS nilai{$bln}wk
    //             ";

    //             $setKpBulanan[] = "
    //                 ka.nilai{$bln}kn = s.nilai{$bln}kn,
    //                 ka.nilai{$bln}kl = s.nilai{$bln}kl,
    //                 ka.nilai{$bln}wk = s.nilai{$bln}wk
    //             ";

    //             $setFkBulanan[] = "
    //                 fa.nilai{$bln}bkn = s.nilai{$bln}kn,
    //                 fa.nilai{$bln}bkl = s.nilai{$bln}kl,
    //                 fa.nilai{$bln}bwk = s.nilai{$bln}wk
    //             ";
    //         }

    //         dd($selectBulanan);

    //         /* ===============================
    //         * 3. KOLOM SEMESTER
    //         * =============================== */
    //         if ($semester == 1) {
    //             $selectSemester = "
    //                 AVG(CAST(IFNULL(fk.kuantitasb_semester1, kp.kuantitas_semester1) AS DECIMAL(10,2))) AS kuantitas_semester1,
    //                 AVG(CAST(IFNULL(fk.kualitasb_semester1,  kp.kualitas_semester1)  AS DECIMAL(10,2))) AS kualitas_semester1,
    //                 AVG(CAST(IFNULL(fk.waktub_semester1,     kp.waktu_semester1)     AS DECIMAL(10,2))) AS waktu_semester1,
    //                 AVG(CAST(IFNULL(fk.nilaib_semester1,     kp.nilai_semester1)     AS DECIMAL(10,2))) AS nilai_semester1
    //             ";

    //             $setKpSemester = "
    //                 ka.kuantitas_semester1 = s.kuantitas_semester1,
    //                 ka.kualitas_semester1  = s.kualitas_semester1,
    //                 ka.waktu_semester1     = s.waktu_semester1,
    //                 ka.nilai_semester1     = s.nilai_semester1
    //             ";

    //             $setFkSemester = "
    //                 fa.kuantitasb_semester1 = s.kuantitas_semester1,
    //                 fa.kualitasb_semester1  = s.kualitas_semester1,
    //                 fa.waktub_semester1     = s.waktu_semester1,
    //                 fa.nilaib_semester1     = s.nilai_semester1
    //             ";
    //         } else {
    //             $selectSemester = "
    //                 AVG(CAST(IFNULL(fk.kuantitasb_semester2, kp.kuantitas_semester2) AS DECIMAL(10,2))) AS kuantitas_semester2,
    //                 AVG(CAST(IFNULL(fk.kualitasb_semester2,  kp.kualitas_semester2)  AS DECIMAL(10,2))) AS kualitas_semester2,
    //                 AVG(CAST(IFNULL(fk.waktub_semester2,     kp.waktu_semester2)     AS DECIMAL(10,2))) AS waktu_semester2,
    //                 AVG(CAST(IFNULL(fk.nilaib_semester2,     kp.nilai_semester2)     AS DECIMAL(10,2))) AS nilai_semester2
    //             ";

    //             $setKpSemester = "
    //                 ka.kuantitas_semester2 = s.kuantitas_semester2,
    //                 ka.kualitas_semester2  = s.kualitas_semester2,
    //                 ka.waktu_semester2     = s.waktu_semester2,
    //                 ka.nilai_semester2     = s.nilai_semester2
    //             ";

    //             $setFkSemester = "
    //                 fa.kuantitasb_semester2 = s.kuantitas_semester2,
    //                 fa.kualitasb_semester2  = s.kualitas_semester2,
    //                 fa.waktub_semester2     = s.waktu_semester2,
    //                 fa.nilaib_semester2     = s.nilai_semester2
    //             ";
    //         }

    //         /* ===============================
    //         * 4. SUBQUERY SUMBER DATA
    //         * =============================== */
    //         $subQuery = "
    //             SELECT
    //                 dp.approval AS nip_atasan,
    //                 " . implode(",", $selectBulanan) . ",
    //                 $selectSemester
    //             FROM data_pegawai dp
    //             JOIN kpi_pegawai kp ON kp.nip = dp.nip AND kp.tahun = '$tahun'
    //             LEFT JOIN finalisasi_kpi fk
    //                 ON fk.nip = kp.nip AND fk.tahun = kp.tahun
    //             WHERE dp.approval <> ''
    //             GROUP BY dp.approval
    //         ";

    //         // dd(
    //         //     implode(",", $setKpBulanan),
    //         //     implode(",", $setFkBulanan)
    //         // );

    //         /* ===============================
    //         * 5. UPDATE kpi_pegawai (BELUM FINAL)
    //         * =============================== */
    //         DB::statement("
    //             UPDATE kpi_pegawai ka
    //             JOIN ($subQuery) s ON s.nip_atasan = ka.nip
    //             SET
    //                 " . implode(",", $setKpBulanan) . ",
    //                 $setKpSemester
    //             WHERE ka.tahun = '$tahun'
    //             AND NOT EXISTS (
    //                 SELECT 1 FROM finalisasi_kpi f
    //                 WHERE f.nip = ka.nip AND f.tahun = ka.tahun
    //             )
    //         ");

    //         /* ===============================
    //         * 6. UPDATE finalisasi_kpi (SUDAH FINAL)
    //         * =============================== */
    //         // DB::statement("
    //         //     UPDATE finalisasi_kpi fa
    //         //     JOIN ($subQuery) s ON s.nip_atasan = fa.nip
    //         //     SET
    //         //         " . implode(",", $setFkBulanan) . ",
    //         //         $setFkSemester
    //         //     WHERE fa.tahun = '$tahun'
    //         // ");
    //     });
    // }

    // public function hitungApproval(){
    //     error_reporting(E_ALL);
    //     ini_set('display_errors', 1);
    //     date_default_timezone_set('Asia/Jakarta');


    //     $now = Carbon::now('Asia/Jakarta');
    //     $tahun_ini = (int)$now->format('Y');
    //     $bulan_ini = (int)$now->format('m');


    //     // ========================
    //     // PERIODE (SAMA DENGAN SCRIPT ASLI)
    //     // ========================
    //     if ($bulan_ini === 1) {
    //         $tahun_periode = $tahun_ini - 1;
    //         $bulan_periode = 12;
    //     } else {
    //         $tahun_periode = $tahun_ini;
    //         $bulan_periode = $bulan_ini - 1;
    //     }


    //     $bulan = str_pad($bulan_periode, 2, '0', STR_PAD_LEFT);


    //     $response = [
    //         'tahun' => $tahun_periode,
    //         'bulan' => $bulan,
    //         'diproses' => false,
    //         'atasan' => []
    //     ];


    //     // HANYA BULAN 6 & 12
    //     if (!in_array($bulan_periode, [6, 12])) {
    //         $response['message'] = 'Proses dihentikan (bukan periode semester)';
    //         return response()->json($response);
    //     }

    //     // ========================
    //     // AMBIL ATASAN
    //     // ========================
        // $atasanList = DB::select("
        // SELECT a.nip,b.jenis_kpi,b.level_kpi
        // FROM (
        // SELECT approval AS nip
        // FROM data_pegawai
        // WHERE approval<>'' AND aktif='1' AND payroll='1' AND aktif_simkp='1'
        // ) a
        // INNER JOIN data_pegawai b
        // ON a.nip=b.nip
        // AND ((b.jenis_kpi='pusat' AND b.level_kpi>='3') OR b.jenis_kpi<>'pusat')
        // WHERE a.nip='8815012HPI'
        // GROUP BY a.nip
        // ORDER BY b.jenis_kpi,b.level_kpi DESC
        // ");

    //     foreach ($atasanList as $atasan) {
    //         $nip = $atasan->nip;


    //         $atasanData = [
    //             'nip' => $nip,
    //             'jenis_kpi' => $atasan->jenis_kpi,
    //             'level_kpi' => $atasan->level_kpi,
    //             'kpi' => []
    //         ];


    //         // ========================
    //         // KPI ATASAN
    //         // ========================
    //         $kpiList = DB::table('kpi_pegawai')
    //         ->where('nip', $nip)
    //         ->where('tahun', $tahun_periode)
    //         ->orderBy('kode_kpi')
    //         ->get();

    //         foreach ($kpiList as $kpi) {
    //             // level bawah langsung
    //             $levelNext = DB::selectOne("
    //             SELECT MIN(level_kpi) AS level_kpi
    //             FROM kpi_pegawai
    //             WHERE jenis_kpi=?
    //             AND kode_cascading2>?
    //             AND kode_cascading2 LIKE ?
    //             ", [$atasan->jenis_kpi, $kpi->kode_cascading2, $kpi->kode_cascading2 . '%']);


    //             if (!$levelNext || !$levelNext->level_kpi) continue;


    //             $suffix = $bulan;

    //             // ========================
    //             // KPI BAWAHAN (SATU QUERY)
    //             // ========================
    //             $bawahan = DB::table('kpi_pegawai as kp')
    //             ->join('data_pegawai as dp', 'kp.nip', '=', 'dp.nip')
    //             ->join('cascading_kpi as ck', function ($q) use ($tahun_periode) {
    //                 $q->on('kp.kode_cascading', '=', 'ck.kode_cascading')
    //                 ->where('ck.tahun', '=', $tahun_periode);
    //             })
    //             ->where('kp.jenis_kpi', $atasan->jenis_kpi)
    //             ->where('kp.level_kpi', $levelNext->level_kpi)
    //             ->where('kp.kode_cascading2', '>', $kpi->kode_cascading2)
    //             ->where('kp.kode_cascading2', 'like', $kpi->kode_cascading2 . '%')
    //             ->where(function ($q) use ($nip) {
    //                 $q->where('dp.approval', $nip)
    //                 ->orWhere('dp.finalisasi', $nip);
    //             })
    //             ->select([
    //                 "kp.nilai{$suffix}kn",
    //                 "kp.nilai{$suffix}kl",
    //                 "kp.nilai{$suffix}wk",
    //                 "kp.realisasi{$suffix}",
    //                 "ck.target{$suffix}kn",
    //                 "ck.target{$suffix}kl",
    //                 "ck.target{$suffix}wk",
    //                 "ck.target{$suffix}",
    //             ])
    //             ->get();

    //             $sum = [
    //                 'kn' => [0, 0],
    //                 'kl' => [0, 0],
    //                 'wk' => [0, 0],
    //                 'realisasi' => [0, 0]
    //             ];


    //             foreach ($bawahan as $b) {
    //                 if ($b->{"target{$suffix}kn"} !== '' && $b->{"target{$suffix}kn"} !== 'null') {
    //                     $sum['kn'][0]++;
    //                     $sum['kn'][1] += (float)$b->{"nilai{$suffix}kn"};
    //                 }
    //                 if ($b->{"target{$suffix}kl"} !== '' && $b->{"target{$suffix}kl"} !== 'null') {
    //                     $sum['kl'][0]++;
    //                     $sum['kl'][1] += (float)$b->{"nilai{$suffix}kl"};
    //                 }
    //                 if ($b->{"target{$suffix}wk"} !== '' && $b->{"target{$suffix}wk"} !== 'null') {
    //                     $sum['wk'][0]++;
    //                     $sum['wk'][1] += (float)$b->{"nilai{$suffix}wk"};
    //                 }
    //                 if ($b->{"target{$suffix}"} !== '' && $b->{"target{$suffix}"} !== 'null') {
    //                     $sum['realisasi'][0]++;
    //                     $sum['realisasi'][1] += (float)$b->{"realisasi{$suffix}"};
    //                 }
    //             }

    //             $nilai = [
    //                 'nilai_kn' => $sum['kn'][0] ? round($sum['kn'][1] / $sum['kn'][0], 2) . '%' : '',
    //                 'nilai_kl' => $sum['kl'][0] ? round($sum['kl'][1] / $sum['kl'][0], 2) . '%' : '',
    //                 'nilai_wk' => $sum['wk'][0] ? round($sum['wk'][1] / $sum['wk'][0], 2) . '%' : '',
    //                 'realisasi' => $sum['realisasi'][0] ? round($sum['realisasi'][1] / $sum['realisasi'][0], 2) . '%' : '',
    //             ];


    //             DB::table('kpi_pegawai')
    //             ->where('kode_kpi', $kpi->kode_kpi)
    //             ->update([
    //                 "nilai{$suffix}kn" => $nilai['nilai_kn'],
    //                 "nilai{$suffix}kl" => $nilai['nilai_kl'],
    //                 "nilai{$suffix}wk" => $nilai['nilai_wk'],
    //                 "realisasi{$suffix}" => $nilai['realisasi'],
    //             ]);

    //             $atasanData['kpi'][] = [
    //                 'kode_kpi' => $kpi->kode_kpi,
    //                 'kode_cascading' => $kpi->kode_cascading2,
    //                 'hasil' => $nilai
    //             ];
    //         }


    //         $response['atasan'][] = $atasanData;
    //     }


    //     $response['diproses'] = true;
    //     return response()->json($response);

        
    // }

//     public function hitungApproval()
// {
//     $nipAtasan = '8815012HPI';
    // $tahun_ini = date("Y");
    // $bulan_ini = date("m");

    // // Tentukan periode
    // if (intval($bulan_ini) == 1) {
    //     $tahun = $tahun_ini - 1;
    //     $bulan = "12";
    // } else {
    //     $tahun = $tahun_ini;
    //     $bulan = str_pad(intval($bulan_ini) - 1, 2, "0", STR_PAD_LEFT);
    // }

    // // Tentukan semester
    // if ($bulan >= '01' && $bulan <= '06') {
    //     $semester = 1;
    //     $bulanStart = 1;
    //     $bulanEnd = 6;
    // } else {
    //     $semester = 2;
    //     $bulanStart = 7;
    //     $bulanEnd = 12;
    // }

//     $nilaiCols = ['kn','kl','wk'];

//     // =============================
//     // AMBIL SEMUA BAWAHAN
//     // =============================
//     $bawahanKPI = DB::table('kpi_pegawai AS b')
//         ->where('b.nip', '<>', $nipAtasan)
//         ->where('b.tahun', $tahun)
//         ->where('b.kode_cascading2', 'like', DB::raw("(SELECT kode_cascading2 FROM kpi_pegawai WHERE nip='$nipAtasan' AND tahun=$tahun) || '%'"))
//         ->pluck('nip'); // ambil semua nip bawahan

//     $bawahanFinal = DB::table('finalisasi_kpi AS b')
//         ->where('b.nip', '<>', $nipAtasan)
//         ->where('b.tahun', $tahun)
//         ->where('b.kode_cascading2', 'like', DB::raw("(SELECT kode_cascading2 FROM finalisasi_kpi WHERE nip='$nipAtasan' AND tahun=$tahun) || '%'"))
//         ->pluck('nip'); // ambil semua nip bawahan

//     // =============================
//     // KPI_PEGAWAI
//     // =============================
//     $realisasiCols = [];
//     for ($i = $bulanStart; $i <= $bulanEnd; $i++) {
//         $idx = str_pad($i, 2, "0", STR_PAD_LEFT);
//         $realisasiCols[] = "realisasi$idx";
//     }

//     $selectCols = [];
//     foreach ($realisasiCols as $col) {
//         $selectCols[] = "ROUND(AVG(CAST(b.$col AS DECIMAL(10,2))),2) AS $col";
//         $bulanNum = substr($col, -2);
//         foreach ($nilaiCols as $val) {
//             $colName = "nilai{$bulanNum}$val";
//             $selectCols[] = "ROUND(AVG(CAST(b.$colName AS DECIMAL(10,2))),2) AS $colName";
//         }
//     }

//     $semesterCol = $semester == 1 ? 'nilai_semester1' : 'nilai_semester2';
//     $semesterExpr = implode(' + ', array_map(fn($c)=>"CAST(b.$c AS DECIMAL(10,2))", $realisasiCols)) . " / " . count($realisasiCols);
//     $selectCols[] = "ROUND(AVG($semesterExpr),2) AS $semesterCol";

//     $rincianPegawai = DB::table('kpi_pegawai AS a')
//         ->selectRaw(implode(', ', $selectCols))
//         ->join('kpi_pegawai AS b', function($join) use ($tahun, $nipAtasan){
//             $join->on('b.kode_cascading2', 'like', DB::raw("CONCAT(a.kode_cascading2,'%')"))
//                  ->whereColumn('b.nip', '<>', 'a.nip')
//                  ->whereColumn('b.tahun', '=', 'a.tahun');
//         })
//         ->where('a.nip', $nipAtasan)
//         ->where('a.tahun', $tahun)
//         ->first();

//     // Siapkan data untuk update
//     $sets = [];
//     foreach ($realisasiCols as $col) { $sets[$col] = $rincianPegawai->$col; }
//     foreach ($realisasiCols as $col) {
//         $bulanNum = substr($col, -2);
//         foreach ($nilaiCols as $val) {
//             $colName = "nilai{$bulanNum}$val";
//             $sets[$colName] = $rincianPegawai->$colName;
//         }
//     }
//     $sets[$semesterCol] = $rincianPegawai->$semesterCol;

//     $updatedPegawai = DB::table('kpi_pegawai')
//         ->where('nip', $nipAtasan)
//         ->where('tahun', $tahun)
//         ->update($sets);

//     // =============================
//     // FINALISASI_KPI
//     // =============================
//     $realisasiColsFinal = [];
//     for ($i = $bulanStart; $i <= $bulanEnd; $i++) {
//         $idx = str_pad($i, 2, "0", STR_PAD_LEFT);
//         $realisasiColsFinal[] = "realisasi{$idx}b";
//     }

//     $selectColsFinal = [];
//     foreach ($realisasiColsFinal as $col) {
//         $selectColsFinal[] = "ROUND(AVG(CAST(b.$col AS DECIMAL(10,2))),2) AS $col";
//         $bulanNum = substr($col, 9, 2);
//         foreach ($nilaiCols as $val) {
//             $colName = "nilai{$bulanNum}b{$val}";
//             $selectColsFinal[] = "ROUND(AVG(CAST(b.$colName AS DECIMAL(10,2))),2) AS $colName";
//         }
//     }

//     $semesterColFinal = $semester == 1 ? 'nilaib_semester1' : 'nilaib_semester2';
//     $semesterExprFinal = implode(' + ', array_map(fn($c)=>"CAST(b.$c AS DECIMAL(10,2))", $realisasiColsFinal)) . " / ".count($realisasiColsFinal);
//     $selectColsFinal[] = "ROUND(AVG($semesterExprFinal),2) AS $semesterColFinal";

//     $rincianFinal = DB::table('finalisasi_kpi AS a')
//         ->selectRaw(implode(', ', $selectColsFinal))
//         ->join('finalisasi_kpi AS b', function($join){
//             $join->on('b.kode_cascading2', 'like', DB::raw("CONCAT(a.kode_cascading2,'%')"))
//                  ->whereColumn('b.nip', '<>', 'a.nip')
//                  ->whereColumn('b.tahun', '=', 'a.tahun');
//         })
//         ->where('a.nip', $nipAtasan)
//         ->where('a.tahun', $tahun)
//         ->first();

//     $setsFinal = [];
//     foreach ($realisasiColsFinal as $col) { $setsFinal[$col] = $rincianFinal->$col; }
//     foreach ($realisasiColsFinal as $col) {
//         $bulanNum = substr($col, 9, 2);
//         foreach ($nilaiCols as $val) {
//             $colName = "nilai{$bulanNum}b{$val}";
//             $setsFinal[$colName] = $rincianFinal->$colName;
//         }
//     }
//     $setsFinal[$semesterColFinal] = $rincianFinal->$semesterColFinal;

//     $updatedFinal = DB::table('finalisasi_kpi')
//         ->where('nip', $nipAtasan)
//         ->where('tahun', $tahun)
//         ->update($setsFinal);

//     // =============================
//     // Response rinci
//     // =============================
//     $response =  response()->json([
//         'status' => 'success',
//         'nip' => $nipAtasan,
//         'semester' => $semester,
//         'tahun' => $tahun,
//         'update_count' => [
//             'kpi_pegawai' => $updatedPegawai,
//             'finalisasi_kpi' => $updatedFinal
//         ],
//         'bawahan' => [
//             'kpi_pegawai' => $bawahanKPI,
//             'finalisasi_kpi' => $bawahanFinal
//         ],
//         'rata_rata_bulanan' => [
//             'kpi_pegawai' => $rincianPegawai,
//             'finalisasi_kpi' => $rincianFinal
//         ]
//     ], 200);
//     dd($response);
// }


// public function hitungApproval(Request $request)
// {
//     /* =========================================
//      * 1. PERIODE & SEMESTER
//      * ========================================= */
//     $nipAtasan = '8815012HPI';
//     $tahun_ini = date('Y');
//     $bulan_ini = date('m');

//     if (intval($bulan_ini) == 1) {
//         $tahun = $tahun_ini - 1;
//         $bulan = "12";
//     } else {
//         $tahun = $tahun_ini;
//         $bulan = str_pad(intval($bulan_ini) - 1, 2, "0", STR_PAD_LEFT);
//     }

//     if ($bulan >= '01' && $bulan <= '06') {
//         $semester   = 1;
//         $bulanStart = 1;
//         $bulanEnd   = 6;
//     } else {
//         $semester   = 2;
//         $bulanStart = 7;
//         $bulanEnd   = 12;
//     }

//     /* =========================================
//      * 2. KPI ATASAN
//      * ========================================= */
//     $kpiAtasanList = DB::table('kpi_pegawai')
//         ->where('nip', $nipAtasan)
//         ->where('tahun', $tahun)
//         ->get();

//     if ($kpiAtasanList->isEmpty()) {
//         return response()->json([
//             'status' => 'gagal',
//             'pesan'  => 'KPI atasan tidak ditemukan'
//         ]);
//     }

//     $responseDetail = [];

//     /* =========================================
//      * 3. LOOP KPI ATASAN
//      * ========================================= */
//     foreach ($kpiAtasanList as $kpa) {

//         /* =========================================
//          * 4. BUILD KOLOM AVG
//          * ========================================= */
//         $selectCols = [];

//         for ($i = $bulanStart; $i <= $bulanEnd; $i++) {
//             $bln = str_pad($i, 2, '0', STR_PAD_LEFT);

//             /* ===== nilai KN, KL, WK ===== */
//             foreach (['kn','kl','wk'] as $jenis) {
//                 $col = "nilai{$bln}{$jenis}";

//                 $selectCols[] = "
//                     COALESCE(
//                         ROUND(
//                             SUM(
//                                 CAST(
//                                     REPLACE(NULLIF(kb.$col,''),'%','')
//                                     AS DECIMAL(10,4)
//                                 )
//                             )
//                             / NULLIF(COUNT(*),0)
//                         , 2),
//                     0) AS $col
//                 ";
//             }

//             /* ===== realisasi ===== */
//             $selectCols[] = "
//                 COALESCE(
//                     ROUND(
//                         SUM(
//                             CAST(
//                                 REPLACE(NULLIF(kb.realisasi{$bln},''),'%','')
//                                 AS DECIMAL(10,4)
//                             )
//                         )
//                         / NULLIF(COUNT(*),0)
//                     , 2),
//                 0) AS realisasi{$bln}
//             ";
//         }

//         /* =========================================
//          * 5. AMBIL DATA BAWAHAN (RESPONSE)
//          * ========================================= */
//         $bawahan = DB::table('kpi_pegawai as kb')
//             ->join('data_pegawai as dp', function ($join) use ($nipAtasan) {
//                 $join->on('dp.nip', '=', 'kb.nip')
//                      ->where('dp.approval', '=', $nipAtasan);
//             })
//             ->where('kb.tahun', $tahun)
//             ->whereRaw('kb.kode_cascading2 LIKE CONCAT(?, "%")', [$kpa->kode_cascading2])
//             ->select('kb.nip','kb.kode_cascading2')
//             ->get();

//         /* =========================================
//          * 6. HITUNG AVG KPI BAWAHAN
//          * ========================================= */
//         $avg = DB::selectOne("
//             SELECT
//                 " . implode(", ", $selectCols) . "
//             FROM kpi_pegawai kb
//             JOIN data_pegawai dp
//                 ON dp.nip = kb.nip
//                AND dp.approval = ?
//             WHERE kb.tahun = ?
//               AND kb.kode_cascading2 LIKE CONCAT(?, '%')
//         ", [
//             $nipAtasan,
//             $tahun,
//             $kpa->kode_cascading2
//         ]);

//         /* =========================================
//          * 7. UPDATE KPI ATASAN
//          * ========================================= */
//         $updateData = [];

//         for ($i = $bulanStart; $i <= $bulanEnd; $i++) {
//             $bln = str_pad($i, 2, '0', STR_PAD_LEFT);

//             /* ===== nilai KN, KL, WK ===== */
//             foreach (['kn','kl','wk'] as $jenis) {
//                 $col = "nilai{$bln}{$jenis}";
//                 // hasil sudah numerik & max 2 desimal dari SQL
//                 $updateData[$col] = number_format($avg->$col, 2, '.', '').'%';
//             }

//             /* ===== realisasi ===== */
//             $realisasiCol = "realisasi{$bln}";
//             $updateData[$realisasiCol] = number_format($avg->$realisasiCol, 2, '.', '').'%';
//         }

//         DB::table('kpi_pegawai')
//             ->where('id', $kpa->id)
//             ->update($updateData);

//         /* =========================================
//          * 8. RESPONSE
//          * ========================================= */
//         $responseDetail[] = [
//             'kpi_atasan_id' => $kpa->id,
//             'kode_cascading2' => $kpa->kode_cascading2,
//             'jumlah_bawahan' => $bawahan->count(),
//             'bawahan' => $bawahan
//         ];
//     }

//     // return response()->json([
//     //     'status' => 'success',
//     //     'nip_atasan' => $nipAtasan,
//     //     'tahun' => $tahun,
//     //     'semester' => $semester,
//     //     'detail_kpi' => $responseDetail
//     // ]);
//     $response = response()->json([
//         'status' => 'success',
//         'nip_atasan' => $nipAtasan,
//         'tahun' => $tahun,
//         'semester' => $semester,
//         'detail_kpi' => $responseDetail
//     ]);
//     dd($response);
// }

// public function hitungApproval(Request $request)
// {
//     /* =========================================
//      * 1. PERIODE & SEMESTER
//      * ========================================= */
//     $nipAtasan = '8815012HPI';
    // $tahun_ini = date('Y');
    // $bulan_ini = date('m');

    // if ((int)$bulan_ini === 1) {
    //     $tahun = $tahun_ini - 1;
    //     $bulan = "12";
    // } else {
    //     $tahun = $tahun_ini;
    //     $bulan = str_pad((int)$bulan_ini - 1, 2, "0", STR_PAD_LEFT);
    // }

    // if ($bulan >= '01' && $bulan <= '06') {
    //     $semester   = 1;
    //     $bulanStart = 1;
    //     $bulanEnd   = 6;
    //     $semesterSuffix = 'semester1';
    // } else {
    //     $semester   = 2;
    //     $bulanStart = 7;
    //     $bulanEnd   = 12;
    //     $semesterSuffix = 'semester2';
    // }

//     /* =========================================
//      * 2. KPI ATASAN
//      * ========================================= */
//     $kpiAtasanList = DB::table('kpi_pegawai')
//         ->where('nip', $nipAtasan)
//         ->where('tahun', $tahun)
//         ->get();

//     if ($kpiAtasanList->isEmpty()) {
//         return response()->json([
//             'status' => 'gagal',
//             'pesan'  => 'KPI atasan tidak ditemukan'
//         ]);
//     }

//     $responseDetail = [];

//     foreach ($kpiAtasanList as $kpa) {

//         /* =========================================
//          * 4. BUILD KOLOM AVG BULANAN (TETAP)
//          * ========================================= */
//         $selectCols = [];

//         for ($i = $bulanStart; $i <= $bulanEnd; $i++) {
//             $bln = str_pad($i, 2, '0', STR_PAD_LEFT);

//             foreach (['kn','kl','wk'] as $jenis) {
//                 $col = "nilai{$bln}{$jenis}";
//                 $selectCols[] = "
//                     COALESCE(
//                         ROUND(
//                             SUM(
//                                 CAST(REPLACE(NULLIF(kb.$col,''),'%','') AS DECIMAL(10,4))
//                             ) / NULLIF(COUNT(*),0)
//                         ,2),
//                     0) AS $col
//                 ";
//             }

//             $selectCols[] = "
//                 COALESCE(
//                     ROUND(
//                         SUM(
//                             CAST(REPLACE(NULLIF(kb.realisasi{$bln},''),'%','') AS DECIMAL(10,4))
//                         ) / NULLIF(COUNT(*),0)
//                     ,2),
//                 0) AS realisasi{$bln}
//             ";
//         }

//         /* =========================================
//          * === TAMBAHAN SEMESTER (TANPA MERUBAH BULANAN)
//          * ========================================= */
//         $semesterSuffix = $semester == 1 ? 'semester1' : 'semester2';

//         $selectCols[] = "
//             COALESCE(
//                 ROUND(
//                     SUM(
//                         CAST(
//                             REPLACE(NULLIF(kb.kuantitas_$semesterSuffix,''),'%','')
//                             AS DECIMAL(10,4)
//                         )
//                     ) / NULLIF(COUNT(*),0)
//                 ,2),
//             0) AS kuantitas_$semesterSuffix
//         ";

//         $selectCols[] = "
//             COALESCE(
//                 ROUND(
//                     SUM(
//                         CAST(
//                             REPLACE(NULLIF(kb.kualitas_$semesterSuffix,''),'%','')
//                             AS DECIMAL(10,4)
//                         )
//                     ) / NULLIF(COUNT(*),0)
//                 ,2),
//             0) AS kualitas_$semesterSuffix
//         ";

//         $selectCols[] = "
//             COALESCE(
//                 ROUND(
//                     SUM(
//                         CAST(
//                             REPLACE(NULLIF(kb.waktu_$semesterSuffix,''),'%','')
//                             AS DECIMAL(10,4)
//                         )
//                     ) / NULLIF(COUNT(*),0)
//                 ,2),
//             0) AS waktu_$semesterSuffix
//         ";

//         $selectCols[] = "
//             COALESCE(
//                 ROUND(
//                     SUM(
//                         CAST(
//                             REPLACE(NULLIF(kb.nilai_$semesterSuffix,''),'%','')
//                             AS DECIMAL(10,4)
//                         )
//                     ) / NULLIF(COUNT(*),0)
//                 ,2),
//             0) AS nilai_$semesterSuffix
//         ";

//         /* =========================================
//          * 6. QUERY AVG
//          * ========================================= */
//         $avg = DB::selectOne("
//             SELECT
//                 " . implode(", ", $selectCols) . "
//             FROM kpi_pegawai kb
//             JOIN data_pegawai dp
//               ON dp.nip = kb.nip
//              AND dp.approval = ?
//             WHERE kb.tahun = ?
//               AND kb.kode_cascading2 LIKE CONCAT(?, '%')
//               AND kb.jenis_kpi = ?
//         ", [
//             $nipAtasan,
//             $tahun,
//             $kpa->kode_cascading2,
//             $kpa->jenis_kpi
//         ]);

//         /* =========================================
//          * 7. UPDATE KPI ATASAN
//          * ========================================= */
//         $updateData = [];

//         for ($i = $bulanStart; $i <= $bulanEnd; $i++) {
//             $bln = str_pad($i, 2, '0', STR_PAD_LEFT);

//             foreach (['kn','kl','wk'] as $jenis) {
//                 $col = "nilai{$bln}{$jenis}";
//                 $updateData[$col] = number_format($avg->$col, 2, '.', '') . '%';
//             }

//             $updateData["realisasi{$bln}"] =
//                 number_format($avg->{"realisasi{$bln}"}, 2, '.', '') . '%';
//         }

//         // === UPDATE SEMESTER (SEKALI SAJA)
//         $updateData["kuantitas_$semesterSuffix"]
//             = number_format($avg->{"kuantitas_$semesterSuffix"}, 2, '.', '') . '%';

//         $updateData["kualitas_$semesterSuffix"]
//             = number_format($avg->{"kualitas_$semesterSuffix"}, 2, '.', '') . '%';

//         $updateData["waktu_$semesterSuffix"]
//             = number_format($avg->{"waktu_$semesterSuffix"}, 2, '.', '') . '%';

//         $updateData["nilai_$semesterSuffix"]
//             = number_format($avg->{"nilai_$semesterSuffix"}, 2, '.', '') . '%';

//         DB::table('kpi_pegawai')
//             ->where('id', $kpa->id)
//             ->update($updateData);
//     }

//     return response()->json([
//         'status' => 'success',
//         'nip_atasan' => $nipAtasan,
//         'tahun' => $tahun,
//         'semester' => $semester
//     ]);
// }


// 

// public function hitungAtasan(Request $request)
// {
//     $nipAtasan = '6924002PRO';
//     $tahun = date('m') <= 6 ? date('Y') - 1 : date('Y');

//     /* ================== DATA ATASAN ================== */
//     $atasan = DB::table('data_pegawai')
//         ->select('jenis_kpi','kd_area')
//         ->where('nip', $nipAtasan)
//         ->first();

//     if (!$atasan) {
//         abort(404, 'Atasan tidak ditemukan');
//     }

//     /* ================== BAWAHAN ================== */
//     $bawahanNip = DB::table('data_pegawai')
//         ->where('kd_area', $atasan->kd_area)
//         ->where('jenis_kpi', $atasan->jenis_kpi)
//         ->where(function ($q) use ($nipAtasan) {
//             $q->where('approval', $nipAtasan)
//               ->orWhere(function ($q2) use ($nipAtasan) {
//                   $q2->where('finalisasi', $nipAtasan)
//                      ->where(function ($q3) {
//                          $q3->whereNull('approval')
//                             ->orWhere('approval','');
//                      });
//               });
//         })
//         ->pluck('nip');

//     if ($bawahanNip->isEmpty()) {
//         return response()->json(['message' => 'Tidak ada bawahan']);
//     }

//     $periodeList = ['07','08','09','10','11','12'];
//     $hasil = [];

//     /* ======================================================
//        KPI PEGAWAI (nilai07kn, nilai07kl, nilai07wk, realisasi07)
//     ====================================================== */
//     $kpiAtasanList = DB::table('kpi_pegawai')
//         ->where('nip', $nipAtasan)
//         ->where('tahun', $tahun)
//         ->get();

//     foreach ($kpiAtasanList as $kpiAtasan) {

//         $kpiBawahan = DB::table('kpi_pegawai')
//             ->whereIn('nip', $bawahanNip)
//             ->where('kode_cascading2', 'like', $kpiAtasan->kode_cascading2.'%')
//             ->where('tahun', $tahun)
//             ->get();

//         if ($kpiBawahan->isEmpty()) {
//             continue;
//         }

//         $rowUpdate = [];

//         foreach ($periodeList as $p) {
//             $total = ['kn'=>0,'kl'=>0,'wk'=>0,'realisasi'=>0];

//             foreach ($kpiBawahan as $row) {
//                 $total['kn'] += (float) $row->{'nilai'.$p.'kn'};
//                 $total['kl'] += (float) $row->{'nilai'.$p.'kl'};
//                 $total['wk'] += (float) $row->{'nilai'.$p.'wk'};
//                 $total['realisasi'] += (float) $row->{'realisasi'.$p};
//             }

//             $count = $kpiBawahan->count();

//             $rowUpdate['nilai'.$p.'kn'] = round($total['kn'] / $count, 2);
//             $rowUpdate['nilai'.$p.'kl'] = round($total['kl'] / $count, 2);
//             $rowUpdate['nilai'.$p.'wk'] = round($total['wk'] / $count, 2);
//             $rowUpdate['realisasi'.$p] = round($total['realisasi'] / $count, 2);
//         }

//         DB::table('kpi_pegawai')
//             ->where('id', $kpiAtasan->id)
//             ->update($rowUpdate);

//         $hasil['kpi_pegawai'][$kpiAtasan->kode_kpi] = $rowUpdate;
//     }

//     /* ======================================================
//        FINALISASI KPI (nilai07bkn, nilai07bkl, nilai07bwk, realisasi07b)
//     ====================================================== */
//     $finalisasiAtasanList = DB::table('finalisasi_kpi')
//         ->where('nip', $nipAtasan)
//         ->where('tahun', $tahun)
//         ->get();

//     foreach ($finalisasiAtasanList as $atasanFinal) {

//         $finalisasiBawahan = DB::table('finalisasi_kpi')
//             ->whereIn('nip', $bawahanNip)
//             ->where('kode_cascading2', 'like', $atasanFinal->kode_cascading2.'%')
//             ->where('tahun', $tahun)
//             ->get();

//         if ($finalisasiBawahan->isEmpty()) {
//             continue;
//         }

//         $rowUpdate = [];

//         foreach ($periodeList as $p) {
//             $total = ['bkn'=>0,'bkl'=>0,'bwk'=>0,'realisasi'=>0];

//             foreach ($finalisasiBawahan as $row) {
//                 $total['bkn'] += (float) $row->{'nilai'.$p.'bkn'};
//                 $total['bkl'] += (float) $row->{'nilai'.$p.'bkl'};
//                 $total['bwk'] += (float) $row->{'nilai'.$p.'bwk'};
//                 $total['realisasi'] += (float) $row->{'realisasi'.$p.'b'};
//             }

//             $count = $finalisasiBawahan->count();

//             $rowUpdate['nilai'.$p.'bkn'] = round($total['bkn'] / $count, 2);
//             $rowUpdate['nilai'.$p.'bkl'] = round($total['bkl'] / $count, 2);
//             $rowUpdate['nilai'.$p.'bwk'] = round($total['bwk'] / $count, 2);
//             $rowUpdate['realisasi'.$p.'b'] = round($total['realisasi'] / $count, 2);
//         }

//         DB::table('finalisasi_kpi')
//             ->where('id', $atasanFinal->id)
//             ->update($rowUpdate);

//         $hasil['finalisasi_kpi'][$atasanFinal->kode_kpi] = $rowUpdate;
//     }

//     return response()->json($hasil);
// }

// public function hitungAtasan(Request $request)
// {
//     $nipAtasan = '6924002PRO';
//     $atasan = DB::table('data_pegawai')
//         ->where('nip', $nipAtasan)
//         ->first();

//     if (!$atasan) {
//         return false;
//     }

//     $jenis_kpi_atasan = $atasan->jenis_kpi;
//     $kd_area_atasan  = $atasan->kd_area;

//     // =========================
//     // 2. AMBIL KPI BAWAHAN
//     // =========================
//     $kpiBawahan = DB::table('data_pegawai')
//         ->where('kd_area', $kd_area_atasan)
//         ->where('jenis_kpi', $jenis_kpi_atasan)
//         ->whereRaw("(approval = ? OR (finalisasi = ? AND approval = ''))", [
//             $nipAtasan,
//             $nipAtasan
//         ])
//         ->get();

//     if ($kpiBawahan->isEmpty()) {
//         return false;
//     }

//     // =========================
//     // 3. HELPER AMBIL NILAI VALID
//     // =========================
//     $getValidValues = function ($rows, $field) {
//         $values = [];
//         foreach ($rows as $r) {
//             if (isset($r->$field) && $r->$field !== '' && $r->$field !== null) {
//                 $values[] = (float) $r->$field;
//             }
//         }
//         return $values;
//     };

//     // =========================
//     // 4. HITUNG KPI PER PERIODE
//     // =========================
//     $periodeList = ['07','08','09','10','11','12'];
//     $rowUpdate   = [];

//     foreach ($periodeList as $p) {

//         $fieldKn = 'nilai'.$p.'kn';
//         $fieldKl = 'nilai'.$p.'kl';
//         $fieldWk = 'nilai'.$p.'wk';
//         $fieldRe = 'realisasi'.$p;

//         $valsKn = $getValidValues($kpiBawahan, $fieldKn);
//         $valsKl = $getValidValues($kpiBawahan, $fieldKl);
//         $valsWk = $getValidValues($kpiBawahan, $fieldWk);
//         $valsRe = $getValidValues($kpiBawahan, $fieldRe);

//         // ❌ SEMUA bawahan kosong → atasan NULL
//         if (empty($valsKn) && empty($valsKl) && empty($valsWk) && empty($valsRe)) {
//             $rowUpdate[$fieldKn] = null;
//             $rowUpdate[$fieldKl] = null;
//             $rowUpdate[$fieldWk] = null;
//             $rowUpdate[$fieldRe] = null;
//             continue;
//         }

//         // ✅ Hitung dari yang TERISI saja
//         $rowUpdate[$fieldKn] = !empty($valsKn) ? round(array_sum($valsKn) / count($valsKn), 2) : null;
//         $rowUpdate[$fieldKl] = !empty($valsKl) ? round(array_sum($valsKl) / count($valsKl), 2) : null;
//         $rowUpdate[$fieldWk] = !empty($valsWk) ? round(array_sum($valsWk) / count($valsWk), 2) : null;
//         $rowUpdate[$fieldRe] = !empty($valsRe) ? round(array_sum($valsRe) / count($valsRe), 2) : null;
//     }

//     // =========================
//     // 5. UPDATE KPI ATASAN
//     // =========================
//     DB::table('data_pegawai')
//         ->where('nip', $nipAtasan)
//         ->update($rowUpdate);

//     return true;
// }

// public function hitungAtasan(Request $request)
// {
//     $nipAtasan = '6924002PRO';

//     /* =====================================================
//      * 1. TENTUKAN PERIODE (SEMESTER)
//      * ===================================================== */
//     $tahun_ini = date('Y');
//     $bulan_ini = date('m');

//     if ((int)$bulan_ini <= 6) {
//         $tahun = $tahun_ini - 1;
//         $bulan = '12';
//     } else {
//         $tahun = $tahun_ini;
//         $bulan = str_pad((int)$bulan_ini - 1, 2, '0', STR_PAD_LEFT);
//     }

//     if ($bulan >= '01' && $bulan <= '06') {
//         $bulanStart = 1;
//         $bulanEnd   = 6;
//     } else {
//         $bulanStart = 7;
//         $bulanEnd   = 12;
//     }

//     $periodeList = [];
//     for ($i = $bulanStart; $i <= $bulanEnd; $i++) {
//         $periodeList[] = str_pad($i, 2, '0', STR_PAD_LEFT);
//     }

//     /* =====================================================
//      * 2. DATA ATASAN
//      * ===================================================== */
//     $atasan = DB::table('data_pegawai')
//         ->where('nip', $nipAtasan)
//         ->first();

//     if (!$atasan) {
//         return response()->json(['message' => 'Atasan tidak ditemukan'], 404);
//     }

//     /* =====================================================
//      * 3. AMBIL NIP BAWAHAN
//      * ===================================================== */
//     $bawahanNip = DB::table('data_pegawai')
//         ->where('kd_area', $atasan->kd_area)
//         ->where('jenis_kpi', $atasan->jenis_kpi)
//         ->where(function ($q) use ($nipAtasan) {
//             $q->where('approval', $nipAtasan)
//               ->orWhere(function ($q2) use ($nipAtasan) {
//                   $q2->where('finalisasi', $nipAtasan)
//                      ->where(function ($q3) {
//                          $q3->whereNull('approval')
//                             ->orWhere('approval', '');
//                      });
//               });
//         })
//         ->pluck('nip');

//     if ($bawahanNip->isEmpty()) {
//         return response()->json(['message' => 'Tidak ada bawahan']);
//     }

//     /* =====================================================
//      * 4. HELPER NILAI VALID
//      * ===================================================== */
//     $getValidValues = function ($rows, $field) {
//         $vals = [];
//         foreach ($rows as $r) {
//             if (isset($r->$field) && $r->$field !== '' && $r->$field !== null) {
//                 $vals[] = (float) $r->$field;
//             }
//         }
//         return $vals;
//     };

//     /* =====================================================
//      * 5. HITUNG KPI_PEGAWAI (ATASAN)
//      * ===================================================== */
//     $kpiAtasan = DB::table('kpi_pegawai')
//         ->where('nip', $nipAtasan)
//         ->where('tahun', $tahun)
//         ->first();

//     // $kpiBawahan = DB::table('kpi_pegawai')
//     //     ->whereIn('nip', $bawahanNip)
//     //     ->where('tahun', $tahun)
//     //     ->get();
//     $kodeCascadingAtasan = $kpiAtasan->kode_cascading2 ?? null;

//     // KPI BAWAHAN
//     $kpiBawahan = DB::table('kpi_pegawai')
//         ->whereIn('nip', $bawahanNip)
//         ->where('tahun', $tahun)
//         ->when($kodeCascadingAtasan, function ($query) use ($kodeCascadingAtasan) {
//             $query->where('kode_cascading2', 'like', $kodeCascadingAtasan . '%');
//         })
//         ->get();

//     $updateKpiPegawai = [];

//     foreach ($periodeList as $p) {

//         foreach (['kn','kl','wk'] as $suffix) {

//             $field = 'nilai'.$p.$suffix;
//             $vals  = $getValidValues($kpiBawahan, $field);

//             $updateKpiPegawai[$field] = empty($vals)
//                 ? ''
//                 : round(array_sum($vals) / count($vals), 2).'%';
//         }

//         $fieldRe = 'realisasi'.$p;
//         $valsRe  = $getValidValues($kpiBawahan, $fieldRe);

//         $updateKpiPegawai[$fieldRe] = empty($valsRe)
//             ? ''
//             : round(array_sum($valsRe) / count($valsRe), 2).'%';
//     }

//     if ($kpiAtasan) {
//         DB::table('kpi_pegawai')
//             ->where('id', $kpiAtasan->id)
//             ->update($updateKpiPegawai);
//     }

//     /* =====================================================
//      * 6. HITUNG FINALISASI_KPI (ATASAN)
//      * ===================================================== */
//     $finalAtasan = DB::table('finalisasi_kpi')
//         ->where('nip', $nipAtasan)
//         ->where('tahun', $tahun)
//         ->first();

//     // $finalBawahan = DB::table('finalisasi_kpi')
//     //     ->whereIn('nip', $bawahanNip)
//     //     ->where('tahun', $tahun)
//     //     ->get();

//     $kodeCascadingAtasan = $finalAtasan->kode_cascading2 ?? null;

//     // FINAL KPI BAWAHAN
//     $finalBawahan = DB::table('finalisasi_kpi')
//     ->whereIn('nip', $bawahanNip)
//     ->where('tahun', $tahun)
//     ->when($kodeCascadingAtasan, function ($query) use ($kodeCascadingAtasan) {
//         $query->where('kode_cascading2', 'like', $kodeCascadingAtasan . '%');
//     })
//     ->get();

//     $updateFinal = [];

//     foreach ($periodeList as $p) {

//         foreach (['bkn','bkl','bwk'] as $suffix) {

//             $field = 'nilai'.$p.$suffix;
//             $vals  = $getValidValues($finalBawahan, $field);

//             $updateFinal[$field] = empty($vals)
//                 ? ''
//                 : round(array_sum($vals) / count($vals), 2).'%';
//         }

//         $fieldRe = 'realisasi'.$p.'b';
//         $valsRe  = $getValidValues($finalBawahan, $fieldRe);

//         $updateFinal[$fieldRe] = empty($valsRe)
//             ? ''
//             : round(array_sum($valsRe) / count($valsRe), 2).'%';
//     }

//     if ($finalAtasan) {
//         DB::table('finalisasi_kpi')
//             ->where('id', $finalAtasan->id)
//             ->update($updateFinal);
//     }

//     return response()->json([
//         'message' => 'Perhitungan KPI atasan berhasil',
//         'periode' => $periodeList
//     ]);
// }

// public function hitungAtasan(Request $request)
// {
//     $nipAtasan = '6924002PRO';

//     /* =====================================================
//      * 1. TENTUKAN PERIODE (SEMESTER)
//      * ===================================================== */
//     $tahun_ini = date('Y');
//     $bulan_ini = date('m');

//     if ((int)$bulan_ini <= 6) {
//         $tahun = $tahun_ini - 1;
//         $bulan = '12';
//     } else {
//         $tahun = $tahun_ini;
//         $bulan = str_pad((int)$bulan_ini - 1, 2, '0', STR_PAD_LEFT);
//     }

//     if ($bulan >= '01' && $bulan <= '06') {
//         $bulanStart = 1;
//         $bulanEnd   = 6;
//     } else {
//         $bulanStart = 7;
//         $bulanEnd   = 12;
//     }

//     $periodeList = [];
//     for ($i = $bulanStart; $i <= $bulanEnd; $i++) {
//         $periodeList[] = str_pad($i, 2, '0', STR_PAD_LEFT);
//     }

//     /* =====================================================
//      * 2. DATA ATASAN
//      * ===================================================== */
//     $atasan = DB::table('data_pegawai')
//         ->where('nip', $nipAtasan)
//         ->first();

//     if (!$atasan) {
//         return response()->json(['message' => 'Atasan tidak ditemukan'], 404);
//     }

//     /* =====================================================
//      * 3. AMBIL NIP BAWAHAN
//      * ===================================================== */
//     $bawahanNip = DB::table('data_pegawai')
//         ->where('kd_area', $atasan->kd_area)
//         ->where('jenis_kpi', $atasan->jenis_kpi)
//         ->where(function ($q) use ($nipAtasan) {
//             $q->where('approval', $nipAtasan)
//               ->orWhere(function ($q2) use ($nipAtasan) {
//                   $q2->where('finalisasi', $nipAtasan)
//                      ->where(function ($q3) {
//                          $q3->whereNull('approval')
//                             ->orWhere('approval', '');
//                      });
//               });
//         })
//         ->pluck('nip');

//     if ($bawahanNip->isEmpty()) {
//         return response()->json(['message' => 'Tidak ada bawahan']);
//     }

//     /* =====================================================
//      * 4. HELPER NILAI VALID
//      * ===================================================== */
//     $getValidValues = function ($rows, $field) {
//         $vals = [];
//         foreach ($rows as $r) {
//             if (isset($r->$field) && $r->$field !== '' && $r->$field !== null) {
//                 $vals[] = (float) $r->$field;
//             }
//         }
//         return $vals;
//     };

//     /* =====================================================
//      * 5. HITUNG KPI_PEGAWAI (ATASAN)
//      * ===================================================== */
//     $jumlahUpdateKpiAtasan = 0;

//     $kpiAtasan = DB::table('kpi_pegawai')
//         ->where('nip', $nipAtasan)
//         ->where('tahun', $tahun)
//         ->first();

//     $kodeCascadingAtasan = $kpiAtasan->kode_cascading2 ?? null;

//     $kpiBawahan = DB::table('kpi_pegawai')
//         ->whereIn('nip', $bawahanNip)
//         ->where('tahun', $tahun)
//         ->when($kodeCascadingAtasan, function ($query) use ($kodeCascadingAtasan) {
//             $query->where('kode_cascading2', 'like', $kodeCascadingAtasan . '%');
//         })
//         ->get();

//     $updateKpiPegawai = [];

//     foreach ($periodeList as $p) {

//         foreach (['kn','kl','wk'] as $suffix) {
//             $field = 'nilai'.$p.$suffix;
//             $vals  = $getValidValues($kpiBawahan, $field);

//             $updateKpiPegawai[$field] = empty($vals)
//                 ? ''
//                 : round(array_sum($vals) / count($vals), 2) . '%';
//         }

//         $fieldRe = 'realisasi'.$p;
//         $valsRe  = $getValidValues($kpiBawahan, $fieldRe);

//         $updateKpiPegawai[$fieldRe] = empty($valsRe)
//             ? ''
//             : round(array_sum($valsRe) / count($valsRe), 2) . '%';
//     }

//     if ($kpiAtasan) {
//         $jumlahUpdateKpiAtasan = DB::table('kpi_pegawai')
//             ->where('id', $kpiAtasan->id)
//             ->update($updateKpiPegawai);
//     }

//     /* =====================================================
//      * 6. HITUNG FINALISASI_KPI (ATASAN)
//      * ===================================================== */
//     $jumlahUpdateFinalAtasan = 0;

//     $finalAtasan = DB::table('finalisasi_kpi')
//         ->where('nip', $nipAtasan)
//         ->where('tahun', $tahun)
//         ->first();

//     $kodeCascadingAtasan = $finalAtasan->kode_cascading2 ?? null;

//     $finalBawahan = DB::table('finalisasi_kpi')
//         ->whereIn('nip', $bawahanNip)
//         ->where('tahun', $tahun)
//         ->when($kodeCascadingAtasan, function ($query) use ($kodeCascadingAtasan) {
//             $query->where('kode_cascading2', 'like', $kodeCascadingAtasan . '%');
//         })
//         ->get();

//     $updateFinal = [];

//     foreach ($periodeList as $p) {

//         foreach (['bkn','bkl','bwk'] as $suffix) {
//             $field = 'nilai'.$p.$suffix;
//             $vals  = $getValidValues($finalBawahan, $field);

//             $updateFinal[$field] = empty($vals)
//                 ? ''
//                 : round(array_sum($vals) / count($vals), 2) . '%';
//         }

//         $fieldRe = 'realisasi'.$p.'b';
//         $valsRe  = $getValidValues($finalBawahan, $fieldRe);

//         $updateFinal[$fieldRe] = empty($valsRe)
//             ? ''
//             : round(array_sum($valsRe) / count($valsRe), 2) . '%';
//     }

//     if ($finalAtasan) {
//         $jumlahUpdateFinalAtasan = DB::table('finalisasi_kpi')
//             ->where('id', $finalAtasan->id)
//             ->update($updateFinal);
//     }

//     /* =====================================================
//      * 7. RESPONSE
//      * ===================================================== */
//     return response()->json([
//         'message' => 'Perhitungan KPI atasan berhasil',
//         'tahun'   => $tahun,
//         'periode' => $periodeList,
//         'update'  => [
//             'kpi_atasan'        => $jumlahUpdateKpiAtasan,
//             'finalisasi_atasan' => $jumlahUpdateFinalAtasan
//         ]
//     ]);
// }

// public function hitungAtasan(Request $request)
// {
//     $nipAtasan = '6924002PRO';

//     /* =====================================================
//      * 1. TENTUKAN PERIODE (SEMESTER)
//      * ===================================================== */
//     $tahun_ini = date('Y');
//     $bulan_ini = date('m');

//     if ((int)$bulan_ini <= 6) {
//         $tahun = $tahun_ini - 1;
//         $bulan = '12';
//     } else {
//         $tahun = $tahun_ini;
//         $bulan = str_pad((int)$bulan_ini - 1, 2, '0', STR_PAD_LEFT);
//     }

//     if ($bulan >= '01' && $bulan <= '06') {
//         $bulanStart = 1;
//         $bulanEnd   = 6;
//     } else {
//         $bulanStart = 7;
//         $bulanEnd   = 12;
//     }

//     $periodeList = [];
//     for ($i = $bulanStart; $i <= $bulanEnd; $i++) {
//         $periodeList[] = str_pad($i, 2, '0', STR_PAD_LEFT);
//     }

//     /* =====================================================
//      * 2. DATA ATASAN
//      * ===================================================== */
//     $atasan = DB::table('data_pegawai')
//         ->where('nip', $nipAtasan)
//         ->first();

//     if (!$atasan) {
//         return response()->json(['message' => 'Atasan tidak ditemukan'], 404);
//     }

//     /* =====================================================
//      * 3. AMBIL NIP BAWAHAN
//      * ===================================================== */
//     $bawahanNip = DB::table('data_pegawai')
//         ->where('kd_area', $atasan->kd_area)
//         ->where('jenis_kpi', $atasan->jenis_kpi)
//         ->where(function ($q) use ($nipAtasan) {
//             $q->where('approval', $nipAtasan)
//               ->orWhere(function ($q2) use ($nipAtasan) {
//                   $q2->where('finalisasi', $nipAtasan)
//                      ->where(function ($q3) {
//                          $q3->whereNull('approval')
//                             ->orWhere('approval', '');
//                      });
//               });
//         })
//         ->pluck('nip');

//     if ($bawahanNip->isEmpty()) {
//         return response()->json(['message' => 'Tidak ada bawahan']);
//     }

//     /* =====================================================
//      * 4. HELPER NILAI VALID
//      * ===================================================== */
//     $getValidValues = function ($rows, $field) {
//         $vals = [];
//         foreach ($rows as $r) {
//             if (isset($r->$field) && $r->$field !== '' && $r->$field !== null) {
//                 $vals[] = (float)$r->$field;
//             }
//         }
//         return $vals;
//     };

//     /* =====================================================
//      * 5. KPI_PEGAWAI ATASAN (PER KODE_KPI)
//      * ===================================================== */
//     $kpiAtasanList = DB::table('kpi_pegawai')
//         ->where('nip', $nipAtasan)
//         ->where('tahun', $tahun)
//         ->get();

//     if ($kpiAtasanList->isEmpty()) {
//         return response()->json(['message' => 'Tidak ada KPI atasan']);
//     }

//     $statusKpiAtasan = [];

//     foreach ($kpiAtasanList as $kpiAtasan) {

//         $kodeKpi = $kpiAtasan->kode_kpi;
//         $kodeCascadingAtasan = $kpiAtasan->kode_cascading2 ?? null;

//         $kpiBawahan = DB::table('kpi_pegawai')
//             ->whereIn('nip', $bawahanNip)
//             ->where('tahun', $tahun)
//             ->when($kodeCascadingAtasan, function ($q) use ($kodeCascadingAtasan) {
//                 $q->where('kode_cascading2', 'like', $kodeCascadingAtasan . '%');
//             })
//             ->get();

//         $updateData = [];
//         $changed = false;

//         foreach ($periodeList as $p) {

//             foreach (['kn','kl','wk'] as $suffix) {
//                 $field = 'nilai'.$p.$suffix;
//                 $vals  = $getValidValues($kpiBawahan, $field);

//                 $newVal = empty($vals) ? '' : round(array_sum($vals)/count($vals), 2).'%';

//                 if ($kpiAtasan->$field != $newVal) {
//                     $changed = true;
//                 }

//                 $updateData[$field] = $newVal;
//             }

//             $fieldRe = 'realisasi'.$p;
//             $valsRe  = $getValidValues($kpiBawahan, $fieldRe);

//             $newRe = empty($valsRe) ? '' : round(array_sum($valsRe)/count($valsRe), 2).'%';

//             if ($kpiAtasan->$fieldRe != $newRe) {
//                 $changed = true;
//             }

//             $updateData[$fieldRe] = $newRe;
//         }

//         $rows = 0;
//         if ($changed) {
//             $rows = DB::table('kpi_pegawai')
//                 ->where('id', $kpiAtasan->id)
//                 ->update($updateData);
//         }

//         $statusKpiAtasan[] = [
//             'kode_kpi' => $kodeKpi,
//             'status'   => !$changed ? 'not_changed' : ($rows > 0 ? 'updated' : 'failed'),
//             'fields'   => count($updateData)
//         ];
//     }

//     /* =====================================================
//      * 6. FINALISASI_KPI ATASAN (PER KODE_KPI)
//      * ===================================================== */
//     $finalAtasanList = DB::table('finalisasi_kpi')
//         ->where('nip', $nipAtasan)
//         ->where('tahun', $tahun)
//         ->get();

//     $statusFinalAtasan = [];

//     foreach ($finalAtasanList as $finalAtasan) {

//         $kodeKpi = $finalAtasan->kode_kpi;
//         $kodeCascadingAtasan = $finalAtasan->kode_cascading2 ?? null;

//         $finalBawahan = DB::table('finalisasi_kpi')
//             ->whereIn('nip', $bawahanNip)
//             ->where('tahun', $tahun)
//             ->when($kodeCascadingAtasan, function ($q) use ($kodeCascadingAtasan) {
//                 $q->where('kode_cascading2', 'like', $kodeCascadingAtasan . '%');
//             })
//             ->get();

//         $updateData = [];
//         $changed = false;

//         foreach ($periodeList as $p) {

//             foreach (['bkn','bkl','bwk'] as $suffix) {
//                 $field = 'nilai'.$p.$suffix;
//                 $vals  = $getValidValues($finalBawahan, $field);

//                 $newVal = empty($vals) ? '' : round(array_sum($vals)/count($vals), 2).'%';

//                 if ($finalAtasan->$field != $newVal) {
//                     $changed = true;
//                 }

//                 $updateData[$field] = $newVal;
//             }

//             $fieldRe = 'realisasi'.$p.'b';
//             $valsRe  = $getValidValues($finalBawahan, $fieldRe);

//             $newRe = empty($valsRe) ? '' : round(array_sum($valsRe)/count($valsRe), 2).'%';

//             if ($finalAtasan->$fieldRe != $newRe) {
//                 $changed = true;
//             }

//             $updateData[$fieldRe] = $newRe;
//         }

//         $rows = 0;
//         if ($changed) {
//             $rows = DB::table('finalisasi_kpi')
//                 ->where('id', $finalAtasan->id)
//                 ->update($updateData);
//         }

//         $statusFinalAtasan[] = [
//             'kode_kpi' => $kodeKpi,
//             'status'   => !$changed ? 'not_changed' : ($rows > 0 ? 'updated' : 'failed'),
//             'fields'   => count($updateData)
//         ];
//     }

//     /* =====================================================
//      * 7. RESPONSE
//      * ===================================================== */
//     return response()->json([
//         'message' => 'Perhitungan KPI atasan selesai',
//         'tahun'   => $tahun,
//         'periode' => $periodeList,
//         'kpi_pegawai' => $statusKpiAtasan,
//         'finalisasi'  => $statusFinalAtasan
//     ]);
// }

public function hitungAtasan(Request $request)
{
    $nipAtasan = '6924002PRO';
    // $nipAtasan = $request->nip;
    /* =====================================================
     * 1. TENTUKAN PERIODE & SEMESTER AKTIF
     * ===================================================== */
    $tahun_ini = date('Y');
    $bulan_ini = (int)date('m');

    if ($bulan_ini <= 6) {
        $tahun = $tahun_ini - 1;
        $semesterAktif = 2;
        $periodeList = ['07','08','09','10','11','12'];
    } else {
        $tahun = $tahun_ini;
        $semesterAktif = 1;
        $periodeList = ['01','02','03','04','05','06'];
    }

    /* =====================================================
     * 2. DATA ATASAN
     * ===================================================== */
    $atasan = DB::table('data_pegawai')
        ->where('nip', $nipAtasan)
        ->first();

    if (!$atasan) {
        return response()->json(['message' => 'Atasan tidak ditemukan'], 404);
    }

    /* =====================================================
     * 3. AMBIL NIP BAWAHAN
     * ===================================================== */
    // $bawahanNip = DB::table('data_pegawai')
    //     ->where('kd_area', $atasan->kd_area)
    //     ->where('jenis_kpi', $atasan->jenis_kpi)
    //     ->where(function ($q) use ($nipAtasan) {
    //         $q->where('approval', $nipAtasan)
    //           ->orWhere(function ($q2) use ($nipAtasan) {
    //               $q2->where('finalisasi', $nipAtasan)
    //                  ->where(function ($q3) {
    //                      $q3->whereNull('approval')
    //                         ->orWhere('approval', '');
    //                  });
    //           });
    //     })
    //     ->pluck('nip');
    $bawahanNip = DB::table('data_pegawai')
        ->where('kd_area', $atasan->kd_area)
        ->where('jenis_kpi', $atasan->jenis_kpi)
        ->where('approval', $nipAtasan)
        ->pluck('nip');
    // $bawahanNip = DB::table('data_pegawai')
    //     ->whereRaw("nip='8815012HPI' or nip='8205016TRK'")
    //     ->pluck('nip');

    if ($bawahanNip->isEmpty()) {
        return response()->json(['message' => 'Tidak ada bawahan']);
    }

    /* =====================================================
     * 4. HELPER AVG
     * ===================================================== */
    $avg = function ($rows, $field) {
        $vals = [];
        foreach ($rows as $r) {
            if (isset($r->$field) && $r->$field !== '' && $r->$field !== null) {
                $vals[] = (float)$r->$field;
            }
        }
        return empty($vals) ? null : round(array_sum($vals) / count($vals), 2);
    };

    /* =====================================================
     * 5. KPI_PEGAWAI ATASAN (BULANAN + SEMESTER)
     * ===================================================== */
    $kpiAtasanList = DB::table('kpi_pegawai')
        ->where('nip', $nipAtasan)
        ->where('tahun', $tahun)
        // ->where('kode_cascading2', '15101')
        ->get();

    $statusKpiAtasan = [];

    foreach ($kpiAtasanList as $kpiAtasan) {

        $kpiBawahan = DB::table('kpi_pegawai')
            ->whereIn('nip', $bawahanNip)
            ->where('jenis_kpi', $atasan->jenis_kpi)
            ->where('tahun', $tahun)
            ->where('kode_cascading2', 'like', $kpiAtasan->kode_cascading2 . '%')
            ->get();

        $updateData = [];
        $changed = false;

        /* ===== BULANAN (TETAP ADA) ===== */
        foreach ($periodeList as $p) {
            foreach (['kn','kl','wk'] as $s) {
                $field = 'nilai'.$p.$s;
                $new = $avg($kpiBawahan, $field);
                $new = $new === null ? '' : $new.'%';

                if ($kpiAtasan->$field != $new) $changed = true;
                $updateData[$field] = $new;
            }

            $fieldRe = 'realisasi'.$p;
            $newRe = $avg($kpiBawahan, $fieldRe);
            $newRe = $newRe === null ? '' : $newRe.'%';

            if ($kpiAtasan->$fieldRe != $newRe) $changed = true;
            $updateData[$fieldRe] = $newRe;
        }

        /* ===== TAMBAHAN SEMESTER ===== */
        // $mapSemester = [
        //     'kuantitas' => 'kn',
        //     'kualitas'  => 'kl',
        //     'waktu'     => 'wk',
        //     'nilai'     => null
        // ];

        // foreach ($mapSemester as $label => $suffix) {
        //     $vals = [];
        //     foreach ($periodeList as $p) {
        //         $field = $suffix ? 'nilai'.$p.$suffix : 'realisasi'.$p;
        //         $v = $avg($kpiBawahan, $field);
        //         if ($v !== null) $vals[] = $v;
        //     }

        //     $semesterField = $label.'_semester'.$semesterAktif;
        //     $newSemester = empty($vals) ? '' : round(array_sum($vals) / count($vals), 2).'%';

        //     if ($kpiAtasan->$semesterField != $newSemester) $changed = true;
        //     $updateData[$semesterField] = $newSemester;
        // }
        $mapSemester = [
            'kuantitas' => 'kn',
            'kualitas'  => 'kl',
            'waktu'     => 'wk',
            'nilai'     => null
        ];

        foreach ($mapSemester as $label => $suffix) {
            // Tentukan field semester yang akan diupdate
            $semesterField = $label . '_semester' . $semesterAktif;
            
            // Ambil semua nilai dari bawahan untuk field yang sama
            $vals = [];
            foreach ($kpiBawahan as $bawahan) {
                $value = $bawahan->$semesterField ?? null;
                
                // Bersihkan nilai (hapus %, convert ke float)
                if ($value !== null && $value !== '') {
                    $cleanValue = (float) str_replace('%', '', $value);
                    if ($cleanValue > 0) {
                        $vals[] = $cleanValue;
                    }
                }
            }
            
            // Hitung rata-rata
            $newSemester = empty($vals) ? '' : round(array_sum($vals) / count($vals), 2) . '%';
            
            // Cek perubahan
            if ($kpiAtasan->$semesterField != $newSemester) {
                $changed = true;
            }
            
            $updateData[$semesterField] = $newSemester;
        }

        if ($changed) {
            DB::table('kpi_pegawai')
                ->where('id', $kpiAtasan->id)
                ->update($updateData);
        }

        $statusKpiAtasan[] = [
            'kode_kpi' => $kpiAtasan->kode_kpi,
            'status'   => $changed ? 'updated' : 'not_changed'
        ];
    }

    /* =====================================================
     * 6. FINALISASI_KPI ATASAN (BULANAN + SEMESTER)
     * ===================================================== */
    $finalAtasanList = DB::table('finalisasi_kpi')
        ->where('nip', $nipAtasan)
        ->where('tahun', $tahun)
        // ->where('kode_cascading2', '15101')
        ->get();

    $statusFinalAtasan = [];

    foreach ($finalAtasanList as $finalAtasan) {

        $finalBawahan = DB::table('finalisasi_kpi')
            ->whereIn('nip', $bawahanNip)
            ->where('jenis_kpi', $atasan->jenis_kpi)
            ->where('tahun', $tahun)
            ->where('kode_cascading2', 'like', $finalAtasan->kode_cascading2 . '%')
            ->get();

        $updateData = [];
        $changed = false;

        /* ===== BULANAN (TETAP ADA) ===== */
        foreach ($periodeList as $p) {
            foreach (['bkn','bkl','bwk'] as $s) {
                $field = 'nilai'.$p.$s;
                $new = $avg($finalBawahan, $field);
                $new = $new === null ? '' : $new.'%';

                if ($finalAtasan->$field != $new) $changed = true;
                $updateData[$field] = $new;
            }

            $fieldRe = 'realisasi'.$p.'b';
            $newRe = $avg($finalBawahan, $fieldRe);
            $newRe = $newRe === null ? '' : $newRe.'%';

            if ($finalAtasan->$fieldRe != $newRe) $changed = true;
            $updateData[$fieldRe] = $newRe;
        }

        /* ===== TAMBAHAN SEMESTER ===== */
        // $mapFinalSemester = [
        //     'kuantitasb' => 'bkn',
        //     'kualitasb'  => 'bkl',
        //     'waktub'     => 'bwk',
        //     'nilaib'     => 'realisasib'
        // ];

        // foreach ($mapFinalSemester as $semesterFieldPrefix => $suffix) {

        //     $vals = [];

        //     foreach ($periodeList as $p) {

        //         $field = ($suffix === 'realisasib')
        //             ? 'realisasi'.$p.'b'
        //             : 'nilai'.$p.$suffix;

        //         $v = $avg($finalBawahan, $field);

        //         if ($v !== null) {
        //             $vals[] = $v;
        //         }
        //     }

        //     // nama kolom semester final
        //     $semesterField = $semesterFieldPrefix.'_semester'.$semesterAktif;

        //     // jika tidak ada data valid → JANGAN update (biar tidak NULL)
        //     // if (empty($vals)) {
        //     //     continue;
        //     // }

        //     // $newSemester = round(array_sum($vals) / count($vals), 2);
        //     $newSemester = empty($vals) ? '' : round(array_sum($vals) / count($vals), 2).'%';

        //     if ($finalAtasan->$semesterField != $newSemester) {
        //         $changed = true;
        //         $updateData[$semesterField] = $newSemester;
        //     }
        // }
        $mapFinalSemester = [
            'kuantitasb' => 'bkn',
            'kualitasb'  => 'bkl',
            'waktub'     => 'bwk',
            'nilaib'     => 'realisasib'
        ];

        foreach ($mapFinalSemester as $semesterFieldPrefix => $suffix) {
            // Tentukan field semester yang akan diupdate
            $semesterField = $semesterFieldPrefix . '_semester' . $semesterAktif;
            
            // Ambil semua nilai dari bawahan untuk field yang sama
            $vals = [];
            foreach ($finalBawahan as $bawahan) {
                $value = $bawahan->$semesterField ?? null;
                
                // Bersihkan nilai (hapus %, convert ke float)
                if ($value !== null && $value !== '') {
                    $cleanValue = (float) str_replace('%', '', $value);
                    if ($cleanValue > 0) {
                        $vals[] = $cleanValue;
                    }
                }
            }
            
            // Hitung rata-rata
            $newSemester = empty($vals) ? '' : round(array_sum($vals) / count($vals), 2) . '%';
            
            // Cek perubahan dan update
            if ($finalAtasan->$semesterField != $newSemester) {
                $changed = true;
            }
            
            $updateData[$semesterField] = $newSemester;
        }

        if ($changed) {
            DB::table('finalisasi_kpi')
                ->where('id', $finalAtasan->id)
                ->update($updateData);
        }

        $statusFinalAtasan[] = [
            'kode_kpi' => $finalAtasan->kode_kpi,
            'status'   => $changed ? 'updated' : 'not_changed'
        ];
    }

    /* =====================================================
     * 7. RESPONSE
     * ===================================================== */
    return response()->json([
        'message' => 'Perhitungan KPI atasan selesai',
        'tahun'   => $tahun,
        'semester'=> $semesterAktif,
        'kpi_pegawai' => $statusKpiAtasan,
        'finalisasi'  => $statusFinalAtasan
    ]);
}



// public static function hitungAtasan(Request $request)
// {
//     // $nip_atasan = $request->nip;

//     $nipAtasan = '6924002PRO';

//     $data = DB::table('kpi_pegawai as ka')
//     ->join('data_pegawai as dp', 'dp.approval', '=', 'ka.nip')
//     ->join('kpi_pegawai as kb', function ($join) {
//         $join->on('kb.nip', '=', 'dp.nip')
//              ->whereRaw("kb.kode_cascading2 LIKE CONCAT(ka.kode_cascading2,'%')");
//     })
//     ->where('ka.nip', $nipAtasan)
//     ->groupBy('ka.id', 'ka.kode_kpi', 'ka.kode_cascading2')
//     ->select([
//         'ka.kode_kpi',
//         'ka.kode_cascading2',

//         DB::raw('AVG(kb.nilai01kn) as avg_nilai01kn'),
//         DB::raw('AVG(kb.nilai01kl) as avg_nilai01kl'),
//         DB::raw('AVG(kb.nilai01wk) as avg_nilai01wk'),

//         DB::raw('AVG(kb.nilai02kn) as avg_nilai02kn'),
//         DB::raw('AVG(kb.nilai02kl) as avg_nilai02kl'),
//         DB::raw('AVG(kb.nilai02wk) as avg_nilai02wk'),

//         DB::raw('AVG(kb.nilai_semester1) as avg_nilai_semester1'),
//         DB::raw('AVG(kb.nilai_semester2) as avg_nilai_semester2'),
//     ])
//     ->get();
//     dd($data);
    
// }

public function hitungAtasanAll(Request $request)
{
    /* =========================================
     * 1. PERIODE & SEMESTER
     * ========================================= */
    // $nipAtasan = '8815012HPI';
    $tahun_ini = date('Y');
    $bulan_ini = date('m');

    if ((int)$bulan_ini <= 6) {
        $tahun = $tahun_ini - 1;
        $bulan = "12";
    } else {
        $tahun = $tahun_ini;
        $bulan = str_pad((int)$bulan_ini - 1, 2, "0", STR_PAD_LEFT);
    }

    if ($bulan >= '01' && $bulan <= '06') {
        $semester   = 1;
        $bulanStart = 1;
        $bulanEnd   = 6;
        $semesterSuffix = 'semester1';
    } else {
        $semester   = 2;
        $bulanStart = 7;
        $bulanEnd   = 12;
        $semesterSuffix = 'semester2';
    }

    $atasanList = DB::select("
        SELECT a.nip, b.jenis_kpi, b.level_kpi
        FROM (
            SELECT approval AS nip
            FROM data_pegawai
            WHERE approval <> ''
            AND aktif = '1'
            AND payroll = '1'
            AND aktif_simkp = '1'

            UNION ALL

            SELECT finalisasi AS nip
            FROM data_pegawai
            WHERE finalisasi <> ''
            AND aktif = '1'
            AND payroll = '1'
            AND aktif_simkp = '1'
        ) a
        INNER JOIN data_pegawai b
            ON a.nip = b.nip
        AND (
                (b.jenis_kpi = 'pusat' AND b.level_kpi >= '3')
                OR b.jenis_kpi <> 'pusat'
        )
        GROUP BY a.nip
        ORDER BY b.jenis_kpi, b.level_kpi DESC
    ");

    foreach ($atasanList as $atasan) {
        $nipAtasan = $atasan->nip;

        /* =========================================
        * 2. KPI ATASAN
        * ========================================= */
        $kpiAtasanList = DB::table('kpi_pegawai')
            ->where('nip', $nipAtasan)
            ->where('tahun', $tahun)
            ->get();

        if ($kpiAtasanList->isEmpty()) {
            return response()->json([
                'status' => 'gagal',
                'pesan'  => 'KPI atasan tidak ditemukan'
            ]);
        }

        $responseDetail = [];

        foreach ($kpiAtasanList as $kpa) {

            /* =========================================
            * 4. BUILD KOLOM AVG BULANAN (TETAP)
            * ========================================= */
            $selectCols = [];

            for ($i = $bulanStart; $i <= $bulanEnd; $i++) {
                $bln = str_pad($i, 2, '0', STR_PAD_LEFT);

                foreach (['kn','kl','wk'] as $jenis) {
                    $col = "nilai{$bln}{$jenis}";
                    $selectCols[] = "
                        COALESCE(
                            ROUND(
                                SUM(
                                    CAST(REPLACE(NULLIF(kb.$col,''),'%','') AS DECIMAL(10,4))
                                ) / NULLIF(COUNT(*),0)
                            ,2),
                        0) AS $col
                    ";
                }

                $selectCols[] = "
                    COALESCE(
                        ROUND(
                            SUM(
                                CAST(REPLACE(NULLIF(kb.realisasi{$bln},''),'%','') AS DECIMAL(10,4))
                            ) / NULLIF(COUNT(*),0)
                        ,2),
                    0) AS realisasi{$bln}
                ";
            }


            /* =========================================
            * === TAMBAHAN SEMESTER (TANPA MERUBAH BULANAN)
            * ========================================= */
            $semesterSuffix = $semester == 1 ? 'semester1' : 'semester2';

            $selectCols[] = "
                COALESCE(
                    ROUND(
                        SUM(
                            CAST(
                                REPLACE(NULLIF(kb.kuantitas_$semesterSuffix,''),'%','')
                                AS DECIMAL(10,4)
                            )
                        ) / NULLIF(COUNT(*),0)
                    ,2),
                0) AS kuantitas_$semesterSuffix
            ";

            $selectCols[] = "
                COALESCE(
                    ROUND(
                        SUM(
                            CAST(
                                REPLACE(NULLIF(kb.kualitas_$semesterSuffix,''),'%','')
                                AS DECIMAL(10,4)
                            )
                        ) / NULLIF(COUNT(*),0)
                    ,2),
                0) AS kualitas_$semesterSuffix
            ";

            $selectCols[] = "
                COALESCE(
                    ROUND(
                        SUM(
                            CAST(
                                REPLACE(NULLIF(kb.waktu_$semesterSuffix,''),'%','')
                                AS DECIMAL(10,4)
                            )
                        ) / NULLIF(COUNT(*),0)
                    ,2),
                0) AS waktu_$semesterSuffix
            ";

            $selectCols[] = "
                COALESCE(
                    ROUND(
                        SUM(
                            CAST(
                                REPLACE(NULLIF(kb.nilai_$semesterSuffix,''),'%','')
                                AS DECIMAL(10,4)
                            )
                        ) / NULLIF(COUNT(*),0)
                    ,2),
                0) AS nilai_$semesterSuffix
            ";

            
            /* FINALISASI KPI */
            $selectColsFinal = [];

            for ($i = $bulanStart; $i <= $bulanEnd; $i++) {
                $bln = str_pad($i, 2, '0', STR_PAD_LEFT);

                foreach (['kn','kl','wk'] as $jenis) {
                    $col = "nilai{$bln}b{$jenis}";
                    $selectColsFinal[] = "
                        COALESCE(
                            ROUND(
                                SUM(
                                    CAST(REPLACE(NULLIF(kb.$col,''),'%','') AS DECIMAL(10,4))
                                ) / NULLIF(COUNT(*),0)
                            ,2),
                        0) AS $col
                    ";
                }

                $selectColsFinal[] = "
                    COALESCE(
                        ROUND(
                            SUM(
                                CAST(REPLACE(NULLIF(kb.realisasi{$bln}b,''),'%','') AS DECIMAL(10,4))
                            ) / NULLIF(COUNT(*),0)
                        ,2),
                    0) AS realisasi{$bln}b
                ";
            }

            $selectColsFinal[] = "
                COALESCE(
                    ROUND(
                        SUM(
                            CAST(REPLACE(NULLIF(kb.kuantitasb_$semesterSuffix,''),'%','') AS DECIMAL(10,4))
                        ) / NULLIF(COUNT(*),0)
                    ,2),
                0) AS kuantitasb_$semesterSuffix
            ";

            $selectColsFinal[] = "
                COALESCE(
                    ROUND(
                        SUM(
                            CAST(REPLACE(NULLIF(kb.kualitasb_$semesterSuffix,''),'%','') AS DECIMAL(10,4))
                        ) / NULLIF(COUNT(*),0)
                    ,2),
                0) AS kualitasb_$semesterSuffix
            ";

            $selectColsFinal[] = "
                COALESCE(
                    ROUND(
                        SUM(
                            CAST(REPLACE(NULLIF(kb.waktub_$semesterSuffix,''),'%','') AS DECIMAL(10,4))
                        ) / NULLIF(COUNT(*),0)
                    ,2),
                0) AS waktub_$semesterSuffix
            ";

            $selectColsFinal[] = "
                COALESCE(
                    ROUND(
                        SUM(
                            CAST(REPLACE(NULLIF(kb.nilaib_$semesterSuffix,''),'%','') AS DECIMAL(10,4))
                        ) / NULLIF(COUNT(*),0)
                    ,2),
                0) AS nilaib_$semesterSuffix
            ";

            /* =========================================
            * 6. QUERY AVG
            * ========================================= */
            $avg = DB::selectOne("
                SELECT
                    " . implode(", ", $selectCols) . "
                FROM kpi_pegawai kb
                JOIN data_pegawai dp
                ON dp.nip = kb.nip
                AND dp.approval = ?
                WHERE kb.tahun = ?
                AND kb.kode_cascading2 LIKE CONCAT(?, '%')
                AND kb.jenis_kpi = ?
            ", [
                $nipAtasan,
                $tahun,
                $kpa->kode_cascading2,
                $kpa->jenis_kpi
            ]);

            /* =========================================
            * 6. QUERY AVG FINALISASI kpi
            * ========================================= */

            $avgFinal = DB::selectOne("
                SELECT
                    " . implode(", ", $selectColsFinal) . "
                FROM finalisasi_kpi kb
                JOIN data_pegawai dp
                ON dp.nip = kb.nip
                AND dp.approval = ?
                WHERE kb.tahun = ?
                AND kb.kode_cascading2 LIKE CONCAT(?, '%')
                AND kb.jenis_kpi = ?
            ", [
                $nipAtasan,
                $tahun,
                $kpa->kode_cascading2,
                $kpa->jenis_kpi
            ]);

            /* =========================================
            * 7. UPDATE KPI ATASAN
            * ========================================= */
            $updateData = [];

            for ($i = $bulanStart; $i <= $bulanEnd; $i++) {
                $bln = str_pad($i, 2, '0', STR_PAD_LEFT);

                foreach (['kn','kl','wk'] as $jenis) {
                    $col = "nilai{$bln}{$jenis}";
                    $updateData[$col] = number_format($avg->$col, 2, '.', '') . '%';
                }

                $updateData["realisasi{$bln}"] =
                    number_format($avg->{"realisasi{$bln}"}, 2, '.', '') . '%';
            }

            // === UPDATE SEMESTER (SEKALI SAJA)
            $updateData["kuantitas_$semesterSuffix"]
                = number_format($avg->{"kuantitas_$semesterSuffix"}, 2, '.', '') . '%';

            $updateData["kualitas_$semesterSuffix"]
                = number_format($avg->{"kualitas_$semesterSuffix"}, 2, '.', '') . '%';

            $updateData["waktu_$semesterSuffix"]
                = number_format($avg->{"waktu_$semesterSuffix"}, 2, '.', '') . '%';

            $updateData["nilai_$semesterSuffix"]
                = number_format($avg->{"nilai_$semesterSuffix"}, 2, '.', '') . '%';

            /* UPDATE FINALISASI KPI */
            $updateFinal = [];

            for ($i = $bulanStart; $i <= $bulanEnd; $i++) {
                $bln = str_pad($i, 2, '0', STR_PAD_LEFT);

                foreach (['kn','kl','wk'] as $jenis) {
                    $col = "nilai{$bln}b{$jenis}";
                    $updateFinal[$col] = number_format($avgFinal->$col, 2, '.', '') . '%';
                }

                $updateFinal["realisasi{$bln}b"] =
                    number_format($avgFinal->{"realisasi{$bln}b"}, 2, '.', '') . '%';
            }

            $updateFinal["kuantitasb_$semesterSuffix"]
                = number_format($avgFinal->{"kuantitasb_$semesterSuffix"}, 2, '.', '') . '%';

            $updateFinal["kualitasb_$semesterSuffix"]
                = number_format($avgFinal->{"kualitasb_$semesterSuffix"}, 2, '.', '') . '%';

            $updateFinal["waktub_$semesterSuffix"]
                = number_format($avgFinal->{"waktub_$semesterSuffix"}, 2, '.', '') . '%';

            $updateFinal["nilaib_$semesterSuffix"]
                = number_format($avgFinal->{"nilaib_$semesterSuffix"}, 2, '.', '') . '%';

            DB::table('kpi_pegawai')
            ->where('kode_kpi', $kpa->kode_kpi)
            ->update($updateData);

            DB::table('finalisasi_kpi')
            ->where('kode_kpi', $kpa->kode_kpi)
            ->update($updateFinal);
        }
    }

    return response()->json([
        'status' => 'success',
        'nip_atasan' => $nipAtasan,
        'tahun' => $tahun,
        'semester' => $semester
    ]);
}





    public function exportDatapencapaian(Request $request) 
    {
        return Excel::download(new DatapencapaianExport(
            $request->get('tahuncarinya'),
            $request->get('nipcarinya'),
            $request->get('namacarinya')
        ), 'Rincian Pencapaian KPI.xlsx');
    } 

}

