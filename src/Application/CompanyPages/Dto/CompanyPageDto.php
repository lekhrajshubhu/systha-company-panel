<?php

namespace Systha\CompanyPanel\Application\CompanyPages\Dto;

final class CompanyPageDto
{
    public function __construct(
        public readonly string $title,
        public readonly ?string $subtitle,
        public readonly string $slug,
        public readonly ?string $content,
        public readonly string $status,
    ) {
    }

    public static function fromArray(array $payload): self
    {
        return new self(
            title: trim((string) ($payload['title'] ?? '')),
            subtitle: self::nullableTrim($payload['subtitle'] ?? null),
            slug: trim((string) ($payload['slug'] ?? '')),
            content: self::nullableTrim($payload['content'] ?? null),
            status: trim((string) ($payload['status'] ?? 'draft')),
        );
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'slug' => $this->slug,
            'content' => $this->content,
            'status' => $this->status,
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
