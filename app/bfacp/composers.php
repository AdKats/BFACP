<?php

use Illuminate\Support\Facades\View as View;

View::composer('*', 'BFACP\Composers\UserComposer');
