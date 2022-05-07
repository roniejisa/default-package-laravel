<?php
namespace ExampleRS\Providers;

use Config;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;

class ExampleRSProvider extends ServiceProvider
{
    const CONFIG_KEY_START = 'nrsc_';
    const CONFIG_ADMIN_KEY = 'sys_';
    
    public function boot()
    {
        // Push config
        $this->initConfigFile();
        // Push view
        $this->loadViewsFrom(__DIR__ . '/views', config(static::CONFIG_KEY_START.'setting.soure_view'));
        // Push Provider Aliast
        // Lưu ý sẽ được khởi tạo sau Provider cuối cùng trong app\config
        $this->initProviderAlias();
        // Push Config muốn ghi đề
        $this->pushConfig();
        // Di chuyển thư mục css , js ,images của package tới file public
        $this->moveFolderTheme();
        // Cài đặt router cho package
        $this->initRouters();
    }

    private function moveFolderTheme()
    {
        if (config(static::CONFIG_KEY_START . 'setting.no_render_assets_file')) {
            return;
        }
        // Cache::rememberForever(config(static::CONFIG_KEY_START.'setting.cache_name'), function () {
        $folderTo = public_path(config(static::CONFIG_KEY_START . 'setting.public_name'));
        $folderCopy = base_path() . config(static::CONFIG_KEY_START . 'setting.base_path') . '/public/';

        /** Chú ý chỗ này sẽ có thể xóa hết file cần xác định đúng folder */
        /** Kiểm tra xem có 1 file bất kì tồn tại trong thư mục public không nếu có thì xóa cả folder cũ đi để thay mới */
        // if (file_exists($folderTo . '/css/base.css')) {
        shell_exec("rm -rf $folderTo");
        // }

        shell_exec("cp -r $folderCopy $folderTo");
        // });
    }

    private function initProviderAlias()
    {
        $config = include base_path() . config(static::CONFIG_KEY_START . 'setting.base_path') . "/config/app.php";
        foreach ($config['providers'] as $key => $value) {
            $this->app->register($value);
        }
        foreach ($config['aliases'] as $key => $value) {
            AliasLoader::getInstance()->alias($key, $value);
        }
        foreach($config['listeners'] as $listener){
            Event::subscribe($listener);
        }
    }

    private function initConfigFile()
    {
        $arr = glob(base_path() . "/packages/example_packages/config/*.php");
        foreach ($arr as $value) {
            $nameConfig = pathinfo($value)['filename'];
            if ($nameConfig !== 'app') {
                \Config::set(static::CONFIG_KEY_START . $nameConfig, include_once $value);
            }
        }
    }

    private function pushConfig()
    {   
        $arrayConfigAdminNeedEdit = ['action'];
        foreach($arrayConfigAdminNeedEdit as $config_table){
            $config_defaults = config(static::CONFIG_ADMIN_KEY.$config_table, []);
            $newArray = [];
            foreach ($config_defaults as $table => $value) {
                $newArray[$table] = $value;
            }
            $config_package_currents = config(static::CONFIG_KEY_START . $config_table, []);
            foreach ($config_package_currents as $table => $data) {
                $newArray[$table] = $data;
            }
            Config::set(static::CONFIG_ADMIN_KEY.$config_table, $newArray);
        }
    }

    public function initRouters()
    {
        $routes = glob(base_path() . config(static::CONFIG_KEY_START . 'setting.base_path') . "/routes/*.php");
        $this->routes(function () use ($routes) {
            foreach ($routes as $route) {
                $name = pathinfo($route)['filename'];
                if ($name == 'web') {
                    $this->loadRoutesFrom(base_path() . config(static::CONFIG_KEY_START . 'setting.base_path') . '/routes/web.php');
                } else {
                    Route::prefix(config(static::CONFIG_KEY_START . 'setting.route_prefix') . "/$name")->middleware('web')
                        ->namespace(config(static::CONFIG_KEY_START . 'setting.namespace_controller'))
                        ->group($route);
                }

            }
        });
    }
}
