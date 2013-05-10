<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\Data\Stack\Attribute\Image;

class Cloudinary extends Cloudinary\__Parent
{


    /* ATTRIBUTES
     *************************************************************************/
    protected $model;
    protected $transformation = [];
    protected $original;


    /* CONSTRUCTOR
     *************************************************************************/
    public function initBySetting($model, $setting)
    {
        parent::initBySetting($model, $setting);
        $this->model = $model;
        if (is_array($setting)) {
            if (isset($setting['transformation'])) {
                $this->transformation = $setting['transformation'];
            }
            if (!is_array($this->transformation)) {
                throw new \Stack\Exception\MissingSetting('"transformation" must be an array for Image\Cloudinary attribute.');
            }
            if (isset($setting['original'])) {
                $this->editable = FALSE;
                $this->original = $setting['original'];
            }
        }
    }


    /* PUBLIC USER METHODS
     *************************************************************************/
    public function get()
    {
        new \Stack\Util\Cloudinary();
        return \Cloudinary::cloudinary_url( rawurlencode($this->getPublicId()), $this->transformation);
    }

    public function getPublicId()
    {
        $attribute = $this;
        if ( $this->original ) {
            $attribute = $this->model->getAttribute( $this->original );
        }
        return $attribute->getSeed();
    }
}