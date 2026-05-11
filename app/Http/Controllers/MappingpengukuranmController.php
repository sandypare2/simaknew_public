<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\Datatables;
use App\models\Mappingpengukuranm;
use App\models\Masterpengukuranm;
use App\models\Masterkinerjam;
use App\models\Masterindividum;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class MappingpengukuranmController extends Controller
{
    private $mappingpengukuranm;
    private $masterpengukuranm;
    private $masterkinerjam;
    private $masterindividum;
    public function __construct(Mappingpengukuranm $mappingpengukuranm, Masterpengukuranm $masterpengukuranm, Masterkinerjam $masterkinerjam, Masterindividum $masterindividum)
    {
        $this->mappingpengukuranm = $mappingpengukuranm;
        $this->masterpengukuranm = $masterpengukuranm;
        $this->masterkinerjam = $masterkinerjam;
        $this->masterindividum = $masterindividum;
    }

    public function index(Request $request)
    {        
        $tahun_ini = Carbon::now()->format('Y');
        $bulan_ini = Carbon::now()->format('Y-m');
        $tgl_ini = Carbon::now()->format('Y-m-01');
        if ($request->ajax()) {            
            $data = Mappingpengukuranm::selectRaw("
                matriks_pengukuran.*,
                b.nama_pengukuran
            ")
            ->leftJoin('master_pengukuran as b','b.kode_pengukuran','=','matriks_pengukuran.kode_pengukuran')
            ->orderBy('matriks_pengukuran.id','desc');
            return Datatables::eloquent($data)
                ->addIndexColumn()
                ->filter(function ($instance) use ($request) {
                    if (!empty($request->get('search'))) {
                        $instance->whereRaw("(matriks_pengukuran.kode_pengukuran like '%" . request('search') . "%' or master_pengukuran.nama_pengukuran like '%" . request('search') . "%')");
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

        return view('admin.mappingpengukuranm.index',[
            'masterpengukuranm' => $this->masterpengukuranm->getAllData(),
            'masterkinerjam' => $this->masterkinerjam->getAllData(),
            'masterindividum' => $this->masterindividum->getAllData(),
        ]);

    }    

    public function store(Request $request)
    {
        $id = intval($request->id);
        $nilai_huruf_kinerja = $request->nilai_huruf_kinerja;
        $nilai_huruf_individu = $request->nilai_huruf_individu;
        $kode_pengukuran = $request->kode_pengukuran;

        $row1 = DB::table('master_nilai_kinerja')
        ->selectRaw("*")
        ->whereRaw("kode_kinerja='$nilai_huruf_kinerja'")
        ->first();
        $nilai_awal_kinerja = $row1->nilai_awal;
        $nilai_akhir_kinerja = $row1->nilai_akhir;

        $row2 = DB::table('master_nilai_individu')
        ->selectRaw("*")
        ->whereRaw("kode_individu='$nilai_huruf_individu'")
        ->first();
        $nilai_awal_individu = $row2->nilai_awal;
        $nilai_akhir_individu = $row2->nilai_akhir;
        try {
            Mappingpengukuranm::updateOrCreate([
                'id' => $id, 
            ],
            [
                'nilai_awal_kinerja' => $nilai_awal_kinerja,
                'nilai_akhir_kinerja' => $nilai_akhir_kinerja,
                'nilai_huruf_kinerja' => $nilai_huruf_kinerja,
                'nilai_awal_individu' => $nilai_awal_individu,
                'nilai_akhir_individu' => $nilai_akhir_individu,
                'nilai_huruf_individu' => $nilai_huruf_individu,
                'kode_pengukuran' => $kode_pengukuran
            ]);    
            return response()->json(["status" => 'sukses', "pesan" => 'Sukses simpan data']);
        } catch (\Exception $e) {
            return response()->json(["status" => 'error', "pesan" => 'Gagal simpan data ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $mappingpengukuranm = Mappingpengukuranm::find($id);
        return response()->json($mappingpengukuranm);
    }

    public function destroy(Request $request)
    {
        $id = $request->id;
        DB::table('matriks_pengukuran')->where('id', $id)->delete();
      
        return response()->json(['success'=>'Sukses hapus data.']);
    }


}
