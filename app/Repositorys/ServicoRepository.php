<?php

namespace App\Repositorys;
use App\Models\servico;

class ServicoRepository {

    public function obterServicos(){
        return Servico::all();
    }

}
