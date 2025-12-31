<?php
/**
 * https://lookup.icann.org/en/lookup
 * https://rdap.namesilo.com/domain/4ASPORT.COM
 * 获取域名的DNS服务器地址
 * @param string $domain 完整域名
 * @return array 包含查询状态和DNS服务器列表
 */
function getDomainDnsServers($domain) {
    // 验证域名格式
    if (!filter_var($domain, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) {
        return [
            'status' => 'error',
            'message' => '无效的域名格式'
        ];
    }

    // 获取DNS服务器信息
    $dnsServers = dns_get_record($domain, DNS_NS);
    echo "<pre>";
    print_r($dnsServers);
    if (empty($dnsServers)) {
        return [
            'status' => 'error',
            'message' => '无法获取该域名的DNS服务器信息'
        ];
    }

    // 提取DNS服务器域名
    $serverList = [];
    foreach ($dnsServers as $server) {
        $serverList[] = $server['target'];
    }

    return [
        'status' => 'success',
        'domain' => $domain,
        'dns_servers' => $serverList
    ];
}

// 测试使用
$domain = '4asport.com';
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