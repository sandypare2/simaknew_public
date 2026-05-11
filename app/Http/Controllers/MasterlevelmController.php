<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\Datatables;
use App\models\Masterlevelm;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class MasterlevelmController extends Controller
{
    private $masterlevelm;
    public function __construct(Masterlevelm $masterlevelm)
    {
        $this->masterlevelm = $masterlevelm;
    }

    public function index(Request $request)
    {        
        $tahun_ini = Carbon::now()->format('Y');
        $bulan_ini = Carbon::now()->format('Y-m');
        $tgl_ini = Carbon::now()->format('Y-m-01');
        if ($request->ajax()) {            
            $data = Masterlevelm::selectRaw("*")
            ->orderBy('id','desc');
            return Datatables::eloquent($data)
                ->addIndexColumn()
                ->filter(function ($instance) use ($request) {
                    if (!empty($request->get('search'))) {
                        $instance->whereRaw("(master_divisi.nama_divisi like '%" . request('search') . "%')");
                    }
                })
                ->addColumn('aksi', function ($data) {
                    $a = '<div class="acao text-center">';
                    $a .= '<a href="javascript:void(0)" data-id="'.$data->id.'" title="Edit Data" class="edit_row"><button type="button" class="btn btn-light-warning icon-btn-sm" style="margin-right:3px;"><i class="ri-pencil-fill fs-14"></i></button></a>';
                    $a .= '<a href="javascript:void(0)" data-id="'.$data->id.'" title="Hapus Data" class="delete_row"><button type="button" class="btn btn-light-danger icon-btn-sm"><i class="ri-delete-bin-line fs-14"></i></button></a>';
                    $a .= '</div>';
                    return $a;
                })
                ->rawColumns(['aksi'])
                ->make(true);
        }

        return view('admin.masterlevelm.index');

    }    

    public function store(Request $request)
    {
        $id = intval($request->id);
        try {
            Masterlevelm::updateOrCreate([
                'id' => $id, 
            ],
            [
                'level_kpi' => $level_kpi,
                'nama_level_kpi' => $request->nama_level_kpi
            ]);    
            return response()->json(["status" => 'sukses', "pesan" => 'Sukses simpan data']);
        } catch (\Exception $e) {
            return response()->json(["status" => 'error', "pesan" => 'Gagal simpan data ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $masterlevelm = Masterlevelm::find($id);
        return response()->json($masterlevelm);
    }

    public function destroy(Request $request)
    {
        $id = $request->id;
        DB::table('master_level_kpi')->where('id', $id)->delete();
      
        return response()->json(['success'=>'Sukses hapus data.']);
    }


}
