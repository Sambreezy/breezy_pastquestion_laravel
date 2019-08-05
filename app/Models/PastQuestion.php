<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ramsey\Uuid\Uuid;

class PastQuestion extends Model
{
    use Notifiable;
    use softDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'department', 
        'course_name', 
        'course_code', 
        'semester', 
        'year', 
        'tags', 
        'uploaded_by',
    ];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'vote_up', 
        'vote_down',
        'approved',
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
        'vote_up' => 'integer',
        'vote_down' => 'integer',

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

        // While creating this model make an id
        self::creating(function ($model) {
            $model->id = Uuid::uuid4()->toString();
        });

        // While deleting this model delete some of its related models
        self::deleted(function($model) {
            $model->image()->where('deleted_at', NULL)->delete();
            $model->document()->where('deleted_at', NULL)->delete();
            $model->comment()->where('deleted_at', NULL)->delete();
        });

        // While restoring this model restore some of its related models
        self::restored(function($model) {
            $model->image()->onlyTrashed()->restore();
            $model->document()->onlyTrashed()->restore();
            $model->comment()->onlyTrashed()->restore();
        });
    }

    /**
     * Establishes a belongs to one relationship with users table
     */
    public function users()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Establishes a one to many relationship with images table
     */
    public function image()
    {
        return $this->hasMany(Image::class);
    }

    /**
     * Establishes a one to many relationship with documents table
     */
    public function document()
    {
        return $this->hasMany(Document::class);
    }

    /**
     * Establishes a one to many relationship with comments table
     */
    public function comment()
    {
        return $this->hasMany(Comment::class);
    }
}
