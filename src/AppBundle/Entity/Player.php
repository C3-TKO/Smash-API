<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation\Link;
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * Player
 *
 * @ORM\Table(name="players", indexes={@ORM\Index(name="name", columns={"name"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PlayerRepository")
 * @Hateoas\Relation(
 *     "self",
 *     href=@Hateoas\Route(
 *          "get_player",
 *          parameters={"id"= "expr(object.getId())"}
 *     )
 * )
 */
class Player
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="smallint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=30, nullable=false)
     *
     * @Assert\NotBlank(message="A player must have a name - except for Jaqen H'ghar - who is actually No one")
     */
    private $name;



    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

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
}
