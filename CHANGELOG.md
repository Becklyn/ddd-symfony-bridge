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
