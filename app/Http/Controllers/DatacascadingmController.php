<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\Cascadingimportm;
use App\Imports\Cascading2importm;
use App\Imports\Cascadingcabangimportm;

use Yajra\DataTables\Facades\Datatables;
use App\models\Datacascadingm;
use App\models\Mappingkpim;
use App\models\Mappingapprovalm;
use App\models\Mappingfinalisasim;
use App\models\Mappingpenilaianm;
use App\models\masteraream;
use App\models\Masterlevelm;
use App\models\Divisim;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DatacascadingmController extends Controller
{
    private $datacascadingm;
    private $mappingkpim;
    private $mappingapprovalm;
    private $mappingfinalisasim;
    private $mappingpenilaianm;
    private $masteraream;
    private $divisim;
    private $masterlevelm;
    public function __construct(Datacascadingm $datacascadingm, Mappingkpim $mappingkpim, Mappingapprovalm $mappingapprovalm, Mappingfinalisasim $mappingfinalisasim, Mappingpenilaianm $mappingpenilaianm, Masteraream $masteraream, Divisim $divisim, Masterlevelm $masterlevelm)
    {
        $this->datacascadingm = $datacascadingm;
        $this->mappingkpim = $mappingkpim;
        $this->mappingapprovalm = $mappingapprovalm;
        $this->mappingfinalisasim = $mappingfinalisasim;
        $this->mappingpenilaianm = $mappingpenilaianm;
        $this->masteraream = $masteraream;
        $this->divisim = $divisim;
        $this->masterlevelm = $masterlevelm;
    }

    public function index(Request $request)
    {        
        $tahun_ini = intval(Carbon::now()->format('Y'));
        $tahun_awal = 2024;
        $datatahun = [];
        for($i=$tahun_ini;$i>=$tahun_awal;$i--){
            $datatahun[] = $i;
        }
        // dd($datatahun);
        if ($request->ajax()) {            
            $data = Datacascadingm::selectRaw("
                cascading_kpi.*,
                if(cascading_kpi.kd_divisi='00' or cascading_kpi.kd_divisi='' or cascading_kpi.kd_divisi is null,'SEMUA',b.nama_divisi) as nama_divisi,
                c.nama_level_kpi as nama_level_kpi,
                d.nama_area as nama_area
            ")
            ->leftJoin('master_divisi as b','b.kd_divisi','=','cascading_kpi.kd_divisi')
            // ->leftJoin('master_level_kpi as c','c.level_kpi','=','cascading_kpi.level_kpi')
            ->leftJoin('master_level_kpi as c', function($join){
                $join->whereRaw("c.jenis_kpi=cascading_kpi.jenis_kpi and c.level_kpi=cascading_kpi.level_kpi");
            })    
            ->leftJoin('master_area as d','d.kd_area','=','cascading_kpi.kd_area')
            // ->leftJoin('master_level_kpi as e', function($join){
            //     $join->whereRaw("e.jenis_kpi<>'PST' and e.level_kpi=cascading_kpi.level_kpi");
            // })    
            ->orderBy('jenis_kpi','asc')
            ->orderBy('kd_urut','asc');
            return Datatables::eloquent($data)
                ->addIndexColumn()
                ->filter(function ($instance) use ($request) {
                    if (!empty($request->get('tahuncari')) && !empty($request->get('tahuncari')!="semua")) {
                        $instance->whereRaw("cascading_kpi.tahun='" . request('tahuncari') . "'");
                    }
                    if (!empty($request->get('kd_areacari')) && !empty($request->get('kd_areacari')!="semua")) {
                        $instance->whereRaw("cascading_kpi.kd_area='" . request('kd_areacari') . "'");
                    }
                    if (!empty($request->get('search'))) {
                        $instance->whereRaw("(cascading_kpi.uraian like '%" . request('search') . "%')");
                    }
                })
                ->addColumn('aksi', function ($data) {
                    $a = '<div class="acao text-center">';
                    $a .= '<a href="javascript:void(0)" data-id="'.$data->id.'" title="Edit Data" class="edit_row"><button type="button" class="btn btn-light-warning icon-btn-sm" style="margin-right:3px;"><i class="ri-pencil-fill font-size-14"></i></button></a>';
                    // $a .= '<a href="javascript:void(0)" data-id="'.$data->id.'" title="Hapus Data" class="delete_row"><button type="button" class="btn btn-icon btn-sm btn-danger"><span class="ti ti-trash ti-sm"></span></button></a>';
                    $a .= '</div>';
                    return $a;
                })
                ->rawColumns(['aksi'])
                ->make(true);
        }

        return view('admin.datacascadingm.index',[
            'divisim' => $this->divisim->getAllData(),
            'masteraream' => $this->masteraream->getAllData()
        ],compact('datatahun'));

    }    

    public function store(Request $request)
    {
        $id = intval($request->id);
        $row4 = DB::table('cascading_kpi')
        ->selectRaw("level_kpi")
        ->whereRaw("id='$id'")
        ->first();
        $level_kpi4 = $row4->level_kpi;

        try {
            $datacascadingm = Datacascadingm::where('id', $id);
            $updateData = [];
            $updateData['satuan_kuantitas'] = $request->satuan_kuantitas;
            $updateData['satuan_kualitas'] = $request->satuan_kualitas;
            $updateData['satuan_waktu'] = $request->satuan_waktu;
            $updateData['level_kpi'] = $request->level_kpi;
            $updateData['target01kn'] = $request->target01kn;
            $updateData['target01kl'] = $request->target01kl;
            $updateData['target01wk'] = $request->target01wk;
            $updateData['target01'] = $request->target01;
            $updateData['target02kn'] = $request->target02kn;
            $updateData['target02kl'] = $request->target02kl;
            $updateData['target02wk'] = $request->target02wk;
            $updateData['target02'] = $request->target02;
            $updateData['target03kn'] = $request->target03kn;
            $updateData['target03kl'] = $request->target03kl;
            $updateData['target03wk'] = $request->target03wk;
            $updateData['target03'] = $request->target03;
            $updateData['target04kn'] = $request->target04kn;
            $updateData['target04kl'] = $request->target04kl;
            $updateData['target04wk'] = $request->target04wk;
            $updateData['target04'] = $request->target04;
            $updateData['target05kn'] = $request->target05kn;
            $updateData['target05kl'] = $request->target05kl;
            $updateData['target05wk'] = $request->target05wk;
            $updateData['target05'] = $request->target05;
            $updateData['target06kn'] = $request->target06kn;
            $updateData['target06kl'] = $request->target06kl;
            $updateData['target06wk'] = $request->target06wk;
            $updateData['target06'] = $request->target06;
            $updateData['target07kn'] = $request->target07kn;
            $updateData['target07kl'] = $request->target07kl;
            $updateData['target07wk'] = $request->target07wk;
            $updateData['target07'] = $request->target07;
            $updateData['target08kn'] = $request->target08kn;
            $updateData['target08kl'] = $request->target08kl;
            $updateData['target08wk'] = $request->target08wk;
            $updateData['target08'] = $request->target08;
            $updateData['target09kn'] = $request->target09kn;
            $updateData['target09kl'] = $request->target09kl;
            $updateData['target09wk'] = $request->target09wk;
            $updateData['target09'] = $request->target09;
            $updateData['target10kn'] = $request->target10kn;
            $updateData['target10kl'] = $request->target10kl;
            $updateData['target10wk'] = $request->target10wk;
            $updateData['target10'] = $request->target10;
            $updateData['target11kn'] = $request->target11kn;
            $updateData['target11kl'] = $request->target11kl;
            $updateData['target11wk'] = $request->target11wk;
            $updateData['target11'] = $request->target11;
            $updateData['target12kn'] = $request->target12kn;
            $updateData['target12kl'] = $request->target12kl;
            $updateData['target12wk'] = $request->target12wk;
            $updateData['target12'] = $request->target12;
            $datacascadingm->update($updateData); 

            if($datacascadingm){
                $row1 = DB::table('cascading_kpi')
                ->selectRaw("*")
                ->whereRaw("id='$id'")
                ->first();
                $uraian = $row1->uraian;
                $tahun = $row1->tahun;
                $kd_area = $row1->kd_area;
                $jenis_kpi = $row1->jenis_kpi;
                $kd_divisi = $row1->kd_divisi;
                $level_kpi = $row1->level_kpi;
                $kode_cascading = $row1->kode_cascading;
                $kode_cascading2 = $row1->kode_cascading2;
                $satuan_kuantitas = $row1->satuan_kuantitas;
                $satuan_kualitas = $row1->satuan_kualitas;
                $satuan_waktu = $row1->satuan_waktu;
                $prioritas = $row1->prioritas;
                $type_target = $row1->type_target;
                $polarisasi = $row1->polarisasi;
                $target01kn = $row1->target01kn;
                $target01kl = $row1->target01kl;
                $target01wk = $row1->target01wk;
                $target02kn = $row1->target02kn;
                $target02kl = $row1->target02kl;
                $target02wk = $row1->target02wk;
                $target03kn = $row1->target03kn;
                $target03kl = $row1->target03kl;
                $target03wk = $row1->target03wk;
                $target04kn = $row1->target04kn;
                $target04kl = $row1->target04kl;
                $target04wk = $row1->target04wk;
                $target05kn = $row1->target05kn;
                $target05kl = $row1->target05kl;
                $target05wk = $row1->target05wk;
                $target06kn = $row1->target06kn;
                $target06kl = $row1->target06kl;
                $target06wk = $row1->target06wk;
                $target07kn = $row1->target07kn;
                $target07kl = $row1->target07kl;
                $target07wk = $row1->target07wk;
                $target08kn = $row1->target08kn;
                $target08kl = $row1->target08kl;
                $target08wk = $row1->target08wk;
                $target09kn = $row1->target09kn;
                $target09kl = $row1->target09kl;
                $target09wk = $row1->target09wk;
                $target10kn = $row1->target10kn;
                $target10kl = $row1->target10kl;
                $target10wk = $row1->target10wk;
                $target11kn = $row1->target11kn;
                $target11kl = $row1->target11kl;
                $target11wk = $row1->target11wk;
                $target12kn = $row1->target12kn;
                $target12kl = $row1->target12kl;
                $target12wk = $row1->target12wk;
                $target01 = $row1->target01;
                $target02 = $row1->target02;
                $target03 = $row1->target03;
                $target04 = $row1->target04;
                $target05 = $row1->target05;
                $target06 = $row1->target06;
                $target07 = $row1->target07;
                $target08 = $row1->target08;
                $target09 = $row1->target09;
                $target10 = $row1->target10;
                $target11 = $row1->target11;
                $target12 = $row1->target12;                

                $perintah2 = "";
                if($level_kpi>=3){
                    $perintah2 .= " and find_in_set(kd_divisi,'$kd_divisi')";
                }
                $rows2 = DB::table('data_pegawai')
                ->selectRaw("*")
                ->whereRaw("aktif='1' and payroll='1' and aktif_simkp='1' and jenis_kpi='$jenis_kpi' and level_kpi='$level_kpi'".$perintah2)
                ->orderBy('id','asc')
                ->get();
                
                foreach($rows2 as $row2){
                    $nip = $row2->nip;                    
                    $kode_kpi = $nip."-".$kode_cascading;
                    $kode_penilaian = $tahun."-".$nip;

                    if($level_kpi!=$level_kpi4 && ($level_kpi>=3 || strpos(trim($uraian), "urjab") !== false || strpos(trim($uraian), "URJAB") !== false)){
                        $mappingkpim = Mappingkpim::firstOrCreate([
                            'kode_kpi' => $kode_kpi, 
                        ],
                        [
                            'tahun' => $tahun, 
                            'kd_area' => $kd_area,
                            'jenis_kpi' => $jenis_kpi, 
                            'kd_divisi' => $kd_divisi, 
                            'level_kpi' => $level_kpi, 
                            'nip' => $nip,
                            'kode_cascading' => $kode_cascading,
                            'kode_cascading2' => $kode_cascading2,
                            'kode_kpi' => $kode_kpi
                        ]); 
                        // $mappingapprovalm = Mappingapprovalm::updateOrCreate([
                        $mappingapprovalm = Mappingapprovalm::firstOrCreate([
                            'kode_kpi' => $kode_kpi, 
                        ],
                        [
                            'tahun' => $tahun, 
                            'kd_area' => $kd_area,
                            'jenis_kpi' => $jenis_kpi, 
                            'kd_divisi' => $kd_divisi, 
                            'level_kpi' => $level_kpi, 
                            'nip' => $nip,
                            'kode_cascading' => $kode_cascading,
                            'kode_cascading2' => $kode_cascading2,
                            'kode_kpi' => $kode_kpi
                        ]); 
                        // $mappingfinalisasim = Mappingfinalisasim::updateOrCreate([
                        $mappingfinalisasim = Mappingfinalisasim::firstOrCreate([
                            'kode_kpi' => $kode_kpi, 
                        ],
                        [
                            'tahun' => $tahun, 
                            'kd_area' => $kd_area,
                            'jenis_kpi' => $jenis_kpi, 
                            'kd_divisi' => $kd_divisi, 
                            'level_kpi' => $level_kpi, 
                            'nip' => $nip,
                            'kode_cascading' => $kode_cascading,
                            'kode_cascading2' => $kode_cascading2,
                            'kode_kpi' => $kode_kpi
                        ]);        
                        $mappingpenilaianm = Mappingpenilaianm::updateOrCreate([
                            'kode_penilaian' => $kode_penilaian,
                        ],
                        [
                            'tahun' => $tahun, 
                            'kd_area' => $kd_area,
                            'jenis_kpi' => $jenis_kpi, 
                            'kd_divisi' => $kd_divisi, 
                            'nip' => $nip,
                            'kode_penilaian' => $kode_penilaian
                        ]); 
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
        // $datacascadingm = Datacascadingm::find($id);
        // return response()->json($datacascadingm);
        $datacascadingm = Datacascadingm::selectRaw("
            cascading_kpi.*,
            b.nama_level_kpi as nama_level_kpi,
            if(cascading_kpi.kd_divisi='00' or cascading_kpi.kd_divisi='' or cascading_kpi.kd_divisi is null,'SEMUA',c.nama_divisi) as nama_divisi,
            d.nama_area as nama_area
        ")
        ->leftJoin('master_level_kpi as b', function($join){
            $join->whereRaw("b.jenis_kpi=cascading_kpi.jenis_kpi and b.level_kpi=cascading_kpi.level_kpi");
        })    
        // ->leftJoin('master_level_kpi as b','b.level_kpi','=','cascading_kpi.level_kpi')
        ->leftJoin('master_divisi as c','c.kd_divisi','=','cascading_kpi.kd_divisi')
        ->leftJoin('master_area as d','d.kd_area','=','cascading_kpi.kd_area')
        ->whereRaw("cascading_kpi.id='$id'")
        // ->orderBy('cascading_kpi.kode_cascading','asc')
        ->first();  
        return response()->json($datacascadingm);      
    }

    public function destroy(Request $request)
    {
        $id = $request->id;
        DB::table('data_mahasiswa')->where('id', $id)->delete();
      
        return response()->json(['success'=>'Sukses hapus data.']);
    }

    public function importCascading(Request $request)
    {
        $tahun = $request->tahun3;
        $kd_area = $request->kd_area3;

        $row3 = DB::table('master_area')
        ->selectRaw("*")
        ->whereRaw("kd_area='$kd_area'")
        ->first();
        $jenis_kpi = $row3->jenis_kpi;

		$file = $request->file('filecascading3');
 		$nama_file = rand().$file->getClientOriginalName();
		$file->move('file_upload',$nama_file);
        if($jenis_kpi=="pusat"){
		    $import = Excel::import(new Cascadingimportm($tahun,$kd_area), public_path('/file_upload/'.$nama_file)); 
        } else {
            $import = Excel::import(new Cascading2importm($tahun,$kd_area), public_path('/file_upload/'.$nama_file)); 
        }
        if($import) {
            return response()->json(['status'=>'sukses']);
        } else {
            return response()->json(['status'=>'gagal']);
        }        

    }

    public function importcabangCascading(Request $request)
    {
        $tahun = $request->tahun3;
        $kd_area = $request->kd_area3;
		$file = $request->file('filecascading3');
 		$nama_file = rand().$file->getClientOriginalName();
		$file->move('file_upload',$nama_file);
		$import = Excel::import(new Cascadingcabangimportm($tahun,$kd_area), public_path('/file_upload/'.$nama_file)); 
        if($import) {
            return response()->json(['status'=>'sukses']);
        } else {
            return response()->json(['status'=>'gagal']);
        }        

    }

    public function resetCascading(Request $request)
    {
        $tahun = $request->tahun4;
        $kd_area = $request->kd_area4;
        try {
            DB::table('cascading_kpi')->whereRaw("tahun='$tahun' and kd_area='$kd_area'")->delete();
            return response()->json(['status'=>'sukses']);
        } catch (\Exception $e) {
            return response()->json(['status'=>'gagal']);
        }        
    }

    public function prosesCascading(Request $request)
    {
        $tahuncari = $request->tahun5;
        $kd_areacari = $request->kd_area5;
        $kd_divisicari = $request->kd_divisi5;
        $perintah = "";
        if($kd_divisicari!="semua"){
            $perintah .= " and kd_divisi='$kd_divisicari'";
        }
        try {
            $rows1 = DB::table('cascading_kpi')
            ->selectRaw("*")
            ->whereRaw("tahun='$tahuncari' and kd_area='$kd_areacari'".$perintah)
            ->orderBy('kode_cascading','asc')
            ->get();
            // dd($rows1);
            foreach($rows1 as $row1){
                $uraian = $row1->uraian;
                $tahun = $row1->tahun;
                $kd_area = $row1->kd_area;
                $jenis_kpi = $row1->jenis_kpi;
                $kd_divisi = $row1->kd_divisi;
                $level_kpi = $row1->level_kpi;
                $kode_cascading = $row1->kode_cascading;
                $kode_cascading2 = $row1->kode_cascading2;
                $satuan_kuantitas = $row1->satuan_kuantitas;
                $satuan_kualitas = $row1->satuan_kualitas;
                $satuan_waktu = $row1->satuan_waktu;
                $prioritas = $row1->prioritas;
                $type_target = $row1->type_target;
                $polarisasi = $row1->polarisasi;
                $target01kn = $row1->target01kn;
                $target01kl = $row1->target01kl;
                $target01wk = $row1->target01wk;
                $target02kn = $row1->target02kn;
                $target02kl = $row1->target02kl;
                $target02wk = $row1->target02wk;
                $target03kn = $row1->target03kn;
                $target03kl = $row1->target03kl;
                $target03wk = $row1->target03wk;
                $target04kn = $row1->target04kn;
                $target04kl = $row1->target04kl;
                $target04wk = $row1->target04wk;
                $target05kn = $row1->target05kn;
                $target05kl = $row1->target05kl;
                $target05wk = $row1->target05wk;
                $target06kn = $row1->target06kn;
                $target06kl = $row1->target06kl;
                $target06wk = $row1->target06wk;
                $target07kn = $row1->target07kn;
                $target07kl = $row1->target07kl;
                $target07wk = $row1->target07wk;
                $target08kn = $row1->target08kn;
                $target08kl = $row1->target08kl;
                $target08wk = $row1->target08wk;
                $target09kn = $row1->target09kn;
                $target09kl = $row1->target09kl;
                $target09wk = $row1->target09wk;
                $target10kn = $row1->target10kn;
                $target10kl = $row1->target10kl;
                $target10wk = $row1->target10wk;
                $target11kn = $row1->target11kn;
                $target11kl = $row1->target11kl;
                $target11wk = $row1->target11wk;
                $target12kn = $row1->target12kn;
                $target12kl = $row1->target12kl;
                $target12wk = $row1->target12wk;
                $target01 = $row1->target01;
                $target02 = $row1->target02;
                $target03 = $row1->target03;
                $target04 = $row1->target04;
                $target05 = $row1->target05;
                $target06 = $row1->target06;
                $target07 = $row1->target07;
                $target08 = $row1->target08;
                $target09 = $row1->target09;
                $target10 = $row1->target10;
                $target11 = $row1->target11;
                $target12 = $row1->target12;

                // $array_divisi = explode(",",$kd_divisi);
                

                $perintah2 = "";
                if($level_kpi>=3 && $jenis_kpi=="pusat"){
                    $perintah2 .= " and find_in_set(kd_divisi,'$kd_divisi')";
                }
                $rows2 = DB::table('data_pegawai')
                ->selectRaw("*")
                ->whereRaw("aktif='1' and payroll='1' and aktif_simkp='1' and jenis_kpi='$jenis_kpi' and kd_area='$kd_area' and level_kpi='$level_kpi'".$perintah2)
                ->orderBy('id','asc')
                ->get();
                foreach($rows2 as $row2){
                    $nip = $row2->nip;
                    $level_kpi = $row2->level_kpi;
                    $kode_kpi = $nip."-".$kode_cascading;
                    $kode_penilaian = $tahun."-".$nip;

                    if(($jenis_kpi=="pusat" && $level_kpi>=3) || $jenis_kpi=="cabang" || strpos(trim($uraian), "urjab") !== false || strpos(trim($uraian), "URJAB") !== false){
                        $mappingkpim = Mappingkpim::firstOrCreate([
                            'kode_kpi' => $kode_kpi, 
                        ],
                        [
                            'tahun' => $tahun, 
                            'kd_area' => $kd_area,
                            'jenis_kpi' => $jenis_kpi, 
                            'kd_divisi' => $kd_divisi, 
                            'level_kpi' => $level_kpi, 
                            'nip' => $nip,
                            'kode_cascading' => $kode_cascading,
                            'kode_cascading2' => $kode_cascading2,
                            'kode_kpi' => $kode_kpi
                        ]); 
                        $mappingapprovalm = Mappingapprovalm::firstOrCreate([
                            'kode_kpi' => $kode_kpi, 
                        ],
                        [
                            'tahun' => $tahun, 
                            'kd_area' => $kd_area,
                            'jenis_kpi' => $jenis_kpi, 
                            'kd_divisi' => $kd_divisi, 
                            'level_kpi' => $level_kpi, 
                            'nip' => $nip,
                            'kode_cascading' => $kode_cascading,
                            'kode_cascading2' => $kode_cascading2,
                            'kode_kpi' => $kode_kpi
                        ]); 
                        $mappingfinalisasim = Mappingfinalisasim::firstOrCreate([
                            'kode_kpi' => $kode_kpi, 
                        ],
                        [
                            'tahun' => $tahun, 
                            'kd_area' => $kd_area,
                            'jenis_kpi' => $jenis_kpi, 
                            'kd_divisi' => $kd_divisi, 
                            'level_kpi' => $level_kpi, 
                            'nip' => $nip,
                            'kode_cascading' => $kode_cascading,
                            'kode_cascading2' => $kode_cascading2,
                            'kode_kpi' => $kode_kpi
                        ]);        
                        $mappingpenilaianm = Mappingpenilaianm::updateOrCreate([
                            'kode_penilaian' => $kode_penilaian,
                        ],
                        [
                            'tahun' => $tahun, 
                            'kd_area' => $kd_area,
                            'jenis_kpi' => $jenis_kpi, 
                            'kd_divisi' => $kd_divisi, 
                            'nip' => $nip,
                            'kode_penilaian' => $kode_penilaian
                        ]); 
                    }

                }
            }

            return response()->json(['status'=>'sukses']);
        } catch (\Exception $e) {
            return response()->json(['status'=>'gagal']);
        }        
    }

    public function resetmappingCascading(Request $request)
    {
        $tahuncari = $request->tahun6;
        $kd_areacari = $request->kd_area6;
        $kd_divisicari = $request->kd_divisi6;
        $perintah = "";
        if($kd_divisicari!="semua"){
            $perintah .= " and kd_divisi='$kd_divisicari'";
        }
        try {
            DB::table('kpi_pegawai')->whereRaw("tahun='$tahuncari' and kd_area='$kd_areacari'".$perintah)->delete();
            DB::table('approval_kpi')->whereRaw("tahun='$tahuncari' and kd_area='$kd_areacari'".$perintah)->delete();
            DB::table('finalisasi_kpi')->whereRaw("tahun='$tahuncari' and kd_area='$kd_areacari'".$perintah)->delete();
            DB::table('penilaian_pegawai')->whereRaw("tahun='$tahuncari' and kd_area='$kd_areacari'".$perintah)->delete();
            
            return response()->json(['status'=>'sukses']);
        } catch (\Exception $e) {
            return response()->json(['status'=>'gagal']);
        }        
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


}
