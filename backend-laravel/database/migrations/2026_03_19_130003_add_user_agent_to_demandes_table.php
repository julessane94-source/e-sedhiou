<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Ajout de la clé étrangère user_id sur demandes (citoyen propriétaire de la demande)
        // et agent_id (agent assigné)
        Schema::table('demandes', function (Blueprint $table): void {
            $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
            $table->foreignId('agent_id')->nullable()->after('user_id')->constrained('users')->nullOnDelete();
            $table->text('agent_notes')->nullable()->after('details');
            $table->timestamp('assigned_at')->nullable()->after('agent_notes');
            $table->timestamp('processed_at')->nullable()->after('assigned_at');
            // status: pending | assigned | processing | completed | rejected
            $table->index(['user_id']);
            $table->index(['agent_id']);
        });
    }

    public function down(): void
    {
        Schema::table('demandes', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('user_id');
            $table->dropConstrainedForeignId('agent_id');
            $table->dropColumn(['agent_notes', 'assigned_at', 'processed_at']);
        });
    }
};
