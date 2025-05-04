# Lost and Found Portal

Introduction
Lost and Found Portal is a web application designed to simplify the process of reporting and retrieving lost items. Users can post lost or found item listings, search for matches, and contact each other for item returns.



Problem Statement
In university campuses and public areas, personal belongings are frequently lost, causing stress and inconvenience. The absence of a centralized system for reporting lost and found items makes it difficult to return them to their owners.

Objectives
- Create a user-friendly platform for posting and searching lost/found items.
- Provide an efficient filtering system for quick match discovery.
- Offer an admin panel for content moderation and user management.

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
- **Frontend**: HTML, CSS, JavaScript(Bootstrap 4, Font Awesome)
- **Containerization**: Docker

## Installation

### Prerequisites

- Docker and Docker Compose installed on your system

### Setup Instructions

1. **Clone the repository**

```bash
git clone https://github.com/Erchii/LostAndFoundWebSite_14P
```

```bash
cd LostAndFoundWebSite_14P
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



### Testing
There are currently no automated tests. Manual testing is recommended by using the application as described above.

### Known Issues / Limitations
- No automated tests.
- Limited mobile support.
- No integration with external notification services.



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




### Team Members
Yerassyl Issayev, 220103164, 14-p
Ismailov Nurbakh, 220103264, 14-p
Bakyt Gulnar, 220103099, 13-p
Nazarov Darkhan, 220103319, 13-p
Zhailaubayev Yrysdaulet, 220103081, 13-p

