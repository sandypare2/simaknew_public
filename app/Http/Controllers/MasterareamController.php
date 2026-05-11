<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\Datatables;
use App\models\Masterregionm;
use App\models\Masteraream;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class MasterareamController extends Controller
{
    private $masterregionm;
    private $masteraream;
    public function __construct(Masterregionm $masterregionm, Masteraream $masteraream)
    {
        $this->masterregionm = $masterregionm;
        $this->masteraream = $masteraream;
    }

    public function index(Request $request)
    {        
        $tahun_ini = Carbon::now()->format('Y');
        $bulan_ini = Carbon::now()->format('Y-m');
        $tgl_ini = Carbon::now()->format('Y-m-01');
        if ($request->ajax()) {            
            $data = Masteraream::selectRaw("
                master_area.*,
                b.nama_region as nama_region
            ")
            ->leftJoin('master_region as b','b.kd_region','=','master_area.kd_region')
            ->orderBy('master_area.kd_region','asc')
            ->orderBy('master_area.id','asc');
            return Datatables::eloquent($data)
                ->addIndexColumn()
                ->filter(function ($instance) use ($request) {
                    if (!empty($request->get('search'))) {
                        $instance->whereRaw("(b.nama_region like '%" . request('search') . "%' or master_area.nama_area like '%" . request('search') . "%')");
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

        return view('admin.masteraream.index',[
            'masterregionm' => $this->masterregionm->getAllData()
        ]);

    }    

    public function store(Request $request)
    {
        $id = intval($request->id);
        $kd_area2 = $request->kd_area2;
        if($id==0){
            $row4 = DB::table('master_area')->selectRaw("max(kd_area) as kd_area3")
            ->first();
            if($row4){
                $kd_area3 = intval($row4->kd_area3);
            } else {
                $kd_area3 = 0;
            }
            $kd_area = str_pad(intval($kd_area3)+1,2,"0",STR_PAD_LEFT);  
        } else {
            $kd_area = $kd_area2;
        }

        try {
            Masteraream::updateOrCreate([
                'id' => $id, 
            ],
            [
                'kd_region' => $request->kd_region,
                'kd_area' => $kd_area,
                'kode_area' => strtoupper($request->kode_area),
                'nama_area' => $request->nama_area,
                'jenis_kpi' => $request->jenis_kpi
            ]);    
            return response()->json(["status" => 'sukses', "pesan" => 'Sukses simpan data']);
        } catch (\Exception $e) {
            return response()->json(["status" => 'error', "pesan" => 'Gagal simpan data ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $masteraream = Masteraream::find($id);
        return response()->json($masteraream);
    }

    public function destroy(Request $request)
    {
        $id = $request->id;
        DB::table('master_area')->where('id', $id)->delete();
      
        return response()->json(['success'=>'Sukses hapus data.']);
    }


}
