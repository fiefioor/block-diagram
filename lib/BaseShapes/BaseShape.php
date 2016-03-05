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

    protected $prev_id;

    /**
     * @return mixed
     */
    public function getPrevId()
    {
        return $this->prev_id;
    }

    /**
     * @param mixed $prev_id
     */
    public function setPrevId($prev_id)
    {
        $this->prev_id = $prev_id;
    }

    /**
     * Pobiera Id poprzedniego Elementu
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

}