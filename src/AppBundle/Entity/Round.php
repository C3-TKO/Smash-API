<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * Round
 *
 * @ORM\Table(name="rounds", uniqueConstraints={@ORM\UniqueConstraint(name="date", columns={"date"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RoundRepository")
 * @Hateoas\Relation(
 *     "self",
 *     href=@Hateoas\Route(
 *          "get_round",
 *          parameters={"id"= "expr(object.getId())"}
 *     )
 * )
 */
class Round
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
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date", nullable=true)
     * @Serializer\Type("DateTime<'Y-m-d'>")
     */
    private $date;



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
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Round
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }
}
