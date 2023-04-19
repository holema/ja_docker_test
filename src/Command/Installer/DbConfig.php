<?php

declare(strict_types=1);

namespace App\Command\Installer;

class DbConfig implements ConvertToEnvironmentInterface
{
    use ConvertToEnvironmentTrait;

    private static string $DNS_FORMAT = '%s://%s:%s@%s:%d/%s?serverVersion=%s';

    private const ENVIRONMENT = [
        'DATABASE_URL' => 'dsn',
    ];

    private function __construct(
        private string  $engine,
        private string $serverVersion,
        private string $host,
        private int    $port,
        private string $database,
        private string $username = 'jitsiadmin',
        private string $password = 'jitsiadmin',
    )
    {
    }

    public static function createFromParameters(
        string $engine,
        string $serverVersion,
        string $host,
        int    $port,
        string $database,
        string $username,
        string $password,
    ): self
    {
        return new self(
            engine: $engine,
            serverVersion: $serverVersion,
            host: $host,
            port: $port,
            database: $database,
            username: $username,
            password: $password,
        );
    }

    public static function createFromDefault(): self
    {
        return new self(
            engine: 'mysql',
            serverVersion: '5.7',
            host: 'localhost',
            port: 3306,
            database: 'jitsi-admin',
            username: 'jitsiadmin',
            password: 'jitsiadmin',
        );
    }

    public function getEnvironmentMap(): array
    {
        return self::ENVIRONMENT;
    }

    public function dsn(): string
    {
        return sprintf(
            self::$DNS_FORMAT,
            $this->engine,
            $this->username,
            $this->password,
            $this->host,
            $this->port,
            $this->database,
            $this->serverVersion,
        );
    }
}