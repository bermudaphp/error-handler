# Install
```bash
composer require bermudaphp/error-handler
````
# Usage
```php
$errorHandler = new ErrorHandlerMiddleware(new WhoopsErrorGenerator($Psr17ResponseFactory));
$pipeline->pipe($errorHandler); // Add ErrorHandlerMiddleware at the beginning of the middleware queue
````
# Event listening
```php
$errorListenerInterfaceInstance = new LogErrorListener($logger);
$errorHandler->on($errorListenerInterfaceInstance);
````
# ServerErrorEvent
```php
$request = $event->getRequest();
$exception = $event->getThrowable();
$response = $event->getResponse();
````
