<?php

/**
 * Created by PhpStorm.
 * User: fiefioor
 * Date: 05.03.16
 * Time: 13:54
 */
abstract class BaseShape
{
    protected $Content;
    protected $id;

    protected $prev_ids;
    protected $return;

    /**
     * @return mixed
     */
    public function getPrevIds()
    {
        return $this->prev_ids;
    }

    /**
     * @param mixed $prev_ids
     */
    public function setPrevIds($prev_ids)
    {
        $this->prev_ids = $prev_ids;
    }

    public function addPrevId($id){
        $this->prev_ids[] = $id;
    }

    /**
     *
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Pobiera zawartosc elementu
     *
     * @return mixed
     */
    public function getContent()
    {
        return $this->Content;
    }

    /**
     * @param mixed $Content
     */
    public function setContent($Content)
    {
        $this->Content = $Content;
    }

    public function fill(){
        $this->preFill();

    }

    public abstract function preFill();

    public abstract function postFill();

}