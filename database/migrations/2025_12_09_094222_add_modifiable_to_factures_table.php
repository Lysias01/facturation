<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('factures', function (Blueprint $table) {
            $table->boolean('modifiable')->default(true)->after('type_document');
        });
    }

    public function down()
    {
        Schema::table('factures', function (Blueprint $table) {
            $table->dropColumn('modifiable');
        });
    }
};
