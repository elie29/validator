<?php

namespace Elie\Validator\Helper;

use PHPUnit\Framework\TestCase;

class TextTest extends TestCase
{

    public function testRemoveInvisibleCharsRemovesNullByte(): void
    {
        // "Java\0script" avec un null byte entre "Java" et "script"
        $input = "Java\0script";
        $expected = 'Javascript';

        $this->assertSame($expected, Text::removeInvisibleChars($input));
    }

    public function testRemoveInvisibleCharsRemovesUrlEncodedControlChars(): void
    {
        // %00 (null) et %1F (unit separator) doivent disparaître
        $input = 'Test%00String%1F';
        $expected = 'TestString';

        $this->assertSame($expected, Text::removeInvisibleChars($input));
    }

    public function testRemoveInvisibleCharsDoesNotStripNewlineAndCarriageReturn(): void
    {
        // \n (0x0A) et \r (0x0D) doivent être conservés
        $input = "Line1\nLine2\rLine3";
        $expected = "Line1\nLine2\rLine3";

        $this->assertSame($expected, Text::removeInvisibleChars($input));
    }

    public function testRemoveInvisibleCharsDoubleEncodedNull(): void
    {
        // Double-encoded null byte %2500
        $input = 'Test%2500String';

        $result = Text::removeInvisibleChars($input);

        // removeInvisibleChars only removes direct %00, double encoding survives
        $this->assertSame('Test%2500String', $result);
    }

    public function testRemoveInvisibleCharsTripleEncoded(): void
    {
        // Triple-encoded null: %25%32%35%30%30
        $input = 'Test%25%32%35%30%30String';
        $result = Text::removeInvisibleChars($input);

        // Triple encoding is not decoded by removeInvisibleChars
        $this->assertSame('Test%25%32%35%30%30String', $result);
    }

    public function testFilterCRLFInjection(): void
    {
        // CRLF injection: %0D%0A
        $input = "Header:%0D%0AX-Injected: true";
        $result = Text::removeInvisibleChars($input);

        // URL-encoded CRLF should be removed
        $this->assertStringNotContainsString('%0D', $result);
        $this->assertStringNotContainsString('%0A', $result);
    }

    public function testFilterNullByteInjection(): void
    {
        // Null byte to truncate string
        $input = "file.php%00.jpg";
        $result = Text::removeInvisibleChars($input);

        // %00 should be removed
        $this->assertSame('file.php.jpg', $result);
    }

    public function testRemoveInvisibleCharsMixedEncoding(): void
    {
        // Mix of literal and encoded control chars
        $input = "Test\x00%00String";
        $result = Text::removeInvisibleChars($input);

        $this->assertSame('TestString', $result);
    }

    public function testRemoveInvisibleCharsUTF8NullByte(): void
    {
        // UTF-8 encoded null (C0 80 is overlong encoding)
        // Now properly filtered to prevent security bypass
        $input = "Test\xC0\x80String";
        $result = Text::removeInvisibleChars($input);

        // Overlong UTF-8 sequence should be removed
        $this->assertSame('TestString', $result);
    }

    public function testRemoveInvisibleCharsUnicodeZeroWidthSpace(): void
    {
        // Unicode zero-width space U+200B (E2 80 8B in UTF-8)
        $input = "Test\xE2\x80\x8BString";
        $result = Text::removeInvisibleChars($input);

        // Zero-width space should now be filtered
        $this->assertSame('TestString', $result);
    }

    public function testRemoveInvisibleCharsUnicodeRightToLeftOverride(): void
    {
        // U+202E (RLO - Right-to-Left Override)
        $input = "Test\xE2\x80\xAEString";
        $result = Text::removeInvisibleChars($input);

        // RLO is now filtered to prevent phishing attacks
        $this->assertSame('TestString', $result);
    }

    public function testRemoveInvisibleCharsTabAndNewline(): void
    {
        // Tab (\t = 0x09) should be removed, but newline (\n = 0x0A) preserved
        $input = "Line1\tTab\nLine2";
        $result = Text::removeInvisibleChars($input);

        // Tab removed, newline preserved
        $this->assertStringNotContainsString("\t", $result);
        $this->assertStringContainsString("\n", $result);
        $this->assertSame("Line1Tab\nLine2", $result);
    }

    public function testRemoveInvisibleCharsVerticalTab(): void
    {
        // Vertical tab (0x0B) should be removed
        $input = "Test\x0BString";
        $result = Text::removeInvisibleChars($input);

        $this->assertSame('TestString', $result);
    }

    public function testRemoveInvisibleCharsFormFeed(): void
    {
        // Form feed (0x0C) should be removed
        $input = "Test\x0CString";
        $result = Text::removeInvisibleChars($input);

        $this->assertSame('TestString', $result);
    }

    public function testRemoveInvisibleCharsDEL(): void
    {
        // DEL character (0x7F) should be removed
        $input = "Test\x7FString";
        $result = Text::removeInvisibleChars($input);

        $this->assertSame('TestString', $result);
    }

    public function testRemoveInvisibleCharsEmptyString(): void
    {
        $result = Text::removeInvisibleChars('');

        $this->assertSame('', $result);
    }

    public function testRemoveInvisibleCharsLongString(): void
    {
        // Test with a long string containing scattered control chars
        $input = str_repeat(
                'a', 1000) . "\x00" . str_repeat('b', 1000) . '%00'
            . str_repeat('c', 1000);
        $result = Text::removeInvisibleChars($input);

        $this->assertStringNotContainsString("\x00", $result);
        $this->assertStringNotContainsString('%00', $result);
        $this->assertEquals(3000, strlen($result));
    }
}