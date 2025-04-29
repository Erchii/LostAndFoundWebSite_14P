# Lost and Found Portal

A comprehensive web application for reporting and searching for lost and found items. Users can create accounts, post lost or found items, search for matches, and connect with each other to recover lost belongings.

## Features

- **User Management**: Registration, login, profile management
- **Item Management**: Post lost/found items with details and images
- **Search System**: Advanced search with filters for effective item matching
- **Admin Panel**: Verify items, manage users, view statistics
- **Notification System**: Real-time updates for users
- **Responsive Design**: Mobile-friendly interface

## Technology Stack

- **Backend**: PHP
- **Database**: MySQL
- **Frontend**: HTML, CSS, JavaScript
- **CSS Framework**: Bootstrap 4
- **Icons**: Font Awesome
- **Containerization**: Docker

## Installation

### Prerequisites

- Docker and Docker Compose installed on your system

### Setup Instructions

1. **Clone the repository**

```bash
git clone <repository-url>
cd lost-and-found-portal
```

2. **Configure environment variables**

Review and update the `.env` file with your preferred settings:

```
# Database Configuration
DB_HOST=db
DB_PORT=3306
DB_DATABASE=lost_found_db
DB_USERNAME=lost_found_user
DB_PASSWORD=secret_password
DB_ROOT_PASSWORD=root_password

# Application Settings
APP_NAME="Lost and Found Portal"
APP_URL=http://localhost:8080
APP_DEBUG=true
```

3. **Build and start the Docker containers**

```bash
docker-compose up -d
```

4. **Access the application**

Open your browser and navigate to:
- Web Application: http://localhost:8080
- PHPMyAdmin: http://localhost:8081 (for database management)

## Usage

### User Roles

1. **Regular Users**
   - Create an account
   - Report lost or found items
   - Search for matching items
   - Contact other users about items
   - Manage their own items

2. **Administrators**
   - All regular user capabilities
   - Verify/reject submitted items
   - Manage all users and items
   - View dashboard statistics

### Default Admin Account

- **Username**: admin
- **Password**: password
- **Email**: admin@example.com

## Project Structure

```
lost-and-found-portal/
├── .env                     # Environment variables
├── docker-compose.yml       # Docker compose configuration
├── Dockerfile               # Docker configuration for PHP application
├── src/                     # Application source code
│   ├── config/              # Configuration files
│   ├── controllers/         # Controller files
│   ├── models/              # Model files
│   ├── views/               # View files
│   ├── assets/              # Static assets (CSS, JS, images)
│   ├── utils/               # Utility functions
│   └── index.php            # Main entry point
├── database/                # Database setup
│   └── init.sql             # Initial database schema and data
└── README.md                # Project documentation
```

## Development

To make changes to the project:

1. The source code is in the `src` directory
2. Database schema is in `database/init.sql`
3. After making changes, rebuild the containers:

```bash
docker-compose down
docker-compose up -d --build
```

## Customization

- Application settings can be modified in `src/config/config.php`
- Styling can be customized in `src/assets/css/`
- Database schema can be modified in `database/init.sql`

## License

[MIT License](LICENSE)

## Credits

Developed as a project for lost and found item management.

## Support

For support, please open an issue in the repository.