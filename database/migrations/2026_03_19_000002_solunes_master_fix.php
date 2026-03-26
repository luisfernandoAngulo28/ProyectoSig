<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SolunesMasterFix extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('languages')) {
            Schema::create('languages', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('order')->nullable()->default(0);
                $table->string('code');
                $table->string('name');
                $table->string('image')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('sites')) {
            Schema::create('sites', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('order')->nullable()->default(0);
                $table->string('name');
                $table->string('domain');
                $table->string('root');
                $table->text('google_verification')->nullable();
                $table->text('analytics')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('site_translation')) {
            Schema::create('site_translation', function(Blueprint $table) {
                $table->increments('id');
                $table->integer('site_id')->unsigned();
                $table->string('locale')->index();
                $table->string('title');
                $table->text('description')->nullable();
                $table->string('keywords')->nullable();
                $table->unique(['site_id','locale']);
                $table->foreign('site_id')->references('id')->on('sites')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('pages')) {
            Schema::create('pages', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('site_id')->unsigned()->default(1);
                $table->integer('order')->nullable()->default(0);
                $table->enum('type', ['normal', 'customized'])->default('normal');
                $table->string('customized_name')->nullable();
                $table->timestamps();
                $table->softDeletes();
                $table->foreign('site_id')->references('id')->on('sites')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('page_translation')) {
            Schema::create('page_translation', function(Blueprint $table) {
                $table->increments('id');
                $table->integer('page_id')->unsigned();
                $table->string('locale')->index();
                $table->string('name');
                $table->string('slug');
                $table->string('meta_title')->nullable();
                $table->text('meta_description')->nullable();
                $table->unique(['page_id','locale']);
                $table->foreign('page_id')->references('id')->on('pages')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('menus')) {
            Schema::create('menus', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('site_id')->unsigned()->default(1);
                $table->integer('parent_id')->nullable();
                $table->integer('level')->nullable()->default(1);
                $table->integer('order')->nullable()->default(0);
                $table->boolean('active')->nullable()->default(1);
                $table->enum('menu_type', ['site', 'customer', 'admin'])->default('site');
                $table->enum('type', ['normal', 'external', 'blank'])->default('normal');
                $table->string('permission')->nullable();
                $table->integer('page_id')->nullable();
                $table->string('icon')->nullable();
                $table->timestamps();
                $table->softDeletes();
                $table->foreign('site_id')->references('id')->on('sites')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('menu_translation')) {
            Schema::create('menu_translation', function(Blueprint $table) {
                $table->increments('id');
                $table->integer('menu_id')->unsigned();
                $table->string('locale')->index();
                $table->string('name')->nullable();
                $table->string('slug')->nullable();
                $table->string('link')->nullable();
                $table->unique(['menu_id','locale']);
                $table->foreign('menu_id')->references('id')->on('menus')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('nodes')) {
            Schema::create('nodes', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->string('table_name')->nullable();
                $table->string('model')->nullable();
                $table->enum('location', ['package', 'app', 'business', 'project', 'sales', 'product', 'inventory', 'accounting', 'payments', 'store', 'notification', 'pagostt', 'customer', 'staff', 'reservation'])->default('app');
                $table->enum('type', ['normal', 'child', 'subchild', 'field'])->default('normal');
                $table->string('folder')->nullable();
                $table->integer('parent_id')->nullable();
                $table->string('permission')->nullable();
                $table->boolean('multilevel')->default(0);
                $table->boolean('dynamic')->default(0);
                $table->boolean('customized')->default(0);
                $table->boolean('translation')->default(0);
                $table->boolean('indicators')->default(0);
                $table->boolean('soft_delete')->default(0);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('node_translation')) {
            Schema::create('node_translation', function(Blueprint $table) {
                $table->increments('id');
                $table->integer('node_id')->unsigned();
                $table->string('locale')->index();
                $table->string('singular');
                $table->string('plural');
                $table->unique(['node_id','locale']);
                $table->foreign('node_id')->references('id')->on('nodes')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name')->unique();
                $table->string('display_name')->nullable();
                $table->string('description')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('role_user')) {
            Schema::create('role_user', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('user_id')->unsigned();
                $table->integer('role_id')->unsigned();
                $table->foreign('user_id')->references('id')->on('users')
                    ->onUpdate('cascade')->onDelete('cascade');
                $table->foreign('role_id')->references('id')->on('roles')
                    ->onUpdate('cascade')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('permissions')) {
            Schema::create('permissions', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name')->unique();
                $table->string('display_name')->nullable();
                $table->string('description')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('permission_role')) {
            Schema::create('permission_role', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('permission_id')->unsigned();
                $table->integer('role_id')->unsigned();
                $table->foreign('permission_id')->references('id')->on('permissions')
                    ->onUpdate('cascade')->onDelete('cascade');
                $table->foreign('role_id')->references('id')->on('roles')
                    ->onUpdate('cascade')->onDelete('cascade');
            });
        }

        // Add missing columns to users
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'site_id')) {
                $table->integer('site_id')->unsigned()->default(1)->after('id');
                // $table->foreign('site_id')->references('id')->on('sites')->onDelete('cascade');
            }
            if (!Schema::hasColumn('users', 'status')) {
                $table->enum('status', ['normal','ask_password','pending_confirmation','banned'])->default('normal')->after('password');
            }
            if (!Schema::hasColumn('users', 'notifications_email')) {
                $table->boolean('notifications_email')->default(0)->after('status');
            }
        });
        
        if (!Schema::hasTable('fields')) {
            Schema::create('fields', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('parent_id')->unsigned();
                $table->integer('order')->nullable()->default(0);
                $table->string('name');
                $table->string('trans_name');
                $table->enum('type', ['string','integer','decimal','text','select','password','image','file','barcode','map','color','radio','checkbox','date','array','score','hidden','child','subchild','field','custom','title','content'])->default('string');
                $table->enum('display_list', ['show', 'excel', 'none'])->default('show');
                $table->enum('display_item', ['show', 'admin', 'none'])->default('show');
                $table->boolean('relation')->default(0);
                $table->boolean('multiple')->default(0);
                $table->boolean('translation')->default(0);
                $table->boolean('required')->default(0);
                $table->boolean('new_row')->default(0);
                $table->boolean('preset')->default(0);
                $table->string('permission')->nullable();
                $table->string('child_table')->nullable();
                $table->string('relation_cond')->nullable();
                $table->string('value')->nullable();
                $table->timestamps();
                $table->softDeletes();
                $table->foreign('parent_id')->references('id')->on('nodes')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('filters')) {
            Schema::create('filters', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('order')->nullable()->default(0);
                $table->enum('category', ['admin','indicator','site','customer','custom'])->default('admin');
                $table->enum('display', ['all','user'])->default('all');
                $table->enum('type', ['field','parent_field','custom'])->default('field');
                $table->enum('subtype', ['select','date','string','field'])->default('select');
                $table->integer('node_id')->unsigned();
                $table->string('parameter')->nullable();
                $table->text('action_value')->nullable();
                $table->integer('user_id')->nullable();
                $table->integer('category_id')->nullable();
                $table->foreign('node_id')->references('id')->on('nodes')->onDelete('cascade');
            });
        }

    }

    public function down()
    {
        // No down for repair migration
    }
}
