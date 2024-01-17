<?php

namespace App\Services;

use App\Services\Interfaces\GenerateServiceInterface;

use App\Repositories\Interfaces\GenerateRepositoryInterface as GenerateRepository;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Artisan;

use Illuminate\Support\Facades\File;


/**
 * Class GenerateService
 * @package App\Services
 */
class GenerateService implements GenerateServiceInterface
{
    protected $GenerateRepository;


    public function __construct(
        GenerateRepository $GenerateRepository,

    ){
        $this->generateRepository = $GenerateRepository;

    }

    public function paginate($request)
    {
        $condition['keyword']= addslashes($request->input('keyword'));
        $condition['publish']= $request->integer('publish');

        $perPage = $request->integer('perpage');
        $generates = $this->generateRepository->pagination
        (
            $this->paginateSelect(),
            $condition,
            $perPage, 
            ['path'=>'Generate/index'],
        );
        // dd($generates);
        return $generates;
    }

    public function create($request){
        DB::beginTransaction();
        try{
            $database = $this->makeDatabase($request);
            $controller = $this->makeController($request);
            $model = $this->makeModel($request);
            $repository = $this->makeRepository($request);
            $service = $this->makeService($request);
            $provider = $this->makeProvider($request);
            $makeRequest = $this->makeRequest($request);
            $view = $this->makeView($request);
            if($request->input('module_type') == 1){
                $rule = $this->makeRule($request);
            }
            $route = $this->makeRoute($request);


            // $this->makeLang();

            // $payload =$request->except('_token','send');
            // $payload['user_id'] = Auth::id();
            // // dd($payload);
            // $generate = $this->generateRepository->create($payload);
            DB::commit();
            return true;
        }catch(\Exception $e ){
            DB::rollBack();
            // Log::error($e->getMessage());
            echo $e->getMessage();die();
            return false;
        }
    }

    private function makeDatabase($request){
        DB::beginTransaction();
        try{
            $payload =$request->only('schema', 'name','module_type');
            $tableName = $this->convertModuleNameToTableName($payload['name']).'s';
            $migrationFileName = date('Y_m_d_His').'_'.$tableName.'_table'.'.php';
            $migrationPath = database_path('migrations/'.$migrationFileName);
            $migrationTemplate = $this->createMigrationFile($payload);
            FILE::put($migrationPath, $migrationTemplate);

            if($payload['module_type'] !== 3){
                $foreignKey = $this->convertModuleNameToTableName($payload['name']).'_id';
                $pivotTableName = $this->convertModuleNameToTableName($payload['name']).'_langauge';
                $pivotSchema = $this->pivotSchema($tableName, $foreignKey, $pivotTableName);
                $migrationPivotTemplate = $this->createMigrationFile([
                    'schema' => $pivotSchema,
                    'name' => $pivotTableName,
                ]);
                // dd($migrationPivotTemplate);die();
                $migrationPivotFileName = date('Y_m_d_His', time() + 10).'_'.$pivotTableName.'_table'.'.php';
                // echo $migrationPivotFileName;die();
                $migrationPivotPath = database_path('migrations/'.$migrationPivotFileName);
                // dd($migrationPivotPath);

                FILE::put($migrationPivotPath, $migrationPivotTemplate);

            }
            ARTISAN::call('migrate');
            return true;
        }catch(\Exception $e ){
            DB::rollBack();
            echo $e->getMessage();die();
            return false;
        }

        
    }

    private function pivotSchema($tableName = '', $foreignKey = '', $pivot = ''){
        $pivotSchema = <<<SCHEMA
            Schema::create('{$pivot}', function (Blueprint \$table) {
                \$table->unsignedBigInteger('{$foreignKey}');
                \$table->unsignedBigInteger('language_id');
                \$table->foreign('{$foreignKey}')->references('id')->on('{$tableName}')->onDelete('cascade');
                \$table->foreign('language_id')->references('id')->on('languages')->onDelete('cascade');
                \$table->string('name');
                \$table->text('description');
                \$table->longText('content');
                \$table->string('meta_title');
                \$table->string('meta_keyword');
                \$table->text('meta_description');
            });
        SCHEMA;
        return $pivotSchema;die();
    }


    private function createMigrationFile($payload){
        $migrationTemplate = <<<MIGRATION
        <?php

        use Illuminate\Database\Migrations\Migration;
        use Illuminate\Database\Schema\Blueprint;
        use Illuminate\Support\Facades\Schema;
        
        return new class extends Migration
        {
            /**
             * Run the migrations.
             */
            public function up()
            {
                {$payload['schema']}
            }
        
            /**
             * Reverse the migrations.
             */
            public function down()
            {
                Schema::dropIfExists('{$this->convertModuleNameToTableName($payload['name'])}');
            }
        };
        MIGRATION;
        return $migrationTemplate;
    }

    private function convertModuleNameToTableName($name){
        $temp = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $name));
        return $temp;

    }

    private function makeController($request){
        $payload = $request->only('name', 'module_type');

        switch($payload['module_type']){
            case 1:
                $this->createTemlateCatalogueController($payload['name'], 'TemplateCatalogueController');
                break;
            case 2:
                $this->createTemlateCatalogueController($payload['name'], 'TemplateController');
                break;
            default: 
                echo 5;die();
        }
    }

    private function createTemlateCatalogueController($name, $controllerFile){

        try{
        $controllerName = $name.'Controller.php';
        $templateControllerParth = base_path('app/Templates/'.$controllerFile.'.php');
        $controllerContent = file_get_contents($templateControllerParth);

        $replace = [
            'ModuleTemplate' => $name,
            'moduleTemplate' => lcfirst($name),
            'foreignKey' => $this->convertModuleNameToTableName($name).'_id',
            'tableName' => $this->convertModuleNameToTableName($name).'s',
            'moduleView' => str_replace('_','.',$this->convertModuleNameToTableName($name))
        ];



        foreach($replace as $key => $val){
            $controllerContent = str_replace('{'.$key.'}', $replace[$key], $controllerContent);

        }

        // dd($controllerContent);

        $controllerPath = base_path('app/Http/Controllers/Backend/'.$controllerName);

        FILE::put($controllerPath, $controllerContent);


        

        return true;
        }catch(\Exception $e ){
            echo $e->getMessage();die();
            return false;
        }
    }

    public function makeModel($request){
        try{
            if($request->input('module_type') == 1){
                $this->createModelTemplate($request);
            }else{
                echo 1;die();
            }
            return true;
            }catch(\Exception $e ){
                echo $e->getMessage();die();
                return false;
            }
    }

    private function createModelTemplate($request){
        $modelName = $request->input('name').'.php';

        $templateModelParth = base_path('app/Templates/TemplateCatalogueModel.php');
        
        $modelContent = file_get_contents($templateModelParth);
        $module = $this->convertModuleNameToTableName($request->input('name'));
        $extractModule = explode('_',$module,);
        $replace = [
            'ModuleTemplate' => $request->input('name'),
            'foreignKey' => $module.'_id',
            'tableName' => $module.'s',
            'relation' => $extractModule[0],
            'pivotModel' => $request->input('name').'_'.'Language',
            'relationPivot' => $module.'_'.$extractModule[0],
            'pivotTable' => $module.'_language',
            'module' => $module,
            'relationModel' => Ucfirst($extractModule[0]),
        ];
        // dd($replace);
        foreach($replace as $key => $val){
            $modelContent = str_replace('{'.$key.'}', $replace[$key], $modelContent);
        }

        $modelPath = base_path('app/Models/'.$modelName);
        FILE::put($modelPath, $modelContent);
        
    }
//makeRepository
    public function makeRepository($request){
        try{
            $name = $request->input('name');
            $module = $this->convertModuleNameToTableName($name);
            $moduleExtract = explode('_',$module); 
            $resository = $this->initializeServiceLayer(
                'Repository', 'Repositories', $request
            );
            $replace = [
                'Module' => $name,
            ];

            $resositoryInterfaceContent = $resository['interface']['layerInterfaceContent'] ;

            $resositoryInterfaceContent = str_replace('{Module}', $replace['Module'], $resositoryInterfaceContent);

            $replaceRepository = [
                'Module' => $name,
                'tableName' => $module.'s',
                'pivotTableName' => $module.'_'.$moduleExtract[0],
                'foreingKey' => $module.'_id',
            ];
            $resositoryContent = $resository['service']['layerContent'];

            foreach($replaceRepository as $key => $val){
                $resositoryContent = str_replace('{'.$key.'}', $replaceRepository[$key], $resositoryContent);
            } 

            FILE::put($resository['interface']['layerInterfacePath'], $resositoryInterfaceContent);
            FILE::put($resository['service']['layerPathPut'], $resositoryContent);

            return true;
        }catch(\Exception $e ){
            DB::rollBack();
            echo $e->getMessage();die();
            return false;
        }
    }

//end makeRepository


//makeService

    public function makeService($request){
        try{
            $name = $request->input('name');
            $module = $this->convertModuleNameToTableName($name);
            $moduleExtract = explode('_',$module); 
            $service = $this->initializeServiceLayer(
                'Service', 'Services', $request
            );
            // dd($service);
            $replace = [
                'Module' => $name,
            ];

            $serviceInterfaceContent = $service['interface']['layerInterfaceContent'] ;

            $serviceInterfaceContent = str_replace('{Module}', $replace['Module'], $serviceInterfaceContent);

            $replaceService = [
                'Module' => $name,
                'module' => lcfirst($name),
                'tableName' => $module.'s',
                'foreingKey' => $module.'_id',
            ];
            // dd($replaceService);
            $serviceContent = $service['service']['layerContent'];

            foreach($replaceService as $key => $val){
                $serviceContent = str_replace('{'.$key.'}', $replaceService[$key], $serviceContent);
            } 

            FILE::put($service['interface']['layerInterfacePath'], $serviceInterfaceContent);
            FILE::put($service['service']['layerPathPut'], $serviceContent);

            return true;
        }catch(\Exception $e ){
            DB::rollBack();
            echo $e->getMessage();die();
            return false;
        }
    }

//end makeService

//chung Service Repository
    public function initializeServiceLayer($layer = '', $folder = '',$request){
        $name = $request->input('name');
        
        $option = [
            $layer.'Name' => $name.$layer,
            $layer.'InterfaceName' => $name.$layer.'Interface',
        ];
        $layerInterfaceRead = base_path('app/Templates/Template'.$layer.'Interface.php');
        $layerInterfaceContent = file_get_contents($layerInterfaceRead);


        $layerInterfacePath = base_path('app/'.$folder.'/Interfaces/'.$option[$layer.'InterfaceName'].'.php');

        
    
        $layerPathRead = base_path('app/Templates/Template'.$layer.'.php');
        $layerContent = file_get_contents($layerPathRead);
        
        $layerPathPut = base_path('app/'.$folder.'/'.$option[$layer.'Name'].'.php');
               
        return [
            'interface' => [
                'layerInterfaceContent' => $layerInterfaceContent,
                'layerInterfacePath' => $layerInterfacePath,
            ],
            
            'service'=> [
                'layerContent' => $layerContent,
                'layerPathPut' => $layerPathPut,
            ]
            
        ];
    }
//end chung Service Repository

    public function makeProvider($request){
        try{
            $name = $request->input('name');
            $provider = [
                'providerPath' => base_path('app/Providers/AppServiceProvider.php'),
                'repositoryProviderPath' => base_path('app/Providers/RepositoryServiceProvider.php'),
            ];

            foreach($provider as $key => $val){
                $content = file_get_contents($val);
                $insertLine = ($key == 'providerPath') ? "'App\\Services\\Interfaces\\{$name}ServiceInterface' => 'App\\Services\\{$name}Service'," : "'App\\Repository\\Interfaces\\{$name}RepositoryInterface' => 'App\\Repository\\{$name}Repository',";
                $position = strpos($content, '];');

                if($position !== false){
                    $newContent = substr_replace($content,"        ".$insertLine."\n".'', $position,0);
                }

                FILE::put($val, $newContent);
                
            }
           
            return true;
        }catch(\Exception $e ){
            DB::rollBack();
            echo $e->getMessage();die();
            return false;
        }

    }

    public function makeRequest($request){
        $name = $request->input('name');
        $requestArray =['Store'.$name.'Request', 'Update'.$name.'Request', 'Delete'.$name.'Request'];
        // dd($requestArray);
        $requestTemplate = ['RequestTemplateStore','RequestTemplateUpdate','RequestTemplateDelete'];
        if($request->input('module_type') != 1){
            unset($requestArray[2]);
            unset($requestTemplate[2]);
        }
        foreach($requestTemplate as $key => $val){
            $requestPath = base_path('app/Templates/'.$val.'.php');
            $requestContent = file_get_contents($requestPath);
            $requestContent = str_replace('{Module}', $name, $requestContent);
            // dd($requestContent);
            $requestPut = base_path('app/Http/Requests/'.$requestArray[$key].'.php');
            // echo $requestPut;die();
            FILE::put($requestPut, $requestContent);
        }
        
    }

    private function makeView($request){
        try{
            $name = $request->input('name');
            $module = $this->convertModuleNameToTableName($name); 
            $extractModule = explode('_', $module);
            $basePath =  resource_path("views/backend/{$extractModule[0]}");

            $folderPath = (count($extractModule) == 2) ? "$basePath/{$extractModule[1]}" : "$basePath/{$extractModule[0]}";
            $componentPath = "$folderPath/component";

            $this->createDirectory($folderPath);
            $this->createDirectory($componentPath);
            

            $sourcePath = base_path('app/Templates/views/'.((count($extractModule) == 2) ? 'catalogue' : 'post').'/');
            $viewPath = (count($extractModule) == 2) ? "{$extractModule[0]}.{$extractModule[1]}" : $extractModule[0];
            $replacement = [
                'view' => $viewPath,
                'module' => lcfirst($name),
                'Module' => $name,
            ];
            $fileArray = ['store.blade.php','index.blade.php','delete.blade.php'];
            $componentFile = ['aside.blade.php', 'filter.blade.php','table.blade.php'];
            $this->CopyAndReplaceContent($sourcePath, $folderPath, $fileArray, $replacement);
            $this->CopyAndReplaceContent("{$sourcePath}component/", $componentPath, $componentFile, $replacement);
            
            return true;
        }catch(\Exception $e ){
            DB::rollBack();
            // Log::error($e->getMessage());
            echo $e->getMessage();die();
            return false;
        } 
    }

    private function createDirectory($path){
        if(!FILE::exists($path)){
            File::makeDirectory($path, 0755, true);
        }
    }

    private function CopyAndReplaceContent(string $sourcePath ,string $destinationPath, array $fileArray, array $replacement){
        foreach($fileArray as $key => $val){
            $sourceFile = $sourcePath.$val;
            $destination = "{$destinationPath}/{$val}";
            $content = file_get_contents($sourceFile);
            foreach($replacement as $keyReplace => $replace){
                $content = str_replace('{'.$keyReplace.'}', $replace, $content);
            }
            if(!FILE::exists($destination)){
                FILE::put($destination, $content);
            }
        }
    }

    private function makeRule($request){
        $name = $request->input('name');
        $destination = base_path('app/Rules/Check'.$name.'ChildrenRule.php');
        $ruleTemplate = base_path('app/Templates/RuleTemplate.php');
        $content = file_get_contents($ruleTemplate);
        $content = str_replace('{Module}', $name, $content);
        if(!FILE::exists($destination)){
            FILE::put($destination, $content);
        }
    }

    private function makeRoute($request){
        $name = $request->input('name');
        $module = $this->convertModuleNameToTableName($name);
        $moduleExtract = explode('_', $module);
        // dd($moduleExtract);
        $routesPath = base_path('routes/web.php');
        $content = file_get_contents($routesPath);
        $routeUrl = (count($moduleExtract) == 2) ? "{$moduleExtract[0]}/$moduleExtract[1]" : $moduleExtract[0];
        $routeName = (count($moduleExtract) == 2) ? "{$moduleExtract[0]}.$moduleExtract[1]" : $moduleExtract[0];

       
        
        $routeGroup = <<<ROUTE
Route::group(['prefix' => '$routeUrl'], function () {
    Route::get('index', [{$name}Controller::class, 'index'])->name('{$routeName}.index');
    Route::get('create', [{$name}Controller::class, 'create'])->name('{$routeName}.create');
    Route::post('store', [{$name}Controller::class, 'store'])->name('{$routeName}.store');
    Route::get('{id}/edit', [{$name}Controller::class, 'edit'])->where(['id' => '[0-9]+'])->name('{$routeName}.edit');
    Route::post('{id}/update', [{$name}Controller::class, 'update'])->where(['id' => '[0-9]+'])->name('{$routeName}.update');
    Route::get('{id}/delete', [{$name}Controller::class, 'delete'])->where(['id' => '[0-9]+'])->name('{$routeName}.delete');
    Route::delete('{id}/destroy', [{$name}Controller::class, 'destroy'])->where(['id' => '[0-9]+'])->name('{$routeName}.destroy');
});
//@@new-module@@

ROUTE;

// dd($routeGroup);
        $useController = <<<ROUTE
use App\Http\Controllers\Backend\\{$name}Controller;
//@@useController@@
ROUTE;


        $content = str_replace('//@@new-module@@', $routeGroup, $content);
        $content = str_replace('//@@useController@@', $useController, $content);
        FILE::put($routesPath, $content);
    }

    public function update($id, $request){
        DB::beginTransaction();
        try{

            $payload =$request->except('_token','send');
            $generate = $this->generateRepository->update($id, $payload);
            
            DB::commit();
            return true;
        }catch(\Exception $e ){
            DB::rollBack();
            echo $e->getMessage();die();
            return false;
        }
    }

    public function destroy($id){
       
        DB::beginTransaction();
        try{ 
            $generate = $this->generateRepository->delete($id);
            DB::commit();
            return true;
        }catch(\Exception $e ){
            DB::rollBack();
            echo $e->getMessage();die();
            return false;
        }
    }

    public function updateStatus($post = []){
        DB::beginTransaction();
        try{
            $payload[$post['field']] = (($post['value'] == 1)?2:1);
            $generate = $this->generateRepository->update($post['modelId'], $payload);
            // $this->changeUserStatus($post, $payload[$post['field']]);

            DB::commit();
            return true;
        }catch(\Exception $e ){
            DB::rollBack();
            // Log::error($e->getMessage());
            echo $e->getMessage();die();
            return false;
        }
    }

    public function updateStatusAll($post){
        DB::beginTransaction();
        try{
            $payload[$post['field']] = $post['value'];
            $flag = $this->generateRepository->updateByWhereIn('id', $post['id'], $payload);
            // $this->changeUserStatus($post, $post['value']);

            DB::commit();
            return true;
        }catch(\Exception $e ){
            DB::rollBack();
            // Log::error($e->getMessage());
            echo $e->getMessage();die();
            return false;
        }
    }

    private function paginateSelect(){
        return [
            'id',
            'name',
            'schema',
        ];
    }

}
