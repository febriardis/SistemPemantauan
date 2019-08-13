<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Monitoring;

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

    //rule base
    // rendah atau tinggi bernilai == 1
        if($nTemp[0]==1 && $nPH[0]==1 && $nTurbidity[0]!=0){
            $ket   = "suhu dan ph rendah";
            $aPred = min($nTemp[0], $nPH[0], $nTurbidity[0]);
            $z     = 100 - (50*$aPred);   
        }//[R1]
        elseif ($nTemp[0]==1 && $nPH[1]!=0 && $nTurbidity[0]!=0) {
            $ket   = "suhu rendah";        
            $aPred = min($nTemp[0], $nPH[1], $nTurbidity[0]);
            $z     = 100 - (50*$aPred);   
        }//[R2]
        elseif ($nTemp[0]==1 && $nPH[2]==1 && $nTurbidity[0]!=0) {
            $ket   = "suhu rendah, dan ph tinggi";   
            $aPred = min($nTemp[0], $nPH[2], $nTurbidity[0]);
            $z     = 100 - (50*$aPred);          
        }//[R3]
        elseif ($nTemp[0]==1 && $nPH[0]==1 && $nTurbidity[1]==1) {
            $ket   = "suhu dan ph rendah, serta kekeruhan tinggi"; 
            $aPred = min($nTemp[0], $nPH[0], $nTurbidity[1]);
            $z     = 100 - (50*$aPred);           
        }//[R4]
        elseif ($nTemp[0]==1 && $nPH[1]!=0 && $nTurbidity[1]==1) {
            $ket   = "suhu rendah dan kekeruhan tinggi";   
            $aPred = min($nTemp[0], $nPH[1], $nTurbidity[1]);
            $z     = 100 - (50*$aPred);        
        }//[R5]
        elseif ($nTemp[0]==1 && $nPH[2]==1 && $nTurbidity[1]==1) {
            $ket   = "suhu rendah, ph dan kekeruhan tinggi";  
            $aPred = min($nTemp[0], $nPH[2], $nTurbidity[1]);
            $z     = 100 - (50*$aPred);          
        }//[R6]
        elseif ($nTemp[1]!=0 && $nPH[0]==1 && $nTurbidity[0]!=0) {
            $ket   = "ph rendah";   
            $aPred = min($nTemp[1], $nPH[0], $nTurbidity[0]);
            $z     = 100 - (50*$aPred);      
        }//[R7]
        elseif ($nTemp[1]!=0 && $nPH[1]!=0 && $nTurbidity[0]!=0) {
            $ket   = "nilai parameter normal";  
            $aPred = min($nTemp[1], $nPH[1], $nTurbidity[0]);
            $z     = abs(1 - ($aPred*50)); 
        }//[R8]
        elseif ($nTemp[1]!=0 && $nPH[2]==1 && $nTurbidity[0]!=0) {
            $ket   = "ph tinggi";  
            $aPred = min($nTemp[1], $nPH[2], $nTurbidity[0]);
            $z     = 100 - (50*$aPred); 
        }//[R9]
        elseif ($nTemp[1]!=0 && $nPH[0]==1 && $nTurbidity[1]==1) {
            $ket   = "ph rendah dan kekeruhan tinggi";  
            $aPred = min($nTemp[1], $nPH[0], $nTurbidity[1]);
            $z     = 100 - (50*$aPred); 
        }//[R10]
        elseif ($nTemp[1]!=0 && $nPH[1]!=0 && $nTurbidity[1]==1) {
            $ket   = "kekeruhan tinggi";  
            $aPred = min($nTemp[1], $nPH[1], $nTurbidity[1]);
            $z     = 100 - (50*$aPred); 
        }//[R11]
        elseif ($nTemp[1]!=0 && $nPH[2]==1 && $nTurbidity[1]==1) {
            $ket   = "ph dan kekeruhan tinggi";  
            $aPred = min($nTemp[1], $nPH[2], $nTurbidity[1]);
            $z     = 100 - (50*$aPred); 
        }//[R12]
        elseif ($nTemp[2]==1 && $nPH[0]==1 && $nTurbidity[0]!=0) {
            $ket   = "suhu tinggi dan ph rendah";  
            $aPred = min($nTemp[2], $nPH[0], $nTurbidity[0]);
            $z     = 100 - (50*$aPred); 
        }//[R13]
        elseif ($nTemp[2]==1 && $nPH[1]!=0 && $nTurbidity[0]!=0) {
            $ket   = "suhu tinggi";  
            $aPred = min($nTemp[2], $nPH[1], $nTurbidity[0]);
            $z     = 100 - (50*$aPred); 
        }//[R14]
        elseif ($nTemp[2]==1 && $nPH[2]==1 && $nTurbidity[0]!=0) {
            $ket   = "suhu dan ph tinggi";  
            $aPred = min($nTemp[2], $nPH[2], $nTurbidity[0]);
            $z     = 100 - (50*$aPred); 
        }//[R15]
        elseif ($nTemp[2]==1 && $nPH[0]==1 && $nTurbidity[1]==1) {
            $ket   = "ph rendah, serta suhu dan kekeruhan tinggi";  
            $aPred = min($nTemp[2], $nPH[0], $nTurbidity[1]);
            $z     = 100 - (50*$aPred); 
        }//[R16]
        elseif ($nTemp[2]==1 && $nPH[1]!=0 && $nTurbidity[1]==1) {
            $ket   = "suhu dan kekeruhan tinggi";  
            $aPred = min($nTemp[2], $nPH[1], $nTurbidity[1]);
            $z     = 100 - (50*$aPred); 
        }//[R17]
        elseif ($nTemp[2]==1 && $nPH[2]==1 && $nTurbidity[1]==1) {
            $ket   = "suhu, ph, dan kekeruhan tinggi";  
            $aPred = min($nTemp[2], $nPH[2], $nTurbidity[1]);
            $z     = 100 - (50*$aPred); 
        }//[R18]

        // defuzifikasi
        $zT = ($aPred*$z)/$aPred;

        // keterangan
        if($zT>=0 && $zT<50){
            $status = "air masih layak!!";
        }elseif($zT >= 50){
            $status = "air tidak layak !!";
        }

        // return response()->json([
        //     // 'aPred' => $aPred,
        //     // 'Z' => $z,
        //     'nilai z' => number_format($zT, 2),
        //     'status' => $status,
        //     'keterangan' => $ket,
        //     'fuzzy Temp' => $nTemp,
        //     'fuzzy pH' => $nPH,
        //     'fuzzy Turb'=> $nTurbidity,
        // ]);

        $this->monitoring()->create([
            'temperature'=> $temperature,
            'ph'         => $ph,
            'turbidity'  => $turbidity,
            'status'     => $status,
            'information'=> $ket,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Data berhasil disimpan'
        ]);
        
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

    public function showCurrentData(){
        // $data = $this->monitoring()->where('created_at')->all();
        // return response()->json([
        //     'status' => true,
        //     'data' => $data    
        // ]);
    }

    public function showAll(){
        $data = $this->monitoring()->orderBy('created_at','desc')->get();
        return response()->json([
            'status' => true,
            'data' => $data    
        ]);
    }

}


// #include <ESP8266HTTPClient.h>
// #include <ESP8266WiFi.h>
 
// void setup() {
 
//     Serial.begin(115200);                                  //Serial connection
//     WiFi.begin("GranadaBoys", "bayardulu5000");   //WiFi connection
   
//     while (WiFi.status() != WL_CONNECTED) {  //Wait for the WiFI connection completion
   
//       delay(500);
//       Serial.println("Waiting for connection");
   
//     }
   
//   }
   
//   void loop() {
//     if(WiFi.status()== WL_CONNECTED){   //Check WiFi connection status
//       String postData; 
        // float temperature; 
        // float ph; 
        // int turbidity;
      
//       HTTPClient http;    //Declare object of class HTTPClient
//       //data
//       temperature = String(20);
//       ph = "21";
//       turbidity = "22";
      
//       //post data
//       postData = "temperature="+temperature+"&ph="+ph+"&turbidity="+turbidity;
      
//       http.begin("http://sip.billionairecoach.co.id/api/create");      //Specify request destination
//       http.addHeader("Content-Type", "application/x-www-form-urlencoded");  //Specify content-type header
      
//       int httpCode = http.POST(postData);   //Send the request
//       String payload = http.getString();                  //Get the response payload
      
//       Serial.println(httpCode);   //Print HTTP return code
//       Serial.println(payload);    //Print request response payload
      
//       http.end();  //Close connection
   
//    }else{
   
//       Serial.println("Error in WiFi connection");   
   
//    }
   
//     delay(30000);  //Send a request every 30 seconds
   
//   }