<?php

/**
 * Created by PhpStorm.
 * User: fiefioor
 * Date: 05.03.16
 * Time: 13:59
 */
abstract class OperandShape extends BaseShape
{
    protected $next_id;

    /**
     * Pobieranie Id nastepnego elementu
     *
     * @return mixed
     */
    public function getNextId()
    {
        return $this->next_id;
    }

    /**
     * @param mixed $next_id
     */
    public function setNextId($next_id)
    {
        $this->next_id = $next_id;
    }

}