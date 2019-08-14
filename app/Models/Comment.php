<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ramsey\Uuid\Uuid;

class Comment extends Model
{
    use Notifiable;
    use softDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'comment', 
        'reply', 
        'parent_comment_id', 
        'past_question_id', 
        'user_picture',
        'user_id',
    ];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'flags',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'deleted_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'flags' => 'integer',
    ];

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->id = Uuid::uuid4()->toString();
        });
    }

    /**
     * Establishes a belongs to one relationship with past questions table
     */
    public function pastQuestion()
    {
        return $this->belongs(PastQuestion::class);
    }

    /**
     * Establishes a belongs to one relationship with users table
     */
    public function user()
    {
        return $this->belongs(User::class);
    }
}
