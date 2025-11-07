<?php

return \Illuminate\Foundation\Application::configure(basePath: dirname(__DIR__))
  ->withRouting(
    web: __DIR__ . "/../routes/web.php",
    commands: __DIR__ . "/../routes/console.php",
    health: "/up",
  )
  ->withMiddleware(function ($middleware) {
    //
  })
  ->withExceptions(function ($exceptions) {
    //
  })
  ->withBindings([
    \Illuminate\Contracts\Http\Kernel::class => \App\Http\Kernel::class,
  ])
  ->create();
