<?php

namespace Bermuda\ErrorHandler;

use Whoops\RunInterface;
use Psr\Container\ContainerInterface;
use Bermuda\Templater\RendererInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Bermuda\ErrorHandler\Generator\WhoopsErrorGenerator;
use Bermuda\ErrorHandler\Generator\TemplateErrorGenerator;

class ErrorResponseGeneratorFactory
{
    public function __invoke(ContainerInterface $container): ErrorResponseGeneratorInterface
    {
        if ($container->get('config')['debug'])
        {
            if ($container->has(RunInterface::class))
            {
                $run = $container->get(RunInterface::class);
            }

            return new WhoopsErrorGenerator($container->get(ResponseFactoryInterface::class), $run ?? null);
        }
        
        return new TemplateErrorGenerator(static function ($code) use ($c)
        {
            return $c->get(RendererInterface::class)->render('errors::' . $code);
        }, $c->get(ResponseFactoryInterface::class));
    }
}
