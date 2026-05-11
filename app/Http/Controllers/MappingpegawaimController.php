<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\Datatables;
use App\models\Mappingpegawaim;
use App\models\Masteraream;
use App\models\Masterlevelm;
use App\models\Masterdivisim;
use App\models\Jenjangjabatanm;
use App\models\Gradem;
use App\models\Masterjenispegawai;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class MappingpegawaimController extends Controller
{
    private $mappingpegawaim;
    private $masteraream;
    private $masterlevelm;
    private $masterdivisim;
    private $jenjangjabatanm;
    private $gradem;
    private $masterjenispegawai;
    public function __construct(Mappingpegawaim $mappingpegawaim, Masteraream $masteraream, Masterlevelm $masterlevelm, Masterdivisim $masterdivisim, Jenjangjabatanm $jenjangjabatanm, Gradem $gradem, Masterjenispegawai $masterjenispegawai)
    {
        $this->mappingpegawaim = $mappingpegawaim;
        $this->masteraream = $masteraream;
        $this->masterlevelm = $masterlevelm;
        $this->masterdivisim = $masterdivisim;
        $this->jenjangjabatanm = $jenjangjabatanm;
        $this->gradem = $gradem;
        $this->masterjenispegawai = $masterjenispegawai;
    }

    public function index(Request $request)
    {        
        $tahun_ini = Carbon::now()->format('Y');
        $bulan_ini = Carbon::now()->format('Y-m');
        $tgl_ini = Carbon::now()->format('Y-m-01');
        if ($request->ajax()) {            
            $data = Mappingpegawaim::selectRaw("
                data_pegawai.*,
                ifnull(b.nama_area,'') as nama_area,
                ifnull(c.nama_level_kpi,'') as nama_level_kpi,
                ifnull(d.nama_divisi,'') as nama_divisi,
                ifnull(e.nama,'') as nama_approval,
                ifnull(f.nama,'') as nama_finalisasi,
                ifnull(g.nama_jenis,'') as nama_jenis
            ")
            ->leftJoin('master_area as b','b.kd_area','=','data_pegawai.kd_area')
            ->leftJoin('master_level_kpi as c', function($join){
                $join->whereRaw("c.jenis_kpi=data_pegawai.jenis_kpi and c.level_kpi=data_pegawai.level_kpi");
            })    
            ->leftJoin('master_divisi as d','d.kd_divisi','=','data_pegawai.kd_divisi')
            ->leftJoin('data_pegawai as e','e.nip','=','data_pegawai.approval')
            ->leftJoin('data_pegawai as f','f.nip','=','data_pegawai.finalisasi')
            ->leftJoin('master_jenis_pegawai as g','g.kd_jenis','=','data_pegawai.kd_jenis')
            ->whereRaw("data_pegawai.aktif='1' and data_pegawai.payroll='1' and data_pegawai.aktif_simkp='1'")
            ->orderBy('id','desc');
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
                    $a .= '<a href="javascript:void(0)" data-id="'.$data->id.'" title="Edit Data" class="edit_row"><button type="button" class="btn btn-light-warning icon-btn-sm" style="margin-right:3px;"><i class="ri-pencil-fill font-size-14"></i></button></a>';
                    // $a .= '<a href="javascript:void(0)" data-id="'.$data->id.'" title="Hapus Data" class="delete_row"><button type="button" class="btn btn-icon btn-sm btn-danger"><span class="ti ti-trash ti-sm"></span></button></a>';
                    $a .= '</div>';
                    return $a;
                })
                ->rawColumns(['aksi'])
                ->make(true);
        }

        return view('admin.mappingpegawaim.index', [
            'masteraream' => $this->masteraream->getAllData(),
            'masterdivisim' => $this->masterdivisim->getAllData(),
            'mappingpegawaim' => $this->mappingpegawaim->getAllData(),
            'jenjangjabatanm' => $this->jenjangjabatanm->getAllData(),
            'gradem' => $this->gradem->getAllData(),
            'masterjenispegawai' => $this->masterjenispegawai->getAllData()
        ]);

    }    

    public function store(Request $request)
    {
        $id = intval($request->id);
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
        try {
            Mappingpegawaim::updateOrCreate([
                'id' => $id, 
            ],
            [
                'nip' => $request->nip, 
                'nama' => $request->nama, 
                'jabatan' => $request->jabatan, 
                'kd_jenis' => $request->kd_jenis, 
                'jenjang_jabatan' => $request->jenjang_jabatan, 
                'grade' => $request->grade, 
                'peg' => $request->peg, 
                'kd_area' => $request->kd_area, 
                'jenis_kpi' => $jenis_kpi,
                'level_kpi' => $request->level_kpi,
                'kd_divisi' => $request->kd_divisi,
                'approval' => $request->approval,
                'finalisasi' => $request->finalisasi
            ]);    
            return response()->json(["status" => 'sukses', "pesan" => 'Sukses simpan data']);
        } catch (\Exception $e) {
            return response()->json(["status" => 'error', "pesan" => 'Gagal simpan data ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $mappingpegawaim = Mappingpegawaim::find($id);
        return response()->json($mappingpegawaim);
    }

    public function destroy(Request $request)
    {
        $id = $request->id;
        try {
            Mappingpegawaim::updateOrCreate([
                'id' => $id, 
            ],
            [
                'kd_area' => "", 
                'jenis_kpi' => "",
                'level_kpi' => "",
                'kd_divisi' => ""
            ]);    
            return response()->json(['success'=>true]);
        } catch (\Exception $e) {
            return response()->json(['success'=>false]);
        }
      
        return response()->json(['success'=>'Sukses hapus data.']);
    }

    public function fetchLevel(Request $request)
    {
        $kd_area = $request->kd_area;
        $data['filter_level'] = Masteraream::selectRaw("
            master_area.jenis_kpi,
            b.level_kpi,
            b.nama_level_kpi
        ")
        ->leftJoin('master_level_kpi as b','b.jenis_kpi','=','master_area.jenis_kpi')
        ->whereRaw("master_area.kd_area='$kd_area'")
        ->get();
        return response()->json($data);
    }

    public function fetchPegawai(Request $request)
    {
        // $kd_area = $request->kd_area;
        $rows1 = DB::table('organikpcn.data_pegawai')
        ->selectRaw("*")
        ->get();
        foreach($rows1 as $row1){
            $nip = $row1->nip;
            $nama = $row1->nama;
            $jabatan = $row1->jabatan;
            $aktif = $row1->aktif;
            $payroll = $row1->payroll;
            $aktif_simkp = $row1->aktif_simkp;
            $aktif = $row1->aktif;

            Mappingpegawaim::updateOrCreate([
                'nip' => $row1->nip, 
            ],
            [
                'nip' => $row1->nip, 
                'nama' => $row1->nama,
                'jabatan' => $row1->jabatan,
                'aktif' => $row1->aktif,
                'payroll' => $row1->payroll,
                'aktif_simkp' => $row1->aktif_simkp
            ]);    
        }
        return response()->json($data);
    }


}
