<?php

namespace JildertMiedema\LaravelPlupload;

class Builder
{
    private $settings;
    private $prefix;

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

    public function addScripts()
    {
        $scripts = <<<EOC
        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.2/themes/smoothness/jquery-ui.min.css" rel="stylesheet" type="text/css" />
        <link href="/vendor/jildertmiedema/laravel-plupload/js/jquery.ui.plupload/css/jquery.ui.plupload.css" media="all" rel="stylesheet" type="text/css" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" type="text/javascript"></script>
        <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.2/jquery-ui.min.js" type="text/javascript"></script>
        <script src="/vendor/jildertmiedema/laravel-plupload/js/plupload.full.min.js"></script>
        <script src="/vendor/jildertmiedema/laravel-plupload/js/jquery.ui.plupload/jquery.ui.plupload.min.js"></script>
EOC;
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

    public function createHtml()
    {
        $html = '';
        $html .= $this->getContainer();
        $html .= $this->addScripts();
        $html .= '<script type="text/javascript">';
        $html .= $this->createJsInit();
        $html .= $this->createJsRun();
        $html .= '</script>';

        return $html;
    }

    public function getDefaultSettings()
    {
        $settings = [];
        $settings['runtimes'] = 'html5';
        $settings['browse_button'] = $this->prefix.'-browse-button';
        $settings['container'] = $this->prefix.'-container';
        $settings['url'] = '/upload';
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
