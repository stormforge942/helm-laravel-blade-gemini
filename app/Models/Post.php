<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Corcel\Model\Post as Corcel;
use Corcel\Model\Taxonomy;
use Corcel\Model\TermRelationship;
class Post extends Corcel
{
    use HasFactory;
    // protected $postType = 'post';
    protected $fillable = [
        'post_title', 'post_content', 'post_excerpt', 'post_status', 'post_type',
        'to_ping', 'pinged', 'post_content_filtered', 'post_name', 'post_author', 'guid'
    ];

    public function categories()
    {
        return $this->taxonomies()->where('taxonomy', 'category');
    }

     // Relationship to term_relationships
     public function termRelationships()
     {
         return $this->hasMany(TermRelationship::class, 'object_id', 'ID');
     }
}
