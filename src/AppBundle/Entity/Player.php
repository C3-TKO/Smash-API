<?php

namespace AppBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation\Link;

/**
 * Player
 * @Link(
 *  "self",
 *  route = "get_player",
 *  params = { "id": "object.getId()" }
 * )
 */
class Player
{
    /**
     * @var string
     * @Assert\NotBlank(message="A player must have a name - except for Jaqen H'ghar - who is actually No one")
     */
    private $name;

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
