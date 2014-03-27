<?php namespace Way\Generators\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Illuminate\Support\Facades\DB;
use Config;

class ScaffoldGeneratorCommand extends ResourceGeneratorCommand {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'generate:scaffold';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scaffold a new resource (with boilerplate)';
    
    
    public function fire(){        
        $resource = $this->argument('resource');        
        if($this->option('confirms')=='all'){
            $this->callAll($resource);
        }else{
            $this->callModel($resource);
            $this->callView($resource);
            $this->callController($resource);
            $this->callMigration($resource);
            $this->callSeeder($resource);
            $this->callMigrate();
            $this->callAddRoutes($resource);               
        }        
        $this->migration_modules($resource);
        $this->call('migrate');
    }
    
    protected function callAll($resource) {
        $modelName = $this->getModelName($resource);
        $collection = $this->getTableName($resource);
        $controllerName = $this->getControllerName($resource);
        $migrationName = $this->getMigrationName($resource);
        $tableName = str_plural($modelName);
        $routesName = "{$collection}.{$controllerName}";
        $field = $this->option('fields');
        
        $this->call('generate:model', [
            'modelName' => $modelName,
            '--templatePath' => Config::get("generators::config.scaffold_model_template_path"),
            '--fields' => $field
        ]);
        
        foreach(['index', 'show', 'create', 'edit', '_form'] as $viewName)
        {
            $viewName = "{$collection}.{$viewName}";

            $this->call('generate:view', [
                'viewName' => $viewName,
                '--templatePath' => Config::get("generators::config.scaffold_view_template_path"),
                '--fields' => $field
            ]);
        }                

        $this->call('generate:controller', [
            'controllerName' => $controllerName,
            '--templatePath' => Config::get("generators::config.scaffold_controller_template_path")
        ]);
        
        $this->call('generate:migration', [
            'migrationName' => $migrationName,
            '--fields' => $field
        ]);
                
        $this->call('generate:seed', compact('tableName'));
        $this->call('migrate');
        $this->call('generate:routes', compact('routesName'));
        
    }
    
    
    
    protected function migration_modules($resource){
        $migrationName = $this->getMigrationName($resource);
        $name = $this->getTableName($resource);
        $this->call('generate:migration', [
            'migrationName' => $migrationName.date('Ymdhis'),
            '--fromScaffold' => $name
        ]);
    }
    
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

            foreach(['index', 'show', 'create', 'edit', '_form'] as $viewName)
            {
                $viewName = "{$collection}.{$viewName}";

                $this->call('generate:view', [
                    'viewName' => $viewName,
                    '--templatePath' => Config::get("generators::config.scaffold_view_template_path"),
                    '--fields' => $this->option('fields')
                ]);
            }
        }
    }
    
    protected function callModel($resource)
    {
        $modelName = $this->getModelName($resource);

        if ($this->confirm("Do you want me to create a $modelName model? [yes|no]"))
        {
            $this->call('generate:model', [
                'modelName' => $modelName,
                '--templatePath' => Config::get("generators::config.scaffold_model_template_path"),
                '--fields' => $this->option('fields')
            ]);
        }
    }

    /**
     * Call controller generator if user confirms
     *
     * @param $resource
     */
    protected function callController($resource)
    {
        $controllerName = $this->getControllerName($resource);

        if ($this->confirm("Do you want me to create a $controllerName controller? [yes|no]"))
        {
            $this->call('generate:controller', [
                'controllerName' => $controllerName,
                '--templatePath' => Config::get("generators::config.scaffold_controller_template_path")
            ]);
        }
    }
    
    protected function getOptions()
    {
        return [
            ['fields', null, InputOption::VALUE_OPTIONAL, 'Fields for the migration'],
            ['confirms', null, InputOption::VALUE_OPTIONAL, 'Confirm']
        ];
    }

}
