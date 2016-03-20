<?php

/**
 * Created by PhpStorm.
 * User: fiefioor
 * Date: 20.03.16
 * Time: 11:14
 */
class Endpredicate extends BaseShape
{

    public function preFill()
    {
        // TODO: Implement preFill() method.
    }

    public function postFill()
    {
        // TODO: Implement postFill() method.
    }

    protected $next_id;
    protected $predicate_id;
    protected $visit_count = 0;

    /**
     * @return mixed
     */
    public function getVisitCount()
    {
        return $this->visit_count;
    }

    /**
     * @param mixed $visit_count
     */
    public function setVisitCount($visit_count)
    {
        $this->visit_count = $visit_count;
    }

    public function addVisit(){
        $this->visit_count += 1;
    }

    /**
     * @return mixed
     */
    public function getPredicateId()
    {
        return $this->predicate_id;
    }

    /**
     * @param mixed $predicate_id
     */
    public function setPredicateId($predicate_id)
    {
        $this->predicate_id = $predicate_id;
    }

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

    /**
     * Metoda sprawdzajaca czy mozna isc dalej w schemacie lub jest jeszcze jakas odnoga
     */
    public function CanMoveFoward(){
        if($this->visit_count < count($this->prev_ids)){
            return false;
        }
        else return true;
    }
}