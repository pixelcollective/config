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
     * @covers \TinyPixel\Config\Bootloader::__construct
     */
    public function testRootsConfig()
    {
        $this->assertEquals(
            $this->bootloader::$rootsConfig,
            \Roots\WPConfig\Config::class
        );
    }

    /**
     * Test: bedrock directory is set
     *
     * @covers \TinyPixel\Config\Bootloader::init
     */
    public function testBedrockDir()
    {
        $this->bootloader->init($this->bedrockDir);

        $this->assertEquals($this->bootloader->bedrockDir, __DIR__ . '/..');
    }

    /**
     * Test: environmental variables are set
     *
     * @depends testBedrockDir
     * @covers  \TinyPixel\Config\Bootloader::loadEnv
     */
    public function testEnv()
    {
        $this->bootloader->init($this->mockDir);

        $this->bootloader->loadEnv();

        $this->assertIsObject($this->bootloader->env);
    }

    /**
     * Test: define
     *
     * @depends testEnv
     * @covers  \TinyPixel\Config\Bootloader::define
     * @covers  \TinyPixel\Config\Bootloader::get
     */
    public function testGetDefine()
    {
        $medium  = 'test';
        $message = 'passes';

        $this->bootloader::define($medium, $message);

        $this->assertEquals($this->bootloader::get($medium), $message);
    }

    /**
     * Test: defineSet
     *
     * @depends testEnv
     * @covers  \TinyPixel\Config\Bootloader::define
     * @covers  \TinyPixel\Config\Bootloader::defineSet
     * @covers  \TinyPixel\Config\Bootloader::get
     */
    public function testGetDefineSet()
    {
        $medium  = 'test';
        $message = 'passes';

        $this->bootloader->defineSet([$medium => $message]);

        $this->assertEquals($this->bootloader::get($medium), $message);
    }

    /**
     * Test: define database
     *
     * @depends testEnv
     * @depends testGetDefineSet
     * @covers  \TinyPixel\Config\Bootloader::defineDB
     */
    public function testDB()
    {
        $medium  = 'test';
        $message = 'passes';

        $this->bootloader->defineDB([$medium => $message]);

        $this->assertEquals($this->bootloader::get($medium), $message);
    }

    /**
     * Test: define FS
     *
     * @depends testEnv
     * @depends testGetDefineSet
     * @covers  \TinyPixel\Config\Bootloader::defineFS
     */
    public function testFS()
    {
        $fakeFS = [
            'CONTENT_DIR' => '/lady/ada',
            'WP_HOME'     => 'https://foo.bar',
        ];

        $this->bootloader->defineFS($fakeFS);

        $this->assertEquals(
            $this->bootloader::get('WP_CONTENT_URL'),
            "{$fakeFS['WP_HOME']}/{$fakeFS['CONTENT_DIR']}"
        );
    }

    /**
     * Test: define environments
     *
     * @depends testEnv
     * @depends testGetDefineSet
     * @covers  \TinyPixel\Config\Bootloader::defineDB
     */
    public function test()
    {
        $medium  = 'test';
        $message = 'passes';

        $this->bootloader->defineEnvironments([$medium => $message]);

        $this->assertEquals($this->bootloader::get($medium), $message);
    }
}
