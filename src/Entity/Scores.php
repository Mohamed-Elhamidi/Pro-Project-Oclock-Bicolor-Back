<?php

namespace App\Entity;
use App\Entity\Game;
use App\Repository\ScoresRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ScoresRepository::class)
 */
class Scores
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=user::class, inversedBy="scores")
     */
    private $id_user;

    /**
     * @ORM\ManyToOne(targetEntity=game::class, inversedBy="scores")
     */
    private $id_game;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbPoints;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $startGame;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $endGame;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdUser(): ?user
    {
        return $this->id_user;
    }

    public function setIdUser(?user $id_user): self
    {
        $this->id_user = $id_user;

        return $this;
    }

    public function getIdGame(): ?game
    {
        return $this->id_game;
    }

    public function setIdGame(?game $id_game): self
    {
        $this->id_game = $id_game;

        return $this;
    }

    public function getNbPoints(): ?int
    {
        return $this->nbPoints;
    }

    public function setNbPoints(int $nbPoints): self
    {
        $this->nbPoints = $nbPoints;

        return $this;
    }

    public function getStartGame(): ?\DateTimeInterface
    {
        return $this->startGame;
    }

    public function setStartGame(?\DateTimeInterface $startGame): self
    {
        $this->startGame = $startGame;

        return $this;
    }

    public function getEndGame(): ?\DateTimeInterface
    {
        return $this->endGame;
    }

    public function setEndGame(?\DateTimeInterface $endGame): self
    {
        $this->endGame = $endGame;

        return $this;
    }
}
