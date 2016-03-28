<?php

/**
 * Created by PhpStorm.
 * User: fiefioor
 * Date: 06.03.16
 * Time: 11:28
 */
class CPredicateShape extends PredicateShape
{

    public function preFill()
    {
        // TODO: Implement preFill() method.

        return "if(".$this->getContent()."){";
    }

    public function preFillTrue()
    {
        // TODO: Implement preFill() method.

        return "if(".$this->getContent()."){";
    }

    public function preFillFalse()
    {
        // TODO: Implement preFill() method.

        return "else{";
    }

    public function fillWhile($code){
        $code[] =  'while('.$this->getContent().'){';

        return $code;
    }

    public function postFill()
    {
        // TODO: Implement postFill() method.

        return "}";
    }

    public function fill($code)
    {
        $code[] = $this->preFill();
//        foreach($this->true_blocks as $true_block){
//            //$code = $this->fill($code);
//        }

        return $code;
    }
}