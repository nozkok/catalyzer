<?php
namespace App\Catalyzer;

use Illuminate\Support\Facades\Cache;
use App\Catalyzer\ColumnHelper\ColumnHelper;

class SuperCacheHelper{

	private static $CACHETIME = 10*60;

	public static function rememberModel($dataModel)
	{
		self::clearCache($dataModel);
		$modelName = $dataModel->getModelName();
		
		if(Cache::has($modelName.'_COLUMNS'))	
		{
			$dataModel->setColumns(Cache::get($modelName.'_COLUMNS'));
			$dataModel->setForeigns(Cache::get($modelName.'_FOREIGNS'));
			$dataModel->setDomestics(Cache::get($modelName.'_DOMESTICS'));
			$dataModel->setRules(Cache::get($modelName.'_RULES'));

		}
		else
		{

			$columnHelper = ColumnHelper::detectColumnHelper();
			$columnHelper->setupColumns($dataModel);
	        $columnHelper->getFKs($dataModel);
            Cache::put($modelName.'_COLUMNS', $dataModel->getColumns(), self::$CACHETIME);
        	Cache::put($modelName.'_FOREIGNS', $dataModel->getForeigns(), self::$CACHETIME);
        	Cache::put($modelName.'_DOMESTICS', $dataModel->getDomestics(), self::$CACHETIME);
        	Cache::put($modelName.'_RULES', $dataModel->getRules(), self::$CACHETIME);
		}

	}
    public static function clearCache($dataModel)
    {
		$modelName = $dataModel->getModelName();

        Cache::forget($modelName.'_COLUMNS');
        Cache::forget($modelName.'_FOREIGNS');
        Cache::forget($modelName.'_DOMESTICS');
        Cache::forget($modelName.'_RULES');
    }
	
}

