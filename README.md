# APEE 短网址平台

> 轻量简约的在线短网址生成平台

## 项目信息

- 作者：欧阳鹏（鹏优创工作室）
- 主页：https://apee.top

## 部署流程

- 本项目后端使用 PHP 开发，请确保网站支持 PHP 和 MySQL
- 本项目要求部署在网站根目录，不支持二级目录
- 下载仓库源代码，上传到网站目录
- 复制 `config.default.php`，命名为 `config.php`
- 填写 `config.php` 中的配置信息
- 设置网站伪静态 `rewrite ^/(\w+)/?(\w*)$ /api/go_url.php?end=$1&password=$2;`
- 部署完成，直接访问即可