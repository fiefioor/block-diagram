<?php

/**
 * Created by PhpStorm.
 * User: fiefioor
 * Date: 05.03.16
 * Time: 14:01
 */
class PredicateShape extends BaseShape
{
    protected $false_id;
    protected $true_id;

    /**
     * zwraca id elementu ktory jest na sciezce nie spelnajacej warunku
     *
     * @return mixed
     */
    public function getFalseId()
    {
        return $this->false_id;
    }

    /**
     * @param mixed $false_id
     */
    public function setFalseId($false_id)
    {
        $this->false_id = $false_id;
    }

    /**
     * zwraca id elementu ktory jest na sciezce spelniajacej warunek
     *
     * @return mixed
     */
    public function getTrueId()
    {
        return $this->true_id;
    }

    /**
     * @param mixed $true_id
     */
    public function setTrueId($true_id)
    {
        $this->true_id = $true_id;
    }



}