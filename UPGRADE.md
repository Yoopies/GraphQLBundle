# Upgrade to PHP 8.2 / Symfony ^5.4 || ^6.0 || ^7.0

## Dependencies

- `php`: `>=7.4` → `>=8.2`
- `symfony/*`: `^4.4 || ^5.0` → `^5.4 || ^6.0 || ^7.0`
- `phpunit/phpunit`: `^8.0 || ^9.0` → `^9.6`
- `youshido/graphql`: requires the [Yoopies fork](https://github.com/Yoopies/GraphQL) with PHP 8.2 support (typed signatures)

## Behavior changes

### ContainerAware replacement (Symfony 7 compatibility)

`Symfony\Component\DependencyInjection\ContainerAwareInterface` and `ContainerAwareTrait`
were removed in Symfony 7.0. The bundle now ships its own replacements:

- `Youshido\GraphQLBundle\DependencyInjection\ContainerAwareInterface`
- `Youshido\GraphQLBundle\DependencyInjection\ContainerAwareTrait`

`AbstractContainerAwareField` and `Execution\Container\SymfonyContainer` now implement the
bundle's interface instead of Symfony's. Fields and schema classes implementing the legacy
Symfony interface are still detected and injected on Symfony 5.4/6.x for backward
compatibility, but should migrate to the bundle's interface.

### Event listeners

`graphql.event_listener` / `graphql.event_subscriber` tagged services are now registered on
`graphql.event_dispatcher` by the bundle's own `RegisterGraphQlListenersPass` (Symfony's
`RegisterListenersPass` no longer supports custom dispatchers since 6.0). Tag usage is
unchanged.

### Routing

Controller routes are declared with PHP attributes (`#[Route]`) instead of annotations,
which are no longer supported in Symfony 7.

### graphql:configure command

The command now uses `kernel.project_dir` (`kernel.root_dir` was removed in Symfony 5.0):
the schema class is generated in `src/GraphQL/Schema.php` and the config in
`config/packages/graphql.yml`.
