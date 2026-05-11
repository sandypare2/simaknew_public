<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\Datatables;
use App\models\Datamahasiswam;
use App\models\Prodim;
use App\models\Tahunmasukm;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DatamahasiswamController extends Controller
{
    private $datamahasiswam;
    private $prodim;
    private $tahunmasukm;
    public function __construct(Datamahasiswam $datamahasiswam, Prodim $prodim, Tahunmasukm $tahunmasukm)
    {
        $this->datamahasiswam = $datamahasiswam;
        $this->prodim = $prodim;
        $this->tahunmasukm = $tahunmasukm;
    }

    public function index(Request $request)
    {        
        $tahun_ini = Carbon::now()->format('Y');
        $bulan_ini = Carbon::now()->format('Y-m');
        $tgl_ini = Carbon::now()->format('Y-m-01');
        if ($request->ajax()) {            
            $data = Datamahasiswam::selectRaw("
                data_mahasiswa.*,
                b.nama_prodi as nama_prodi
            ")
            ->leftJoin('master_prodi as b','b.kd_prodi','=','data_mahasiswa.kd_prodi')
            ->orderBy('id','desc');
            return Datatables::eloquent($data)
                ->addIndexColumn()
                ->filter(function ($instance) use ($request) {
                    if (!empty($request->get('kd_prodicari')) && !empty($request->get('kd_prodicari')!="semua")) {
                        $instance->whereRaw("data_mahasiswa.kd_prodi='" . request('kd_prodicari') . "'");
                    }
                    if (!empty($request->get('tahuncari')) && !empty($request->get('tahuncari')!="semua")) {
                        $instance->whereRaw("data_mahasiswa.tahun_masuk='" . request('tahuncari') . "'");
                    }
                    if (!empty($request->get('search'))) {
                        $instance->whereRaw("(data_mahasiswa.nip='" . request('search') . "' or data_mahasiswa.nama like '%" . request('search') . "%')");
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

        return view('admin.datamahasiswam.index', [
            'prodim' => $this->prodim->getAllData(),
            'tahunmasukm' => $this->tahunmasukm->getAllData()
        ]);

    }    

    public function store(Request $request)
    {
        $id = intval($request->id);
        if($request->tgl_lahir!=""){
            $tglnya = explode('/',$request->tgl_lahir);
            $hari = $tglnya[0];
            $bulan = $tglnya[1];
            $tahun = $tglnya[2];
            $tgl_lahir = $tahun."-".$bulan."-".$hari;
        } else {
            $tgl_lahir = "";
        }
        try {
            Datamahasiswam::updateOrCreate([
                'id' => $id, 
            ],
            [
                'kd_prodi' => $request->kd_prodi, 
                'nim' => $request->nim,
                'nama' => $request->nama, 
                'tempat_lahir' => $request->tempat_lahir,
                'tgl_lahir' => $tgl_lahir,
                'jenis_kelamin' => $request->jenis_kelamin,
                'tahun_masuk' => $request->tahun_masuk
            ]);    
            return response()->json(["status" => 'sukses', "pesan" => 'Sukses simpan data']);
        } catch (\Exception $e) {
            return response()->json(["status" => 'error', "pesan" => 'Gagal simpan data ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $datamahasiswam = Datamahasiswam::find($id);
        return response()->json($datamahasiswam);
    }

    public function destroy(Request $request)
    {
        $id = $request->id;
        DB::table('data_mahasiswa')->where('id', $id)->delete();
      
        return response()->json(['success'=>'Sukses hapus data.']);
    }


}
