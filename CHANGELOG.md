6.2.0
=======

* (feature) Added `MessengerCommandBus` and `MessengerEventBus`, Symfony Messenger implementations of the `CommandBus` and `EventBus` application interfaces. These replace the SimpleBus implementations, which cannot be used beyond Symfony 6: `simple-bus/symfony-bridge` caps `symfony/config`, `symfony/http-kernel` and `symfony/yaml` at `^6.0` and has been unmaintained since 2021.
* (feature) Added `RegisterHandlersPass`, which translates the existing `command_handler` and `event_subscriber` service tags into Symfony Messenger's `messenger.message_handler` tags. Consuming applications keep their tags and handler classes unchanged; no per-handler migration is required.
* (feature) The bundle now prepends `framework.messenger` configuration declaring two synchronous buses, `becklyn_ddd.messenger.command_bus` and `becklyn_ddd.messenger.event_bus`, so applications need no messenger configuration of their own. The event bus sets `allow_no_handlers`, because a domain event without a subscriber is normal.
* (improvement) `becklyn_ddd.commands.command_bus` and `becklyn_ddd.events.event_bus` now point at the Messenger implementations. The SimpleBus classes are kept for consumers still on Symfony 6 with `simple-bus/symfony-bridge` installed, but their tests skip when the library is absent.
* (improvement) Added a return type to `Configuration::getConfigTreeBuilder()`. Without it the class is incompatible with `ConfigurationInterface` on Symfony 7, which declares `: TreeBuilder`.
* (improvement) Added `symfony/messenger` to `require`.

6.1.0
=======

* Updated Symfony component constraints to `^6.4 || ^7.4` to target Symfony 7.4 LTS.
* Updated `doctrine/doctrine-bundle` constraint to `^2.11 || ^3.0`, dropping support for the EOL 1.x branch.
* Updated `becklyn/ddd-doctrine-bridge` to `^3.0` and `becklyn/ddd-core` to `^4.0`, enabling `doctrine/orm ^3.x` and `doctrine/dbal ^4.x` support and removing the abandoned `doctrine/cache` transitive dependency.

6.0.0
=======

* (BC) Bumped Symfony component constraints to `^6.4 || ^7.0`.
* (BC) Raised minimum PHP version to 8.1 (required by Symfony 6.4).
* (BC) Removed the `becklyn_ddd.identity.aggregate_id_param_converter` service registration (Sensio `request.param_converter` tag). Applications relying on auto-resolving `AggregateId` controller arguments must register a custom `Symfony\Component\HttpKernel\Controller\ValueResolverInterface`.
* (BC) Any remaining `@ParamConverter` usages in consumer code must be migrated to `#[MapEntity]` (Symfony 6.2+) and any `@Security` usages to `#[IsGranted]` or a custom value resolver.

5.0.0
=======

* (BC) Remove abandoned package sensio/framework-extra-bundle, use symfony instead.

4.0.2
=======

* (bug) Fixed calling empty from global namespace.

4.0.1
=======

* (bug) Fixed TypeError occurring in Symfony controllers caused by `AggregateIdParamConverter`

4.0.0
=======

* (bc) Switched to 4.0 series of becklyn/ddd-core with separate CommandBus methods with and without correlation

3.1.0
=======

* (feature) Support for 3.2 series of becklyn/ddd-core which allows CommandBus to correlate commands

3.0.1
=======

* (improvement) Capped supported versions of becklyn/ddd-core to < 3.1.0 

3.0.0
=======

* (bc) Support for latest version of becklyn/ddd-core and becklyn/ddd-doctrine-bridge which provide event correlation and causation IDs.

2.1.0
=====

*   (feature) Support for latest version of becklyn/ddd-doctrine-bridge which provides \DateTimeImmutable microsecond support for Oracle

2.0.2
=====

*   (improvement) Pins Symfony 5 version to at least `5.4`

2.0.1
=====

*   (improvement) Now works with Symfony 6

2.0.0
=====

*   (feature) PHP 8 branch providing Symfony integration for components provided by becklyn/ddd-core and becklyn/ddd-doctrine-bridge
*   (bc) No longer works with PHP 7

1.0.0
=====

*   (feature) PHP 7 branch providing Symfony integration for components provided by becklyn/ddd-core and becklyn/ddd-doctrine-bridge
