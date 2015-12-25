<!doctype html>
<html>
    <head>
        <title>Database Error</title>
        <style>
            body { text-align: center; padding: 20px; }
            @media (min-width: 768px){
                body{ padding-top: 150px; }
            }
            h1 { font-size: 50px; }
            body { font: 20px Helvetica, sans-serif; color: #333; }
            article { display: block; text-align: left; max-width: 750px; margin: 0 auto; }
            pre {
                white-space: pre-wrap;
                white-space: -moz-pre-wrap;
                white-space: -pre-wrap;
                white-space: -o-pre-wrap;
                word-wrap: break-word;
            }
        </style>
    </head>

    <body>
        <article>
            <h1>&nbsp;Database Error!</h1>
            <div>
                @if($exception->getCode() == 2002)
                    <p>Sorry, application could not connect to the database.</p>
                @elseif($exception->getCode() == 1045)
                    <p>Could not connect to database with provided credentials.</p>
                @elseif($exception->getCode() == 1044)
                    <p>Database user doesn't have privileges to connect to the database.</p>
                @else
                    <p>Sorry, something went wrong with the database.</p>
                @endif

                @if(isset($isWhitelisted) && $isWhitelisted)
                    <pre>{{ $exception->getMessage() }}</pre>
                @endif
            </div>
        </article>
    </body>
</html>
