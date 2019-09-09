<?php
namespace Pepsite\Entity;

class Vote extends DBEntity
{
    protected $id;
    protected $effect;
    protected $voteTime;
    protected $voter;
    protected $voteFor;
}
