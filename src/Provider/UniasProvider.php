<?php
declare(strict_types = 1);
namespace Liginc\UniasClient\Provider;

use BadMethodCallException;
use InvalidArgumentException;
use GuzzleHttp\Psr7\Request;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;

class UniasProvider extends AbstractProvider
{
    use BearerAuthorizationTrait;

    /**
     * @var string
     */
    protected $apiBaseUri;

    /**
     * @var string
     */
    protected $authorizeUri;

    /**
     * @var string
     */
    protected $tokenUri;

    /**
     * @param array $options
     * @param array $collaborators
     */
    public function __construct(array $options = [], array $collaborators = [])
    {
        parent::__construct($this->filterOptions($options), $collaborators);
    }

    /**
     * Get authorization url to begin OAuth flow
     *
     * @return string
     */
    public function getBaseAuthorizationUrl(): string
    {
        return $this->authorizeUri;
    }

    /**
     * Get access token url to retrieve token
     *
     * @return string
     */
    public function getBaseAccessTokenUrl(array $params): string
    {
        return $this->tokenUri;
    }

    /**
     * Get provider url to fetch user details
     *
     * @param  AccessToken $token
     *
     * @return string
     */
    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        return $this->getApiUrl() .'/v1/account/info';
    }

    /**
     * Requests a resource owner information.
     *
     * @param  string $method
     * @param  string $path
     * @param  AccessToken $token
     * @param  string $idField
     * @return UniasResourceOwner
     */
    public function requestResourceOwner(string $method, string $url, AccessToken $token, string $idField = 'id'): UniasResourceOwner
    {
        $response = $this->getParsedResponse($this->getAuthenticatedRequest($method, $url, $token));
        if (!is_array($response)) {
            $response = [$idField => $response];
        }

        return new UniasResourceOwner($response, $idField);
    }

    /**
     * Creates and returns api base url base on client configuration.
     *
     * @return string
     */
    protected function getApiUrl(): string
    {
        return $this->apiBaseUri;
    }

    /**
     * Get the default scopes used by this provider.
     *
     * This should not be a complete list of all scopes, but the minimum
     * required for the provider user interface!
     *
     * @return string[]
     */
    protected function getDefaultScopes(): array
    {
        return [];
    }

    /**
     * Check a provider response for errors.
     *
     * @throws IdentityProviderException
     * @param  ResponseInterface $response
     * @param  string $data Parsed response data
     * @return void
     */
    protected function checkResponse(ResponseInterface $response, $data)
    {
        $statusCode = $response->getStatusCode();
        if ($statusCode >= 400) {
            throw new IdentityProviderException(
                $data['message'] ?? $response->getReasonPhrase(),
                $statusCode,
                $response
            );
        }
    }

    /**
     * Generate a user object from a successful user details request.
     *
     * @param object $response
     * @param AccessToken $token
     * @return PaypalResourceOwner
     */
    protected function createResourceOwner(array $response, AccessToken $token): UniasResourceOwner
    {
        return new UniasResourceOwner($response, 'sub');
    }

    /**
     * Returns a prepared request for requesting an access token.
     *
     * @param array $params Query string parameters
     * @return RequestInterface
     */
    protected function getAccessTokenRequest(array $params): Request
    {
        unset($params['client_id'], $params['client_secret']);
        return parent::getAccessTokenRequest($params);
    }

    /**
     * Builds request options used for requesting an access token.
     *
     * @param  array $params
     * @return array
     */
    protected function getAccessTokenOptions(array $params): array
    {
        $params = parent::getAccessTokenOptions($params);
        $params['headers']['authorization'] = 'Basic '.base64_encode("{$this->clientId}:{$this->clientSecret}");
        return $params;
    }

    /**
     * Returns all options that can be configured.
     *
     * @return array
     */
    protected function getConfigurableOptions(): array
    {
        return array_merge(static::getRequiredOptions(), [
        ]);
    }

    /**
     * Returns all options that are required.
     *
     * @return array
     */
    protected static function getRequiredOptions(): array
    {
        return [
            'apiBaseUri',
            'authorizeUri',
            'tokenUri',
            'clientId',
            'clientSecret',
            'redirectUri',
        ];
    }

    /**
     * Prunes non-whitelisted configs and asserts that all required options have been passed.
     *
     * @param  array $options
     * @return void
     * @throws InvalidArgumentException
     */
    private function filterOptions(array $options): array
    {
        $options = array_intersect_key($options, array_flip($this->getConfigurableOptions()));

        // Trim trailing shashes
        foreach (['apiBaseUri', 'tokenUri', 'authorizeUri'] as $key) {
            $options[$key] = rtrim($options[$key] ?? '', '/');
        }

        // Assert that all the required values are set
        $missing = array_filter(static::getRequiredOptions(), function (string $key) use ($options): bool {
            return empty($options[$key]);
        });
        if (!empty($missing)) {
            throw new InvalidArgumentException('Required options were undefined or empty: ' . implode(', ', $missing));
        }

        return $options;
    }
}
