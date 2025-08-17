<?php
// 快速设置脚本 - 一键完成博客系统数据库设置
session_start();

// 检查是否已经设置过
if (isset($_SESSION['setup_completed']) && $_SESSION['setup_completed']) {
    echo "<h2>✅ 数据库已经设置完成</h2>";
    echo "<p>如果您需要重新设置，请清除浏览器缓存或删除会话。</p>";
    echo "<p><a href='index.php'>返回首页</a></p>";
    exit;
}

// 处理表单提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $step = $_POST['step'] ?? 1;
    
    if ($step == 1) {
        // 步骤1：检查环境
        $errors = [];
        
        // 检查PHP版本
        if (version_compare(PHP_VERSION, '7.4.0', '<')) {
            $errors[] = "PHP版本过低，需要7.4或更高版本，当前版本：" . PHP_VERSION;
        }
        
        // 检查PDO扩展
        if (!extension_loaded('pdo_mysql')) {
            $errors[] = "PDO MySQL扩展未安装";
        }
        
        // 检查数据库连接
        try {
            require_once 'inc/db.php';
            $pdo->query('SELECT 1');
        } catch (Exception $e) {
            $errors[] = "数据库连接失败：" . $e->getMessage();
        }
        
        if (empty($errors)) {
            $_SESSION['step1_passed'] = true;
        }
    } elseif ($step == 2 && isset($_SESSION['step1_passed'])) {
        // 步骤2：创建数据库表
        try {
            require_once 'inc/db.php';
            
            // 执行数据库设置脚本
            require_once 'setup_database.php';
            
            $_SESSION['setup_completed'] = true;
            $success = true;
        } catch (Exception $e) {
            $error = "设置失败：" . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>博客系统快速设置</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .setup-container { max-width: 800px; margin: 50px auto; }
        .step-card { background: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .step-number { width: 40px; height: 40px; border-radius: 50%; background: #007bff; color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; }
        .step-completed { background: #28a745; }
    </style>
</head>
<body>
    <div class="setup-container">
        <div class="text-center mb-4">
            <h1>🚀 博客系统快速设置</h1>
            <p class="text-muted">一键完成数据库配置和初始化</p>
        </div>

        <?php if (isset($success)): ?>
            <div class="alert alert-success">
                <h4>🎉 设置完成！</h4>
                <p>您的博客系统已经成功设置完成。</p>
                <hr>
                <h5>默认管理员账户：</h5>
                <ul class="mb-3">
                    <li><strong>用户名：</strong> admin</li>
                    <li><strong>密码：</strong> admin123</li>
                    <li><strong>邮箱：</strong> admin@example.com</li>
                </ul>
                <div class="d-grid gap-2">
                    <a href="index.php" class="btn btn-primary">访问博客首页</a>
                    <a href="admin/login.php" class="btn btn-outline-primary">进入管理后台</a>
                </div>
            </div>
        <?php elseif (isset($error)): ?>
            <div class="alert alert-danger">
                <h4>❌ 设置失败</h4>
                <p><?= htmlspecialchars($error) ?></p>
                <a href="quick_setup.php" class="btn btn-primary">重新设置</a>
            </div>
        <?php else: ?>
            <div class="step-card p-4">
                <div class="d-flex align-items-center mb-4">
                    <div class="step-number me-3">1</div>
                    <div>
                        <h4 class="mb-1">环境检查</h4>
                        <p class="text-muted mb-0">检查PHP环境和数据库连接</p>
                    </div>
                </div>

                <?php if (isset($errors) && !empty($errors)): ?>
                    <div class="alert alert-danger">
                        <h5>发现以下问题：</h5>
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <form method="post">
                        <input type="hidden" name="step" value="1">
                        <button type="submit" class="btn btn-primary">重新检查</button>
                    </form>
                <?php elseif (isset($_SESSION['step1_passed'])): ?>
                    <div class="alert alert-success">
                        <h5>✅ 环境检查通过</h5>
                        <p class="mb-0">所有必要的组件都已正确安装和配置。</p>
                    </div>
                    
                    <div class="d-flex align-items-center mb-4">
                        <div class="step-number step-completed me-3">2</div>
                        <div>
                            <h4 class="mb-1">创建数据库表</h4>
                            <p class="text-muted mb-0">创建博客系统所需的所有数据表</p>
                        </div>
                    </div>
                    
                    <form method="post">
                        <input type="hidden" name="step" value="2">
                        <button type="submit" class="btn btn-success">开始创建数据库表</button>
                    </form>
                <?php else: ?>
                    <p>点击下面的按钮开始环境检查：</p>
                    <form method="post">
                        <input type="hidden" name="step" value="1">
                        <button type="submit" class="btn btn-primary">开始检查</button>
                    </form>
                <?php endif; ?>
            </div>

            <div class="mt-4 text-center">
                <small class="text-muted">
                    需要帮助？请查看 <a href="README.md">README.md</a> 文件获取详细说明
                </small>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
