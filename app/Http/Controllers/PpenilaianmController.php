<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\Datatables;
use App\models\Ppenilaianm;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class PpenilaianmController extends Controller
{
    private $ppenilaianm;
    public function __construct(Ppenilaianm $ppenilaianm)
    {
        $this->ppenilaianm = $ppenilaianm;
    }

    public function index(Request $request)
    {        
        $tahun_ini = Carbon::now()->format('Y');
        $bulan_ini = Carbon::now()->format('Y-m');
        $tgl_ini = Carbon::now()->format('Y-m-01');
        if ($request->ajax()) {            
            $data = Ppenilaianm::selectRaw("*")
            ->orderBy('id','desc');
            return Datatables::eloquent($data)
                ->addIndexColumn()
                // ->filter(function ($instance) use ($request) {
                //     if (!empty($request->get('search'))) {
                //         $instance->whereRaw("(master_divisi.nama_divisi like '%" . request('search') . "%')");
                //     }
                // })
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

        return view('admin.ppenilaianm.index');

    }    

    public function store(Request $request)
    {
        $id = intval($request->id);
        $tgl_awal2 = $request->tgl_awal;
        $tgl_akhir2 = $request->tgl_akhir;
        if($tgl_awal2!=""){
            $tglnya = explode('/',$tgl_awal2);
            $hari = $tglnya[0];
            $bulan = $tglnya[1];
            $tahun = $tglnya[2];
            $tgl_awal = $tahun."-".$bulan."-".$hari;
        } else {
            $tgl_awal = "";
        }
        if($tgl_akhir2!=""){
            $tglnya = explode('/',$tgl_akhir2);
            $hari = $tglnya[0];
            $bulan = $tglnya[1];
            $tahun = $tglnya[2];
            $tgl_akhir = $tahun."-".$bulan."-".$hari;
        } else {
            $tgl_akhir = "";
        }
        try {
            Ppenilaianm::updateOrCreate([
                'id' => $id, 
            ],
            [
                'blth_awal' => $request->blth_awal,
                'blth_akhir' => $request->blth_akhir,
                'tgl_awal' => $tgl_awal,
                'tgl_akhir' => $tgl_akhir,
                'status' => $request->status
            ]);    
            return response()->json(["status" => 'sukses', "pesan" => 'Sukses simpan data']);
        } catch (\Exception $e) {
            return response()->json(["status" => 'error', "pesan" => 'Gagal simpan data ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $ppenilaianm = Ppenilaianm::find($id);
        return response()->json($ppenilaianm);
    }

    public function destroy(Request $request)
    {
        $id = $request->id;
        DB::table('periode_penilaian')->where('id', $id)->delete();
      
        return response()->json(['success'=>'Sukses hapus data.']);
    }


}
