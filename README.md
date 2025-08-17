# 博客系统部署指南

## 🚀 快速部署

### Docker部署（推荐）

```bash
# 一键部署
chmod +x docker-setup.sh
./docker-setup.sh

# 或使用Makefile
make setup
```

### 传统部署

## 系统要求

- PHP 7.4 或更高版本
- MySQL 5.7 或更高版本
- PDO MySQL 扩展
- Web服务器 (Apache/Nginx)

**或者使用Docker（推荐）**：
- Docker 20.10+
- Docker Compose 2.0+

## 数据库配置

### 1. 创建数据库

首先在MySQL中创建数据库：

```sql
CREATE DATABASE blog CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 2. 创建用户并授权

```sql
CREATE USER 'wukazi'@'localhost' IDENTIFIED BY 'rewink';
GRANT ALL PRIVILEGES ON blog.* TO 'wukazi'@'localhost';
FLUSH PRIVILEGES;
```

### 3. 配置文件说明

数据库配置文件位于 `inc/db.php`：

```php
$host = 'localhost';     // 数据库主机
$db   = 'blog';         // 数据库名称
$user = 'wukazi';       // 用户名
$pass = 'rewink';       // 密码
$charset = 'utf8mb4';   // 字符集
```

## 设置步骤

### Docker部署（推荐）

1. **一键部署**
   ```bash
   ./docker-setup.sh
   ```

2. **手动部署**
   ```bash
   # 构建镜像
   docker-compose build
   
   # 启动服务
   docker-compose up -d
   
   # 查看状态
   docker-compose ps
   ```

3. **访问应用**
   - 博客首页: http://localhost:8080
   - phpMyAdmin: http://localhost:8081

### 传统部署

#### 步骤1：检查数据库连接

在浏览器中访问：
```
http://your-domain/check_database.php
```

这将检查：
- PHP PDO扩展是否安装
- 数据库连接是否成功
- 数据库表是否存在
- MySQL版本信息

#### 步骤2：创建数据库表

在浏览器中访问：
```
http://your-domain/setup_database.php
```

这个脚本将自动创建以下表：
- `users` - 用户表
- `posts` - 文章表
- `categories` - 分类表
- `post_categories` - 文章分类关联表
- `tags` - 标签表
- `post_tags` - 文章标签关联表
- `comments` - 评论表

#### 步骤3：验证设置

设置完成后，访问主页：
```
http://your-domain/index.php
```

## 数据库表结构

### users 表
- `id` - 用户ID (主键)
- `username` - 用户名 (唯一)
- `password` - 密码 (加密)
- `email` - 邮箱 (唯一)
- `created_at` - 创建时间
- `updated_at` - 更新时间

### posts 表
- `id` - 文章ID (主键)
- `title` - 文章标题
- `content` - 文章内容
- `excerpt` - 文章摘要
- `author_id` - 作者ID (外键)
- `status` - 状态 (draft/published)
- `created_at` - 创建时间
- `updated_at` - 更新时间

### categories 表
- `id` - 分类ID (主键)
- `name` - 分类名称
- `slug` - 分类别名 (唯一)
- `description` - 分类描述
- `created_at` - 创建时间

### tags 表
- `id` - 标签ID (主键)
- `name` - 标签名称
- `slug` - 标签别名 (唯一)
- `created_at` - 创建时间

### comments 表
- `id` - 评论ID (主键)
- `post_id` - 文章ID (外键)
- `author_name` - 评论者姓名
- `author_email` - 评论者邮箱
- `content` - 评论内容
- `status` - 状态 (pending/approved/spam)
- `created_at` - 创建时间

## 默认账户

设置完成后，系统会自动创建默认管理员账户：

- **用户名**: admin
- **密码**: admin123
- **邮箱**: admin@example.com

**重要提示**: 请在生产环境中立即修改默认密码！

## 故障排除

### 常见问题

1. **连接被拒绝**
   - 检查MySQL服务是否运行
   - 验证主机地址和端口

2. **访问被拒绝**
   - 检查用户名和密码是否正确
   - 确认用户是否有数据库访问权限

3. **表不存在**
   - 运行 `setup_database.php` 创建表
   - 检查数据库名称是否正确

4. **字符集问题**
   - 确保数据库使用 `utf8mb4` 字符集
   - 检查PHP文件是否保存为UTF-8编码

### 安全建议

1. 修改默认管理员密码
2. 使用强密码
3. 定期备份数据库
4. 限制数据库用户权限
5. 启用SSL连接（生产环境）

## 备份和恢复

### 备份数据库
```bash
mysqldump -u wukazi -p blog > blog_backup.sql
```

### 恢复数据库
```bash
mysql -u wukazi -p blog < blog_backup.sql
```

## 🐳 Docker管理

### 常用命令

```bash
# 查看帮助
make help

# 启动服务
make up

# 停止服务
make down

# 重启服务
make restart

# 查看日志
make logs

# 进入容器
make shell

# 备份数据库
make backup

# 恢复数据库
make restore file=backups/backup_20240101_120000.sql
```

### 环境配置

- **开发环境**: `docker-compose.dev.yml`
- **生产环境**: `docker-compose.prod.yml`
- **详细说明**: 查看 `DOCKER_README.md`

## 联系支持

如果遇到问题，请检查：
1. 错误日志文件
2. MySQL错误日志
3. PHP错误日志
4. Docker容器日志: `docker-compose logs`

---

**注意**: 这是一个基础的博客系统，建议在生产环境中添加更多的安全措施和功能。
