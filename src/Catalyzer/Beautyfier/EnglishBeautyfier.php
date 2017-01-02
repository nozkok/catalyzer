<?php 
namespace Catalyzer\Beautyfier;

use Catalyzer\Beautyfier\Beautyfier;

class EnglishBeautyfier extends Beautyfier
{
    protected $RESERVED_ACTION_NAMES = ['edit', 'destroy', 'create', 'update', 'show', 'restore'];
    protected $MODELS_FOLDER = '\\App\\Http\\Models\\'; 
    protected $DATA_MODELS_FOLDER = '\\App\\Http\\DataModels\\'; 
    protected $SPECIAL_PLURALS = ['children' => 'child', ];

    public function singularize( $plural, $shouldUc = false)
    {
        $plural = lcfirst($plural);
        if( isset( $this->SPECIAL_PLURALS[$plural] ) )
        {
            $singular = $this->SPECIAL_PLURALS[$plural];
        }
        else
        {
            if(substr($plural,-1) == 's' )
            {
                $singular = substr($plural,0,-1);
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