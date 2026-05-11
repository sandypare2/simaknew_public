<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\Datatables;
use App\models\Userm;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UsermController extends Controller
{
    private $userm;
    public function __construct(Userm $userm)
    {
        $this->userm = $userm;
    }

    public function index(Request $request)
    {        
        if ($request->ajax()) {
            $data = Userm::leftJoin('master_afiliasi','master_afiliasi.kd_afiliasi','=','useralvaro.kd_afiliasi')
            ->selectRaw("*")
            ->orderBy('id','desc');            
            return Datatables::eloquent($data)
                ->addIndexColumn()
                ->filter(function ($instance) use ($request) {
                    if (!empty($request->get('search'))) {
                        $instance->whereRaw("(usersimkp.username='" . request('search') . "' or usersimkp.role like '%" . request('search') . "%' or usersimkp.nama like '%" . request('search') . "%')");
                    }
                })
                ->addColumn('afiliasi2','')
                ->addColumn('aktif2','')
                ->addColumn('aksi', function ($data) {
                    return 
                    '<div class="acao text-center">'.
                    // '<a href="javascript:void(0)" data-id="'.$data->id.'" title="Edit Data" class="edit_row btn btn-xs btn-warning btn-icon" style="margin-right:3px;"><i class="fa fa-pencil"></i></a>'.
                    '<a href="javascript:void(0)" data-id="'.$data->id.'" title="Edit Data" class="edit_row"><button type="button" class="btn btn-icon btn-warning" style="margin-right:3px;"><span class="ti ti-pencil-star ti-sm"></span></button></a>'.
                    '<a href="javascript:void(0)" data-id="'.$data->id.'" title="Hapus Data" class="delete_row"><button type="button" class="btn btn-icon btn-danger" style="margin-right:3px;"><span class="ti ti-trash ti-sm"></span></button></a>'.
                    '<a href="javascript:void(0)" data-id="'.$data->id.'" title="Reset Password" class="reset_pass"><button type="button" class="btn btn-icon btn-info"><span class="ti ti-lock-pin ti-sm"></span></button></a>'.
                    '</div>';                    
                })
                ->rawColumns(['aksi'])
                ->make(true);
        }

        return view('admin.userm.index', [
            'data_afiliasi' => $this->afiliasim->getAllData2()
        ]);
    }    

    public function show(Userm $userm)
    {
        return view('admin.userm.show', ['userm' => $userm]);
    }

    public function store(Request $request)
    {
        if($request->email!=""){
            $email = $request->email;
        } else {
            $email = "-";
        }
        if($request->id == 0 || $request->id == '' || $request->id == null){
            Userm::create([
                'username' => $request->username,
                'password' => md5($request->password),
                'user_pass' => md5($request->password),
                'nama' => $request->nama,
                'email' => $email,
                'role' => $request->role,
                'jabatan' => $request->jabatan,
                'aktif' => $request->aktif
            ]);
        } else {
            $userm = Userm::whereRaw("id='$request->id'")
            ->update([
                'username' => $request->username,
                'nama' => $request->nama,
                'email' => $email,
                'role' => $request->role,
                'jabatan' => $request->jabatan,
                'aktif' => $request->aktif
            ]);    
        }
        return response()->json(['success'=>'Sukses simpan data.']);
    }

    public function edit($id)
    {
        $userm = Userm::find($id);
        return response()->json($userm);
    }

    public function destroy(Request $request)
    {
        DB::table('usersimkp')->where('id', $request->id)->delete();
      
        return response()->json(['success'=>'Sukses hapus data.']);
    }

    public function resetpass(Request $request)
    {
        $userm = Userm::where('id', $request->id)
        ->update([
            'password' => md5($request->newpass),
            'user_pass' => md5($request->newpass)
        ]);
        if($userm){
            return response()->json(['success'=>'Sukses reset password.']);
        } else {
            return redirect(url('/dashboard'))->with('error', 'Gagal Reset Password ' . $e->getMessage());
        }
    }

    public function gantipass(Request $request)
    {
        $userm = Userm::where('id', $request->id)
        ->update([
            'password' => md5($request->password),
            'user_pass' => md5($request->newpass)
        ]);
        if($userm){
            return response()->json(['success'=>'Sukses ganti password.']);
        } else {
            return redirect(url('/dashboard'))->with('error', 'Gagal Ganti Password ' . $e->getMessage());
        }
    }

    // public function fetchJenis(Request $request)
    // {
    //     $kd_kategoricari = $request->kd_kategoricari;
    //     if($kd_kategoricari!="semua"){
    //         $data['filter_jenis'] = Jenism::whereRaw("kd_kategori='".$request->kd_kategoricari."'")->orderBy('id')->get(["kd_jenis", "nama_jenis"]);
    //     } else {
    //         $data['filter_jenis'] = Jenism::orderBy('id')->get(["kd_jenis", "nama_jenis"]);
    //     }
    //     return response()->json($data);
    // }
    
    // public function fetchJenis2(Request $request)
    // {
    //     $kd_kategori = $request->kd_kategori;
    //     $data['filter_jenis'] = Jenism::whereRaw("kd_kategori='".$request->kd_kategori."'")->orderBy('id')->get(["kd_jenis", "nama_jenis"]);
    //     return response()->json($data);
    // }

}
