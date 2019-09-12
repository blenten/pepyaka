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

    public function getUserVotes($userId, $rowLimit = null)
    {
        return $this->tableGateway->select(function (Select $select) use ($userId, $rowLimit) {
            $select->where(['voter' => $userId])->order('voteTime DESC');
            if (!is_null($rowLimit)) {
                $select->limit($rowLimit);
            }
        });
    }
}
