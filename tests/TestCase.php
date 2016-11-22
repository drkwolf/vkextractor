<?php

abstract class TestCase extends Illuminate\Foundation\Testing\TestCase
{
    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://localhost';

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
      $app = require __DIR__.'/../bootstrap/app.php';

      $app->loadEnvironmentFrom('.env.testing'); // specify the file to use for environment, must be run before boostrap

      $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

      return $app;
    }

      public function saveTo(Array $data, $id, $filename)
  {
    $root = storage_path('app/data/'.$id);
    $path = $root . '/'. $filename . '.php';
    if (!is_dir($root)) mkdir($pathname = $root, $mode=0777, $recursive = true);

    file_put_contents($path,'<?php return '. var_export($data, true).";\n");
  }

  public function getArray($id, $filename)
  {
    $root = storage_path('app/data/'.$id);
    $path = $root . '/'. $filename . '.php';
    return include $path;
//    return $data;
  }

}
