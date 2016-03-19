<?php

/**
 * Created by PhpStorm.
 * User: fiefioor
 * Date: 05.03.16
 * Time: 16:38
 */
class COperandShape extends OperandShape
{

    public function preFill()
    {
        // TODO: Implement preFill() method.
    }

    public function postFill()
    {
        // TODO: Implement postFill() method.
    }

    public function fill($code)
    {
            $code[] = $this->getContent().";";
        return $code;
    }
}