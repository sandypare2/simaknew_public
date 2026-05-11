<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\Datatables;
use App\models\Prodim;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ProdimController extends Controller
{
    private $prodim;
    public function __construct(Prodim $prodim)
    {
        $this->prodim = $prodim;
    }

    public function index(Request $request)
    {        
        $tahun_ini = Carbon::now()->format('Y');
        $bulan_ini = Carbon::now()->format('Y-m');
        $tgl_ini = Carbon::now()->format('Y-m-01');
        if ($request->ajax()) {            
            $data = Prodim::selectRaw("*")
            ->orderBy('id','desc');
            return Datatables::eloquent($data)
                ->addIndexColumn()
                ->filter(function ($instance) use ($request) {
                    if (!empty($request->get('search'))) {
                        $instance->whereRaw("(master_prodi.nama_prodi like '%" . request('search') . "%')");
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

        return view('admin.prodim.index');

    }    

    public function store(Request $request)
    {
        $id = intval($request->id);
        $kd_prodi2 = $request->kd_prodi2;
        if($id==0){
            $row4 = DB::table('master_prodi')->selectRaw("max(kd_prodi) as kd_prodi3")
            ->first();
            if($row4){
                $kd_prodi3 = intval($row4->kd_prodi3);
            } else {
                $kd_prodi3 = 0;
            }
            $kd_prodi = str_pad(intval($kd_prodi3)+1,2,"0",STR_PAD_LEFT);  
        } else {
            $kd_prodi = $kd_prodi2;
        }

        try {
            Prodim::updateOrCreate([
                'id' => $id, 
            ],
            [
                'kd_prodi' => $kd_prodi,
                'nama_prodi' => $request->nama_prodi
            ]);    
            return response()->json(["status" => 'sukses', "pesan" => 'Sukses simpan data']);
        } catch (\Exception $e) {
            return response()->json(["status" => 'error', "pesan" => 'Gagal simpan data ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $prodim = Prodim::find($id);
        return response()->json($prodim);
    }

    public function destroy(Request $request)
    {
        $id = $request->id;
        DB::table('master_prodi')->where('id', $id)->delete();
      
        return response()->json(['success'=>'Sukses hapus data.']);
    }


}
