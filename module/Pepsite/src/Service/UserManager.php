<?php
/** @noinspection PhpUndefinedMethodInspection */

namespace Pepsite\Service;

use Pepsite\Entity\User;
use Pepsite\Entity\Vote;
use Pepsite\Model\UsersTable;
use Pepsite\Model\VotesTable;
use Zend\Crypt\Password\Bcrypt;

class UserManager
{
    private $userTable;
    private $votesTable;

    public const DVOTE_POSITIVE = 1;
    public const DVOTE_NEGATIVE = -1;

    public function __construct(UsersTable $usersTable, VotesTable $votesTable)
    {
        $this->userTable = $usersTable;
        $this->votesTable = $votesTable;
    }

    public function getUser($login)
    {
        return $this->userTable->getUser($login);
    }

    public function getTopUsers()
    {
        return $this->userTable->getTopUsers();
    }

    public function getValidUser($login, $password)
    {
        $user = $this->userTable->getUser($login);
        if (!is_null($user)) {
            if ($this->validatePassword($user, $password)) {
                return $user;
            }
        }
        return null;
    }

    public function validatePassword(User $user, $password)
    {
        $bcrypt = new Bcrypt();
        if ($bcrypt->verify($password, $user->getPassword())) {
            return true;
        }
        return false;
    }

    public function addUser(array $data)
    {
        $bcrypt = new Bcrypt();
        $data['password'] = $bcrypt->create($data['password']);
        $user = new User();
        $user->exchangeArray($data);
        $this->userTable->createUser($user);
    }

    public function updateUser(User $user)
    {
        $this->userTable->updateUser($user);
    }

    public function upvote($voterLogin, $targetLogin)
    {
        return $this->vote($voterLogin, $targetLogin, Vote::VOTE_POSITIVE);
    }

    public function downvote($voterLogin, $targetLogin)
    {
        return $this->vote($voterLogin, $targetLogin, Vote::VOTE_NEGATIVE);
    }

    public function unvote($voterLogin, $targetLogin)
    {
        return $this->vote($voterLogin, $targetLogin, Vote::VOTE_NEUTRAL);
    }

    private function vote($voterLogin, $targetLogin, $effect)
    {
        if (!in_array($effect, [Vote::VOTE_NEUTRAL, Vote::VOTE_POSITIVE, Vote::VOTE_NEGATIVE])) {
            throw new \Exception("Wrong vote effect: {$effect}");
        }
        $lastVote = $this->votesTable->getLastVote($voterLogin, $targetLogin);
        $dVotes = 0;
        if (is_null($lastVote)) {
            if ($effect == Vote::VOTE_NEUTRAL) {
                throw new \Exception("Cannot unvote the unvoted\n\t{$voterLogin} {$targetLogin}");
            }
            switch ($effect) {
                case Vote::VOTE_NEGATIVE:
                    $dVotes = self::DVOTE_NEGATIVE;
                    break;
                case Vote::VOTE_POSITIVE:
                    $dVotes = self::DVOTE_POSITIVE;
                    break;
                case Vote::VOTE_NEUTRAL:
                    $dVotes = 0;
                    break;
            }
        } else {
            $lastVoteEffect = $lastVote->getEffect();
            if ($lastVoteEffect === $effect) {
                return false;
            }
            switch ([$lastVoteEffect, $effect]) {
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
        $user = $this->userTable->getUser($targetLogin);
        if (is_null($user)) {
            throw new \Exception("Could not update votes for user {$targetLogin} , user doesn't exist");
        }
        $user->setVotes($user->getVotes() + $dVotes);
        $this->userTable->updateUser($user);
        return true;
    }
}
