<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\File;

class MakeService extends Command
{
    protected $signature = 'make:service {name}';
    protected $description = 'Create a new service class';

    public function handle()
    {
        $name = $this->argument('name');

        // Normalize directory separators to `/` for paths
        $name = str_replace('\\', '/', $name);
        $path = app_path("Services/{$name}.php");

        // Get the directory and create it if it doesn't exist
        $directory = dirname($path);
        if (!File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        // Check if the service file already exists
        if (File::exists($path)) {
            $this->error("Service {$name} already exists!");
            return false;
        }

        // Extract the class name
        $className = class_basename($name);

        // Generate the namespace dynamically, removing extra `/` at the start of the path
        $namespacePath = trim(str_replace('/', '\\', dirname($name)), '\\');
        $namespace = 'App\\Services' . (!empty($namespacePath) ? '\\' . $namespacePath : '');


        $content = <<<PHP
        <?php

        namespace {$namespace};

        use App\Services\BaseService;

        class {$className} extends BaseService
        {
            // Service logic here
            public function __construct()
            {
                // \$this->model = new YourModel;
            }
        }

        PHP;

        File::put($path, $content);

        $this->info("Service {$name} created successfully.");
    }
}
