<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Codedge\Fpdf\Fpdf\Fpdf;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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

class CetaktalentamController extends Controller
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
        $hari_ini = date("Y-m-d",strtotime('+1 hour'));
        $tgl_cetak = TanggalIndo($hari_ini);
                
        $tahuncari2 = $request->tahuncari;
        $kd_areacari2 = $request->kd_areacari;
        $kd_jeniscari2 = $request->kd_jeniscari;
        $semestercari2 = $request->semestercari;
        if($tahuncari2=="2025" && $semestercari2=="1"){
            $tgl_cetak = TanggalIndo("2025-07-03");
        }

        $perintah = "";
        if($kd_areacari2!="" && $kd_areacari2!="semua"){
            $perintah .= " and penilaian_pegawai.kd_area='$kd_areacari2'";
        }
        if($kd_jeniscari2!="" && $kd_jeniscari2!="semua"){
            $perintah .= " and c.kd_jenis='$kd_jeniscari2'";
        }

        $rows1 = DB::table('penilaian_pegawai')->selectRaw("
            penilaian_pegawai.*,
            ifnull(b.nama_area,'') as nama_area,
            c.nama as nama,
            c.jabatan as jabatan,
            c.grade as grade,
            c.peg as peg
        ")       
        ->leftJoin('master_area as b','b.kd_area','=','penilaian_pegawai.kd_area')
        // ->leftJoin('data_pegawai as c','c.nip','=','penilaian_pegawai.nip')
        ->leftJoin('data_pegawai as c', function($join){
            // $join->whereRaw("c.nip=penilaian_pegawai.nip and c.level_kpi>='3'");
            $join->whereRaw("c.nip=penilaian_pegawai.nip");
        })    
        ->whereRaw("penilaian_pegawai.tahun='$tahuncari2' and penilaian_pegawai.nip not in (select nip from data_pegawai where jenis_kpi='pusat' and level_kpi<='2')".$perintah)
        ->groupBy('penilaian_pegawai.nip')
        ->orderBy('penilaian_pegawai.id','asc')
        ->get();
        
        $pdf = new PDF('P','mm','A4');
        $pdf->SetMargins(10, 10, 10);
        $pdf->AliasNbPages();
        $pdf->SetAutoPageBreak(false);
        $pdf->SetFillColor(202,228,250);
        $pdf->SetTextColor(0,0,0);
        // $pdf->SetFont('Arial','B',8);
        $pdf->SetFont('Arial','',8);
        
        $pdf->AddPage();
        $pdf->Image(asset('assets/images/pcn.png'),15,10,0,12);
        
        $pdf->SetFont('Arial','B',9);
        $y= $pdf->GetY();
        $pdf->SetXY(15,$y);
        $pdf->MultiCell(0,5,'TIM APRAISAL','','C',0);
        $pdf->SetFont('Arial','',8);
        $y= $pdf->GetY();
        $pdf->SetXY(15,$y);
        $pdf->MultiCell(0,5,'HASIL EVALUSASI PENILAIAN SEMESTER '.$semestercari2.' TAHUN '.$tahuncari2,'','C',0);

        $y= $pdf->GetY();
        $pdf->SetXY(15,$y);
        $pdf->MultiCell(0,8,'','','L',0);
        
        $pdf->SetFont('Arial','',8);        
        $y= $pdf->GetY();
        $pdf->SetXY(15,$y);
        $pdf->MultiCell(10,8,'No','LRTB','C',1);
        $pdf->SetXY(25,$y);
        $pdf->MultiCell(20,8,'Nip','RTB','C',1);
        $pdf->SetXY(45,$y);
        $pdf->MultiCell(40,8,'Nama','RTB','L',1);
        $pdf->SetXY(85,$y);
        $pdf->MultiCell(20,8,'Grade','RTB','C',1);
        $pdf->SetXY(105,$y);
        $pdf->MultiCell(10,8,'Peg','RTB','C',1);                
        $pdf->SetXY(115,$y);
        $pdf->MultiCell(15,4,'NSK','RT','C',1);
        $pdf->SetXY(115,$y+4);
        $pdf->MultiCell(15,4,'(Angka)','RB','C',1);
        $pdf->SetXY(130,$y);
        $pdf->MultiCell(15,4,'NSK','RT','C',1);
        $pdf->SetXY(130,$y+4);
        $pdf->MultiCell(15,4,'(Huruf)','RB','C',1);
        $pdf->SetXY(145,$y);
        $pdf->MultiCell(15,4,'NKI','RT','C',1);
        $pdf->SetXY(145,$y+4);
        $pdf->MultiCell(15,4,'(Angka)','RB','C',1);
        $pdf->SetXY(160,$y);
        $pdf->MultiCell(15,4,'NKI','RT','C',1);
        $pdf->SetXY(160,$y+4);
        $pdf->MultiCell(15,4,'(Huruf)','RB','C',1);
        $pdf->SetXY(175,$y);
        $pdf->MultiCell(0,8,'Kriteria Talenta','RTB','C',1);
        $no=1;
        foreach ($rows1 as $row1) {
            $nip = $row1->nip;
            $nama = $row1->nama;
            $jabatan = $row1->jabatan;
            $grade = $row1->grade;
            $peg = $row1->peg;
            $nama_area = $row1->nama_area;
            if($semestercari2=="1"){
                $skor_kinerja_semester = $row1->skor_kinerja_semester1;
                $huruf_kinerja_semester = $row1->huruf_kinerja_semester1;
                $skor_individu_semester = $row1->skor_individu_semester1;
                $huruf_individu_semester = $row1->huruf_individu_semester1;
                $nama_talenta_semester = $row1->nama_talenta_semester1;
            } else {
                $skor_kinerja_semester = $row1->skor_kinerja_semester2;
                $huruf_kinerja_semester = $row1->huruf_kinerja_semester2;
                $skor_individu_semester = $row1->skor_individu_semester2;
                $huruf_individu_semester = $row1->huruf_individu_semester2;
                $nama_talenta_semester = $row1->nama_talenta_semester2;
            }


            $batas_halaman= $pdf->GetY();
            if($batas_halaman>220){
                $pdf->SetFont('Arial','I',6);
                $pdf->SetXY(110,-12);
                $pdf->MultiCell(0, 4,'Halaman '.$pdf->PageNo().' / {nb}','','R',0); 
                
                
                $pdf->AddPage();
                $pdf->Image(asset('assets/images/pcn.png'),15,10,0,12);
                
                $pdf->SetFont('Arial','B',9);
                $y= $pdf->GetY();
                $pdf->SetXY(15,$y);
                $pdf->MultiCell(0,5,'TIM APRAISAL','','C',0);
                $pdf->SetFont('Arial','',8);
                $y= $pdf->GetY();
                $pdf->SetXY(15,$y);
                $pdf->MultiCell(0,5,'HASIL EVALUSASI PENILAIAN SEMESTER '.$semestercari2.' TAHUN '.$tahuncari2,'','C',0);

                $y= $pdf->GetY();
                $pdf->SetXY(15,$y);
                $pdf->MultiCell(0,8,'','','L',0);

                $pdf->SetFont('Arial','',8);
                $y= $pdf->GetY();
                $pdf->SetXY(15,$y);
                $pdf->MultiCell(10,8,'No','LRTB','C',1);
                $pdf->SetXY(25,$y);
                $pdf->MultiCell(20,8,'Nip','RTB','C',1);
                $pdf->SetXY(45,$y);
                $pdf->MultiCell(40,8,'Nama','RTB','L',1);
                $pdf->SetXY(85,$y);
                $pdf->MultiCell(20,8,'Grade','RTB','C',1);
                $pdf->SetXY(105,$y);
                $pdf->MultiCell(10,8,'Peg','RTB','C',1);                
                $pdf->SetXY(115,$y);
                $pdf->MultiCell(15,4,'NSK','RT','C',1);
                $pdf->SetXY(115,$y+4);
                $pdf->MultiCell(15,4,'(Angka)','RB','C',1);
                $pdf->SetXY(130,$y);
                $pdf->MultiCell(15,4,'NSK','RT','C',1);
                $pdf->SetXY(130,$y+4);
                $pdf->MultiCell(15,4,'(Huruf)','RB','C',1);
                $pdf->SetXY(145,$y);
                $pdf->MultiCell(15,4,'NKI','RT','C',1);
                $pdf->SetXY(145,$y+4);
                $pdf->MultiCell(15,4,'(Angka)','RB','C',1);
                $pdf->SetXY(160,$y);
                $pdf->MultiCell(15,4,'NKI','RT','C',1);
                $pdf->SetXY(160,$y+4);
                $pdf->MultiCell(15,4,'(Huruf)','RB','C',1);
                $pdf->SetXY(175,$y);
                $pdf->MultiCell(0,8,'Kriteria Talenta','RTB','C',1);
            }

            $pdf->SetFont('Arial','',8);
            $y= $pdf->GetY();
            $pdf->SetXY(15,$y);
            $pdf->MultiCell(10,1,'','LR','C',0);
            $pdf->SetXY(25,$y);
            $pdf->MultiCell(20,1,'','R','C',0);
            $pdf->SetXY(45,$y);
            $pdf->MultiCell(40,1,'','R','L',0);
            $pdf->SetXY(85,$y);
            $pdf->MultiCell(20,1,'','R','C',0);
            $pdf->SetXY(105,$y);
            $pdf->MultiCell(10,1,'','R','C',0);                
            $pdf->SetXY(115,$y);
            $pdf->MultiCell(15,1,'','R','C',0);
            $pdf->SetXY(130,$y);
            $pdf->MultiCell(15,1,'','R','C',0);
            $pdf->SetXY(145,$y);
            $pdf->MultiCell(15,1,'','R','C',0);
            $pdf->SetXY(160,$y);
            $pdf->MultiCell(15,1,'','R','C',0);
            $pdf->SetXY(175,$y);
            $pdf->MultiCell(0,1,'','R','C',0);

            $y= $pdf->GetY();
            $pdf->SetXY(15,$y);
            $pdf->MultiCell(10,4,$no,'LR','C',0);
            $x1 = $pdf->GetY();
            $tinggi1 = $x1-$y;
            $pdf->SetXY(25,$y);
            $pdf->MultiCell(20,4,$nip,'R','C',0);
            $x2 = $pdf->GetY();
            $tinggi2 = $x2-$y;
            $pdf->SetXY(45,$y);
            $pdf->MultiCell(40,4,$nama,'R','L',0);
            $x3 = $pdf->GetY();
            $tinggi3 = $x3-$y;
            $pdf->SetXY(85,$y);
            $pdf->MultiCell(20,4,$grade,'R','C',0);
            $x4 = $pdf->GetY();
            $tinggi4 = $x4-$y;
            $pdf->SetXY(105,$y);
            $pdf->MultiCell(10,4,$peg,'R','C',0);
            $x5 = $pdf->GetY();
            $tinggi5 = $x5-$y;
            $pdf->SetXY(115,$y);
            $pdf->MultiCell(15,4,$skor_kinerja_semester,'R','C',0);
            $x6 = $pdf->GetY();
            $tinggi6 = $x6-$y;
            $pdf->SetXY(130,$y);
            $pdf->MultiCell(15,4,$huruf_kinerja_semester,'R','C',0);
            $x7 = $pdf->GetY();
            $tinggi7 = $x7-$y;
            $pdf->SetXY(145,$y);
            $pdf->MultiCell(15,4,$skor_individu_semester,'R','C',0);
            $x8 = $pdf->GetY();
            $tinggi8 = $x8-$y;
            $pdf->SetXY(160,$y);
            $pdf->MultiCell(15,4,$huruf_individu_semester,'R','C',0);
            $x9 = $pdf->GetY();
            $tinggi9 = $x9-$y;
            $pdf->SetXY(175,$y);
            $pdf->MultiCell(0,4,$nama_talenta_semester,'R','C',0);
            $x10 = $pdf->GetY();
            $tinggi10 = $x10-$y;

            $tinggi = max($tinggi1,$tinggi2,$tinggi3,$tinggi4,$tinggi5,$tinggi6,$tinggi7,$tinggi8,$tinggi9,$tinggi10);
            $selisih1 = $tinggi-$tinggi1+1;
            $selisih2 = $tinggi-$tinggi2+1;
            $selisih3 = $tinggi-$tinggi3+1;
            $selisih4 = $tinggi-$tinggi4+1;
            $selisih5 = $tinggi-$tinggi5+1;
            $selisih6 = $tinggi-$tinggi6+1;
            $selisih7 = $tinggi-$tinggi7+1;
            $selisih8 = $tinggi-$tinggi8+1;
            $selisih9 = $tinggi-$tinggi9+1;
            $selisih10 = $tinggi-$tinggi10+1;

            $pdf->SetXY(15,$x1);
            $pdf->MultiCell(10,$selisih1,'','LRB','C',0);
            $pdf->SetXY(25,$x2);
            $pdf->MultiCell(20,$selisih2,'','RB','C',0);
            $pdf->SetXY(45,$x3);
            $pdf->MultiCell(40,$selisih3,'','RB','L',0);
            $pdf->SetXY(85,$x4);
            $pdf->MultiCell(20,$selisih4,'','RB','C',0);
            $pdf->SetXY(105,$x5);
            $pdf->MultiCell(10,$selisih5,'','RB','C',0);                
            $pdf->SetXY(115,$x6);
            $pdf->MultiCell(15,$selisih6,'','RB','C',0);
            $pdf->SetXY(130,$x7);
            $pdf->MultiCell(15,$selisih7,'','RB','C',0);
            $pdf->SetXY(145,$x8);
            $pdf->MultiCell(15,$selisih8,'','RB','C',0);
            $pdf->SetXY(160,$x9);
            $pdf->MultiCell(15,$selisih9,'','RB','C',0);
            $pdf->SetXY(175,$x10);
            $pdf->MultiCell(0,$selisih10,'','RB','C',0);
            $no++;
        }
        $batas_halaman= $pdf->GetY();
        if($batas_halaman>200){
            $pdf->SetFont('Arial','I',6);
            $pdf->SetXY(110,-12);
            $pdf->MultiCell(0, 4,'Halaman '.$pdf->PageNo().' / {nb}','','R',0); 
            
            $pdf->AddPage();
            $pdf->Image(asset('assets/images/pcn.png'),15,10,0,12);
            
            $pdf->SetFont('Arial','B',9);
            $y= $pdf->GetY();
            $pdf->SetXY(15,$y);
            $pdf->MultiCell(0,5,'TIM APRAISAL','','C',0);
            $pdf->SetFont('Arial','',8);
            $y= $pdf->GetY();
            $pdf->SetXY(15,$y);
            $pdf->MultiCell(0,5,'HASIL EVALUSASI PENILAIAN SEMESTER '.$semestercari2.' TAHUN '.$tahuncari2,'','C',0);

            $y= $pdf->GetY();
            $pdf->SetXY(15,$y);
            $pdf->MultiCell(0,8,'','','L',0);

            $pdf->SetFont('Arial','',8);
            $y= $pdf->GetY();
            $pdf->SetXY(15,$y);
            $pdf->MultiCell(10,8,'No','LRTB','C',1);
            $pdf->SetXY(25,$y);
            $pdf->MultiCell(20,8,'Nip','RTB','C',1);
            $pdf->SetXY(45,$y);
            $pdf->MultiCell(40,8,'Nama','RTB','L',1);
            $pdf->SetXY(85,$y);
            $pdf->MultiCell(20,8,'Grade','RTB','C',1);
            $pdf->SetXY(105,$y);
            $pdf->MultiCell(10,8,'Peg','RTB','C',1);                
            $pdf->SetXY(115,$y);
            $pdf->MultiCell(15,4,'NSK','RT','C',1);
            $pdf->SetXY(115,$y+4);
            $pdf->MultiCell(15,4,'(Angka)','RB','C',1);
            $pdf->SetXY(130,$y);
            $pdf->MultiCell(15,4,'NSK','RT','C',1);
            $pdf->SetXY(130,$y+4);
            $pdf->MultiCell(15,4,'(Huruf)','RB','C',1);
            $pdf->SetXY(145,$y);
            $pdf->MultiCell(15,4,'NKI','RT','C',1);
            $pdf->SetXY(145,$y+4);
            $pdf->MultiCell(15,4,'(Angka)','RB','C',1);
            $pdf->SetXY(160,$y);
            $pdf->MultiCell(15,4,'NKI','RT','C',1);
            $pdf->SetXY(160,$y+4);
            $pdf->MultiCell(15,4,'(Huruf)','RB','C',1);
            $pdf->SetXY(175,$y);
            $pdf->MultiCell(0,8,'Kriteria Talenta','RTB','C',1);
        }

        $pdf->SetFont('Arial','',9);
        $y= $pdf->GetY();
        $pdf->SetXY(15,$y);
        $pdf->MultiCell(0,8,'','','L',0);

        $y= $pdf->GetY();
        $pdf->SetXY(120,$y);
        $pdf->MultiCell(0,5,'Balikpapan, '.$tgl_cetak,'','C',0);

        $pdf->SetFont('Arial','',8);
        $y= $pdf->GetY();
        $pdf->SetXY(15,$y);
        $pdf->MultiCell(0,2,'','','C',0);
        
        $y= $pdf->GetY();
        $pdf->SetXY(15,$y);
        $pdf->MultiCell(70,5,'DIREKTUR KEUANGAN & ADM','','C',0);
        $pdf->SetXY(120,$y);
        $pdf->MultiCell(0,5,'DIREKTUR OPERASI','','C',0);

        $y= $pdf->GetY();
        $pdf->SetXY(15,$y);
        $pdf->MultiCell(0,14,'','','C',0);

        $pdf->SetFont('Arial','B',8);
        $y= $pdf->GetY();
        $pdf->SetXY(15,$y);
        $pdf->MultiCell(70,5,'ANDRY APRIAWAN','','C',0);
        $pdf->SetXY(120,$y);
        $pdf->MultiCell(0,5,'FATAHUDDIN YOGI AMIBOWO','','C',0);

        $y= $pdf->GetY();
        $pdf->SetXY(15,$y);
        $pdf->MultiCell(0,5,'','','C',0);

        $pdf->SetFont('Arial','',8);
        $y= $pdf->GetY();
        $pdf->SetXY(15,$y);
        $pdf->MultiCell(0,5,'DIREKTUR UTAMA','','C',0);

        $y= $pdf->GetY();
        $pdf->SetXY(15,$y);
        $pdf->MultiCell(0,14,'','','C',0);

        $pdf->SetFont('Arial','B',8);
        $y= $pdf->GetY();
        $pdf->SetXY(15,$y);
        $pdf->MultiCell(0,5,'IRAWAN HERNANDA','','C',0);
        
        $pdf->SetFont('Arial','I',6);
        $pdf->SetXY(110,-12);
        $pdf->MultiCell(0, 4,'Halaman '.$pdf->PageNo().' / {nb}','','R',0); 
 
        $pdf->Output();

        exit;

    }
    
}
