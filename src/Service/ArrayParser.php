<?php

namespace App\Service;

class ArrayParser{

    public function getArrayValue($array,$key)
    {
        if(isset($array[$key]))
        {
            return $array[$key];
        }

        return null;
    }

}