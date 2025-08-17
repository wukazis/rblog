<?php
// 数据库连接检查脚本
echo "<h2>数据库连接检查</h2>";

// 检查PHP PDO扩展
if (!extension_loaded('pdo_mysql')) {
    echo "<p style='color: red;'>❌ PDO MySQL扩展未安装</p>";
    echo "<p>请安装PHP PDO MySQL扩展</p>";
    exit;
} else {
    echo "<p>✅ PDO MySQL扩展已安装</p>";
}

// 包含数据库配置
require_once 'inc/db.php';

try {
    // 测试数据库连接
    $pdo->query('SELECT 1');
    echo "<p>✅ 数据库连接成功</p>";
    
    // 检查数据库是否存在
    $stmt = $pdo->query("SELECT DATABASE() as current_db");
    $result = $stmt->fetch();
    echo "<p>✅ 当前数据库: " . $result['current_db'] . "</p>";
    
    // 检查表是否存在
    $tables = ['users', 'posts', 'categories', 'tags', 'comments'];
    echo "<h3>数据库表检查：</h3>";
    
    foreach ($tables as $table) {
        try {
            $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
            if ($stmt->rowCount() > 0) {
                echo "<p>✅ 表 '$table' 存在</p>";
            } else {
                echo "<p>⚠️ 表 '$table' 不存在</p>";
            }
        } catch (PDOException $e) {
            echo "<p>❌ 检查表 '$table' 时出错: " . $e->getMessage() . "</p>";
        }
    }
    
    // 检查MySQL版本
    $stmt = $pdo->query("SELECT VERSION() as version");
    $result = $stmt->fetch();
    echo "<p>✅ MySQL版本: " . $result['version'] . "</p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ 数据库连接失败: " . $e->getMessage() . "</p>";
    echo "<h3>可能的解决方案：</h3>";
    echo "<ul>";
    echo "<li>检查MySQL服务是否正在运行</li>";
    echo "<li>验证数据库主机地址是否正确</li>";
    echo "<li>确认数据库名称是否存在</li>";
    echo "<li>检查用户名和密码是否正确</li>";
    echo "<li>确认用户是否有访问数据库的权限</li>";
    echo "</ul>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h2, h3 { color: #333; }
p { margin: 10px 0; }
ul { margin: 10px 0; padding-left: 20px; }
</style>
