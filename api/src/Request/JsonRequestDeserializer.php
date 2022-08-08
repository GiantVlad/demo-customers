<?php

declare(strict_types=1);

namespace App\Request;

//use App\Exception\BadJsonRequestException;
//use App\Exception\InvalidDiscriminatorTypeException;
//use App\Exception\InvalidValueException;
//use App\Exception\RequiredPropertyException;
use App\Entity\Interfaces\IdentifiableInterface;
use App\Entity\Interfaces\UploadableInterface;
use Doctrine\Persistence\ManagerRegistry;
use JMS\Serializer\Context;
use JMS\Serializer\ContextFactory\DeserializationContextFactoryInterface;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerInterface;
use ReflectionProperty;
use Symfony\Component\HttpFoundation\Request;

final class JsonRequestDeserializer
{
    public function __construct(
        private SerializerInterface $serializer,
        private DeserializationContextFactoryInterface $contextFactory,
        private ManagerRegistry $registry
    ) {
    }

    /**
     * @psalm-template T
     *
     * @psalm-param class-string<T> $class
     *
     * @psalm-return T
     */
    public function deserialize(Request $request, string $class): object
    {
        $context = $this->contextFactory->createDeserializationContext();

        // try {
            if ($request->isMethod(Request::METHOD_GET)) {
                return $this->deserializeRequestQuery($request, $class, $context);
            }

        if (str_contains($request->headers->get('Content-Type'), 'multipart/form-data') &&
            (new $class) instanceof UploadableInterface
        ) {
            return $this->deserializeFormData($request, $class, $context);
        }

            return $this->deserializeRequestContent($request, $class, $context);
//        } catch (RuntimeException | \TypeError $e) {
//            throw BadJsonRequestException::forIncorrectPropertyType(self::getPropertyPath($context), $e);
//        } catch (RequiredPropertyException $e) {
//            throw BadJsonRequestException::forRequiredProperty(self::getPropertyPath($context, $e->getProperty()));
//        } catch (InvalidValueException|\Synchtank\Common\Exception\InvalidValueException $e) {
//            throw BadJsonRequestException::forInvalidPropertyValue(
//                self::getPropertyPath($context),
//                $e->getMessage()
//            );
//        } catch (InvalidDiscriminatorTypeException $e) {
//            throw BadJsonRequestException::forInvalidPropertyValue(
//                self::getPropertyPath($context, $e->getFieldName()),
//                $e->getMessage(),
//            );
//        }
    }

    /**
     * @psalm-template T
     *
     * @psalm-param class-string<T> $class
     *
     * @psalm-return T
     */
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
     * @psalm-template T
     *
     * @psalm-param class-string<T> $class
     *
     * @psalm-return T
     */
    private function deserializeRequestContent(Request $request, string $class, DeserializationContext $context): object
    {
        return $this->serializer->deserialize(
            (string) $request->getContent(),
            $class,
            'json',
            $context
        );
    }

    private static function getPropertyPath(Context $context, ?string $property = null): string
    {
        $currentPath = $context->getCurrentPath();

        if (null !== $property) {
            $currentPath[] = $property;
        }

        return implode('.', $currentPath);
    }

    private function deserializeFormData(Request $request, string $class, DeserializationContext $context)
    {
        $object = new $class;
        $file = $request->files->get('file');
        $object->setFile($file);

        $formData = $request->request?->all() ?? [];
        foreach ($formData as $key => $value) {
            if (property_exists($object, $key)) {
                $reflect = new \ReflectionClass($class);
                if ($reflect->implementsInterface(IdentifiableInterface::class)) {
                    $rProperty = new ReflectionProperty($class, $key);
                    $em = $this->getManager($rProperty->getType()->getName());
                    $value = $em->getRepository($rProperty->getType()->getName())->find($value);
                }

                $setter = 'set' . ucwords($key);
                if (method_exists($object, $setter)) {
                    $object->$setter($value);
                }
            }
        }

        return $object;
    }

    private function getManager($class, $name = null)
    {
        if (null === $name) {
            return $this->registry->getManagerForClass($class);
        }

        return $this->registry->getManager($name);
    }
}
