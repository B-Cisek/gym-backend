<?php

declare(strict_types=1);

namespace App\Tests\Unit\Owner\Domain;

use App\Owner\Domain\InvalidTaxIdException;
use App\Owner\Domain\TaxId;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class TaxIdTest extends TestCase
{
    #[Test]
    public function it_creates_from_valid_ten_digit_nip(): void
    {
        $taxId = TaxId::fromString('1234563218');

        self::assertSame('1234563218', $taxId->value);
    }

    #[Test]
    public function it_returns_value_as_string(): void
    {
        $taxId = TaxId::fromString('1234563218');

        self::assertSame('1234563218', (string) $taxId);
    }

    #[Test]
    public function it_trims_whitespace(): void
    {
        $taxId = TaxId::fromString('  1234563218  ');

        self::assertSame('1234563218', $taxId->value);
    }

    #[Test]
    public function it_returns_true_when_equal(): void
    {
        $taxId = TaxId::fromString('1234563218');
        $other = TaxId::fromString('1234563218');

        self::assertTrue($taxId->equals($other));
    }

    #[Test]
    public function it_returns_false_when_not_equal(): void
    {
        $taxId = TaxId::fromString('1234563218');
        $other = TaxId::fromString('9999999999');

        self::assertFalse($taxId->equals($other));
    }

    #[Test]
    public function it_throws_on_empty_string(): void
    {
        $this->expectException(InvalidTaxIdException::class);

        TaxId::fromString('');
    }

    #[Test]
    public function it_throws_on_whitespace_only(): void
    {
        $this->expectException(InvalidTaxIdException::class);

        TaxId::fromString('   ');
    }

    #[Test]
    public function it_throws_on_nine_digits(): void
    {
        $this->expectException(InvalidTaxIdException::class);

        TaxId::fromString('123456789');
    }

    #[Test]
    public function it_throws_on_eleven_digits(): void
    {
        $this->expectException(InvalidTaxIdException::class);

        TaxId::fromString('12345632181');
    }

    #[Test]
    public function it_throws_on_letters(): void
    {
        $this->expectException(InvalidTaxIdException::class);

        TaxId::fromString('123456789A');
    }

    #[Test]
    public function it_throws_on_dashes(): void
    {
        $this->expectException(InvalidTaxIdException::class);

        TaxId::fromString('123-456-78-90');
    }

    #[Test]
    public function it_throws_on_special_characters(): void
    {
        $this->expectException(InvalidTaxIdException::class);

        TaxId::fromString('1234 56789');
    }
}
