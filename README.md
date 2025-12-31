# WHOIS-lookup

一个功能完整的PHP WHOIS查询和DNS记录查询工具，支持多种域名后缀，提供简单易用的域名信息查询功能。

## 🚀 功能特性

### WHOIS查询
- 支持**100+种域名后缀**查询
- 兼容单级和多级域名后缀（如 .com、.co.uk、.com.cn）
- 支持国别域名后缀查询
- 自动识别对应WHOIS服务器
- TCP连接超时控制（10秒）
- 完善的错误处理机制

### DNS记录查询
- 查询域名DNS服务器信息
- 支持NS记录查询
- 返回结构化的DNS服务器列表
- 域名格式验证

### 支持的域名后缀
- **通用顶级域名**：.com、.net、.org、.info、.biz、.us、.io、.cn等
- **新通用顶级域名**：.xyz、.top、.club、.online、.shop、.site、.me等
- **国别域名**：.uk、.de、.jp、.kr、.fr、.it、.ru、.au、.ca等
- **多级域名**：.co.uk、.com.cn、.net.cn、.org.cn、.co.jp等

## 📋 系统要求

- **PHP版本**：PHP 7.0 或更高版本
- **PHP扩展**：
  - `fsockopen()` 函数支持（用于TCP连接）
  - `dns_get_record()` 函数（用于DNS查询）
  - `filter_var()` 函数（用于域名验证）
- **网络要求**：能够访问外网连接到WHOIS服务器

## 📦 安装说明

1. **克隆或下载项目**
   ```bash
   git clone <repository-url>
   cd WHOIS-lookup
   ```

2. **直接使用**
   项目为纯PHP实现，无需安装依赖，直接运行PHP文件即可。

3. **验证安装**
   ```bash
   php whois.php
   php dns.php
   ```

## 🔧 使用方法

### WHOIS查询

```php
<?php
require_once 'whois.php';

// 查询域名WHOIS信息
$domain = '4asport.com'; // 可替换为其他域名
$whoisResult = getDomainWhois($domain);

// 输出结果
if ($whoisResult['status'] === 'success') {
    echo "查询成功！\n";
    echo "域名：" . $whoisResult['domain'] . "\n";
    echo "WHOIS服务器：" . $whoisResult['whois_server'] . "\n";
    echo "----------------------------------------\n";
    echo "WHOIS信息：\n" . $whoisResult['whois_info'];
} else {
    echo "查询失败：" . $whoisResult['message'];
}
?>
```

### DNS记录查询

```php
<?php
require_once 'dns.php';

// 查询域名DNS服务器
$domain = '4asport.com'; // 可替换为其他域名
$dnsResult = getDomainDnsServers($domain);

if ($dnsResult['status'] === 'success') {
    echo "域名：" . $dnsResult['domain'] . "\n";
    echo "DNS服务器列表：\n";
    foreach ($dnsResult['dns_servers'] as $index => $server) {
        echo ($index + 1) . ". " . $server . "\n";
    }
} else {
    echo "查询失败：" . $dnsResult['message'];
}
?>
```

### 命令行使用

```bash
# WHOIS查询
php whois.php

# DNS查询
php dns.php
```

## 🏗️ 项目结构

```
WHOIS-lookup/
├── README.md          # 项目说明文档
├── LICENSE           # GNU GPL v3 许可证
├── whois.php         # WHOIS查询功能
└── dns.php          # DNS记录查询功能
```

## ⚙️ 配置说明

### 自定义超时时间
在 `whois.php` 中修改连接超时时间：
```php
$socket = fsockopen($whoisServer, 43, $errno, $errstr, 10); // 10秒超时
```

### 添加新的域名后缀
在 `whois.php` 中的 `$whoisServers` 数组添加新的域名映射：
```php
'新后缀' => '对应的WHOIS服务器',
```

## 🛡️ 错误处理

项目包含完善的错误处理机制：

- **域名格式验证**：使用PHP内置函数验证域名格式
- **连接超时控制**：防止长时间等待连接
- **错误状态返回**：统一返回状态码和错误信息
- **异常处理**：捕获网络连接异常

## 📝 示例输出

### WHOIS查询结果
```
查询成功！
域名：4asport.com
WHOIS服务器：whois.verisign-grs.com
----------------------------------------
WHOIS信息：
   Domain Name: 4ASPORT.COM
   Registry Domain ID: D123456789
   Registrar WHOIS Server: whois.name.com
   Registrar URL: http://www.name.com
   Updated Date: 2023-01-01T00:00:00Z
   ...
```

### DNS查询结果
```
域名：4asport.com
DNS服务器列表：
1. ns1.example-dns.com
2. ns2.example-dns.com
```

## 🔒 许可证

本项目基于 [GNU General Public License v3](https://www.gnu.org/licenses/gpl-3.0.html) 开源协议。

## 🤝 贡献

欢迎提交Issue和Pull Request来帮助改进项目！

## 📞 联系我们

如有问题或建议，请通过以下方式联系：
- 提交GitHub Issue
- 发送邮件至：[您的邮箱]

---

**注意**：WHOIS查询可能受速率限制影响，请合理使用以避免被WHOIS服务器封禁。
