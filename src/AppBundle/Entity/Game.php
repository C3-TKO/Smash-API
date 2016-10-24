<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Entity\Round;
use AppBundle\Entity\Team;
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * Game
 *
 * @ORM\Table(name="games", indexes={@ORM\Index(name="IDX_232B318C2B6BAB04", columns={"id_round"}), @ORM\Index(name="IDX_232B318C92977BE4", columns={"id_team_a"}), @ORM\Index(name="IDX_232B318CB9E2A5E", columns={"id_team_b"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\GameRepository")
 * @Hateoas\Relation(
 *     "self",
 *     href=@Hateoas\Route(
 *          "get_game",
 *          parameters={"id"= "expr(object.getId())"}
 *     )
 * )
 */
class Game
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="team_a_score", type="smallint", nullable=false)
     * @Assert\NotBlank(message="Team A must have scored points.")
     * @Assert\Range(min=0,max=30)
     */
    private $teamAScore;

    /**
     * @var integer
     *
     * @ORM\Column(name="team_b_score", type="smallint", nullable=false)
     * @Assert\NotBlank(message="Team B must have scored points.")
     * @Assert\Range(min=0,max=30)
     */
    private $teamBScore;

    /**
     * @var Round
     *
     * @ORM\ManyToOne(targetEntity="Round")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_round", referencedColumnName="id")
     * })
     */
    private $round;

    /**
     * @var Team
     *
     * @ORM\ManyToOne(targetEntity="Team")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_team_a", referencedColumnName="id")
     * })
     */
    private $teamA;

    /**
     * @var Team
     *
     * @ORM\ManyToOne(targetEntity="Team")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_team_b", referencedColumnName="id")
     * })
     */
    private $teamB;



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
     * Set teamAScore
     *
     * @param integer $teamAScore
     *
     * @return Games
     */
    public function setTeamAScore($teamAScore)
    {
        $this->teamAScore = $teamAScore;

        return $this;
    }

    /**
     * Get teamAScore
     *
     * @return integer
     */
    public function getTeamAScore()
    {
        return $this->teamAScore;
    }

    /**
     * Set teamBScore
     *
     * @param integer $teamBScore
     *
     * @return Game
     */
    public function setTeamBScore($teamBScore)
    {
        $this->teamBScore = $teamBScore;

        return $this;
    }

    /**
     * Get teamBScore
     *
     * @return integer
     */
    public function getTeamBScore()
    {
        return $this->teamBScore;
    }

    /**
     * Set round
     *
     * @param Round $round
     *
     * @return Game
     */
    public function setRound(Round $round = null)
    {
        $this->round = $round;

        return $this;
    }

    /**
     * Get round
     *
     * @return Round
     */
    public function getRound()
    {
        return $this->round;
    }

    /**
     * Set teamA
     *
     * @param Team $teamA
     *
     * @return Game
     */
    public function setTeamA(Team $teamA = null)
    {
        $this->teamA = $teamA;

        return $this;
    }

    /**
     * Get teamA
     *
     * @return Team
     */
    public function getTeamA()
    {
        return $this->teamA;
    }

    /**
     * Set teamB
     *
     * @param Team $idTeamB
     *
     * @return Game
     */
    public function setTeamB(Team $teamB = null)
    {
        $this->teamB = $teamB;

        return $this;
    }

    /**
     * Get teamB
     *
     * @return Team
     */
    public function getTeamB()
    {
        return $this->teamB;
    }

    /**
     * @Serializer\VirtualProperty()
     * @Serializer\SerializedName("id_round")
     */
    public function getIdRound() {
        return $this->round->getId();
    }

    /**
     * @Serializer\VirtualProperty()
     * @Serializer\SerializedName("id_team_a")
     */
    public function getIdTeamA() {
        return $this->teamA->getId();
    }

    /**
     * @Serializer\VirtualProperty()
     * @Serializer\SerializedName("id_team_b")
     */
    public function getIdTeamB() {
        return $this->teamB->getId();
    }
}
