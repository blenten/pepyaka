<?php
namespace Pepsite\Entity;

class Comment extends DBEntity
{
    protected $id;
    protected $content;
    protected $postTime;
    protected $author;
    protected $target;

    public function getAuthor()
    {
        return $this->author;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getPostTime()
    {
        return $this->postTime;
    }

    public function getTarget()
    {
        return $this->target;
    }
}
