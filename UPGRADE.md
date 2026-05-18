6.0.0
=======

* BC - Symfony component constraints bumped to `^6.4 || ^7.0`. Symfony 6.0–6.3 are no longer supported.
* BC - Minimum PHP version raised to 8.1 (required by Symfony 6.4).
* BC - The bundled `request.param_converter` service registration for `AggregateIdParamConverter` has been removed. If your application relied on auto-resolving `AggregateId` controller arguments, register a custom `Symfony\Component\HttpKernel\Controller\ValueResolverInterface`.
* BC - Any remaining `@ParamConverter` annotations in consumer code must be replaced with `#[MapEntity]` (Symfony 6.2+) and any `@Security` annotations with `#[IsGranted]` or a custom value resolver.

5.2.0
=======

* BC - Drop Support for Symfony 5, add Support for Symfony 7

5.1.0
=======

* BC - remove sensio framework extra bundle

4.0.0
=======

* CommandBus::dispatch no longer accepts a second argument. To correlate commands, use the new CommandBus::dispatchAndCorrelate method.
