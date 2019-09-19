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

    public function __invoke($voterLogin, $targetLogin) : ?Vote
    {
        return $this->votesTable->getLastVote($voterLogin, $targetLogin);
    }
}
