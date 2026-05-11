<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\Datatables;
use App\models\Kinerjapegawaim;
use App\models\Riwayattalentam;
use App\models\Mappingpegawaim;
use App\models\Masteraream;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
// use App\Services\CustomEncryptionService;

class KinerjapegawaimController extends Controller
{
    private $kinerjapegawaim;
    private $mappingpegawaim;
    private $masteraream;
    // private $customEncryptionService;
    // public function __construct(Kinerjapegawaim $kinerjapegawaim, Mappingpegawaim $mappingpegawaim, Masteraream $masteraream, CustomEncryptionService $customEncryptionService)
    public function __construct(Kinerjapegawaim $kinerjapegawaim, Mappingpegawaim $mappingpegawaim, Masteraream $masteraream)
    {
        $this->kinerjapegawaim = $kinerjapegawaim;
        $this->mappingpegawaim = $mappingpegawaim;
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
        if(intval($bulan_ini)<=6){
            $tahun_periode = $tahun_ini-1;
            $bulan_periode = "12";
        } else {
            $tahun_periode = $tahun_ini;
            $bulan_periode = str_pad(intval($bulan_ini)-1,2,"0",STR_PAD_LEFT);
        }

        if ($request->ajax()) {  
            $tahuncari = $request->tahuncari;
            $semestercari = $request->semestercari;
            $data = Kinerjapegawaim::selectRaw("
                penilaian_pegawai.*,
                ifnull(b.nama_area,'') as nama_area,
                c.nama as nama,
                c.jabatan as jabatan
            ")
            ->leftJoin('master_area as b','b.kd_area','=','penilaian_pegawai.kd_area')
            ->leftJoin('data_pegawai as c','c.nip','=','penilaian_pegawai.nip')
            // ->whereRaw("penilaian_pegawai.tahun='$tahuncari' and penilaian_pegawai.nip not in (select nip from data_pegawai where jenis_kpi='pusat' and level_kpi<='2')")
            // ->groupBy('penilaian_pegawai.nip','penilaian_pegawai.id','c.nama','c.jabatan')
            ->groupBy('penilaian_pegawai.nip','c.nama','c.jabatan')
            ->orderBy('id','asc');
            return Datatables::eloquent($data)
                ->addIndexColumn()
                ->filter(function ($instance) use ($request) {
                    if (!empty($request->get('kd_areacari')) && !empty($request->get('kd_areacari')!="semua")) {
                        $instance->whereRaw("penilaian_pegawai.kd_area='" . request('kd_areacari') . "'");
                    }
                    if (!empty($request->get('search'))) {
                        $instance->whereRaw("(c.nip='" . request('search') . "' or c.nama like '%" . request('search') . "%')");
                    }
                })
                ->addColumn('aksi', function ($data) use($tahun_periode,$bulan_periode,$batas_tgl_penilaian,$tanggal_ini,$semestercari) {
                    $a = '<div class="acao text-center">';
                    // if(intval($tanggal_ini)>=$batas_tgl_penilaian && (intval($bulan_periode)==1 || intval($bulan_periode)==7)){
                    //     $a .= '<a href="javascript:void(0)" data-tahun_periode="'.$tahun_periode.'" data-bulan_periode="'.$bulan_periode.'" data-id="'.$data->id.'" data-nip="'.$data->nip.'" data-nama="'.$data->nama.'" data-jabatan="'.$data->jabatan.'" data-kode_penilaian="'.$data->kode_penilaian.'" title="Update Penilaian" class="edit_row"><button type="button" class="btn btn-icon btn-sm btn-warning" style="margin-right:3px;"><span class="ti ti-pencil-star ti-sm"></span></button></a>';
                    // } else {
                    //     $a .= '<a href="javascript:void(0)" data-tahun_periode="'.$tahun_periode.'" data-bulan_periode="'.$bulan_periode.'" data-id="'.$data->id.'" data-nip="'.$data->nip.'" data-nama="'.$data->nama.'" data-jabatan="'.$data->jabatan.'" data-kode_penilaian="'.$data->kode_penilaian.'" title="Update Penilaian" class="edit_row"><button type="button" class="btn btn-icon btn-sm btn-warning" style="margin-right:3px;"><span class="ti ti-pencil-star ti-sm"></span></button></a>';
                    // }
                    $a .= '<a href="javascript:void(0)" data-tahun_periode="'.$tahun_periode.'" data-bulan_periode="'.$bulan_periode.'" data-id="'.$data->id.'" data-nip="'.$data->nip.'" data-nama="'.$data->nama.'" data-jabatan="'.$data->jabatan.'" data-kode_penilaian="'.$data->kode_penilaian.'" title="Update Penilaian" class="edit_row"><button type="button" class="btn btn-light-warning icon-btn-sm" style="margin-right:3px;"><i class="ri-pencil-line fs-14"></i></button></a>';
                    $a .= '<a href="javascript:void(0)" data-tahun="'.$data->tahun.'" data-semester="'.$semestercari.'" data-nip="'.$data->nip.'" title="Form SIMKP" class="form_simkp"><button type="button" class="btn btn-light-success icon-btn-sm" style="margin-right:0px;"><i class="ri-file-pdf-2-line fs-14"></i></button></a>';
                    $a .= '</div>';
                    return $a;
                })
                // ->addColumn('skor_kuantitas_semester2nya', function($data){
                //     if($data->skor_kuantitas_semester2!=""){
                //         try {
                //             return $this->customEncryptionService->decrypt($data->skor_kuantitas_semester2);
                //         } catch (DecryptException $e) {
                //             return 'Error decrypting data';
                //         }
                //     } else {
                //         return '';
                //     }
                // })
                // ->addColumn('huruf_kinerja_semester2nya', function($data){
                //     if($data->huruf_kinerja_semester2!=""){
                //         try {
                //             return $this->customEncryptionService->decrypt($data->huruf_kinerja_semester2);
                //         } catch (DecryptException $e) {
                //             return 'Error decrypting data';
                //         }
                //     } else {
                //         return '';
                //     }
                // })
                // ->addColumn('skor_individu_semester2nya', function($data){
                //     if($data->skor_individu_semester2!=""){
                //         try {
                //             return $this->customEncryptionService->decrypt($data->skor_individu_semester2);
                //         } catch (DecryptException $e) {
                //             return 'Error decrypting data';
                //         }
                //     } else {
                //         return '';
                //     }
                // })
                // ->addColumn('huruf_individu_semester2nya', function($data){
                //     if($data->huruf_individu_semester2!=""){
                //         try {
                //             return $this->customEncryptionService->decrypt($data->huruf_individu_semester2);
                //         } catch (DecryptException $e) {
                //             return 'Error decrypting data';
                //         }
                //     } else {
                //         return '';
                //     }
                // })
                // ->addColumn('nilai_talenta_semester2nya', function($data){
                //     if($data->nilai_talenta_semester2!=""){
                //         try {
                //             return $this->customEncryptionService->decrypt($data->nilai_talenta_semester2);
                //         } catch (DecryptException $e) {
                //             return 'Error decrypting data';
                //         }
                //     } else {
                //         return '';
                //     }
                // })
                // ->addColumn('nama_talenta_semester2nya', function($data){
                //     if($data->nama_talenta_semester2!=""){
                //         try {
                //             return $this->customEncryptionService->decrypt($data->nama_talenta_semester2);
                //         } catch (DecryptException $e) {
                //             return 'Error decrypting data';
                //         }
                //     } else {
                //         return '';
                //     }
                // })
                ->rawColumns(['aksi'])
                ->make(true);
        }

        return view('admin.kinerjapegawaim.index',[
            'masteraream' => $this->masteraream->getAllData(),
        ],compact('bulan_ini','tanggal_ini','batas_tgl_penilaian','tahun_periode','bulan_periode','datatahun'));

    }    

    public function store(Request $request)
    {
        // $sensitiveData = 'sensitive_data';
        // $encryptedData = $this->customEncryptionService->encrypt($sensitiveData);
        // $decryptedData = $this->customEncryptionService->decrypt($encryptedData);
        // dd($encryptedData." ".$decryptedData);

        $nipnya = Auth::user()->nip;
        $id = intval($request->id);
        $bulan_periode = $request->bulan_periode;
        $semester = $request->semester;
        // dd($id." ".$bulan_periode);

        try {
            $kinerjapegawaim = Kinerjapegawaim::where('id', $id);    
            $updateData = [];
            // if(intval($bulan_periode)<=6){
            if($semester=="semester1"){
                $updateData['nilai_semester1'] = $request->nilai_semester;
                $updateData['pdp_semester1'] = $request->pdp_semester;
                $updateData['kuantitas_semester1'] = $request->kuantitas_semester;
                $updateData['kualitas_semester1'] = $request->kualitas_semester;
                $updateData['waktu_semester1'] = $request->waktu_semester;
                $updateData['skor_kuantitas_semester1'] = $request->skor_kuantitas_semester;
                $updateData['skor_kualitas_semester1'] = $request->skor_kualitas_semester;
                $updateData['skor_waktu_semester1'] = $request->skor_waktu_semester;
                $updateData['skor_kinerja_semester1'] = $request->skor_kinerja_semester;
                $updateData['huruf_kinerja_semester1'] = $request->huruf_kinerja_semester;
                $updateData['skor_individu_semester1'] = $request->skor_individu_semester;
                $updateData['huruf_individu_semester1'] = $request->huruf_individu_semester;
                $updateData['nilai_talenta_semester1'] = $request->nilai_talenta_semester;
                $updateData['nama_talenta_semester1'] = $request->nama_talenta_semester;
            } else {
                $updateData['nilai_semester2'] = $request->nilai_semester;
                $updateData['pdp_semester2'] = $request->pdp_semester;
                $updateData['kuantitas_semester2'] = $request->kuantitas_semester;
                $updateData['kualitas_semester2'] = $request->kualitas_semester;
                $updateData['waktu_semester2'] = $request->waktu_semester;
                $updateData['skor_kuantitas_semester2'] = $request->skor_kuantitas_semester;
                $updateData['skor_kualitas_semester2'] = $request->skor_kualitas_semester;
                $updateData['skor_waktu_semester2'] = $request->skor_waktu_semester;
                $updateData['skor_kinerja_semester2'] = $request->skor_kinerja_semester;
                $updateData['huruf_kinerja_semester2'] = $request->huruf_kinerja_semester;
                $updateData['skor_individu_semester2'] = $request->skor_individu_semester;
                $updateData['huruf_individu_semester2'] = $request->huruf_individu_semester;
                $updateData['nilai_talenta_semester2'] = $request->nilai_talenta_semester;
                $updateData['nama_talenta_semester2'] = $request->nama_talenta_semester;
            }
            $kinerjapegawaim->update($updateData);          
            
            return response()->json(["status" => 'sukses', "pesan" => 'Sukses simpan data']);
        } catch (\Exception $e) {
            return response()->json(["status" => 'error', "pesan" => 'Gagal simpan data ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        // $row1 = DB::table('penilaian_pegawai')
        // ->select('nip')
        // ->where('id',$id)
        // ->first();  
        // $nip = $row1->nip;
        $row1 = DB::table('penilaian_pegawai')
        ->select(
            'penilaian_pegawai.nip',
            DB::raw('ROUND(AVG(CAST(b1.kuantitasb_semester1 AS DECIMAL(10,2))), 2) AS rata_kuantitas_semester1'),
            DB::raw('ROUND(AVG(CAST(b2.kualitasb_semester1 AS DECIMAL(10,2))), 2) AS rata_kualitas_semester1'),
            DB::raw('ROUND(AVG(CAST(b3.waktub_semester1 AS DECIMAL(10,2))), 2) AS rata_waktu_semester1'),
            DB::raw('ROUND(AVG(CAST(c1.kuantitasb_semester2 AS DECIMAL(10,2))), 2) AS rata_kuantitas_semester2'),
            DB::raw('ROUND(AVG(CAST(c2.kualitasb_semester2 AS DECIMAL(10,2))), 2) AS rata_kualitas_semester2'),
            DB::raw('ROUND(AVG(CAST(c3.waktub_semester2 AS DECIMAL(10,2))), 2) AS rata_waktu_semester2')
        )
        // ->leftJoin('finalisasi_kpi as b', function($join) {
        //     $join->on('penilaian_pegawai.nip', '=', 'b.nip')
        //         ->whereNotNull('b.kuantitasb_semester1')
        //         ->where('b.kuantitasb_semester1', '!=', '')
        //         ->whereNotNull('b.kualitasb_semester1')
        //         ->where('b.kualitasb_semester1', '!=', '')
        //         ->whereNotNull('b.waktub_semester1')
        //         ->where('b.waktub_semester1', '!=', '');
        // })
        // ->leftJoin('finalisasi_kpi as c', function($join) {
        //     $join->on('penilaian_pegawai.nip', '=', 'c.nip')
        //         ->whereNotNull('c.kuantitasb_semester2')
        //         ->where('c.kuantitasb_semester2', '!=', '')
        //         ->whereNotNull('c.kualitasb_semester2')
        //         ->where('c.kualitasb_semester2', '!=', '')
        //         ->whereNotNull('c.waktub_semester2')
        //         ->where('c.waktub_semester2', '!=', '');
        // })
        ->leftJoin('finalisasi_kpi as b1', function($join) {
            $join->on('penilaian_pegawai.nip', '=', 'b1.nip')
                ->whereNotNull('b1.kuantitasb_semester1')
                ->where('b1.kuantitasb_semester1', '!=', '')
                ->where('b1.kuantitasb_semester1', '>', 0);
        })
        ->leftJoin('finalisasi_kpi as b2', function($join) {
            $join->on('penilaian_pegawai.nip', '=', 'b2.nip')
                ->whereNotNull('b2.kualitasb_semester1')
                ->where('b2.kualitasb_semester1', '!=', '')
                ->where('b2.kualitasb_semester1', '>', 0);
        })
        ->leftJoin('finalisasi_kpi as b3', function($join) {
            $join->on('penilaian_pegawai.nip', '=', 'b3.nip')
                ->whereNotNull('b3.waktub_semester1')
                ->where('b3.waktub_semester1', '!=', '')
                ->where('b3.waktub_semester1', '>', 0);
        })
        ->leftJoin('finalisasi_kpi as c1', function($join) {
            $join->on('penilaian_pegawai.nip', '=', 'c1.nip')
                ->whereNotNull('c1.kuantitasb_semester2')
                ->where('c1.kuantitasb_semester2', '!=', '')
                ->where('c1.kuantitasb_semester2', '>', 0);
        })
        ->leftJoin('finalisasi_kpi as c2', function($join) {
            $join->on('penilaian_pegawai.nip', '=', 'c2.nip')
                ->whereNotNull('c2.kualitasb_semester2')
                ->where('c2.kualitasb_semester2', '!=', '')
                ->where('c2.kualitasb_semester2', '>', 0);
        })
        ->leftJoin('finalisasi_kpi as c3', function($join) {
            $join->on('penilaian_pegawai.nip', '=', 'c3.nip')
                ->whereNotNull('c3.waktub_semester2')
                ->where('c3.waktub_semester2', '!=', '')
                ->where('c3.waktub_semester2', '>', 0);
        })
        ->where('penilaian_pegawai.id', $id)
        ->groupBy('penilaian_pegawai.nip')
        ->first();
        // dd($rata_rata_semester1." ".$rata_rata_semester2);
        $kinerjapegawaim = Kinerjapegawaim::find($id);
        if ($row1) {
            $rata_kuantitas_semester1 = $row1->rata_kuantitas_semester1 ?? 0;
            $rata_kualitas_semester1 = $row1->rata_kualitas_semester1 ?? 0;
            $rata_waktu_semester1 = $row1->rata_waktu_semester1 ?? 0;
            
            $rata_kuantitas_semester2 = $row1->rata_kuantitas_semester2 ?? 0;
            $rata_kualitas_semester2 = $row1->rata_kualitas_semester2 ?? 0;
            $rata_waktu_semester2 = $row1->rata_waktu_semester2 ?? 0;
        } else {
            $rata_kuantitas_semester1 = 0;
            $rata_kualitas_semester1 = 0;
            $rata_waktu_semester1 = 0;
            
            $rata_kuantitas_semester2 = 0;
            $rata_kualitas_semester2 = 0;
            $rata_waktu_semester2 = 0;
        }

        // dd($rata_kuantitas_semester2." ".$rata_kualitas_semester2." ".$rata_waktu_semester2);
        $kinerjapegawaim->rata_kuantitas_semester1 = $rata_kuantitas_semester1;
        $kinerjapegawaim->rata_kualitas_semester1 = $rata_kualitas_semester1;
        $kinerjapegawaim->rata_waktu_semester1 = $rata_waktu_semester1;

        $kinerjapegawaim->rata_kuantitas_semester2 = $rata_kuantitas_semester2;
        $kinerjapegawaim->rata_kualitas_semester2 = $rata_kualitas_semester2;
        $kinerjapegawaim->rata_waktu_semester2 = $rata_waktu_semester2;
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

    // public function getSkorkuantitas(Request $request)
    // {
    //     $nilai = intval($request->nilai);
    //     $row1 = DB::table('simkppcn.matriks_skor')
    //     ->selectRaw("*")
    //     ->whereRaw("kriteria='kuantitas' and pencapaian_awal<='$nilai' and pencapaian_akhir>='$nilai'")
    //     ->first();
    //     if($row1){
    //         $pencapaian_awal = $row1->pencapaian_awal;
    //         $pencapaian_akhir = $row1->pencapaian_akhir;
    //         $skor_awal = $row1->skor_awal;
    //         $skor_akhir = $row1->skor_akhir;
    //         $selisih_pencapaian = intval($pencapaian_akhir)-intval($pencapaian_awal);
    //         $selisih_skor = intval($skor_akhir)-intval($skor_awal);
    //         $nilai_pengali = round(intval($selisih_skor)/intval($selisih_pencapaian),2);
    //         $nilai_penambah = round((intval($nilai)-intval($pencapaian_awal))*$nilai_pengali);
    //         $skor_akhir = $nilai_penambah+intval($skor_awal);
    //     } else {
    //         $skor_akhir = 0;
    //     }
    //     return response()->json($skor_akhir);
    // }
    public function getSkorkuantitas(Request $request)
    {
        $nilai = (int) $request->nilai;

        $row = DB::table('simkppcn.matriks_skor')
            ->select(
                'pencapaian_awal',
                'pencapaian_akhir',
                'skor_awal',
                'skor_akhir'
            )
            ->where('kriteria', 'kuantitas')
            ->where('pencapaian_awal', '<=', $nilai)
            ->where('pencapaian_akhir', '>=', $nilai)
            ->first();

        if (!$row) {
            return response()->json(0);
        }

        $selisihPencapaian = $row->pencapaian_akhir - $row->pencapaian_awal;
        $selisihSkor       = $row->skor_akhir - $row->skor_awal;

        if ($selisihPencapaian == 0) {
            return response()->json((int) $row->skor_awal);
        }

        $pengali = $selisihSkor / $selisihPencapaian;

        $skorAkhir = $row->skor_awal
            + (($nilai - $row->pencapaian_awal) * $pengali);

        return response()->json((int) round($skorAkhir));
    }


    // public function getSkorkualitas(Request $request)
    // {
    //     $nilai = intval($request->nilai);
    //     $row1 = DB::table('simkppcn.matriks_skor')
    //     ->selectRaw("*")
    //     ->whereRaw("kriteria='kualitas' and pencapaian_awal<='$nilai' and pencapaian_akhir>='$nilai'")
    //     ->first();
    //     if($row1){
    //         $pencapaian_awal = $row1->pencapaian_awal;
    //         $pencapaian_akhir = $row1->pencapaian_akhir;
    //         $skor_awal = $row1->skor_awal;
    //         $skor_akhir = $row1->skor_akhir;
    //         $selisih_pencapaian = intval($pencapaian_akhir)-intval($pencapaian_awal);
    //         $selisih_skor = intval($skor_akhir)-intval($skor_awal);
    //         $nilai_pengali = round(intval($selisih_skor)/intval($selisih_pencapaian),2);
    //         $nilai_penambah = round((intval($nilai)-intval($pencapaian_awal))*$nilai_pengali);
    //         $skor_akhir = $nilai_penambah+intval($skor_awal);
    //     } else {
    //         $skor_akhir = 0;
    //     }
    //     return response()->json($skor_akhir);
    // }
    public function getSkorkualitas(Request $request)
    {
        $nilai = (int) $request->nilai;

        $row = DB::table('simkppcn.matriks_skor')
            ->select(
                'pencapaian_awal',
                'pencapaian_akhir',
                'skor_awal',
                'skor_akhir'
            )
            ->where('kriteria', 'kualitas')
            ->where('pencapaian_awal', '<=', $nilai)
            ->where('pencapaian_akhir', '>=', $nilai)
            ->first();

        if (!$row) {
            return response()->json(0);
        }

        $selisihPencapaian = $row->pencapaian_akhir - $row->pencapaian_awal;
        $selisihSkor       = $row->skor_akhir - $row->skor_awal;

        if ($selisihPencapaian == 0) {
            return response()->json((int) $row->skor_awal);
        }

        $pengali = $selisihSkor / $selisihPencapaian;

        $skorAkhir = $row->skor_awal
            + (($nilai - $row->pencapaian_awal) * $pengali);

        return response()->json((int) round($skorAkhir));
    }

    // public function getSkorwaktu(Request $request)
    // {
    //     $nilai = intval($request->nilai);
    //     $row1 = DB::table('simkppcn.matriks_skor')
    //     ->selectRaw("*")
    //     ->whereRaw("kriteria='waktu' and pencapaian_awal<='$nilai' and pencapaian_akhir>='$nilai'")
    //     ->first();
    //     if($row1){
    //         $pencapaian_awal = $row1->pencapaian_awal;
    //         $pencapaian_akhir = $row1->pencapaian_akhir;
    //         $skor_awal = $row1->skor_awal;
    //         $skor_akhir = $row1->skor_akhir;
    //         $selisih_pencapaian = intval($pencapaian_akhir)-intval($pencapaian_awal);
    //         $selisih_skor = intval($skor_akhir)-intval($skor_awal);
    //         $nilai_pengali = round(intval($selisih_skor)/intval($selisih_pencapaian),2);
    //         $nilai_penambah = round((intval($nilai)-intval($pencapaian_awal))*$nilai_pengali);
    //         $skor_akhir = $nilai_penambah+intval($skor_awal);
    //     } else {
    //         $skor_akhir = 0;
    //     }
    //     return response()->json($skor_akhir);
    // }
    public function getSkorwaktu(Request $request)
    {
        $nilai = (int) $request->nilai;

        $row = DB::table('simkppcn.matriks_skor')
            ->select(
                'pencapaian_awal',
                'pencapaian_akhir',
                'skor_awal',
                'skor_akhir'
            )
            ->where('kriteria', 'waktu')
            ->where('pencapaian_awal', '<=', $nilai)
            ->where('pencapaian_akhir', '>=', $nilai)
            ->first();

        if (!$row) {
            return response()->json(0);
        }

        $selisihPencapaian = $row->pencapaian_akhir - $row->pencapaian_awal;
        $selisihSkor       = $row->skor_akhir - $row->skor_awal;

        if ($selisihPencapaian == 0) {
            return response()->json((int) $row->skor_awal);
        }

        $pengali = $selisihSkor / $selisihPencapaian;

        $skorAkhir = $row->skor_awal
            + (($nilai - $row->pencapaian_awal) * $pengali);

        return response()->json((int) round($skorAkhir));
    }


    // public function getSkorkinerja(Request $request)
    // {
    //     $nilai = $request->nilai;
    //     $row1 = DB::table('simkppcn.master_nilai_kinerja')
    //     ->selectRaw("*")
    //     ->whereRaw("nilai_awal<='$nilai' and nilai_akhir>='$nilai'")
    //     ->first();
    //     if($row1){
    //         $kode_kinerja = $row1->kode_kinerja;
    //     } else {
    //         $kode_kinerja = "";
    //     }
    //     return response()->json($kode_kinerja);
    // }
    public function getSkorkinerja(Request $request)
    {
        $nilai = (int) $request->nilai;

        $kode = DB::table('simkppcn.master_nilai_kinerja')
            ->where('nilai_awal', '<=', $nilai)
            ->where('nilai_akhir', '>=', $nilai)
            ->value('kode_kinerja');

        return response()->json($kode ?? '');
    }

    // public function getSkorindividu(Request $request)
    // {
    //     $nilai = $request->nilai;
    //     $row1 = DB::table('simkppcn.master_nilai_individu')
    //     ->selectRaw("*")
    //     ->whereRaw("nilai_awal<='$nilai' and nilai_akhir>='$nilai'")
    //     ->first();
    //     if($row1){
    //         $kode_individu = $row1->kode_individu;
    //     } else {
    //         $kode_individu = "";
    //     }
    //     return response()->json($kode_individu);
    // }
    public function getSkorindividu(Request $request)
    {
        $nilai = (int) $request->nilai;

        $kode = DB::table('simkppcn.master_nilai_individu')
            ->where('nilai_awal', '<=', $nilai)
            ->where('nilai_akhir', '>=', $nilai)
            ->value('kode_individu');

        return response()->json($kode ?? '');
    }


    // public function getTalenta(Request $request)
    // {
    //     $nilai = $request->nilai;
    //     // dd($nilai);
    //     $row1 = DB::table('simkppcn.master_pengukuran')
    //     ->selectRaw("*")
    //     ->whereRaw("kode_pengukuran='$nilai'")
    //     ->first();
    //     if($row1){
    //         $nama_pengukuran = $row1->nama_pengukuran;
    //     } else {
    //         $nama_pengukuran = "";
    //     }
    //     return response()->json($nama_pengukuran);
    // }
    public function getTalenta(Request $request)
    {
        $kode = $request->nilai;

        $nama = DB::table('simkppcn.master_pengukuran')
            ->where('kode_pengukuran', $kode)
            ->value('nama_pengukuran');

        return response()->json($nama ?? '');
    }


    public function prosesTalenta(Request $request)
    {
        $tahuncari = $request->tahun;
        $semestercari = $request->semester;
        $kd_areacari = $request->kd_area;
        $perintah = "";
        if($kd_areacari!="semua"){
            $perintah .= " and kd_area='$kd_areacari'";
        }
        try {
            if($semestercari=="1"){
                $rows1 = DB::table('penilaian_pegawai')
                ->selectRaw("
                    nip,
                    nama_talenta_semester1 as nama_talenta
                ")
                ->whereRaw("tahun='$tahuncari' and nama_talenta_semester1<>'' and nama_talenta_semester1 is not null".$perintah)
                ->orderBy('id','asc')
                ->get();
            } else {
                $rows1 = DB::table('penilaian_pegawai')
                ->selectRaw("
                    nip,
                    nama_talenta_semester2 as nama_talenta
                ")
                ->whereRaw("tahun='$tahuncari' and nama_talenta_semester2<>'' and nama_talenta_semester2 is not null".$perintah)
                ->orderBy('id','asc')
                ->get();
            }
            foreach($rows1 as $row1){
                Riwayattalentam::Create([
                    'nip' => $row1->nip, 
                    'tahun' => $tahuncari,
                    'semester' => $semestercari,
                    'nama_talenta' => $row1->nama_talenta,
                    'kode' => $row1->nip."-".$row1->tahuncari."-".$tahuncari
                ]);
            }
            return response()->json(["status" => 'sukses', "pesan" => 'Sukses finalisasi talenta']);
        } catch (\Exception $e) {
            return response()->json(["status" => 'error', "pesan" => 'Gagal finalisasi talenta ' . $e->getMessage()]);
        }
    }

    public function resetTalenta(Request $request)
    {
        $tahuncari = $request->tahun;
        $semestercari = $request->semester;
        $kd_areacari = $request->kd_area;        
        $perintah = "";
        if($kd_areacari!="semua"){
            $perintah .= " and kd_area='$kd_areacari'";
        }
        try {
            if($kd_areacari!="semua"){
                DB::table('riwayat_talenta')->whereRaw("tahun='$tahuncari' and semester='$semestercari' and kd_area in (select nip from data_pegawai where kd_area='$kd_areacari')")->delete();                
            } else {
                DB::table('riwayat_talenta')->whereRaw("tahun='$tahuncari' and semester='$semestercari'")->delete();
            }
            return response()->json(["status" => 'sukses', "pesan" => 'Sukses finalisasi talenta']);
        } catch (\Exception $e) {
            return response()->json(["status" => 'error', "pesan" => 'Gagal finalisasi talenta ' . $e->getMessage()]);
        }
    }

}
