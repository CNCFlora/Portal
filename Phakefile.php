<?php

$ini = parse_ini_file(__DIR__."/config.ini");
foreach($ini as $k=>$v) {
    define($k,$v);
}
unset($ini);

task('test',function() {
        echo "Hello, world!";
    });

task('rebrand',function($args) {
        if(!isset($args["from"]) || !isset($args["to"])) {
            echo "Must define 'from' and 'to'\n";
            return;
        }
        $from = $args['from'];
        $to   = $args['to'];

        rename('src/'.$from,'src/'.$to);
        rename('tests/'.$from,'tests/'.$to);

        $paths = array( "./src","./tests" ,"composer.json","index.php");
        foreach($paths as $path) {
            if(is_dir($path)) {
                $files = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($path),
                    RecursiveIteratorIterator::SELF_FIRST);
                foreach($files as $name=>$file) {
                    if($file->isFile()) {
                        $content = file_get_contents($file->getPathname());
                        $newContent = str_replace($from,$to,$content);
                        file_put_contents($file->getPathname(),$newContent);
                    }
                }
            } else if(is_file($path)) {
                $content = file_get_contents($path);
                $newContent = str_replace($from,$to,$content);
                file_put_contents($path,$newContent);
            }
        }

        echo "You should run 'php composer.phar dump-autoload'.\n";
    });

task('change',function($args){
        $to = (isset($args['env'])?$args['env']:'dev');
        unlink('config.ini');
        symlink('condif-'.$to.'.ini','config.ini');
    });

task('deploy',function(){
    passthru('af update latepapo');
});

task('couchdb',function() {
    passthru('lein couchapp --upload resources/couchdb/* '.COUCHDB);
});

