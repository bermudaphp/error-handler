# Install
```bash
composer require bermudaphp/error-handler
````
# Usage
```php
$generator = new ErrorResponseGenerator($psr17ResponseFactory, new WhoopsErrorGenerator($psr17ResponseFactory));
$generator->addGenerator($myConcreteErrorResponseGenerator);
$errorHandler = new ErrorHandlerMiddleware(new ErrorHandler($generator));
$pipeline->pipe($errorHandler); // Add ErrorHandlerMiddleware at the beginning of the middleware queue
````
# Event listening
```php
$errorHandler->listen(new Listener\LoggerListener($logger));
````
