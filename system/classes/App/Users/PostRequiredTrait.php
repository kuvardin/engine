<?php

declare(strict_types=1);

namespace App\Users;

trait PostRequiredTrait
{
    protected int $users_post_id;

    public function getUsersPostId(): int
    {
        return $this->users_post_id;
    }

    public function getUsersPost(): Post
    {
        return Post::requireById($this->users_post_id);
    }
}