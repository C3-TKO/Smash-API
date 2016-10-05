<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\Player;

/**
 * Teams
 *
 * @ORM\Table(name="teams", uniqueConstraints={@ORM\UniqueConstraint(name="team_combination", columns={"id_player_a", "id_player_b"})}, indexes={@ORM\Index(name="IDX_team_id_2_player_a_id", columns={"id_player_a"}), @ORM\Index(name="IDX_team_id_2_player_b_id", columns={"id_player_b"})})
 * @ORM\Entity
 */
class Teams
{
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=45, nullable=true)
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var Player
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Player")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_player_b", referencedColumnName="id")
     * })
     */
    private $idPlayerB;

    /**
     * @var Player
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Player")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_player_a", referencedColumnName="id")
     * })
     */
    private $idPlayerA;



    /**
     * Set name
     *
     * @param string $name
     *
     * @return Teams
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
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set idPlayerB
     *
     * @param Player $idPlayerB
     *
     * @return Teams
     */
    public function setIdPlayerB(Player $idPlayerB = null)
    {
        $this->idPlayerB = $idPlayerB;

        return $this;
    }

    /**
     * Get idPlayerB
     *
     * @return Player
     */
    public function getIdPlayerB()
    {
        return $this->idPlayerB;
    }

    /**
     * Set idPlayerA
     *
     * @param Player $idPlayerA
     *
     * @return Teams
     */
    public function setIdPlayerA(Player $idPlayerA = null)
    {
        $this->idPlayerA = $idPlayerA;

        return $this;
    }

    /**
     * Get idPlayerA
     *
     * @return Player
     */
    public function getIdPlayerA()
    {
        return $this->idPlayerA;
    }
}
