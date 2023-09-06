{include common/header@psrphp/installer}
    <?php
    $env_err = false;
    ?>
    <?php
    // 检查安装环境
    function check_env(&$env_err)
    {
        $items = [
            'os' => ['操作系统', '不限制', '类Unix', PHP_OS, true],
            'php' => ['PHP版本', '7.1', '7.1+', PHP_VERSION, true],
            'upload' => ['附件上传', '不限制', '2M+', '未知', true],
            'gd' => ['GD库', '2.0', '2.0+', '未知', true],
            'disk' => ['磁盘空间', '100M', '不限制', '未知', true],
        ];

        //PHP环境检测
        if ($items['php'][3] < $items['php'][1]) {
            $items['php'][4] = false;
            $env_err = true;
        }

        //附件上传检测
        if (@ini_get('file_uploads'))
            $items['upload'][3] = ini_get('upload_max_filesize');

        //GD库检测
        $tmp = function_exists('gd_info') ? gd_info() : [];
        if (empty($tmp['GD Version'])) {
            $items['gd'][3] = '未安装';
            $items['gd'][4] = false;
            $env_err = true;
        } else {
            $items['gd'][3] = $tmp['GD Version'];
        }
        unset($tmp);

        //磁盘空间检测
        if (function_exists('disk_free_space')) {
            $items['disk'][3] = floor(disk_free_space(realpath('./') . DIRECTORY_SEPARATOR) / (1024 * 1024)) . 'M';
        }
        return $items;
    }
    $env = check_env($env_err);
    ?>
    <table style="margin-top: 20px;">
        <thead>
            <tr>
                <th>检测项</th>
                <th>最低要求</th>
                <th>推荐配置</th>
                <th>当前配置</th>
                <th style="width:120px;">检测结果</th>
            </tr>
        </thead>
        <tbody>
            {foreach $env as $v}
            <tr>
                <td>{$v[0]}</td>
                <td>{$v[1]}</td>
                <td>{$v[2]}</td>
                <td>{$v[3]}</td>
                <td>{echo $v[4]?'<span class="text-primary">通过</span>':'<span class="text-danger">不通过</span>'}</td>
            </tr>
            {/foreach}
        </tbody>
    </table>
    <?php

    function check_dir(string $dir): bool
    {
        return is_dir($dir) ? is_writable($dir) : check_dir(dirname($dir));
    }

    /**
     * 目录，文件读写检测
     * @return array 检测数据
     */
    function check_dirfile(array $items, &$env_err)
    {
        $root = dirname(dirname(dirname((new ReflectionClass(Composer\InstalledVersions::class))->getFileName())));
        foreach ($items as &$val) {
            if ('dir' == $val[0]) {
                if (!is_dir($val[1]) || !is_writable($val[1])) {
                    $val[2] = '不存在或不可写';
                    $val[3] = false;
                    $env_err = true;
                }
            } else {
                if (!file_exists($val[1])) {
                    if (!check_dir(dirname($val[1]))) {
                        $val[2] = '不可写';
                        $val[3] = false;
                        $env_err = true;
                    }
                } else {
                    if (!is_writable($val[1])) {
                        $val[2] = '不可写';
                        $val[3] = false;
                        $env_err = true;
                    }
                }
            }
            $val[1] = str_replace($root, '', $val[1]);
        }
        return $items;
    }

    $dirfile = check_dirfile($config->get('dirfile@psrphp/installer', [
        ['dir', './upload', '可写', true],
        ['dir', $root . '/config', '可写', true],
        ['dir', $root . '/runtime', '可写', true],
        ['file', $root . '/config/database.php', '可写', true],
    ]), $env_err);
    ?>
    <table style="margin-top: 20px;">
        <thead>
            <tr>
                <th>检测项</th>
                <th>要求</th>
                <th>当前配置</th>
                <th style="width:120px;">检测结果</th>
            </tr>
        </thead>
        <tbody>
            {foreach $dirfile as $v}
            <tr>
                <td>{$v[1]}</td>
                <td>可写</td>
                <td>{$v[2]}</td>
                <td>{echo $v[3]?'<span class="text-primary">通过</span>':'<span class="text-danger">不通过</span>'}</td>
            </tr>
            {/foreach}
        </tbody>
    </table>
    <?php
    /**
     * 函数检测
     * @return array 检测数据
     */
    function check_func(array $items, &$env_err)
    {
        foreach ($items as &$val) {
            if (
                ('类' == $val[3] && !class_exists($val[0]))
                || ('模块' == $val[3] && !extension_loaded($val[0]))
                || ('函数' == $val[3] && !function_exists($val[0]))
            ) {
                $val[1] = '不支持';
                $val[2] = 'error';
                $env_err = true;
            }
        }
        return $items;
    }
    $func = check_func($config->get('func@psrphp/installer', [
        ['pdo', '支持', true, '类'],
        ['pdo_mysql', '支持', true, '模块'],
        ['mb_strlen', '支持', true, '函数'],
        ['curl_init', '支持', true, '函数'],
    ]), $env_err);
    ?>
    <table style="margin-top: 20px;">
        <thead>
            <tr>
                <th>检测项</th>
                <th>要求</th>
                <th>当前配置</th>
                <th style="width:120px;">检测结果</th>
            </tr>
        </thead>
        <tbody>
            {foreach $func as $v}
            <tr>
                <td>{$v[0]}</td>
                <td>支持</td>
                <td>{$v[1]}</td>
                <td>{echo $v[2]?'<span class="text-primary">通过</span>':'<span class="text-danger">不通过</span>'}</td>
            </tr>
            {/foreach}
        </tbody>
    </table>
<div style="display: flex;gap: 10px;margin-top: 20px;">
    {if !$env_err}
    <a href="{echo $router->build('/psrphp/installer/index', ['step'=>1])}">上一步</a>
    <a href="{echo $router->build('/psrphp/installer/index', ['step'=>3])}">下一步</a>
    {else}
    <a href="{echo $router->build('/psrphp/installer/index', ['step'=>1])}">上一步</a>
    <a href="{echo $router->build('/psrphp/installer/index', ['step'=>2])}">系统环境不满足需求，请检查！</a>
    {/if}
</div>
{include common/footer@psrphp/installer}