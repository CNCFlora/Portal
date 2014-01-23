<?php

class View implements \Rest\View {

    protected $file ;
    protected $props ;

    public static $defaults ;

    function __construct($file,$props=null) {
        $this->template = file_get_contents( __DIR__."/resources/templates/".$file );

        if(is_array($props)) $this->props = $props ;
        else if(is_object($props)) $this->props = (array) $props;
        else $this->props = array();

        if(!isset($_SERVER["HTTP_X_PJAX"]) && !isset($_SERVER['X_PJAX'])) {
            $iterator = new \DirectoryIterator(__DIR__."/resources/templates");
            foreach ($iterator as $file) {
                if($file->isFile() && preg_match("/\.html$/",$file->getFilename())) {
                    $this->partials[substr( $file->getFilename(),0,-5)] = file_get_contents($file->getPath()."/".$file->getFilename());
                }
            }
        }
    }

    function execute(\Rest\Server $rest) {
        $props = array_merge($rest->getParameters(),$this->props);

        $m = new \Mustache_Engine(array('partials'=>$this->partials));
        $content = $m->render($this->template,$props);
        $content = str_replace("href='/","href='/portal/",$content);
        $content = str_replace('href="/','href="/portal/',$content);
        $content = str_replace("action='/","action='/portal/",$content);
        $content = str_replace('action="/','action="/portal/',$content);
        $content = str_replace("src='/","src='/portal/",$content);
        $content = str_replace('src="/','src="/portal/',$content);
        $content = str_replace('src="sites','src="/sites',$content);
        $content = str_replace("src='sites","src='/sites",$content);
        $content = str_replace('href="sites','href="/sites',$content);
        $content = str_replace("href='sites","href='/sites",$content);
        $rest->getResponse()->setResponse($content);
        return $rest ;
    }

}
