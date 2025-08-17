#!/bin/bash

# 博客系统Docker设置脚本

echo "🚀 博客系统Docker设置开始..."

# 检查Docker是否安装
if ! command -v docker &> /dev/null; then
    echo "❌ Docker未安装，请先安装Docker"
    exit 1
fi

# 检查Docker Compose是否安装
if ! command -v docker-compose &> /dev/null; then
    echo "❌ Docker Compose未安装，请先安装Docker Compose"
    exit 1
fi

# 创建日志目录
mkdir -p logs

# 创建SSL目录（用于生产环境）
mkdir -p ssl

# 复制环境变量文件
if [ ! -f .env ]; then
    cp env.example .env
    echo "✅ 已创建.env文件，请根据需要修改配置"
fi

# 构建并启动容器
echo "🔨 构建Docker镜像..."
docker-compose build

echo "🚀 启动服务..."
docker-compose up -d

# 等待服务启动
echo "⏳ 等待服务启动..."
sleep 30

# 检查服务状态
echo "📊 检查服务状态..."
docker-compose ps

# 检查数据库连接
echo "🔍 检查数据库连接..."
docker-compose exec app php check_database.php

echo ""
echo "🎉 Docker设置完成！"
echo ""
echo "📱 访问地址："
echo "   - 博客首页: http://localhost"
echo "   - 直接访问: http://localhost:8080"
echo "   - phpMyAdmin: http://localhost:8081"
echo ""
echo "🔧 管理命令："
echo "   - 查看日志: docker-compose logs"
echo "   - 停止服务: docker-compose down"
echo "   - 重启服务: docker-compose restart"
echo "   - 进入容器: docker-compose exec app bash"
echo ""
echo "⚠️  重要提醒："
echo "   - 请修改默认管理员密码"
echo "   - 生产环境请使用docker-compose.prod.yml"
echo "   - 请配置SSL证书用于HTTPS"
