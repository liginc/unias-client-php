<?php
declare(strict_types = 1);
namespace Liginc\UniasClient\Provider;

use League\OAuth2\Client\Provider\GenericResourceOwner;

class UniasResourceOwner extends GenericResourceOwner
{
    public function read($key = null)
    {
        if ($key === null) {
            return $this->response;
        }

        return $this->response[$key];
    }
}
