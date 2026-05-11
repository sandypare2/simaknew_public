<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\Datatables;
use App\models\Divisim;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DivisimController extends Controller
{
    private $divisim;
    public function __construct(Divisim $divisim)
    {
        $this->divisim = $divisim;
    }

    public function index(Request $request)
    {        
        $tahun_ini = Carbon::now()->format('Y');
        $bulan_ini = Carbon::now()->format('Y-m');
        $tgl_ini = Carbon::now()->format('Y-m-01');
        if ($request->ajax()) {            
            $data = Divisim::selectRaw("*")
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

        return view('admin.divisim.index');

    }    

    public function store(Request $request)
    {
        $id = intval($request->id);
        $kd_divisi2 = $request->kd_divisi2;
        if($id==0){
            $row4 = DB::table('master_divisi')->selectRaw("max(kd_divisi) as kd_divisi3")
            ->first();
            if($row4){
                $kd_divisi3 = intval($row4->kd_divisi3);
            } else {
                $kd_divisi3 = 0;
            }
            $kd_divisi = str_pad(intval($kd_divisi3)+1,2,"0",STR_PAD_LEFT);  
        } else {
            $kd_divisi = $kd_divisi2;
        }

        try {
            Divisim::updateOrCreate([
                'id' => $id, 
            ],
            [
                'kd_divisi' => $kd_divisi,
                'nama_divisi' => $request->nama_divisi
            ]);    
            return response()->json(["status" => 'sukses', "pesan" => 'Sukses simpan data']);
        } catch (\Exception $e) {
            return response()->json(["status" => 'error', "pesan" => 'Gagal simpan data ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $divisim = Divisim::find($id);
        return response()->json($divisim);
    }

    public function destroy(Request $request)
    {
        $id = $request->id;
        DB::table('master_divisi')->where('id', $id)->delete();
      
        return response()->json(['success'=>'Sukses hapus data.']);
    }


}
