<?php

namespace Bermuda\ErrorHandler;

use Whoops\RunInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Bermuda\ErrorHandler\Generator\WhoopsErrorGenerator;
use Bermuda\ErrorHandler\Generator\TemplateErrorGenerator;

final class ErrorResponseGeneratorFactory
{
    public function __invoke(ContainerInterface $container): WhoopsErrorGenerator|TemplateErrorGenerator|JsonErrorGenerator
    {
        if ($container->get('config')['errors']['mode'] == 'whoops') {
            if ($container->has(RunInterface::class)) {
                $run = $container->get(RunInterface::class);
            }

            return new WhoopsErrorGenerator($container->get(ResponseFactoryInterface::class), $run ?? null);
        }
        
        if ($container->has('Bermuda\Templater\RendererInterface') && $container->get('config')['errors']['mode'] == 'template') {
            return new TemplateErrorGenerator(static function ($code) use ($container) {
                return $container->get('Bermuda\Templater\RendererInterface')->render('errors::' . $code);
            }, $container->get(ResponseFactoryInterface::class));
        }
       
        return new JsonErrorGenerator($container->get(ResponseFactoryInterface::class));
    }
}
