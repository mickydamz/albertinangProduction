<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class CatalogueRevision extends Model {
    protected $guarded = [];
    protected $casts = ['version'=>'integer','editor_id'=>'integer','product_id'=>'integer','reviewer_id'=>'integer','payload'=>'array','base_snapshot'=>'array','submitted_at'=>'datetime','reviewed_at'=>'datetime'];
    public function editor() { return $this->belongsTo(User::class, 'editor_id'); }
    public function reviewer() { return $this->belongsTo(User::class, 'reviewer_id'); }
    public function product() { return $this->belongsTo(Product::class); }
    public function events() { return $this->hasMany(CatalogueRevisionEvent::class, 'revision_id')->orderByDesc('id'); }
    public function uploads() { return $this->hasMany(CatalogueUpload::class, 'revision_id'); }
}
