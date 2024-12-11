<?php

namespace App\Consumer;

use Psr\Log\LoggerInterface;
use RdKafka\Message;
use SimPod\Kafka\Clients\Consumer\ConsumerConfig;
use SimPod\Kafka\Clients\Consumer\KafkaConsumer;
use SimPod\KafkaBundle\Kafka\Clients\Consumer\NamedConsumer;
use SimPod\KafkaBundle\Kafka\Configuration;
use Throwable;

abstract class AbstractConsumer implements NamedConsumer
{
    private const TIMEOUT_MS = 2000;

    public function __construct(
        readonly Configuration   $configuration,
        readonly LoggerInterface $logger,
    ) {
    }

    public function run(): void
    {
        $kafkaConsumer = new KafkaConsumer($this->getConfig());
        $kafkaConsumer->subscribe([$this->getTopicName()]);
        while (true) {
            $kafkaConsumer->start(
                self::TIMEOUT_MS,
                function (Message $message) use ($kafkaConsumer): void {
                    try {
                        $this->processMessage($message);
                        $kafkaConsumer->commit($message);
                    } catch (Throwable $exception) {
                        $this->logger->error("[{$this->getName()}]", [
                            'message' => $exception->getMessage(),
                            'exception' => $exception->getTrace()
                        ]);
                    }
                }
            );
        }
    }
    abstract public function getName(): string;

    abstract protected function getTopicName(): string;

    abstract protected function processMessage(Message $message): void;

    private function getConfig(): ConsumerConfig
    {
        $config = new ConsumerConfig();

        $config->set(ConsumerConfig::BOOTSTRAP_SERVERS_CONFIG, $this->configuration->getBootstrapServers());
        $config->set(ConsumerConfig::ENABLE_AUTO_COMMIT_CONFIG, false);
        $config->set(ConsumerConfig::CLIENT_ID_CONFIG, $this->configuration->getClientIdWithHostname());
        $config->set(ConsumerConfig::AUTO_OFFSET_RESET_CONFIG, 'earliest');
        $config->set(ConsumerConfig::GROUP_ID_CONFIG, 'consumer_group');

        return $config;
    }
}
