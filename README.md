# Perf Reporter
[![PHP Composer](https://github.com/Fred-QS/Perf-Reporter/actions/workflows/php.yml/badge.svg)](https://github.com/Fred-QS/Perf-Reporter/actions/workflows/php.yml)

Perf-Reporter is a package dedicated to Symfony 5 and more, allowing to measure the performance of the runtime through its execution time and the memory allocated to it.

To install it, run the following command:
```bash
composer require smilian/perfreporter
```
<hr>
<h3>Publish files</h3>

After installing the package, run the following command to publish the files:
```bash
php vendor/smilian/perfreporter/publish
```

By publishing those files, a new controller called "DisplayPerfReportsController.php" has been created in src/Controller folder and defines a new Route with URL: "/perf-reporter".
<img src="https://github.com/Fred-QS/Perf-Reporter/blob/main/public/img/screenshot1.png" alt="screenshot1">
Some commands have been added to bin/console with the "app" namespace.
Check them with:
```bash
   bin/console --short
```
<hr>
<h3>Unpublish files</h3>
If you want to remove the files corresponding to the package, you can also debug them with the command:

```bash
php vendor/smilian/perfreporter/unpublish
```
<hr>
<h3>Public methods list</h3>

```php
// Set timezone (default: "Europe/London")
PerformancesLogger::setTimezone(string $zone);

// Set locale (default: "en")
PerformancesLogger::setLocale(string $locale);

// Launch chronometer
PerformancesLogger::setStart();

// Set Alarm step value in seconds (default: 3)
PerformancesLogger::setAlarmStep(int $val);

// Set Maximum report files to create/conserve (default: 4)
PerformancesLogger::setMax(int $val);

// Set report title (default: "Performances and Measurement")
PerformancesLogger::setTitle(string $data);

// Set app/site owner/customer logo where $data is a absolute path/to/your/image
PerformancesLogger::setAppOwnerLogo(string $data);

// As breadcrumbs, dispatch this method anywhere you want to get performances
PerformancesLogger::setStep(string $data);

// Optional, Set some information if needed, that will fill an array like self::$header[$key] = $value;
PerformancesLogger::setHeader(string $key, mixed $value);

// Render the Report file
PerformancesLogger::getResult();

// Delete reports/ folder
PerformancesLogger::deleteReports();

// Get the list of all reports (if mode === 'html' return perf-reports template, but if mode === '' as default, will return reports list)
PerformancesLogger::getReportList(string $mode = '');

// Get report html content
PerformancesLogger::getReport(string $path);
```
<hr>
<h3>Usage</h3>

In the file where the process works:
```php
// ...
use Smile\Perfreporter\Performers\PerformancesLogger;
// ...
$perfs = PerformancesLogger::setTitle(YOUR_CUSTOM_TITLE)
            ->setTimezone('Europe/Paris')
            ->setLocale('fr')
            ->setAppOwnerLogo(PATH_TO_IMAGE)
            ->setStart();
// Process to check
$perfs::setStep(DETAIL_YOU_NEED_TO_FILL);
// End of the process
$perfs::getResult();
```

Because <b>Smile\Perfreporter\Performers\PerformancesLogger</b> is a static class, you can put some <i>setStep()</i> in any file where the process works and finish to use <i>setRender()</i> in the file where the process ends.

Where process starts
```php
// ...
use Smile\Perfreporter\Performers\PerformancesLogger;
// ...
$perfs = PerformancesLogger::setTitle(YOUR_CUSTOM_TITLE)
            ->setTimezone('Europe/Paris')
            ->setLocale('fr')
            ->setAppOwnerLogo(PATH_TO_IMAGE)
            ->setStart();
// ...
```
Some file where process works
```php
// ...
use Smile\Perfreporter\Performers\PerformancesLogger;
// ...
$perfs::setStep(DETAIL_YOU_NEED_TO_FILL);
// ...
```

Where process ends
```php
// ...
use Smile\Perfreporter\Performers\PerformancesLogger;
// ...
$perfs::getResult();
// ...
```