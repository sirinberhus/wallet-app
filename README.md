# Laravel Stateless Wallet Service

This project is a stateless backend system for a wallet service, built with Laravel. It features JWT-based authentication for two distinct user roles: Players and Backoffice Agents. The application is fully containerized using Docker for easy setup and development.

## 🧩 Table of Contents

- [Features](#-features)
- [Technologies Used](#-technologies-used)
- [System Requirements](#-system-requirements)
- [Installation and Setup](#-installation-and-setup)
- [API Endpoints](#-api-endpoints)
  - [Player API](#-player-api)
  - [Backoffice API](#-backoffice-api)
- [Authentication](#-authentication)
- [Caching](#-caching)
- [CI/CD Pipeline](#-cicd-pipeline)
- [Bonus Features](#-bonus-features)

## ✨ Features

- **Stateless Architecture**: Designed to be scalable and robust.
- **Dual Role Authentication**: JWT-based authentication for both Players and Backoffice Agents.
- **High-Performance Caching**: Utilizes Redis to cache frequently accessed Player data.
- **Containerized Environment**: Comes with a complete Docker setup for a consistent development experience.
- **CI/CD Ready**: Includes a basic CI/CD pipeline for automated builds and deployments.

## 🛠️ Technologies Used

- **Backend**: PHP 8.0, Laravel 8.x
- **Database**: PostgreSQL
- **Caching**: Redis
- **Authentication**: JWT (JSON Web Tokens)
- **Containerization**: Docker & Docker Compose
- **CI/CD**: GitHub Actions / GitLab CI
- **Web Server**: Nginx

## 📋 System Requirements

- Docker
- Docker Compose
- A code editor of your choice (e.g., VSCode)
- A terminal or command prompt

## 🚀 Installation and Setup

Follow these steps to get your development environment up and running.

**1. Clone the Repository**

      `git clone https://github.com/sirinberhus/wallet-app.git
cd wallet-app`

**2. Set Up Environment Variables**

Copy the example environment file and generate your application key.

      `cp .env.example .env`

Now, open the .env file and configure your database credentials and other settings.

**3. Build and Run with Docker**

Use Docker Compose to build the images and start the services.


      `docker-compose up -d --build`


**4. Install Dependencies and Run Migrations**

Access the PHP container and run the necessary commands.

<pre><code>```bash docker-compose exec app bash composer install php artisan migrate php artisan key:generate php artisan jwt:secret exit ```</code></pre>


**5. Create a Backoffice Admin User**

A custom Artisan command is available to create a Backoffice Agent.

      `docker-compose exec app php artisan backoffice:create-admin`


Your application should now be accessible at http://localhost:8080 (or the port you configured).

## API Endpoints

### 🎮 Player API

| Feature | Method | Endpoint | Auth Required |
| --- | --- | --- | --- |
| Register | POST | /api/register | ❌ |
| Login | POST | /api/login | ❌ |
| View Profile (cached) | GET | /api/me | ✅ |
| View Balance (cached) | GET | /api/balance | ✅ |
| View Promotions (cached) | GET | /api/promotions | ✅ |
| Claim Promotion by Code | POST | /api/promotions/claim | ✅ |

### 🛠️ Backoffice API

| Feature | Method | Endpoint | Auth Required |
| --- | --- | --- | --- |
| Login | POST | /api/bo/login | ❌ |
| View Profile | GET | /api/bo/me | ✅ |
| List Users (paginated) | GET | /api/bo/users | ✅ |
| List Promotions | GET | /api/bo/promotions | ✅ |
| Create Promotion | POST | /api/bo/promotions | ✅ |
| Delete Promotion | DELETE | /api/bo/promotions/{id} | ✅ |
| Change Promotion Status | PATCH | /api/bo/promotions/{id}/status | ✅ |

## 🔐 Authentication

Authentication is handled using JWT. After a successful login, a token is returned. This token must be included in the Authorization header for all protected endpoints.

**Header Format:**

Authorization: Bearer <your_jwt_token>

## ⚡ Caching

To ensure fast response times, high-frequency Player endpoints are cached using Redis. The cached endpoints are:

- /api/me
- /api/balance
- /api/promotions

## 🔄 CI/CD Pipeline

This project is set up with a basic CI/CD pipeline using [GitHub Actions/GitLab CI]. The pipeline automates the following steps:

1. **Code Linting**: Checks the code for style and syntax errors.
2. **Running Tests**: Executes the test suite to ensure code quality.
3. **Building Docker Image**: Creates a production-ready Docker image.
4. **Deployment**: Pushes the image to a container registry and deploys to the staging/production environment.

You can find the pipeline configuration in the .github/workflows/ .
