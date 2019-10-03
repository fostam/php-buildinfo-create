# buildinfo

Store and use build time information. Useful for automated builds where information like
the application version needs to be retrieved at build time, e.g. from a Git tag.

## Features
- Create build information file
- Store information like version, build number, build time etc.
- Read build information in your application and easily use it

## Install
The easiest way to install *buildinfo* is by using [composer](https://getcomposer.org/): 

```
$> composer require fostam/buildinfo
```

After installation, the build time tool can be called from the following location:
```
$> vendor/bin/buildinfo
```

## Usage Overview
Create build info file during the build phase for bundling it into the release:
```
buildinfo create buildinfo.php --set-version 2.1.0 --set-commit d921970aadf03b3cf0e71becdaab3147ba71cdef
```

Reading build info file from the application:
```
$buildInfo = BuildInfo::fromFile('buildinfo.php');
echo "my application version: " . $buildInfo->getVersion();
```


## Build Info Creation
To create a build info file, pass the desired filename to the tool along with the
build information you want to store.

The file location is relative to the current working directory, unless you give
an absolute path.

### Buildinfo File Types
Currently, two file types are supported, PHP and JSON. PHP is faster to read and cachable,
whereas JSON is useful if the build info needs to be processed from outside PHP, too
(e.g. JavaScript).

The file type is specified by the file extension. As you can give multiple build
info targets, you can let both a PHP and JSON file be created.

Example:
```
buildinfo create dist/buildinfo.php dist/buildinfo.json --set-version 2.1.0
```

### Build Info Parameters
There are a couple of predefined build information parameters:
```
--set-name my-application-name
--set-time 2019-07-23T15:34:12Z
--set-version 2.1.0
--set-build-number 3541
--set-branch master
--set-commit d921970aadf03b3cf0e71becdaab3147ba71cdef
```

NOTE: the time

Additionally, arbitrary custom parameters can be given using the `--set` option:
```
--set author=john.doe@example.com
--set "comment=this is the final release"
```

## Build Information Usage
The predefined build info parameters can be retrieved with the following methods:
```
use Fostam\Buildinfo;

$buildInfo = BuildInfo::fromFile('buildinfo.json');
echo $buildInfo->getName();
echo $buildInfo->getTime();
echo $buildInfo->getVersion();
echo $buildInfo->getBuildNumber();
echo $buildInfo->getBranch();
echo $buildInfo->getCommit();
```

The custom parameters can be retrieved with a generic `get()` method:
```
echo $buildInfo->get('author');
echo $buildInfo->get('comment');
```

For convenience, the build time can be also retrieved as `DateTime` object:
```
$dt = $buildInfo->getTimeAsDateTime();
echo $dt->format(DateTime::RFC3339);
```
