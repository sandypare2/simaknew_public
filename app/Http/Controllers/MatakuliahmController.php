<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\Datatables;
use App\models\Matakuliahm;
use App\models\Prodim;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class MatakuliahmController extends Controller
{
    private $matakuliahm;
    private $prodim;
    public function __construct(Matakuliahm $matakuliahm, Prodim $prodim)
    {
        $this->matakuliahm = $matakuliahm;
        $this->prodim = $prodim;
    }

    public function index(Request $request)
    {        
        $tahun_ini = Carbon::now()->format('Y');
        $bulan_ini = Carbon::now()->format('Y-m');
        $tgl_ini = Carbon::now()->format('Y-m-01');
        if ($request->ajax()) {            
            $data = Matakuliahm::selectRaw("*")
            ->orderBy('id','desc');
            return Datatables::eloquent($data)
                ->addIndexColumn()
                ->filter(function ($instance) use ($request) {
                    if (!empty($request->get('search'))) {
                        $instance->whereRaw("nama_mata_kuliah like '%" . request('search') . "%'");
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

        return view('admin.matakuliahm.index', [
            'prodim' => $this->prodim->getAllData()
        ]);

    }    

    public function show(Datauserm $datauserm)
    {
        return view('admin.datauserm.show', ['datauserm' => $datauserm]);
    }

    public function store(Request $request)
    {
        $id = intval($request->id);
        $kd_prodi = $request->kd_prodi;
        $kd_mata_kuliah2 = $request->kd_mata_kuliah2;
        if($id==0){
            $row4 = DB::table('master_mata_kuliah')->selectRaw("max(kd_mata_kuliah) as kd_mata_kuliah2")
            ->first();
            if($row4){
                $kd_mata_kuliah2 = intval($row4->kd_mata_kuliah2);
            } else {
                $kd_mata_kuliah2 = 0;
            }
            $kd_mata_kuliah = str_pad(intval($kd_mata_kuliah2)+1,3,"0",STR_PAD_LEFT);  
        } else {
            $kd_mata_kuliah = $kd_mata_kuliah2;
        }
        

        try {
            Matakuliahm::updateOrCreate([
                'id' => $id, 
            ],
            [
                'kd_mata_kuliah' => $kd_mata_kuliah, 
                'nama_mata_kuliah' => $request->nama_mata_kuliah
            ]);    
            return response()->json(["status" => 'sukses', "pesan" => 'Sukses simpan data']);
        } catch (\Exception $e) {
            return response()->json(["status" => 'error', "pesan" => 'Gagal simpan data ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $matakuliahm = Matakuliahm::find($id);
        return response()->json($matakuliahm);
    }

    public function destroy(Request $request)
    {
        $id = $request->id;
        DB::table('master_mata_kuliah')->where('id', $id)->delete();
      
        return response()->json(['success'=>'Sukses hapus data.']);
    }


}
