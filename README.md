# Laravel 超市管理系统

一个基于Laravel的超市管理系统，支持管理员和普通用户两种角色。

## 功能特性

### 管理员功能
- 商品管理（增删改查）
- 订单管理
- 销售统计
- 库存管理

### 普通用户功能
- 浏览商品
- 搜索商品
- 添加商品到购物车
- 下单购买
- 查看订单历史

## 安装和运行

1. 安装依赖：
```bash
composer install
```

2. 复制环境配置文件：
```bash
cp .env.example .env
```

3. 生成应用密钥：
```bash
php artisan key:generate
```

4. 配置数据库（在.env文件中）：
```env
DB_CONNECTION=sqlite
DB_DATABASE=/path/to/your/database.sqlite
```

5. 运行数据库迁移：
```bash
php artisan migrate
```

6. 运行数据库种子：
```bash
php artisan db:seed
```

7. 启动开发服务器：
```bash
php artisan serve
```

8. 访问系统：
打开浏览器访问 `http://localhost:8000`

## 默认账户

系统会自动创建默认管理员账户：
- 邮箱：`admin@supermarket.com`
- 密码：`admin123`
- 角色：管理员

**请及时修改默认密码！**

## 角色说明

### 管理员 (admin)
- 可以管理所有商品
- 可以查看所有订单
- 可以添加、编辑、删除商品
- 可以管理库存

### 普通用户 (user)
- 可以浏览和搜索商品
- 可以添加商品到购物车
- 可以下单购买
- 可以查看自己的订单历史

## 数据库结构

- `users`: 用户表（管理员和普通用户）
- `products`: 商品表
- `cart_items`: 购物车项目表
- `orders`: 订单表
- `order_items`: 订单项目表

## 技术栈

- **后端**: Laravel 10
- **前端**: Blade模板 + Bootstrap
- **数据库**: SQLite/MySQL
- **认证**: Laravel内置认证

## 文件结构

```
supermarket-laravel/
├── app/
│   ├── Http/Controllers/     # 控制器
│   ├── Models/              # 模型
│   └── Http/Middleware/     # 中间件
├── database/
│   ├── migrations/          # 数据库迁移
│   └── seeders/            # 数据库种子
├── resources/
│   └── views/              # Blade视图
├── routes/
│   └── web.php             # Web路由
└── README.md               # 说明文档
```

## 使用说明

1. **注册新用户**：访问注册页面，选择角色（管理员或普通用户）
2. **管理员登录**：使用默认账户或注册的管理员账户登录
3. **添加商品**：管理员可以在管理面板中添加新商品
4. **用户购物**：普通用户可以浏览商品并添加到购物车
5. **下单购买**：用户可以在购物车中确认订单
6. **订单管理**：管理员可以查看和处理所有订单

## 注意事项

- 首次运行时会自动创建数据库和默认管理员账户
- 请及时修改默认管理员密码
- 系统默认使用SQLite数据库，也可以配置为MySQL
- 建议在生产环境中使用更安全的配置
