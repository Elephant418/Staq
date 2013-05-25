<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\Router\Stack\Controller;

class PublicFile extends PublicFile\__Parent
{


    /*************************************************************************
    ATTRIBUTES
     *************************************************************************/
    public static $setting = [
        'route.view.uri' => '/**'
    ];


    /*************************************************************************
    ACTION METHODS
     *************************************************************************/
    public function actionView()
    {
        $path = $this->getPublicPath();
        $realPath = \Staq::App()->getFilePath('/public' . $path);
        if (
            empty($realPath) ||
            is_dir($realPath) ||
            \UString::isEndWith($realPath, '.php') /* TODO: Add to settings */
        ) {
            return NULL;
        }
        $this->renderStaticFile($realPath);
        return TRUE;
    }


    /*************************************************************************
    PRIVATE METHODS
     *************************************************************************/
    protected function getPublicPath()
    {
        return \Staq::App()->getCurrentUri();
    }

    protected function renderStaticFile($filePath)
    {
        $resource = fopen($filePath, 'rb');
        if (!headers_sent()) {
            $contentType = $this->getContentType($filePath);
            $cacheTime = $this->getPublicFileCacheTime();
            $control = ($cacheTime > 0) ? 'public' : 'private';
            header('Pragma: public');
            header('Content-Type: ' . $contentType . '; charset: UTF-8');
            header('Content-Length: ' . filesize($filePath));
            header('Cache-Control: max-age=' . ($cacheTime - time()) . ', pre-check=' . ($cacheTime - time()) . ', ' . $control, true);
            header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', $cacheTime), true);
        }
        fpassthru($resource);
    }

    protected function getContentType($filePath)
    {
        $extension = \UString::substrAfterLast($filePath, '.');
        if (in_array($extension, ['html', 'css'])) {
            $contentType = 'text/' . $extension;
        } else if ($extension === 'js') {
            $contentType = 'text/javascript';
        } else if ($extension === 'ico') {
            $contentType = 'image/png';
        } else {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $contentType = finfo_file($finfo, $filePath);
            finfo_close($finfo);
        }
        return $contentType;
    }

    protected function getPublicFileCacheTime()
    {
        $setting = (new \Stack\Setting)->parse('Application');
        $publicFileCache = $setting['cache.public_file_cache'];
        if (!$publicFileCache = strtotime($publicFileCache)) {
            $publicFileCache = strtotime('+1 hour');
        }
        return $publicFileCache;
    }

}

?>