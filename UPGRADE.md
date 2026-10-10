# Upgrade to Symfony ^6.4 || ^7.0

## Dependencies

- `symfony/*`: `^5.4 || ^6.0 || ^7.0` → `^6.4 || ^7.0`
- `symfony/yaml` is now a runtime requirement (it was only a dev dependency)

## Behavior changes

### YAML configuration

The bundle configuration files moved from XML (deprecated loading format) to YAML:

- `Resources/config/services.xml` → `Resources/config/services.yaml` (same service ids, parameters and tags)
- `Resources/config/route.xml` → `Resources/config/routes.yaml` (same route names and paths)

If you imported the routing file directly, update the resource:

```yaml
graphql:
    resource: "@GraphQLBundle/Resources/config/routes.yaml"
```

Importing `@GraphQLBundle/Controller/` (attribute routes) is unchanged.

### Routing attributes

Controllers now use `Symfony\Component\Routing\Attribute\Route` (the `Annotation` namespace is
deprecated since Symfony 6.4) and declare explicit route names, identical to the previously
generated ones (`youshido_graphql_graphql_default`, `youshido_graphql_graphqlexplorer_explorer`).

### Extension base class

`GraphQLExtension` now extends `Symfony\Component\DependencyInjection\Extension\Extension`
instead of the HttpKernel one, deprecated since Symfony 7.1.

# Upgrade to PHP 8.2 / Symfony ^5.4 || ^6.0 || ^7.0

## Dependencies

- `php`: `>=7.4` → `>=8.2`
- `symfony/*`: `^4.4 || ^5.0` → `^5.4 || ^6.0 || ^7.0`
- `phpunit/phpunit`: `^8.0 || ^9.0` → `^9.6`
- `youshido/graphql`: `~1.4` → `^2.0` ([Yoopies fork](https://github.com/Yoopies/GraphQL) release with PHP 8.2 support and typed signatures)

## Behavior changes

### ContainerAware replacement (Symfony 7 compatibility)

`Symfony\Component\DependencyInjection\ContainerAwareInterface` and `ContainerAwareTrait`
were removed in Symfony 7.0. The bundle now ships its own replacements:

- `Youshido\GraphQLBundle\DependencyInjection\ContainerAwareInterface`
- `Youshido\GraphQLBundle\DependencyInjection\ContainerAwareTrait`

`AbstractContainerAwareField` and `Execution\Container\SymfonyContainer` now implement the
bundle's interface instead of Symfony's. Fields and schema classes implementing the legacy
Symfony interface are still detected and injected on Symfony 6.4 for backward
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
