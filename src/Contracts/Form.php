<?php
declare(strict_types=1);

namespace Devpark\Transfers24\Contracts;

interface Form
{
    public function getUri():string;
    public function getMethod():string;

    public function toArray():array;

    public function getOrderId():string;

    public function getSessionId():string;
}
