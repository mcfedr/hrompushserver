<?php

namespace Mcfedr\Hromadske\NewsBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class McfedrHromadskeNewsExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $container->setParameter('mcfedr_hromadske_news.homepage', $config['homepage']);

        $args = [
            $container->getParameter('mcfedr_hromadske_news.homepage'),
            new Reference('logger')
        ];

        if (isset($config['cache'])) {
            $args[] = new Reference($config['cache']);
            $args[] = isset($config['cache_timeout']) ? $config['cache_timeout'] : 3600;
        }

        $container->setDefinition(
            'mcfedr_hromadske_news.crawler.news',
            new Definition('Mcfedr\Hromadske\NewsBundle\Crawler\NewsCrawler', $args)
        );
    }
}
