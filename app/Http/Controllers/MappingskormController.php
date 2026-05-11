<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\Datatables;
use App\models\Mappingskorm;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class MappingskormController extends Controller
{
    private $mappingskorm;
    public function __construct(Mappingskorm $mappingskorm)
    {
        $this->mappingskorm = $mappingskorm;
    }

    public function index(Request $request)
    {        
        $tahun_ini = Carbon::now()->format('Y');
        $bulan_ini = Carbon::now()->format('Y-m');
        $tgl_ini = Carbon::now()->format('Y-m-01');
        if ($request->ajax()) {            
            $data = Mappingskorm::selectRaw("*")
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
                    $a .= '<a href="javascript:void(0)" data-id="'.$data->id.'" title="Edit Data" class="edit_row"><button type="button" class="btn btn-light-warning icon-btn-sm" style="margin-right:3px;"><i class="ri-pencil-fill font-size-14"></i></button></a>';
                    $a .= '<a href="javascript:void(0)" data-id="'.$data->id.'" title="Hapus Data" class="delete_row"><button type="button" class="btn btn-light-danger icon-btn-sm"><i class="ri-delete-bin-line font-size-14"></i></button></a>';
                    $a .= '</div>';
                    return $a;
                })
                ->rawColumns(['aksi'])
                ->make(true);
        }

        return view('admin.mappingskorm.index');

    }    

    public function store(Request $request)
    {
        $id = intval($request->id);
        try {
            Mappingskorm::updateOrCreate([
                'id' => $id, 
            ],
            [
                'kriteria' => $request->kriteria,
                'polarisasi' => $request->polarisasi,
                'pencapaian_awal' => $request->pencapaian_awal,
                'pencapaian_akhir' => $request->pencapaian_akhir,
                'skor_awal' => $request->skor_awal,
                'skor_akhir' => $request->skor_akhir
            ]);    
            return response()->json(["status" => 'sukses', "pesan" => 'Sukses simpan data']);
        } catch (\Exception $e) {
            return response()->json(["status" => 'error', "pesan" => 'Gagal simpan data ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $mappingskorm = Mappingskorm::find($id);
        return response()->json($mappingskorm);
    }

    public function destroy(Request $request)
    {
        $id = $request->id;
        DB::table('matriks_skor')->where('id', $id)->delete();
      
        return response()->json(['success'=>'Sukses hapus data.']);
    }


}
