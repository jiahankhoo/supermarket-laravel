# 🚀 Laravel 超市系统服务器部署指南

## 📋 部署前准备

### 1. 服务器环境要求
- PHP >= 8.1
- MySQL/MariaDB 或 SQLite
- Web 服务器 (Apache/Nginx)
- Composer
- Git

### 2. 本地准备
确保你的本地项目已经完成以下步骤：
```bash
# 1. 更新依赖
composer update

# 2. 生成生产环境配置
composer install --no-dev --optimize-autoloader

# 3. 清除缓存
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

## 🔧 服务器端安装步骤

### 步骤 1: 安装 Composer
```bash
# Ubuntu/Debian
sudo apt update
sudo apt install composer

# CentOS/RHEL
sudo yum install composer

# 验证安装
composer --version
```

### 步骤 2: 安装 PHP 和扩展
```bash
# Ubuntu/Debian
sudo apt install php8.1 php8.1-cli php8.1-fpm php8.1-mysql php8.1-sqlite3 php8.1-mbstring php8.1-xml php8.1-curl php8.1-zip php8.1-gd

# CentOS/RHEL
sudo yum install php php-cli php-fpm php-mysqlnd php-sqlite3 php-mbstring php-xml php-curl php-zip php-gd
```

### 步骤 3: 创建项目目录
```bash
# 创建网站目录
sudo mkdir -p /var/www/supermarket-laravel
sudo chown -R $USER:$USER /var/www/supermarket-laravel
```

## 📤 项目上传方法

### 方法 1: 使用 Git (推荐)
```bash
# 在服务器上克隆项目
cd /var/www
git clone https://github.com/your-username/supermarket-laravel.git
cd supermarket-laravel
```

### 方法 2: 使用 SCP/SFTP
```bash
# 从本地压缩项目
tar -czf supermarket-laravel.tar.gz --exclude=node_modules --exclude=vendor --exclude=.git supermarket-laravel/

# 上传到服务器
scp supermarket-laravel.tar.gz user@your-server:/tmp/

# 在服务器上解压
cd /var/www
sudo tar -xzf /tmp/supermarket-laravel.tar.gz
sudo chown -R $USER:$USER supermarket-laravel
```

### 方法 3: 使用 rsync
```bash
rsync -avz --exclude=node_modules --exclude=vendor --exclude=.git supermarket-laravel/ user@your-server:/var/www/supermarket-laravel/
```

## ⚙️ 项目配置

### 步骤 1: 安装依赖
```bash
cd /var/www/supermarket-laravel

# 安装生产环境依赖
composer install --no-dev --optimize-autoloader
```

### 步骤 2: 环境配置
```bash
# 复制环境配置文件
cp .env.example .env

# 编辑配置文件
nano .env
```

### 步骤 3: 配置 .env 文件
```env
APP_NAME="Laravel 超市系统"
APP_ENV=production
APP_KEY=base64:your-generated-key
APP_DEBUG=false
APP_URL=http://your-domain.com

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

# 数据库配置
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=supermarket_laravel
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

# 或者使用 SQLite
# DB_CONNECTION=sqlite
# DB_DATABASE=/var/www/supermarket-laravel/database/database.sqlite

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120
```

### 步骤 4: 生成应用密钥
```bash
php artisan key:generate
```

### 步骤 5: 数据库设置
```bash
# 创建数据库 (MySQL)
mysql -u root -p
CREATE DATABASE supermarket_laravel;
CREATE USER 'supermarket_user'@'localhost' IDENTIFIED BY 'your_password';
GRANT ALL PRIVILEGES ON supermarket_laravel.* TO 'supermarket_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# 或者创建 SQLite 数据库
touch database/database.sqlite
```

### 步骤 6: 运行迁移和填充
```bash
# 运行数据库迁移
php artisan migrate

# 运行数据填充 (可选)
php artisan db:seed
```

### 步骤 7: 设置文件权限
```bash
# 设置存储目录权限
sudo chmod -R 755 storage bootstrap/cache
sudo chown -R www-data:www-data storage bootstrap/cache

# 创建存储链接
php artisan storage:link
```

### 步骤 8: 清除缓存
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

## 🌐 Web 服务器配置

### Apache 配置
```bash
# 创建虚拟主机配置
sudo nano /etc/apache2/sites-available/supermarket-laravel.conf
```

```apache
<VirtualHost *:80>
    ServerName your-domain.com
    ServerAdmin webmaster@your-domain.com
    DocumentRoot /var/www/supermarket-laravel/public

    <Directory /var/www/supermarket-laravel/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/supermarket-laravel_error.log
    CustomLog ${APACHE_LOG_DIR}/supermarket-laravel_access.log combined
</VirtualHost>
```

```bash
# 启用站点和模块
sudo a2ensite supermarket-laravel.conf
sudo a2enmod rewrite
sudo systemctl restart apache2
```

### Nginx 配置
```bash
# 创建 Nginx 配置
sudo nano /etc/nginx/sites-available/supermarket-laravel
```

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/supermarket-laravel/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

```bash
# 启用站点
sudo ln -s /etc/nginx/sites-available/supermarket-laravel /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

## 🔒 安全配置

### 设置防火墙
```bash
# Ubuntu/Debian
sudo ufw allow 22
sudo ufw allow 80
sudo ufw allow 443
sudo ufw enable

# CentOS/RHEL
sudo firewall-cmd --permanent --add-service=ssh
sudo firewall-cmd --permanent --add-service=http
sudo firewall-cmd --permanent --add-service=https
sudo firewall-cmd --reload
```

### SSL 证书 (可选)
```bash
# 安装 Certbot
sudo apt install certbot python3-certbot-apache

# 获取 SSL 证书
sudo certbot --apache -d your-domain.com
```

## 🧪 测试部署

### 检查应用状态
```bash
# 检查 PHP 版本
php --version

# 检查 Composer
composer --version

# 检查 Laravel
php artisan --version

# 测试数据库连接
php artisan tinker
>>> DB::connection()->getPdo();
```

### 访问网站
在浏览器中访问 `http://your-domain.com` 或 `http://your-server-ip`

## 📝 部署后维护

### 定期更新
```bash
# 更新代码
git pull origin main

# 更新依赖
composer install --no-dev --optimize-autoloader

# 运行迁移
php artisan migrate

# 清除缓存
php artisan config:clear
php artisan cache:clear
```

### 监控日志
```bash
# 查看 Laravel 日志
tail -f storage/logs/laravel.log

# 查看 Web 服务器日志
tail -f /var/log/apache2/supermarket-laravel_error.log
# 或
tail -f /var/log/nginx/supermarket-laravel_error.log
```

## 🆘 常见问题解决

### 问题 1: 权限错误
```bash
sudo chown -R www-data:www-data /var/www/supermarket-laravel
sudo chmod -R 755 /var/www/supermarket-laravel
```

### 问题 2: 500 错误
```bash
# 检查日志
tail -f storage/logs/laravel.log

# 重新生成配置缓存
php artisan config:cache
```

### 问题 3: 数据库连接错误
```bash
# 测试数据库连接
php artisan tinker
>>> DB::connection()->getPdo();
```

## 📞 技术支持

如果遇到问题，请检查：
1. 服务器错误日志
2. Laravel 应用日志
3. 数据库连接状态
4. 文件权限设置
5. Web 服务器配置

---

**部署完成后，你的 Laravel 超市系统就可以在服务器上正常运行了！** 🎉 