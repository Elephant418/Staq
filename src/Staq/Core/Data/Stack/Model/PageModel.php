<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\Data\Stack\Model;

class PageModel extends Page\__Parent
{
    public function initialize() {
        if (empty($this->title)) {
            $this->set('title', $this->name);
        }
    }
}
