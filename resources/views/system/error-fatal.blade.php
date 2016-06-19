<!doctype html>
<html>
<head>
    <title>Fatal System Error</title>
    <style>
        body {
            text-align: center;
            padding: 20px;
        }

        @media (min-width: 768px) {
            body {
                padding-top: 150px;
            }
        }

        h1 {
            font-size: 50px;
        }

        body {
            font: 20px Helvetica, sans-serif;
            color: #333;
        }

        article {
            display: block;
            text-align: left;
            max-width: 750px;
            margin: 0 auto;
        }

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
    <h1>&nbsp;Fatal System Error!</h1>

    <div>
        <pre>{{ $exception->getMessage() }}</pre>
    </div>
</article>
</body>
</html>
