<?php

namespace App\Exports;

use App\Models\Monitoring\Stoknew;
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

class StoknewExport implements FromCollection, WithColumnFormatting, WithHeadings, WithMapping, ShouldAutoSize, WithColumnWidths, WithEvents
{
    use Exportable;
    public function  __construct($kd_regioncarinya,$kd_areacarinya)
    {
        $this->kd_regioncarinya = $kd_regioncarinya;
        $this->kd_areacarinya = $kd_areacarinya;
        // $this->tanggalcarinya = $tanggalcarinya;
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
            'D' => 15,
            'E' => 20,
        ];
    }
    public function headings(): array
    {
        return [
            'No',
            'Nama Region',
            'Gudang UL/Site',
            'Jumlah Stok',
            'Jumlah harga'
        ];
    }

    public function map($data): array
    {
        // set number
        static $number = 1;
        // $jumlah_stok = intval($data->jumlah_stok)-intval($data->jumlah_penerimaan)+intval($data->jumlah_pengeluaran);
        // $jumlah_stok = intval($data->jumlah_stok)." ".intval($data->jumlah_penerimaan)." ".intval($data->jumlah_pengeluaran);
        return [
            $number++,
            $data->nama_region,
            $data->nama_area,
            $data->jumlah_stok,
            $data->harga_stok,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_NUMBER,
            'B' => NumberFormat::FORMAT_TEXT,
            'C' => NumberFormat::FORMAT_TEXT,
            'D' => NumberFormat::FORMAT_NUMBER,
            'E' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }

    public function collection()
    {

        $kd_regioncari = $this->kd_regioncarinya;
        $kd_areacari = $this->kd_areacarinya;
        // $tanggalcari2 = $this->tanggalcarinya;
        // $datanya2 = explode("/",$tanggalcari2);
        // $hari = $datanya2[0];
        // $bulan = $datanya2[1];
        // $tahun = $datanya2[2];
        // $tanggalcari = $tahun."-".$bulan."-".$hari;
        // dd($tanggalcari);
        // $tanggalcari = "2024-12-01";

        $perintah = "";
        if($kd_regioncari!="semua"){
            $perintah .= "v_stok_new.kd_region='$kd_regioncari'";
        }
        if($kd_areacari!="semua"){
            if($perintah==""){
                $perintah .= "v_stok_new.kd_area='$kd_areacari'";
            } else {
                $perintah .= " and v_stok_new.kd_area='$kd_areacari'";
            }
        }
        if($perintah==""){
            return $data = DB::table('teams.master_area')
            ->leftjoin('teams.master_region', 'master_region.kd_region', '=', 'master_area.kd_region')
            ->leftjoin('gudang.v_stok_new', 'v_stok_new.kd_area', '=', 'master_area.kd_area')
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
                master_area.kd_region as kd_region,
                master_region.nama_region as nama_region,
                master_area.kd_area as kd_area,
                master_area.nama_area as nama_area,
                ifnull(sum(v_stok_new.baik),0) as jumlah_stok,
                ifnull(sum(v_stok_new.jumlah_harga),0) as harga_stok
            ")
            // ->whereRaw('master_kontrak.status', '=', '1')
            // ->whereRaw($perintah)
            ->groupBy('master_area.kd_region','master_area.kd_area')
            ->orderBy('master_area.kd_region','asc')
            ->orderBy('master_area.kd_area','asc')
            ->get();
        } else {
            return $data = DB::table('v_stok_new')
            ->leftjoin('teams.master_region', 'master_region.kd_region', '=', 'v_stok_new.kd_region')
            ->leftjoin('teams.master_area', 'master_area.kd_area', '=', 'v_stok_new.kd_area')
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
                v_stok_new.kd_region as kd_region,
                master_region.nama_region as nama_region,
                v_stok_new.kd_area as kd_area,
                master_area.nama_area as nama_area,
                sum(v_stok_new.baik) as jumlah_stok,
                sum(v_stok_new.jumlah_harga) as harga_stok
            ")
            // ->whereRaw('master_kontrak.status', '=', '1')
            ->whereRaw($perintah)
            // ->groupBy('v_stok_new.kd_region','v_stok_new.kd_area')
            ->groupBy('v_stok_new.kode_gudang')
            ->get();
        }
        // dd($data);
    }
}