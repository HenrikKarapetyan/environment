# Envoronment

The basic usage of library

## Here is our simple.ini file

```ini
[app]
env=dev
environments[]=dev
environments[]=prod
environments[]=local

[parameters]
basePath = /..
viewDirectory = ${parameters:basePath}/templates
serviceExcludedPaths[] = Core
serviceExcludedPaths[] = Entity
serviceExcludedPaths[] = Repository
sourcesRootPath = ${parameters:basePath}/src
assetsDir = ${parameters:basePath}/public
rootNamespace = \Hk\App
sessionSavePath = ${parameters:basePath}/var/session/
logsSaveDirectory = ${parameters:basePath}/var/logs/
[services]
Hk\App\FignyaInterface = \Hk\App\Fignya
```

```php

$iniConfigParser = new IniEnvironmentParser();
$env             = new Environment($iniConfigParser);
$env->load(__DIR__.'/stubs/simple.ini');

//here parameters is our section name 
//get method returns array of parameters in this point
//and we can work with them as php assoc array
$env->get('parameters')['basePath'];

```