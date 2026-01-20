# Guide de migration GraphQLBundle vers Symfony 5

## Changements apportés

### Dépendances
- **PHP minimum** : 7.2.5 (requis par Symfony 4.4+)
- **Symfony** : Compatible avec 4.4 et 5.x
- **PHPUnit** : Mis à jour vers 8.x/9.x
- **ext-json** : Ajout explicite de la dépendance

### Changements techniques

#### 1. composer.json
Ajout de toutes les dépendances Symfony explicites :
- `symfony/http-foundation: ^4.4 || ^5.0`
- `symfony/http-kernel: ^4.4 || ^5.0`
- `symfony/dependency-injection: ^4.4 || ^5.0`
- `symfony/config: ^4.4 || ^5.0`
- `symfony/console: ^4.4 || ^5.0`
- `symfony/routing: ^4.4 || ^5.0`
- `symfony/event-dispatcher: ^4.4 || ^5.0`
- `ext-json: *`

#### 2. Controllers
- **GraphQLController** : Migration de `Controller` vers `AbstractController`
- Remplacement de `$this->get()` par injection de dépendances
- Remplacement de `$this->getParameter()` par `ParameterBagInterface`
- Injection du `RequestStack`, `ParameterBagInterface`, et `ContainerInterface`
- **Important** : Le `Processor` est récupéré de manière lazy depuis le container pour éviter les problèmes avec le service synthétique `graphql.schema`

#### 3. Services (Resources/config/services.xml)
- Ajout des définitions de services pour les contrôleurs
- Configuration de l'autowiring pour les contrôleurs

#### 4. Routing (Resources/config/route.xml)
- Remplacement du format `Bundle:Controller:action` (deprecated)
- Utilisation du format FQCN : `Youshido\GraphQLBundle\Controller\GraphQLController::defaultAction`

#### 5. Commands
- Ajout du type de retour `int` pour la méthode `execute()`
- Retour de `Command::SUCCESS` au lieu de `void`

#### 6. Tests
- Remplacement de `\PHPUnit_Framework_TestCase` par `PHPUnit\Framework\TestCase`
- Suppression de `ResolveDefinitionTemplatesPass` (n'existe plus dans Symfony 4.4+)

## Instructions de migration

### 1. Mettre à jour les dépendances

```bash
cd /var/www/Yoopies/GraphQLBundle
composer update
```

### 2. Vérifier la configuration de votre application

Assurez-vous que dans votre `config/routes.yaml` ou équivalent, vous avez :

```yaml
graphql:
    resource: "@GraphQLBundle/Resources/config/route.xml"
```

### 3. Lancer les tests

```bash
vendor/bin/phpunit
```

## Points d'attention

### Service synthétique graphql.schema

⚠️ **Important** : Le service `graphql.schema` est défini comme synthétique dans le container Symfony. Il est initialisé dynamiquement au moment de la première requête GraphQL.

**Problème résolu** : Dans les versions précédentes de ce bundle avec Symfony 5, injecter directement le `Processor` dans le constructeur du contrôleur causait cette erreur :

```
The "graphql.schema" service is synthetic, it needs to be set at boot time before it can be used.
```

**Solution implémentée** : Le `Processor` est maintenant récupéré de manière lazy depuis le service container uniquement quand il est nécessaire (dans la méthode `executeQuery()`), après que le schéma ait été initialisé par `initializeSchemaService()`.

Cette approche garantit que :
1. Le contrôleur peut être instancié sans problème
2. Le schéma est initialisé avant d'être utilisé
3. Le processor ne tente pas d'accéder au schéma synthétique trop tôt

### ContainerAwareInterface et ContainerAwareTrait

Ces classes sont toujours présentes dans Symfony 5 mais marquées comme deprecated. Elles continuent de fonctionner pour :
- `AbstractContainerAwareField`
- `SymfonyContainer`

Pour une migration complète vers Symfony 6, il faudra remplacer ces usages par de l'injection de dépendances.

## Compatibilité

- ✅ Symfony 4.4
- ✅ Symfony 5.0+
- ✅ PHP 7.2.5+
- ✅ PHP 8.0+

## Notes importantes

- Les versions antérieures à Symfony 4.4 ne sont plus supportées
- Le format de routing `Bundle:Controller:action` n'est plus supporté
- La classe `Controller` de base a été remplacée par `AbstractController`
- Les services doivent maintenant utiliser l'injection de dépendances au lieu de `$this->get()`

## Migration personnalisée

Si vous avez des champs personnalisés qui étendent `AbstractContainerAwareField`, ils continueront de fonctionner. Cependant, pour une meilleure compatibilité future, envisagez de :

1. Injecter les services nécessaires via le constructeur
2. Utiliser l'injection de dépendances au lieu de `$this->container->get()`

Exemple :

```php
// Ancien code (fonctionne encore)
class MyField extends AbstractContainerAwareField
{
    public function resolve($value, array $args, ResolveInfo $info)
    {
        $service = $this->container->get('my_service');
        // ...
    }
}

// Nouveau code (recommandé)
class MyField extends AbstractField
{
    private $myService;
    
    public function __construct(MyService $myService)
    {
        $this->myService = $myService;
        parent::__construct([]);
    }
    
    public function resolve($value, array $args, ResolveInfo $info)
    {
        // Utiliser $this->myService
    }
}
```

## Dépannage

### Erreur: "The graphql.schema service is synthetic..."

Si vous rencontrez cette erreur, vérifiez que :
1. Vous utilisez la dernière version du bundle compatible Symfony 5
2. Le contrôleur ne tente pas d'injecter le `Processor` directement dans le constructeur
3. Votre cache Symfony est bien vidé : `bin/console cache:clear`

### Erreur: "Controller not callable"

Assurez-vous que :
1. Les routes utilisent le format FQCN et non `Bundle:Controller:action`
2. Le contrôleur est bien défini comme service dans `services.xml`
3. Le tag `controller.service_arguments` est présent

## Support

Pour toute question ou problème, consultez :
- Les issues du repository original
- La documentation Symfony sur la migration vers Symfony 5
