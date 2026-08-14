<?php

namespace Youshido\GraphQLBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Argument\ServiceClosureArgument;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Registers services tagged "graphql.event_listener" / "graphql.event_subscriber"
 * on the "graphql.event_dispatcher" service.
 *
 * Replaces Symfony's RegisterListenersPass, which no longer supports custom
 * dispatcher/tag names since Symfony 6.0.
 */
class RegisterGraphQlListenersPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('graphql.event_dispatcher')) {
            return;
        }

        $dispatcher = $container->getDefinition('graphql.event_dispatcher');

        foreach ($container->findTaggedServiceIds('graphql.event_listener', true) as $id => $tags) {
            foreach ($tags as $attributes) {
                if (!isset($attributes['event'])) {
                    throw new InvalidArgumentException(sprintf('Service "%s" must define the "event" attribute on "graphql.event_listener" tags.', $id));
                }

                $dispatcher->addMethodCall('addListener', [
                    $attributes['event'],
                    [new ServiceClosureArgument(new Reference($id)), $attributes['method'] ?? '__invoke'],
                    $attributes['priority'] ?? 0,
                ]);
            }
        }

        foreach (array_keys($container->findTaggedServiceIds('graphql.event_subscriber', true)) as $id) {
            $class = $container->getParameterBag()->resolveValue($container->getDefinition($id)->getClass());
            if (!is_subclass_of($class, EventSubscriberInterface::class)) {
                throw new InvalidArgumentException(sprintf('Service "%s" must implement interface "%s".', $id, EventSubscriberInterface::class));
            }

            foreach ($class::getSubscribedEvents() as $event => $params) {
                if (is_string($params)) {
                    $listeners = [[$params, 0]];
                } elseif (is_string($params[0])) {
                    $listeners = [$params];
                } else {
                    $listeners = $params;
                }

                foreach ($listeners as $listener) {
                    $dispatcher->addMethodCall('addListener', [
                        $event,
                        [new ServiceClosureArgument(new Reference($id)), $listener[0]],
                        $listener[1] ?? 0,
                    ]);
                }
            }
        }
    }
}
