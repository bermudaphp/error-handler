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
    public function __invoke(ContainerInterface $c): ErrorResponseGeneratorInterface
    {
        if ($c->get('config')['debug'])
        {
            if ($c->has(RunInterface::class))
            {
                $run = $c->get(RunInterface::class);
            }

            return new WhoopsErrorGenerator($c->get(ResponseFactoryInterface::class), $run ?? null);
        }
        
        return new TemplateErrorGenerator(static function ($code) use ($c)
        {
            return $c->get(RendererInterface::class)->render('error::' . $code);
        }, $c->get(ResponseFactoryInterface::class));
    }
}