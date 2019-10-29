<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Monitoring;
use Carbon\Carbon;

class MonitoringController extends Controller
{
    public function monitoring(){
        return new Monitoring;
    }

    public function createManual(Request $req){
        $nTemp       = [];
        $nPH         = [];
        $nTurbidity  = [];

        $temperature = $req->temperature;
        $ph          = $req->ph;
        $turbidity   = $req->turbidity;

    // =============== Fuzzy Logic =================
    // ---------------- variabel suhu ------------------
        // fungsi keanggotaan suhu dingin
        if($temperature < 26){
            $nTemp[0] = 1;
        }
        elseif($temperature >= 26 && $temperature <= 28){
            $nTemp[0] = (28 - $temperature)/2;
        }
        elseif($temperature > 28){
            $nTemp[0] = 0;
        }

        // fungsi keanggotaan suhu normal
        if($temperature > 30 || $temperature < 26)
        {
            $nTemp[1] = 0;
        }
        elseif($temperature >= 26 && $temperature <= 28)
        {
            $nTemp[1] = ($temperature - 26) / 2;
        }
        elseif($temperature > 28 && $temperature <= 30){
            $nTemp[1] = (30 - $temperature) / 2;
        }

        //fungsi keanggotaaan suhu panas
        if($temperature <= 28)
        {
            $nTemp[2] = 0;
        }elseif($temperature > 28 && $temperature <= 30)
        {
            $nTemp[2] = ($temperature-28)/2;
        }
        elseif ($temperature > 30) 
        {
            $nTemp[2] = 1;
        }

    //------------------ Variabel pH -----------------
        //fungsi keanggotaan ph rendah
        if($ph < 6.5)
        {
            $nPH[0] = 1;
        }
        elseif ($ph >= 6.5 && $ph <= 6.8) {
            $nPH[0] = (6.8 - $ph) / 0.3;
        }
        elseif ($ph > 6.8) {
            $nPH[0] = 0;
        }
        //fungsi keanggotaan ph normal
        if($ph > 7 || $ph < 6.5){
            $nPH[1] = 0;
        }
        elseif($ph >= 6.5 && $ph <= 6.8){
            $nPH[1] = ($ph - 6.5) / 0.3;
        }
        elseif($ph > 6.8 && $ph <= 7){
            $nPH[1] = (7 - $ph) / 0.2;
        }

        //fungsi keanggotaan ph tinggi
        if($ph <= 6.8)
        {
            $nPH[2]=0;
        }
        elseif ($ph>6.8 && $ph<7) 
        {
            $nPH[2]=($ph-6.8)/0.3;
        }
        elseif ($ph>=7){ 
            $nPH[2]=1;
        }
    
    //------------------ Variabel Kekeruhan -----------------
        //fungsi keanggotaan kekeruhan normal
        if($turbidity > 25){
            $nTurbidity[0] = 0;
        }
        elseif ($turbidity > 0 && $turbidity <= 25) {
            $nTurbidity[0] = (25 - $turbidity) / 25;
        }
        // else{
        elseif ($turbidity <= 0) {
            $nTurbidity[0] = 1;
        }

    //fungsi keanggotaan kekeruhan tinggi
        if($turbidity <= 0){
            $nTurbidity[1] = 0;
        }
        elseif ($turbidity > 0 && $turbidity <= 25) {
            $nTurbidity[1] = ($turbidity - 0) / 25;
        }
        // else{
        elseif ($turbidity >= 25) {
            $nTurbidity[1] = 1;
        }

    //inferensi dengan implikasi MIN
        //[R1]
        $aPred1 = min($nTemp[0], $nPH[0], $nTurbidity[0]); //tidak layak
        $z1 = 50 + (50*$aPred1); //tidak layak
        //[R2]
        $aPred2 = min($nTemp[0], $nPH[1], $nTurbidity[0]); //tidak layak
        $z2 = 50 + (50*$aPred2); //tidak layak
        //[R3]
        $aPred3 = min($nTemp[0], $nPH[2], $nTurbidity[0]); //tidak layak
        $z3 = 50 + (50*$aPred3); //tidak layak
        //[R4]
        $aPred4 = min($nTemp[0], $nPH[0], $nTurbidity[1]); //tidak layak
        $z4 = 50 + (50*$aPred4); //tidak layak
        //[R5]
        $aPred5 = min($nTemp[0], $nPH[1], $nTurbidity[1]); //tidak layak
        $z5 = 50 + (50*$aPred5); //tidak layak
        //[R6]
        $aPred6 = min($nTemp[0], $nPH[2], $nTurbidity[1]); //tidak layak
        $z6 = 50 + (50*$aPred6); //tidak layak
        //[R7]
        $aPred7 = min($nTemp[1], $nPH[0], $nTurbidity[0]); //tidak layak
        $z7 = 50 + (50*$aPred7); //tidak layak
        //[R8]
        $aPred8 = min($nTemp[1], $nPH[1], $nTurbidity[0]); //layak
        $z8 = 50 - (49*$aPred8); //layak
        //[R9]
        $aPred9 = min($nTemp[1], $nPH[2], $nTurbidity[0]); //tidak layak
        $z9 = 50 + (50*$aPred9); //tidak layak
        //[R10]
        $aPred10 = min($nTemp[1], $nPH[0], $nTurbidity[1]); //tidak layak
        $z10 = 50 + (50*$aPred10); //tidak layak
        //[R11]
        $aPred11 = min($nTemp[1], $nPH[1], $nTurbidity[1]); //tidak layak
        $z11 = 50 + (50*$aPred11); //tidak layak
        //[R12]
        $aPred12 = min($nTemp[1], $nPH[2], $nTurbidity[1]); //tidak layak
        $z12 = 50 + (50*$aPred12); //tidak layak
        //[R13]
        $aPred13 = min($nTemp[2], $nPH[0], $nTurbidity[0]); //tidak layak
        $z13 = 50 + (50*$aPred13); //tidak layak
        //[R14]
        $aPred14 = min($nTemp[2], $nPH[1], $nTurbidity[0]); //tidak layak
        $z14 = 50 + (50*$aPred14); //tidak layak
        //[R15]
        $aPred15 = min($nTemp[2], $nPH[2], $nTurbidity[0]); //tidak layak
        $z15 = 50 + (50*$aPred15); //tidak layak
        //[R16]
        $aPred16 = min($nTemp[2], $nPH[0], $nTurbidity[1]); //tidak layak
        $z16 = 50 + (50*$aPred16); //tidak layak
        //[R17]
        $aPred17 = min($nTemp[2], $nPH[1], $nTurbidity[1]); //tidak layak
        $z17 = 50 + (50*$aPred17); //tidak layak
        //[R18]
        $aPred18 = min($nTemp[2], $nPH[2], $nTurbidity[1]); //tidak layak
        $z18 = 50 + (50*$aPred18); //tidak layak
        
        //defuzifikasi / menghitung rata-rata
        $atas  = ($aPred1*$z1)+($aPred2*$z2)+($aPred3*$z3)+($aPred4*$z4)+($aPred5*$z5)+($aPred6*$z6)+($aPred7*$z7)+($aPred8*$z8)+($aPred9*$z9)+($aPred10*$z10)+($aPred11*$z11)+($aPred12*$z12)+($aPred13*$z13)+($aPred14*$z14)+($aPred15*$z15)+($aPred16*$z16)+($aPred17*$z17)+($aPred18*$z18);
        $bawah = $aPred1+$aPred2+$aPred3+$aPred4+$aPred5+$aPred6+$aPred7+$aPred8+$aPred9+$aPred10+$aPred11+$aPred12+$aPred13+$aPred14+$aPred15+$aPred16+$aPred17+$aPred18;
        $rZ    = $atas/$bawah;

        // keterangan
        if($rZ>=0 && $rZ<50){
            $status = "air masih layak!!";
        }elseif($rZ >= 50){
            $status = "air tidak layak !!"; 
        }
        //informasi
        if($nTemp[0]==1 && $nPH[0]==1 && $nTurbidity[0]!=0){
            $ket   = "suhu dan ph rendah";
        }
        elseif ($nTemp[0]==1 && $nPH[1]!=0 && $nTurbidity[0]!=0) {
            $ket   = "suhu rendah";        
        }
        elseif ($nTemp[0]==1 && $nPH[2]==1 && $nTurbidity[0]!=0) {
            $ket   = "suhu rendah, dan ph tinggi";   
        }
        elseif ($nTemp[0]==1 && $nPH[0]==1 && $nTurbidity[1]==1) {
            $ket   = "suhu dan ph rendah, serta kekeruhan tinggi"; 
        }
        elseif ($nTemp[0]==1 && $nPH[1]!=0 && $nTurbidity[1]==1) {
            $ket   = "suhu rendah dan kekeruhan tinggi";   
        }
        elseif ($nTemp[0]==1 && $nPH[2]==1 && $nTurbidity[1]==1) {
            $ket   = "suhu rendah, ph dan kekeruhan tinggi";  
        }
        elseif ($nTemp[1]!=0 && $nPH[0]==1 && $nTurbidity[0]!=0) {
            $ket   = "ph rendah";   
        }
        elseif ($nTemp[1]!=0 && $nPH[1]!=0 && $nTurbidity[0]!=0) {
            $ket   = "nilai parameter normal";  
        }
        elseif ($nTemp[1]!=0 && $nPH[2]==1 && $nTurbidity[0]!=0) {
            $ket   = "ph tinggi";  
        }
        elseif ($nTemp[1]!=0 && $nPH[0]==1 && $nTurbidity[1]==1) {
            $ket   = "ph rendah dan kekeruhan tinggi";  
        }
        elseif ($nTemp[1]!=0 && $nPH[1]!=0 && $nTurbidity[1]==1) {
            $ket   = "kekeruhan tinggi";  
        }
        elseif ($nTemp[1]!=0 && $nPH[2]==1 && $nTurbidity[1]==1) {
            $ket   = "ph dan kekeruhan tinggi";  
        }
        elseif ($nTemp[2]==1 && $nPH[0]==1 && $nTurbidity[0]!=0) {
            $ket   = "suhu tinggi dan ph rendah";  
        }
        elseif ($nTemp[2]==1 && $nPH[1]!=0 && $nTurbidity[0]!=0) {
            $ket   = "suhu tinggi";  
        }
        elseif ($nTemp[2]==1 && $nPH[2]==1 && $nTurbidity[0]!=0) {
            $ket   = "suhu dan ph tinggi";  
        }
        elseif ($nTemp[2]==1 && $nPH[0]==1 && $nTurbidity[1]==1) {
            $ket   = "ph rendah, serta suhu dan kekeruhan tinggi"; 
        }
        elseif ($nTemp[2]==1 && $nPH[1]!=0 && $nTurbidity[1]==1) {
            $ket   = "suhu dan kekeruhan tinggi";  }
        
        elseif ($nTemp[2]==1 && $nPH[2]==1 && $nTurbidity[1]==1) {
            $ket   = "suhu, ph, dan kekeruhan tinggi";  
        }

        //output response
        return response()->json([
            'atas' => number_format($atas,2),
            'bawah'=> number_format($bawah,2),
            'rata-rata Z ' => number_format($rZ,2),
            'fuzzy Temp' => $nTemp,
            'fuzzy pH' => $nPH,
            'fuzzy Turb'=> $nTurbidity,
            'aPred'     => [
                'aPred1'  => number_format($aPred1,2),
                'aPred2'  => number_format($aPred2,2),
                'aPred3'  => number_format($aPred3,2),
                'aPred4'  => number_format($aPred4,2),
                'aPred5'  => number_format($aPred5,2),
                'aPred6'  => number_format($aPred6,2),
                'aPred7'  => number_format($aPred7,2),
                'aPred8'  => number_format($aPred8,2),
                'aPred9'  => number_format($aPred9,2),
                'aPred10' => number_format($aPred10,2),
                'aPred11' => number_format($aPred11,2),
                'aPred12' => number_format($aPred12,2),
                'aPred13' => number_format($aPred13,2),
                'aPred14' => number_format($aPred14,2),
                'aPred15' => number_format($aPred15,2),
                'aPred16' => number_format($aPred16,2),
                'aPred17' => number_format($aPred17,2),
                'aPred18' => number_format($aPred18,2),
            ],
            'z'  => [
                'z1 ' => number_format($z1,2),
                'z2 ' => number_format($z2,2),
                'z3 ' => number_format($z3,2),
                'z4 ' => number_format($z4,2),
                'z5 ' => number_format($z5,2),
                'z6 ' => number_format($z6,2),
                'z7 ' => number_format($z7,2),
                'z8 ' => number_format($z8,2),
                'z9 ' => number_format($z9,2),
                'z10' => number_format($z10,2),
                'z11' => number_format($z11,2),
                'z12' => number_format($z12,2),
                'z13' => number_format($z13,2),
                'z14' => number_format($z14,2),
                'z15' => number_format($z15,2),
                'z16' => number_format($z16,2),
                'z17' => number_format($z17,2),
                'z18' => number_format($z18,2),
            ],
        ]);

        // return response()->json([
        //     'nilai z' => number_format($rZ, 2),
        //     'status' => $status,
        //     'keterangan' => $ket,
        //     'fuzzy Temp' => $nTemp,
        //     'fuzzy pH' => $nPH,
        //     'fuzzy Turb'=> $nTurbidity,
        // ]);
    }

    public function create(Request $req){
        $this->monitoring()->create([
            'temperature'=> $req->temperature,
            'ph'         => $req->ph,
            'turbidity'  => $req->turbidity,
            'status'     => $req->status,
            'information'=> $req->information,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Data berhasil disimpan'
        ]);
    }

    public function showData(){
        $data = $this->monitoring()->latest()->first();
        return response()->json([
            'status' => true,
            'data' => $data    
        ]);
    }

    public function showCurrentTemperature(){
        $data = $this->monitoring()->select('temperature', 'created_at')
                    ->whereDate('created_at',  Carbon::today())
                    ->orderBy('created_at','asc')
                    ->get();
        dd($data);
        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }

    public function showCurrentPH(){
        $data = $this->monitoring()->select('ph', 'created_at')
                    ->whereDate('created_at',  Carbon::today())
                    ->orderBy('created_at','asc')
                    ->get();
        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }

    public function showCurrentTurbidity(){
        $data = $this->monitoring()->select('turbidity', 'created_at')
                    ->whereDate('created_at',  Carbon::today())
                    ->orderBy('created_at','asc')
                    ->get();
        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }

    public function showAll(){
        $data = $this->monitoring()
                    ->orderBy('created_at', 'desc')
                    ->get();
        return response()->json([
            'status' => true,
            'data' => $data    
        ]);
    }

}