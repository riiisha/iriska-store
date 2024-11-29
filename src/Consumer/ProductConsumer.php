<?php

namespace App\Consumer;

use App\DTO\Product\CreateProductDTO;
use App\Service\Product\ProductService;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializerInterface;
use RdKafka\Message;
use SimPod\Kafka\Clients\Consumer\ConsumerConfig;
use SimPod\Kafka\Clients\Consumer\KafkaConsumer;
use SimPod\KafkaBundle\Kafka\Clients\Consumer\NamedConsumer;
use SimPod\KafkaBundle\Kafka\Configuration;

final class ProductConsumer implements NamedConsumer
{
    private const TIMEOUT_MS = 2000;
    private Configuration $configuration;
    private SerializerInterface $serializer;
    private ProductService $productManager;

    public function __construct(
        Configuration  $configuration,
        ProductService $productManager,
    ) {
        $this->configuration = $configuration;
        $this->serializer = (new SerializerBuilder())->build();
        $this->productManager = $productManager;
    }

    public function run(): void
    {
        $kafkaConsumer = new KafkaConsumer($this->getConfig());
        $kafkaConsumer->subscribe(['product_topic']);
        while (true) {
            $kafkaConsumer->start(
                self::TIMEOUT_MS,
                function (Message $message) use ($kafkaConsumer): void {
                    $newProduct = $this->serializer->deserialize($message->payload, CreateProductDTO::class, 'json');
                    $this->productManager->createOrUpdateProduct($newProduct);
                    $kafkaConsumer->commit($message);
                }
            );
        }
    }

    public function getName(): string
    {
        return 'product_consumer';
    }

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
