{include common/header@psrphp/installer}
<h2>安装成功</h2>
<div style="border: 1px dotted red;background:yellow;padding: 10px;">后台帐户：<b>admin</b>&nbsp;&nbsp;&nbsp;密码：<b>123456</b></div>
<div style="color:red;">此页面只会出现一次，请牢记您的帐户和密码！</div>
<div style="display: flex;gap: 10px;margin-top: 20px;">
    <a href="{echo $router->build('/psrphp/admin/index')}" target="_blank">登录后台</a>
</div>
{include common/footer@psrphp/installer}