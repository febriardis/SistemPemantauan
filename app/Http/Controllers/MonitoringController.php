<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Monitoring;

class MonitoringController extends Controller
{
    public function monitoring(){
        return new Monitoring;
    }

    public function create(Request $req){
        $nTemp = [];
        $temperature = $req->temperature;
        $ph          = $req->ph;
        $turbidity   = $req->turbidity;

        //fuzzy
        //Fungsi Keanggotaan Dingin
        // if($temperature <= 26){
        //     $nTemp = 1;
        // }
        // elseif($temperature > 26 || $temperature < 28){
        //     $nTemp = (28 - $temperature)/2;
        // }
        // elseif($temperature >= 28){
        //     $nTemp = 0;
        // }

        //fungsi keanggotaan normal
        // if($temperature >= 30 || $temperature <= 26){
        //     $nTemp = 0;
        // }elseif($temperature > 26 || $temperature < 28){
        //     $nTemp = ()
        // }


        //tsukamoto

        $this->monitoring()->create([
            'temperature'=> $temperature,
            'ph'         => $ph,
            'turbidity'  => $turbidity,
            'status'     => 'coba'
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
//       String postData, temperature, ph, turbidity;
      
//       HTTPClient http;    //Declare object of class HTTPClient
//       //data
//       temperature = "20";
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