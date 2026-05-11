<?php

namespace App\Exports;

use App\Models\Monitoring\Stokgudang;
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

class StokgudangExport implements FromCollection, WithColumnFormatting, WithHeadings, WithMapping, ShouldAutoSize, WithColumnWidths, WithEvents
{
    use Exportable;
    public function  __construct($kode_gudangcarinya)
    {
        $this->kode_gudangcarinya = $kode_gudangcarinya;
        // $this->tanggalcarinya = $tanggalcarinya2;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $event->sheet->getDelegate()->getStyle('A1:H1')
                ->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()
                ->setARGB('b4abab');
                $cellRange = 'A:H'; // All headers
                // $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(12);
                $event->sheet->getDelegate()->getStyle($cellRange)->getAlignment()->setWrapText(true);
                $cellRange1 = 'A1:H1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange1)->getAlignment()->setHorizontal('center');
                $cellRange2 = 'A:H'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange2)->getAlignment()->setVertical('top');
            },
        ];
    }
    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 30,
            'C' => 40,
            'D' => 25,
            'E' => 40,
            'F' => 10,
            'G' => 15,
            'H' => 15,
        ];
    }
    public function headings(): array
    {
        return [
            'No',
            'item No',
            'Item Description',
            'Kode Project',
            'Nama Project',
            'Stok',
            'Harga Stn',
            'Total',
        ];
    }

    public function map($data): array
    {
        // set number
        static $number = 1;
        $total = $data->baik*$data->harga_satuan2;
        return [
            $number++,
            $data->item_no,
            $data->item_description,
            $data->kd_project,
            $data->nama_project,
            $data->baik,
            $data->harga_satuan2,
            $total,
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
            'F' => NumberFormat::FORMAT_NUMBER,
            'G' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'H' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }

    public function collection()
    {

        $kode_gudangcarinya = $this->kode_gudangcarinya;
        // $tanggalcarinya = $this->tanggalcarinya;
        return $data = DB::table('stok')
        ->leftjoin('teams.list_item_sap', 'list_item_sap.item_no', '=', 'stok.item_no')
        ->leftjoin('teams.project_sap', 'project_sap.kd_project', '=', 'stok.kd_project')
        // ->leftJoin('gudang.v_rincian_penerimaan', function($query) use ($tanggalcari) {
        //     $query->on('v_rincian_penerimaan.kode_tujuan','=','v_stok_new.kode_gudang')
        //         ->whereRaw("v_rincian_penerimaan.tanggal>'$tanggalcari'")
        //         ->groupBy('v_rincian_penerimaan.kode_tujuan');
        // })    
        // ->leftJoin('gudang.v_rincian_pengeluaran', function($query) use ($tanggalcari) {
        //     $query->on('v_rincian_pengeluaran.kode_asal','=','v_stok_new.kode_gudang')
        //         ->whereRaw("v_rincian_pengeluaran.tgl_transaksi>'$tanggalcari'")
        //         ->groupBy('v_rincian_pengeluaran.kode_asal');
        // })    
        ->selectRaw("
            stok.*,
            REPLACE(list_item_sap.item_description,'&#47;','/') as item_description,
            list_item_sap.harga_satuan as harga_satuan2,
            project_sap.nama_project as nama_project
        ")
        // ->whereRaw('master_kontrak.status', '=', '1')
        ->whereRaw("stok.kode_gudang='".$kode_gudangcarinya."'")
        ->groupBy('stok.kode')
        ->get();
        // dd($data);
    }
}