<?php

namespace App\Exports;

use App\Models\Kinerjapegawaim;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
// use Maatwebsite\Excel\Sheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
    use PhpOffice\PhpSpreadsheet\Style\Color;

use Illuminate\Support\Facades\DB;

// use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Database\Eloquent\Builder;

class DatapencapaianExport implements FromCollection, WithColumnFormatting, WithHeadings, WithMapping, ShouldAutoSize, WithColumnWidths, WithEvents
{
    use Exportable;
    public function  __construct($tahuncarinya,$nipcarinya,$namacarinya)
    {
        $this->tahuncarinya = $tahuncarinya;
        $this->nipcarinya = $nipcarinya;
        $this->namacarinya = $namacarinya;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $cellRangenya1 = 'A1:BB1';
                $cellRangenya2 = 'A2:BB2';
                $event->sheet->insertNewRowBefore(1,2);
                $event->sheet->mergeCells('A1:BB1');
                $event->sheet->setCellValue('A1','TAHUN : '.$this->tahuncarinya);
                $event->sheet->getDelegate()->getStyle($cellRangenya1)->getAlignment()->setHorizontal('left');
                $event->sheet->mergeCells('A2:BB2');
                $event->sheet->setCellValue('A2',"PEGAWAI :".$this->namacarinya." (".$this->nipcarinya.")");
                $event->sheet->getDelegate()->getStyle($cellRangenya2)->getAlignment()->setHorizontal('left');

                $event->sheet->getDelegate()->getStyle('A3:BB4')
                ->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()
                ->setARGB('b4abab');

                $event->sheet->mergeCells('E3:H3');
                $event->sheet->mergeCells('I3:L3');
                $event->sheet->mergeCells('M3:P3');
                $event->sheet->mergeCells('Q3:T3');
                $event->sheet->mergeCells('U3:X3');
                $event->sheet->mergeCells('Y3:AB3');
                $event->sheet->mergeCells('AC3:AC4');
                $event->sheet->mergeCells('AD3:AG3');
                $event->sheet->mergeCells('AH3:AK3');
                $event->sheet->mergeCells('AL3:AO3');
                $event->sheet->mergeCells('AP3:AS3');
                $event->sheet->mergeCells('AT3:AW3');
                $event->sheet->mergeCells('AX3:BA3');
                $event->sheet->mergeCells('BB3:BB4');
                $cellRange = 'A:BB'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getAlignment()->setWrapText(true);
                $cellRange1 = 'A1:BB2'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange1)->getAlignment()->setHorizontal('center');
                // $cellRange1 = 'A:AZ'; // All headers
                // $event->sheet->getDelegate()->getStyle($cellRange1)->getAlignment()->setHorizontal('center');
                $cellRange2 = 'A:BB'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange2)->getAlignment()->setVertical('top');

                $totalRows = $event->sheet->getDelegate()->getHighestRow();
                for ($row = 5; $row <= $totalRows + 1; $row++) {
                    $event->sheet->getDelegate()->getStyle('E'.$row.':BB'.$row)->getAlignment()->setHorizontal('center');
                    $lastRow = $event->sheet->getDelegate()->getHighestRow();
                    $cellValue = $event->sheet->getCell('D' . $row)->getValue();
                    if ($cellValue=="Target") {
                        $event->sheet->getStyle('D'.$row.':BB'.$row)
                              ->getFill()
                              ->setFillType(Fill::FILL_SOLID)
                              ->getStartColor()
                              ->setARGB('ffffd9cc');
                    } else if ($cellValue=="Realisasi") {
                        $event->sheet->getStyle('D'.$row.':BB'.$row)
                              ->getFill()
                              ->setFillType(Fill::FILL_SOLID)
                              ->getStartColor()
                              ->setARGB('ffccffcc');
                    } else if ($cellValue=="Nilai:R") {
                        $event->sheet->getStyle('D'.$row.':BB'.$row)
                              ->getFill()
                              ->setFillType(Fill::FILL_SOLID)
                              ->getStartColor()
                              ->setARGB('FFE5E4E2');
                    } else if ($cellValue=="Finalisasi") {
                        $event->sheet->getStyle('D'.$row.':BB'.$row)
                              ->getFill()
                              ->setFillType(Fill::FILL_SOLID)
                              ->getStartColor()
                              ->setARGB('FF1E90FF');
                    } else if ($cellValue=="Nilai:F") {
                        $event->sheet->getStyle('D'.$row.':BB'.$row)
                              ->getFill()
                              ->setFillType(Fill::FILL_SOLID)
                              ->getStartColor()
                              ->setARGB('FFE5E4E2');
                    }

                    $cellValue1 = $event->sheet->getCell('A' . $row)->getValue();
                    if ($cellValue1!="") {
                        $event->sheet->mergeCells('A'.$row.':A'.($row+4));
                        $event->sheet->mergeCells('B'.$row.':B'.($row+4));
                        $event->sheet->mergeCells('C'.$row.':C'.($row+4));
                    }
                }
            },
        ];
    }
    public function columnWidths(): array
    {
        return [
            'A'=>5,
            'B'=>10,
            'C'=>40,
            'D'=>15,
            'E'=>10,
            'F'=>10,
            'G'=>10,
            'H'=>10,
            'I'=>10,
            'J'=>10,
            'K'=>10,
            'L'=>10,
            'M'=>10,
            'N'=>10,
            'O'=>10,
            'P'=>10,
            'Q'=>10,
            'R'=>10,
            'S'=>10,
            'T'=>10,
            'U'=>10,
            'V'=>10,
            'W'=>10,
            'X'=>10,
            'Y'=>10,
            'Z'=>10,
            'AA'=>10,
            'AB'=>10,
            'AC'=>10,
            'AD'=>10,
            'AE'=>10,
            'AF'=>10,
            'AG'=>10,
            'AH'=>10,
            'AI'=>10,
            'AJ'=>10,
            'AK'=>10,
            'AL'=>10,
            'AM'=>10,
            'AN'=>10,
            'AO'=>10,
            'AP'=>10,
            'AQ'=>10,
            'AR'=>10,
            'AS'=>10,
            'AT'=>10,
            'AU'=>10,
            'AV'=>10,
            'AW'=>10,
            'AX'=>10,
            'AY'=>10,
            'AZ'=>10,
            'BA'=>10,
            'BB'=>10,

        ];
    }
    public function headings(): array
    {
        $header1 = [
            'No',
            'Tahun',
            'Uraian Kinerja',
            'Keterangan',
            'Januari',
            '',
            '',
            '',
            'Februari',
            '',
            '',
            '',
            'Maret',
            '',
            '',
            '',
            'April',
            '',
            '',
            '',
            'Mei',
            '',
            '',
            '',
            'Juni',
            '',
            '',
            '',
            'Semester 1',
            'Juli',
            '',
            '',
            '',
            'Agustus',
            '',
            '',
            '',
            'September',
            '',
            '',
            '',
            'Oktober',
            '',
            '',
            '',
            'Nopember',
            '',
            '',
            '',
            'Desember',
            '',
            '',
            '',
            'Semester 2',
        ];

        $header2 = [
            '',
            '',
            '',
            '',
            'Kuantitas',
            'Kualitas',
            'Waktu',
            'Prosentase',
            'Kuantitas',
            'Kualitas',
            'Waktu',
            'Prosentase',
            'Kuantitas',
            'Kualitas',
            'Waktu',
            'Prosentase',
            'Kuantitas',
            'Kualitas',
            'Waktu',
            'Prosentase',
            'Kuantitas',
            'Kualitas',
            'Waktu',
            'Prosentase',
            'Kuantitas',
            'Kualitas',
            'Waktu',
            'Prosentase',
            '',
            'Kuantitas',
            'Kualitas',
            'Waktu',
            'Prosentase',
            'Kuantitas',
            'Kualitas',
            'Waktu',
            'Prosentase',
            'Kuantitas',
            'Kualitas',
            'Waktu',
            'Prosentase',
            'Kuantitas',
            'Kualitas',
            'Waktu',
            'Prosentase',
            'Kuantitas',
            'Kualitas',
            'Waktu',
            'Prosentase',
            'Kuantitas',
            'Kualitas',
            'Waktu',
            'Prosentase',
            '',
        ];

        return [
            $header1,
            $header2,
        ];        
    }

    public function map($data): array
    {
        // set number
        static $number = 1;
        $baris1 = [
            $number++,
            $data->tahun,
            $data->uraian,
            'Target',
            $data->target01kn,
            $data->target01kl,
            $data->target01wk,
            $data->target01,
            $data->target02kn,
            $data->target02kl,
            $data->target02wk,
            $data->target02,
            $data->target03kn,
            $data->target03kl,
            $data->target03wk,
            $data->target03,
            $data->target04kn,
            $data->target04kl,
            $data->target04wk,
            $data->target04,
            $data->target05kn,
            $data->target05kl,
            $data->target05wk,
            $data->target05,
            $data->target06kn,
            $data->target06kl,
            $data->target06wk,
            $data->target06,
            '',
            $data->target07kn,
            $data->target07kl,
            $data->target07wk,
            $data->target07,
            $data->target08kn,
            $data->target08kl,
            $data->target08wk,
            $data->target08,
            $data->target09kn,
            $data->target09kl,
            $data->target09wk,
            $data->target09,
            $data->target10kn,
            $data->target10kl,
            $data->target10wk,
            $data->target10,
            $data->target11kn,
            $data->target11kl,
            $data->target11wk,
            $data->target11,
            $data->target12kn,
            $data->target12kl,
            $data->target12wk,
            $data->target12,
            '',
        ]; 
        $baris2 = [
            '',
            '',
            '',
            'Realisasi',
            $data->realisasi01kn,
            $data->realisasi01kl,
            $data->realisasi01wk,
            $data->realisasi01,
            $data->realisasi02kn,
            $data->realisasi02kl,
            $data->realisasi02wk,
            $data->realisasi02,
            $data->realisasi03kn,
            $data->realisasi03kl,
            $data->realisasi03wk,
            $data->realisasi03,
            $data->realisasi04kn,
            $data->realisasi04kl,
            $data->realisasi04wk,
            $data->realisasi04,
            $data->realisasi05kn,
            $data->realisasi05kl,
            $data->realisasi05wk,
            $data->realisasi05,
            $data->realisasi06kn,
            $data->realisasi06kl,
            $data->realisasi06wk,
            $data->realisasi06,
            $data->nilai_semester1,
            $data->realisasi07kn,
            $data->realisasi07kl,
            $data->realisasi07wk,
            $data->realisasi07,
            $data->realisasi08kn,
            $data->realisasi08kl,
            $data->realisasi08wk,
            $data->realisasi08,
            $data->realisasi09kn,
            $data->realisasi09kl,
            $data->realisasi09wk,
            $data->realisasi09,
            $data->realisasi10kn,
            $data->realisasi10kl,
            $data->realisasi10wk,
            $data->realisasi10,
            $data->realisasi11kn,
            $data->realisasi11kl,
            $data->realisasi11wk,
            $data->realisasi11,
            $data->realisasi12kn,
            $data->realisasi12kl,
            $data->realisasi12wk,
            $data->realisasi12,
            $data->nilai_semester2,
        ]; 
        $baris3 = [
            '',
            '',
            '',
            'Nilai:R',
            $data->nilai01kn,
            $data->nilai01kl,
            $data->nilai01wk,
            '',
            $data->nilai02kn,
            $data->nilai02kl,
            $data->nilai02wk,
            '',
            $data->nilai03kn,
            $data->nilai03kl,
            $data->nilai03wk,
            '',
            $data->nilai04kn,
            $data->nilai04kl,
            $data->nilai04wk,
            '',
            $data->nilai05kn,
            $data->nilai05kl,
            $data->nilai05wk,
            '',
            $data->nilai06kn,
            $data->nilai06kl,
            $data->nilai06wk,
            '',
            '',
            $data->nilai07kn,
            $data->nilai07kl,
            $data->nilai07wk,
            '',
            $data->nilai08kn,
            $data->nilai08kl,
            $data->nilai08wk,
            '',
            $data->nilai09kn,
            $data->nilai09kl,
            $data->nilai09wk,
            '',
            $data->nilai10kn,
            $data->nilai10kl,
            $data->nilai10wk,
            '',
            $data->nilai11kn,
            $data->nilai11kl,
            $data->nilai11wk,
            '',
            $data->nilai12kn,
            $data->nilai12kl,
            $data->nilai12wk,
            '',
            '',
        ]; 
        $baris4 = [
            '',
            '',
            '',
            'Finalisasi',
            $data->realisasi01bkn,
            $data->realisasi01bkl,
            $data->realisasi01bwk,
            $data->realisasi01b,
            $data->realisasi02bkn,
            $data->realisasi02bkl,
            $data->realisasi02bwk,
            $data->realisasi02b,
            $data->realisasi03bkn,
            $data->realisasi03bkl,
            $data->realisasi03bwk,
            $data->realisasi03b,
            $data->realisasi04bkn,
            $data->realisasi04bkl,
            $data->realisasi04bwk,
            $data->realisasi04b,
            $data->realisasi05bkn,
            $data->realisasi05bkl,
            $data->realisasi05bwk,
            $data->realisasi05b,
            $data->realisasi06bkn,
            $data->realisasi06bkl,
            $data->realisasi06bwk,
            $data->realisasi06b,
            $data->nilaib_semester1,
            $data->realisasi07bkn,
            $data->realisasi07bkl,
            $data->realisasi07bwk,
            $data->realisasi07b,
            $data->realisasi08bkn,
            $data->realisasi08bkl,
            $data->realisasi08bwk,
            $data->realisasi08b,
            $data->realisasi09bkn,
            $data->realisasi09bkl,
            $data->realisasi09bwk,
            $data->realisasi09b,
            $data->realisasi10bkn,
            $data->realisasi10bkl,
            $data->realisasi10bwk,
            $data->realisasi10b,
            $data->realisasi11bkn,
            $data->realisasi11bkl,
            $data->realisasi11bwk,
            $data->realisasi11b,
            $data->realisasi12bkn,
            $data->realisasi12bkl,
            $data->realisasi12bwk,
            $data->realisasi12b,
            $data->nilaib_semester2,
        ]; 
        $baris5 = [
            '',
            '',
            '',
            'Nilai:F',
            $data->nilai01bkn,
            $data->nilai01bkl,
            $data->nilai01bwk,
            '',
            $data->nilai02bkn,
            $data->nilai02bkl,
            $data->nilai02bwk,
            '',
            $data->nilai03bkn,
            $data->nilai03bkl,
            $data->nilai03bwk,
            '',
            $data->nilai04bkn,
            $data->nilai04bkl,
            $data->nilai04bwk,
            '',
            $data->nilai05bkn,
            $data->nilai05bkl,
            $data->nilai05bwk,
            '',
            $data->nilai06bkn,
            $data->nilai06bkl,
            $data->nilai06bwk,
            '',
            '',
            $data->nilai07bkn,
            $data->nilai07bkl,
            $data->nilai07bwk,
            '',
            $data->nilai08bkn,
            $data->nilai08bkl,
            $data->nilai08bwk,
            '',
            $data->nilai09bkn,
            $data->nilai09bkl,
            $data->nilai09bwk,
            '',
            $data->nilai10bkn,
            $data->nilai10bkl,
            $data->nilai10bwk,
            '',
            $data->nilai11bkn,
            $data->nilai11bkl,
            $data->nilai11bwk,
            '',
            $data->nilai12bkn,
            $data->nilai12bkl,
            $data->nilai12bwk,
            '',
            '',
        ]; 
        return [
            $baris1,
            $baris2,
            $baris3,
            $baris4,
            $baris5,
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
            'K' => NumberFormat::FORMAT_TEXT,
            'L' => NumberFormat::FORMAT_TEXT,
            'M' => NumberFormat::FORMAT_TEXT,
            'N' => NumberFormat::FORMAT_TEXT,
            'O' => NumberFormat::FORMAT_TEXT,
            'P' => NumberFormat::FORMAT_TEXT,
            'Q' => NumberFormat::FORMAT_TEXT,
            'R' => NumberFormat::FORMAT_TEXT,
            'S' => NumberFormat::FORMAT_TEXT,
            'T' => NumberFormat::FORMAT_TEXT,
            'U' => NumberFormat::FORMAT_TEXT,
            'V' => NumberFormat::FORMAT_TEXT,
            'W' => NumberFormat::FORMAT_TEXT,
            'X' => NumberFormat::FORMAT_TEXT,
            'Y' => NumberFormat::FORMAT_TEXT,
            'Z' => NumberFormat::FORMAT_TEXT,
            'AA' => NumberFormat::FORMAT_TEXT,
            'AB' => NumberFormat::FORMAT_TEXT,
            'AC' => NumberFormat::FORMAT_TEXT,
            'AD' => NumberFormat::FORMAT_TEXT,
            'AE' => NumberFormat::FORMAT_TEXT,
            'AF' => NumberFormat::FORMAT_TEXT,
            'AG' => NumberFormat::FORMAT_TEXT,
            'AH' => NumberFormat::FORMAT_TEXT,
            'AI' => NumberFormat::FORMAT_TEXT,
            'AJ' => NumberFormat::FORMAT_TEXT,
            'AK' => NumberFormat::FORMAT_TEXT,
            'AL' => NumberFormat::FORMAT_TEXT,
            'AM' => NumberFormat::FORMAT_TEXT,
            'AN' => NumberFormat::FORMAT_TEXT,
            'AO' => NumberFormat::FORMAT_TEXT,
            'AP' => NumberFormat::FORMAT_TEXT,
            'AQ' => NumberFormat::FORMAT_TEXT,
            'AR' => NumberFormat::FORMAT_TEXT,
            'AS' => NumberFormat::FORMAT_TEXT,
            'AT' => NumberFormat::FORMAT_TEXT,
            'AU' => NumberFormat::FORMAT_TEXT,
            'AV' => NumberFormat::FORMAT_TEXT,
            'AW' => NumberFormat::FORMAT_TEXT,
            'AX' => NumberFormat::FORMAT_TEXT,
            'AY' => NumberFormat::FORMAT_TEXT,
            'AZ' => NumberFormat::FORMAT_TEXT,
            'BA' => NumberFormat::FORMAT_TEXT,
            'BB' => NumberFormat::FORMAT_TEXT,
        ];
    }

    public function collection()
    {

        $tahuncari2 = $this->tahuncarinya;
        $nipcari2 = $this->nipcarinya;
        return $data = DB::table('kpi_pegawai')->selectRaw("
            kpi_pegawai.*,
            b.type_target as type_target,
            b.kd_urut as kd_urut,
            b.polarisasi as polarisasi,
            b.satuan_kuantitas as satuan_kuantitas,
            b.satuan_kualitas as satuan_kualitas,
            b.satuan_waktu as satuan_waktu,
            b.uraian as uraian,
            b.target01kn as target01kn,
            b.target01kl as target01kl,
            b.target01wk as target01wk,
            b.target01 as target01,
            b.target02kn as target02kn,
            b.target02kl as target02kl,
            b.target02wk as target02wk,
            b.target02 as target02,
            b.target03kn as target03kn,
            b.target03kl as target03kl,
            b.target03wk as target03wk,
            b.target03 as target03,
            b.target04kn as target04kn,
            b.target04kl as target04kl,
            b.target04wk as target04wk,
            b.target04 as target04,
            b.target05kn as target05kn,
            b.target05kl as target05kl,
            b.target05wk as target05wk,
            b.target05 as target05,
            b.target06kn as target06kn,
            b.target06kl as target06kl,
            b.target06wk as target06wk,
            b.target06 as target06,
            b.target07kn as target07kn,
            b.target07kl as target07kl,
            b.target07wk as target07wk,
            b.target07 as target07,
            b.target08kn as target08kn,
            b.target08kl as target08kl,
            b.target08wk as target08wk,
            b.target08 as target08,
            b.target09kn as target09kn,
            b.target09kl as target09kl,
            b.target09wk as target09wk,
            b.target09 as target09,
            b.target10kn as target10kn,
            b.target10kl as target10kl,
            b.target10wk as target10wk,
            b.target10 as target10,
            b.target11kn as target11kn,
            b.target11kl as target11kl,
            b.target11wk as target11wk,
            b.target11 as target11,
            b.target12kn as target12kn,
            b.target12kl as target12kl,
            b.target12wk as target12wk,
            b.target12 as target12,
            c.realisasi01bkn as realisasi01bkn,
            c.realisasi01bkl as realisasi01bkl,
            c.realisasi01bwk as realisasi01bwk,
            c.nilai01bkn as nilai01bkn,
            c.nilai01bkl as nilai01bkl,
            c.nilai01bwk as nilai01bwk,
            c.realisasi01b as realisasi01b,
            c.realisasi02bkn as realisasi02bkn,
            c.realisasi02bkl as realisasi02bkl,
            c.realisasi02bwk as realisasi02bwk,
            c.nilai02bkn as nilai02bkn,
            c.nilai02bkl as nilai02bkl,
            c.nilai02bwk as nilai02bwk,
            c.realisasi02b as realisasi02b,
            c.realisasi03bkn as realisasi03bkn,
            c.realisasi03bkl as realisasi03bkl,
            c.realisasi03bwk as realisasi03bwk,
            c.nilai03bkn as nilai03bkn,
            c.nilai03bkl as nilai03bkl,
            c.nilai03bwk as nilai03bwk,
            c.realisasi03b as realisasi03b,
            c.realisasi04bkn as realisasi04bkn,
            c.realisasi04bkl as realisasi04bkl,
            c.realisasi04bwk as realisasi04bwk,
            c.nilai04bkn as nilai04bkn,
            c.nilai04bkl as nilai04bkl,
            c.nilai04bwk as nilai04bwk,
            c.realisasi04b as realisasi04b,
            c.realisasi05bkn as realisasi05bkn,
            c.realisasi05bkl as realisasi05bkl,
            c.realisasi05bwk as realisasi05bwk,
            c.nilai05bkn as nilai05bkn,
            c.nilai05bkl as nilai05bkl,
            c.nilai05bwk as nilai05bwk,
            c.realisasi05b as realisasi05b,
            c.realisasi06bkn as realisasi06bkn,
            c.realisasi06bkl as realisasi06bkl,
            c.realisasi06bwk as realisasi06bwk,
            c.nilai06bkn as nilai06bkn,
            c.nilai06bkl as nilai06bkl,
            c.nilai06bwk as nilai06bwk,
            c.realisasi06b as realisasi06b,
            c.realisasi07bkn as realisasi07bkn,
            c.realisasi07bkl as realisasi07bkl,
            c.realisasi07bwk as realisasi07bwk,
            c.nilai07bkn as nilai07bkn,
            c.nilai07bkl as nilai07bkl,
            c.nilai07bwk as nilai07bwk,
            c.realisasi07b as realisasi07b,
            c.realisasi08bkn as realisasi08bkn,
            c.realisasi08bkl as realisasi08bkl,
            c.realisasi08bwk as realisasi08bwk,
            c.nilai08bkn as nilai08bkn,
            c.nilai08bkl as nilai08bkl,
            c.nilai08bwk as nilai08bwk,
            c.realisasi08b as realisasi08b,
            c.realisasi09bkn as realisasi09bkn,
            c.realisasi09bkl as realisasi09bkl,
            c.realisasi09bwk as realisasi09bwk,
            c.nilai09bkn as nilai09bkn,
            c.nilai09bkl as nilai09bkl,
            c.nilai09bwk as nilai09bwk,
            c.realisasi09b as realisasi09b,
            c.realisasi10bkn as realisasi10bkn,
            c.realisasi10bkl as realisasi10bkl,
            c.realisasi10bwk as realisasi10bwk,
            c.nilai10bkn as nilai10bkn,
            c.nilai10bkl as nilai10bkl,
            c.nilai10bwk as nilai10bwk,
            c.realisasi10b as realisasi10b,
            c.realisasi11bkn as realisasi11bkn,
            c.realisasi11bkl as realisasi11bkl,
            c.realisasi11bwk as realisasi11bwk,
            c.nilai11bkn as nilai11bkn,
            c.nilai11bkl as nilai11bkl,
            c.nilai11bwk as nilai11bwk,
            c.realisasi11b as realisasi11b,
            c.realisasi12bkn as realisasi12bkn,
            c.realisasi12bkl as realisasi12bkl,
            c.realisasi12bwk as realisasi12bwk,
            c.nilai12bkn as nilai12bkn,
            c.nilai12bkl as nilai12bkl,
            c.nilai12bwk as nilai12bwk,
            c.realisasi12b as realisasi12b,
            c.nilaib_semester1 as nilaib_semester1,
            c.nilaib_semester2 as nilaib_semester2
        ")
        ->leftJoin('cascading_kpi as b','b.kode_cascading','=','kpi_pegawai.kode_cascading')
        ->leftJoin('finalisasi_kpi as c','c.kode_kpi','=','kpi_pegawai.kode_kpi')
        ->whereRaw("kpi_pegawai.tahun='$tahuncari2' and kpi_pegawai.nip='$nipcari2'")
        ->orderBy('kpi_pegawai.kode_cascading','asc')
        ->get();
        // dd($data);
    }
}