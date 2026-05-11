<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\Datatables;
use App\models\Datapegawaim;
use App\models\Historytalentam;
use App\models\Masteraream;
use App\models\Masterlevelm;
use App\models\Masterdivisim;
use App\models\Masterpengukuranm;
use App\models\Jenjangjabatanm;
use App\models\Gradem;
use App\models\Masterjenispegawai;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class HistorytalentamController extends Controller
{
    private $historytalentam;
    private $masteraream;
    private $masterpengukuranm;
    private $masterjenispegawai;
    public function __construct(Historytalentam $historytalentam, Masteraream $masteraream, Masterpengukuranm $masterpengukuranm, Masterjenispegawai $masterjenispegawai)
    {
        $this->historytalentam = $historytalentam;
        $this->masteraream = $masteraream;
        $this->masterpengukuranm = $masterpengukuranm;
        $this->masterjenispegawai = $masterjenispegawai;
    }

    public function index(Request $request)
    {        
        $tahun_ini = Carbon::now()->format('Y');
        $bulan_ini = Carbon::now()->format('Y-m');
        $tgl_ini = Carbon::now()->format('Y-m-01');
        $tahun_ini = intval(Carbon::now()->format('Y'));
        $tahun_awal = 2022;
        $datatahun = [];
        for($i=$tahun_ini;$i>=$tahun_awal;$i--){
            $datatahun[] = $i;
        }

        if ($request->ajax()) {      
            $tahuncari = $request->get('tahuncari');
            $semestercari = $request->get('semestercari');      
            $data = Datapegawaim::selectRaw("
                data_pegawai.*,
                ifnull(b.nama_area,'') as nama_area,
                ifnull(d.nama_divisi,'') as nama_divisi,
                ifnull(e.id,'') as id2,
                ifnull(e.tahun,'') as tahun,
                ifnull(e.semester,'') as semester,
                ifnull(e.nama_talenta,'') as nama_talenta,
                ifnull(g.nama_jenis,'') as nama_jenis
            ")
            ->leftJoin('master_area as b','b.kd_area','=','data_pegawai.kd_area')
            ->leftJoin('master_divisi as d','d.kd_divisi','=','data_pegawai.kd_divisi')
            // ->leftJoin('riwayat_talenta as e','e.nip','=','data_pegawai.nip')
            ->leftJoin('riwayat_talenta as e', function ($join) use ($tahuncari, $semestercari) {
                $join->on('e.nip', '=', 'data_pegawai.nip');
                if (!empty($tahuncari)) {
                    $join->where('e.tahun', '=', $tahuncari);
                }
                if (!empty($semestercari)) {
                    $join->where('e.semester', '=', $semestercari);
                }
            })
            ->leftJoin('master_jenis_pegawai as g','g.kd_jenis','=','data_pegawai.kd_jenis')
            ->when(
                $request->filled('kd_areacari') && $request->get('kd_areacari') !== 'semua',
                function ($q) use ($request) {
                    $q->where('data_pegawai.kd_area', $request->get('kd_areacari'));
                }
            )
            ->whereRaw("data_pegawai.aktif='1' and data_pegawai.payroll='1' and data_pegawai.aktif_simkp='1'")
            ->orderBy('id','desc');
            return Datatables::eloquent($data)
                ->addIndexColumn()
                ->filter(function ($instance) use ($request) {
                    if (!empty($request->get('search'))) {
                        $instance->whereRaw("(data_pegawai.nip='" . request('search') . "' or data_pegawai.nama like '%" . request('search') . "%')");
                    }
                })
                ->addColumn('aksi', function ($data) {
                    $a = '<div class="acao text-center">';
                    $a .= '<a href="javascript:void(0)" data-id="'.$data->id2.'" data-nip="'.$data->nip.'" data-nama="'.$data->nama.'" data-jabatan="'.$data->jabatan.'" data-nama_jenis="'.$data->nama_jenis.'" data-nama_area="'.$data->nama_area.'" data-nama_talenta="'.$data->nama_talenta.'" title="Edit Data" class="edit_row"><button type="button" class="btn btn-light-warning icon-btn-sm" style="margin-right:3px;"><i class="ri-pencil-fill font-size-14"></i></button></a>';
                    // $a .= '<a href="javascript:void(0)" data-id="'.$data->id.'" title="Hapus Data" class="delete_row"><button type="button" class="btn btn-icon btn-sm btn-danger"><span class="ti ti-trash ti-sm"></span></button></a>';
                    $a .= '</div>';
                    return $a;
                })
                ->rawColumns(['aksi'])
                ->make(true);
        }

        return view('admin.historytalentam.index', [
            'masteraream' => $this->masteraream->getAllData(),
            'masterpengukuranm' => $this->masterpengukuranm->getAllData2(),
            'masterjenispegawai' => $this->masterjenispegawai->getAllData()
        ],compact('datatahun'));

    }    

    public function store(Request $request)
    {
        $id = intval($request->id);
        try {
            Historytalentam::updateOrCreate([
                'id' => $id, 
            ],
            [
                'nip' => $request->nip, 
                'tahun' => $request->tahun, 
                'semester' => $request->semester, 
                'nama_talenta' => $request->nama_talenta, 
                'kode' => $request->nip.'-'.$request->tahun.'-'.$request->semester
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
