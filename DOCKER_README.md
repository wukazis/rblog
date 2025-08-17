# 博客系统Docker部署指南

## 🐳 系统架构

本博客系统使用Docker容器化部署，包含以下服务：

- **PHP应用** (Apache + PHP 8.1)
- **MySQL数据库** (MySQL 8.0)
- **Nginx反向代理** (可选)
- **phpMyAdmin** (数据库管理)

## 📋 系统要求

- Docker 20.10+
- Docker Compose 2.0+
- 至少2GB可用内存
- 至少10GB可用磁盘空间

## 🚀 快速开始

### 1. 克隆项目
```bash
git clone <your-repo-url>
cd blog
```

### 2. 一键部署
```bash
# 给脚本执行权限
chmod +x docker-setup.sh

# 运行设置脚本
./docker-setup.sh
```

### 3. 手动部署
```bash
# 构建镜像
docker-compose build

# 启动服务
docker-compose up -d

# 查看服务状态
docker-compose ps
```

## 🌐 访问地址

部署完成后，可以通过以下地址访问：

- **博客首页**: http://localhost 或 http://localhost:8080
- **phpMyAdmin**: http://localhost:8081
- **数据库**: localhost:3306

## 🔧 环境配置

### 开发环境
```bash
# 使用开发配置
docker-compose up -d

# 查看日志
docker-compose logs -f app
```

### 生产环境
```bash
# 使用生产配置
docker-compose -f docker-compose.prod.yml up -d

# 使用环境变量
cp env.example .env
# 编辑.env文件
docker-compose -f docker-compose.prod.yml --env-file .env up -d
```

## 📁 文件结构

```
blog/
├── Dockerfile                 # PHP应用镜像
├── docker-compose.yml         # 开发环境配置
├── docker-compose.prod.yml    # 生产环境配置
├── docker-compose.override.yml # 开发环境覆盖
├── nginx.conf                 # Nginx配置
├── nginx.prod.conf           # 生产环境Nginx配置
├── docker-setup.sh           # 一键设置脚本
├── env.example               # 环境变量示例
├── .dockerignore             # Docker忽略文件
└── DOCKER_README.md          # 本文档
```

## 🔒 安全配置

### 1. 修改默认密码
```bash
# 进入容器
docker-compose exec app bash

# 修改管理员密码
php -r "
require_once 'inc/db.php';
\$stmt = \$pdo->prepare('UPDATE users SET password = ? WHERE username = ?');
\$stmt->execute([password_hash('your_new_password', PASSWORD_DEFAULT), 'admin']);
echo '密码已更新';
"
```

### 2. 配置SSL证书
```bash
# 创建SSL目录
mkdir -p ssl

# 复制证书文件
cp your-cert.pem ssl/cert.pem
cp your-key.pem ssl/key.pem

# 使用生产配置
docker-compose -f docker-compose.prod.yml up -d
```

### 3. 环境变量安全
```bash
# 编辑环境变量
cp env.example .env
nano .env

# 重要变量
MYSQL_ROOT_PASSWORD=your_secure_password
MYSQL_PASSWORD=your_secure_password
ADMIN_PASSWORD=your_secure_password
```

## 📊 监控和维护

### 查看服务状态
```bash
# 查看所有容器状态
docker-compose ps

# 查看资源使用
docker stats
```

### 查看日志
```bash
# 查看所有服务日志
docker-compose logs

# 查看特定服务日志
docker-compose logs app
docker-compose logs mysql

# 实时查看日志
docker-compose logs -f
```

### 备份数据库
```bash
# 备份
docker-compose exec mysql mysqldump -u wukazi -p blog > backup.sql

# 恢复
docker-compose exec -T mysql mysql -u wukazi -p blog < backup.sql
```

## 🔄 更新和升级

### 更新应用
```bash
# 拉取最新代码
git pull

# 重新构建镜像
docker-compose build --no-cache

# 重启服务
docker-compose down
docker-compose up -d
```

### 更新数据库
```bash
# 进入容器
docker-compose exec app bash

# 运行数据库更新脚本
php setup_database.php
```

## 🚨 故障排除

### 常见问题

1. **端口冲突**
   ```bash
   # 检查端口占用
   netstat -tulpn | grep :8080
   
   # 修改端口映射
   # 编辑docker-compose.yml中的ports配置
   ```

2. **数据库连接失败**
   ```bash
   # 检查数据库容器状态
   docker-compose ps mysql
   
   # 查看数据库日志
   docker-compose logs mysql
   
   # 重启数据库
   docker-compose restart mysql
   ```

3. **权限问题**
   ```bash
   # 修复文件权限
   docker-compose exec app chown -R www-data:www-data /var/www/html
   ```

4. **内存不足**
   ```bash
   # 查看内存使用
   docker stats
   
   # 限制容器内存
   # 在docker-compose.yml中添加resources配置
   ```

### 重置环境
```bash
# 停止所有服务
docker-compose down

# 删除所有数据
docker-compose down -v
docker system prune -a

# 重新开始
./docker-setup.sh
```

## 📈 性能优化

### 1. 资源限制
```yaml
# 在docker-compose.yml中添加
services:
  app:
    deploy:
      resources:
        limits:
          memory: 512M
          cpus: '0.5'
```

### 2. 缓存配置
```yaml
# 使用Redis缓存
redis:
  image: redis:alpine
  ports:
    - "6379:6379"
```

### 3. 负载均衡
```yaml
# 多实例部署
services:
  app:
    deploy:
      replicas: 3
```

## 🔗 相关链接

- [Docker官方文档](https://docs.docker.com/)
- [Docker Compose文档](https://docs.docker.com/compose/)
- [MySQL Docker镜像](https://hub.docker.com/_/mysql)
- [PHP Docker镜像](https://hub.docker.com/_/php)

## 📞 支持

如果遇到问题：

1. 查看日志文件
2. 检查Docker服务状态
3. 验证网络连接
4. 参考故障排除部分

---

**注意**: 生产环境部署前请务必：
- 修改所有默认密码
- 配置SSL证书
- 设置防火墙规则
- 配置定期备份
- 监控系统资源
