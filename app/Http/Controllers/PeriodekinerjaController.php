<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\Datatables;
use App\models\Periodekinerja;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class periodekinerjaController extends Controller
{
    private $periodekinerja;
    public function __construct(Periodekinerja $periodekinerja)
    {
        $this->periodekinerja = $periodekinerja;
    }

    public function index(Request $request)
    {        
        $tahunAwal = 2024;
        $tahunSekarang = Carbon::now()->year;
        $listTahun = collect(range($tahunAwal, $tahunSekarang))
        ->sortDesc()
        ->values();
        // dd($listTahun);
        if ($request->ajax()) {            
            $tahuncari = $request->get('tahuncari', now()->year);
            $bulanList = collect(range(1, 12))->map(function ($bulan) use ($tahuncari) {
                return Carbon::createFromDate($tahuncari, $bulan, 1)->format('Y-m');
            });
            $bulanSub = $bulanList->map(fn ($blth) =>
                "SELECT '{$blth}' AS blth"
            )->implode(' UNION ALL ');
            $query = DB::table(DB::raw("($bulanSub) AS b"))
            ->leftJoin('periode_kinerja as pk', 'pk.blth', '=', 'b.blth')
            ->select([
                'b.blth',
                'pk.id',
                DB::raw("
                    CASE
                        WHEN pk.id IS NULL THEN 0
                        ELSE pk.status
                    END AS status
                ")
            ])
            ->orderBy('b.blth');

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('blth', function ($row) {
                    return Carbon::createFromFormat('Y-m-d', $row->blth . '-01')
                        ->locale('id')
                        ->translatedFormat('F Y');
                })
                ->addColumn('status2', function ($row) {
                    return $row->status == 1
                    ? '<span class="badge bg-success">Open</span>'
                    : '<span class="badge bg-danger">Close</span>';
                })
                ->addColumn('aksi', function ($data) {
                    $a = '<div class="text-left">';
                    $a .= '<a href="javascript:void(0)" data-id="'.$data->id.'" data-blth="'.$data->blth.'" data-status="'.$data->status.'" title="Edit Data" class="edit_row"><button type="button" class="btn btn-light-warning icon-btn-sm" style="margin-right:0px;"><i class="ri-pencil-fill fs-14"></i></button></a>';
                    // $a .= '<a href="javascript:void(0)" data-id="'.$data->id.'" title="Hapus Data" class="delete_row"><button type="button" class="btn btn-light-danger icon-btn-sm"><i class="ri-delete-bin-line fs-14"></i></button></a>';
                    $a .= '</div>';
                    return $a;
                })
                ->rawColumns(['aksi','status2'])
                ->make(true);
        }

        return view('admin.periodekinerja.index',compact('listTahun'));
    }    

    public function store(Request $request)
    {
        try {
            Periodekinerja::updateOrCreate([
                'id' => $request->id, 
            ],
            [
                'blth' => $request->blth,
                'status' => $request->status
            ]);    
            return response()->json(["status" => 'sukses', "pesan" => 'Sukses simpan data']);
        } catch (\Exception $e) {
            return response()->json(["status" => 'error', "pesan" => 'Gagal simpan data ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $periodekinerja = Periodekinerja::find($id);
        return response()->json($periodekinerja);
    }

    public function destroy(Request $request)
    {
        DB::table('periode_kinerja')->where('id', $request->id)->delete();
      
        return response()->json(['success'=>'Sukses hapus data.']);
    }

}
