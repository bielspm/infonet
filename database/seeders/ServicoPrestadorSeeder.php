<?php

namespace Database\Seeders;

use App\Models\servico;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServicoPrestadorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $idServicos = DB::table('servico')->pluck('id')->toArray();
        $idPrestadores = DB::table('prestador')->pluck('id')->toArray();
        foreach ($idServicos as $idServico) {
            foreach ($idPrestadores as $idPrestador) {
                DB::table('servico_prestador')->insert([
                    'prestador_id' => $idPrestador,
                    'servico_id' => $idServico,
                    'km_saida' => rand(1, 100),
                    'valor_saida' => rand(1, 100),
                    'valor_km_excedente' => rand(1, 100),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
