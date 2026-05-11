<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\Datatables;
use App\models\Mappingkpim;
use App\models\Mappingfinalisasim;
use App\models\Mappingpenilaianm;
use App\models\Mappingpegawaim;
use App\models\Datacascadingm;
use App\models\Masteraream;
use App\models\Masterlevelm;
use App\models\Masterdivisim;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class MappingkpimController extends Controller
{
    private $mappingkpim;
    private $mappingfinalisasim;
    private $mappingpenilaianm;
    private $mappingpegawaim;
    private $datacascadingm;
    private $masteraream;
    private $masterlevelm;
    private $masterdivisim;
    public function __construct(Mappingkpim $mappingkpim, Mappingfinalisasim $mappingfinalisasim, Mappingpenilaianm $mappingpenilaianm, Mappingpegawaim $mappingpegawaim, Datacascadingm $datacascadingm, Masteraream $masteraream, Masterlevelm $masterlevelm, Masterdivisim $masterdivisim)
    {
        $this->mappingkpim = $mappingkpim;
        $this->mappingfinalisasim = $mappingfinalisasim;
        $this->mappingpenilaianm = $mappingpenilaianm;
        $this->mappingpegawaim = $mappingpegawaim;
        $this->datacascadingm = $datacascadingm;
        $this->masteraream = $masteraream;
        $this->masterlevelm = $masterlevelm;
        $this->masterdivisim = $masterdivisim;
    }

    public function index(Request $request)
    {        
        $tahun_ini = intval(Carbon::now()->format('Y'));
        $tahun_awal = 2024;
        $datatahun = [];
        for($i=$tahun_ini;$i>=$tahun_awal;$i--){
            $datatahun[] = $i;
        }

        if ($request->ajax()) { 
            $tahuncari = $request->tahuncari;     
            $data = Mappingkpim::selectRaw("
                kpi_pegawai.nip as nip2,
                kpi_pegawai.tahun as tahun,
                b.*,
                ifnull(c.nama_area,'') as nama_area,
                ifnull(d.nama_level_kpi,'') as nama_level_kpi,
                ifnull(e.nama_divisi,'') as nama_divisi
            ")
            ->leftJoin('data_pegawai as b','b.nip','=','kpi_pegawai.nip')
            ->leftJoin('master_area as c','c.kd_area','=','kpi_pegawai.kd_area')
            ->leftJoin('master_level_kpi as d', function($join){
                $join->whereRaw("d.jenis_kpi=kpi_pegawai.jenis_kpi and d.level_kpi=kpi_pegawai.level_kpi");
            })    
            ->leftJoin('master_divisi as e','e.kd_divisi','=','kpi_pegawai.kd_divisi')
            ->whereRaw("kpi_pegawai.tahun='$tahuncari' and b.aktif='1' and b.payroll='1' and b.aktif_simkp='1'")
            // ->groupBy('kpi_pegawai.nip','b.id','c.nama_area','d.nama_level_kpi','e.nama_divisi')
            ->groupBy('kpi_pegawai.nip')
            ->orderBy('b.id','asc');
            return Datatables::eloquent($data)
                ->addIndexColumn()
                ->filter(function ($instance) use ($request) {
                    if (!empty($request->get('kd_areacari')) && !empty($request->get('kd_areacari')!="semua")) {
                        $instance->whereRaw("kpi_pegawai.kd_area='" . request('kd_areacari') . "'");
                    }
                    if (!empty($request->get('search'))) {
                        $instance->whereRaw("(kpi_pegawai.nip='" . request('search') . "' or b.nama like '%" . request('search') . "%')");
                    }
                })
                ->addColumn('aksi', function ($data) {
                    $a = '<div class="acao text-center">';
                    // $a .= '<a href="javascript:void(0)" data-id="'.$data->id.'" title="Edit Data" class="edit_row"><button type="button" class="btn btn-icon btn-sm btn-warning" style="margin-right:3px;"><span class="ti ti-pencil-star ti-sm"></span></button></a>';
                    // $a .= '<a href="javascript:void(0)" data-id="'.$data->id.'" title="Hapus Data" class="delete_row"><button type="button" class="btn btn-icon btn-sm btn-danger"><span class="ti ti-trash ti-sm"></span></button></a>';
                    $a .= '<a href="javascript:void(0)" data-tahun="'.$data->tahun.'" data-kd_area="'.$data->kd_area.'" data-nip="'.$data->nip.'" data-nama="'.$data->nama.'" data-jabatan="'.$data->jabatan.'" data-jenis_kpi="'.$data->jenis_kpi.'" data-level_kpi="'.$data->level_kpi.'" data-nama_level_kpi="'.$data->nama_level_kpi.'" data-kd_divisi="'.$data->kd_divisi.'" title="Rincian KPI" class="detail_row"><button type="button" class="btn btn-light-success icon-btn-sm" style="margin-right:3px;"><i class="ri-crosshair-2-line font-size-14"></i></button></a>';
                    $a .= '</div>';
                    return $a;
                })
                ->rawColumns(['aksi'])
                ->make(true);
        }

        return view('admin.mappingkpim.index', [
            'masteraream' => $this->masteraream->getAllData(),
            'masterdivisim' => $this->masterdivisim->getAllData()
        ],compact('datatahun'));

    }    

    public function store(Request $request)
    {
        $id = intval($request->id);
        try {
            $mappingkpim = Mappingkpim::whereRaw("id='$id'")
            ->update([
                'target01' => $request->target01,
                'target02' => $request->target02,
                'target03' => $request->target03,
                'target04' => $request->target04,
                'target05' => $request->target05,
                'target06' => $request->target06,
                'target07' => $request->target07,
                'target08' => $request->target08,
                'target09' => $request->target09,
                'target10' => $request->target10,
                'target11' => $request->target11,
                'target12' => $request->target12
            ]);
            return response()->json(["status" => 'sukses', "pesan" => 'Sukses simpan data']);
        } catch (\Exception $e) {
            return response()->json(["status" => 'error', "pesan" => 'Gagal simpan data ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $mappingkpim = Mappingkpim::selectRaw("
            kpi_pegawai.*,
            b.uraian as uraian
        ")
        ->leftJoin('cascading_kpi as b','b.kode_cascading','=','kpi_pegawai.kode_cascading')
        ->whereRaw("kpi_pegawai.id='$id'")
        ->orderBy('kode_cascading','asc')
        ->first();  
        return response()->json($mappingkpim);      
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
        $kd_areacari = $request->kd_areacari;
        $jenis_kpicari = $request->jenis_kpicari;
        $level_kpicari = $request->level_kpicari;
        $kd_divisicari = $request->kd_divisicari;
        $data = Datacascadingm::selectRaw("
            cascading_kpi.*,
            if(b.kd_divisi='00' or b.kd_divisi is null,'SEMUA',group_concat(b.nama_divisi order by b.kd_divisi separator '\n')) as nama_divisi,
            if(c.nip<>'' and c.nip<>'null','1','0') as pilihan,
            if(c.nip<>'' and c.nip<>'null',c.id,0) as id2,
            if(c.nip<>'' and c.nip<>'null',c.kode_kpi,0) as kode_kpi2
        ")
        ->leftJoin('master_divisi as b', function ($join) {
            $join->whereRaw("FIND_IN_SET(b.kd_divisi, cascading_kpi.kd_divisi)")
                ->orWhereNull('cascading_kpi.kd_divisi');
        })   
        ->leftJoin('kpi_pegawai as c', function($join) use($nipcari){
            $join->whereRaw("c.kode_cascading=cascading_kpi.kode_cascading and c.kd_area=cascading_kpi.kd_area and c.nip='$nipcari'");
        })    
        // ->whereRaw("cascading_kpi.tahun='$tahuncari' and cascading_kpi.jenis_kpi='$jenis_kpicari' and cascading_kpi.level_kpi='$level_kpicari' and (cascading_kpi.kd_divisi='$kd_divisicari' or cascading_kpi.kd_divisi='00' or cascading_kpi.kd_divisi='')")
        // ->whereRaw("cascading_kpi.tahun='$tahuncari' and cascading_kpi.jenis_kpi='$jenis_kpicari' and cascading_kpi.level_kpi='$level_kpicari' and (cascading_kpi.kd_divisi='00' or cascading_kpi.kd_divisi is null or find_in_set(cascading_kpi.kd_divisi,'$kd_divisicari'))")
        ->whereRaw("cascading_kpi.tahun='$tahuncari' and cascading_kpi.kd_area='$kd_areacari' and cascading_kpi.jenis_kpi='$jenis_kpicari' and cascading_kpi.level_kpi='$level_kpicari' and (cascading_kpi.kd_divisi='00' or cascading_kpi.kd_divisi is null or cascading_kpi.kd_divisi like '%$kd_divisicari%')")
        ->groupBy('cascading_kpi.id','cascading_kpi.kode_cascading','b.kd_divisi','c.id')
        ->orderBy('cascading_kpi.kode_cascading','asc');

        // $data = Datacascadingm::selectRaw("
        //     cascading_kpi.*,
        //     CASE 
        //         WHEN b.kd_divisi = '00' OR b.kd_divisi IS NULL THEN 'SEMUA'
        //         ELSE GROUP_CONCAT(b.nama_divisi ORDER BY b.kd_divisi SEPARATOR '\n')
        //     END as nama_divisi,
        //     CASE 
        //         WHEN c.nip IS NOT NULL AND c.nip <> '' AND c.nip <> 'null' THEN '1'
        //         ELSE '0'
        //     END as pilihan,
        //     COALESCE(c.id, 0) as id2,
        //     COALESCE(c.kode_kpi, 0) as kode_kpi2
        // ")
        // ->leftJoin('master_divisi as b', function ($join) {
        //     $join->whereRaw("FIND_IN_SET(b.kd_divisi, cascading_kpi.kd_divisi)")
        //         ->orWhereNull('cascading_kpi.kd_divisi');
        // })   
        // ->leftJoin('kpi_pegawai as c', function($join) use($nipcari) {
        //     $join->on('c.kode_cascading', '=', 'cascading_kpi.kode_cascading')
        //         ->on('c.kd_area', '=', 'cascading_kpi.kd_area')
        //         ->where('c.nip', '=', $nipcari);
        // })    
        // ->where('cascading_kpi.tahun', $tahuncari)
        // ->where('cascading_kpi.kd_area', $kd_areacari)
        // ->where('cascading_kpi.jenis_kpi', $jenis_kpicari)
        // ->where('cascading_kpi.level_kpi', $level_kpicari)
        // ->where(function($query) use($kd_divisicari) {
        //     $query->where('cascading_kpi.kd_divisi', '00')
        //         ->orWhereNull('cascading_kpi.kd_divisi')
        //         ->orWhereRaw("FIND_IN_SET(?, cascading_kpi.kd_divisi)", [$kd_divisicari]);
        // })
        // ->groupBy('cascading_kpi.id', 'cascading_kpi.kode_cascading', 'b.kd_divisi', 'c.id')
        // ->orderBy('cascading_kpi.kode_cascading', 'asc');
        return Datatables::eloquent($data)
            ->addIndexColumn()
            ->filter(function ($instance) use ($request) {
                // if (!empty($request->get('search'))) {
                //     $instance->whereRaw("(jadwal.nama_mata_kuliah like '%" . request('search') . "%')");
                // }
            })
            ->addColumn('aksi', function ($data) use($nipcari) {
                $a = '<div class="acao text-center">';
                if(Auth::user()->role=="superadmin"){
                    if(intval($data->pilihan)==1){
                        $a .= '<a><button type="button" class="btn btn-light-secondary icon-btn-sm" style="margin-right:3px;"><i class="ri-check-line fs-14"></i></button></a>';
                        $a .= '<a href="javascript:void(0)" data-id="'.$data->id.'" data-id2="'.$data->id2.'" data-kode_kpi2="'.$data->kode_kpi2.'" title="Batal" class="batal_row"><button type="button" class="btn btn-light-danger icon-btn-sm" style="margin-right:3px;"><i class="ri-close-line fs-14"></i></button></a>';
                    } else {
                        $a .= '<a href="javascript:void(0)" data-id="'.$data->id.'" data-tahun="'.$data->tahun.'" data-nip="'.$nipcari.'" data-kode_cascading="'.$data->kode_cascading.'" data-kode_cascading2="'.$data->kode_cascading2.'" data-jenis_kpi="'.$data->jenis_kpi.'" data-kd_divisi="'.$data->kd_divisi.'" data-level_kpi="'.$data->level_kpi.'" data-kode_kpi2="'.$data->kode_kpi2.'" title="Pilih" class="pilih_row"><button type="button" class="btn btn-light-success icon-btn-sm" style="margin-right:3px;"><i class="ri-check-line fs-14"></i></span></button></a>';
                        $a .= '<a><button type="button" class="btn btn-light-secondary icon-btn-sm" style="margin-right:3px;"><i class="ri-close-line fs-14"></i></span></button></a>';
                    }
                } else {
                    $a .= '<a><button type="button" class="btn btn-light-secondary icon-btn-sm" style="margin-right:3px;"><i class="ri-check-line fs-14"></i></button></a>';
                    $a .= '<a><button type="button" class="btn btn-light-secondary icon-btn-sm" style="margin-right:3px;"><i class="ri-close-line fs-14"></i></button></a>';
                }
                $a .= '</div>';
                return $a;
            })
            ->rawColumns(['aksi'])
            ->make(true);        
    }

    
    public function fetchRincian(Request $request)
    {
        $tahuncari = $request->tahuncari;
        $nipcari = $request->nipcari;
        $kd_areacari = $request->kd_areacari;
        $data = Mappingkpim::selectRaw("
            kpi_pegawai.nip,
            kpi_pegawai.kode_kpi,
            b.*
        ")
        ->leftJoin('cascading_kpi as b','b.kode_cascading','=','kpi_pegawai.kode_cascading')
        ->whereRaw("kpi_pegawai.tahun='$tahuncari' and kpi_pegawai.kd_area='$kd_areacari' and kpi_pegawai.nip='$nipcari'")
        ->orderBy('kpi_pegawai.kode_cascading','asc');
        return Datatables::eloquent($data)
            ->addIndexColumn()
            ->filter(function ($instance) use ($request) {
                // if (!empty($request->get('search'))) {
                //     $instance->whereRaw("(jadwal.nama_mata_kuliah like '%" . request('search') . "%')");
                // }
            })
            // ->addColumn('aksi', function ($data) use($nipcari) {
            //     $a = '<div class="acao text-center">';
            //     $a .= '<a href="javascript:void(0)" data-id="'.$data->id.'" title="Edit Data" class="edit_row"><button type="button" class="btn btn-icon btn-sm btn-warning" style="margin-right:3px;"><span class="ti ti-pencil-star ti-sm"></span></button></a>';
            //     $a .= '</div>';
            //     return $a;
            // })
            ->rawColumns(['aksi'])
            ->make(true);        
    }    

    public function savePilih(Request $request)
    {
        $nip = $request->nip;
        $tahun = $request->tahun;
        $jenis_kpi = $request->jenis_kpi;
        $kd_divisi = $request->kd_divisi;
        $level_kpi = $request->level_kpi;
        $kode_cascading = $request->kode_cascading;
        $kode_cascading2 = $request->kode_cascading2;
        $kode_kpi = $nip."-".$kode_cascading;
        try {
            $mappingkpim = Mappingkpim::Create([
                'tahun' => $tahun, 
                'jenis_kpi' => $jenis_kpi, 
                'kd_divisi' => $kd_divisi,
                'level_kpi' => $level_kpi,
                'nip' => $nip,
                'kode_cascading' => $kode_cascading,
                'kode_cascading2' => $kode_cascading2,
                'kode_kpi' => $kode_kpi
            ]); 
            $mappingfinalisasim = Mappingfinalisasim::Create([
                'tahun' => $tahun, 
                'jenis_kpi' => $jenis_kpi, 
                'kd_divisi' => $kd_divisi,
                'level_kpi' => $level_kpi,
                'nip' => $nip,
                'kode_cascading' => $kode_cascading,
                'kode_cascading2' => $kode_cascading2,
                'kode_kpi' => $kode_kpi
            ]); 
            return response()->json(["status" => 'sukses', "pesan" => 'Sukses simpan data']);
        } catch (\Exception $e) {
            return response()->json(["status" => 'error', "pesan" => 'Gagal simpan data ' . $e->getMessage()]);
        }
    }
    
    public function saveBatal(Request $request)
    {
        $id2 = intval($request->id2);
        $kode_kpi2 = $request->kode_kpi2;
        // dd($kode_kpi2);
        try {
            DB::table('kpi_pegawai')->where('kode_kpi', $kode_kpi2)->delete();
            DB::table('finalisasi_Kpi')->where('kode_kpi', $kode_kpi2)->delete();
            return response()->json(["status" => 'sukses', "pesan" => 'Sukses simpan data']);
        } catch (\Exception $e) {
            return response()->json(["status" => 'error', "pesan" => 'Gagal simpan data ' . $e->getMessage()]);
        }
    }    

}
