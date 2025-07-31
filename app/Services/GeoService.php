<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GeoService {

    protected $usuario = "teste-Infornet";
    protected $senha = "c@nsulta-dad0s-ap1-teste-Infornet#24";
    protected $URL = "https://nhen90f0j3.execute-api.us-east-1.amazonaws.com/v1/api/";
    //protected $URL = "https://google.com";

    public function obterCoordenadas($endereco){
        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . base64_encode("$this->usuario:$this->senha"),
            'Content-Type' => 'application/json'
        ])->get($this->URL . 'endereco/geocode/' . $endereco);
        $data = $response->json();
        return $data;
    }

    public function obterPrestadoresOnline($ids){
        return Http::withHeaders([
            'Authorization' => 'Basic ' . base64_encode("$this->usuario:$this->senha"),
        ])->post($this->URL . 'prestadores/online/',$ids);
    }

}
