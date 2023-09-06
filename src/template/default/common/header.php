<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>系统安装</title>
    <style>
        body {
            padding: 10px;
            line-height: 1.4;
        }

        table {
            border-collapse: collapse;
        }

        table th,
        table td {
            border: 1px solid #ddd;
            padding: 5px;
        }
    </style>
</head>

<body>
    <h1>系统安装/setup</h1>
    <div style="display: flex;gap: 10px;">
        <?php
        $step = $_GET['step'] ?? 0;
        $per = ($step + 1) * 20;
        $steps = ['系统简介', '协议条款', '环境检测', '初始设置', '安装完成'];
        ?>
        {foreach $steps as $k=>$v}
        {if $step > $k}
        <div style="color: gray;">{$v}</div>
        {elseif $step == $k}
        <div style="color: red;">{$v}</div>
        {else}
        <div style="color: gray;">{$v}</div>
        {/if}
        {/foreach}
    </div>