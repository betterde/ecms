<?php

namespace App\Contracts;

/**
 * Interface UserTypeInterface
 * @package App\Contracts
 * Date: 2020/5/22
 * @author George
 * @property-read string $id
 */
interface UserTypeInterface
{
    public function getUserType(): string;
}
