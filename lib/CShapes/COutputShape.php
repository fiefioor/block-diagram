<?php

/**
 * Created by PhpStorm.
 * User: fiefioor
 * Date: 05.03.16
 * Time: 14:46
 */
class COutputShape extends OutputShape
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
        parent::fill(); // TODO: Change the autogenerated stub
        //return "printf(\"%d %s %f %c\n\", dec, str, pi,  ch);";
        $code[] = "std::cout << ".$this->getContent().";";
        return $code;
    }

}