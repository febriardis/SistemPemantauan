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
        $info_temp   = "";
        $info_ph     = "";
        $info_turb   = "";

        $temperature = $req->temperature;
        $ph          = $req->ph;
        $turbidity   = $req->turbidity;

    // =============== Fuzzy Logic =================
    // ---------------- variabel suhu ------------------
        // fungsi keanggotaan suhu dingin
        if($temperature < 26){
            $nTemp[0] = 1;
        }
        elseif($temperature >= 26 && $temperature <= 27){
            $nTemp[0] = (27-$temperature)/(27-26);
        }
        elseif($temperature > 27){
            $nTemp[0] = 0;
        }

        // fungsi keanggotaan suhu normal
        if($temperature >= 30 || $temperature <= 26){
            $nTemp[1] = 0;
        }
        elseif($temperature > 26 && $temperature < 27){
            $nTemp[1] = ($temperature-26)/(27-26);
        }
        elseif($temperature > 28 && $temperature < 30){
            $nTemp[1] = (30-$temperature)/(30-29);
        }
        elseif($temperature >= 27 && $temperature <= 29){
            $nTemp[1] = 1;
        }

        //fungsi keanggotaaan suhu panas
        if($temperature <= 29){
            $nTemp[2] = 0;
        }
        elseif($temperature > 29 && $temperature <= 30){
            $nTemp[2] = ($temperature-29)/(30-29);
        }
        elseif ($temperature > 30) {
            $nTemp[2] = 1;
        }

    //------------------ Variabel pH -----------------
        //fungsi keanggotaan ph rendah
        if($ph < 6.5){
            $nPH[0] = 1;
        }
        elseif ($ph >= 6.5 && $ph <= 6.6) {
            $nPH[0] = (6.6-$ph)/(6.6-6.5);
        }
        elseif ($ph > 6.6) {
            $nPH[0] = 0;
        }

        //fungsi keanggotaan ph normal
        if($ph >= 7 || $ph <= 6.5){
            $nPH[1] = 0;
        }
        elseif($ph >= 6.5 && $ph <= 6.6){
            $nPH[1] = ($ph-6.5)/(6.6-6.5);
        }
        elseif($ph >= 6.9 && $ph <= 7){
            $nPH[1] = (7 - $ph) / (7-6.9);
        }
        elseif($ph > 6.6 && $ph < 6.9){
            $nPH[1] = 1;
        }

        //fungsi keanggotaan ph tinggi
        if($ph < 6.9){
            $nPH[2] = 0;
        }
        elseif ($ph >= 6.9 && $ph <= 7){
            $nPH[2] = ($ph-6.9)/(7-6.9);
        }
        elseif ($ph > 7){ 
            $nPH[2] = 1;
        }
    
    //------------------ Variabel Kekeruhan -----------------
        //fungsi keanggotaan kekeruhan normal
        if($turbidity >= 25){
            $nTurbidity[0] = 0;
        }
        elseif ($turbidity > 0 && $turbidity < 25) {
            $nTurbidity[0] = 1;//(25 - $turbidity) / 25;
        }
        elseif ($turbidity <= 0) {
            $nTurbidity[0] = 1;
        }

    //fungsi keanggotaan kekeruhan tinggi
        if($turbidity <= 0){
            $nTurbidity[1] = 0;
        }
        elseif ($turbidity > 0 && $turbidity < 25) {
            $nTurbidity[1] = 0;//($turbidity - 0) / 25;
        }
        elseif ($turbidity >= 25) {
            $nTurbidity[1] = 1;
        }

        //inferensi dengan implikasi MIN
        //[R1]
        $aPred1 = min($nTemp[0], $nPH[0], $nTurbidity[0]); //tidak layak
        $z1 = 1 + (50*$aPred1); //tidak layak
        //[R2]
        $aPred2 = min($nTemp[0], $nPH[1], $nTurbidity[0]); //tidak layak
        $z2 = 1 + (50*$aPred2); //tidak layak
        //[R3]
        $aPred3 = min($nTemp[0], $nPH[2], $nTurbidity[0]); //tidak layak
        $z3 = 1 + (50*$aPred3); //tidak layak
        //[R4]
        $aPred4 = min($nTemp[0], $nPH[0], $nTurbidity[1]); //tidak layak
        $z4 = 1 + (50*$aPred4); //tidak layak
        //[R5]
        $aPred5 = min($nTemp[0], $nPH[1], $nTurbidity[1]); //tidak layak
        $z5 = 1 + (50*$aPred5); //tidak layak
        //[R6]
        $aPred6 = min($nTemp[0], $nPH[2], $nTurbidity[1]); //tidak layak
        $z6 = 1 + (50*$aPred6); //tidak layak
        //[R7]
        $aPred7 = min($nTemp[1], $nPH[0], $nTurbidity[0]); //tidak layak
        $z7 = 1 + (50*$aPred7); //tidak layak
        //[R8]
        $aPred8 = min($nTemp[1], $nPH[1], $nTurbidity[0]); //layak
        $z8 = 50 - (49*$aPred8); //layak
        //[R9]
        $aPred9 = min($nTemp[1], $nPH[2], $nTurbidity[0]); //tidak layak
        $z9 = 1 + (50*$aPred9); //tidak layak
        //[R10]
        $aPred10 = min($nTemp[1], $nPH[0], $nTurbidity[1]); //tidak layak
        $z10 = 1 + (50*$aPred10); //tidak layak
        //[R11]
        $aPred11 = min($nTemp[1], $nPH[1], $nTurbidity[1]); //tidak layak
        $z11 = 1 + (50*$aPred11); //tidak layak
        //[R12]
        $aPred12 = min($nTemp[1], $nPH[2], $nTurbidity[1]); //tidak layak
        $z12 = 1 + (50*$aPred12); //tidak layak
        //[R13]
        $aPred13 = min($nTemp[2], $nPH[0], $nTurbidity[0]); //tidak layak
        $z13 = 1 + (50*$aPred13); //tidak layak
        //[R14]
        $aPred14 = min($nTemp[2], $nPH[1], $nTurbidity[0]); //tidak layak
        $z14 = 1 + (50*$aPred14); //tidak layak
        //[R15]
        $aPred15 = min($nTemp[2], $nPH[2], $nTurbidity[0]); //tidak layak
        $z15 = 1 + (50*$aPred15); //tidak layak
        //[R16]
        $aPred16 = min($nTemp[2], $nPH[0], $nTurbidity[1]); //tidak layak
        $z16 = 1 + (50*$aPred16); //tidak layak
        //[R17]
        $aPred17 = min($nTemp[2], $nPH[1], $nTurbidity[1]); //tidak layak
        $z17 = 1 + (50*$aPred17); //tidak layak
        //[R18]
        $aPred18 = min($nTemp[2], $nPH[2], $nTurbidity[1]); //tidak layak
        $z18 = 1 + (50*$aPred18); //tidak layak
        
        //defuzifikasi / menghitung rata-rata
        $atas  = ($aPred1*$z1)+($aPred2*$z2)+($aPred3*$z3)+($aPred4*$z4)+($aPred5*$z5)+($aPred6*$z6)+($aPred7*$z7)+($aPred8*$z8)+($aPred9*$z9)+($aPred10*$z10)+($aPred11*$z11)+($aPred12*$z12)+($aPred13*$z13)+($aPred14*$z14)+($aPred15*$z15)+($aPred16*$z16)+($aPred17*$z17)+($aPred18*$z18);
        $bawah = $aPred1+$aPred2+$aPred3+$aPred4+$aPred5+$aPred6+$aPred7+$aPred8+$aPred9+$aPred10+$aPred11+$aPred12+$aPred13+$aPred14+$aPred15+$aPred16+$aPred17+$aPred18;
        $rZ    = $atas/$bawah;

        // keterangan
        if($rZ>=0 && $rZ<50){
            $status = "air masih layak!!";
            $ket    = "nilai parameter normal";
        }elseif($rZ >= 50){
            $status = "air tidak layak !!"; 
            //informasi
            if($temperature<=26){
                $info_temp = "suhu rendah, ";
            }elseif($temperature >=30){
                $info_temp = "suhu tinggi, ";
            }
            if($ph<=6.5){
                $info_ph = "ph rendah, ";
            }elseif($ph>=7){
                $info_ph = "ph tinggi, ";
            }
            if($turbidity>=25){
                $info_turb = "kekeruhan tinggi";
            }
            $ket = $info_temp.$info_ph.$info_turb;
        }

        return response()->json([
            'nilai z' => number_format($rZ, 2),
            'status' => $status,
            'keterangan' => $ket,
            'fuzzy Temp' => $nTemp,
            'fuzzy pH' => $nPH,
            'fuzzy Turb'=> $nTurbidity,
        ]);

        // $this->monitoring()->create([
        //     'temperature'=> $temperature,
        //     'ph'         => $ph,
        //     'turbidity'  => $turbidity,
        //     'status'     => $status,
        //     'information'=> $ket,
        // ]);
        // return response()->json([
        //     'status' => true,
        //     'message' => 'Data berhasil disimpan'
        // ]);

        // return response()->json([
        //     'atas' => number_format($atas,2),
        //     'bawah'=> number_format($bawah,2),
        //     'rata-rata Z ' => number_format($rZ,2),
        //     'fuzzy Temp' => $nTemp,
        //     'fuzzy pH' => $nPH,
        //     'fuzzy Turb'=> $nTurbidity,
        //     'aPred'     => [
        //         'aPred1'  => number_format($aPred1,2),
        //         'aPred2'  => number_format($aPred2,2),
        //         'aPred3'  => number_format($aPred3,2),
        //         'aPred4'  => number_format($aPred4,2),
        //         'aPred5'  => number_format($aPred5,2),
        //         'aPred6'  => number_format($aPred6,2),
        //         'aPred7'  => number_format($aPred7,2),
        //         'aPred8'  => number_format($aPred8,2),
        //         'aPred9'  => number_format($aPred9,2),
        //         'aPred10' => number_format($aPred10,2),
        //         'aPred11' => number_format($aPred11,2),
        //         'aPred12' => number_format($aPred12,2),
        //         'aPred13' => number_format($aPred13,2),
        //         'aPred14' => number_format($aPred14,2),
        //         'aPred15' => number_format($aPred15,2),
        //         'aPred16' => number_format($aPred16,2),
        //         'aPred17' => number_format($aPred17,2),
        //         'aPred18' => number_format($aPred18,2),
        //     ],
        //     'z'  => [
        //         'z1 ' => number_format($z1,2),
        //         'z2 ' => number_format($z2,2),
        //         'z3 ' => number_format($z3,2),
        //         'z4 ' => number_format($z4,2),
        //         'z5 ' => number_format($z5,2),
        //         'z6 ' => number_format($z6,2),
        //         'z7 ' => number_format($z7,2),
        //         'z8 ' => number_format($z8,2),
        //         'z9 ' => number_format($z9,2),
        //         'z10' => number_format($z10,2),
        //         'z11' => number_format($z11,2),
        //         'z12' => number_format($z12,2),
        //         'z13' => number_format($z13,2),
        //         'z14' => number_format($z14,2),
        //         'z15' => number_format($z15,2),
        //         'z16' => number_format($z16,2),
        //         'z17' => number_format($z17,2),
        //         'z18' => number_format($z18,2),
        //     ],
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


// #include<SoftwareSerial.h>
// #include<ArduinoJson.h>
// #include<OneWire.h>
// #include<DallasTemperature.h>
// #include<LiquidCrystal_I2C.h>
// #include<Wire.h>
// #include <elapsedMillis.h>

// elapsedMillis sincePrint1, sincePrint2;
// LiquidCrystal_I2C lcd(0x27,2,1,0,4,5,6,7,3,POSITIVE);                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             
// SoftwareSerial S_SERIAL(3,5);

// #define ONE_WIRE_BUS 2
// OneWire oneWire(ONE_WIRE_BUS);
// DallasTemperature sensorSuhu(&oneWire);

// const int PIN_PH = A0;
// const int PIN_TURB = A1;
// const int pinSaklar = 6;
// const int pinLed = 4;
// const int pinLed2 = 7;
// int pinBuzzer = 8;
// int kondisiSaklar = 0;
// float ph, temperature, turbidity;
// String ket,status_air;

// float nilTemp = 0;
// int adcPH = 0;
// int adcTurb = 0;

// void setup() {
//   // put your setup code here, to run once:
//   Serial.begin(9600);
//   S_SERIAL.begin(9600);
//   sensorSuhu.begin();

//   pinMode(pinSaklar, INPUT);
//   pinMode(3, INPUT);
//   pinMode(5, OUTPUT);
//   pinMode(pinLed, OUTPUT);
//   pinMode(pinLed2, OUTPUT);
//   pinMode(pinBuzzer, OUTPUT);

//   //lcd
//   lcd.begin(20, 4);
// }

// void loop() {
//   kondisiSaklar = digitalRead(pinSaklar);
//   if(kondisiSaklar == HIGH){
//     digitalWrite(pinLed, LOW);
//     Serial.println("lampu nyala");
//     get_data(); //get function get data from sensor
//     fuzzy_logic(); //get function fuzzy logic
//     delay(200); 
//   }else{
//     digitalWrite(pinLed, HIGH);
//     if (sincePrint1 > 5000){     //eksekusi per 5 detik
//       // ------ turbidity sensor code --------
//       sincePrint1 = 0;
//       sensorSuhu.requestTemperatures();
//       nilTemp = nilTemp + sensorSuhu.getTempCByIndex(0);
//       adcPH = adcPH + analogRead(PIN_PH);
//       adcTurb = adcTurb + analogRead(PIN_TURB);
//     }
//     else if(sincePrint2 > 30000){ //eksekusi per 30 detik
//       Serial.println("lampu mati");
//       sincePrint2 = 0;
    
//       temperature = nilTemp / 6;
      
//       int adcP = adcPH / 6;
//       float vol_ph = 5 / 1023.0 * adcP;
//       ph = 7 + ((2.5 - vol_ph)/0.18);
      
//       int adcT = adcTurb/6;
//       float vol_turb = adcT * (5.0 / 1024);
//       turbidity = 100.0 - (vol_turb / 4.25) * 100.00;
      
//       nilTemp = 0;
//       adcTurb = 0;
//       adcPH = 0;
      
//       fuzzy_logic(); //memanggil fungsi fuzzy logic
//     }
//   }
// }

// void get_data(){
//   // ------ ph sensor code ----------
//   int adcPH = analogRead(PIN_PH);
//   float vol_ph = 5 / 1023.0 * adcPH;
//   ph = 7 + ((2.5 - vol_ph)/0.18);

//   // ------ temperature sensor code -------
//   sensorSuhu.requestTemperatures();
//   temperature = sensorSuhu.getTempCByIndex(0);
  
//   // ------ turbidity sensor code --------
//   int val = analogRead(PIN_TURB);
//   float vol_turb = val * (5.0 / 1024);
//   turbidity = 100.0 - (vol_turb / 4.25) * 100.00;
// }

// void fuzzy_logic(){
//   float nTemp[3];
//   float nPH[3];
//   float nTurbidity[3];
//   float aPred, z;
   
//   // ---------------- fuzzy logic code -------------------
//   // Fuzifikasi
//   // ---------------- variabel suhu ------------------
//   // fungsi keanggotaan suhu dingin
//   if(temperature < 26){
//       nTemp[0] = 1;
//   }
//   else if(temperature >= 26 && temperature <= 28){
//       nTemp[0] = (28 - temperature)/2;
//   }
//   else if(temperature > 28){
//       nTemp[0] = 0;
//   }
  
//   // fungsi keanggotaan suhu normal
//   if(temperature > 30 || temperature < 26){
//       nTemp[1] = 0;
//   }
//   else if(temperature >= 26 && temperature <= 28){
//       nTemp[1] = (temperature - 26) / 2;
//   }
//   else if(temperature > 28 && temperature <= 30){
//       nTemp[1] = (30 - temperature) / 2;
//   }
  
//   //fungsi keanggotaaan suhu panas
//   if(temperature <= 28){
//       nTemp[2] = 0;
//   }else if(temperature > 28 && temperature <= 30){
//       nTemp[2] = (temperature-28)/2;
//   }
//   else if (temperature > 30){
//       nTemp[2] = 1;
//   }

//   //------------------ Variabel pH -----------------
//   //fungsi keanggotaan ph rendah
//   if(ph < 6.5){
//       nPH[0] = 1;
//   }
//   else if (ph >= 6.5 && ph <= 6.8) {
//       nPH[0] = (6.8 - ph) / 0.3;
//   }
//   else if (ph > 6.8) {
//       nPH[0] = 0;
//   }
//   //fungsi keanggotaan ph normal
//   if(ph > 7 || ph < 6.5){
//       nPH[1] = 0;
//   }
//   else if(ph >= 6.5 && ph <= 6.8){
//       nPH[1] = (ph - 6.5) / 0.3;
//   }
//   else if(ph > 6.8 && ph <= 7){
//       nPH[1] = (7 - ph) / 0.2;
//   }
  
//   //fungsi keanggotaan ph tinggi
//   if(ph <= 6.8){
//       nPH[2]=0;
//   }
//   else if (ph>6.8 && ph<7){
//       nPH[2]=(ph-6.8)/0.3;
//   }
//   else if (ph>=7){ 
//       nPH[2]=1;
//   }
  
//   //------------------ Variabel Kekeruhan -----------------
//   //fungsi keanggotaan kekeruhan normal
//   if(turbidity > 25){
//       nTurbidity[0] = 0;
//   }
//   else if (turbidity > 0 && turbidity <= 25){
//       nTurbidity[0] = (25 - turbidity) / 25;
//   }
//   else if (turbidity <= 0){
//       nTurbidity[0] = 1;
//   }
  
//   //fungsi keanggotaan kekeruhan tinggi
//   if(turbidity <= 0){
//       nTurbidity[1] = 0;
//   }
//   else if (turbidity > 0 && turbidity <= 25){
//       nTurbidity[1] = (turbidity - 0) / 25;
//   }
//   else if (turbidity >= 25){
//       nTurbidity[1] = 1;
//   }

//   // rule base
//   if(nTemp[0]==1 && nPH[0]==1 && nTurbidity[0]!=0){
//       ket   = "suhu dan ph rendah";
//       aPred = min(nTemp[0], min(nPH[0], nTurbidity[0]));
//       z     = 100 - (50*aPred);   
//   }//[R1]
//   else if (nTemp[0]==1 && nPH[1]!=0 && nTurbidity[0]!=0) {
//       ket   = "suhu rendah";        
//       aPred = min(nTemp[0], min(nPH[1], nTurbidity[0]));
//       z     = 100 - (50*aPred);   
//   }//[R2]
//   else if (nTemp[0]==1 && nPH[2]==1 && nTurbidity[0]!=0) {
//       ket   = "suhu rendah, dan ph tinggi";   
//       aPred = min(nTemp[0], min(nPH[2], nTurbidity[0]));
//       z     = 100 - (50*aPred);          
//   }//[R3]
//   else if (nTemp[0]==1 && nPH[0]==1 && nTurbidity[1]==1) {
//       ket   = "suhu dan ph rendah, serta kekeruhan tinggi"; 
//       aPred = min(nTemp[0], min(nPH[0], nTurbidity[1]));
//       z     = 100 - (50*aPred);           
//   }//[R4]
//   else if (nTemp[0]==1 && nPH[1]!=0 && nTurbidity[1]==1) {
//       ket   = "suhu rendah dan kekeruhan tinggi";   
//       aPred = min(nTemp[0], min(nPH[1], nTurbidity[1]));
//       z     = 100 - (50*aPred);        
//   }//[R5]
//   else if (nTemp[0]==1 && nPH[2]==1 && nTurbidity[1]==1) {
//       ket   = "suhu rendah, ph dan kekeruhan tinggi";  
//       aPred = min(nTemp[0], min(nPH[2], nTurbidity[1]));
//       z     = 100 - (50*aPred);          
//   }//[R6]
//   else if (nTemp[1]!=0 && nPH[0]==1 && nTurbidity[0]!=0) {
//       ket   = "ph rendah";   
//       aPred = min(nTemp[1], min(nPH[0], nTurbidity[0]));
//       z     = 100 - (50*aPred);      
//   }//[R7]
//   else if (nTemp[1]!=0 && nPH[1]!=0 && nTurbidity[0]!=0) {
//       ket   = "nilai parameter normal";  
//       aPred = min(nTemp[1], min(nPH[1], nTurbidity[0]));
//       z     = abs(1 - (aPred*50)); 
//   }//[R8]
//   else if (nTemp[1]!=0 && nPH[2]==1 && nTurbidity[0]!=0) {
//       ket   = "ph tinggi";  
//       aPred = min(nTemp[1], min(nPH[2], nTurbidity[0]));
//       z     = 100 - (50*aPred); 
//   }//[R9]
//   else if (nTemp[1]!=0 && nPH[0]==1 && nTurbidity[1]==1) {
//       ket   = "ph rendah dan kekeruhan tinggi";  
//       aPred = min(nTemp[1], min(nPH[0], nTurbidity[1]));
//       z     = 100 - (50*aPred); 
//   }//[R10]
//   else if (nTemp[1]!=0 && nPH[1]!=0 && nTurbidity[1]==1) {
//       ket   = "kekeruhan tinggi";  
//       aPred = min(nTemp[1], min(nPH[1], nTurbidity[1]));
//       z     = 100 - (50*aPred); 
//   }//[R11]
//   else if (nTemp[1]!=0 && nPH[2]==1 && nTurbidity[1]==1) {
//       ket   = "ph dan kekeruhan tinggi";  
//       aPred = min(nTemp[1], min(nPH[2], nTurbidity[1]));
//       z     = 100 - (50*aPred); 
//   }//[R12]
//   else if (nTemp[2]==1 && nPH[0]==1 && nTurbidity[0]!=0) {
//       ket   = "suhu tinggi dan ph rendah";  
//       aPred = min(nTemp[2], min(nPH[0], nTurbidity[0]));
//       z     = 100 - (50*aPred); 
//   }//[R13]
//   else if (nTemp[2]==1 && nPH[1]!=0 && nTurbidity[0]!=0) {
//       ket   = "suhu tinggi";  
//       aPred = min(nTemp[2], min(nPH[1], nTurbidity[0]));
//       z     = 100 - (50*aPred); 
//   }//[R14]
//   else if (nTemp[2]==1 && nPH[2]==1 && nTurbidity[0]!=0) {
//       ket   = "suhu dan ph tinggi";  
//       aPred = min(nTemp[2], min(nPH[2], nTurbidity[0]));
//       z     = 100 - (50*aPred); 
//   }//[R15]
//   else if (nTemp[2]==1 && nPH[0]==1 && nTurbidity[1]==1) {
//       ket   = "ph rendah, serta suhu dan kekeruhan tinggi";  
//       aPred = min(nTemp[2], min(nPH[0], nTurbidity[1]));
//       z     = 100 - (50*aPred); 
//   }//[R16]
//   else if (nTemp[2]==1 && nPH[1]!=0 && nTurbidity[1]==1) {
//       ket   = "suhu dan kekeruhan tinggi";  
//       aPred = min(nTemp[2], min(nPH[1], nTurbidity[1]));
//       z     = 100 - (50*aPred); 
//   }//[R17]
//   else if (nTemp[2]==1 && nPH[2]==1 && nTurbidity[1]==1) {
//       ket   = "suhu, ph, dan kekeruhan tinggi";  
//       aPred = min(nTemp[2], min(nPH[2], nTurbidity[1]));
//       z     = 100 - (50*aPred); 
//   }//[R18]
  
//   // defuzifikasi
//   int zT = (aPred*z)/aPred;
  
//   // keterangan
//   if(zT>=0 && zT<50){
//       status_air = "air masih layak!!";
//   }else if(zT >= 50){
//       status_air = "air tidak layak !!";
// //      tone(pinBuzzer, 300, 10000);
//   }
    
//   post_mcu(); //get function post data too nodemcu
//   show_lcd(); //get function show data in lcd
// }

// //function post data too nodemcu
// void post_mcu(){
//   // ------ arduino json code ----------
//   const size_t capacity = JSON_OBJECT_SIZE(3);
//   DynamicJsonBuffer jsonBuffer(capacity);
  
//   JsonObject& root = jsonBuffer.createObject();
//   root["temperature"] = temperature;
//   root["ph"] = ph;
//   root["turbidity"] = turbidity; 
//   root["status"] = status_air;
//   root["information"] = ket;
//   root.prettyPrintTo(Serial);
//   root.printTo(S_SERIAL);

// //  menyalakan lampu led
//   digitalWrite(pinLed, LOW);
//   delay(200);
//   digitalWrite(pinLed, HIGH);
//   delay(200);
// }

// //function show data in lcd
// void show_lcd(){
//   //show data in LCD
//   lcd.setCursor(0,0);
//   lcd.print("Suhu:");
//   lcd.print(temperature,2);
//   lcd.print(", pH:");
//   lcd.print(ph,2);
//   lcd.setCursor(0,1);
//   lcd.print("Kekeruhan:");
//   lcd.print(turbidity);                                                                  
//   lcd.setCursor(0,2); 
//   lcd.print("=====STATUS AIR=====");
//   lcd.setCursor(0,3);
//   lcd.print(status_air);
// }