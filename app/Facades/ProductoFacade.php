<?php 

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class ProductoFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'App\Services\ProductoService';
    }
}