<?php

namespace App\Exports;

use App\Models\Datapegawaim;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
// use Maatwebsite\Excel\Sheet;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Database\Eloquent\Builder;

class KenaikangrademExport implements FromCollection, WithHeadings, WithColumnWidths, WithEvents
{
    use Exportable;
    protected $minTahunSemester;
    protected $minYear;
    protected $minSemester;
    protected $years;
    public function  __construct()
    {
        $row3 = DB::table('riwayat_grade')
        ->selectRaw('MIN(tgl_kenaikan) as tgl_kenaikan_awal')
            ->whereRaw("tgl_kenaikan<>''")
            ->first();
        $minYear = substr($row3->tgl_kenaikan_awal,0,4);
        $this->years = range($minYear, Carbon::now()->year);
    }

    public function registerEvents(): array
    {
        return [
            // AfterSheet::class    => function(AfterSheet $event) {
            //     $event->sheet->insertNewRowBefore(1);
            //     $event->sheet->setCellValue('A1','MONITORING KENAIKAN GRADE');
            // },
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;
                // $highestColumn = $sheet->getHighestColumn();

                // Title
                $sheet->insertNewRowBefore(1);
                $highestColumn = $sheet->getHighestColumn();
                // $sheet->mergeCells("A1:{$highestColumn}1");
                // $sheet->setCellValue('A1', 'MONITORING KENAIKAN GRADE');
                // $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                // $sheet->getStyle('A1')->getAlignment()->setHorizontal('center')->setVertical('center');

                // === 2️⃣ Merge the year header cells (each year spans 2 columns) ===
                $baseCount = 8; // number of fixed columns before year columns
                $colIndex = $baseCount + 1;

                foreach ($this->years as $year) {
                    $startCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
                    $endCol   = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1);

                    // Merge year cell (row 2)
                    $sheet->mergeCells("{$startCol}2:{$endCol}2");

                    // Center text
                    $sheet->getStyle("{$startCol}2:{$endCol}3")->getAlignment()
                        ->setHorizontal('center')
                        ->setVertical('center');

                    $colIndex += 2;
                }

                $sheet->mergeCells("A1:{$endCol}1");
                $sheet->setCellValue('A1', 'MONITORING TALENTA PEGAWAI');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal('center')->setVertical('center');

                // === 3️⃣ Style the entire header (row 2–3) ===
                // $headerRange = 'A2:' . $highestColumn . '3';
                $headerRange = 'A2:' . $endCol . '3';
                $sheet->getStyle($headerRange)->getFont()->setBold(false);
                $sheet->getStyle($headerRange)->getAlignment()->setHorizontal('center')->setVertical('center');
                $sheet->getStyle($headerRange)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('D9D9D9'); // light gray background
                // $sheet->getStyle($headerRange)->applyFromArray([
                //     'borders' => [
                //         'allBorders' => [
                //             'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                //             'color' => ['argb' => '000000'],
                //         ],
                //     ],
                // ]);

                // Auto height for headers
                $sheet->getDelegate()->getRowDimension(2)->setRowHeight(25);
                $sheet->getDelegate()->getRowDimension(3)->setRowHeight(22);
            },            
        ];
    }
    public function columnWidths(): array
    {
        $alphabet = range('A', 'Z');
        $widths = [];
        foreach ($alphabet as $col) {
            if($col=='A'){
                $widths[$col] = 12; 
            } else if($col=='B' || $col=='D'){
                $widths[$col] = 20; 
            } else if($col=='C'){
                $widths[$col] = 30; 
            } else if($col>='E'){
                $widths[$col] = 15; 
            }
        }

        // If your export can exceed 26 columns, handle AA, AB, etc:
        foreach ($alphabet as $a) {
            foreach ($alphabet as $b) {
                $col = $a.$b; // e.g. AA, AB, AC...
                $widths[$col] = 20;
            }
        }

        return $widths;        
    }

    public function headings(): array
    {
        $base = [
            'Nip',
            'Nama',
            'Jabatan',
            'Unit',
            'Grade Terakhir',
            'Tgl Kenaikan',
            'Rencana Kenaikan',
            'Tgl Rencana Kenaikan'
        ];

        $firstRow = $base;
        foreach ($this->years as $year) {
            $firstRow[] = $year;
            $firstRow[] = ''; // placeholder for merge
        }

        // Second row (semesters)
        $secondRow = array_fill(0, count($base), '');
        foreach ($this->years as $year) {
            $secondRow[] = 'Sem 1';
            $secondRow[] = 'Sem 2';
        }

        return [$firstRow, $secondRow];        

        // $yearCols = [];
        // foreach ($this->years as $year) {
        //     $yearCols[] = "{$year} Sem 1";
        //     $yearCols[] = "{$year} Sem 2";
        // }

        // return array_merge($base, $yearCols);
    }

    public function collection()
    {
        Carbon::setLocale('id');
        // $kd_areacari2 = $this->kd_areacarinya;
        // if($kd_areacari!="" && $kd_areacari!="semua"){
        //     $perintah = " and data_pegawai.kd_area='$kd_areacari'";
        // }
        $perintah = "";
        $pegawainya = DB::table('data_pegawai')
        ->selectRaw("
            data_pegawai.*,
            ifnull(b.nama_area,'') as nama_area,
            ifnull(c.id,0) as id2,
            ifnull(c.grade,'') as grade2,
            ifnull(c.tgl_kenaikan,'') as tgl_kenaikan2,
            ifnull(d.id,'') as id_master_grade
        ")
        ->leftJoin('master_area as b','b.kd_area','=','data_pegawai.kd_area')
        ->leftJoin('riwayat_grade as c','c.nip','=','data_pegawai.nip')
        ->leftJoin('master_grade as d','d.grade','=','c.grade')
        ->whereRaw("data_pegawai.aktif='1' and data_pegawai.payroll='1' and data_pegawai.aktif_simkp='1' and (data_pegawai.jenis_kpi<>'pusat' or (data_pegawai.jenis_kpi='pusat' and data_pegawai.level_kpi>=3))".$perintah)
        ->groupBy('data_pegawai.nip')
        ->orderBy('data_pegawai.id','asc')
        ->get();

        return $pegawainya->map(function ($row1) {
            if($row1->tgl_kenaikan2!=""){
                $tanggalnya2 = Carbon::parse($row1->tgl_kenaikan2)->subDay();
                // $tanggalnya = explode("-",$row1->tgl_kenaikan2);
                $tanggalnya = explode("-",$tanggalnya2);
                $tahun = $tanggalnya[0];
                $bulan = $tanggalnya[1];
                if(intval($bulan)<=6){
                    $semester = 1;
                } else {
                    $semester = 2;
                }
                $tahun_semester = $tahun.$semester;
            } else {
                $tahun = "";
                $bulan = "";
                $tahun_semester = "";
            }
            
            $id_master_grade = intval($row1->id_master_grade);
            if($tahun_semester!=""){
                if(intval($id_master_grade)>0){
                    $row3 = DB::table('master_grade')
                    ->selectRaw("*")
                    ->whereRaw("id>'$id_master_grade'")
                    ->orderby('id','asc')
                    ->first();
                    if($row3){
                        $rencana_kenaikan = $row3->grade;
                    } else {
                        $rencana_kenaikan = "";
                    }
                } else {
                    $rencana_kenaikan = "";
                }

                $rows2 = DB::table('riwayat_talenta')
                ->selectRaw("*")
                ->whereRaw("nip='".$row1->nip."' and concat(tahun,semester)>'$tahun_semester'")
                ->orderby('tahun','asc')
                ->orderby('semester','asc')
                ->get();
                $jumlah_pps = 0;
                $jumlah_kpo = 0;
                $jumlah_pot = 0;
                $jumlah_opt = 0;
                $jumlah_sop_spo = 0;
                $jumlah_lbs = 0;
                $jumlah_talenta = 0;
                $tahun_kenaikan = "";
                $semester_kenaikan = "";
                $tgl_kenaikan = "";
                foreach($rows2 as $row2){
                    $tahun_kenaikan = $row2->tahun;
                    $semester_kenaikan = $row2->semester;
                    if(intval($semester_kenaikan)==1){                            
                        $bulan_kenaikan = "06";
                    } else {
                        $bulan_kenaikan = "12";
                    }
                    // $blth_kenaikan = $tahun_kenaikan."-".str_pad($semester_kenaikan, 2, '0', STR_PAD_LEFT);                        
                    $tgl_kenaikan_awal = $tahun_kenaikan."-".$bulan_kenaikan."-01";
                    $jumlah_talenta++;

                    if($row2->nama_talenta=="Perlu Penyesuaian"){
                        // $jumlah_pps =  $jumlah_pps+$row2->jumlah_talenta;
                        $jumlah_pps++;                            
                    } else if($row2->nama_talenta=="Kandidat Potensial"){
                        $jumlah_kpo++;
                    } else if($row2->nama_talenta=="Potensial"){
                        $jumlah_pot++;
                    } else if($row2->nama_talenta=="Optimal"){
                        $jumlah_opt++;
                    } else if($row2->nama_talenta=="Sangat Potensial" || $row2->nama_talenta=="Sangat Optimal"){
                        $jumlah_sop_spo++;
                    } else if($row2->nama_talenta=="Luar Biasa"){
                        $jumlah_lbs++;
                    }

                    if($tgl_kenaikan==""){
                        if($jumlah_kpo>0){
                            if($jumlah_talenta>=8){
                                $tgl_kenaikan = Carbon::createFromFormat('Y-m-d', $tgl_kenaikan_awal)->endOfMonth()->addDay()->isoFormat('D MMMM Y');
                            }
                        } else if($jumlah_pot>0){
                            if($jumlah_talenta>=6){
                                $tgl_kenaikan = Carbon::createFromFormat('Y-m-d', $tgl_kenaikan_awal)->endOfMonth()->addDay()->isoFormat('D MMMM Y');
                            }
                        } else if($jumlah_opt>0){
                            if($jumlah_talenta>=5){
                                $tgl_kenaikan = Carbon::createFromFormat('Y-m-d', $tgl_kenaikan_awal)->endOfMonth()->addDay()->isoFormat('D MMMM Y');
                            }
                        } else if($jumlah_sop_spo>0){
                            if($jumlah_talenta>=4){
                                $tgl_kenaikan = Carbon::createFromFormat('Y-m-d', $tgl_kenaikan_awal)->endOfMonth()->addDay()->isoFormat('D MMMM Y');
                            }
                        } else if($jumlah_lbs>0){
                            if($jumlah_talenta>=2){
                                $tgl_kenaikan = Carbon::createFromFormat('Y-m-d', $tgl_kenaikan_awal)->endOfMonth()->addDay()->isoFormat('D MMMM Y');
                            }
                        } else {
                            $tgl_kenaikan = "";
                        }
                    }
                }
                if($jumlah_kpo>0){
                    $jumlah_talenta2 = $jumlah_talenta+1;
                    $tahun_kenaikan2 = $tahun_kenaikan;
                    $semester_kenaikan2 = $semester_kenaikan;
                    for($x=$jumlah_talenta2;$x<=8;$x++){
                        if(intval($semester_kenaikan2)==2){
                            $tahun_kenaikan2++;
                            $semester_kenaikan2 = 1;
                            $bulan_kenaikan2 = "06";
                        } else {
                            $semester_kenaikan2++;
                            $bulan_kenaikan2 = "12";
                        }
                        $tgl_kenaikan_awal2 = $tahun_kenaikan2."-".$bulan_kenaikan2."-01";
                    }
                    if($tgl_kenaikan==""){
                        $tgl_kenaikan = Carbon::createFromFormat('Y-m-d', $tgl_kenaikan_awal2)->endOfMonth()->addDay()->isoFormat('D MMMM Y');
                    }
                }
                if($jumlah_pot>0){
                    $jumlah_talenta2 = $jumlah_talenta+1;
                    $tahun_kenaikan2 = $tahun_kenaikan;
                    $semester_kenaikan2 = $semester_kenaikan;
                    for($x=$jumlah_talenta2;$x<=6;$x++){
                        if(intval($semester_kenaikan2)==2){
                            $tahun_kenaikan2++;
                            $semester_kenaikan2 = 1;
                            $bulan_kenaikan2 = "06";
                        } else {
                            $semester_kenaikan2++;
                            $bulan_kenaikan2 = "12";
                        }
                        $tgl_kenaikan_awal2 = $tahun_kenaikan2."-".$bulan_kenaikan2."-01";
                    }
                    if($tgl_kenaikan==""){
                        $tgl_kenaikan = Carbon::createFromFormat('Y-m-d', $tgl_kenaikan_awal2)->endOfMonth()->addDay()->isoFormat('D MMMM Y');
                    }
                } else if($jumlah_opt>0){
                    $jumlah_talenta2 = $jumlah_talenta+1;
                    $tahun_kenaikan2 = $tahun_kenaikan;
                    $semester_kenaikan2 = $semester_kenaikan;
                    for($x=$jumlah_talenta2;$x<=5;$x++){
                        if(intval($semester_kenaikan2)==2){
                            $tahun_kenaikan2++;
                            $semester_kenaikan2 = 1;
                            $bulan_kenaikan2 = "06";
                        } else {
                            $semester_kenaikan2++;
                            $bulan_kenaikan2 = "12";
                        }
                        $tgl_kenaikan_awal2 = $tahun_kenaikan2."-".$bulan_kenaikan2."-01";
                    }
                    if($tgl_kenaikan==""){
                        $tgl_kenaikan = Carbon::createFromFormat('Y-m-d', $tgl_kenaikan_awal2)->endOfMonth()->addDay()->isoFormat('D MMMM Y');
                    }
                } else if($jumlah_sop_spo>0){
                    $jumlah_talenta2 = $jumlah_talenta+1;
                    $tahun_kenaikan2 = $tahun_kenaikan;
                    $semester_kenaikan2 = $semester_kenaikan;
                    for($x=$jumlah_talenta2;$x<=4;$x++){
                        if(intval($semester_kenaikan2)==2){
                            $tahun_kenaikan2++;
                            $semester_kenaikan2 = 1;
                            $bulan_kenaikan2 = "06";
                        } else {
                            $semester_kenaikan2++;
                            $bulan_kenaikan2 = "12";
                        }
                        $tgl_kenaikan_awal2 = $tahun_kenaikan2."-".$bulan_kenaikan2."-01";
                    }
                    if($tgl_kenaikan==""){
                        $tgl_kenaikan = Carbon::createFromFormat('Y-m-d', $tgl_kenaikan_awal2)->endOfMonth()->addDay()->isoFormat('D MMMM Y');
                    }
                } else if($jumlah_lbs>0){
                    $jumlah_talenta2 = $jumlah_talenta+1;
                    $tahun_kenaikan2 = $tahun_kenaikan;
                    $semester_kenaikan2 = $semester_kenaikan;
                    for($x=$jumlah_talenta2;$x<=2;$x++){
                        if(intval($semester_kenaikan2)==2){
                            $tahun_kenaikan2++;
                            $semester_kenaikan2 = 1;
                            $bulan_kenaikan2 = "06";
                        } else {
                            $semester_kenaikan2++;
                            $bulan_kenaikan2 = "12";
                        }
                        $tgl_kenaikan_awal2 = $tahun_kenaikan2."-".$bulan_kenaikan2."-01";
                    }
                    if($tgl_kenaikan==""){
                        $tgl_kenaikan = Carbon::createFromFormat('Y-m-d', $tgl_kenaikan_awal2)->endOfMonth()->addDay()->isoFormat('D MMMM Y');
                    }
                }
            } else {
                $rencana_kenaikan = '';
                $tgl_kenaikan = '';
            }

            $row = [
                'Nip'    => $row1->nip,
                'Nama'  => $row1->nama,
                'Jabatan' => $row1->jabatan,
                'Unit' => $row1->nama_area,
                'Grade Terakhir' => $row1->grade2,
                'Tgl Kenaikan' => $row1->tgl_kenaikan2,
                'Rencana Kenaikan' => $rencana_kenaikan,
                'Tgl Rencana Kenaikan' => $tgl_kenaikan,
            ];

            foreach ($this->years as $year) {                
                $tahun_semester = $year."1";
                $row4 = DB::table('riwayat_talenta')
                ->selectRaw("nama_talenta")
                ->whereRaw("nip='".$row1->nip."' and tahun='$year' and semester='1'")
                ->first();
                if($row4){
                    $row["{$year} Sem 1"] = $row4->nama_talenta;
                } else {
                    $row["{$year} Sem 1"] = "";
                }

                $tahun_semester = $year."2";
                $row4 = DB::table('riwayat_talenta')
                ->selectRaw("nama_talenta")
                ->whereRaw("nip='".$row1->nip."' and tahun='$year' and semester='2'")
                ->first();
                if($row4){
                    $row["{$year} Sem 2"] = $row4->nama_talenta;
                } else {
                    $row["{$year} Sem 2"] = "";
                }
            }

            return $row;
        });
    }
}