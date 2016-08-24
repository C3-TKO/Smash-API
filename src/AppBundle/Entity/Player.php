<?php

namespace AppBundle\Entity;

/**
 * Player
 */
class Player
{
    /**
     * @var string
     */
    private $name = '';

    /**
     * @var boolean
     */
    private $id;


    /**
     * Set name
     *
     * @param string $name
     *
     * @return Player
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get id
     *
     * @return boolean
     */
    public function getId()
    {
        return $this->id;
    }
}
