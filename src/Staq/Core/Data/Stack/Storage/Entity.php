<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\Data\Stack\Storage;

class Entity
{


    /* PRIVATE CORE METHODS
     *************************************************************************/
    protected function getModel()
    {
        $modelClass = 'Stack\\Model\\' . \Staq\Util::getStackSubQuery($this);
        return new $modelClass;
    }

    protected function resultAsModelList($data)
    {
        $list = [];
        $model = $this->getModel();
        foreach ($data as $item) {
            $list[] = $model->byData($item);
        }
        return $list;
    }

    protected function resultAsModel($data)
    {
        return $this->getModel()->byData($data);
    }
}
