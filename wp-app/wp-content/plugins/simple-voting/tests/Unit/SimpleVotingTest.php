<?php

namespace Unit;

use SimpleVoting;
use WP_Mock;
use WP_Mock\Functions;
use WP_Mock\Tools\TestCase;

/**
 * @covers SimpleVoting
 */
final class SimpleVotingTest extends TestCase
{
    public SimpleVoting $plugin_class;

    public function setUp(): void
    {
        parent::setUp();
        WP_Mock::setUp();
        $this->plugin_class = new SimpleVoting();
    }

    public function test_get_version(): void
    {
        $version = $this->plugin_class->get_version();
        $this->assertNotEmpty($version);
        $this->assertIsString($version);
    }

    public function test_get_plugin_name(): void
    {
        $name = $this->plugin_class->get_name();
        $this->assertNotEmpty($name);
        $this->assertIsString($name);
    }

    public function test_insert_voting_to_content()
    {
        $content = "This is some sample content.";
        // Mock is_single() function to return true (simulating a single post)
        WP_Mock::userFunction('is_single', array('return' => true));
        // Mock get_post_type() function to return 'post'
        WP_Mock::userFunction('get_post_type', array('return' => 'post'));
        $expectedContent = $content . $this->plugin_class->get_voting_html();
        $this->assertEquals($expectedContent, $this->plugin_class->insert_voting($content));
    }

    public function tearDown(): void
    {
        parent::tearDown();
        WP_Mock::tearDown();
    }
}