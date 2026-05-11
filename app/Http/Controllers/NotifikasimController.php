<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;

class NotifikasimController extends Controller
{
    public function index(Request $request)
    {        

    }    

    public function fetchNotifikasi(Request $request)
    {
        // $kd_kontrak = $request->kd_kontrak;
        // $data['filter_notifikasi'] = DB::table('v_kontrak2')
        $nipnya = Auth::user()->nip;
        $datanya = array();
        $row1 = DB::table('sppd1')
        ->selectRaw("
            sum(if(approve1<>'2' and approval1='$nipnya',1,0)) as notif_sppd1,
            sum(if(approve1='2' and approve2<>'2' and approval2='$nipnya',1,0)) as notif_sppd2,
            sum(if(approve2='2' and approvesdm<>'2' and approvalsdm='$nipnya',1,0)) as notif_sppd3,
            sum(if(approvesdm='2' and validasi_biaya='0' and approvalbayar='$nipnya',1,0)) as notif_sppd4
        ")
        // ->whereRaw("status<='1' and hari<0")
        ->first();
        if($row1){
            $notif_sppd = intval($row1->notif_sppd1)+intval($row1->notif_sppd2)+intval($row1->notif_sppd3)+intval($row1->notif_sppd4);
        } else {
            $notif_sppd = 0;
        }
        $datanya["notif_sppd"] = $notif_sppd;

        $row2 = DB::table('biaya_restitusi')
        ->selectRaw("
            count(DISTINCT idsppd) as notif_reimburse
        ")
        ->whereRaw("approve1<>'2' and approval1='$nipnya'")
        ->first();
        if($row2){
            $notif_reimburse = intval($row2->notif_reimburse);
        } else {
            $notif_reimburse = 0;
        }
        $datanya['notif_reimburse'] = $notif_reimburse;

        $row2 = DB::table('cuti')
        ->selectRaw("
            sum(if(approve1='0' and approval1='$nipnya',1,0)) as notif_cuti
        ")
        ->first();
        if($row2){
            $notif_cuti = intval($row2->notif_cuti);
        } else {
            $notif_cuti = 0;
        }
        $datanya['notif_cuti'] = $notif_cuti;

        $row2 = DB::table('ijin')
        ->selectRaw("
            sum(if(approve1='0' and approval1='$nipnya',1,0)) as notif_izin
        ")
        ->first();
        if($row2){
            $notif_izin = intval($row2->notif_izin);
        } else {
            $notif_izin = 0;
        }
        $datanya['notif_izin'] = $notif_izin;

        $row2 = DB::table('konsumsi')
        ->selectRaw("
            sum(if((approve1<>'2' and approval1='$nipnya') or (approve2<>'2' and approval2='$nipnya'),1,0)) as notif_konsumsi
        ")
        ->first();
        if($row2){
            $notif_konsumsi = intval($row2->notif_konsumsi);
        } else {
            $notif_konsumsi = 0;
        }
        $datanya['notif_konsumsi'] = $notif_konsumsi;

        $row2 = DB::table('absensi')
        ->selectRaw("
            sum(if((approve1='0' and approval1='$nipnya') or (approve2='0' and approval2='$nipnya'),1,0)) as notif_absensi
        ")
        ->first();
        if($row2){
            $notif_absensi = intval($row2->notif_absensi);
        } else {
            $notif_absensi = 0;
        }
        $datanya['notif_absensi'] = $notif_absensi;

        $row2 = DB::table('hris.pilihan_jabatan')
        ->selectRaw("
            sum(if((approve1='0' and approval1='$nipnya') or (approve2='0' and approval2='$nipnya'),1,0)) as notif_lowongan
        ")
        ->first();
        if($row2){
            $notif_lowongan = intval($row2->notif_lowongan);
        } else {
            $notif_lowongan = 0;
        }
        $datanya['notif_lowongan'] = $notif_lowongan;

        $data['filter_notifikasi'] = $datanya;
        return response()->json($data);
    }
    
}
