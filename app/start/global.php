<?php
App::error(function(Exception $exception, $code)
{
    if ($exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException)
    {
        Log::error('NotFoundHttpException Route: ' . Request::url() );
    }

    Log::error($exception);
});


/*
|--------------------------------------------------------------------------
| Register The Laravel Class Loader
|--------------------------------------------------------------------------
|
| In addition to using Composer, you may use the Laravel class loader to
| load your controllers and models. This is useful for keeping all of
| your classes in the "global" namespace without Composer updating.
|
*/

ClassLoader::addDirectories(array(

	app_path().'/commands',
	app_path().'/controllers',
	app_path().'/presenters',
	app_path().'/models',
	app_path().'/libraries',
	app_path().'/database/migrations',
	app_path().'/database/seeds',

));

/*
|--------------------------------------------------------------------------
| Application Error Logger
|--------------------------------------------------------------------------
|
| Here we will configure the error logger setup for the application which
| is built on top of the wonderful Monolog library. By default we will
| build a rotating log file setup which creates a new file each day.
|
*/

$logFile = 'log-'.php_sapi_name().'.txt';

Log::useDailyFiles(storage_path().'/logs/'.$logFile);

/*
|--------------------------------------------------------------------------
| Application Error Handler
|--------------------------------------------------------------------------
|
| Here you may handle any errors that occur in your application, including
| logging them or displaying custom views for specific errors. You may
| even register several error handlers to handle different types of
| exceptions. If nothing is returned, the default error view is
| shown, which includes a detailed stack trace during debug.
|
*/

App::error(function(Exception $exception, $code)
{
    $pathInfo = Request::getPathInfo();
    $message = $exception->getMessage() ?: 'Exception';
    Log::error("$code - $message @ $pathInfo\r\n$exception");
    
    if (Config::get('app.debug')) {
    	return;
    }

    switch ($code)
    {
        case 403:
            return Response::view('error/403', array(), 403);

        case 500:
            return Response::view('error/500', array(), 500);

        default:
            return Response::view('error/404', array(), $code);
    }
});

/*
|--------------------------------------------------------------------------
| Maintenance Mode Handler
|--------------------------------------------------------------------------
|
| The "down" Artisan command gives you the ability to put an application
| into maintenance mode. Here, you will define what is displayed back
| to the user if maintenace mode is in effect for this application.
|
*/

App::down(function()
{
	return Response::make("Be right back!", 503);
});


/*
*   An improvised Gist from
*   https://gist.github.com/zmsaunders/5619519  
*   https://gist.github.com/garagesocial/6059962
*/
App::after(function($request, $response)
{
	
	if(App::Environment() != 'local')
	{
		if($response instanceof Illuminate\Http\Response)
		{
			$output = $response->getOriginalContent();
			
			$filters = array(
				//Remove HTML comments except IE conditions
				'/<!--(?!\s*(?:\[if [^\]]+]|<!|>))(?:(?!-->).)*-->/s'	=> '', 
				// Remove comments in the form /* */
				'/(?<!\S)\/\/\s*[^\r\n]*/'				=> '',
				// Shorten multiple white spaces 
				'/\s{2,}/'						=> '',
				// Collapse new lines 
				'/(\r?\n)/'						=> '', 
			);
			
			$output = preg_replace(array_keys($filters), array_values($filters), $output);
			$response->setContent($output);
		}
	}
});

/*
|--------------------------------------------------------------------------
| Require The Filters File
|--------------------------------------------------------------------------
|
| Next we will load the filters file for the application. This gives us
| a nice separate location to store our route and application filter
| definitions instead of putting them all in the main routes file.
|
*/

require __DIR__.'/../filters.php';
