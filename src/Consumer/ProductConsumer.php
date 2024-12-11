<?php

namespace App\Consumer;

use App\DTO\Product\CreateProductDTO;
use App\Service\Product\ProductService;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use Psr\Log\LoggerInterface;
use RdKafka\Message;
use SimPod\KafkaBundle\Kafka\Configuration;

final class ProductConsumer extends AbstractConsumer
{
    private Serializer $serializer;

    public function __construct(
        Configuration           $configuration,
        LoggerInterface         $logger,
        readonly ProductService $productManager,
    ) {
        parent::__construct($configuration, $logger);
        $this->serializer = (new SerializerBuilder())->build();
    }

    public function getName(): string
    {
        return 'product_consumer';
    }

    protected function getTopicName(): string
    {
        return 'product';
    }

    protected function processMessage(Message $message): void
    {
        $newProduct = $this->serializer->deserialize($message->payload, CreateProductDTO::class, 'json');
        $this->productManager->createOrUpdateProduct($newProduct);
    }
}
