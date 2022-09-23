<?php
declare(strict_types=1);

namespace Amasty\CheckoutProCustomization\Test\Unit\Model\Address;

use Amasty\CheckoutProCustomization\Model\Address\Comparer;
use Magento\Customer\Api\Data\AddressInterface;
use Magento\Customer\Model\Address;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ComparerTest extends TestCase
{

    /**
     * @var Comparer
     */
    private $comparer;

    protected function setUp(): void
    {
        $this->comparer = new Comparer(
            [
                AddressInterface::CITY,
                AddressInterface::COUNTRY_ID
            ]
        );
    }

    /**
     * @param Address|MockObject $address1
     * @param Address|MockObject $address2
     * @param bool $isEqual
     * @return void
     * @dataProvider isEqualProvider
     */
    public function testIsEqual($address1, $address2, bool $isEqual): void
    {
        $this->assertEquals($isEqual, $this->comparer->isEqual($address1, $address2));
    }

    /**
     * @return array
     */
    public function isEqualProvider(): array
    {
        $differentAddress1Mock = $this->createMock(Address::class);
        $differentAddress2Mock = $this->createMock(Address::class);
        $sameAddress1Mock = $this->createMock(Address::class);
        $sameAddress2Mock = $this->createMock(Address::class);

        $differentAddress1Mock->expects($this->any())
            ->method('getData')
            ->withConsecutive(
                [AddressInterface::CITY, null],
                [AddressInterface::COUNTRY_ID, null]
            )
            ->willReturnOnConsecutiveCalls(
                'Calder',
                'US'
            );
        $differentAddress2Mock->expects($this->any())
            ->method('getData')
            ->withConsecutive(
                [AddressInterface::CITY, null],
                [AddressInterface::COUNTRY_ID, null]
            )
            ->willReturnOnConsecutiveCalls(
                'Tokio',
                'JP'
            );
        $sameAddress1Mock->expects($this->any())
            ->method('getData')
            ->withConsecutive(
                [AddressInterface::CITY, null],
                [AddressInterface::COUNTRY_ID, null]
            )
            ->willReturnOnConsecutiveCalls(
                'Tokio',
                'JP'
            );
        $sameAddress2Mock->expects($this->any())
            ->method('getData')
            ->withConsecutive(
                [AddressInterface::CITY, null],
                [AddressInterface::COUNTRY_ID, null]
            )
            ->willReturnOnConsecutiveCalls(
                'Tokio',
                'JP'
            );
        
        return [
            [$differentAddress1Mock, $differentAddress2Mock, false],
            [$sameAddress1Mock, $sameAddress2Mock, true],
        ];
    }
}
