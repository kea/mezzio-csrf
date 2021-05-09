<?php

/**
 * @see       https://github.com/mezzio/mezzio-csrf for the canonical source repository
 * @copyright https://github.com/mezzio/mezzio-csrf/blob/master/COPYRIGHT.md
 * @license   https://github.com/mezzio/mezzio-csrf/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace MezzioTest\Csrf;

use Mezzio\Csrf\Exception;
use Mezzio\Csrf\FlashCsrfGuard;
use Mezzio\Csrf\FlashCsrfGuardFactory;
use Mezzio\Flash\FlashMessageMiddleware;
use Mezzio\Flash\FlashMessagesInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

class FlashCsrfGuardFactoryTest extends TestCase
{
    public function testConstructionUsesSaneDefaults(): void
    {
        $factory = new FlashCsrfGuardFactory();
        /**
         * TODO: Replace checks to internal properties
         */
        //$this->assertAttributeSame(FlashMessageMiddleware::FLASH_ATTRIBUTE, 'attributeKey', $factory);
    }

    public function testConstructionAllowsPassingAttributeKey(): void
    {
        $factory = new FlashCsrfGuardFactory('alternate-attribute');
        /**
         * TODO: Replace checks to internal properties
         */
        //$this->assertAttributeSame('alternate-attribute', 'attributeKey', $factory);
    }

    public function attributeKeyProvider(): array
    {
        return [
            'default' => [FlashMessageMiddleware::FLASH_ATTRIBUTE],
            'custom'  => ['custom-flash-attribute'],
        ];
    }

    /**
     * @dataProvider attributeKeyProvider
     */
    public function testCreateGuardFromRequestRaisesExceptionIfAttributeDoesNotContainFlash(string $attribute): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects(self::atLeastOnce())->method('getAttribute')->with($attribute, false)->willReturn(false);

        $factory = new FlashCsrfGuardFactory($attribute);

        $this->expectException(Exception\MissingFlashMessagesException::class);
        $factory->createGuardFromRequest($request);
    }

    /**
     * @dataProvider attributeKeyProvider
     */
    public function testCreateGuardFromRequestReturnsCsrfGuardWithSessionWhenPresent(string $attribute): void
    {
        $flash   = $this->createMock(FlashMessagesInterface::class);
        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects(self::atLeastOnce())->method('getAttribute')->with($attribute, false)->willReturn($flash);

        $factory = new FlashCsrfGuardFactory($attribute);

        $guard = $factory->createGuardFromRequest($request);
        $this->assertInstanceOf(FlashCsrfGuard::class, $guard);
    }
}
