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
- 设置网站伪静态

      rewrite "^/s/(\w*)$" "/api/go_share.php?end=$1" break;
      rewrite "^/(\w+)/?(\w*)$" "/api/go_url.php?end=$1&password=$2" break;

- 部署完成，直接访问即可

## 项目特点

- 前端使用 jQuery 和 Bootstrap，后端使用 PHP，部署简单方便
- 使用 SPA 模式开发，交互效果好
- 简约轻量，贴心设计，不为盈利，只求实用
- 支持短链跳转和网址分享页
- 支持短链加密和设置有效期
- 支持设置短链描述文本
- 通过伪静态对 URL 重写
- 单独设计加密算法，结合代码混淆，实现对 API 的保护

![](img/%E6%88%AA%E5%9B%BE_home.jpg)
![](img/%E6%88%AA%E5%9B%BE_result.jpg)