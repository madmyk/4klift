<?php

namespace deasilworks\cef\test\Manager;

use deasilworks\cef\EntityManager;
use deasilworks\cef\test\Collection\UserCollection;

class UserManager extends EntityManager
{
    /**
     * A ResultContainer class
     *
     * @var string $collectionClass
     */
    protected $collectionClass = UserCollection::class;

}