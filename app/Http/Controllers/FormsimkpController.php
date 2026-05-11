<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Codedge\Fpdf\Fpdf\Fpdf;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Auth;

class PDF extends FPDF
{	
	function Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link=''){
		$k=$this->k;
		if($this->y+$h>$this->PageBreakTrigger && !$this->InHeader && !$this->InFooter && $this->AcceptPageBreak()){
			$x=$this->x;
			$ws=$this->ws;
			if($ws>0){
				$this->ws=0;
				$this->_out('0 Tw');
			}
			$this->AddPage($this->CurOrientation);
			$this->x=$x;
			if($ws>0){
				$this->ws=$ws;
				$this->_out(sprintf('%.3F Tw',$ws*$k));
			}
		}
		if($w==0)
			$w=$this->w-$this->rMargin-$this->x;
		$s='';
		if($fill || $border==1){
			if($fill)
				$op=($border==1) ? 'B' : 'f';
			else
				$op='S';
			$s=sprintf('%.2F %.2F %.2F %.2F re %s ',$this->x*$k,($this->h-$this->y)*$k,$w*$k,-$h*$k,$op);
		}
		if(is_string($border)){
			$x=$this->x;
			$y=$this->y;
			if(is_int(strpos($border,'L')))
				$s.=sprintf('%.2F %.2F m %.2F %.2F l S ',$x*$k,($this->h-$y)*$k,$x*$k,($this->h-($y+$h))*$k);
			if(is_int(strpos($border,'T')))
				$s.=sprintf('%.2F %.2F m %.2F %.2F l S ',$x*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-$y)*$k);
			if(is_int(strpos($border,'R')))
				$s.=sprintf('%.2F %.2F m %.2F %.2F l S ',($x+$w)*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
			if(is_int(strpos($border,'B')))
				$s.=sprintf('%.2F %.2F m %.2F %.2F l S ',$x*$k,($this->h-($y+$h))*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
		}
		if($txt!=''){
			if($align=='R')
				$dx=$w-$this->cMargin-$this->GetStringWidth($txt);
			elseif($align=='C')
				$dx=($w-$this->GetStringWidth($txt))/2;
			elseif($align=='FJ'){
				//Set word spacing
				$wmax=($w-2*$this->cMargin);
				$this->ws=($wmax-$this->GetStringWidth($txt))/substr_count($txt,' ');
				$this->_out(sprintf('%.3F Tw',$this->ws*$this->k));
				$dx=$this->cMargin;
			}else
				$dx=$this->cMargin;
			$txt=str_replace(')','\\)',str_replace('(','\\(',str_replace('\\','\\\\',$txt)));
			if($this->ColorFlag)
				$s.='q '.$this->TextColor.' ';
			$s.=sprintf('BT %.2F %.2F Td (%s) Tj ET',($this->x+$dx)*$k,($this->h-($this->y+.5*$h+.3*$this->FontSize))*$k,$txt);
			if($this->underline)
				$s.=' '.$this->_dounderline($this->x+$dx,$this->y+.5*$h+.3*$this->FontSize,$txt);
			if($this->ColorFlag)
				$s.=' Q';
			if($link){
				if($align=='FJ')
					$wlink=$wmax;
				else
					$wlink=$this->GetStringWidth($txt);
				$this->Link($this->x+$dx,$this->y+.5*$h-.5*$this->FontSize,$wlink,$this->FontSize,$link);
			}
		}
		if($s)
			$this->_out($s);
		if($align=='FJ'){
			//Remove word spacing
			$this->_out('0 Tw');
			$this->ws=0;
		}
		$this->lasth=$h;
		if($ln>0){
			$this->y+=$h;
			if($ln==1)
				$this->x=$this->lMargin;
		}
		else
			$this->x+=$w;
	}
}

class FormsimkpController extends Controller
{
    protected $fpdf;
    protected $pdf;
 
    public function __construct()
    {
        $this->fpdf = new Fpdf;
        $pdf = new PDF();
    }

    public function index(Request $request) 
    {
        
    	// $this->fpdf->SetFont('Arial', 'B', 15);
        // $this->fpdf->AddPage("L", ['100', '100']);
        // $this->fpdf->Text(10, 10, "Hello World!");       
         
        // $this->fpdf->Output();

        // exit;
        function TanggalIndo($date){
            $BulanIndo = array("Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "Nopember", "Desember");
            $tahun = substr($date, 0, 4);
            $bulan = substr($date, 5, 2);
            $tgl   = substr($date, 8, 2);
            $result = $tgl . " " . $BulanIndo[(int)$bulan-1] . " ". $tahun;	
            return($result);
        }

        function TanggalIndo2($date){
            $tahun = substr($date, 0, 4);
            $bulan = substr($date, 5, 2);
            $tgl   = substr($date, 8, 2);
            $result = $tgl . "-" . $bulan . "-". $tahun;	
            return($result);
        }


        function penyebut($nilai) {
            $nilai = intval(abs($nilai));
            $huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
            $temp = "";
            if ($nilai < 12) {
            $temp = " ". $huruf[$nilai];
            } else if ($nilai <20) {
            $temp = penyebut($nilai - 10). " belas";
            } else if ($nilai < 100) {
            $temp = penyebut($nilai/10)." puluh". penyebut($nilai % 10);
            } else if ($nilai < 200) {
            $temp = " seratus" . penyebut($nilai - 100);
            } else if ($nilai < 1000) {
            $temp = penyebut($nilai/100) . " ratus" . penyebut($nilai % 100);
            } else if ($nilai < 2000) {
            $temp = " seribu" . penyebut($nilai - 1000);
            } else if ($nilai < 1000000) {
            $temp = penyebut($nilai/1000) . " ribu" . penyebut($nilai % 1000);
            } else if ($nilai < 1000000000) {
            $temp = penyebut($nilai/1000000) . " juta" . penyebut($nilai % 1000000);
            } else if ($nilai < 1000000000000) {
            $temp = penyebut($nilai/1000000000) . " milyar" . penyebut(fmod($nilai,1000000000));
            } else if ($nilai < 1000000000000000) {
            $temp = penyebut($nilai/1000000000000) . " trilyun" . penyebut(fmod($nilai,1000000000000));
            }     
            return $temp;
        }
        
        function terbilang($nilai) {
            if($nilai<0) {
            $hasil = "minus ". trim(penyebut($nilai));
            } else {
            $hasil = trim(penyebut($nilai));
            }     		
            return $hasil;
        }            
                
        $hari_ini = Carbon::now()->format('Y-m-d');
        $nipnya = Auth::user()->nip;
        $tahuncari = $request->tahuncari;
        $semestercari = $request->semestercari;
        $nipcari = $request->nipcari;
        if(intval($semestercari)==1){
            $semester = "Semester 1 Tahun ".$tahuncari;
        } else {
            $semester = "Semester 2 Tahun ".$tahuncari;
        }

        $pdf = new PDF('L','mm','A4');
        $pdf->SetMargins(10, 10, 10);
        $pdf->AliasNbPages();
        $pdf->SetAutoPageBreak(false);
        
        if($nipcari!="semua"){
            $rows3 = DB::table('penilaian_pegawai')->selectRaw("
                penilaian_pegawai.nip,
                b.nama as nama,
                b.jabatan as jabatan,
                b.jenjang_jabatan as jenjang_jabatan,
                b.finalisasi as finalisasi,
                c.nama_area as posisi
            ")
            ->leftJoin('data_pegawai as b','b.nip','=','penilaian_pegawai.nip')
            ->leftJoin('master_area as c','c.kd_area','=','b.kd_area')
            ->whereRaw("penilaian_pegawai.nip='$nipcari' and penilaian_pegawai.tahun='$tahuncari'")
            ->take(1)
            ->get();
        } else {
            $rows3 = DB::table('penilaian_pegawai')->selectRaw("
                penilaian_pegawai.nip,
                b.nama as nama,
                b.jabatan as jabatan,
                b.jenjang_jabatan as jenjang_jabatan,
                b.finalisasi as finalisasi,
                c.nama_area as posisi
            ")
            ->leftJoin('data_pegawai as b','b.nip','=','penilaian_pegawai.nip')
            ->leftJoin('master_area as c','c.kd_area','=','b.kd_area')
            ->whereRaw("penilaian_pegawai.tahun='$tahuncari' and penilaian_pegawai.nip not in (select nip from data_pegawai where jenis_kpi='pusat' and level_kpi<='2')")
            ->get();
        }
        foreach($rows3 as $row3){   
            $hal = 1;
            $nip = $row3->nip;
            $nama = $row3->nama;
            $jabatan = $row3->jabatan;
            $jenjang_jabatan = $row3->jenjang_jabatan;
            $posisi = $row3->posisi;
            $finalisasi = $row3->finalisasi;

            $row4 = DB::table('data_pegawai')->selectRaw("
                data_pegawai.*,
                b.nama_area as posisi
            ")
            ->leftJoin('master_area as b','b.kd_area','=','data_pegawai.kd_area')
            ->whereRaw("data_pegawai.nip='$finalisasi'")
            ->first();
            if($row4){
                $nama2 = $row4->nama;
                $jabatan2 = $row4->jabatan;
                $jenjang_jabatan2 = $row4->jenjang_jabatan;
                $posisi2 = $row4->posisi;
            } else {
                $nama2 = "";
                $jabatan2 = "";
                $jenjang_jabatan2 = "";
                $posisi2 = "";
            }

            $rows1 = DB::table('simkppcn.finalisasi_kpi')->selectRaw("
            finalisasi_kpi.*,
                b.type_target as type_target,
                b.kd_urut as kd_urut,
                b.prioritas as prioritas,
                b.polarisasi as polarisasi,
                b.type_target as type_target,
                b.satuan_kuantitas as satuan_kuantitas,
                b.satuan_kualitas as satuan_kualitas,
                b.satuan_waktu as satuan_waktu,
                b.uraian as uraian,
                b.target01 as target01,
                b.target02 as target02,
                b.target03 as target03,
                b.target04 as target04,
                b.target05 as target05,
                b.target06 as target06,
                b.target07 as target07,
                b.target08 as target08,
                b.target09 as target09,
                b.target10 as target10,
                b.target11 as target11,
                b.target12 as target12,
                c.nilaib_semester1 as nilaib_semester1,
                c.nilaib_semester2 as nilaib_semester2
            ")
            ->leftJoin('simkppcn.cascading_kpi as b','b.kode_cascading','=','finalisasi_kpi.kode_cascading')
            ->leftJoin('simkppcn.finalisasi_kpi as c','c.kode_kpi','=','finalisasi_kpi.kode_kpi')
            ->whereRaw("finalisasi_kpi.tahun='$tahuncari' and finalisasi_kpi.nip='$nip'")
            ->groupBy('finalisasi_kpi.kode_kpi','finalisasi_kpi.id')
            ->orderBy('finalisasi_kpi.kode_cascading','asc')
            ->get();
            
            // $pdf = new PDF('L','mm','A4');
            // $pdf->SetMargins(10, 10, 10);
            // $pdf->AliasNbPages();
            // $pdf->SetAutoPageBreak(false);
            $pdf->SetFillColor(0, 153, 115);
            $pdf->SetTextColor(0,0,0);
            $pdf->SetFont('Arial','',8);
            
            $pdf->AddPage();

            $pdf->SetFont('Arial','B',12);        
            $y= $pdf->GetY();
            $pdf->SetXY(15,$y);
            $pdf->MultiCell(265,8,'DATA SASARAN KINERJA PEGAWAI','','C',0);

            $y= $pdf->GetY();
            $pdf->SetXY(15,$y);
            $pdf->MultiCell(0,2,'','','L',0);

            $pdf->SetFont('Arial','B',9);
            $y= $pdf->GetY();
            $pdf->SetXY(15,$y);
            $pdf->MultiCell(120,6,'DATA PEGAWAI','LRTB','L',0);
            $pdf->SetXY(150,$y);
            $pdf->MultiCell(130,6,'DATA ATASAN PEGAWAI','LRTB','L',0);

            $pdf->SetFont('Arial','',9);
            $y= $pdf->GetY();
            $pdf->SetXY(15,$y);
            $pdf->MultiCell(40,1,'','LR','L',0);
            $pdf->SetXY(55,$y);
            $pdf->MultiCell(80,1,'','R','L',0);
            $pdf->SetXY(150,$y);
            $pdf->MultiCell(40,1,'','LR','L',0);
            $pdf->SetXY(190,$y);
            $pdf->MultiCell(90,1,'','R','L',0);
            $y= $pdf->GetY();
            $pdf->SetXY(15,$y);
            $pdf->MultiCell(40,3,'Nama','LR','L',0);
            $x1 = $pdf->GetY();
            $tinggi1 = $x1-$y;
            $pdf->SetXY(55,$y);
            $pdf->MultiCell(80,3,$nama,'R','L',0);
            $x2 = $pdf->GetY();
            $tinggi2 = $x2-$y;
            $pdf->SetXY(150,$y);
            $pdf->MultiCell(40,3,'Nama','LR','L',0);
            $x3 = $pdf->GetY();
            $tinggi3 = $x3-$y;
            $pdf->SetXY(190,$y);
            $pdf->MultiCell(90,3,$nama2,'R','L',0);
            $x4 = $pdf->GetY();
            $tinggi4 = $x4-$y;
            $tinggi = max($tinggi1,$tinggi2,$tinggi3,$tinggi4);
            $selisih1 = $tinggi-$tinggi1+1;
            $selisih2 = $tinggi-$tinggi2+1;
            $selisih3 = $tinggi-$tinggi3+1;
            $selisih4 = $tinggi-$tinggi4+1;
            $y= $pdf->GetY();
            $pdf->SetXY(15,$x1);
            $pdf->MultiCell(40,$selisih1,'','LRB','L',0);
            $pdf->SetXY(55,$x2);
            $pdf->MultiCell(80,$selisih2,'','RB','L',0);
            $pdf->SetXY(150,$x3);
            $pdf->MultiCell(40,$selisih3,'','LRB','L',0);
            $pdf->SetXY(190,$x4);
            $pdf->MultiCell(90,$selisih4,'','RB','L',0);

            $y= $pdf->GetY();
            $pdf->SetXY(15,$y);
            $pdf->MultiCell(40,1,'','LR','L',0);
            $pdf->SetXY(55,$y);
            $pdf->MultiCell(80,1,'','R','L',0);
            $pdf->SetXY(150,$y);
            $pdf->MultiCell(40,1,'','LR','L',0);
            $pdf->SetXY(190,$y);
            $pdf->MultiCell(90,1,'','R','L',0);
            $y= $pdf->GetY();
            $pdf->SetXY(15,$y);
            $pdf->MultiCell(40,3,'Jabatan','LR','L',0);
            $x1 = $pdf->GetY();
            $tinggi1 = $x1-$y;
            $pdf->SetXY(55,$y);
            $pdf->MultiCell(80,3,$jabatan,'R','L',0);
            $x2 = $pdf->GetY();
            $tinggi2 = $x2-$y;
            $pdf->SetXY(150,$y);
            $pdf->MultiCell(40,3,'Jabatan','LR','L',0);
            $x3 = $pdf->GetY();
            $tinggi3 = $x3-$y;
            $pdf->SetXY(190,$y);
            $pdf->MultiCell(90,3,$jabatan2,'R','L',0);
            $x4 = $pdf->GetY();
            $tinggi4 = $x4-$y;
            $tinggi = max($tinggi1,$tinggi2,$tinggi3,$tinggi4);
            $selisih1 = $tinggi-$tinggi1+1;
            $selisih2 = $tinggi-$tinggi2+1;
            $selisih3 = $tinggi-$tinggi3+1;
            $selisih4 = $tinggi-$tinggi4+1;
            $y= $pdf->GetY();
            $pdf->SetXY(15,$x1);
            $pdf->MultiCell(40,$selisih1,'','LRB','L',0);
            $pdf->SetXY(55,$x2);
            $pdf->MultiCell(80,$selisih2,'','RB','L',0);
            $pdf->SetXY(150,$x3);
            $pdf->MultiCell(40,$selisih3,'','LRB','L',0);
            $pdf->SetXY(190,$x4);
            $pdf->MultiCell(90,$selisih4,'','RB','L',0);

            $y= $pdf->GetY();
            $pdf->SetXY(15,$y);
            $pdf->MultiCell(40,1,'','LR','L',0);
            $pdf->SetXY(55,$y);
            $pdf->MultiCell(80,1,'','R','L',0);
            $pdf->SetXY(150,$y);
            $pdf->MultiCell(40,1,'','LR','L',0);
            $pdf->SetXY(190,$y);
            $pdf->MultiCell(90,1,'','R','L',0);
            $y= $pdf->GetY();
            $pdf->SetXY(15,$y);
            $pdf->MultiCell(40,3,'Jenjang Jabatan','LR','L',0);
            $x1 = $pdf->GetY();
            $tinggi1 = $x1-$y;
            $pdf->SetXY(55,$y);
            $pdf->MultiCell(80,3,$jenjang_jabatan,'R','L',0);
            $x2 = $pdf->GetY();
            $tinggi2 = $x2-$y;
            $pdf->SetXY(150,$y);
            $pdf->MultiCell(40,3,'Jenjang Jabatan','LR','L',0);
            $x3 = $pdf->GetY();
            $tinggi3 = $x3-$y;
            $pdf->SetXY(190,$y);
            $pdf->MultiCell(90,3,$jenjang_jabatan2,'R','L',0);
            $x4 = $pdf->GetY();
            $tinggi4 = $x4-$y;
            $tinggi = max($tinggi1,$tinggi2,$tinggi3,$tinggi4);
            $selisih1 = $tinggi-$tinggi1+1;
            $selisih2 = $tinggi-$tinggi2+1;
            $selisih3 = $tinggi-$tinggi3+1;
            $selisih4 = $tinggi-$tinggi4+1;
            $y= $pdf->GetY();
            $pdf->SetXY(15,$x1);
            $pdf->MultiCell(40,$selisih1,'','LRB','L',0);
            $pdf->SetXY(55,$x2);
            $pdf->MultiCell(80,$selisih2,'','RB','L',0);
            $pdf->SetXY(150,$x3);
            $pdf->MultiCell(40,$selisih3,'','LRB','L',0);
            $pdf->SetXY(190,$x4);
            $pdf->MultiCell(90,$selisih4,'','RB','L',0);

            $y= $pdf->GetY();
            $pdf->SetXY(15,$y);
            $pdf->MultiCell(40,1,'','LR','L',0);
            $pdf->SetXY(55,$y);
            $pdf->MultiCell(80,1,'','R','L',0);
            $pdf->SetXY(150,$y);
            $pdf->MultiCell(40,1,'','LR','L',0);
            $pdf->SetXY(190,$y);
            $pdf->MultiCell(90,1,'','R','L',0);
            $y= $pdf->GetY();
            $pdf->SetXY(15,$y);
            $pdf->MultiCell(40,3,'Posisi','LR','L',0);
            $x1 = $pdf->GetY();
            $tinggi1 = $x1-$y;
            $pdf->SetXY(55,$y);
            $pdf->MultiCell(80,3,$posisi,'R','L',0);
            $x2 = $pdf->GetY();
            $tinggi2 = $x2-$y;
            $pdf->SetXY(150,$y);
            $pdf->MultiCell(40,3,'Posisi','LR','L',0);
            $x3 = $pdf->GetY();
            $tinggi3 = $x3-$y;
            $pdf->SetXY(190,$y);
            $pdf->MultiCell(90,3,$posisi2,'R','L',0);
            $x4 = $pdf->GetY();
            $tinggi4 = $x4-$y;
            $tinggi = max($tinggi1,$tinggi2,$tinggi3,$tinggi4);
            $selisih1 = $tinggi-$tinggi1+1;
            $selisih2 = $tinggi-$tinggi2+1;
            $selisih3 = $tinggi-$tinggi3+1;
            $selisih4 = $tinggi-$tinggi4+1;
            $y= $pdf->GetY();
            $pdf->SetXY(15,$x1);
            $pdf->MultiCell(40,$selisih1,'','LRB','L',0);
            $pdf->SetXY(55,$x2);
            $pdf->MultiCell(80,$selisih2,'','RB','L',0);
            $pdf->SetXY(150,$x3);
            $pdf->MultiCell(40,$selisih3,'','LRB','L',0);
            $pdf->SetXY(190,$x4);
            $pdf->MultiCell(90,$selisih4,'','RB','L',0);

            $pdf->SetFont('Arial','B',9);
            $y= $pdf->GetY();
            $pdf->SetXY(15,$y);
            $pdf->MultiCell(265,6,'SASARAN KINERJA - '.strtoupper($semester),'','L',0);
            
            $pdf->SetFont('Arial','',8);        
            $y= $pdf->GetY();
            $pdf->SetXY(15,$y);
            $pdf->MultiCell(10,12,'NO','LRTB','C',1);
            $pdf->SetXY(25,$y);
            $pdf->MultiCell(70,12,'KEY PERFORMANCE INDICATOR | POLARISASI','RTB','L',1);
            $pdf->SetXY(95,$y);
            $pdf->MultiCell(93,12,'PENCAPAIAN BULANAN','RTB','C',1);
            $pdf->SetXY(188,$y);
            $pdf->MultiCell(15,6,'KUAN','RT','C',1);
            $pdf->SetXY(188,$y+6);
            $pdf->MultiCell(15,6,'TITAS','RB','C',1);
            $pdf->SetXY(203,$y);
            $pdf->MultiCell(15,6,'KUAL','RT','C',1);
            $pdf->SetXY(203,$y+6);
            $pdf->MultiCell(15,6,'ITAS','RB','C',1);
            $pdf->SetXY(218,$y);
            $pdf->MultiCell(15,12,'WAKTU','RTB','C',1);
            $pdf->SetXY(233,$y);
            $pdf->MultiCell(47,4,'IDE TEROBOSAN PROSES PENCAPAIAN KPI SUMBER INFORMASI','RTB','C',1);
            // $pdf->MultiCell(47,8,'IDE TEROBOSAN','RTB','C',1);
            $no=1;
            foreach ($rows1 as $row1) {            
                $uraian = $row1->uraian;
                $satuan_kuantitas = $row1->satuan_kuantitas;
                $satuan_kualitas = $row1->satuan_kualitas;
                $satuan_waktu = $row1->satuan_waktu;
                $polarisasi = $row1->polarisasi;
                $prioritas = $row1->prioritas;
                $type_target = $row1->type_target;
                $target01 = $row1->target01;
                $target02 = $row1->target02;
                $target03 = $row1->target03;
                $target04 = $row1->target04;
                $target05 = $row1->target05;
                $target06 = $row1->target06;
                $target07 = $row1->target07;
                $target08 = $row1->target08;
                $target09 = $row1->target09;
                $target10 = $row1->target10;
                $target11 = $row1->target11;
                $target12 = $row1->target12;
                $realisasi01b = $row1->realisasi01b;
                $realisasi02b = $row1->realisasi02b;
                $realisasi03b = $row1->realisasi03b;
                $realisasi04b = $row1->realisasi04b;
                $realisasi05b = $row1->realisasi05b;
                $realisasi06b = $row1->realisasi06b;
                $realisasi07b = $row1->realisasi07b;
                $realisasi08b = $row1->realisasi08b;
                $realisasi09b = $row1->realisasi09b;
                $realisasi10b = $row1->realisasi10b;
                $realisasi11b = $row1->realisasi11b;
                $realisasi12b = $row1->realisasi12b;
                $nilaib_semester1 = $row1->nilaib_semester1;
                $nilaib_semester2 = $row1->nilaib_semester2;
                if(intval($semestercari)==1){
                    $nilai_target01 = $target01;
                    $nilai_target02 = $target02;
                    $nilai_target03 = $target03;
                    $nilai_target04 = $target04;
                    $nilai_target05 = $target05;
                    $nilai_target06 = $target06;
                    $nilai_realisasi01 = $realisasi01b;
                    $nilai_realisasi02 = $realisasi02b;
                    $nilai_realisasi03 = $realisasi03b;
                    $nilai_realisasi04 = $realisasi04b;
                    $nilai_realisasi05 = $realisasi05b;
                    $nilai_realisasi06 = $realisasi06b;
                    $nilai_semester = $row1->nilaib_semester1;
                } else {
                    $nilai_target01 = $target07;
                    $nilai_target02 = $target08;
                    $nilai_target03 = $target09;
                    $nilai_target04 = $target10;
                    $nilai_target05 = $target11;
                    $nilai_target06 = $target12;
                    $nilai_realisasi01 = $realisasi07b;
                    $nilai_realisasi02 = $realisasi08b;
                    $nilai_realisasi03 = $realisasi09b;
                    $nilai_realisasi04 = $realisasi10b;
                    $nilai_realisasi05 = $realisasi11b;
                    $nilai_realisasi06 = $realisasi12b;
                    $nilai_semester = $row1->nilaib_semester2;
                }
                
                $batas_akhir= $pdf->GetY();
                if($batas_akhir>160){
                    $pdf->SetFont('Arial','I',6);
                    $pdf->SetXY(15,-12);
                    $pdf->MultiCell(0, 4,$nip.' - '.$nama,'','L',0); 
                    $pdf->SetXY(110,-12);
                    // $pdf->MultiCell(0, 4,'Halaman '.$pdf->PageNo().' / {nb}','','R',0); 
                    $pdf->MultiCell(0, 4,'Halaman '.$hal,'','R',0); 

                    $pdf->AddPage();
                    $hal++;
                    $pdf->SetFillColor(0, 153, 115);
                    $pdf->SetFont('Arial','',8); 
                    
                    $y= $pdf->GetY();
                    $pdf->SetXY(15,$y);
                    $pdf->MultiCell(10,12,'NO','LRTB','C',1);
                    $pdf->SetXY(25,$y);
                    $pdf->MultiCell(70,12,'KEY PERFORMANCE INDICATOR | POLARISASI','RTB','L',1);
                    $pdf->SetXY(95,$y);
                    $pdf->MultiCell(93,12,'PENCAPAIAN BULANAN','RTB','C',1);
                    $pdf->SetXY(188,$y);
                    $pdf->MultiCell(15,6,'KUAN','RT','C',1);
                    $pdf->SetXY(188,$y+6);
                    $pdf->MultiCell(15,6,'TITAS','RB','C',1);
                    $pdf->SetXY(203,$y);
                    $pdf->MultiCell(15,6,'KUAL','RT','C',1);
                    $pdf->SetXY(203,$y+6);
                    $pdf->MultiCell(15,6,'ITAS','RB','C',1);
                    $pdf->SetXY(218,$y);
                    $pdf->MultiCell(15,12,'WAKTU','RTB','C',1);
                    $pdf->SetXY(233,$y);
                    $pdf->MultiCell(47,4,'IDE TEROBOSAN PROSES PENCAPAIAN KPI SUMBER INFORMASI','RTB','C',1);

                }
                $pdf->SetFillColor(179, 204, 255);
                $pdf->SetFont('Arial','',7.5);
                $y= $pdf->GetY();
                $pdf->SetXY(15,$y);
                $pdf->MultiCell(10,4,$no,'LR','C',0);
                $x1 = $pdf->GetY();
                $tinggi1 = $x1-$y;
                $pdf->SetFont('Arial','B',7.5);
                $pdf->SetXY(25,$y);
                $pdf->MultiCell(70,4,$uraian,'R','L',0);
                $pdf->SetFont('Arial','',7.5);
                $x2 = $pdf->GetY();
                $tinggi2 = $x2-$y;
                $pdf->SetXY(95,$y);
                $pdf->MultiCell(15,4,'','R','L',1);
                $x3 = $pdf->GetY();
                $tinggi3 = $x3-$y;
                $pdf->SetXY(110,$y);
                $pdf->MultiCell(13,4,'1','R','C',1);
                $x4 = $pdf->GetY();
                $tinggi4 = $x4-$y;
                $pdf->SetXY(123,$y);
                $pdf->MultiCell(13,4,'2','R','C',1);
                $x5 = $pdf->GetY();
                $tinggi5 = $x5-$y;
                $pdf->SetXY(136,$y);
                $pdf->MultiCell(13,4,'3','R','C',1);
                $x6 = $pdf->GetY();
                $tinggi6 = $x6-$y;
                $pdf->SetXY(149,$y);
                $pdf->MultiCell(13,4,'4','R','C',1);
                $x7 = $pdf->GetY();
                $tinggi7 = $x7-$y;
                $pdf->SetXY(162,$y);
                $pdf->MultiCell(13,4,'5','R','C',1);
                $x8 = $pdf->GetY();
                $tinggi8 = $x8-$y;
                $pdf->SetXY(175,$y);
                $pdf->MultiCell(13,4,'6','R','C',1);
                $x9 = $pdf->GetY();
                $tinggi9 = $x9-$y;
                $pdf->SetXY(188,$y);
                $pdf->MultiCell(15,4,'100%','R','C',1);
                $x10 = $pdf->GetY();
                $tinggi10 = $x10-$y;
                $pdf->SetXY(203,$y);
                $pdf->MultiCell(15,4,'100%','R','C',1);
                $x11 = $pdf->GetY();
                $tinggi11 = $x11-$y;
                $pdf->SetXY(218,$y);
                $pdf->MultiCell(15,4,'100%','R','C',1);
                $x12 = $pdf->GetY();
                $tinggi12 = $x12-$y;
                $pdf->SetXY(233,$y);
                $pdf->MultiCell(47,4,'','R','L',0);
                $x13 = $pdf->GetY();
                $tinggi13 = $x13-$y;

                $tinggi = max($tinggi1,$tinggi2,$tinggi3,$tinggi4,$tinggi5,$tinggi6,$tinggi7,$tinggi8,$tinggi9,$tinggi10,$tinggi11,$tinggi12,$tinggi13);
                $selisih1 = $tinggi-$tinggi1;
                $selisih2 = $tinggi-$tinggi2;
                $selisih3 = $tinggi-$tinggi3;
                $selisih4 = $tinggi-$tinggi4;
                $selisih5 = $tinggi-$tinggi5;
                $selisih6 = $tinggi-$tinggi6;
                $selisih7 = $tinggi-$tinggi7;
                $selisih8 = $tinggi-$tinggi8;
                $selisih9 = $tinggi-$tinggi9;
                $selisih10 = $tinggi-$tinggi10;
                $selisih11 = $tinggi-$tinggi11;
                $selisih12 = $tinggi-$tinggi12;
                $selisih13 = $tinggi-$tinggi13;

                $pdf->SetXY(15,$x1);
                $pdf->MultiCell(10,$selisih1,'','LR','C',0);
                $pdf->SetXY(25,$x2);
                $pdf->MultiCell(70,$selisih2,'','RB','L',0);
                $pdf->SetXY(95,$x3);
                $pdf->MultiCell(15,$selisih3,'','RB','C',1);
                $pdf->SetXY(110,$x4);
                $pdf->MultiCell(13,$selisih4,'','RB','L',1);
                $pdf->SetXY(123,$x5);
                $pdf->MultiCell(13,$selisih5,'','RB','C',1);
                $pdf->SetXY(136,$x6);
                $pdf->MultiCell(13,$selisih6,'','RB','C',1);
                $pdf->SetXY(149,$x7);
                $pdf->MultiCell(13,$selisih7,'','RB','L',1);
                $pdf->SetXY(162,$x8);
                $pdf->MultiCell(13,$selisih8,'','RB','C',1);
                $pdf->SetXY(175,$x9);
                $pdf->MultiCell(13,$selisih9,'','RB','R',1);
                $pdf->SetXY(188,$x10);
                $pdf->MultiCell(15,$selisih10,'','RB','R',1);
                $pdf->SetXY(203,$x11);
                $pdf->MultiCell(15,$selisih11,'','RB','R',1);
                $pdf->SetXY(218,$x12);
                $pdf->MultiCell(15,$selisih12,'','RB','R',1);
                $pdf->SetXY(233,$x13);
                $pdf->MultiCell(47,$selisih13,'','R','R',0);

                $pdf->SetFont('Arial','',7.5);
                $y= $pdf->GetY();
                $pdf->SetXY(15,$y);
                $pdf->MultiCell(10,4,'','LR','C',0);
                $pdf->SetXY(25,$y);
                $pdf->MultiCell(30,4,'Satuan Kuantitas','RB','L',0);
                $pdf->SetXY(55,$y);
                $pdf->MultiCell(20,4,': '.$satuan_kuantitas,'RB','L',0);
                $pdf->SetXY(75,$y);
                $pdf->MultiCell(20,4,$polarisasi,'RB','L',0);
                $pdf->SetXY(95,$y);
                $pdf->MultiCell(15,4,'Target','RB','L',0);
                $pdf->SetXY(110,$y);
                $pdf->MultiCell(13,4,$nilai_target01,'RB','C',0);
                $pdf->SetXY(123,$y);
                $pdf->MultiCell(13,4,$nilai_target02,'RB','C',0);
                $pdf->SetXY(136,$y);
                $pdf->MultiCell(13,4,$nilai_target03,'RB','C',0);
                $pdf->SetXY(149,$y);
                $pdf->MultiCell(13,4,$nilai_target04,'RB','C',0);
                $pdf->SetXY(162,$y);
                $pdf->MultiCell(13,4,$nilai_target05,'RB','C',0);
                $pdf->SetXY(175,$y);
                $pdf->MultiCell(13,4,$nilai_target06,'RB','C',0);
                $pdf->SetXY(188,$y);
                $pdf->MultiCell(15,4,'','RB','C',0);
                $pdf->SetXY(203,$y);
                $pdf->MultiCell(15,4,'','RB','C',0);
                $pdf->SetXY(218,$y);
                $pdf->MultiCell(15,4,'','RB','C',0);
                $pdf->SetXY(233,$y);
                $pdf->MultiCell(47,4,'','R','C',0);
                
                $pdf->SetFillColor(179, 236, 255);
                $pdf->SetFont('Arial','',7.5);
                $y= $pdf->GetY();
                $pdf->SetXY(15,$y);
                $pdf->MultiCell(10,4,'','LR','C',0);
                $pdf->SetXY(25,$y);
                $pdf->MultiCell(30,4,'Satuan Kualitas','RB','L',0);
                $pdf->SetXY(55,$y);
                $pdf->MultiCell(20,4,': '.$satuan_kualitas,'RB','L',0);
                $pdf->SetXY(75,$y);
                $pdf->MultiCell(20,4,$polarisasi,'RB','L',0);
                $pdf->SetXY(95,$y);
                $pdf->MultiCell(15,4,'Realisasi','RB','L',1);
                $pdf->SetXY(110,$y);
                $pdf->MultiCell(13,4,$nilai_realisasi01,'RB','C',1);
                $pdf->SetXY(123,$y);
                $pdf->MultiCell(13,4,$nilai_realisasi02,'RB','C',1);
                $pdf->SetXY(136,$y);
                $pdf->MultiCell(13,4,$nilai_realisasi03,'RB','C',1);
                $pdf->SetXY(149,$y);
                $pdf->MultiCell(13,4,$nilai_realisasi04,'RB','C',1);
                $pdf->SetXY(162,$y);
                $pdf->MultiCell(13,4,$nilai_realisasi05,'RB','C',1);
                $pdf->SetXY(175,$y);
                $pdf->MultiCell(13,4,$nilai_realisasi06,'RB','C',1);
                $pdf->SetXY(188,$y);
                $pdf->MultiCell(15,4,'','RB','C',1);
                $pdf->SetXY(203,$y);
                $pdf->MultiCell(15,4,'','RB','C',1);
                $pdf->SetXY(218,$y);
                $pdf->MultiCell(15,4,'','RB','C',1);
                $pdf->SetXY(233,$y);
                $pdf->MultiCell(47,4,'','R','C',0);

                $pdf->SetFont('Arial','',7.5);
                $y= $pdf->GetY();
                $pdf->SetXY(15,$y);
                $pdf->MultiCell(10,4,'','LR','C',0);
                $pdf->SetXY(25,$y);
                $pdf->MultiCell(30,4,'Satuan Waktu','RB','L',0);
                $pdf->SetXY(55,$y);
                $pdf->MultiCell(20,4,': '.$satuan_waktu,'RB','L',0);
                $pdf->SetXY(75,$y);
                $pdf->MultiCell(20,4,$polarisasi,'RB','L',0);
                $pdf->SetXY(95,$y);
                $pdf->MultiCell(15,4,'','RB','L',0);
                $pdf->SetXY(110,$y);
                $pdf->MultiCell(13,4,'','RB','C',0);
                $pdf->SetXY(123,$y);
                $pdf->MultiCell(13,4,'','RB','C',0);
                $pdf->SetXY(136,$y);
                $pdf->MultiCell(13,4,'','RB','C',0);
                $pdf->SetXY(149,$y);
                $pdf->MultiCell(13,4,'','RB','C',0);
                $pdf->SetXY(162,$y);
                $pdf->MultiCell(13,4,'','RB','C',0);
                $pdf->SetXY(175,$y);
                $pdf->MultiCell(13,4,'','RB','C',0);
                $pdf->SetXY(188,$y);
                $pdf->MultiCell(15,4,'','RB','C',0);
                $pdf->SetXY(203,$y);
                $pdf->MultiCell(15,4,'','RB','C',0);
                $pdf->SetXY(218,$y);
                $pdf->MultiCell(15,4,'','RB','C',0);
                $pdf->SetXY(233,$y);
                $pdf->MultiCell(47,4,'','R','C',0);
                
                $pdf->SetFillColor(230, 230, 0);
                $pdf->SetFont('Arial','',7.5);
                $y= $pdf->GetY();
                $pdf->SetXY(15,$y);
                $pdf->MultiCell(10,4,'','LR','C',0);
                $pdf->SetXY(25,$y);
                $pdf->MultiCell(30,4,'Prioritas','RB','L',0);
                $pdf->SetXY(55,$y);
                $pdf->MultiCell(20,4,': Prioritas '.$prioritas,'RB','L',0);
                $pdf->SetXY(75,$y);
                $pdf->MultiCell(20,4,'','RB','L',0);
                $pdf->SetXY(95,$y);
                $pdf->MultiCell(80,4,'PROSENTASE PENCAPAIAN','RB','L',1);
                $pdf->SetXY(175,$y);
                $pdf->MultiCell(13,4,$nilai_semester,'RB','C',1);
                $pdf->SetXY(188,$y);
                $pdf->MultiCell(15,4,'','RB','C',1);
                $pdf->SetXY(203,$y);
                $pdf->MultiCell(15,4,'','RB','C',1);
                $pdf->SetXY(218,$y);
                $pdf->MultiCell(15,4,'','RB','C',1);
                $pdf->SetXY(233,$y);
                $pdf->MultiCell(47,4,'','R','C',0);
                $pdf->SetFillColor(179, 204, 255);

                $pdf->SetFont('Arial','',7.5);
                $y= $pdf->GetY();
                $pdf->SetXY(15,$y);
                $pdf->MultiCell(10,4,'','LRB','C',0);
                $pdf->SetXY(25,$y);
                $pdf->MultiCell(30,4,'Type Target','RB','L',0);
                $pdf->SetXY(55,$y);
                $pdf->MultiCell(40,4,': '.$type_target,'RB','L',0);
                $pdf->SetXY(95,$y);
                $pdf->MultiCell(15,4,'','RB','L',0);
                $pdf->SetXY(110,$y);
                $pdf->MultiCell(13,4,'','RB','C',0);
                $pdf->SetXY(123,$y);
                $pdf->MultiCell(13,4,'','RB','C',0);
                $pdf->SetXY(136,$y);
                $pdf->MultiCell(13,4,'','RB','C',0);
                $pdf->SetXY(149,$y);
                $pdf->MultiCell(13,4,'','RB','C',0);
                $pdf->SetXY(162,$y);
                $pdf->MultiCell(13,4,'','RB','C',0);
                $pdf->SetXY(175,$y);
                $pdf->MultiCell(13,4,'','RB','C',0);
                $pdf->SetXY(188,$y);
                $pdf->MultiCell(15,4,'','RB','C',0);
                $pdf->SetXY(203,$y);
                $pdf->MultiCell(15,4,'','RB','C',0);
                $pdf->SetXY(218,$y);
                $pdf->MultiCell(15,4,'','RB','C',0);
                $pdf->SetXY(233,$y);
                $pdf->MultiCell(47,4,'','RB','C',0);

                $no++;
            }

            // $batas_bawah= $pdf->GetY()-5;
            // $tinggi_box = $batas_atas-$batas_bawah;
            // $pdf->Rect(10, $batas_atas, 275, $batas_bawah, 'S');  
            $batas_akhir= $pdf->GetY();
            if($batas_akhir>150){
                $pdf->SetFont('Arial','I',6);
                $pdf->SetXY(15,-12);
                $pdf->MultiCell(0, 4,$nip.' - '.$nama,'','L',0); 
                $pdf->SetXY(110,-12);
                // $pdf->MultiCell(0, 4,'Halaman '.$pdf->PageNo().' / {nb}','','R',0); 
                $pdf->MultiCell(0, 4,'Halaman '.$hal,'','R',0); 

                $pdf->AddPage();
                $hal++;
                $pdf->SetFillColor(0, 153, 115);
                $pdf->SetFont('Arial','',8); 
                
                $y= $pdf->GetY();
                $pdf->SetXY(15,$y);
                $pdf->MultiCell(10,12,'NO','LRTB','C',1);
                $pdf->SetXY(25,$y);
                $pdf->MultiCell(70,12,'KEY PERFORMANCE INDICATOR | POLARISASI','RTB','L',1);
                $pdf->SetXY(95,$y);
                $pdf->MultiCell(93,12,'PENCAPAIAN BULANAN','RTB','C',1);
                $pdf->SetXY(188,$y);
                $pdf->MultiCell(15,6,'KUAN','RT','C',1);
                $pdf->SetXY(188,$y+6);
                $pdf->MultiCell(15,6,'TITAS','RB','C',1);
                $pdf->SetXY(203,$y);
                $pdf->MultiCell(15,6,'KUAL','RT','C',1);
                $pdf->SetXY(203,$y+6);
                $pdf->MultiCell(15,6,'ITAS','RB','C',1);
                $pdf->SetXY(218,$y);
                $pdf->MultiCell(15,12,'WAKTU','RTB','C',1);
                $pdf->SetXY(233,$y);
                $pdf->MultiCell(47,4,'IDE TEROBOSAN PROSES PENCAPAIAN KPI SUMBER INFORMASI','RTB','C',1);
            }        

            $pdf->SetFont('Arial','',9);
            $y= $pdf->GetY();
            $pdf->SetXY(15,$y);
            $pdf->MultiCell(0,2,'','','L',0);

            $y= $pdf->GetY();
            $pdf->SetXY(15,$y);
            $pdf->MultiCell(0,4,'Dengan ini menyatakan dengan sebenar-benarnya bahwa data informasi realisasi pencapaian kinerja untuk '.$semester.' adalah merupakan hasil pencapaian yang sesungguhnya  dan dapat dipertanggung . Demikian pernyataan ini dibuat dengan sebenarnya dan telah dikomunikasikan dengan Atasan, serta bersedia menerima sanksi dan penyesuaian sesuai ketentuan yang berlaku apabila ternyata pernyataan saya.','','J',0);

            $y= $pdf->GetY();
            $pdf->SetXY(15,$y);
            $pdf->MultiCell(0,2,'','','L',0);

            $y= $pdf->GetY();
            $pdf->SetXY(35,$y);
            $pdf->MultiCell(90,4,'Balikpapan, '.TanggalIndo($hari_ini),'','L',0);

            $y= $pdf->GetY();
            $pdf->SetXY(35,$y);
            $pdf->MultiCell(70,4,$jabatan,'','L',0);
            $x1 = $pdf->GetY();
            $tinggi1 = $x1-$y;
            $pdf->SetXY(200,$y);
            $pdf->MultiCell(70,4,$jabatan2,'','L',0);
            $x2 = $pdf->GetY();
            $tinggi2 = $x2-$y;

            $tinggi = max($tinggi1,$tinggi2);
            $selisih1 = $tinggi-$tinggi1;
            $selisih2 = $tinggi-$tinggi2;

            $y= $pdf->GetY();
            $pdf->SetXY(35,$x1);
            $pdf->MultiCell(70,$selisih1,'','','L',0);
            $pdf->SetXY(200,$x2);
            $pdf->MultiCell(70,$selisih2,'','','L',0);

            $y= $pdf->GetY();
            $pdf->SetXY(15,$y);
            $pdf->MultiCell(0,14,'','','L',0);

            $pdf->SetFont('Arial','B',9);
            $y= $pdf->GetY();
            $pdf->SetXY(35,$y);
            $pdf->MultiCell(70,4,$nama,'','L',0);
            $pdf->SetXY(200,$y);
            $pdf->MultiCell(70,4,$nama2,'','L',0);


            /*Batas Atas*/   
            $pdf->SetFont('Arial','I',6);
            $pdf->SetXY(15,-12);
            $pdf->MultiCell(0, 4,$nip.' - '.$nama,'','L',0); 
            $pdf->SetXY(110,-12);
            // $pdf->MultiCell(0, 4,'Halaman '.$pdf->PageNo().' / {nb}','','R',0); 
            $pdf->MultiCell(0, 4,'Halaman '.$hal,'','R',0); 
            /*Batas Bawah*/   
    
            // $pdf->Output();
            // exit;

            
        }
        $pdf->Output();
        exit;

    }
    
}
