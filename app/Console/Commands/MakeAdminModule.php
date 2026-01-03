<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakeAdminModule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:admin-module {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Создать полный модуль для админки';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $modelName = Str::studly($this->argument('name'));
        $tableName = Str::snake(Str::plural($modelName));
        $adminPath = "Admin/{$modelName}";

        // Модель с миграцией
        $this->call('make:model', [
            'name' => $modelName,
            '-m' => true,
        ]);

        $this->call('make:controller', [
            'name' => "{$adminPath}Controller",
            '--resource' => true,
            '--model' => $modelName
        ]);

        // Request
        $this->call('make:request', [
            'name' => "{$adminPath}Request"
        ]);

        // Seeder
        $this->call('make:seeder', [
            'name' => "{$modelName}Seeder"
        ]);

        $this->newLine();
        $this->info("✅ Модуль '{$modelName}' успешно создан!");
        $this->table(['Компонент', 'Путь'], [
            ['Модель', "app/Models/{$modelName}.php"],
            ['Миграция', "database/migrations/*_create_{$tableName}_table.php"],
            ['Контроллер', "app/Http/Controllers/Admin/{$modelName}Controller.php"],
            ['Request', "app/Http/Requests/Admin/{$modelName}Request.php"],
            ['Seeder', "database/seeders/{$modelName}Seeder.php"],
        ]);
    }
}
