<?php

namespace App\Console\Commands;

class MakeApiModule extends BaseMakeModule
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:api-module {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Создать полный модуль для Api';

    protected function getModulePath(string $modelName): string
    {
        return "Api/{$modelName}";
    }

    protected function getControllerPath(): string
    {
        return 'Api';
    }

    protected function getRequestPath(): string
    {
        return 'Api';
    }
}
