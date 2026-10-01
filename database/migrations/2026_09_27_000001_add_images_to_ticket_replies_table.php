<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * TicketReply's model + TicketController::reply() write an `images` array, but
 * the original ticket_replies table never had the column — so posting a reply
 * threw "Unknown column 'images'". This adds the missing JSON column.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ticket_replies', function (Blueprint $table) {
            if (!Schema::hasColumn('ticket_replies', 'images')) {
                $table->json('images')->nullable()->after('message');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ticket_replies', function (Blueprint $table) {
            if (Schema::hasColumn('ticket_replies', 'images')) {
                $table->dropColumn('images');
            }
        });
    }
};
