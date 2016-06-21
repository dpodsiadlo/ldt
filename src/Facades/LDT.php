<?php


namespace DPodsiadlo\Facades;



use Illuminate\Support\Facades\Facade;

class LDT extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \DPodsiadlo\LDT::class;
    }
}