<?php

namespace Systha\CompanyPanel\Application\PaymentCredentials\Dto;

final class CompanyPaymentCredentialDto
{
    public function __construct(
        public string $name,
        public string $code,
        public string $mode,
        public string $publishableKey,
        public string $secretKey,
        public ?string $webhookSecret,
        public bool $isActive,
    ) {
    }

    public static function fromArray(array $payload): self
    {
        $credentials = is_array($payload['credentials'] ?? null) ? $payload['credentials'] : [];

        return new self(
            name: trim((string) ($payload['name'] ?? '')),
            code: trim((string) ($payload['code'] ?? '')),
            mode: trim((string) ($payload['mode'] ?? 'test')),
            publishableKey: trim((string) ($credentials['publishable_key'] ?? '')),
            secretKey: trim((string) ($credentials['secret_key'] ?? '')),
            webhookSecret: self::nullableTrim($credentials['webhook_secret'] ?? null),
            isActive: (bool) ($payload['is_active'] ?? true),
        );
    }

    public function credentialsArray(): array
    {
        return [
            'publishable_key' => $this->publishableKey,
            'secret_key' => $this->secretKey,
            'webhook_secret' => $this->webhookSecret,
        ];
    }

    private static function nullableTrim(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $trimmed = trim((string) $value);
        return $trimmed === '' ? null : $trimmed;
    }
}
