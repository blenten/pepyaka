<?php
namespace Pepsite\Model;

use Zend\Db\Sql\Select;
use Pepsite\Entity\Comment;

class CommentsTable extends DBTable
{
    public static function makeFactories()
    {
        return parent::makeFactoriesFor('comments', self::class, Comment::class);
    }

    public function getCommentsOnPage($targetUserId, $rowLimit = null)
    {
        return $this->tableGateway->select(function (Select $select) use ($targetUserId, $rowLimit) {
            $select->where(['target' => $targetUserId])->order('postTime DESC');
            if (!is_null($rowLimit)) {
                $select->limit($rowLimit);
            }
        });
    }

    public function createComment(Comment $comment)
    {
        $data = [
            'content' => $comment->getContent(),
            'author'  => $comment->getAuthor(),
            'target'  => $comment->getTarget()
        ];
        return $this->tableGateway->insert($data);
    }
}
