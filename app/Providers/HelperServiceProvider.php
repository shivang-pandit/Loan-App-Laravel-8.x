<?php
/**
 * Created by PhpStorm editor.
 * User: Shivang
 * Date: 02-08-2021
 * Time: pm 07:30
 */

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class HelperServiceProvider extends ServiceProvider
{

    private $files = array();

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $recursive_path = __DIR__ . '/../Helpers';

        $this->require_once_dir($recursive_path . "/*");

        for ($f = 0; $f < count($this->files); $f++) {
            $file = $this->files[$f];
            /** @noinspection PhpIncludeInspection */
            require_once($file);
        }

    }


    public function require_once_dir($dir)
    {
        $item = glob($dir);
        foreach ($item as $filename) {
            if (is_dir($filename)) {
                $this->require_once_dir($filename . '/' . "*");
            } elseif (is_file($filename)) {
                $this->files[] = $filename;
            }
        }
    }

}