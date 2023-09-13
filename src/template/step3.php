{include common/header@psrphp/installer}
<form action="{echo $router->build('/psrphp/installer/index', ['step'=>4])}" method="POST">
    <table style="margin-top: 20px;">
        <tr>
            <td>
                数据库地址：
            </td>
            <td>
                <input type="text" name="database_server" value="127.0.0.1" required>
            </td>
            <td>
                <small>通常是127.0.0.1</small>
            </td>
        </tr>
        <tr>
            <td>
                数据库端口：
            </td>
            <td>
                <input type="text" name="database_port" value="3306" required>
            </td>
            <td>
                <small>通常是3306</small>
            </td>
        </tr>
        <tr>
            <td>
                数据库帐号：
            </td>
            <td>
                <input type="text" name="database_username" value="root" required>
            </td>
            <td>
            </td>
        </tr>
        <tr>
            <td>
                数据库密码：
            </td>
            <td>
                <input type="text" name="database_password" value="root">
            </td>
            <td>
            </td>
        </tr>
        <tr>
            <td>
                数据库名称：
            </td>
            <td>
                <input type="text" name="database_name" value="" required>
            </td>
            <td>
                <small>本安装程序不会创建数据库，请先手动创建（mysql, utf8mb4）~</small>
            </td>
        </tr>
        <tr>
            <td>
                是否安装演示数据：
            </td>
            <td>
                <label>
                    <input type="radio" name="demo" value="0" checked>
                    不安装
                </label>
                <label>
                    <input type="radio" name="demo" value="1">
                    安装
                </label>
            </td>
            <td>
                <small>演示数据请勿用于正式项目~</small>
            </td>
        </tr>
    </table>
    <div style="display: flex;gap: 10px;margin-top: 20px;">
        <a href="{echo $router->build('/psrphp/installer/index', ['step'=>2])}">上一步</a>
        <button type="submit" style="display: none;">安装</button>
        <a href="#" onclick="this.previousElementSibling.click()">安装</a>
    </div>
</form>
{include common/footer@psrphp/installer}