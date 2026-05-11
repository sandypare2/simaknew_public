<?php

namespace App\Exports;

use App\Models\WO\Datato;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
// use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Database\Eloquent\Builder;

class DatatoExport implements FromQuery, WithColumnFormatting, WithHeadings, WithMapping, ShouldAutoSize
{
    use Exportable;
    public function  __construct($blth_wo,$kd_wilayah,$kd_area,$kd_unit,$jenis_to,$status_wo,$idpel,$tarif,$daya1,$daya2,$kogol,$textcari)
    {
        $this->blth_wo = $blth_wo;
        $this->kd_wilayah = $kd_wilayah;
        $this->kd_area = $kd_area;
        $this->kd_unit = $kd_unit;
        $this->jenis_to = $jenis_to;
        $this->status_wo = $status_wo;
        $this->idpel = $idpel;
        $this->daya1 = $daya1;
        $this->daya2 = $daya2;
        $this->kogol = $kogol;
        $this->nama = $textcari;
    }

    public function headings(): array
    {
        return [
            'no',
            'idpel',
            'nama',
        ];
    }

    public function map($datato): array
    {
        // set number
        static $number = 1;
        return [
            $number++,
            $datato->idpel,
            $datato->nama,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_NUMBER,
            'B' => NumberFormat::FORMAT_TEXT,
            'C' => NumberFormat::FORMAT_TEXT,
        ];
    }

    public function query()
    {
        // return Datato::query();
        $data = Datato::query();
        if ($this->blth_wo != '' && $this->blth_wo != 'semua') {
            $data = $data->where('blth_wo',$this->blth_wo);
        }
        if ($this->kd_wilayah != '' && $this->kd_wilayah != 'semua') {
            $data = $data->where('kd_wilayah',$this->kd_wilayah);
        }
        if ($this->kd_area != '' && $this->kd_area != 'semua') {
            $data = $data->where('kd_area',$this->kd_area);
        }
        if ($this->kd_unit != '' && $this->kd_unit != 'semua') {
            $data = $data->where('kd_unit',$this->kd_unit);
        }
        if ($this->jenis_to != '-' && $this->jenis_to != 'semua') {
            $data = $data->where('jenis_to',$this->jenis_to);
        }
        if ($this->status_wo != '2' && $this->jenis_to != 'semua') {
            $data = $data->where('status_wo',$this->status_wo);
        }
        if ($this->idpel != '' && $this->idpel != 'semua') {
            $data = $data->where('idpel',$this->idpel);
        }
        if ($this->daya1 != '' && $this->daya2 != '') {
            $data = $data->where('daya','>=',$this->daya1);
            $data = $data->where('daya','<=',$this->daya2);
        }
        if ($this->kogol != '-' && $this->kogol != 'semua') {
            $data = $data->where('kogol',$this->kogol);
        }
        if (!empty($this->nama)) {
            $data = $data->where('nama', 'like', "%" . $this->nama . "%");
        }
        return $data;
    }
}