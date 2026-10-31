<?php

namespace App\Models;

use App\Notifications\GenericPortalNotification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SystemNotification extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'message',
        'type',
        'link',
        'is_read',
    ];

    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function send(User|int $user, string $title, string $message, string $type = 'system', ?string $link = null): self
    {
        $targetUser = $user instanceof User ? $user : User::find($user);
        $userId = $targetUser ? $targetUser->id : (int) $user;

        $notification = static::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'link' => $link,
            'is_read' => false,
        ]);

        if ($targetUser) {
            $targetUser->notify(new GenericPortalNotification($title, $message, $type, $link));
        }

        return $notification;
    }
}
