<?php

namespace App\Service;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtService
{
    private $secretKey;
    private $algorithm;

    public function __construct(string $secretKey, string $algorithm = 'HS256')
    {
        $this->secretKey = $secretKey;
        $this->algorithm = $algorithm;
    }

    public function createToken(array $data): string
    {
        return JWT::encode($data, $this->secretKey, $this->algorithm);
    }


}
