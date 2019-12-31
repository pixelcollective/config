<?php

namespace TinyPixel\Config;

use \PHPUnit\Framework\TestCase;

/**
 * Bootloader Test
 *
 * @package   TinyPixel
 * @author    Kelly Mears <developers@tinypixel.dev>
 * @copyright 2019 Kelly Mears
 * @license   MIT <https://github.com/pixelcollective/tinypixel.dev/tree/master/LICENSE.md>
 * @link      Tiny Pixel <https://tinypixel.dev>
 *
 * @coversDefaultClass \TinyPixel\Config\Bootloader
 */
final class BootloaderTest extends TestCase
{
    /**
     * class instance
     */
    public $bootloader;

    /**
     * @string
     */
    public $bedrockDir = __DIR__ . '/..';

    /**
     * @string
     */
    public $mockDir = __DIR__ . '/mock';

    /**
     * Set up
     *
     * @covers \TinyPixel\Config\Bootloader
     */
    public function setUp(): void
    {
        $this->bootloader = new  \TinyPixel\Config\Bootloader();
    }

    /**
     * Test: Roots configuration class loads
     *
     * @coversNothing
     */
    public function testIfRootsConfigLoads()
    {
        $this->assertEquals(
            $this->bootloader::$rootsConfig,
            \Roots\WPConfig\Config::class
        );
    }

    /**
     * Test: bedrock directory is set
     *
     * @covers       ::init
     */
    public function testIfBedrockDirIsSet()
    {
        $this->bootloader->init($this->bedrockDir);

        $this->assertEquals($this->bootloader->bedrockDir, __DIR__ . '/..');
    }

    /**
     * Test: environmental variables are set
     *
     * @depends testIfBedrockDirIsSet
     * @covers  ::loadEnv
     */
    public function testIfEnvIsSet()
    {
        $this->bootloader->init($this->mockDir);

        $this->bootloader->loadEnv();

        $this->assertIsObject($this->bootloader->env);
    }
}
