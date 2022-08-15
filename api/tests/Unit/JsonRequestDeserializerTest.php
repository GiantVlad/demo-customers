<?php

namespace App\Tests\Unit;

use App\Entity\Contact;
use App\Request\JsonRequestDeserializer;
use Doctrine\Persistence\ManagerRegistry;
use JMS\Serializer\ContextFactory\DeserializationContextFactoryInterface;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\SerializerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request;

class JsonRequestDeserializerTest extends TestCase
{
    public function testDeserialize()
    {
        $context = $this->createMock(DeserializationContext::class);

        $registry = $this->createMock(ManagerRegistry::class);

        $headerBag = $this->createMock(HeaderBag::class);
        $headerBag->expects($this->once())
            ->method('get')
            ->willReturn('json');

        $request = $this->createMock(Request::class);
        $request->expects($this->once())
            ->method('isMethod')
            ->with(Request::METHOD_GET)
            ->willReturn(false);
        $request->expects($this->once())
            ->method('getContent')
            ->willReturn('content');
        $request->headers = $headerBag;

        $serializer = $this->createMock(SerializerInterface::class);
        $serializer->expects($this->once())
            ->method('deserialize')
            ->with('content', Contact::class, 'json', $context)
            ->willReturn(new Contact);
        $contextFactory = $this->createMock(DeserializationContextFactoryInterface::class);
        $contextFactory->expects($this->once())
            ->method('createDeserializationContext')
            ->willReturn($context);



        $jsonRequestDeserializer = new JsonRequestDeserializer($serializer, $contextFactory, $registry);

        $contact = $jsonRequestDeserializer->deserialize($request, Contact::class);

        $this->assertInstanceOf(Contact::class, $contact);
    }
}
