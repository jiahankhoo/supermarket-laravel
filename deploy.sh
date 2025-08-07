#!/bin/bash

# 🚀 Laravel 超市系统自动部署脚本
# 使用方法: ./deploy.sh

set -e  # 遇到错误时退出

echo "🚀 开始部署 Laravel 超市系统..."

# 颜色定义
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# 日志函数
log_info() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

log_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

log_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# 检查是否为 root 用户
check_root() {
    if [[ $EUID -eq 0 ]]; then
        log_error "请不要使用 root 用户运行此脚本"
        exit 1
    fi
}

# 检查系统类型
check_system() {
    if [[ -f /etc/os-release ]]; then
        . /etc/os-release
        OS=$NAME
        VER=$VERSION_ID
    else
        log_error "无法检测操作系统"
        exit 1
    fi
    
    log_info "检测到操作系统: $OS $VER"
}

# 安装依赖包
install_dependencies() {
    log_info "安装系统依赖..."
    
    if [[ "$OS" == *"Ubuntu"* ]] || [[ "$OS" == *"Debian"* ]]; then
        sudo apt update
        sudo apt install -y curl wget git unzip software-properties-common
        sudo apt install -y php8.1 php8.1-cli php8.1-fpm php8.1-mysql php8.1-sqlite3 php8.1-mbstring php8.1-xml php8.1-curl php8.1-zip php8.1-gd php8.1-bcmath
    elif [[ "$OS" == *"CentOS"* ]] || [[ "$OS" == *"Red Hat"* ]]; then
        sudo yum update -y
        sudo yum install -y curl wget git unzip epel-release
        sudo yum install -y php php-cli php-fpm php-mysqlnd php-sqlite3 php-mbstring php-xml php-curl php-zip php-gd php-bcmath
    else
        log_error "不支持的操作系统: $OS"
        exit 1
    fi
    
    log_success "系统依赖安装完成"
}

# 安装 Composer
install_composer() {
    log_info "安装 Composer..."
    
    if command -v composer &> /dev/null; then
        log_info "Composer 已安装，版本: $(composer --version)"
        return
    fi
    
    # 下载 Composer 安装程序
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
    
    # 验证安装程序
    EXPECTED_CHECKSUM="$(php -r 'copy("https://composer.github.io/installer.sig", "php://stdout");')"
    ACTUAL_CHECKSUM="$(php -r "echo hash_file('sha384', 'composer-setup.php');")"
    
    if [ "$EXPECTED_CHECKSUM" != "$ACTUAL_CHECKSUM" ]; then
        log_error "Composer 安装程序校验失败"
        rm composer-setup.php
        exit 1
    fi
    
    # 安装 Composer
    php composer-setup.php --quiet
    RESULT=$?
    rm composer-setup.php
    
    # 移动到全局位置
    sudo mv composer.phar /usr/local/bin/composer
    sudo chmod +x /usr/local/bin/composer
    
    log_success "Composer 安装完成"
}

# 创建项目目录
create_project_directory() {
    log_info "创建项目目录..."
    
    PROJECT_DIR="/var/www/supermarket-laravel"
    
    if [ -d "$PROJECT_DIR" ]; then
        log_warning "项目目录已存在: $PROJECT_DIR"
        read -p "是否删除现有目录并重新创建? (y/N): " -n 1 -r
        echo
        if [[ $REPLY =~ ^[Yy]$ ]]; then
            sudo rm -rf "$PROJECT_DIR"
        else
            log_info "使用现有目录"
            return
        fi
    fi
    
    sudo mkdir -p "$PROJECT_DIR"
    sudo chown -R $USER:$USER "$PROJECT_DIR"
    
    log_success "项目目录创建完成: $PROJECT_DIR"
}

# 上传项目文件
upload_project() {
    log_info "准备上传项目文件..."
    
    PROJECT_DIR="/var/www/supermarket-laravel"
    
    # 检查是否有本地项目文件
    if [ -f "composer.json" ]; then
        log_info "检测到本地项目，复制文件到服务器..."
        cp -r . "$PROJECT_DIR/"
        log_success "项目文件复制完成"
    else
        log_warning "未检测到本地项目文件"
        log_info "请手动上传项目文件到: $PROJECT_DIR"
        log_info "或者使用以下命令从 Git 克隆:"
        echo "cd /var/www && git clone <your-repository-url> supermarket-laravel"
        read -p "按回车键继续..."
    fi
}

# 配置项目
configure_project() {
    log_info "配置项目..."
    
    cd /var/www/supermarket-laravel
    
    # 安装依赖
    log_info "安装 Composer 依赖..."
    composer install --no-dev --optimize-autoloader
    
    # 复制环境配置文件
    if [ ! -f ".env" ]; then
        cp .env.example .env
        log_info "已创建 .env 文件，请手动配置数据库连接"
    fi
    
    # 生成应用密钥
    php artisan key:generate
    
    # 设置文件权限
    sudo chmod -R 755 storage bootstrap/cache
    sudo chown -R www-data:www-data storage bootstrap/cache
    
    # 创建存储链接
    php artisan storage:link
    
    log_success "项目配置完成"
}

# 配置数据库
configure_database() {
    log_info "配置数据库..."
    
    cd /var/www/supermarket-laravel
    
    # 询问数据库类型
    echo "请选择数据库类型:"
    echo "1) MySQL/MariaDB"
    echo "2) SQLite"
    read -p "请输入选择 (1/2): " db_choice
    
    case $db_choice in
        1)
            configure_mysql
            ;;
        2)
            configure_sqlite
            ;;
        *)
            log_error "无效选择"
            exit 1
            ;;
    esac
}

# 配置 MySQL
configure_mysql() {
    log_info "配置 MySQL 数据库..."
    
    read -p "请输入数据库名称 (默认: supermarket_laravel): " db_name
    db_name=${db_name:-supermarket_laravel}
    
    read -p "请输入数据库用户名 (默认: supermarket_user): " db_user
    db_user=${db_user:-supermarket_user}
    
    read -s -p "请输入数据库密码: " db_password
    echo
    
    # 创建数据库和用户
    mysql -u root -p -e "
    CREATE DATABASE IF NOT EXISTS $db_name;
    CREATE USER IF NOT EXISTS '$db_user'@'localhost' IDENTIFIED BY '$db_password';
    GRANT ALL PRIVILEGES ON $db_name.* TO '$db_user'@'localhost';
    FLUSH PRIVILEGES;
    "
    
    # 更新 .env 文件
    sed -i "s/DB_CONNECTION=.*/DB_CONNECTION=mysql/" .env
    sed -i "s/DB_DATABASE=.*/DB_DATABASE=$db_name/" .env
    sed -i "s/DB_USERNAME=.*/DB_USERNAME=$db_user/" .env
    sed -i "s/DB_PASSWORD=.*/DB_PASSWORD=$db_password/" .env
    
    log_success "MySQL 数据库配置完成"
}

# 配置 SQLite
configure_sqlite() {
    log_info "配置 SQLite 数据库..."
    
    # 创建 SQLite 数据库文件
    touch database/database.sqlite
    
    # 更新 .env 文件
    sed -i "s/DB_CONNECTION=.*/DB_CONNECTION=sqlite/" .env
    sed -i "s/DB_DATABASE=.*/DB_DATABASE=\/var\/www\/supermarket-laravel\/database\/database.sqlite/" .env
    
    log_success "SQLite 数据库配置完成"
}

# 运行数据库迁移
run_migrations() {
    log_info "运行数据库迁移..."
    
    cd /var/www/supermarket-laravel
    
    # 运行迁移
    php artisan migrate --force
    
    # 询问是否运行数据填充
    read -p "是否运行数据填充? (y/N): " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        php artisan db:seed --force
        log_success "数据填充完成"
    fi
    
    log_success "数据库迁移完成"
}

# 配置 Web 服务器
configure_web_server() {
    log_info "配置 Web 服务器..."
    
    echo "请选择 Web 服务器:"
    echo "1) Apache"
    echo "2) Nginx"
    read -p "请输入选择 (1/2): " web_choice
    
    case $web_choice in
        1)
            configure_apache
            ;;
        2)
            configure_nginx
            ;;
        *)
            log_error "无效选择"
            exit 1
            ;;
    esac
}

# 配置 Apache
configure_apache() {
    log_info "配置 Apache..."
    
    # 安装 Apache
    if [[ "$OS" == *"Ubuntu"* ]] || [[ "$OS" == *"Debian"* ]]; then
        sudo apt install -y apache2
        sudo a2enmod rewrite
    elif [[ "$OS" == *"CentOS"* ]] || [[ "$OS" == *"Red Hat"* ]]; then
        sudo yum install -y httpd
        sudo systemctl enable httpd
    fi
    
    # 创建虚拟主机配置
    sudo tee /etc/apache2/sites-available/supermarket-laravel.conf > /dev/null <<EOF
<VirtualHost *:80>
    ServerName localhost
    ServerAdmin webmaster@localhost
    DocumentRoot /var/www/supermarket-laravel/public

    <Directory /var/www/supermarket-laravel/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog \${APACHE_LOG_DIR}/supermarket-laravel_error.log
    CustomLog \${APACHE_LOG_DIR}/supermarket-laravel_access.log combined
</VirtualHost>
EOF
    
    # 启用站点
    sudo a2ensite supermarket-laravel.conf
    sudo a2dissite 000-default.conf
    sudo systemctl restart apache2
    
    log_success "Apache 配置完成"
}

# 配置 Nginx
configure_nginx() {
    log_info "配置 Nginx..."
    
    # 安装 Nginx
    if [[ "$OS" == *"Ubuntu"* ]] || [[ "$OS" == *"Debian"* ]]; then
        sudo apt install -y nginx
    elif [[ "$OS" == *"CentOS"* ]] || [[ "$OS" == *"Red Hat"* ]]; then
        sudo yum install -y nginx
        sudo systemctl enable nginx
    fi
    
    # 创建 Nginx 配置
    sudo tee /etc/nginx/sites-available/supermarket-laravel > /dev/null <<EOF
server {
    listen 80;
    server_name localhost;
    root /var/www/supermarket-laravel/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php\$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
EOF
    
    # 启用站点
    sudo ln -sf /etc/nginx/sites-available/supermarket-laravel /etc/nginx/sites-enabled/
    sudo rm -f /etc/nginx/sites-enabled/default
    sudo nginx -t
    sudo systemctl restart nginx
    
    log_success "Nginx 配置完成"
}

# 配置防火墙
configure_firewall() {
    log_info "配置防火墙..."
    
    if [[ "$OS" == *"Ubuntu"* ]] || [[ "$OS" == *"Debian"* ]]; then
        sudo ufw allow 22
        sudo ufw allow 80
        sudo ufw allow 443
        sudo ufw --force enable
    elif [[ "$OS" == *"CentOS"* ]] || [[ "$OS" == *"Red Hat"* ]]; then
        sudo firewall-cmd --permanent --add-service=ssh
        sudo firewall-cmd --permanent --add-service=http
        sudo firewall-cmd --permanent --add-service=https
        sudo firewall-cmd --reload
    fi
    
    log_success "防火墙配置完成"
}

# 清除缓存
clear_cache() {
    log_info "清除应用缓存..."
    
    cd /var/www/supermarket-laravel
    
    php artisan config:clear
    php artisan cache:clear
    php artisan view:clear
    php artisan route:clear
    
    log_success "缓存清除完成"
}

# 测试部署
test_deployment() {
    log_info "测试部署..."
    
    cd /var/www/supermarket-laravel
    
    # 检查 PHP 版本
    php --version
    
    # 检查 Composer
    composer --version
    
    # 检查 Laravel
    php artisan --version
    
    # 测试数据库连接
    php artisan tinker --execute="echo 'Database connection: ' . (DB::connection()->getPdo() ? 'OK' : 'FAILED') . PHP_EOL;"
    
    log_success "部署测试完成"
}

# 显示部署信息
show_deployment_info() {
    log_success "🎉 部署完成！"
    echo
    echo "📋 部署信息:"
    echo "   项目目录: /var/www/supermarket-laravel"
    echo "   访问地址: http://$(hostname -I | awk '{print $1}')"
    echo "   配置文件: /var/www/supermarket-laravel/.env"
    echo
    echo "🔧 常用命令:"
    echo "   查看日志: tail -f /var/www/supermarket-laravel/storage/logs/laravel.log"
    echo "   清除缓存: cd /var/www/supermarket-laravel && php artisan cache:clear"
    echo "   重启服务: sudo systemctl restart apache2 (或 nginx)"
    echo
    echo "⚠️  重要提醒:"
    echo "   1. 请确保 .env 文件中的数据库配置正确"
    echo "   2. 生产环境请设置 APP_DEBUG=false"
    echo "   3. 建议配置 SSL 证书"
    echo "   4. 定期备份数据库"
}

# 主函数
main() {
    echo "🚀 Laravel 超市系统自动部署脚本"
    echo "=================================="
    echo
    
    check_root
    check_system
    install_dependencies
    install_composer
    create_project_directory
    upload_project
    configure_project
    configure_database
    run_migrations
    configure_web_server
    configure_firewall
    clear_cache
    test_deployment
    show_deployment_info
}

# 运行主函数
main "$@" 