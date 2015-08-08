<!doctype html>
<title>Database Error</title>
<style>
  body { text-align: center; padding: 20px; }
  @media (min-width: 768px){
    body{ padding-top: 150px; }
  }
  h1 { font-size: 50px; }
  h1 img {
    width: 80px;
  }
  body { font: 20px Helvetica, sans-serif; color: #333; }
  article { display: block; text-align: left; max-width: 650px; margin: 0 auto; }
  a { color: #dc8100; text-decoration: none; }
  a:hover { color: #333; text-decoration: none; }
</style>

<article>
    <h1>{{ HTML::image('images/dino.gif') }}&nbsp;Database Error!</h1>
    <div>
        @if($exception->getCode() == 2002)
            <p>Sorry, application could not connect to the database.</p>
        @elseif($exception->getCode() == 1045)
            <p>Could not connect to database with provided credentials.</p>
        @else
            <p>Sorry, something went wrong with the database.</p>
        @endif

        @if($isWhitelisted)
            <pre>{{ $exception->getMessage() }}</pre>
        @endif
    </div>
</article>
