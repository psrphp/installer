<?php

declare(strict_types=1);

namespace App\Psrphp\Installer\Http;

use App\Psrphp\Admin\Lib\Response;
use App\Psrphp\Admin\Traits\RestfulTrait;
use Composer\Autoload\ClassLoader;
use PsrPHP\Request\Request;
use PsrPHP\Template\Template;
use PDO;
use Rah\Danpu\Dump;
use Rah\Danpu\Import;
use ReflectionClass;
use Throwable;

class Index
{

    use RestfulTrait;

    public function get(
        Request $request,
        Template $template
    ) {
        return $template->renderFromFile('step' . $request->get('step', '0') . '@psrphp/installer', [
            'root' => dirname(dirname(dirname((new ReflectionClass(ClassLoader::class))->getFileName())))
        ]);
    }

    public function post(
        Template $template,
        Request $request,
        Dump $dump
    ) {
        $root = dirname(dirname(dirname((new ReflectionClass(ClassLoader::class))->getFileName())));
        try {
            $sql_file = $root . '/config/psrphp/installer/' . ($request->post('demo') == '1' ? 'install_demo.sql' : 'install.sql');
            if (is_file($sql_file)) {
                $dump
                    ->file($sql_file)
                    ->dsn('mysql:dbname=' . $request->post('database_name') . ';host=' . $request->post('database_server'))
                    ->user($request->post('database_username'))
                    ->pass($request->post('database_password'))
                    ->attributes([
                        PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
                    ])
                    ->tmp($root . '/runtime');
                new Import($dump);
            }

            $databasetpl = <<<'str'
<?php
return [
    'master'=>[
        'database_type' => 'mysql',
        'database_name' => '{database_name}',
        'server' => '{server}',
        'username' => '{username}',
        'password' => '{password}',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_general_ci',
        'port' => '{port}',
        'logging' => false,
        'option' => [
            \PDO::ATTR_CASE => \PDO::CASE_NATURAL,
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_STRINGIFY_FETCHES => false,
            \PDO::ATTR_EMULATE_PREPARES => false,
        ],
        'command' => ['SET SQL_MODE=ANSI_QUOTES'],
    ],
];
str;

            $database_file = $root . '/config/database.php';
            if (!file_exists($database_file)) {
                if (!is_dir(dirname($database_file))) {
                    mkdir(dirname($database_file), 0755, true);
                }
            }
            file_put_contents($database_file, str_replace([
                '{server}',
                '{port}',
                '{database_name}',
                '{username}',
                '{password}',
            ], [
                $request->post('database_server'),
                $request->post('database_port'),
                $request->post('database_name'),
                $request->post('database_username'),
                $request->post('database_password'),
            ], $databasetpl));
            if (!is_dir($root . '/config/psrphp/installer/')) {
                mkdir($root . '/config/psrphp/installer/', 0755, true);
            }
            file_put_contents($root . '/config/psrphp/installer/disabled.lock', date('Y-m-d H:i:s'));
        } catch (Throwable $th) {
            return Response::error('发生错误：' . $th->getMessage());
        }

        return $template->renderFromFile('success@psrphp/installer', [
            'account' => 'admin',
            'password' => '123456',
        ]);
    }
}
