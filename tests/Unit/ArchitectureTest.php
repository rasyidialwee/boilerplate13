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

arch('forbids debug helpers in factories')
    ->expect('Database\Factories')
    ->not->toUse(['dd', 'dump', 'ray']);

arch('forbids debug helpers in seeders')
    ->expect('Database\Seeders')
    ->not->toUse(['dd', 'dump', 'ray']);

arch('forbids env helper outside application layer')
    ->expect('App')
    ->not->toUse(['env']);
