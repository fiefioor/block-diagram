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

    protected $false_blocks = array();
    protected $true_blocks = array();

    /**
     * zwraca id elementu ktory jest na sciezce nie spelnajacej warunku
     *
     * @return mixed
     */
    public function getFalseId()
    {
        return $this->false_id[0];
    }

    /**
     * @return array
     */
    public function getFalseBlocks()
    {
        return $this->false_blocks;
    }

    /**
     * @param array $false_blocks
     */
    public function setFalseBlocks($false_blocks)
    {
        $this->false_blocks = $false_blocks;
    }

    /**
     * @return array
     */
    public function getTrueBlocks()
    {
        return $this->true_blocks;
    }

    /**
     * @param array $true_blocks
     */
    public function setTrueBlocks($true_blocks)
    {
        $this->true_blocks = $true_blocks;
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



}