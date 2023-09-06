<?php

declare(strict_types=1);

namespace App\Psrphp\Installer\Http;

use App\Psrphp\Admin\Lib\Response;
use PDO;
use PsrPHP\Framework\Framework;
use PsrPHP\Request\Request;
use PsrPHP\Template\Template;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use PsrPHP\Framework\Config;
use PsrPHP\Router\Router;
use PsrPHP\Session\Session;
use Rah\Danpu\Dump;
use Rah\Danpu\Import;
use Throwable;

class Index implements RequestHandlerInterface
{
    public function handle(
        ServerRequestInterface $request
    ): ResponseInterface {
        $method = strtolower($request->getMethod());
        if (in_array($method, ['get', 'put', 'post', 'delete', 'head', 'patch', 'options']) && is_callable([$this, $method])) {
            $resp = Framework::execute([$this, $method]);
            if (is_scalar($resp) || (is_object($resp) && method_exists($resp, '__toString'))) {
                return Response::html((string) $resp);
            }
            return $resp;
        } else {
            return Framework::execute(function (
                ResponseFactoryInterface $responseFactory
            ): ResponseInterface {
                return $responseFactory->createResponse(405);
            });
        }
    }

    public function get(
        Router $router,
        Config $config,
        Request $request,
        Template $template
    ) {
        $root = dirname(dirname(dirname(dirname(dirname(dirname(__DIR__))))));
        return $template->renderFromFile('step' . $request->get('step', '0') . '@psrphp/installer', [
            'root' => $root,
            'config' => $config,
            'router' => $router,
            'request' => $request,
        ]);
    }

    public function post(
        Dump $dump,
        Router $router,
        Request $request,
        Template $template,
    ) {
        $root = dirname(dirname(dirname(dirname(dirname(dirname(__DIR__))))));
        try {
            $sql_file = $root . '/' . ($request->post('demo') == '1' ? 'install_demo.sql' : 'install.sql');
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

            $session = new Session();
            $session->set('admin_auth', 1);
        } catch (Throwable $th) {
            return Response::error('发生错误：' . $th->getMessage());
        }

        return $template->renderFromFile('success@psrphp/installer', [
            'router' => $router,
        ]);
    }
}
