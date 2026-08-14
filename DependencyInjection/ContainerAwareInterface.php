<?php

namespace Youshido\GraphQLBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Replacement for Symfony\Component\DependencyInjection\ContainerAwareInterface,
 * removed in Symfony 7.0.
 */
interface ContainerAwareInterface
{
    public function setContainer(?ContainerInterface $container = null): void;
}
