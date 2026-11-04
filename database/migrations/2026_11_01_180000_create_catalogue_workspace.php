<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('catalogue_revisions', function (Blueprint $t) {
            $t->id();
            $t->uuid('creation_key')->nullable()->unique();
            $t->unsignedBigInteger('editor_id')->index();
            $t->unsignedBigInteger('product_id')->nullable()->index();
            $t->string('status', 30)->default('draft')->index();
            $t->unsignedInteger('version')->default(0);
            $t->json('payload');
            $t->string('base_fingerprint', 64)->nullable();
            $t->json('base_snapshot')->nullable();
            $t->unsignedBigInteger('reviewer_id')->nullable();
            $t->text('review_notes')->nullable();
            $t->timestamp('submitted_at')->nullable();
            $t->timestamp('reviewed_at')->nullable();
            $t->timestamps();
        });
        Schema::create('catalogue_revision_events', function (Blueprint $t) {
            $t->id(); $t->unsignedBigInteger('revision_id')->index();
            $t->unsignedBigInteger('actor_id')->nullable();
            $t->string('actor_name'); $t->string('action', 40);
            $t->text('notes')->nullable(); $t->json('payload')->nullable(); $t->timestamps();
        });
        Schema::create('catalogue_uploads', function (Blueprint $t) {
            $t->id(); $t->unsignedBigInteger('editor_id')->index();
            $t->unsignedBigInteger('revision_id')->nullable()->index();
            $t->string('path'); $t->string('kind', 20); $t->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('catalogue_uploads');
        Schema::dropIfExists('catalogue_revision_events');
        Schema::dropIfExists('catalogue_revisions');
    }
};
