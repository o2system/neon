<?php
/**
 * Created by PhpStorm.
 * User: steevenz
 * Date: 04/07/18
 * Time: 02.10
 */

namespace App\Models\Master\Companies;

use O2System\Framework\Models\Sql\Model;
use O2System\Framework\Models\Sql\Traits\FinderTrait;
use O2System\Framework\Models\Sql\Traits\ModifierTrait;

class Themes extends Model
{
    use FinderTrait;
    use ModifierTrait;
    
    public $table = 'companies_themes';

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
    public function getCurrent($idCompany)
    {
        // Get current theme.
        $theme = $this->models->companyThemes->findWhere([
            'id_company' => $idCompany
        ]);

        if ($theme == false)
            return false;
        
        return $theme->last();
    }
}