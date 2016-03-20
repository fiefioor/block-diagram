<?php

/**
 * Created by PhpStorm.
 * User: fiefioor
 * Date: 20.03.16
 * Time: 11:15
 */
class CEndpredicateShape extends Endpredicate
{

    public function fill($code)
    {
        $code[] = '}';
        return $code;
    }

}