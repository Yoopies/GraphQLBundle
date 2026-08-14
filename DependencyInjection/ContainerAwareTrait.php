<?php

namespace Youshido\GraphQLBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Replacement for Symfony\Component\DependencyInjection\ContainerAwareTrait,
 * removed in Symfony 7.0.
 */
trait ContainerAwareTrait
{
    /** @var ContainerInterface|null */
    protected $container;

    public function setContainer(?ContainerInterface $container = null): void
    {
        $this->container = $container;
    }
}
