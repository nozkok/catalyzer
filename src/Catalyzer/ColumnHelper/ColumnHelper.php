<?php
namespace Catalyzer\ColumnHelper;

use Catalyzer\Contracts\ColumnHelperContract;
use Catalyzer\Beautyfier\Beautyfier;

abstract class ColumnHelper implements ColumnHelperContract{
	
    protected static $VALIDATOR_TYPES = ['text' => 'string', 'number'=> 'numeric', 'email' => 'email', 'password' => 'string', 'date' => 'date', 'datetime'=>'datetime', 'checkbox'=>'string','time'=>'time' ];
    protected static $OR = '|';
    protected static $SPECIAL_FIELD_TYPES = ['password'=>'password', 'email'=>'email'];
    protected static $NON_EDITABLE_FIELDS = ['id','created_by'];
    protected static $FIELD_TYPES = [
        'varchar' => 'text',
        'tinyint'=>'number','int' => 'number', 'bigint'=> 'number','smallint'=> 'number', 'mediumint'=>'number',
        'int2'=>'number','int4'=>'number','int8'=>'number',
        'blob'=>'text',
        'char'=>'text',
        'text'=>'text',
        'mediumtext'=>'text',
        'decimal'=>'number','double'=>'number','enum'=>'text',
        'bool' => 'checkbox',
        'timestamp' => 'date', 'date'=>'date','datetime'=>'datetime', 'time'=>'time'
        ];
    protected static $LENGHTS = ['text'=>'1000','json'=>'1000'];
    protected static $FOREIGN_FIELDS = ['constraint_name'=>'constraintName', 'table_name'=> 'tableName', 'column_name'=> 'columnName', 'foreign_table_name'=>'foreignTableName', 'foreign_column_name'=>'foreignColumnName'];

    public static function detectColumnHelper()
    {
    	$dbConnection = config('database.default');
    	if( isset($dbConnection) )
    	{
    		switch ($dbConnection){
    			case 'pgsql':
        			$columnHelper = new PgSqlColumnHelper();
        			break;
                case 'mysql':
                    $columnHelper = new MySqlColumnHelper();
                    break;

        		default:
        			$columnHelper = new MySqlColumnHelper();
        			break;
    		}
    		return $columnHelper;
    	}
    	else
    	{
    		throw new Exception('DB_CONNECTION is not set');
    	}
    }
    public static function setupForeignsAndDomestics($dataModel,$fks)
    {
        $beautyfier = Beautyfier::detectBeautyfier();

        foreach($fks as $fk)
        {   
            if($fk->table_name == $dataModel->getTable())
            {
                $foreign = new \stdClass();
                foreach(self::$FOREIGN_FIELDS as $key => $foreignField)
                {
                    $foreign->$foreignField = $fk->$key;
                }
                $foreign->functionName = $beautyfier->singularize($fk->foreign_table_name);
                $foreign->foreignModelName = $beautyfier->toForeignModelName($foreign->functionName);
                $foreign->dataModelName = $beautyfier->toDataModelName($fk->foreign_table_name);
                $foreigns[$fk->column_name] = $foreign;
            }
            else
            {
                $domestic = new \stdClass();

                foreach(self::$FOREIGN_FIELDS as $key => $foreignField)
                {
                    $domestic->$foreignField = $fk->$key;
                }
                $domestic->functionName = $beautyfier->singularize($fk->table_name);
                $domestic->foreignModelName = $beautyfier->toForeignModelName($domestic->functionName);
                $domestic->dataModelName = $beautyfier->toDataModelName($fk->table_name);
                $domestics[$domestic->functionName] = $domestic;
            }
        }

        if(isset($foreigns))
            $dataModel->setForeigns($foreigns);
        if(isset($domestics))
            $dataModel->setDomestics($domestics);
    }



}
?>