<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SendReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envia el reporte.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(){
        $this->info('Comenzando el envío.');
        $products = \Solunes\Product\App\Product::where('id',517)->get();
        $count = 0;
        foreach($products as $product){
            $product->updated_at = NULL;
            $product->touch();
        }
        /*$this->info('Comenzando el envío.');
        $products = \Solunes\Product\App\Product::get();
        $count = 0;
        foreach($products as $product){
            $product->touch();
            $product->save();
            $count++;
        }*/
        $this->info('Se eliminaron '.$count.' items.');
    }
}
