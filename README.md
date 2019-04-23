# Assets

## Install

````sh
composer require chomenko/asset
````

## Minimal configuration

```neon
asset:
	wwwDir: %wwwDir%
	assetDir: %wwwDir%/app-data/assets

extensions:
	asset: Chomenko\Asset\DI\AssetExtension
```

## Use

```latte
<a n:asset="$photo">
	<img n:asset="$photo, resize, 200, 300">
</a>
```
