# Unias PHP Client

LIG Unified Account Service OAuth 2.0 Client Provider for The PHP League OAuth2-Client.

## Requirements

- `PHP >= 7.0`

## Usage
### Installation

```sh
composer require liginc/unias-client-php
```

### Setting up a new client

```php
new UniasProvider([
    'clientId'     => 'XXXXXXXXXXXXXXXXXXXX',
    'clientSecret' => 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX',
    'redirectUri'  => 'https://yourservice.example.com/login/callback',
    'authorizeUri' => 'https://unias.example.com/',
    'tokenUri'     => 'https://api.unias.example.com/token',
    'apiBaseUri'   => 'https://api.example.com/',
]);
```

#### Parameters

- **`clientId`:** A registered [*Client ID*](https://tools.ietf.org/html/rfc6749#section-2.2) provided by the [*Authorization Server*](https://tools.ietf.org/html/rfc6749#section-1.1) (Unias service provider).
- **`clientSecret`:** A [*Client Password*](https://tools.ietf.org/html/rfc6749#section-2.3.1) registered with the *Client ID* to the *Authorization Server*.
- **`redirectUri`:** An URI to the [*Redirection Endpoint*](https://tools.ietf.org/html/rfc6749#section-3.1.2) of the *Client* (Your Service).
- **`authorizeUri`:** An URI to the [*Authorization Endpoint*](https://tools.ietf.org/html/rfc6749#section-3.1) of the *Authorization Server*.
- **`tokenUri`:** An URI to the [*Token Endpoint*](https://tools.ietf.org/html/rfc6749#section-3.2) of the *Authorization Server*.
- **`apiBaseUri`:** A base URI to the API endpoints of the *Resource Server*.

## Library Documentation

This is a plug-in provider of the [PHP League's OAuth 2.0 Client](https://github.com/thephpleague/oauth2-client), so we suggest you to refer to [the library documentation](https://github.com/thephpleague/oauth2-client/blob/master/README.md#usage) for detailed usage.
