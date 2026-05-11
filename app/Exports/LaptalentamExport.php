<?php

namespace App\Exports;

use App\Models\Laptalentam;
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

// use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Database\Eloquent\Builder;

class LaptalentamExport implements FromCollection, WithColumnFormatting, WithHeadings, WithMapping, ShouldAutoSize, WithColumnWidths, WithEvents
{
    use Exportable;
    public function  __construct($tahuncarinya,$kd_areacarinya,$kd_jeniscarinya,$semestercarinya)
    {
        $this->tahuncarinya = $tahuncarinya;
        $this->kd_areacarinya = $kd_areacarinya;
        $this->kd_jeniscarinya = $kd_jeniscarinya;
        $this->semestercarinya = $semestercarinya;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $cellRangenya1 = 'A1:J1';
                $cellRangenya2 = 'A2:J2';
                $event->sheet->insertNewRowBefore(1,2);
                $event->sheet->mergeCells('A1:J1');
                $event->sheet->setCellValue('A1','TIM APRAISAL : '.$this->tahuncarinya);
                $event->sheet->getDelegate()->getStyle($cellRangenya1)->getAlignment()->setHorizontal('center');
                $event->sheet->mergeCells('A2:J2');
                $event->sheet->setCellValue('A2',"HASIL EVALUSASI PENILAIAN SEMESTER ".$this->semestercarinya." TAHUN ".$this->tahuncarinya);
                $event->sheet->getDelegate()->getStyle($cellRangenya2)->getAlignment()->setHorizontal('center');

                $event->sheet->getDelegate()->getStyle('A3:J3')
                ->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()
                ->setARGB('b4abab');
                $cellRange = 'A:J'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getAlignment()->setWrapText(true);
                $cellRange1 = 'A3:J3'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange1)->getAlignment()->setHorizontal('center');
                $cellRange2 = 'A:J'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange2)->getAlignment()->setVertical('top');
            },
        ];
    }
    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 20,
            'C' => 40,
            'D' => 20,
            'E' => 15,
            'F' => 15,
            'G' => 15,
            'H' => 15,
            'I' => 15,
            'J' => 30,
        ];
    }
    public function headings(): array
    {
        return [
            'No',
            'Nip',
            'Nama',
            'Grade',
            'PeG',
            'NSK (Angka)',
            'NSK (Huruf)',
            'NKI (Angka)',
            'NKI (Huruf)',
            'Kriteria Talenta',
        ];
    }

    public function map($data): array
    {
        // set number
        static $number = 1;
        if($this->semestercarinya=="1"){
            $skor_kinerja_semester = $data->skor_kinerja_semester1;
            $huruf_kinerja_semester = $data->huruf_kinerja_semester1;
            $skor_individu_semester = $data->skor_individu_semester1;
            $huruf_individu_semester = $data->huruf_individu_semester1;
            $nama_talenta_semester = $data->nama_talenta_semester1;
        } else {
            $skor_kinerja_semester = $data->skor_kinerja_semester2;
            $huruf_kinerja_semester = $data->huruf_kinerja_semester2;
            $skor_individu_semester = $data->skor_individu_semester2;
            $huruf_individu_semester = $data->huruf_individu_semester2;
            $nama_talenta_semester = $data->nama_talenta_semester2;
        }

        return [
            $number++,
            $data->nip,
            $data->nama,
            $data->grade,
            $data->peg,
            $skor_kinerja_semester,
            $huruf_kinerja_semester,
            $skor_individu_semester,
            $huruf_individu_semester,
            $nama_talenta_semester,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_NUMBER,
            'B' => NumberFormat::FORMAT_TEXT,
            'C' => NumberFormat::FORMAT_TEXT,
            'D' => NumberFormat::FORMAT_TEXT,
            'E' => NumberFormat::FORMAT_TEXT,
            'F' => NumberFormat::FORMAT_TEXT,
            'G' => NumberFormat::FORMAT_TEXT,
            'H' => NumberFormat::FORMAT_TEXT,
            'I' => NumberFormat::FORMAT_TEXT,
            'J' => NumberFormat::FORMAT_TEXT,
        ];
    }

    public function collection()
    {

        $tahuncari2 = $this->tahuncarinya;
        $kd_areacari2 = $this->kd_areacarinya;
        $kd_jeniscari2 = $this->kd_jeniscarinya;
        $semestercari = $this->semestercarinya;

        $perintah = "";
        if($kd_areacari2!="" && $kd_areacari2!="semua"){
            $perintah .= " and penilaian_pegawai.kd_area='$kd_areacari2'";
        }
        if($kd_jeniscari2!="" && $kd_jeniscari2!="semua"){
            $perintah .= " and c.kd_jenis='$kd_jeniscari2'";
        }

        return DB::table('penilaian_pegawai')->selectRaw("
            penilaian_pegawai.*,
            ifnull(b.nama_area,'') as nama_area,
            c.nama as nama,
            c.jabatan as jabatan,
            c.grade as grade,
            c.peg as peg
        ")       
        ->leftJoin('master_area as b','b.kd_area','=','penilaian_pegawai.kd_area')
        ->leftJoin('data_pegawai as c','c.nip','=','penilaian_pegawai.nip')
        // ->leftJoin('data_pegawai as c', function($join){
        //     $join->whereRaw("c.nip=penilaian_pegawai.nip and c.level_kpi>='3'");
        // })    
        ->whereRaw("penilaian_pegawai.tahun='$tahuncari2' and penilaian_pegawai.nip not in (select nip from data_pegawai where jenis_kpi='pusat' and level_kpi<='2')".$perintah)
        ->groupBy('penilaian_pegawai.nip')
        ->orderBy('penilaian_pegawai.id','asc')
        ->get();
        // dd($data);
    }
}
