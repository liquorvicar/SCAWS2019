<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PointsTable")
 * @ORM\Table(name="score")
 */
class Score
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @var Prediction
     * @ORM\ManyToOne(targetEntity="Prediction")
     * @ORM\JoinColumn(name="prediction_id", referencedColumnName="id")
     */
    private $prediction;
    /**
     * @var Goal|null
     * @ORM\ManyToOne(targetEntity="Goal", inversedBy="scores")
     * @ORM\JoinColumn(name="goal_id", referencedColumnName="id", nullable=true)
     */
    private $goal;
    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $reason;
    /**
     * @var float
     * @ORM\Column(type="decimal")
     */
    private $points = 0;

    public function getPoints(): float
    {
        return $this->points;
    }

    public function setPoints(float $points): Score
    {
        $this->points = $points;

        return $this;
    }

    public function getPrediction(): Prediction
    {
        return $this->prediction;
    }

    public function setPrediction(Prediction $prediction): Score
    {
        $this->prediction = $prediction;
        $prediction->addScore($this);

        return $this;
    }

    public function setGoal(Goal $goal): Score
    {
        $this->goal = $goal;
        $goal->addScore($this);

        return $this;
    }

    public function getReason(): int
    {
        return $this->reason;
    }

    public function setReason(int $reason): Score
    {
        $this->reason = $reason;

        return $this;
    }

    public function getGoal(): ?Goal
    {
        return $this->goal;
    }
}
