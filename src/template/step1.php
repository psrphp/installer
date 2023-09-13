{include common/header@psrphp/installer}
<div style="max-height: 300px;overflow-y: auto;border: 1px solid #ff0000;background: yellow;padding: 10px;margin-top: 20px;">
    {if file_exists($root.'/LICENSE')}
    <pre>{echo file_get_contents($root.'/LICENSE')}</pre>
    {else}
    <div>暂无授权协议</div>
    {/if}
</div>
<div style="display: flex;gap: 10px;margin-top: 20px;">
    <a href="{echo $router->build('/psrphp/installer/index', ['step'=>0])}">上一步</a>
    <a href="{echo $router->build('/psrphp/installer/index', ['step'=>2])}" onclick="return confirm('此协议具有法律效应，请认真阅读！！\r\n同意请点击“确定”\r\n不同意请点击“取消”');">下一步</a>
</div>
{include common/footer@psrphp/installer}