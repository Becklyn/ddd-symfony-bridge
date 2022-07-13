becklyn/ddd-symfony-bridge integrates components provided by becklyn/ddd-core and becklyn/ddd-doctrine-bridge with a Symfony application. It uses SimpleBus to implement the event and command buses.
 
## Installation

- Run `composer require becklyn/ddd-symfony-bridge`.
- Add the following to `bundles.php`:
```
SimpleBus\SymfonyBridge\SimpleBusCommandBusBundle::class => ['all' => true],
SimpleBus\SymfonyBridge\SimpleBusEventBusBundle::class => ['all' => true],
Becklyn\Ddd\BecklynDddBundle::class => ['all' => true],
```

### Enabling Event Bus & Subscribers and Command Bus & Handlers
- Add the following to `services.yaml`:
```
event_subscribers:
    namespace: App\
    resource: '../src/**/*Subscriber.php'
    public: true
    tags:
        - { name: event_subscriber, register_public_methods: true }

command_handlers:
    namespace: App\
    resource: '../src/**/*Handler.php'
    public: true
    tags:
        - { name: command_handler, register_public_methods: true }
```
- The above registers all classes ending in 'Subscriber' as event subscribers, and all classes ending in 'Handler' as command handlers. Make sure you have no classes in your code with those endings that are not intended to be event subscribers or command handlers.

### Enabling the Event Store
- Run `php bin/console doctrine:migrations:migrate`. This executes the Doctrine migration provided by becklyn/ddd-doctrine-bridge to create database tables for the event store.
- If you do not wish to use the event store, see the `use_event_store` configuration option below.
- Add the following to `services.yaml` to enable persisting event data in the event store
```
# services.yaml
    # required for serializing event data into the event store
    symfony_property_normalizer:
        class: Symfony\Component\Serializer\Normalizer\PropertyNormalizer
        public: false
        tags: [ serializer.normalizer ]
```
- Add the following to `services.yaml` and `doctrine.yaml` if you wish for Doctrine ORM to persist microseconds as part of domain event timestamps in the event store (should not be required for MySQL and Doctrine 3):
```
# services.yaml
    # required for microsecond serialization in event store for raisedTs
    datetime_normalizer:
        class: Symfony\Component\Serializer\Normalizer\DateTimeNormalizer
        public: false
        tags: [serializer.normalizer]
        arguments:
            -
                datetime_format: 'Y-m-d H:i:s.u'
    
    # only if using Oracle
    oracle_connector:
        class: Becklyn\Ddd\DateTime\Infrastructure\Doctrine\MicrosecondsOracleSessionInit
        tags:
            - { name: doctrine.event_listener, event: postConnect }

# doctrine.yaml
    # required for writing event raisedTs microseconds into the DB
    doctrine:
        dbal:
            types:
                datetime_immutable: Becklyn\Ddd\DateTime\Infrastructure\Doctrine\DateTimeImmutableMicrosecondsType
```

## How To
 
### Use Event Subscribers and Command Handlers

If you followed the installation instructions above, the only thing you need to register a class as an event subscriber or command handler is to have its class name end in 'Subscriber' or 'Handler', respectively.

Each subscriber or handler may handle one or more events or commands. An individual event class may be handled by multiple subscribers, while the application will throw exceptions or behave unpredictably if a command class is handled by multiple handlers.

To have a subscriber or handler handle an event or command, simply implement a public method with a single argument typed to the event or command class. The name of the method and the argument can be anything, but the method must return void. It is traditional for the method to be named `handle` and the argument `$event` or `$command`, or for methods to be named `handleMyEventClass` in case of subscribers or handlers handling multiple different events or commands.

## Configuration
To change the values of configuration options from their defaults, create a `becklyn_ddd.yaml` file in the `config/packages` folder within your Symfony application with the following contents:
```
becklyn_ddd:
    option_name: value
    another_option_name: value
```

### Available Options

#### use_event_store

- Type: boolean
- Default: true

By default, the event store is active. If you do not want to use it for whatever reason, set this option to false.

Note that if you leave this option set to true and do not execute the Doctrine migrations required for the store, the application will throw an exception as soon as a transaction is committed and there have been events registered.
 
## SimpleBus Configuration

SimpleBus always finishes the handling of the current command before new commands dispatched during this handling are processed. In other words, it puts all commands dispatched during the processing of another command into a queue, and flushes the queue only when the processing of the current command is complete.

Having a command dispatch another command and that new command be processed before handling of the originating command resumes can be a code smell in a lot of situations. But in case you require such functionality it is possible to configure SimpleBus to do so. Add the following to the `command_bus.yaml` in `config/packages`:
```
command_bus:
    middlewares:
        finishes_command_before_handling_next: false
```