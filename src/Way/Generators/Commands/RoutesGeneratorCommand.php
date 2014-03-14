<?php namespace Way\Generators\Commands;

use Illuminate\Support\Facades\File;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class RoutesGeneratorCommand extends GeneratorCommand {
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'generate:routes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a custom routes';
    /**
     * Create directory tree for views,
     * and fire generator
     */
    public function fire()
    {
        $directoryPath = dirname($this->getFileGenerationPath());
        if ( ! File::exists($directoryPath))
        {
            File::makeDirectory($directoryPath, 0777, true);
        }
        $this->generator->make_or_update(
            $this->getTemplatePath(),
            $this->getTemplateData(),
            $this->getFileGenerationPath()
        );
    }

    /**
     * The path where the file will be created
     *
     * @return mixed
     */
    protected function getFileGenerationPath()
    {
        $path = $this->getPathByOptionOrConfig('path', 'routes_target_path');
        return sprintf('%s/routes.php', $path);
    }

    /**
     * Fetch the template data
     *
     * @return array
     */
    protected function getTemplateData()
    {
        $tag = '<?php'.PHP_EOL.PHP_EOL;
        $file = $this->getFileGenerationPath();
        $contents = '';
        list($table, $controler) = explode('.', $this->argument('routesName'));
        $string = "Route::resource('{$table}', '{$controler}');";
        if (File::isFile($file)){
            $contents = str_replace($tag, '', File::get($file));
        }
        
        $routes = $tag.$contents.PHP_EOL.$string;
        
        return compact('routes');
    }
    

    /**
     * Get path to the template for the generator
     *
     * @return mixed
     */
    protected function getTemplatePath()
    {
        return $this->getPathByOptionOrConfig('templatePath', 'view_routes_path');
    }
    
    protected function getArguments()
    {
        return [
            ['routesName', InputArgument::REQUIRED, 'The name of the desired routes']
        ];
    }

}
