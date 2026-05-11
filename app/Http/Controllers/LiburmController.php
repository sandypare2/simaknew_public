<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\Datatables;
use App\models\Liburm;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class LiburmController extends Controller
{
    private $liburm;
    public function __construct(Liburm $liburm)
    {
        $this->liburm = $liburm;
    }

    public function index(Request $request)
    {        
        if ($request->ajax()) {
            $data = Liburm::selectRaw("*")
            ->orderBy('id','desc');            
            return Datatables::eloquent($data)
                ->addIndexColumn()
                ->filter(function ($instance) use ($request) {
                    if (!empty($request->get('tahuncari')) && intval($request->get('tahuncari')>0)) {
                        $instance->whereRaw("substr(tanggal,1,4)='" . request('tahuncari') . "'");
                    }
                    if (!empty($request->get('search'))) {
                        $instance->whereRaw("keterangan like '%" . request('search') . "%'");
                    }
                })
                ->addColumn('tanggalnya','')
                ->addColumn('aksi', function ($data) {
                    return 
                    '<div class="acao text-center">'.
                    // '<a href="javascript:void(0)" data-id="'.$data->id.'" title="Edit Data" class="edit_row btn btn-xs btn-warning btn-icon" style="margin-right:3px;"><i class="fa fa-pencil"></i></a>'.
                    '<a href="javascript:void(0)" data-id="'.$data->id.'" title="Edit Data" class="edit_row"><button type="button" class="btn btn-icon btn-primary" style="margin-right:3px;"><span class="ti ti-pencil-star ti-sm"></span></button></a>'.
                    '<a href="javascript:void(0)" data-id="'.$data->id.'" title="Hapus Data" class="delete_row"><button type="button" class="btn btn-icon btn-danger"><span class="ti ti-trash ti-sm"></span></button></a>'.
                    '</div>';                    
                })
                ->rawColumns(['aksi'])
                ->make(true);
        }

        return view('admin.liburm.index');
    }    

    public function show(Liburm $liburm)
    {
        return view('admin.liburm.show', ['liburm' => $liburm]);
    }

    public function store(Request $request)
    {
        if($request->tanggal!=""){
            $tglnya2 = explode('/',$request->tanggal);
            $hari = $tglnya2[0];
            $bulan = $tglnya2[1];
            $tahun = $tglnya2[2];
            $tanggal = $tahun."-".$bulan."-".$hari;
        } else {
            $tanggal = $hari_ini;
        }
        Liburm::updateOrCreate([
            'id' => $request->id, 
        ],
        [
            'tanggal' => $tanggal, 
            'keterangan' => $request->keterangan
        ]);    
        return response()->json(['success'=>'Sukses simpan data.']);
    }

    public function edit($id)
    {
        $liburm = Liburm::find($id);
        return response()->json($liburm);
    }

    public function destroy(Request $request)
    {
        DB::table('libur_nasional')->where('id', $request->id)->delete();
      
        return response()->json(['success'=>'Sukses hapus data.']);
    }

}
