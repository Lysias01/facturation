<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mouvements_stock', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produit_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['entree', 'sortie']);
            $table->integer('quantite');
            $table->string('raison')->nullable(); // vente, achat, ajustement
            $table->string('reference')->nullable(); // ex: RECU-0001
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mouvements_stock');
    }
};
