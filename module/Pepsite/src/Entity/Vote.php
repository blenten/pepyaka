<?php
namespace Pepsite\Entity;

class Vote extends DBEntity
{
    protected $id;
    protected $effect;
    protected $voteTime;
    protected $voter;
    protected $voteFor;

    public const VOTE_POSITIVE = '+';
    public const VOTE_NEGATIVE = '-';
    public const VOTE_NEUTRAL  = '0';

    public function getId()
    {
        return $this->id;
    }

    public function getEffect()
    {
        return $this->effect;
    }

    public function getVoter()
    {
        return $this->voter;
    }

    public function getVoteFor()
    {
        return $this->voteFor;
    }

    public function getVoteTime()
    {
        return $this->voteTime;
    }
}
