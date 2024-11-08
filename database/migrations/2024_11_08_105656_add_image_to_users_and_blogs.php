<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddImageToUsersAndBlogs extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('image')->nullable()->after('id'); // Replace 'existing_column_name' with the column after which you want to add 'image'
        });

        Schema::table('blogs', function (Blueprint $table) {
            $table->string('image')->nullable()->after('id'); // Replace 'existing_column_name' with the column after which you want to add 'image'
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('image');
        });

        Schema::table('blogs', function (Blueprint $table) {
            $table->dropColumn('image');
        });
    }
}
