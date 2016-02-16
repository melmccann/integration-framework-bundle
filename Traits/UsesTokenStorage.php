<?php

namespace Smartbox\Integration\FrameworkBundle\Traits;


use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

/**
 * Class UsesTokenStorage
 * @package Smartbox\Integration\FrameworkBundle\Traits
 */
trait UsesTokenStorage
{
    /** @var  TokenStorage */
    var $tokenStorage;

    /**
     * @return TokenStorage
     */
    public function getTokenStorage()
    {
        return $this->tokenStorage;
    }

    /**
     * @param TokenStorage $tokenStorage
     */
    public function setTokenStorage($tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

}