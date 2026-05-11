<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\Datatables;
use App\models\Dosenm;
use App\models\Matakuliahm;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DosenmController extends Controller
{
    private $dosenm;
    private $matakuliahm;
    public function __construct(Dosenm $dosenm, Matakuliahm $matakuliahm)
    {
        $this->dosenm = $dosenm;
        $this->matakuliahm = $matakuliahm;
    }

    public function index(Request $request)
    {        
        $tahun_ini = Carbon::now()->format('Y');
        $bulan_ini = Carbon::now()->format('Y-m');
        $tgl_ini = Carbon::now()->format('Y-m-01');
        if ($request->ajax()) {            
            $data = Dosenm::selectRaw("
                master_dosen.*,
                GROUP_CONCAT(b.nama_mata_kuliah SEPARATOR '\n') as nama_mata_kuliah
            ")
            // ->leftJoin('master_mata_kuliah as b','master_dosen.mata_kuliah','=','b.kd_mata_kuliah')
            ->leftJoin('master_mata_kuliah as b', function($join){
                $join->whereRaw("find_in_set(b.kd_mata_kuliah,master_dosen.mata_kuliah)");
            })    
            ->groupBy('master_dosen.id')
            ->orderBy('master_dosen.id','desc');
            return Datatables::eloquent($data)
                ->addIndexColumn()
                ->filter(function ($instance) use ($request) {
                    if (!empty($request->get('search'))) {
                        $instance->whereRaw("(master_dosen.nama like '%" . request('search') . "%' or master_mata_kuliah.nama_mata_kuliah like '%" . request('search') . "%')");
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

        return view('admin.dosenm.index', [
            'matakuliahm' => $this->matakuliahm->getAllData()
        ]);

    }    

    public function show(Datauserm $datauserm)
    {
        return view('admin.datauserm.show', ['datauserm' => $datauserm]);
    }

    public function store(Request $request)
    {
        $id = intval($request->id);
        try {
            Dosenm::updateOrCreate([
                'id' => $id, 
            ],
            [
                'nip' => $request->nip, 
                'nama' => $request->nama, 
                'mata_kuliah' => $request->kd_mata_kuliah2 
            ]);    
            return response()->json(["status" => 'sukses', "pesan" => 'Sukses simpan data']);
        } catch (\Exception $e) {
            return response()->json(["status" => 'error', "pesan" => 'Gagal simpan data ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $dosenm = Dosenm::find($id);
        return response()->json($dosenm);
    }

    public function destroy(Request $request)
    {
        $id = $request->id;
        DB::table('master_dosen')->where('id', $id)->delete();
      
        return response()->json(['success'=>'Sukses hapus data.']);
    }


}
