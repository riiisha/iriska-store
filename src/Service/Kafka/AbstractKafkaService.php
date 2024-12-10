<?php

namespace App\Service\Kafka;

use RdKafka\Conf;
use RdKafka\Producer;
use RdKafka\ProducerTopic;
use RuntimeException;
use SimPod\KafkaBundle\Kafka\Configuration;

abstract class AbstractKafkaService
{
    protected Producer $producer;
    protected ProducerTopic $topic;

    public const DEFAULT_TIMEOUT = 1000;
    public const FLUSH_RETRY_COUNT = 10;

    public function __construct(private readonly Configuration $configuration, string $topicName)
    {
        $this->producer = new Producer(new Conf());
        $this->producer->addBrokers($this->configuration->getBootstrapServers());
        $this->topic = $this->producer->newTopic($topicName);
    }

    protected function sendMessage(
        mixed $message,
        int $partition = RD_KAFKA_PARTITION_UA,
        int $timeout = self::DEFAULT_TIMEOUT
    ): void
    {
        $payload = json_encode($message);
        $this->topic->produce($partition, 0, $payload);

        for ($i = 0; $i < self::FLUSH_RETRY_COUNT; ++$i) {
            if ($this->producer->flush($timeout) === RD_KAFKA_RESP_ERR_NO_ERROR) {
                return;
            }
        }

        throw new RuntimeException($payload);
    }
}