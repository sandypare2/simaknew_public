<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\Datatables;
use App\models\Vdetailsppd;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {    
        $tahun_ini = Carbon::now()->format('Y');
        $hari_ini = Carbon::now()->format('Y-m-d');
        $tahun_ini = Carbon::now()->format('Y');
        $bulan_ini = intval(Carbon::now()->format('m'));

        // $rows3 = DB::table('data_pegawai')
        // ->selectRaw("
        //     data_pegawai.kd_area,
        //     count(data_pegawai.nip) as jumlah_pegawai,
        //     b.kode_area,
        //     b.nama_area
        // ")
        // ->leftJoin('master_area as b','b.kd_area','=','data_pegawai.kd_area')
        // ->whereRaw("aktif='1' and payroll='1' and aktif_simkp='1'")
        // ->groupBy('data_pegawai.kd_area')
        // ->get();

        $row1 = DB::table('data_pegawai')
        ->selectRaw("
            count(nip) as jumlah_pegawai,
            sum(if(kd_area='12',1,0)) as jumlah_pusat,
            sum(if(kd_area<>'12',1,0)) as jumlah_cabang,
            sum(if(jenis_kpi='pusat' and level_kpi<='2',1,0)) as jumlah_direksi,
            sum(if((jenis_kpi='pusat' and level_kpi>='3') or jenis_kpi<>'pusat',1,0)) as jumlah_non_direksi
        ")
        ->whereRaw("aktif='1' and payroll='1' and aktif_simkp='1'")
        ->first();
        if($row1){
            $jumlah_pegawai = $row1->jumlah_pegawai;
            $jumlah_pusat = $row1->jumlah_pusat;
            $jumlah_cabang = $row1->jumlah_cabang;
            $jumlah_direksi = $row1->jumlah_direksi;
            $jumlah_non_direksi = $row1->jumlah_non_direksi;
        } else {
            $jumlah_pegawai = 0;
            $jumlah_pusat = 0;
            $jumlah_cabang = 0;
            $jumlah_direksi = 0;
            $jumlah_non_direksi = 0;
        }
        if($jumlah_pusat>0){
            $persen_pusat = round(($jumlah_pusat/$jumlah_pegawai)*100,2);
        } else {
            $persen_pusat = 0;
        }
        if($jumlah_cabang>0){
            $persen_cabang = round(($jumlah_cabang/$jumlah_pegawai)*100,2);
        } else {
            $persen_cabang = 0;
        }

        $row2 = DB::table('penilaian_pegawai')
        ->selectRaw("tahun,sum(skor_kinerja_semester1) as jumlah_sem1,sum(skor_kinerja_semester2) as jumlah_sem2")
        ->groupBy('tahun')
        ->orderBy('tahun','desc')
        ->first();
        if($row2){
            $last_tahun = $row2->tahun;
            $jumlah_sem1 = $row2->jumlah_sem1;
            $jumlah_sem2 = $row2->jumlah_sem2;
        } else {
            $last_tahun = 0;
            $jumlah_sem1 = 0;
            $jumlah_sem2 = 0;
        }
        // if($jumlah_sem2>0 && ){}

        $labels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nop', 'Des'];
        $rand = fn() => rand(0, 100);

        $datachart = [
            'chart1' => [
                'title' => '',
                'color' => '#3b82f6', // text-primary
                'series' => [
                    ['name' => 'Performance', 'data' => array_map($rand, range(1, count($labels)))],
                ],
                'labels' => $labels,
            ],
            'chart2' => [
                'title' => '',
                'color' => '#22c55e', // text-success
                'series' => [
                    ['name' => 'Performance', 'data' => array_map($rand, range(1, count($labels)))],
                ],
                'labels' => $labels,
            ],
            'chart3' => [
                'title' => '',
                'color' => '#f59e0b', // text-warning
                'series' => [
                    ['name' => 'Performance', 'data' => array_map($rand, range(1, count($labels)))],
                ],
                'labels' => $labels,
            ],
            'chart4' => [
                'title' => '',
                'color' => '#06b6d4', // text-info
                'series' => [
                    ['name' => 'Performance', 'data' => array_map($rand, range(1, count($labels)))],
                ],
                'labels' => $labels,
            ],
            'chart5' => [
                'title' => '',
                'color' => '#ef4444', // text-danger
                'series' => [
                    ['name' => 'Performance', 'data' => array_map($rand, range(1, count($labels)))],
                ],
                'labels' => $labels,
            ],
        ];

        $row4 = DB::table('riwayat_talenta')
        ->selectRaw("tahun,semester")
        ->groupBy('tahun','semester')
        ->orderBy('tahun','desc')
        ->orderBy('semester','desc')
        ->first();
        if($row4){
            $tahun_talenta = $row4->tahun;
            $semester_talenta = $row4->semester;
        } else {
            $tahun_talenta = "";
            $semester_talenta = "";
        }

        $rows5 = DB::table(DB::raw('
            (
                SELECT 
                    MIN(id) AS urut_id,
                    nama_pengukuran
                FROM master_pengukuran
                GROUP BY nama_pengukuran
            ) a
        '))
        ->select(
            'a.nama_pengukuran as nama_talenta',
            DB::raw('COUNT(b.id) as jumlah_talenta')
        )
        ->leftJoin('riwayat_talenta as b', function ($join) use ($tahun_talenta, $semester_talenta) {
            $join->on('b.nama_talenta', '=', 'a.nama_pengukuran')
                ->where('b.tahun', $tahun_talenta)
                ->where('b.semester', $semester_talenta);
        })
        ->groupBy('a.nama_pengukuran', 'a.urut_id')
        ->orderBy('a.urut_id', 'asc')
        ->get();
        // dd($rows5);

        $lastPeriods = DB::table('riwayat_talenta')
        ->select('tahun', 'semester')
        ->groupBy('tahun', 'semester')
        ->orderBy('tahun', 'desc')
        ->orderBy('semester', 'desc')
        ->limit(10)
        ->get();
        $periodMap = [];
        foreach ($lastPeriods as $p) {
            $periodMap[$p->tahun][] = $p->semester;
        }

        $chartRaw = DB::table('riwayat_talenta')
            ->select(
                'tahun',
                'semester',
                'nama_talenta',
                DB::raw('COUNT(nip) as total')
            )
            ->where(function ($q) use ($periodMap) {
                foreach ($periodMap as $tahun => $semesters) {
                    $q->orWhere(function ($qq) use ($tahun, $semesters) {
                        $qq->where('tahun', $tahun)
                        ->whereIn('semester', $semesters);
                    });
                }
            })
            ->groupBy('tahun', 'semester', 'nama_talenta')
            ->orderBy('tahun')
            ->orderBy('semester')
            ->get();

        $categories = [];
        $seriesTemp = [];

        foreach ($chartRaw as $row) {
            $label = $row->tahun . ' - S' . $row->semester;

            if (!in_array($label, $categories)) {
                $categories[] = $label;
            }

            $seriesTemp[$row->nama_talenta][$label] = (int) $row->total;
        }
        $series = [];
        foreach ($seriesTemp as $nama => $data) {
            $series[] = [
                'name' => $nama,
                'data' => array_map(
                    fn ($cat) => $data[$cat] ?? 0,
                    $categories
                )
            ];
        }

        return view('admin.dashboard.index',compact('series','categories','datachart','jumlah_pegawai','jumlah_pusat','jumlah_cabang','persen_pusat','persen_cabang','jumlah_direksi','jumlah_non_direksi','rows5','tahun_talenta','semester_talenta'));
    } 

}
