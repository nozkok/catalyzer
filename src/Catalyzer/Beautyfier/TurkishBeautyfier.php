<?php 
namespace Catalyzer\Beautyfier;

use Catalyzer\Beautyfier\Beautyfier;

class TurkishBeautyfier extends Beautyfier
{
    protected $RESERVED_ACTION_NAMES = ['duzenle', 'sil', 'ekle', 'guncelle', 'goster', 'geri_getir'];
    protected $MODELS_FOLDER = '\\App\\Http\\Modeller\\'; 
    protected $DATA_MODELS_FOLDER = '\\App\\Http\\DataModeller\\'; 

    
  	public static function singularize( $plural, $shouldUc = false)
    {
    	$plural = lcfirst($plural);
    	if( isset( self::$SPECIAL_PLURALS[$plural] ) )
    	{
    		$singular = self::$SPECIAL_PLURALS[$plural];
    	}
    	else
    	{
	    	if(substr($plural,-3) == 'lar' || substr($plural,-3) == 'ler' )
	    	{
	    		$singular = substr($plural,0,-3);

	    	}

    	}
    	if (!isset($singular)) 
    		throw new \Exception('Url Path Could Not Be Singularized');

    	if($shouldUc)
    		$singular = ucfirst($singular);
    	return $singular;
    }



}
?>