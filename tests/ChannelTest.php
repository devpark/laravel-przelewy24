<?php

namespace Tests;

use Devpark\Transfers24\Channel;

class ChannelTest extends UnitTestCase
{
    protected function setUp()
    {
        parent::setUp();
    }

    /** @test */
    public function get_one_from_available_channel()
    {
        $channel = '8';
        $pass_channel = Channel::get($channel);
        $this->assertEquals($pass_channel, '8');

        $default_channel = '16';
        $channel = 'other';
        $pass_channel = Channel::get($channel);
        $this->assertEquals($pass_channel, $default_channel);
    }
}
