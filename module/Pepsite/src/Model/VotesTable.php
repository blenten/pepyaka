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

    public function getVotesFor($targetLogin, $rowLimit = null)
    {
        return $this->tableGateway->select(function (Select $select) use ($targetLogin, $rowLimit) {
            $select->where(['voteFor' => $targetLogin])->order('voteTime DESC');
            if (!is_null($rowLimit)) {
                $select->limit((int) $rowLimit);
            }
        });
    }

    public function getUserVoteTargets($voterLogin, $targetList = null)
    {
        $rowset = $this->tableGateway->select(function (Select $select) use ($voterLogin, $targetList) {
            $select->where(['voter' => $voterLogin]);
            if (!is_null($targetList)) {
                $select->where(['voteFor ' => $targetList]);
            }
            $select->order('voteTime DESC');
        });
        if (is_null($rowset)) {
            return null;
        }
        $names = [];
        $result = [];
        foreach ($rowset as $row) {
            $name = $row->getVoteFor();
            if (!isset($names[$name])) {
                $names[$name] = true;
                $result[$name] = $row;
            }
        }
        return $result;
    }

    public function getLastVote($voterLogin, $targetLogin) : ?Vote
    {
        $rowset = $this->tableGateway->select(function (Select $select) use ($voterLogin, $targetLogin) {
            $select->where([
                'voter'   => $voterLogin,
                'voteFor' => $targetLogin
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
