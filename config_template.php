<?php
// 数据库配置模板
// 复制此文件为 inc/db.php 并修改相应的配置值

// 数据库连接配置
$host = 'localhost';        // 数据库主机地址
$db   = 'blog';            // 数据库名称
$user = 'your_username';   // 数据库用户名
$pass = 'your_password';   // 数据库密码
$charset = 'utf8mb4';      // 字符集

// 构建DSN连接字符串
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

// PDO选项配置
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,  // 错误模式
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // 默认获取模式
    PDO::ATTR_EMULATE_PREPARES   => false,                  // 禁用模拟预处理
];

// 创建PDO连接
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // 记录错误日志
    error_log("数据库连接失败: " . $e->getMessage());
    
    // 显示用户友好的错误信息
    if (defined('DEBUG_MODE') && DEBUG_MODE) {
        throw new \PDOException($e->getMessage(), (int)$e->getCode());
    } else {
        die("数据库连接失败，请检查配置或联系管理员。");
    }
}

// 设置时区（可选）
// $pdo->exec("SET time_zone = '+08:00'");

// 定义常量（可选）
define('DB_HOST', $host);
define('DB_NAME', $db);
define('DB_USER', $user);
define('DB_CHARSET', $charset);

// 数据库连接测试函数（可选）
function testDatabaseConnection() {
    global $pdo;
    try {
        $pdo->query('SELECT 1');
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

// 获取数据库信息函数（可选）
function getDatabaseInfo() {
    global $pdo;
    try {
        $stmt = $pdo->query("SELECT VERSION() as version, DATABASE() as database_name");
        return $stmt->fetch();
    } catch (PDOException $e) {
        return false;
    }
}
?>
