<?php

declare(strict_types=1);

namespace App\Serializer;

use App\Entity\Interfaces\IdentifiableInterface;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use JMS\Serializer\Context;
use JMS\Serializer\GraphNavigatorInterface;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\Visitor\DeserializationVisitorInterface;
use JMS\Serializer\Visitor\SerializationVisitorInterface;

final class AppEntityHandler implements SubscribingHandlerInterface
{
    public const APP_ENTITY_NAMESPACE = 'App\Entity';
    public const APP_ENTITY_TYPE = 'AppEntity';

    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribingMethods(): array
    {
        return [
            [
                'type' => self::APP_ENTITY_TYPE,
                'direction' => GraphNavigatorInterface::DIRECTION_DESERIALIZATION,
                'format' => 'json',
                'method' => 'deserialize',
            ],
            [
                'type' => self::APP_ENTITY_TYPE,
                'direction' => GraphNavigatorInterface::DIRECTION_SERIALIZATION,
                'format' => 'json',
                'method' => 'serialize',
            ],
        ];
    }

    /**
     * Deserialize ID to entity.
     *
     * @param int|string|null $id
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function deserialize(
        DeserializationVisitorInterface $visitor,
                                        $id,
        array $type,
        Context $context
    ): ?IdentifiableInterface {
        if (empty($id)) {
            return null;
        }

        if ($id instanceof IdentifiableInterface) {
            return $id;
        }

        $className = $type['params'][0];
        if (is_array($className)) {
            $className = $className['name'];
        }

        if (empty($className)) {
            throw new InvalidArgumentException('Required parameter className missing');
        }

        return $this->findEntityById($className, (int) $id);
    }

    /**
     * Serialize Entity to Integer.
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function serialize(
        SerializationVisitorInterface $visitor,
        IdentifiableInterface $entity
    ): ?int {
        return $entity->getId();
    }

    /**
     * Finds entity in EntityManager.
     *
     * @psalm-suppress LessSpecificReturnStatement
     * @psalm-suppress MoreSpecificReturnType
     */
    private function findEntityById(string $entityName, int $id): ?IdentifiableInterface
    {
        // TODO: LOG NOT VALID RELATIONS?
        /** @psalm-var class-string $entityName */
        $entityName = self::APP_ENTITY_NAMESPACE . '\\' . $entityName;

        return $this->entityManager->find($entityName, $id);
    }
}
