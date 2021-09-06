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
     * @param array $crc_array
     * @return string
     */
    private function expectedCrc(array $keys, array $values): string
    {
        $crc_array = array_combine($keys, $values);
        if (!empty($this->salt)){
            $crc_array += ['salt' => $this->salt];
        }
        return md5(implode('|', $crc_array));
    }


}
