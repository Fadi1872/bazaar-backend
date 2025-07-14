<?php

namespace app\Contracts;

abstract class Roles {
    public const ADMIN = "admin";
    public const INSPECTOR = "inspector";
    public const SELLER = "seller";

    public static function all() {
        return [
            Roles::ADMIN,
            Roles::INSPECTOR,
            Roles::SELLER
        ];
    }
}
