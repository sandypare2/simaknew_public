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

class CetakkenaikanmController extends Controller
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
                
        $kd_areacari = $request->kd_areacari;
        $perintah = "";
        if($kd_areacari!="" && $kd_areacari!="semua"){
            $perintah = " and data_pegawai.kd_area='$kd_areacari'";
        }

        $rows1 = DB::table('data_pegawai')
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

        $pdf = new PDF('P','mm','A4');
        $pdf->SetMargins(10, 10, 10);
        $pdf->AliasNbPages();
        $pdf->SetAutoPageBreak(false);
        $pdf->SetFillColor(202,228,250);
        $pdf->SetTextColor(0,0,0);
        // $pdf->SetFont('Arial','B',8);
        $pdf->SetFont('Arial','',8);
        
        $pdf->AddPage();
        $pdf->Image(asset('assets/images/pcn.png'),10,10,0,12);
        
        $pdf->SetFont('Arial','B',9);
        $y= $pdf->GetY();
        $pdf->SetXY(10,$y);
        $pdf->MultiCell(0,5,'MONITORING TALENTA PEGAWAI','','C',0);
        $pdf->SetFont('Arial','',8);

        $y= $pdf->GetY();
        $pdf->SetXY(10,$y);
        $pdf->MultiCell(0,8,'','','L',0);
        
        $pdf->SetFont('Arial','',8);        
        $y= $pdf->GetY();
        $pdf->SetXY(10,$y);
        $pdf->MultiCell(10,8,'No','LRTB','C',1);
        $pdf->SetXY(20,$y);
        $pdf->MultiCell(20,8,'Nip','RTB','C',1);
        $pdf->SetXY(40,$y);
        $pdf->MultiCell(40,8,'Nama','RTB','L',1);
        $pdf->SetXY(80,$y);
        $pdf->MultiCell(30,8,'Unit','RTB','C',1);
        $pdf->SetXY(110,$y);
        $pdf->MultiCell(35,4,'Grade Terakhir','RTB','C',1);
        $pdf->SetXY(110,$y+4);
        $pdf->MultiCell(20,4,'Grade','RB','C',1);
        $pdf->SetXY(130,$y+4);
        $pdf->MultiCell(15,4,'Tanggal','RB','C',1);
        $pdf->SetXY(145,$y);
        $pdf->MultiCell(20,8,'Talenta','RTB','C',1);
        $pdf->SetXY(165,$y);
        $pdf->MultiCell(35,4,'Rencana Kenaikan','RTB','C',1);
        $pdf->SetXY(165,$y+4);
        $pdf->MultiCell(20,4,'Grade','RB','C',1);
        $pdf->SetXY(185,$y+4);
        $pdf->MultiCell(15,4,'Tanggal','RB','C',1);
        $no=1;
        foreach($rows1 as $row1){
            $id_master_grade = $row1->id_master_grade;
            if($row1->tgl_kenaikan2!=""){
                $tanggalnya = explode("-",$row1->tgl_kenaikan2);
                $tahun = $tanggalnya[0];
                $bulan = $tanggalnya[1];
                if(intval($bulan)<=6){
                    // $tahunnya = $tahun-1;
                    // $semesternya = 2;
                    $semester = 1;
                } else {
                    // $tahunnya = $tahun;
                    // $semesternya = 1;
                    $semester = 2;
                }
                $tahun_semester = $tahun.$semester;
            } else {
                // $tahunnya = "";
                // $semesternya = "";
                $tahun = "";
                $bulan = "";
                $tahun_semester = "";
            }

            if($row1->tgl_kenaikan2!=""){
                $tgl_kenaikan2 = Carbon::createFromFormat('Y-m-d', $row1->tgl_kenaikan2)->format('d/m/Y');
            } else {
                $tgl_kenaikan2 = "";
            }

            $batas_halaman= $pdf->GetY();
            if($batas_halaman>280){
                $pdf->SetFont('Arial','I',6);
                $pdf->SetXY(110,-12);
                $pdf->MultiCell(0, 4,'Halaman '.$pdf->PageNo().' / {nb}','','R',0);                 
                
                $pdf->AddPage();
                $pdf->Image(asset('assets/images/pcn.png'),10,10,0,12);
                
                $pdf->SetFont('Arial','B',9);
                $y= $pdf->GetY();
                $pdf->SetXY(10,$y);
                $pdf->MultiCell(0,5,'MONITORING TALENTA PEGAWAI','','C',0);

                $y= $pdf->GetY();
                $pdf->SetXY(15,$y);
                $pdf->MultiCell(0,8,'','','L',0);

                $pdf->SetFont('Arial','',8);
                $y= $pdf->GetY();
                $pdf->SetXY(10,$y);
                $pdf->MultiCell(10,8,'No','LRTB','C',1);
                $pdf->SetXY(20,$y);
                $pdf->MultiCell(20,8,'Nip','RTB','C',1);
                $pdf->SetXY(40,$y);
                $pdf->MultiCell(40,8,'Nama','RTB','L',1);
                $pdf->SetXY(80,$y);
                $pdf->MultiCell(30,8,'Unit','RTB','C',1);
                $pdf->SetXY(110,$y);
                $pdf->MultiCell(35,4,'Grade Terakhir','RTB','C',1);
                $pdf->SetXY(110,$y+4);
                $pdf->MultiCell(20,4,'Grade','RB','C',1);
                $pdf->SetXY(130,$y+4);
                $pdf->MultiCell(15,4,'Tanggal','RB','C',1);
                $pdf->SetXY(145,$y);
                $pdf->MultiCell(20,8,'Talenta','RTB','C',1);
                $pdf->SetXY(165,$y);
                $pdf->MultiCell(35,4,'Rencana Kenaikan','RTB','C',1);
                $pdf->SetXY(165,$y+4);
                $pdf->MultiCell(20,4,'Grade','RB','C',1);
                $pdf->SetXY(185,$y+4);
                $pdf->MultiCell(15,4,'Tanggal','RB','C',1);
            }

            $y= $pdf->GetY();
            $pdf->SetXY(10,$y);
            $pdf->MultiCell(10,1,'','LR','C',0);
            $pdf->SetXY(20,$y);
            $pdf->MultiCell(20,1,'','R','C',0);
            $pdf->SetXY(40,$y);
            $pdf->MultiCell(40,1,'','R','L',0);
            $pdf->SetXY(80,$y);
            $pdf->MultiCell(30,1,'','R','C',0);
            $pdf->SetXY(110,$y);
            $pdf->MultiCell(20,1,'','R','C',0);
            $pdf->SetXY(130,$y);
            $pdf->MultiCell(15,1,'','R','C',0);
            $pdf->SetXY(145,$y);
            $pdf->MultiCell(20,1,'','R','C',0);
            $pdf->SetXY(165,$y);
            $pdf->MultiCell(20,1,'','R','C',0);
            $pdf->SetXY(185,$y);
            $pdf->MultiCell(15,1,'','R','C',0);

            $pdf->SetFont('Arial','',7);
            $y= $pdf->GetY();
            $pdf->SetXY(10,$y);
            $pdf->MultiCell(10,4,$no,'LR','C',0);
            $x1 = $pdf->GetY();
            $tinggi1 = $x1-$y;
            $pdf->SetXY(20,$y);
            $pdf->MultiCell(20,4,$row1->nip,'R','C',0);
            $x2 = $pdf->GetY();
            $tinggi2 = $x2-$y;
            $pdf->SetXY(40,$y);
            $pdf->MultiCell(40,4,$row1->nama,'R','L',0);
            $x3 = $pdf->GetY();
            $tinggi3 = $x3-$y;
            $pdf->SetXY(80,$y);
            $pdf->MultiCell(30,4,$row1->nama_area,'R','C',0);
            $x4 = $pdf->GetY();
            $tinggi4 = $x4-$y;
            $pdf->SetXY(110,$y);
            $pdf->MultiCell(20,4,$row1->grade2,'R','C',0);
            $x5 = $pdf->GetY();
            $tinggi5 = $x5-$y;
            $pdf->SetXY(130,$y);
            $pdf->MultiCell(15,4,$tgl_kenaikan2,'R','C',0);
            $x6 = $pdf->GetY();
            $tinggi6 = $x6-$y;

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
                ->whereRaw("nip='".$row1->nip."' and concat(tahun,semester)>='$tahun_semester'")
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
                $a = '';
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
                    // $a .= '<span>'.$row2->nama_talenta.' : '.$row2->jumlah_talenta.'</span><br/>';                        

                    if($tgl_kenaikan==""){
                        if($jumlah_kpo>0){
                            if($jumlah_talenta>=8){
                                $tgl_kenaikan = Carbon::createFromFormat('Y-m-d', $tgl_kenaikan_awal)->endOfMonth()->addDay()->format('d/m/Y');
                            }
                        } else if($jumlah_pot>0){
                            if($jumlah_talenta>=6){
                                $tgl_kenaikan = Carbon::createFromFormat('Y-m-d', $tgl_kenaikan_awal)->endOfMonth()->addDay()->format('d/m/Y');
                            }
                        } else if($jumlah_opt>0){
                            if($jumlah_talenta>=5){
                                $tgl_kenaikan = Carbon::createFromFormat('Y-m-d', $tgl_kenaikan_awal)->endOfMonth()->addDay()->format('d/m/Y');
                            }
                        } else if($jumlah_sop_spo>0){
                            if($jumlah_talenta>=4){
                                $tgl_kenaikan = Carbon::createFromFormat('Y-m-d', $tgl_kenaikan_awal)->endOfMonth()->addDay()->format('d/m/Y');
                            }
                        } else if($jumlah_lbs>0){
                            if($jumlah_talenta>=2){
                                $tgl_kenaikan = Carbon::createFromFormat('Y-m-d', $tgl_kenaikan_awal)->endOfMonth()->addDay()->format('d/m/Y');
                            }
                        } else {
                            $tgl_kenaikan = "";
                        }
                    }
                }
                if($jumlah_lbs>0){
                    $pdf->SetXY(145,$y);
                    $pdf->MultiCell(20,4,'LBS : '.$jumlah_lbs,'R','L',0);
                    $a .= '<span>Luar Biasa : '.$jumlah_lbs.'</span><br/>';
                }
                if($jumlah_sop_spo>0){
                    $y1= $pdf->GetY();
                    if($a!=""){
                        $pdf->SetXY(145,$y1);
                    } else {
                        $pdf->SetXY(145,$y);
                    }
                    $pdf->MultiCell(20,4,'SOP/SPO : '.$jumlah_sop_spo,'R','L',0);
                    $a .= '<span>Sangat Potensial/Optimal : '.$jumlah_sop_spo.'</span><br/>';
                }
                if($jumlah_opt>0){
                    $y1= $pdf->GetY();
                    if($a!=""){
                        $pdf->SetXY(145,$y1);
                    } else {
                        $pdf->SetXY(145,$y);
                    }
                    $pdf->MultiCell(20,4,'OPT : '.$jumlah_opt,'R','L',0);
                    $a .= '<span>Optimal : '.$jumlah_opt.'</span><br/>';
                }
                if($jumlah_pot>0){
                    $y1= $pdf->GetY();
                    if($a!=""){
                        $pdf->SetXY(145,$y1);
                    } else {
                        $pdf->SetXY(145,$y);
                    }
                    $pdf->MultiCell(20,4,'POT : '.$jumlah_pot,'R','L',0);
                    $a .= '<span>Potensial : '.$jumlah_pot.'</span><br/>';
                }
                if($jumlah_kpo>0){
                    $y1= $pdf->GetY();
                    if($a!=""){
                        $pdf->SetXY(145,$y1);
                    } else {
                        $pdf->SetXY(145,$y);
                    }
                    $pdf->MultiCell(20,4,'KPO : '.$jumlah_kpo,'R','L',0);
                    $a .= '<span>Kandidat Potensial : '.$jumlah_kpo.'</span><br/>';
                }
                $x7 = $pdf->GetY();
                $tinggi7 = $x7-$y;

                // $pdf->SetXY(145,$y);
                // $pdf->MultiCell(20,5,$a,'R','L',0);

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
                        $tgl_kenaikan = Carbon::createFromFormat('Y-m-d', $tgl_kenaikan_awal2)->endOfMonth()->addDay()->format('d/m/Y');
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
                        $tgl_kenaikan = Carbon::createFromFormat('Y-m-d', $tgl_kenaikan_awal2)->endOfMonth()->addDay()->format('d/m/Y');
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
                        $tgl_kenaikan = Carbon::createFromFormat('Y-m-d', $tgl_kenaikan_awal2)->endOfMonth()->addDay()->format('d/m/Y');
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
                        $tgl_kenaikan = Carbon::createFromFormat('Y-m-d', $tgl_kenaikan_awal2)->endOfMonth()->addDay()->format('d/m/Y');
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
                        $tgl_kenaikan = Carbon::createFromFormat('Y-m-d', $tgl_kenaikan_awal2)->endOfMonth()->addDay()->format('d/m/Y');
                    }
                }
                $pdf->SetXY(165,$y);
                $pdf->MultiCell(20,4,$rencana_kenaikan,'R','C',0);
                $x8 = $pdf->GetY();
                $tinggi8 = $x8-$y;
                $pdf->SetXY(185,$y);
                $pdf->MultiCell(15,4,$tgl_kenaikan,'R','C',0);
                $x9 = $pdf->GetY();
                $tinggi9 = $x9-$y;                
            } else {
                $pdf->SetXY(145,$y);
                $pdf->MultiCell(20,4,'','R','L',0);
                $x7 = $pdf->GetY();
                $tinggi7 = $x7-$y;
                $pdf->SetXY(165,$y);
                $pdf->MultiCell(20,4,"",'R','C',0);
                $x8 = $pdf->GetY();
                $tinggi8 = $x8-$y;
                $pdf->SetXY(185,$y);
                $pdf->MultiCell(15,4,"",'R','C',0);
                $x9 = $pdf->GetY();
                $tinggi9 = $x9-$y;                
            }

            $tinggi = max($tinggi1,$tinggi2,$tinggi3,$tinggi4,$tinggi5,$tinggi6,$tinggi7,$tinggi8,$tinggi9);
            $selisih1 = $tinggi-$tinggi1+1;
            $selisih2 = $tinggi-$tinggi2+1;
            $selisih3 = $tinggi-$tinggi3+1;
            $selisih4 = $tinggi-$tinggi4+1;
            $selisih5 = $tinggi-$tinggi5+1;
            $selisih6 = $tinggi-$tinggi6+1;
            $selisih7 = $tinggi-$tinggi7+1;
            $selisih8 = $tinggi-$tinggi8+1;
            $selisih9 = $tinggi-$tinggi9+1;

            $pdf->SetXY(10,$x1);
            $pdf->MultiCell(10,$selisih1,'','LRB','C',0);
            $pdf->SetXY(20,$x2);
            $pdf->MultiCell(20,$selisih2,'','RB','C',0);
            $pdf->SetXY(40,$x3);
            $pdf->MultiCell(40,$selisih3,'','RB','L',0);
            $pdf->SetXY(80,$x4);
            $pdf->MultiCell(30,$selisih4,'','RB','C',0);
            $pdf->SetXY(110,$x5);
            $pdf->MultiCell(20,$selisih5,'','RB','C',0);
            $pdf->SetXY(130,$x6);
            $pdf->MultiCell(15,$selisih6,'','RB','C',0);
            $pdf->SetXY(145,$x7);
            $pdf->MultiCell(20,$selisih7,'','RB','C',0);
            $pdf->SetXY(165,$x8);
            $pdf->MultiCell(20,$selisih8,'','RB','C',0);
            $pdf->SetXY(185,$x9);
            $pdf->MultiCell(15,$selisih9,'','RB','C',0);

            $no++;
        } 

        $pdf->SetFont('Arial','I',6);
        $pdf->SetXY(110,-12);
        $pdf->MultiCell(0, 4,'Halaman '.$pdf->PageNo().' / {nb}','','R',0);                 

        $pdf->Output();

        exit;

    }
    
}
