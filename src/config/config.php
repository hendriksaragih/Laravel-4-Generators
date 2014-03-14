<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Where the templates for the generators are stored...
    |--------------------------------------------------------------------------
    |
    */
    'model_template_path' => 'vendor/hendriksaragih/generators/src/Way/Generators/templates/model.txt',

    'scaffold_model_template_path' => 'vendor/hendriksaragih/generators/src/Way/Generators/templates/scaffolding/model.txt',

    'controller_template_path' => 'vendor/hendriksaragih/generators/src/Way/Generators/templates/controller.txt',

    'scaffold_controller_template_path' => 'vendor/hendriksaragih/generators/src/Way/Generators/templates/scaffolding/controller.txt',

    'scaffold_view_template_path' => 'vendor/hendriksaragih/generators/src/Way/Generators/templates/scaffolding/view',

    'migration_template_path' => 'vendor/hendriksaragih/generators/src/Way/Generators/templates/migration.txt',

    'seed_template_path' => 'vendor/hendriksaragih/generators/src/Way/Generators/templates/seed.txt',

    'view_template_path' => 'vendor/hendriksaragih/generators/src/Way/Generators/templates/view.txt',
    
    'view_routes_path' => 'vendor/hendriksaragih/generators/src/Way/Generators/templates/routes.txt',


    /*
    |--------------------------------------------------------------------------
    | Where the generated files will be saved...
    |--------------------------------------------------------------------------
    |
    */
    'model_target_path'   => app_path('models'),

    'controller_target_path'   => app_path('controllers'),

    'migration_target_path'   => app_path('database/migrations'),

    'seed_target_path'   => app_path('database/seeds'),

    'view_target_path'   => app_path('views'),
    
    'routes_target_path'   => app_path('routes')

];