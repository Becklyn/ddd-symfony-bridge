5.2.0
=======

* BC - Drop Support for Symfony 5, add Support for Symfony 7

5.1.0
=======

* BC - remove sensio framework extra bundle

4.0.0
=======

* CommandBus::dispatch no longer accepts a second argument. To correlate commands, use the new CommandBus::dispatchAndCorrelate method.
