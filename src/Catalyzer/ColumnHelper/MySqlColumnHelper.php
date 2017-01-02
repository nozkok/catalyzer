<?php
namespace App\Catalyzer\ColumnHelper;

use Carbon\Carbon;
use App\Catalyzer\Contracts\ColumnHelperContract;
use App\Catalyzer\Beautyfier\Beautyfier;

class MySqlColumnHelper extends ColumnHelper{

    protected static $ESSENTIAL_FIELD_VARS = [	'column_name'=>'name',
    											'column_default'=>'columnDefault', 
    											'is_nullable'=>'required',
    											'data_type'=>'fieldType',
    											'character_maximum_length'=>'maxLength'];
    

   	public static function setupColumns($dataModel)
    {
    	$selectFields = implode(",", array_keys(self::$ESSENTIAL_FIELD_VARS));
        $DBColumns = \DB::select( \DB::raw("SELECT {$selectFields} FROM information_schema.columns WHERE table_schema='".env('DB_DATABASE')."' AND table_name='".$dataModel->getTable()."'"));
        $formFields = $dataModel->getFormFields();
       	$tableFields = $dataModel->getTableFields();
        $beautyfier = Beautyfier::detectBeautyfier();
        $fieldVars = array_keys(self::$ESSENTIAL_FIELD_VARS);
        foreach($DBColumns as $DBColumn )
        {
            $column = new \stdClass();
            foreach($fieldVars as $fieldVar)
            {
            	if($fieldVar == 'column_name')
            	{
            		$column->showName = $beautyfier->beautify($DBColumn->column_name);
            		$column->name = $DBColumn->column_name;
            		if(isset(self::$SPECIAL_FIELD_TYPES[$DBColumn->column_name]))
	            	{
	            		$column->fieldType = self::$SPECIAL_FIELD_TYPES[$DBColumn->column_name];
	            	}
	            	else
            		{
                        if(isset(self::$FIELD_TYPES[$DBColumn->data_type]))
	                        $column->fieldType = self::$FIELD_TYPES[$DBColumn->data_type];
                        else
                            throw new \Exception ('data_type Name Could Not Found for: '.$DBColumn->data_type );

            		}
	            	if(in_array($DBColumn->column_name,self::$NON_EDITABLE_FIELDS))
            			$column->editable = false;
            		else
            			$column->editable = true;
            	}
                else if($fieldVar == 'character_maximum_length')
                {
                    if(!isset($DBColumn->character_maximum_length))
                    {
                        if(isset( self::$LENGHTS[$DBColumn->data_type] ))
                        {
                            $column->maxLength = self::$LENGHTS[$DBColumn->data_type] ;
                        }

 
                    }
                    else
                    {
                        $column->maxLength = $DBColumn->character_maximum_length;
                    }

                }
                else if ($fieldVar == 'is_nullable')
                {
                    $fieldVarName = self::$ESSENTIAL_FIELD_VARS[$fieldVar];
                    $column->$fieldVarName = $DBColumn->$fieldVar;                    
                }
            	else if($fieldVar == 'column_default')
            	{
                    $fieldVarName = self::$ESSENTIAL_FIELD_VARS[$fieldVar];
                    $column->$fieldVarName = $DBColumn->$fieldVar;  
            	}
            }

            $columns[$DBColumn->column_name] = $column; 

            if(sizeof($formFields== 1) && $formFields[0]=='*' )
            {

                $tmpFormFields[] = $column->name;
            }
            if(sizeof($tableFields== 1) && $tableFields[0]=='*')
            {
                $tmpTableFields[] = $column->name;

            }


    }

            if(sizeof($formFields== 1) && $formFields[0]=='*')
            {
                $dataModel->setFormFields($tmpFormFields);
                $formFields = $tmpFormFields;
            }
            if(sizeof($tableFields== 1) && $tableFields[0]=='*')
            {
                $dataModel->setTableFields($tmpTableFields);
                $tableFields = $tmpTableFields;

            }

            foreach($columns as $column)
            {
                if(in_array($column->name,$formFields))
                {
                    $rule = null;
                    $rule = ($column->required&& $column->editable)? "required":"";
                    $rule.=($column->required && $column->editable)?self::$OR.self::$VALIDATOR_TYPES[$column->fieldType] : self::$VALIDATOR_TYPES[$column->fieldType];
                    $rule.=(isset($column->maxLength) )?self::$OR."max:".$column->maxLength:'';
                    $rules[$column->name] = $rule;              
                }   
            }
          
        $dataModel->setRules($rules);
        $dataModel->setColumns($columns);


    }
    public static function getFKs($dataModel)
    {

        $fks = \DB::select( \DB::raw(
            "SELECT
                tc.constraint_name,
                tc.table_name,
                kcu.column_name,
                ccu.REFERENCED_TABLE_NAME AS foreign_table_name,
                kcu.REFERENCED_COLUMN_NAME AS foreign_column_name
            FROM
                information_schema.table_constraints AS tc 
                JOIN information_schema.key_column_usage AS kcu
                  ON tc.constraint_name = kcu.constraint_name
                JOIN information_schema.REFERENTIAL_CONSTRAINTS AS ccu
                  ON ccu.constraint_name = tc.constraint_name
            WHERE constraint_type = 'FOREIGN KEY' AND (tc.table_name='".$dataModel->getTable()."' OR ccu.REFERENCED_TABLE_NAME='".$dataModel->getTable()."' );"
            ));
        self::setupForeignsAndDomestics($dataModel,$fks);
    }
}


 ?>

