<?php

namespace Pepsite\ViewHelper;

use Pepsite\Model\VotesTable;
use Pepsite\Entity\Vote;
use Zend\Form\View\Helper\AbstractHelper;

class LastVote extends AbstractHelper
{
    private $votesTable;

    public function __construct(VotesTable $votesTable)
    {
        $this->votesTable = $votesTable;
    }

    public function __invoke($voterLogin, $targetLogin) : ?int
    {
        $vote = $this->votesTable->getLastVote($voterLogin, $targetLogin);
        if (is_null($vote)) {
            return null;
        }
        $effect = $vote->getEffect();
        if ($effect === Vote::VOTE_POSITIVE) {
            return 1;
        }
        if ($effect === Vote::VOTE_NEGATIVE) {
            return -1;
        }
        return 0;
    }
}
