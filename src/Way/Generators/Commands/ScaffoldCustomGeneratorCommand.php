<?php namespace Way\Generators\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Config;

class ScaffoldCustomGeneratorCommand extends ScaffoldGeneratorCommand {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'generate:scaffold_custom';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scaffold a new resource (with custom)';

//    public function fire()
//    {
//        $resource = $this->argument('resource');
//
//        $this->callModel($resource);
//        $this->callView($resource);
//        $this->callController($resource);
//        $this->callMigration($resource);
//        $this->callSeeder($resource);
//        $this->callMigrate();
//
//        // All done!
//        $this->info(sprintf(
//            "All done! Don't forget to add '%s` to %s." . PHP_EOL,
//            "Route::resource('{$this->getTableName($resource)}', '{$this->getControllerName($resource)}');",
//            "app/routes.php"
//        ));
//
//    }

    /**
     * Call model generator if user confirms
     *
     * @param $resource
     */
    protected function callView($resource)
    {        
        $collection = $this->getTableName($resource);
        $modelName = $this->getModelName($resource);

        if ($this->confirm("Do you want me to create views for this $modelName resource? [yes|no]"))
        {

            foreach(['index', 'show', 'create', 'edit', 'form'] as $viewName)
            {
                $viewName = "{$collection}.{$viewName}";

                $this->call('generate:view_custom', [
                    'viewName' => $viewName,
                    '--templatePath' => Config::get("generators::config.scaffold_view_template_path")
                ]);
            }
        }
    }

}
