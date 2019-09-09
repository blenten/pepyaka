<?php
namespace Pepsite\Entity;

class Comment extends DBEntity
{
    protected $id;
    protected $content;
    protected $postTime;
    protected $author;
    protected $target;
}
