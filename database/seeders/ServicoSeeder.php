<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServicoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $servicos = [
            [
                'nome' => 'troca de pneu',
                'situacao' => true,
            ],
            [
                'nome' => 'reboque',
                'situacao' => true,
            ],
            [
                'nome' => 'reabastecimento de gasolina',
                'situacao' => true,
            ]
        ];
        DB::table('servico')->insert($servicos);
    }
}
