<?php

declare(strict_types=1);

namespace Kreait\Firebase\Messaging;

use JsonSerializable;
use Kreait\Firebase\Exception\Messaging\InvalidArgument;

use function array_filter;
use function array_key_exists;

/**
 * @see https://developer.apple.com/documentation/usernotifications/setting_up_a_remote_notification_server/generating_a_remote_notification
 * @see https://developer.apple.com/documentation/usernotifications/setting_up_a_remote_notification_server/sending_notification_requests_to_apns
 * @see https://firebase.google.com/docs/reference/fcm/rest/v1/projects.messages#apnsconfig
 *
 * @phpstan-type ApnsConfigShape array{
 *     headers?: array<non-empty-string, non-empty-string>,
 *     payload?: array<non-empty-string, mixed>,
 *     fcm_options?: array{
 *         analytics_label?: string,
 *         image?: string
 *     },
 *     live_activity_token?: non-empty-string
 * }
 */
final class ApnsConfig implements JsonSerializable
{
    private const PRIORITY_CONSERVE_POWER = '5';

    private const PRIORITY_IMMEDIATE = '10';

    /**
     * @param array<non-empty-string, non-empty-string> $headers
     * @param array<non-empty-string, mixed> $payload
     * @param array<non-empty-string, string> $fcmOptions
     * @param non-empty-string|null $liveActivityToken
     */
    private function __construct(
        private readonly array $headers,
        private readonly array $payload,
        private readonly array $fcmOptions,
        private readonly ?string $liveActivityToken,
    ) {
    }

    public static function new(): self
    {
        return new self([], [], [], null);
    }

    /**
     * @param ApnsConfigShape $data
     */
    public static function fromArray(array $data): self
    {
        $headers = $data['headers'] ?? [];
        $payload = $data['payload'] ?? [];
        $fcmOptions = $data['fcm_options'] ?? [];
        $liveActivityToken = $data['live_activity_token'] ?? null;

        return new self($headers, $payload, $fcmOptions, $liveActivityToken);
    }

    /**
     * @param non-empty-string $name
     * @param non-empty-string $value
     */
    public function withHeader(string $name, string $value): self
    {
        $headers = $this->headers;
        $headers[$name] = $value;

        return new self(
            headers: $headers,
            payload: $this->payload,
            fcmOptions: $this->fcmOptions,
            liveActivityToken: $this->liveActivityToken,
        );
    }

    public function hasHeader(string $name): bool
    {
        return array_key_exists($name, $this->headers);
    }

    /**
     * @param non-empty-string $key
     */
    public function withApsField(string $key, mixed $value): self
    {
        $payload = $this->payload;
        $payload['aps'] ??= [];
        $payload['aps'][$key] = $value;

        return new self(
            headers: $this->headers,
            payload: $payload,
            fcmOptions: $this->fcmOptions,
            liveActivityToken: $this->liveActivityToken,
        );
    }

    /**
     * @param non-empty-string $name
     */
    public function withDataField(string $name, mixed $value): self
    {
        if ($name === 'aps') {
            throw new InvalidArgument('"aps" is a reserved field name');
        }

        $payload = $this->payload;
        $payload[$name] = $value;

        return new self(
            headers: $this->headers,
            payload: $payload,
            fcmOptions: $this->fcmOptions,
            liveActivityToken: $this->liveActivityToken,
        );
    }

    public function withDefaultSound(): self
    {
        return $this->withSound('default');
    }

    /**
     * The name of a sound file in your app’s main bundle or in the Library/Sounds folder of your app’s
     * container directory. Specify the string "default" to play the system sound.
     */
    public function withSound(string $sound): self
    {
        return $this->withApsField('sound', $sound);
    }

    /**
     * The number to display in a badge on your app’s icon. Specify 0 to remove the current badge, if any.
     */
    public function withBadge(int $number): self
    {
        return $this->withApsField('badge', $number);
    }

    public function withImmediatePriority(): self
    {
        return $this->withPriority(self::PRIORITY_IMMEDIATE);
    }

    public function withPowerConservingPriority(): self
    {
        return $this->withPriority(self::PRIORITY_CONSERVE_POWER);
    }

    /**
     * @param non-empty-string $priority
     */
    public function withPriority(string $priority): self
    {
        return $this->withHeader('apns-priority', $priority);
    }

    /**
     * @see https://firebase.google.com/docs/cloud-messaging/ios/live-activity
     * @param non-empty-string $liveActivityToken
    */
    public function withLiveActivityToken(string $liveActivityToken): self
    {
        return new self(
            headers: $this->headers,
            payload: $this->payload,
            fcmOptions: $this->fcmOptions,
            liveActivityToken: $liveActivityToken,
        );
    }

    /**
     * A subtitle of the notification, supported by iOS 9+, silently ignored for others.
     */
    public function withSubtitle(string $subtitle): self
    {
        return $this->withApsField('subtitle', $subtitle);
    }

    public function isAlert(): bool
    {
        return
            isset($this->payload['aps']['alert'])
            || isset($this->payload['aps']['badge'])
            || isset($this->payload['aps']['sound']);
    }

    /**
     * @return array<non-empty-string, mixed>
     */
    public function data(): array
    {
        $payload = $this->payload;

        unset($payload['aps']);

        return $payload;
    }

    /**
     * @return array<non-empty-string, mixed>
     */
    public function toArray(): array
    {
        $filter = static fn($value): bool => $value !== null && $value !== [];

        return array_filter([
            'headers' => array_filter($this->headers, $filter),
            'payload' => array_filter($this->payload, $filter),
            'fcm_options' => array_filter($this->fcmOptions, $filter),
            'live_activity_token' => $this->liveActivityToken,
        ], $filter);
    }

    /**
     * @return ApnsConfigShape
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
