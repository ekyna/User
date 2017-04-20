<?php

declare(strict_types=1);

namespace Ekyna\Component\User\Service\OAuth;

use function is_null;
use function sprintf;

/**
 * Class OAuthConfigurator
 * @package Ekyna\Component\User\Service\OAuth
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
final class OAuthConfigurator
{
    public const OWNERS = [
        'google' => [
            'scopes'  => [
                'https://www.googleapis.com/auth/userinfo.email',
                'https://www.googleapis.com/auth/userinfo.profile',
            ],
            'options' => [
                'display' => 'page',
                'prompt'  => 'select_account',
            ],
        ],
    ];

    public static function route(string $name, string $owner, bool $check): string
    {
        return sprintf('%s_security_oauth_%s_%s', $name, $owner, $check ? 'check' : 'connect');
    }

    public static function controller(string $name, string $owner, ?bool $check): string
    {
        $controller = sprintf('%s.controller.oauth.%s', $name, $owner);

        if (!is_null($check)) {
            $controller .= '::' . ($check ? 'check' : 'connect');
        }

        return $controller;
    }

    public static function authenticator(string $name, string $owner): string
    {
        return sprintf('%s.security.authenticator.oauth.%s', $name, $owner);
    }
}
