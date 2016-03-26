<?php

/**
 * Created by PhpStorm.
 * User: fiefioor
 * Date: 05.03.16
 * Time: 14:01
 */
abstract class PredicateShape extends BaseShape
{
    protected $false_ids = array();
    protected $true_ids = array();

    /**
     * zwraca id elementu ktory jest na sciezce nie spelnajacej warunku
     *
     * @return mixed
     */
    public function getFalseId()
    {
        return $this->false_ids[0];
    }

    /**
     * @param mixed $false_id
     */
    public function setFalseId($false_ids)
    {
        $this->false_ids = $false_ids;
    }

    public function addFalseId($id){
        $this->false_ids[] = $id;
    }

    /**
     * zwraca id elementu ktory jest na sciezce spelniajacej warunek
     *
     * @return mixed
     */
    public function getTrueId()
    {
        return $this->true_ids[0];
    }

    /**
     * @param mixed $true_id
     */
    public function setTrueId($true_ids)
    {
        $this->true_ids = $true_ids;
    }

    public function addTrueId($id){
        $this->true_ids[] = $id;
    }

    public function preFillTrue()
    {
        // TODO: Implement preFill() method.
    }

    public function preFillFalse()
    {
        // TODO: Implement preFill() method.
    }

    public function fillTrue($code)
    {
        $code[] = $this->preFillTrue();

        return $code;
    }

    public function fillFalse($code)
    {
        $code[] = $this->preFillFalse();

        return $code;
    }


}