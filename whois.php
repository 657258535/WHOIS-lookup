<?php
/**
 * 根据域名后缀获取对应WHOIS服务器
 * @param string $domain 完整域名（如4asport.com、baidu.co.uk）
 * @return string|null 对应WHOIS服务器地址
 */
function getWhoisServer($domain) {
    // 提取域名后缀（兼容多级后缀，如.co.uk、.com.cn）
    $parts = explode('.', $domain);
    $partCount = count($parts);
    $tld = '';
    
    // 优先匹配多级后缀
    if ($partCount >= 2) {
        $multiTld1 = $parts[$partCount-2] . '.' . $parts[$partCount-1]; // 二级+顶级（如co.uk）
        $multiTld2 = $partCount >= 3 ? $parts[$partCount-3] . '.' . $multiTld1 : ''; // 三级+二级+顶级（如co.in.net，极少用）
        
        // 先判断多级后缀，再判断单级后缀
        $tld = $multiTld2 ?: ($multiTld1 ?: end($parts));
    } else {
        $tld = end($parts);
    }
    
    if (empty($tld)) return null;

    // 扩展后的WHOIS服务器映射（含单级、多级后缀）
    $whoisServers = [
        // 基础通用后缀
        'com'       => 'whois.verisign-grs.com',
        'net'       => 'whois.verisign-grs.com',
        'org'       => 'whois.pir.org',
        'info'      => 'whois.afilias.net',
        'biz'       => 'whois.neulevel.biz',
        'us'        => 'whois.nic.us',
        'io'        => 'whois.nic.io',
        'cn'        => 'whois.cnnic.cn',
        // 新增国际通用后缀
        'xyz'       => 'whois.nic.xyz',
        'top'       => 'whois.nic.top',
        'club'      => 'whois.nic.club',
        'online'    => 'whois.nic.online',
        'shop'      => 'whois.nic.shop',
        'site'      => 'whois.nic.site',
        'me'        => 'whois.nic.me',
        'cc'        => 'whois.nic.cc',
        'tv'        => 'whois.nic.tv',
        'pw'        => 'whois.nic.pw',
        'tk'        => 'whois.nic.tk',
        'ml'        => 'whois.nic.ml',
        'ga'        => 'whois.nic.ga',
        'cf'        => 'whois.nic.cf',
        'name'      => 'whois.nic.name',
        'mobi'      => 'whois.nic.mobi',
        'asia'      => 'whois.nic.asia',
        'pro'       => 'whois.nic.pro',
        'tel'       => 'whois.nic.tel',
        // 新增国别后缀（单级）
        'uk'        => 'whois.nic.uk',
        'de'        => 'whois.denic.de',
        'jp'        => 'whois.jprs.jp',
        'kr'        => 'whois.krnic.net',
        'fr'        => 'whois.nic.fr',
        'it'        => 'whois.nic.it',
        'ru'        => 'whois.ripn.net',
        'au'        => 'whois.audns.net.au',
        'ca'        => 'whois.cira.ca',
        'sg'        => 'whois.nic.sg',
        'hk'        => 'whois.hknic.net.hk',
        'tw'        => 'whois.twnic.net.tw',
        'th'        => 'whois.thnic.co.th',
        'vn'        => 'whois.vnnic.vn',
        'in'        => 'whois.inregistry.net',
        'br'        => 'whois.registro.br',
        'es'        => 'whois.nic.es',
        'nl'        => 'whois.domain-registry.nl',
        'se'        => 'whois.iis.se',
        'ch'        => 'whois.nic.ch',
        'at'        => 'whois.nic.at',
        'be'        => 'whois.dns.be',
        'dk'        => 'whois.dk-hostmaster.dk',
        'fi'        => 'whois.fi',
        // 新增多级国别后缀
        'co.uk'     => 'whois.nic.uk',
        'org.uk'    => 'whois.nic.uk',
        'me.uk'     => 'whois.nic.uk',
        'com.cn'    => 'whois.cnnic.cn',
        'net.cn'    => 'whois.cnnic.cn',
        'org.cn'    => 'whois.cnnic.cn',
        'gov.cn'    => 'whois.cnnic.cn',
        'hk.com'    => 'whois.hkdnr.hk',
        'co.jp'     => 'whois.jprs.jp',
        'co.kr'     => 'whois.krnic.net',
        'co.ca'     => 'whois.cira.ca',
        'co.in'     => 'whois.inregistry.net',
        'co.au'     => 'whois.audns.net.au',
        'co.sg'     => 'whois.nic.sg'
    ];

    // 优先匹配完整后缀（多级/单级），匹配不到则返回null
    return isset($whoisServers[$tld]) ? $whoisServers[$tld] : (isset($whoisServers[end($parts)]) ? $whoisServers[end($parts)] : null);
}

/**
 * 获取域名WHOIS信息
 * @param string $domain 完整域名
 * @return array 包含查询状态和WHOIS信息
 */
function getDomainWhois($domain) {
    // 验证域名格式
    if (!filter_var($domain, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) {
        return [
            'status' => 'error',
            'message' => '无效的域名格式'
        ];
    }

    // 获取对应WHOIS服务器
    $whoisServer = getWhoisServer($domain);
    if (!$whoisServer) {
        return [
            'status' => 'error',
            'message' => '暂不支持该域名后缀的WHOIS查询'
        ];
    }

    // 建立TCP连接（端口43，超时10秒）
    $socket = fsockopen($whoisServer, 43, $errno, $errstr, 10);
    if (!$socket) {
        return [
            'status' => 'error',
            'message' => "连接WHOIS服务器失败：".$errstr."（错误码：".$errno."）"
        ];
    }

    // 发送查询请求（末尾必须加\r\n）
    fwrite($socket, $domain . "\r\n");

    // 读取返回数据
    $whoisData = '';
    while (!feof($socket)) {
        $whoisData .= fgets($socket, 1024);
    }

    // 关闭连接
    fclose($socket);

    return [
        'status' => 'success',
        'whois_server' => $whoisServer,
        'domain' => $domain,
        'whois_info' => $whoisData
    ];
}

// -------------------------- 测试使用 --------------------------
$domain = '4asport.com'; // 可替换为其他域名，如baidu.com.cn、google.co.uk
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
