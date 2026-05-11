<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\Datatables;
use App\models\Masterregionm;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class MasterregionmController extends Controller
{
    private $masterregionm;
    public function __construct(Masterregionm $masterregionm)
    {
        $this->masterregionm = $masterregionm;
    }

    public function index(Request $request)
    {        
        $tahun_ini = Carbon::now()->format('Y');
        $bulan_ini = Carbon::now()->format('Y-m');
        $tgl_ini = Carbon::now()->format('Y-m-01');
        if ($request->ajax()) {            
            $data = Masterregionm::selectRaw("*")
            ->orderBy('id','asc');
            return Datatables::eloquent($data)
                ->addIndexColumn()
                ->filter(function ($instance) use ($request) {
                    if (!empty($request->get('search'))) {
                        $instance->whereRaw("(master_region.nama_region like '%" . request('search') . "%')");
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

        return view('admin.masterregionm.index');

    }    

    public function store(Request $request)
    {
        $id = intval($request->id);
        $kd_region2 = $request->kd_region2;
        if($id==0){
            $row4 = DB::table('master_region')->selectRaw("max(kd_region) as kd_region3")
            ->first();
            if($row4){
                $kd_region3 = intval($row4->kd_region3);
            } else {
                $kd_region3 = 0;
            }
            $kd_region = str_pad(intval($kd_region3)+1,2,"0",STR_PAD_LEFT);  
        } else {
            $kd_region = $kd_region2;
        }

        try {
            Masterregionm::updateOrCreate([
                'id' => $id, 
            ],
            [
                'kd_region' => $kd_region,
                'nama_region' => $request->nama_region
            ]);    
            return response()->json(["status" => 'sukses', "pesan" => 'Sukses simpan data']);
        } catch (\Exception $e) {
            return response()->json(["status" => 'error', "pesan" => 'Gagal simpan data ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $masterregionm = Masterregionm::find($id);
        return response()->json($masterregionm);
    }

    public function destroy(Request $request)
    {
        $id = $request->id;
        DB::table('master_region')->where('id', $id)->delete();
      
        return response()->json(['success'=>'Sukses hapus data.']);
    }


}
