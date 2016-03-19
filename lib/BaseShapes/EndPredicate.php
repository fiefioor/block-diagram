<?php

/**
 * Created by PhpStorm.
 * User: fiefioor
 * Date: 19.03.16
 * Time: 12:15
 */
class EndPredicate extends BaseShape
{
    protected $predicate_id = null;

    /**
     * @return null
     */
    public function getPredicateId()
    {
        return $this->predicate_id;
    }

    /**
     * @param null $predicate_id
     */
    public function setPredicateId($predicate_id)
    {
        $this->predicate_id = $predicate_id;
    }

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
        return $code;
    }
}