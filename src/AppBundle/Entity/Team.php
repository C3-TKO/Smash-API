<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\Player;
use JMS\Serializer\Annotation as Serializer;
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * Teams
 *
 * @ORM\Table(name="teams", uniqueConstraints={@ORM\UniqueConstraint(name="team_combination", columns={"id_player_a", "id_player_b"})}, indexes={@ORM\Index(name="IDX_team_id_2_player_a_id", columns={"id_player_a"}), @ORM\Index(name="IDX_team_id_2_player_b_id", columns={"id_player_b"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TeamRepository")
 * @Serializer\ExclusionPolicy("all")
 * @Hateoas\Relation(
 *     "player_a",
 *     href=@Hateoas\Route(
 *          "get_player",
 *          parameters={"id"= "expr(object.getIdPlayerA())"}
 *     ),
 *     embedded = "expr(object.getPlayerA())"
 * )
 * @Hateoas\Relation(
 *     "player_b",
 *     href=@Hateoas\Route(
 *          "get_player",
 *          parameters={"id"= "expr(object.getIdPlayerB())"}
 *     ),
 *     embedded = "expr(object.getPlayerB())"
 * )
 */
class Team
{
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=45, nullable=true)
     * @Serializer\Expose()
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Serializer\Expose()
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
    private $playerB;

    /**
     * @var Player
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Player")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_player_a", referencedColumnName="id")
     * })
     */
    private $playerA;



    /**
     * Set name
     *
     * @param string $name
     *
     * @return Team
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
     * Set playerB
     *
     * @param Player $playerB
     *
     * @return Team
     */
    public function setPlayerB(Player $playerB = null)
    {
        $this->playerB = $playerB;

        return $this;
    }

    /**
     * Get playerB
     *
     * @return Player
     */
    public function getPlayerB()
    {
        return $this->playerB;
    }

    /**
     * Set playerA
     *
     * @param Player $playerA
     *
     * @return Teams
     */
    public function setPlayerA(Player $playerA = null)
    {
        $this->playerA = $playerA;

        return $this;
    }

    /**
     * Get idPlayerA
     *
     * @return Player
     */
    public function getPlayerA()
    {
        return $this->playerA;
    }

    /**
     * @Serializer\VirtualProperty()
     * @Serializer\SerializedName("id_player_a")
     */
    public function getIdPlayerA() {
        return $this->playerA->getId();
    }

    /**
     * @Serializer\VirtualProperty()
     * @Serializer\SerializedName("id_player_b")
     */
    public function getIdPlayerB() {
        return $this->playerB->getId();
    }
}
