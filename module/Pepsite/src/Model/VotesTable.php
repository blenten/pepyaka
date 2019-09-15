<?php
namespace Pepsite\Model;

use Zend\Db\Sql\Select;
use Pepsite\Entity\Vote;

class VotesTable extends DBTable
{
    public static function makeFactories()
    {
        return parent::makeFactoriesFor('votes', self::class, Vote::class);
    }

    public function getUserVotes($userLogin, $rowLimit = null)
    {
        return $this->tableGateway->select(function (Select $select) use ($userLogin, $rowLimit) {
            $select->where(['voter' => (string) $userLogin])->order('voteTime DESC');
            if (!is_null($rowLimit)) {
                $select->limit((int) $rowLimit);
            }
        });
    }

    public function getLastVote($voterLogin, $targetLogin)
    {
        $rowset = $this->tableGateway->select(function (Select $select) use ($voterLogin, $targetLogin) {
            $select->where([
                'voter'   => (string) $voterLogin,
                'voteFor' => (string) $targetLogin
            ])
                ->order('voteTime DESC')
                ->limit(1);
        });
        $row = $rowset->current();
        if (!$row) {
            return null;
        }
        return $row;
    }

    public function createVote(Vote $vote)
    {
        $data = [
            'effect'  => $vote->getEffect(),
            'voter'   => $vote->getVoter(),
            'voteFor' => $vote->getVoteFor()
        ];

        return $this->tableGateway->insert($data);
    }
}
