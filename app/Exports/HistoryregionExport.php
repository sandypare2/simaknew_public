<?php

namespace App\Exports;

use App\Models\Monitoring\History;
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

class HistoryregionExport implements FromCollection, WithColumnFormatting, WithHeadings, WithMapping, ShouldAutoSize, WithColumnWidths, WithEvents
{
    use Exportable;
    public function  __construct($kd_regioncarinya3,$nama_regioncarinya3,$materialcarinya3,$nama_materialcarinya3)
    {
        $this->kd_regioncari3 = $kd_regioncarinya3;
        $this->nama_regioncari3 = $nama_regioncarinya3;
        $this->materialcari3 = $materialcarinya3;
        $this->nama_materialcari3 = $nama_materialcarinya3;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $event->sheet->getDelegate()->getStyle('A1:I1')
                ->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()
                ->setARGB('b4abab');
                $event->sheet->insertNewRowBefore(1,2);
                $event->sheet->mergeCells('A1:I1');
                $event->sheet->setCellValue('A1',$this->nama_regioncari3);
                $event->sheet->mergeCells('A2:I2');
                $event->sheet->setCellValue('A2',"MATERIAL :".$this->materialcari3." (".$this->nama_materialcari3.")");
                $cellRange = 'A:I'; // All headers
                // $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(12);
                $event->sheet->getDelegate()->getStyle($cellRange)->getAlignment()->setWrapText(true);
                $cellRange1 = 'A1:I3'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange1)->getAlignment()->setHorizontal('center');
                $cellRange2 = 'A:I'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange2)->getAlignment()->setVertical('top');
            },
        ];
    }
    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 15,
            'C' => 15,
            'D' => 20,
            'E' => 30,
            'F' => 30,
            'G' => 40,
            'H' => 10,
            'I' => 10,
        ];
    }
    public function headings(): array
    {
        // $header1 = [
        //     '',
        //     '',
        //     '',
        //     '',
        //     '',
        //     '',
        //     '',
        //     '',
        //     ''
        // ];        
        // $header2 = [
        //     '',
        //     '',
        //     '',
        //     '',
        //     '',
        //     '',
        //     '',
        //     '',
        //     ''
        // ];        
        // $header3 = [
        //     'No',
        //     'Jenis',
        //     'Tanggal',
        //     'Jenis Transaksi',
        //     'Pengirim',
        //     'Penerima',
        //     'Bukti Transaksi',
        //     'Stok IN',
        //     'Stok OUT'
        // ]; 
        // return [
        //     $header1,
        //     $header2,
        //     $header3,
        // ];        
        return [
            'No',
            'Jenis',
            'Tanggal',
            'Jenis Transaksi',
            'Pengirim',
            'Penerima',
            'Bukti Transaksi',
            'Stok IN',
            'Stok OUT'
        ];
    }

    public function map($data): array
    {
        // set number
        static $number = 1;
        // $total = $data->baik*$data->harga_satuan2;
        $bukti_transaksi = "";
        if($data->DocNum!==""){
            if($bukti_transaksi===""){
                $bukti_transaksi .= "SAP : ".$data->DocNum;
            } else {
                $bukti_transaksi .= "\nSAP : ".$data->DocNum;
            }
        }
        if($data->no_tug3!==""){
            if($bukti_transaksi===""){
                $bukti_transaksi .= "TUG 3 : ".$data->no_tug3;
            } else {
                $bukti_transaksi .= "\nTUG 3 : ".$data->no_tug3;
            }
        }
        if($data->no_tug4!==""){
            if($bukti_transaksi===""){
                $bukti_transaksi .= "TUG 4 : ".$data->no_tug4;
            } else {
                $bukti_transaksi .= "\nTUG 4 : ".$data->no_tug4;
            }
        }
        if($data->no_tug5!==""){
            if($bukti_transaksi===""){
                $bukti_transaksi .= "TUG 5 : ".$data->no_tug5;
            } else {
                $bukti_transaksi .= "\nTUG 5 : ".$data->no_tug5;
            }
        }
        if($data->no_tug8!==""){
            if($bukti_transaksi===""){
                $bukti_transaksi .= "TUG 8 : ".$data->no_tug8;
            } else {
                $bukti_transaksi .= "\nTUG 8 : ".$data->no_tug8;
            }
        }
        if($data->no_tug9!==""){
            if($bukti_transaksi===""){
                $bukti_transaksi .= "TUG 9 : ".$data->no_tug9;
            } else {
                $bukti_transaksi .= "\nTUG 9 : ".$data->no_tug9;
            }
        }
        if($data->no_tug10!==""){
            if($bukti_transaksi===""){
                $bukti_transaksi .= "TUG 10 : ".$data->no_tug10;
            } else {
                $bukti_transaksi .= "\nTUG 10 : ".$data->no_tug10;
            }
        }
        return [
            $number++,
            $data->jenis_transaksi,
            $data->tgl_transaksi,
            $data->nama_jenis,
            $data->nama_asal,
            $data->nama_tujuan,
            $bukti_transaksi,
            $data->jumlah_masuk,
            $data->jumlah_keluar,
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
            'H' => NumberFormat::FORMAT_NUMBER,
            'I' => NumberFormat::FORMAT_NUMBER,
        ];
    }

    public function collection()
    {

        $kd_regioncari = $this->kd_regioncari3;
        $materialcari = $this->materialcari3;
        return $data = DB::table('v_history')
        ->leftJoin('jenis_transaksi','v_history.kd_jenis','=','jenis_transaksi.kd_jenis')
        ->leftJoin('teams.list_item_sap','v_history.item_no','=','list_item_sap.item_no')
        ->leftJoin('teams.v_master_gudang','v_history.kode_tujuan','=','v_master_gudang.kode_gudang')
        ->leftJoin('teams.master_vendornew','v_history.kode_asal','=','master_vendornew.CardCode')
        ->leftJoin('teams.v_master_gudang as v_master_gudang2','v_history.kode_asal','=','v_master_gudang2.kode_gudang')        
        ->selectRaw("
            v_history.*,
            jenis_transaksi.nama_jenis as nama_jenis,
            REPLACE(list_item_sap.item_description,'&#47;','/') as item_description,
            if(v_history.kd_jenis='01',master_vendornew.CardName,v_master_gudang2.nama_gudang) as nama_asal,
            v_master_gudang.nama_gudang as nama_tujuan
        ")
        ->whereRaw("substr(v_history.kode_gudang,4,2)='".$kd_regioncari."' and v_history.item_no='$materialcari' and (v_history.jumlah_masuk>0 or v_history.jumlah_keluar>0)")
        ->get();
        // dd($data);
    }
}