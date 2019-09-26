<?php
/**
 * Created by PhpStorm.
 * User: cicle creative
 * Date: 24/09/2019
 * Time: 13:16
 */

namespace App\Api\Modules\Master\Models;


use O2System\Framework\Models\Sql\Model;
use O2System\Spl\DataStructures\SplArrayObject;

class Themes extends Model
{
    public $table = 'themes';

    /**
     * Is Theme Choosen?
     *
     * Get from latest companies themes and compare.
     *
     * @return boolean
     */
    public function isChoosen($idTheme, $idCompany)
    {
        // Get current theme.
        $theme = $this->models->companyThemes->findWhere([
            'id_company' => $idCompany
        ]);

        if ($theme == false)
            return false;

        if ($theme->last()->id_tm_theme != $idTheme)
            return false;

        return true;
    }

    /**
     * Get current theme
     *
     * Get from latest companies themes.
     *
     * @return mixed
     */
    public function getCurrent()
    {
        $result = new SplArrayObject();
        // Get current theme.
        if($results = $this->all()){
            foreach ($results as $field => $value){
                $result->offsetSet($value->name, $value->content);
            }
        }
        return $result;
    }

}