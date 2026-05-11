<?php

namespace App\Exports;

use App\Models\Monitoring\Itemstok;
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

class ItemstokExport implements FromCollection, WithColumnFormatting, WithHeadings, WithMapping, ShouldAutoSize, WithColumnWidths, WithEvents
{
    use Exportable;
    public function  __construct($kd_regioncarinya,$tanggalcarinya)
    {
        $this->kd_regioncarinya = $kd_regioncarinya;
        $this->tanggalcarinya = $tanggalcarinya;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $event->sheet->getDelegate()->getStyle('A1:E1')
                ->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()
                ->setARGB('b4abab');
                $cellRange = 'A:E'; // All headers
                // $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(12);
                $event->sheet->getDelegate()->getStyle($cellRange)->getAlignment()->setWrapText(true);
                $cellRange1 = 'A1:E1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange1)->getAlignment()->setHorizontal('center');
                $cellRange2 = 'A:E'; // All headers
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
            'D' => 15,
            'E' => 15,
        ];
    }
    public function headings(): array
    {
        return [
            'No',
            'item No',
            'Item Description',
            'Stok Region',
            'Stok SAP',
        ];
    }

    public function map($data): array
    {
        // set number
        static $number = 1;
        // $stok_region = intval($data->stok_region)-intval($data->jumlah_penerimaan)+intval($data->jumlah_pengeluaran);
        return [
            $number++,
            $data->item_no,
            $data->item_description,
            intval($data->stok_region),
            intval($data->stok_sap),
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_NUMBER,
            'B' => NumberFormat::FORMAT_TEXT,
            'C' => NumberFormat::FORMAT_TEXT,
            'D' => NumberFormat::FORMAT_NUMBER,
            'E' => NumberFormat::FORMAT_NUMBER,
        ];
    }

    public function collection()
    {
        // $kd_regioncari = $this->kd_regioncarinya;
        // $tanggalcari = $this->tanggalcarinya;
        return $data = DB::table('v_item_stok')
        ->leftjoin('teams.list_item_sap', 'list_item_sap.item_no', '=', 'v_item_stok.item_no')
        ->leftJoin('v_stok_new as a', function($join){
            $join->on('v_item_stok.kd_warehouse', '=', 'a.kd_warehouse')
                 ->on('v_item_stok.item_no', '=', 'a.item_no');
        })
        ->leftJoin('v_stok_sap_new as b', function($join){
            $join->on('v_item_stok.kd_warehouse', '=', 'b.kd_warehouse')
                 ->on('v_item_stok.item_no', '=', 'b.item_no');
        })
        // ->leftJoin('v_stok_sap3 as b', function($join){
        //     $join->on('v_item_stok.kd_warehouse', '=', 'b.kd_warehouse')
        //          ->on('v_item_stok.item_no', '=', 'b.item_no');
        // })
        // ->leftJoin('gudang.v_rincian_penerimaan', function($query) use ($tanggalcari) {
        //     $query->on('v_rincian_penerimaan.kd_region','=','v_item_stok.kd_region')
        //         ->whereRaw("v_rincian_penerimaan.item_no=v_item_stok.item_no and v_rincian_penerimaan.tanggal>'$tanggalcari'")
        //         ->groupBy('v_rincian_penerimaan.kd_region','v_rincian_penerimaan.item_no');
        // })    
        // ->leftJoin('gudang.v_rincian_pengeluaran', function($query) use ($tanggalcari) {
        //     $query->on('v_rincian_pengeluaran.kd_region','=','v_item_stok.kd_region')
        //         ->whereRaw("v_rincian_pengeluaran.item_no=v_item_stok.item_no and v_rincian_pengeluaran.tanggal>'$tanggalcari'")
        //         ->groupBy('v_rincian_pengeluaran.kd_region','v_rincian_pengeluaran.item_no');
        // })    
        // ->leftJoin('gudang.v_rincian_penerimaan_sap', function($query) use ($tanggalcari) {
        //     $query->on('v_rincian_penerimaan_sap.kd_region','=','v_stok_region_new.kd_region')
        //         ->whereRaw("v_rincian_penerimaan_sap.tanggal>'$tanggalcari'")
        //         ->groupBy('v_rincian_penerimaan_sap.kd_region');
        // })    
        // ->leftJoin('gudang.v_rincian_pengeluaran_sap', function($query) use ($tanggalcari) {
        //     $query->on('v_rincian_pengeluaran_sap.kd_region','=','v_stok_region_new.kd_region')
        //         ->whereRaw("v_rincian_pengeluaran_sap.tanggal>'$tanggalcari'")
        //         ->groupBy('v_rincian_pengeluaran_sap.kd_region');
        // })    
        ->selectRaw("
            v_item_stok.item_no,
            list_item_sap.item_description as item_description,
            sum(ifnull(a.baik, 0)) as stok_region,
            ifnull(b.jumlah,0) as stok_sap
        ")
        ->whereRaw("v_item_stok.kd_region like '%".$this->kd_regioncarinya."%'")
        ->groupBy("v_item_stok.item_no")
        ->get();
        // dd($data);
    }
}