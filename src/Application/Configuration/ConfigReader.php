<?php

declare(strict_types=1);

namespace Pvg\Application\Configuration;

use Psr\Log\LoggerInterface;
use Pvg\Application\Configuration\ValueObject\EventListenerConfig;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher;
use Symfony\Component\Yaml\Yaml;

class ConfigReader
{
    /** @var FileLocator */
    private $fileLocator;

    /** @var string */
    private $configPath;

    /** @var array */
    private $configArray;

    /** @var LoggerInterface */
    private $logger;

    /** @var string */
    private $eventListenerConfigPath;

    /** @var ContainerAwareEventDispatcher */
    private $dispatcher;

    /**
     * Create an global application config from given file.
     */
    public function __construct(
        FileLocator $fileLocator,
        string $applicationConfigPath,
        string $eventListenerConfigPath,
        ContainerAwareEventDispatcher $dispatcher,
        LoggerInterface $logger
    ) {
        $this->fileLocator             = $fileLocator;
        $this->configPath              = $applicationConfigPath;
        $this->logger                  = $logger;
        $this->eventListenerConfigPath = $eventListenerConfigPath;
        $this->dispatcher              = $dispatcher;
    }

    public function init() : void
    {
        if (empty($this->configArray)) {
            $this->configArray = $this->getYamlFileContent($this->configPath);
        }

        $eventListenerConfig = $this->getYamlFileContent($this->eventListenerConfigPath);
        $this->initEventDispatcher(EventListenerConfig::createFromConfigArray($eventListenerConfig));
    }

    public function get() : array
    {
        if (empty($this->configArray)) {
            throw new InvalidConfigurationException('Invalid configuration: empty');
        }

        return $this->configArray;
    }

    /**
     * @throws \Symfony\Component\Config\Exception\FileLocatorFileNotFoundException
     * @throws \Symfony\Component\Yaml\Exception\ParseException
     * @throws \InvalidArgumentException
     */
    private function getYamlFileContent(string $path) : array
    {
        $fullPath = $this->fileLocator->locate($path);
        $this->logger->info('Reading config from {path}', ['path' => $fullPath]);

        return Yaml::parse(file_get_contents($fullPath));
    }

    /**
     * @param EventListenerConfig[] $eventListenerConfig
     *
     * @throws \InvalidArgumentException
     */
    private function initEventDispatcher(array $eventListenerConfig) : void
    {
        /** @var EventListenerConfig $singleListenerConfig */
        foreach ($eventListenerConfig as $singleListenerConfig) {
            $this->dispatcher->addListenerService(
                $singleListenerConfig->event(),
                [
                    $singleListenerConfig->listenerServiceId(),
                    $singleListenerConfig->action(),
                ]
            );
        }
    }
}
