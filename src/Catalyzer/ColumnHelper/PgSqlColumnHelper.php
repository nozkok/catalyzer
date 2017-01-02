<?php
namespace Catalyzer\ColumnHelper;

use Carbon\Carbon;
use Catalyzer\Contracts\ColumnHelperContract;
use Catalyzer\Beautyfier\Beautyfier;

class PgSqlColumnHelper extends ColumnHelper{

    protected static $ESSENTIAL_FIELD_VARS = [	'column_name'=>'name',
    											'column_default'=>'columnDefault', 
    											'is_nullable'=>'required',
    											'udt_name'=>'fieldType',
    											'character_maximum_length'=>'maxLength'];
                                                
   	public static function setupColumns($dataModel)
    {
    	$selectFields = implode(",", array_keys(self::$ESSENTIAL_FIELD_VARS));
        $DBColumns = \DB::select( \DB::raw("SELECT {$selectFields},CASE WHEN is_nullable='NO' THEN true ELSE false END as is_nullable FROM information_schema.columns WHERE table_schema='public' AND table_name='".$dataModel->getTable()."'"));
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
                        if(isset(self::$FIELD_TYPES[$DBColumn->udt_name]))
	                        $column->fieldType = self::$FIELD_TYPES[$DBColumn->udt_name];
                        else
                            throw new \Exception ('UDT Name Could Not Found for: '.$DBColumn->udt_name );

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
                        if(isset( self::$LENGHTS[$DBColumn->udt_name] ))
                        {
                            $column->maxLength = self::$LENGHTS[$DBColumn->udt_name] ;
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
                ccu.table_name AS foreign_table_name,
                ccu.column_name AS foreign_column_name 
            FROM 
                information_schema.table_constraints AS tc 
                JOIN information_schema.key_column_usage AS kcu
                  ON tc.constraint_name = kcu.constraint_name
                JOIN information_schema.constraint_column_usage AS ccu
                  ON ccu.constraint_name = tc.constraint_name
            WHERE constraint_type = 'FOREIGN KEY' AND (tc.table_name='".$dataModel->getTable()."' OR ccu.table_name='".$dataModel->getTable()."' );"
            ));
        self::setupForeignsAndDomestics($dataModel,$fks);


    }
}


 ?>

