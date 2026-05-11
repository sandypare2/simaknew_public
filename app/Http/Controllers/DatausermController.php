<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\Datatables;
use App\models\Datauserm;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DatausermController extends Controller
{
    private $datauserm;
    public function __construct(Datauserm $datauserm)
    {
        $this->datauserm = $datauserm;
    }

    public function index(Request $request)
    {        
        $tahun_ini = Carbon::now()->format('Y');
        $bulan_ini = Carbon::now()->format('Y-m');
        $tgl_ini = Carbon::now()->format('Y-m-01');
        if ($request->ajax()) {   
            if(Auth::user()->role=="superadmin"){
                $data = Datauserm::selectRaw("*")
                ->orderBy('id','desc');
            } else {
                $data = Datauserm::selectRaw("*")
                ->whereRaw("role<>'superadmin'")
                ->orderBy('id','desc');
            }
            return Datatables::eloquent($data)
                ->addIndexColumn()
                ->addColumn('aksi', function ($data) {
                    $a = '<div class="acao text-center">';
                    $a .= '<a href="javascript:void(0)" data-id="'.$data->id.'" title="Edit Data" class="edit_row"><button type="button" class="btn btn-light-warning icon-btn-sm" style="margin-right:3px;"><i class="ri-pencil-line fs-14"></i></button></a>';
                    $a .= '<a href="javascript:void(0)" data-id="'.$data->id.'" title="Hapus Data" class="delete_row"><button type="button" class="btn btn-light-danger icon-btn-sm"><i class="ri-delete-bin-line fs-14"></i></button></a>';
                    $a .= '</div>';
                    return $a;
                })
                ->rawColumns(['aksi'])
                ->make(true);
        }

        return view('admin.datauserm.index');

    }    

    public function show(Datauserm $datauserm)
    {
        return view('admin.datauserm.show', ['datauserm' => $datauserm]);
    }

    public function store(Request $request)
    {
        $id = intval($request->id);
        try {
            Datauserm::updateOrCreate([
                'id' => $id, 
            ],
            [
                'username' => $request->username, 
                'password' => md5($request->password), 
                'user_pass' => md5($request->password), 
                'nama' => $request->nama,
                'email' => $request->email,
                'role' => $request->role,
                'jabatan' => $request->jabatan,
                'aktif' => $request->aktif
            ]);    
            return response()->json(["status" => 'sukses', "pesan" => 'Sukses simpan data']);
        } catch (\Exception $e) {
            return response()->json(["status" => 'error', "pesan" => 'Gagal simpan data ' . $e->getMessage()]);
        }
    }

    public function updateUser(Request $request)
    {
        $id2 = intval($request->id2);
        try {
            Datauserm::updateOrCreate([
                'id' => $id2, 
            ],
            [
                'username' => $request->username2, 
                'nama' => $request->nama2,
                'email' => $request->email2,
                'role' => $request->role2,
                'jabatan' => $request->jabatan2,
                'aktif' => $request->aktif2
            ]);    
            return response()->json(["status" => 'sukses', "pesan" => 'Sukses simpan data']);
        } catch (\Exception $e) {
            return response()->json(["status" => 'error', "pesan" => 'Gagal simpan data ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $datauserm = Datauserm::find($id);
        return response()->json($datauserm);
    }

    public function destroy(Request $request)
    {
        $id = $request->id;
        DB::table('usersimkp')->where('id', $id)->delete();
      
        return response()->json(['success'=>'Sukses hapus data.']);
    }


}
