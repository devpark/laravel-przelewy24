<?php

namespace Tests;

use Devpark\Transfers24\Services\Crc;

class CrcTest extends UnitTestCase
{

    /**
     * @var Crc
     */
    private $crc;
    /**
     * @var string
     */
    private $salt;

    protected function setUp()
    {
        parent::setUp();

        $this->salt = 'salt';

        $this->crc = new Crc();
        $this->crc->setSalt($this->salt);
    }


    /**
     * @Feature Payments
     * @Scenario Payment Form
     * @Case Calculate Crc
     * @test
     */
    public function test_calculate_CRC_sum()
    {

        //Given
        $keys = [
            'a',
            'b',
        ];
        $values = [
            'a' => '123456789',
            'b' => 'abcd',
        ];

        //When
        $crc = $this->crc->sum($keys, $values);

        //Then
        $expected_crc = $this->expectedCrc($keys, $values);
        $this->assertSame($expected_crc, $crc);
    }

    /**
     * @Feature Payments
     * @Scenario Payment Form
     * @Case Calculate Crc without salt
     * @test
     */
    public function test_calculate_CRC_sum_without_salt()
    {

        //Given
        $keys = [
            'a',
            'b',
        ];
        $values = [
            'a' => '123456789',
            'b' => 'abcd',
        ];

        //When
        $this->salt = '';
        $this->crc->setSalt($this->salt);
        $crc = $this->crc->sum($keys, $values);

        //Then
        $expected_crc = $this->expectedCrc($keys, $values);
        $this->assertSame($expected_crc, $crc);
    }


    /**
     * @Feature Payments
     * @Scenario Payment Form
     * @Case Calculate Crc with empty data
     * @test
     */
    public function test_return_null_after_calculate_CRC_with_empty_value()
    {

        //Given
        $keys = [
            'a',
            'b',
        ];
        $values = [];

        //When
        $crc = $this->crc->sum($keys, $values);

        //Then
        $this->assertEmpty($crc);
    }

    /**
     * @Feature Payments
     * @Scenario Receive Status
     * @Case Failed check sum
     * @test
     */
    public function test_is_false_check_sum()
    {
        $post_data = [
            'p24_session_id' => '1234',
            'p24_order_id' => '5678',
            'p24_amount' => 'abcd',
            'p24_currency' => 'efgh',
            'p24_sign'  => '1234567689',
        ];
        $crc_test = $this->crc->checkSum($post_data);
        $this->assertFalse($crc_test);
    }


    /**
     * @Feature Payments
     * @Scenario Receive Status
     * @Case Check sum successful
     * @test
     */
    public function test_is_true_check_sum()
    {
        $post_data = [
            'sessionId' => '1234',
            'p24_order_id' => '5678',
            'p24_amount' => 'abcd',
            'p24_currency' => 'efgh',
        ];
        $crc_array = $post_data + ['salt' => 'salt'];
        $crc = hash('sha384', json_encode($crc_array, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        $post_data['p24_sign'] = $crc;

        $crc_test = $this->crc->checkSum($post_data);
        $this->assertTrue($crc_test);
    }

    /**
     * @param array $crc_array
     * @return string
     */
    private function expectedCrc(array $keys, array $values): string
    {
        $crc_array = array_combine($keys, $values);
        if (!empty($this->salt)){
            $crc_array += ['crc' => $this->salt];
        }
        return hash('sha384', json_encode($crc_array, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }


}
