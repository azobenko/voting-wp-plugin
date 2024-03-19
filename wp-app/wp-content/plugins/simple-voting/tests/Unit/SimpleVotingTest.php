<?php

namespace Unit;

use SimpleVoting;
use WP_Mock\Tools\TestCase;

/**
 * @covers SimpleVoting
 */
final class SimpleVotingTest  extends TestCase
{
    public SimpleVoting $plugin_class;

    public function setUp() : void
    {
        parent::setUp();
        \WP_Mock::setUp();

        $plugin_root_dir = dirname(__DIR__, 2);

        \WP_Mock::userFunction(
            'plugin_dir_path',
            array(
                'args'   => array( \WP_Mock\Functions::type( 'string' ) ),
                'return' => $plugin_root_dir . '/src/'
            )
        );

        $this->plugin_class = new SimpleVoting();
    }

    public function tearDown() : void
    {
        parent::tearDown();
        \WP_Mock::tearDown();
    }

    public function testGetVersion(): void
    {
        $version = $this->plugin_class->get_version();
        $this->assertNotEmpty( $version );
        $this->assertIsString( $version );
    }

    public function testGetPluginName(): void
    {
        $name = $this->plugin_class->get_name();
        $this->assertNotEmpty( $name );
        $this->assertIsString( $name );
    }

}