<?php

namespace JildertMiedema\LaravelPlupload;

class Builder
{
    private $settings;
    private $prefix;
    private $scriptUrl = '/vendor/jildertmiedema/laravel-plupload/js/plupload.full.min.js';

    private static $hasOne = false;

    public function createJsInit()
    {
        return sprintf('var %s_uploader = new plupload.Uploader(%s);', $this->prefix, json_encode($this->getSettings()));
    }

    public function createJsRun()
    {
        $script = sprintf('%s_uploader.init();', $this->prefix);
        $script .= sprintf('document.getElementById(\'%s-start-upload\').onclick = function() {%s_uploader.start();};', $this->prefix, $this->prefix);

        return $script;
    }

    public function addScript()
    {
        return sprintf('<script type="text/javascript" src="%s"></script>', $this->scriptUrl);
    }

    public function createJsFrame()
    {
        $prefix = $this->prefix;
        $settings = json_encode($this->getSettings());
        $script = "$(\"#{$prefix}-container\").plupload({$settings});";
        return $script;
    }

    public function addScripts()
    {
        $scripts = '';
        if (config('plupload.bootstrap') !== NULL) {
            $scripts .= '<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" type="text/css" />';
        } 
        if (config('plupload.jquery-ui') !== NULL) { 
            $scripts .= '<link href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" rel="stylesheet" type="text/css" />';
        }
        $scripts .= '<link href="/vendor/jildertmiedema/laravel-plupload/js/jquery.ui.plupload/css/jquery.ui.plupload.css" media="all" rel="stylesheet" type="text/css" />';
        if (config('plupload.jquery') !== NULL) { 
            $scripts .= '<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>';
        }
        if (config('plupload.bootstrap') !== NULL) {
            $scripts .= '<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" type="text/javascript"></script>';
        } 
        if (config('plupload.jquery-ui') !== NULL) { 
            $scripts .= '<script src="//code.jquery.com/ui/1.12.1/jquery-ui.min.js" type="text/javascript"></script>';
        }
        $scripts .= '<script src="/vendor/jildertmiedema/laravel-plupload/js/plupload.full.min.js"></script>';
        $scripts .= '<script src="/vendor/jildertmiedema/laravel-plupload/js/jquery.ui.plupload/jquery.ui.plupload.min.js"></script>';        
        if (config('plupload.lang') !== NULL) { 
            $scripts .= sprintf('<script src="/vendor/jildertmiedema/laravel-plupload/js/i18n/%s.js"></script>', config('plupload.lang'));
        }
        return $scripts;
    }

    public function getContainer()
    {
        $prefix = $this->prefix;
        $html = "<div id=\"{$prefix}-container\">";
        $html .= "<button type=\"button\" id=\"{$prefix}-browse-button\" class=\"btn btn-primary\">Browse...</button>";
        $html .= "<button type=\"button\" id=\"{$prefix}-start-upload\" class=\"btn btn-success\">Upload</button>";
        $html .= '</div>';

        return $html;
    }

    public function getFrame()
    {
        $prefix = $this->prefix;
        $html = "<div id=\"{$prefix}-container\">";
        $html .= "<p>Your browser doesn't have Flash, Silverlight or HTML5 support.</p>";
        $html .= '</div>';

        return $html;
    }

    public function createHtml()
    {
        $html = '';
        $html .= $this->getContainer();
        if (!Builder::$hasOne) {
            $html .= $this->addScript();
        }
        Builder::$hasOne = true;
        $html .= '<script type="text/javascript">';
        $html .= $this->createJsInit();
        $html .= $this->createJsRun();
        $html .= '</script>';

        return $html;
    }

    public function createFrame()
    {
        $html = '';
        $html .= $this->getFrame();
        $html .= $this->addScripts();
        $html .= '<script type="text/javascript">';
        $html .= $this->createJsFrame();
        $html .= '</script>';

        return $html;
    }
    
    public function setScriptUrl($value)
    {
        $this->scriptUrl = $value;
    }

    public function withScriptUrl($value)
    {
        $this->setScriptUrl($value);

        return $this;
    }

    public function getDefaultSettings()
    {
        $settings = [];
        $settings['runtimes'] = 'html5';
        $settings['browse_button'] = $this->prefix.'-browse-button';
        $settings['container'] = $this->prefix.'-container';
        $settings['url'] = '/upload';
        $settings['flash_swf_url'] = '/vendor/jildertmiedema/laravel-plupload/js/Moxie.swf';
        $settings['silverlight_xap_url'] = '/vendor/jildertmiedema/laravel-plupload/js/Moxie.xap';
        $settings['headers'] = [
            'Accept' => 'application/json',
            'X-CSRF-TOKEN' => csrf_token(),
        ];

        return $settings;
    }

    public function setDefaults()
    {
        $this->updateSettings($this->getDefaultSettings());
    }

    public function getSettings()
    {
        $settings = $this->getDefaultSettings();

        $this->settings = $this->settings ?: [];

        foreach ($this->settings as $name => $value) {
            $settings[$name] = $value;
        }

        return $settings;
    }

    public function updateSettings(array $settings)
    {
        foreach ($settings as $name => $value) {
            $this->settings[$name] = $value;
        }
    }

    public function setPrefix($value)
    {
        $this->prefix = $value;
    }

    public function withPrefix($value)
    {
        $this->setPrefix($value);

        return $this;
    }

    public static function make(array $settings = null)
    {
        $instance = static::init($settings);

        return $instance->createHtml();
    }

    public static function init(array $settings = null)
    {
        $instance = new static();

        if ($settings) {
            $instance->updateSettings($settings);
        }

        return $instance;
    }
}
