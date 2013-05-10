<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\App\BackOffice\Stack;

class View extends View\__Parent
{


    /* PRIVATE METHODS
     *************************************************************************/
    protected function addVariables()
    {
        parent::addVariables();
        $groupModelTypes = (new \Stack\Setting)
            ->parse('BackOffice')
            ->get('model');
        foreach ( $groupModelTypes as $group => $modelTypes ) {
            if ( !is_array( $modelTypes )) {
                if (empty($modelTypes)){
                    unset($groupModelTypes[$group]);
                } else {
                    \UArray::doRenameKey($groupModelTypes,$group,$modelTypes);
                    $groupModelTypes[$modelTypes]=[$modelTypes];
                }
            }
        }
        $this['groupModelTypes'] = $groupModelTypes;
    }
}
