# 博客系统Docker管理Makefile

.PHONY: help build up down restart logs clean setup prod backup restore

# 默认目标
help:
	@echo "博客系统Docker管理命令："
	@echo ""
	@echo "   build    构建Docker镜像"
	@echo "   up       启动所有服务"
	@echo "   down     停止所有服务"
	@echo "   restart  重启所有服务"
	@echo "   logs     查看服务日志"
	@echo "   clean    清理所有容器和数据"
	@echo "   setup    一键设置（构建+启动+初始化）"
	@echo "   prod     生产环境部署"
	@echo "   backup   备份数据库"
	@echo "   restore  恢复数据库"
	@echo "   shell    进入应用容器"
	@echo "   status   查看服务状态"

# 构建镜像
build:
	@echo "🔨 构建Docker镜像..."
	docker-compose build

# 启动服务
up:
	@echo "🚀 启动服务..."
	docker-compose up -d

# 停止服务
down:
	@echo "🛑 停止服务..."
	docker-compose down

# 重启服务
restart:
	@echo "🔄 重启服务..."
	docker-compose restart

# 查看日志
logs:
	@echo "📋 查看服务日志..."
	docker-compose logs -f

# 清理环境
clean:
	@echo "🧹 清理所有容器和数据..."
	docker-compose down -v
	docker system prune -a -f

# 一键设置
setup: build up
	@echo "⏳ 等待服务启动..."
	@sleep 30
	@echo "🔍 检查服务状态..."
	docker-compose ps
	@echo "🎉 设置完成！"
	@echo "📱 访问地址："
	@echo "   - 博客首页: http://localhost:8080"
	@echo "   - phpMyAdmin: http://localhost:8081"

# 生产环境部署
prod:
	@echo "🚀 生产环境部署..."
	docker-compose -f docker-compose.prod.yml up -d
	@echo "📱 生产环境访问地址："
	@echo "   - 博客首页: https://localhost"
	@echo "   - HTTP重定向: http://localhost"

# 备份数据库
backup:
	@echo "💾 备份数据库..."
	@mkdir -p backups
	docker-compose exec mysql mysqldump -u wukazi -p rewink123 blog > backups/backup_$(shell date +%Y%m%d_%H%M%S).sql
	@echo "✅ 备份完成"

# 恢复数据库
restore:
	@echo "📥 恢复数据库..."
	@if [ -z "$(file)" ]; then \
		echo "❌ 请指定备份文件: make restore file=backups/backup_20240101_120000.sql"; \
		exit 1; \
	fi
	docker-compose exec -T mysql mysql -u wukazi -p rewink123 blog < $(file)
	@echo "✅ 恢复完成"

# 进入应用容器
shell:
	@echo "🐚 进入应用容器..."
	docker-compose exec app bash

# 查看服务状态
status:
	@echo "📊 服务状态："
	docker-compose ps
	@echo ""
	@echo "📈 资源使用："
	docker stats --no-stream

# 开发环境
dev: up
	@echo "🔧 开发环境已启动"
	@echo "📱 访问地址：http://localhost:8080"

# 测试环境
test: build
	@echo "🧪 测试环境构建完成"
	@echo "运行测试：docker-compose exec app php -l index.php"

# 部署到生产环境
deploy: prod
	@echo "🚀 生产环境部署完成"
	@echo "📱 访问地址：https://localhost"

# 监控
monitor:
	@echo "📊 实时监控："
	docker stats

# 更新
update:
	@echo "🔄 更新系统..."
	git pull
	docker-compose build --no-cache
	docker-compose up -d
	@echo "✅ 更新完成"
