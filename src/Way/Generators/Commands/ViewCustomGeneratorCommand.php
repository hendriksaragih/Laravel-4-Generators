<?php namespace Way\Generators\Commands;

use Illuminate\Support\Facades\File;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ViewCustomGeneratorCommand extends ViewGeneratorCommand {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'generate:view_custom';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a view custom';

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

        parent::fire();
    }

    /**
     * The path where the file will be created
     *
     * @return mixed
     */
    protected function getFileGenerationPath()
    {
        $path = $this->getPathByOptionOrConfig('path', 'view_target_path');
        $viewName = str_replace('.', '/', $this->argument('viewName'));

        return sprintf('%s/%s.blade.php', $path, $viewName);
    }

    /**
     * Fetch the template data
     *
     * @return array
     */
    protected function getTemplateData()
    {
        $arr_action = explode('.', $this->argument('viewName'));
        $name = ucwords($arr_action[0]);
        $collection = strtolower($name);
        $resource = str_singular($collection);
        $model = ucwords($resource);

        return compact('name', 'collection', 'resource', 'model');
    }

    /**
     * Get path to the template for the generator
     *
     * @return mixed
     */
    protected function getTemplatePath()
    {
        $arr_action = explode('.', $this->argument('viewName'));
        return $this->getPathByOptionOrConfig('templatePath', 'scaffold_view_template_path').'/'.end($arr_action).'.txt';
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['viewName', InputArgument::REQUIRED, 'The name of the desired view']
        ];
    }

}
