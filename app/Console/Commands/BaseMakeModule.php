<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

abstract class BaseMakeModule extends Command
{

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $modelName = Str::studly($this->argument('name'));
        $tableName = Str::snake(Str::plural($modelName));
        $path = $this->getModulePath($modelName);

        // Модель с миграцией
        $this->call('make:model', [
            'name' => $modelName,
            '-m' => true,
        ]);

        $this->call('make:controller', [
            'name' => "{$path}Controller",
            '--resource' => true,
            '--model' => $modelName
        ]);

        // Resource
        $this->call('make:resource', [
            'name' => "{$modelName}Resource",
        ]);

        // Request
        $this->call('make:request', [
            'name' => "{$path}Request"
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
            ['Контроллер', "app/Http/Controllers/{$this->getControllerPath()}/{$modelName}Controller.php"],
            ['Request', "app/Http/Requests/{$this->getRequestPath()}/{$modelName}Request.php"],
            ['Resource', "app/Http/Resource/{$modelName}Resource.php"],
            ['Seeder', "database/seeders/{$modelName}Seeder.php"],
        ]);
    }

    /**
     * Получить путь для модуля
     */
    abstract protected function getModulePath(string $modelName): string;

    /**
     * Получить путь для контроллеров
     */
    abstract protected function getControllerPath(): string;

    /**
     * Получить путь для запросов
     */
    abstract protected function getRequestPath(): string;
}
