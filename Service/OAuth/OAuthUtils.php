<?php

declare(strict_types=1);

namespace Ekyna\Component\User\Service\OAuth;

use HWI\Bundle\OAuthBundle\Security\OAuthUtils as BaseUtils;
use League\Uri\Uri;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class OAuthUtils
 * @package Ekyna\Component\User\Service\OAuth
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
final class OAuthUtils extends BaseUtils
{
    private ?string $scheme = 'http';
    private ?string $host   = 'localhost';
    private ?int    $port   = 8011;


    public function configure(?string $scheme, ?string $host, ?int $port): void
    {
        $this->scheme = $scheme;
        $this->host = $host;
        $this->port = $port;
    }

    /**
     * Overrides the login by changing scheme, host and port to
     * make Google happy with the dev environnement (localhost).
     *
     * @inheritDoc
     */
    public function getLoginUrl(Request $request, $name): string
    {
        $url = parent::getLoginUrl($request, $name);

        return $this->replaceSchemeAndHost($url);
    }

    private function replaceSchemeAndHost(string $url): string
    {
        $url = Uri::createFromString($url);

        if ($this->scheme) {
            $url = $url->withScheme($this->scheme);
        }

        if ($this->host) {
            $url = $url->withHost($this->host);
        }

        if ($this->port) {
            $url = $url->withPort($this->port);
        }

        return (string)$url;
    }
}
