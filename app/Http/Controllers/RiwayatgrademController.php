<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\Datatables;
use App\models\Riwayatgradem;
use App\models\Datapegawaim;
use App\models\Gradem;
use App\models\Masteraream;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
// use App\Services\CustomEncryptionService;

class RiwayatgrademController extends Controller
{
    private $riwayatgradem;
    private $gradem;
    private $masteraream;
    // private $customEncryptionService;
    // public function __construct(Kinerjapegawaim $kinerjapegawaim, Mappingpegawaim $mappingpegawaim, Masteraream $masteraream, CustomEncryptionService $customEncryptionService)
    public function __construct(Riwayatgradem $riwayatgradem, Gradem $gradem, Masteraream $masteraream)
    {
        $this->riwayatgradem = $riwayatgradem;
        $this->gradem = $gradem;
        $this->masteraream = $masteraream;
        // $this->customEncryptionService = $customEncryptionService;
    }

    public function index(Request $request)
    {      
        // $customEncryptionService = $this->customEncryptionService;

        $tahun_ini = intval(Carbon::now()->format('Y'));
        $tahun_awal = 2024;
        $datatahun = [];
        for($i=$tahun_ini;$i>=$tahun_awal;$i--){
            $datatahun[] = $i;
        }

        $nipnya = Auth::user()->nip;
        $bulan_ini = Carbon::now()->format('m');
        $tanggal_ini = Carbon::now()->format('d');
        $batas_tgl_penilaian = 20;
        if(intval($bulan_ini)==1){
            $tahun_periode = $tahun_ini-1;
            $bulan_periode = "12";
        } else {
            $tahun_periode = $tahun_ini;
            $bulan_periode = str_pad(intval($bulan_ini)-1,2,"0",STR_PAD_LEFT);
        }

        if ($request->ajax()) {  
            $data = Datapegawaim::selectRaw("
                data_pegawai.*,
                ifnull(b.nama_area,'') as nama_area,
                c.id as id2,
                c.grade as grade2,
                c.tgl_kenaikan as tgl_kenaikan2
            ")
            ->leftJoin('master_area as b','b.kd_area','=','data_pegawai.kd_area')
            ->leftJoin('riwayat_grade as c','c.nip','=','data_pegawai.nip')
            ->whereRaw("data_pegawai.aktif='1' and data_pegawai.payroll='1' and data_pegawai.aktif_simkp='1'")
            ->groupBy('data_pegawai.nip')
            ->orderBy('data_pegawai.id','asc');
            return Datatables::eloquent($data)
                ->addIndexColumn()
                ->filter(function ($instance) use ($request) {
                    if (!empty($request->get('kd_areacari')) && !empty($request->get('kd_areacari')!="semua")) {
                        $instance->whereRaw("data_pegawai.kd_area='" . request('kd_areacari') . "'");
                    }
                    if (!empty($request->get('search'))) {
                        $instance->whereRaw("(data_pegawai.nip='" . request('search') . "' or data_pegawai.nama like '%" . request('search') . "%')");
                    }
                })
                ->addColumn('aksi', function ($data) {
                    $a = '<div class="acao text-center">';
                    $a .= '<a href="javascript:void(0)" data-id="'.$data->id2.'" data-nip="'.$data->nip.'" data-nama="'.$data->nama.'" data-jabatan="'.$data->jabatan.'" data-grade="'.$data->grade2.'" data-tgl_kenaikan="'.$data->tgl_kenaikan2.'" title="Update Data" class="edit_row"><button type="button" class="btn btn-icon btn-light-warning icon-btn-sm" style="margin-right:3px;"><i class="ri-pencil-fill font-size-14"></i></button></a>';
                    $a .= '<a href="javascript:void(0)" data-id="'.$data->id2.'" title="Hapus Data" class="delete_row"><button type="button" class="btn btn-icon btn-light-danger icon-btn-sm" style="margin-right:0px;"><i class="ri-delete-bin-line font-size-14"></i></button></a>';
                    $a .= '</div>';
                    return $a;
                })
                ->rawColumns(['aksi'])
                ->make(true);
        }

        return view('admin.riwayatgradem.index',[
            'masteraream' => $this->masteraream->getAllData(),
            'gradem' => $this->gradem->getAllData(),
        ]);

    }    

    public function store(Request $request)
    {
        $nipnya = Auth::user()->nip;
        $tgl_kenaikan2 = $request->tgl_kenaikan;
        if($tgl_kenaikan2!=""){
            $tgl_kenaikan = Carbon::createFromFormat('d/m/Y', $tgl_kenaikan2)->format('Y-m-d');
        } else {
            $tgl_kenaikan = "";
        }
        // dd($tgl_kenaikan2." ".$tgl_kenaikan);
        try {
            Riwayatgradem::updateOrCreate([
                'id' => $request->id, 
            ],
            [
                'nip' => $request->nip, 
                'grade' => $request->grade,
                'tgl_kenaikan' => $tgl_kenaikan
            ]);                      
            return response()->json(["status" => 'sukses', "pesan" => 'Sukses simpan data']);
        } catch (\Exception $e) {
            return response()->json(["status" => 'error', "pesan" => 'Gagal simpan data ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        // $kinerjapegawaim = Kinerjapegawaim::selectRaw("*")
        // ->whereRaw("id='$id'")
        // ->first();  
        // dd($kinerjapegawaim);
        // return response()->json($kinerjapegawaim);      
        $kinerjapegawaim = Kinerjapegawaim::find($id);
        return response()->json($kinerjapegawaim);

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
        $data = Finalisasikpim::selectRaw("
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
            d.approve01 as approve01,
            d.approval01 as approval01,
            d.approve02 as approve02,
            d.approval02 as approval02,
            d.approve03 as approve03,
            d.approval03 as approval03,
            d.approve04 as approve04,
            d.approval04 as approval04,
            d.approve05 as approve05,
            d.approval05 as approval05,
            d.approve06 as approve06,
            d.approval06 as approval06,
            d.approve07 as approve07,
            d.approval07 as approval07,
            d.approve08 as approve08,
            d.approval08 as approval08,
            d.approve09 as approve09,
            d.approval09 as approval09,
            d.approve10 as approve10,
            d.approval10 as approval10,
            d.approve11 as approve11,
            d.approval11 as approval11,
            d.approve12 as approve12,
            d.approval12 as approval12
       ")
        ->leftJoin('simkppcn.cascading_kpi as b','b.kode_cascading','=','finalisasi_kpi.kode_cascading')
        ->leftJoin('simkppcn.kpi_pegawai as c','c.kode_kpi','=','finalisasi_kpi.kode_kpi')
        ->leftJoin('simkppcn.approval_kpi as d','d.kode_kpi','=','finalisasi_kpi.kode_kpi')
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
            ->rawColumns(['aksi'])
            ->make(true);        
    }     

    public function getSkorkuantitas(Request $request)
    {
        $nilai = $request->nilai;
        $row1 = DB::table('simkppcn.matriks_skor')
        ->selectRaw("*")
        ->whereRaw("kriteria='kuantitas' and pencapaian_awal<='$nilai' and pencapaian_akhir>='$nilai'")
        ->first();
        if($row1){
            $pencapaian_awal = $row1->pencapaian_awal;
            $pencapaian_akhir = $row1->pencapaian_akhir;
            $skor_awal = $row1->skor_awal;
            $skor_akhir = $row1->skor_akhir;
            $selisih_pencapaian = intval($pencapaian_akhir)-intval($pencapaian_awal);
            $selisih_skor = intval($skor_akhir)-intval($skor_awal);
            $nilai_pengali = round(intval($selisih_skor)/intval($selisih_pencapaian),2);
            $nilai_penambah = round((intval($nilai)-intval($pencapaian_awal))*$nilai_pengali);
            $skor_akhir = $nilai_penambah+intval($skor_awal);
        } else {
            $skor_akhir = 0;
        }
        return response()->json($skor_akhir);
    }

    public function getSkorkualitas(Request $request)
    {
        $nilai = $request->nilai;
        $row1 = DB::table('simkppcn.matriks_skor')
        ->selectRaw("*")
        ->whereRaw("kriteria='kualitas' and pencapaian_awal<='$nilai' and pencapaian_akhir>='$nilai'")
        ->first();
        if($row1){
            $pencapaian_awal = $row1->pencapaian_awal;
            $pencapaian_akhir = $row1->pencapaian_akhir;
            $skor_awal = $row1->skor_awal;
            $skor_akhir = $row1->skor_akhir;
            $selisih_pencapaian = intval($pencapaian_akhir)-intval($pencapaian_awal);
            $selisih_skor = intval($skor_akhir)-intval($skor_awal);
            $nilai_pengali = round(intval($selisih_skor)/intval($selisih_pencapaian),2);
            $nilai_penambah = round((intval($nilai)-intval($pencapaian_awal))*$nilai_pengali);
            $skor_akhir = $nilai_penambah+intval($skor_awal);
        } else {
            $skor_akhir = 0;
        }
        return response()->json($skor_akhir);
    }

    public function getSkorwaktu(Request $request)
    {
        // $nilai = $request->nilai;
        // $row1 = DB::table('simkppcn.matriks_skor')
        // ->selectRaw("*")
        // ->whereRaw("kriteria='waktu' and pencapaian_awal<='$nilai' and pencapaian_akhir>='$nilai'")
        // ->first();
        // if($row1){
        //     $pencapaian_awal = $row1->pencapaian_awal;
        //     $pencapaian_akhir = $row1->pencapaian_akhir;
        //     $skor_awal = $row1->skor_awal;
        //     $skor_akhir = $row1->skor_akhir;
        //     $selisih_pencapaian = intval($pencapaian_akhir)-intval($pencapaian_awal);
        //     $selisih_skor = intval($skor_akhir)-intval($skor_awal);
        //     $nilai_pengali = round(intval($selisih_skor)/intval($selisih_pencapaian),2);
        //     $nilai_pengurang = round((intval($nilai)-intval($pencapaian_awal))*$nilai_pengali);
        //     $skor_akhir = intval($skor_akhir)-$nilai_pengurang;
        // } else {
        //     $skor_akhir = 0;
        // }
        $nilai = $request->nilai;
        $row1 = DB::table('simkppcn.matriks_skor')
        ->selectRaw("*")
        ->whereRaw("kriteria='waktu' and pencapaian_awal<='$nilai' and pencapaian_akhir>='$nilai'")
        ->first();
        if($row1){
            $pencapaian_awal = $row1->pencapaian_awal;
            $pencapaian_akhir = $row1->pencapaian_akhir;
            $skor_awal = $row1->skor_awal;
            $skor_akhir = $row1->skor_akhir;
            $selisih_pencapaian = intval($pencapaian_akhir)-intval($pencapaian_awal);
            $selisih_skor = intval($skor_akhir)-intval($skor_awal);
            $nilai_pengali = round(intval($selisih_skor)/intval($selisih_pencapaian),2);
            $nilai_penambah = round((intval($nilai)-intval($pencapaian_awal))*$nilai_pengali);
            $skor_akhir = $nilai_penambah+intval($skor_awal);
        } else {
            $skor_akhir = 0;
        }
        return response()->json($skor_akhir);
    }

    public function getSkorkinerja(Request $request)
    {
        $nilai = $request->nilai;
        $row1 = DB::table('simkppcn.master_nilai_kinerja')
        ->selectRaw("*")
        ->whereRaw("nilai_awal<='$nilai' and nilai_akhir>='$nilai'")
        ->first();
        if($row1){
            $kode_kinerja = $row1->kode_kinerja;
        } else {
            $kode_kinerja = "";
        }
        return response()->json($kode_kinerja);
    }

    public function getSkorindividu(Request $request)
    {
        $nilai = $request->nilai;
        $row1 = DB::table('simkppcn.master_nilai_individu')
        ->selectRaw("*")
        ->whereRaw("nilai_awal<='$nilai' and nilai_akhir>='$nilai'")
        ->first();
        if($row1){
            $kode_individu = $row1->kode_individu;
        } else {
            $kode_individu = "";
        }
        return response()->json($kode_individu);
    }

    public function getTalenta(Request $request)
    {
        $nilai = $request->nilai;
        // dd($nilai);
        $row1 = DB::table('simkppcn.master_pengukuran')
        ->selectRaw("*")
        ->whereRaw("kode_pengukuran='$nilai'")
        ->first();
        if($row1){
            $nama_pengukuran = $row1->nama_pengukuran;
        } else {
            $nama_pengukuran = "";
        }
        return response()->json($nama_pengukuran);
    }

}
