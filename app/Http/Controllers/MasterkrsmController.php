<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\Datatables;
use App\models\Masterkrsm;
use App\models\Matakuliahm;
use App\models\Prodim;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class MasterkrsmController extends Controller
{
    private $masterkrsm;
    private $matakuliahm;
    private $prodim;
    public function __construct(Masterkrsm $masterkrsm, Prodim $prodim, Matakuliahm $matakuliahm)
    {
        $this->masterkrsm = $masterkrsm;
        $this->matakuliahm = $matakuliahm;
        $this->prodim = $prodim;
    }

    public function index(Request $request)
    {        
        $tahun_ini = Carbon::now()->format('Y');
        $bulan_ini = Carbon::now()->format('Y-m');
        $tgl_ini = Carbon::now()->format('Y-m-01');
        if ($request->ajax()) {            
            $data = Masterkrsm::selectRaw("
                master_krs.*,
                b.nama_prodi as nama_prodi,
                c.nama_mata_kuliah as nama_mata_kuliah
            ")
            ->leftJoin('master_prodi as b','b.kd_prodi','=','master_krs.kd_prodi')
            ->leftJoin('master_mata_kuliah as c','c.kd_mata_kuliah','=','master_krs.kd_mata_kuliah')
            ->orderBy('id','desc');
            return Datatables::eloquent($data)
                ->addIndexColumn()
                ->filter(function ($instance) use ($request) {
                    if (!empty($request->get('kd_prodicari')) && !empty($request->get('kd_prodicari')!="semua")) {
                        $instance->whereRaw("master_krs.kd_prodi='" . request('kd_prodicari') . "'");
                    }
                    if (!empty($request->get('semestercari')) && !empty($request->get('semestercari')!="semua")) {
                        $instance->whereRaw("master_krs.semester='" . request('semestercari') . "'");
                    }
                    if (!empty($request->get('search'))) {
                        $instance->whereRaw("(master_prodi.mata_prodi like '%" . request('search') . "%' or master_mata_kuliah.nama_mata_kuliah like '%" . request('search') . "%')");
                    }
                })
                ->addColumn('aksi', function ($data) {
                    $a = '<div class="acao text-center">';
                    $a .= '<a href="javascript:void(0)" data-id="'.$data->id.'" title="Edit Data" class="edit_row"><button type="button" class="btn btn-icon btn-sm btn-warning" style="margin-right:3px;"><span class="ti ti-pencil-star ti-sm"></span></button></a>';
                    $a .= '<a href="javascript:void(0)" data-id="'.$data->id.'" title="Hapus Data" class="delete_row"><button type="button" class="btn btn-icon btn-sm btn-danger"><span class="ti ti-trash ti-sm"></span></button></a>';
                    $a .= '</div>';
                    return $a;
                })
                ->rawColumns(['aksi'])
                ->make(true);
        }

        return view('admin.masterkrsm.index', [
            'matakuliahm' => $this->matakuliahm->getAllData(),
            'prodim' => $this->prodim->getAllData()
        ]);

    }    

    public function store(Request $request)
    {
        $id = intval($request->id);
        $kd_prodi = $request->kd_prodi;
        $kd_mata_kuliah = $request->kd_mata_kuliah;
        $kode2 = $request->kode2;
        $kode = $kd_prodi."-".$kd_mata_kuliah;   
        // dd($request->sks);     
        try {
            Masterkrsm::updateOrCreate([
                'id' => $id, 
            ],
            [
                'kd_prodi' => $request->kd_prodi, 
                'semester' => $request->semester,
                'kd_mata_kuliah' => $request->kd_mata_kuliah, 
                'sks' => $request->sks,
                'kode' => $kode
            ]);    
            return response()->json(["status" => 'sukses', "pesan" => 'Sukses simpan data']);
        } catch (\Exception $e) {
            return response()->json(["status" => 'error', "pesan" => 'Gagal simpan data ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $masterkrsm = Masterkrsm::find($id);
        return response()->json($masterkrsm);
    }

    public function destroy(Request $request)
    {
        $id = $request->id;
        DB::table('master_krs')->where('id', $id)->delete();
      
        return response()->json(['success'=>'Sukses hapus data.']);
    }


}
