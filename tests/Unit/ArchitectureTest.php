<?php

arch('controllers are suffixed with Controller')
    ->expect('App\Http\Controllers')
    ->toHaveSuffix('Controller');

arch('policies are suffixed with Policy')
    ->expect('App\Policies')
    ->toBeClasses()
    ->toHaveSuffix('Policy');

arch('forbids debug helpers in application code')
    ->expect('App')
    ->not->toUse(['dd', 'dump', 'ray']);
