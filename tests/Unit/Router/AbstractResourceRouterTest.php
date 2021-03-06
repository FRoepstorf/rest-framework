<?php
namespace Kartenmacherei\RestFramework\UnitTests\Router;

use Kartenmacherei\RestFramework\Request\Request;
use Kartenmacherei\RestFramework\RestResource\RestResource;
use Kartenmacherei\RestFramework\Router\AbstractResourceRouter;
use Kartenmacherei\RestFramework\Router\NoMoreRoutersException;
use Kartenmacherei\RestFramework\Router\ResourceRouter;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * @covers \Kartenmacherei\RestFramework\Router\AbstractResourceRouter
 */
class AbstractResourceRouterTest extends TestCase
{

    public function testRouteThrowsException()
    {
        $router = $this->getAbstractRouter();
        $this->expectException(NoMoreRoutersException::class);
        $router->route($this->getRequestMock());
    }

    public function testReturnsResourceRequest()
    {
        $restResource = $this->getRestResourceMock();
        $restResource->method('isIdentifiedBy')->willReturn(true);

        $router = $this->getAbstractRouter();
        $router->addResource($restResource);
        $router->method('canRoute')->willReturn(true);

        $this->assertSame($restResource, $router->route($this->getRequestMock()));
    }

    public function testHandsRequestToNextRouterAndReturnsResult()
    {
        $restResource = $this->getRestResourceMock();

        $request = $this->getRequestMock();

        $nextRouter = $this->getRouterMock();
        $nextRouter->expects($this->once())
            ->method('route')
            ->with($request)
            ->willReturn($restResource);

        $router = $this->getAbstractRouter();
        $router->method('canRoute')->willReturn(false);
        $router->setNext($nextRouter);

        $this->assertSame($restResource, $router->route($request));
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|ResourceRouter
     */
    private function getRouterMock()
    {
        return $this->createMock(ResourceRouter::class);
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|RestResource
     */
    private function getRestResourceMock()
    {
        return $this->createMock(RestResource::class);
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|Request
     */
    private function getRequestMock()
    {
        return $this->createMock(Request::class);
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|AbstractResourceRouter
     */
    private function getAbstractRouter()
    {
        return $this->getMockForAbstractClass(AbstractResourceRouter::class);
    }
}
