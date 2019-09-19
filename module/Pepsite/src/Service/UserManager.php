<?php
/** @noinspection PhpUndefinedMethodInspection */

namespace Pepsite\Service;

use Pepsite\Entity\User;
use Pepsite\Entity\Vote;
use Pepsite\Model\UsersTable;
use Pepsite\Model\VotesTable;
use Zend\Crypt\Password\Bcrypt;
use Zend\Db\ResultSet\ResultSet;
use RuntimeException;

class UserManager
{
    private $usersTable;
    private $votesTable;

    public const DVOTE_POSITIVE = 1;
    public const DVOTE_NEGATIVE = -1;

    public function __construct(UsersTable $usersTable, VotesTable $votesTable)
    {
        $this->usersTable = $usersTable;
        $this->votesTable = $votesTable;
    }

    public function getUser($login) : ?User
    {
        return !is_null($login) ? $this->usersTable->getUser($login) : null;
    }
    public function getUsers($userLogins) : ?ResultSet
    {
        return !empty($userLogins) ? $this->usersTable->getUsers($userLogins) : null;
    }

    public function getTopUsers() : ?ResultSet
    {
        return $this->usersTable->getTopUsers();
    }

    public function getVotesFor($targetLogin, $limit) : ?ResultSet
    {
        return $this->votesTable->getVotesFor($targetLogin, $limit);
    }

    public function getUserVoteTargets($voterLogin, $targets = null) : ?array
    {
        return $this->votesTable->getUserVoteTargets($voterLogin, $targets);
    }

    public function getValidUser($login, $password) : ?User
    {
        $user = $this->usersTable->getUser($login);
        if (!is_null($user)) {
            if ($this->validatePassword($user, $password)) {
                return $user;
            }
        }
        return null;
    }

    public function validatePassword(User $user, $password) : bool
    {
        $bcrypt = new Bcrypt();
        if ($bcrypt->verify($password, $user->getPassword())) {
            return true;
        }
        return false;
    }

    public function addUser(array $data) : User
    {
        $bcrypt = new Bcrypt();
        $data['password'] = $bcrypt->create($data['password']);
        $user = new User();
        $user->exchangeArray($data);
        $this->usersTable->createUser($user);
        return $user;
    }

    public function updateUser(User $user) : void
    {
        $this->usersTable->updateUser($user);
    }

    public function upvote($voterLogin, $targetLogin) : bool
    {
        return $this->vote($voterLogin, $targetLogin, Vote::VOTE_POSITIVE);
    }

    public function downvote($voterLogin, $targetLogin) : bool
    {
        return $this->vote($voterLogin, $targetLogin, Vote::VOTE_NEGATIVE);
    }

    private function vote($voterLogin, $targetLogin, $effect)
    {
        if (!in_array($effect, [Vote::VOTE_NEUTRAL, Vote::VOTE_POSITIVE, Vote::VOTE_NEGATIVE])) {
            throw new RuntimeException("Wrong vote effect: {$effect}");
        }
        $lastVote = $this->votesTable->getLastVote($voterLogin, $targetLogin);
        $dVotes = 0;
        if (is_null($lastVote)) {
            switch ($effect) {
                case Vote::VOTE_NEGATIVE:
                    $dVotes = self::DVOTE_NEGATIVE;
                    break;
                case Vote::VOTE_POSITIVE:
                    $dVotes = self::DVOTE_POSITIVE;
                    break;
                case Vote::VOTE_NEUTRAL:
                    return false;
            }
        } else {
            $lastVoteEffect = $lastVote->getEffect();
            if ($lastVoteEffect === $effect) {
                $effect = Vote::VOTE_NEUTRAL;
            }
            switch ([$lastVoteEffect, $effect]) {
                case [Vote::VOTE_NEUTRAL, Vote::VOTE_NEUTRAL]:
                    return false;
                case [Vote::VOTE_NEUTRAL, Vote::VOTE_POSITIVE]:
                case [Vote::VOTE_NEGATIVE, Vote::VOTE_NEUTRAL]:
                    $dVotes = self::DVOTE_POSITIVE;
                    break;
                case [Vote::VOTE_NEGATIVE, Vote::VOTE_POSITIVE]:
                    $dVotes = self::DVOTE_POSITIVE * 2;
                    break;
                case [Vote::VOTE_POSITIVE, Vote::VOTE_NEUTRAL]:
                case [Vote::VOTE_NEUTRAL, Vote::VOTE_NEGATIVE]:
                    $dVotes = self::DVOTE_NEGATIVE;
                    break;
                case [Vote::VOTE_POSITIVE, Vote::VOTE_NEGATIVE]:
                    $dVotes = self::DVOTE_NEGATIVE * 2;
                    break;
            }
        }
        $vote = new Vote();
        $vote->exchangeArray([
            'effect'  => $effect,
            'voter'   => $voterLogin,
            'voteFor' => $targetLogin,
        ]);
        $this->votesTable->createVote($vote);
        $user = $this->usersTable->getUser($targetLogin);
        if (is_null($user)) {
            return false;
        }
        $user->setVotes($user->getVotes() + $dVotes);
        $this->usersTable->updateUser($user);
        return true;
    }
}
