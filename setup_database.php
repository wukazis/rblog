<?php
// 数据库设置脚本
// 运行此脚本来创建博客系统所需的数据库表

// 包含数据库连接配置
require_once 'inc/db.php';

echo "<h2>博客系统数据库设置</h2>";

try {
    // 创建用户表
    $sql_users = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    
    $pdo->exec($sql_users);
    echo "<p>✅ 用户表创建成功</p>";
    
    // 创建文章表
    $sql_posts = "CREATE TABLE IF NOT EXISTS posts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        content TEXT NOT NULL,
        excerpt TEXT,
        author_id INT,
        status ENUM('draft', 'published') DEFAULT 'draft',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL
    )";
    
    $pdo->exec($sql_posts);
    echo "<p>✅ 文章表创建成功</p>";
    
    // 创建分类表
    $sql_categories = "CREATE TABLE IF NOT EXISTS categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        slug VARCHAR(100) UNIQUE NOT NULL,
        description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    $pdo->exec($sql_categories);
    echo "<p>✅ 分类表创建成功</p>";
    
    // 创建文章分类关联表
    $sql_post_categories = "CREATE TABLE IF NOT EXISTS post_categories (
        post_id INT,
        category_id INT,
        PRIMARY KEY (post_id, category_id),
        FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
        FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
    )";
    
    $pdo->exec($sql_post_categories);
    echo "<p>✅ 文章分类关联表创建成功</p>";
    
    // 创建标签表
    $sql_tags = "CREATE TABLE IF NOT EXISTS tags (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(50) NOT NULL,
        slug VARCHAR(50) UNIQUE NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    $pdo->exec($sql_tags);
    echo "<p>✅ 标签表创建成功</p>";
    
    // 创建文章标签关联表
    $sql_post_tags = "CREATE TABLE IF NOT EXISTS post_tags (
        post_id INT,
        tag_id INT,
        PRIMARY KEY (post_id, tag_id),
        FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
        FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
    )";
    
    $pdo->exec($sql_post_tags);
    echo "<p>✅ 文章标签关联表创建成功</p>";
    
    // 创建评论表
    $sql_comments = "CREATE TABLE IF NOT EXISTS comments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        post_id INT NOT NULL,
        author_name VARCHAR(100) NOT NULL,
        author_email VARCHAR(100) NOT NULL,
        content TEXT NOT NULL,
        status ENUM('pending', 'approved', 'spam') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
    )";
    
    $pdo->exec($sql_comments);
    echo "<p>✅ 评论表创建成功</p>";
    
    // 插入默认管理员用户
    $admin_username = 'admin';
    $admin_password = password_hash('admin123', PASSWORD_DEFAULT);
    $admin_email = 'admin@example.com';
    
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$admin_username]);
    
    if (!$stmt->fetch()) {
        $sql_insert_admin = "INSERT INTO users (username, password, email) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql_insert_admin);
        $stmt->execute([$admin_username, $admin_password, $admin_email]);
        echo "<p>✅ 默认管理员用户创建成功 (用户名: admin, 密码: admin123)</p>";
    } else {
        echo "<p>⚠️ 管理员用户已存在</p>";
    }
    
    // 插入示例分类
    $categories = [
        ['技术', 'tech', '技术相关文章'],
        ['生活', 'life', '生活随笔'],
        ['教程', 'tutorial', '教程和指南']
    ];
    
    foreach ($categories as $category) {
        $stmt = $pdo->prepare("SELECT id FROM categories WHERE slug = ?");
        $stmt->execute([$category[1]]);
        
        if (!$stmt->fetch()) {
            $sql_insert_category = "INSERT INTO categories (name, slug, description) VALUES (?, ?, ?)";
            $stmt = $pdo->prepare($sql_insert_category);
            $stmt->execute($category);
        }
    }
    echo "<p>✅ 示例分类创建成功</p>";
    
    // 插入示例文章
    $stmt = $pdo->prepare("SELECT id FROM posts WHERE title = ?");
    $stmt->execute(['欢迎来到我的博客']);
    
    if (!$stmt->fetch()) {
        $sql_insert_post = "INSERT INTO posts (title, content, excerpt, author_id, status) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql_insert_post);
        $stmt->execute([
            '欢迎来到我的博客',
            '<p>欢迎来到我的个人博客！这里是我分享想法、经验和知识的地方。</p><p>我会定期发布关于技术、生活和各种有趣话题的文章。</p><p>感谢您的访问！</p>',
            '欢迎来到我的个人博客！这里是我分享想法、经验和知识的地方。',
            1,
            'published'
        ]);
        echo "<p>✅ 示例文章创建成功</p>";
    } else {
        echo "<p>⚠️ 示例文章已存在</p>";
    }
    
    echo "<h3>🎉 数据库设置完成！</h3>";
    echo "<p><strong>默认管理员账户：</strong></p>";
    echo "<ul>";
    echo "<li>用户名: admin</li>";
    echo "<li>密码: admin123</li>";
    echo "<li>邮箱: admin@example.com</li>";
    echo "</ul>";
    echo "<p><strong>重要提示：</strong>请在生产环境中修改默认密码！</p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ 数据库设置失败: " . $e->getMessage() . "</p>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h2 { color: #333; }
p { margin: 10px 0; }
ul { margin: 10px 0; padding-left: 20px; }
</style>
