<?php

declare(strict_types=1);

namespace App\Request;

use App\Entity\Interfaces\IdentifiableInterface;
use App\Entity\Interfaces\UploadableInterface;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use JMS\Serializer\ContextFactory\DeserializationContextFactoryInterface;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerInterface;
use ReflectionException;
use ReflectionNamedType;
use ReflectionProperty;
use Symfony\Component\HttpFoundation\Request;

final class JsonRequestDeserializer
{
    public function __construct(
        private readonly SerializerInterface                    $serializer,
        private readonly DeserializationContextFactoryInterface $contextFactory,
        private readonly ManagerRegistry                        $registry
    ) {
    }

    public function deserialize(Request $request, string $class): object
    {
        $context = $this->contextFactory->createDeserializationContext();

        if ($request->isMethod(Request::METHOD_GET)) {
            return $this->deserializeRequestQuery($request, $class, $context);
        }

        if (str_contains($request->headers->get('Content-Type'), 'multipart/form-data') &&
            (new $class) instanceof UploadableInterface
        ) {
            return $this->deserializeFormData($request, $class, $context);
        }

        return $this->deserializeRequestContent($request, $class, $context);
    }

    private function deserializeRequestQuery(Request $request, string $class, DeserializationContext $context): object
    {
        if (! $this->serializer instanceof Serializer) {
            throw new \LogicException(sprintf('%s cannot deserialize from array', $this->serializer::class));
        }

        return $this->serializer->fromArray(
            $request->query->all(),
            $class,
            $context
        );
    }

    /**
     * @throws ReflectionException
     */
    private function deserializeFormData(Request $request, string $class, DeserializationContext $context)
    {
        $object = new $class;
        $file = $request->files->get('file');
        $object->setFile($file);

        $formData = $request->request->all();
        foreach ($formData as $key => $value) {
            if (property_exists($object, $key)) {
                $reflect = new \ReflectionClass($class);
                if ($reflect->implementsInterface(IdentifiableInterface::class)) {
                    $rProperty = new ReflectionProperty($class, $key);
                    /** @var ReflectionNamedType $type */
                    $type = $rProperty->getType();
                    $em = $this->getManager($type->getName());
                    $value = $em->getRepository($type->getName())->find($value);
                }

                $setter = 'set' . ucwords($key);
                if (method_exists($object, $setter)) {
                    $object->$setter($value);
                }
            }
        }

        return $object;
    }

    private function deserializeRequestContent(Request $request, string $class, DeserializationContext $context): object
    {
        return $this->serializer->deserialize(
            (string) $request->getContent(),
            $class,
            'json',
            $context
        );
    }

    private function getManager($class, string $name = null): ObjectManager
    {
        if (null === $name) {
            return $this->registry->getManagerForClass($class);
        }

        return $this->registry->getManager($name);
    }
}
