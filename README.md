# ServerJars - Minecraft Server Jar Repository

> [!WARNING]  
> The code in this repository will not populate/update the database, please use [the updater](https://github.com/mc-ServerJars/updater).

<img width="2560" height="1305" alt="image" src="https://github.com/user-attachments/assets/8219d592-8157-48e4-85df-1b278658e02a" />

ServerJars is an open-source repository for Minecraft server jars, providing an easy way to discover and download server software for various Minecraft forks.

## Features

- 🚀 Modern, responsive UI with gaming-inspired design
- 🔍 Powerful search functionality
- 📦 Comprehensive API for programmatic access
- 📊 Real-time statistics and metrics

## Prerequisites

Before installing ServerJars, ensure you have:

- PHP 8.0 or higher
- MySQL 5.7+ or MariaDB 10.3+
- Apache web server with mod_rewrite enabled
- Composer (for dependency management)
- Node.JS to run [the updater](https://github.com/mc-ServerJars/updater) and populate the database

## Installation

### 1. Clone the repository

```bash
git clone https://github.com/mc-ServerJars/serverjars.git
cd serverjars
```

### 2. Install dependencies

```bash
composer install
```

### 3. Configure environment

Create a `config.php` file by copying the sample:

```bash
cp config.sample.php config.php
```

Edit `config.php` with your database credentials:

```php
<?php
$dbHost = 'localhost';      // Database host
$dbName = 'serverjars_db';  // Database name
$dbUsername = 'db_user';    // Database username
$dbPassword = 'secure_pass';// Database password
```

### 4. Set up the database

Import the SQL schema:

```bash
mysql -u [username] -p [database_name] < database/schema.sql
```

## Usage

After installation, you can:

1. Access the web interface at `http://your-server-address`
2. Use the search functionality to find server jars
3. Browse different Minecraft forks (Paper, Purpur, Fabric, etc.)
4. Access the API documentation at `/docs`

## API Documentation

ServerJars provides a comprehensive API for programmatic access:

### Endpoints
- `GET /api/v1/builds` - Retrieve builds with filters
- `GET /api/v1/stats` - Get service statistics
- `GET /api/v1/search` - Search for specific builds

Full api docs available in templates/docs.twig, or /docs on your own instance.

## Updating

To update to the latest version:

```bash
git pull origin main
composer install
```

## Contributing

We welcome contributions! Please follow these steps:

1. Fork the repository
2. Create a new branch (`git checkout -b feature/your-feature`)
3. Commit your changes (`git commit -am 'Add some feature'`)
4. Push to the branch (`git push origin feature/your-feature`)
5. Open a pull request

## License

ServerJars is open-source software licensed under the [Apache 2.0 License](LICENSE).

## Support

If you need help setting up ServerJars, please [open an issue](https://github.com/mc-ServerJars/serverjars/issues).

---

**ServerJars** is not affiliated with Minecraft, Microsoft, Mojang, PaperMC, SpigotMC or PurpurMC.
