# Language Test Booking System

A full-stack web application for booking language test sessions. Users can authenticate, browse available sessions, make reservations, and manage their bookings.

## What I Used

**Backend**
- Symfony 6.4
- PHP 8.2
- MongoDB with Doctrine ODM
- JWT for authentication (LexikJWTAuthenticationBundle)

**Frontend**
- Next.js 14
- React 18 with TypeScript
- TailwindCSS for styling
- Axios for API calls
- Lucide React for icons
- React Hot Toast for notifications

**DevOps**
- Docker & Docker Compose
- PHPUnit for backend tests
- Jest for frontend tests

## What It Does

- Sign up / Log in with JWT tokens
- View all available test sessions with pagination
- Book a session if seats are available
- Cancel your own bookings
- Edit your profile (name, email)
- Admin users can create new sessions

## Getting Started

### Prerequisites

**With Docker (recommended):**
- Docker installed
- Docker Compose installed

**Without Docker:**
- PHP 8.2 or higher
- Composer
- Node.js 18 or higher
- MongoDB 8.0 or higher running locally

### Installation with Docker

```bash
# Clone the repository
git clone https://github.com/yourusername/language-test-booking.git
cd language-test-booking

# Start all containers
docker-compose up -d

# Install backend dependencies
docker exec -it language_test_backend composer install

# Install frontend dependencies
docker exec -it language_test_frontend npm install

# Generate JWT keys
docker exec -it language_test_backend mkdir -p config/jwt
docker exec -it language_test_backend openssl genrsa -out config/jwt/private.pem -aes256 4096
docker exec -it language_test_backend openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem

# Create MongoDB indexes
docker exec -it language_test_backend php bin/console doctrine:mongodb:schema:create

# Open your browser
# Frontend: http://localhost:3000
# Backend API: http://localhost:8000

Manual Installation
1. Configure MongoDB

Verify MongoDB installation:

mongod --version

Create a data directory:

mkdir -p ~/data/db

Start MongoDB:

mongod --dbpath ~/data/db
2. Configure the Backend

Navigate to the backend directory:

cd backend

Install dependencies:

composer install

Create a local environment file:

cp .env .env.local

Configure .env.local:

APP_ENV=dev
APP_SECRET=your_secret_key

MONGODB_URL=mongodb://localhost:27017
MONGODB_DB=language_test_db

JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=your_passphrase

Generate JWT keys:

mkdir -p config/jwt

openssl genrsa -out config/jwt/private.pem -aes256 4096

openssl rsa -pubout \
-in config/jwt/private.pem \
-out config/jwt/public.pem

Create MongoDB indexes:

php bin/console doctrine:mongodb:schema:create

Start the Symfony server:

php -S localhost:8000 -t public

The API will be available at:

http://localhost:8000
3. Configure the Frontend

Open a new terminal and navigate to the frontend directory:

cd frontend

Install dependencies:

npm install

Create the local environment file:

cp .env.example .env.local

Configure .env.local:

NEXT_PUBLIC_API_URL=http://localhost:8000

Start the development server:

npm run dev

The frontend will be available at:

http://localhost:3000
Sample Data

To create sample language test sessions, open MongoDB Shell:

mongosh

Run:

use language_test_db

db.sessions.insertMany([
  {
    language: "English",
    date: new Date("2025-06-10"),
    time: "10:00",
    location: "Paris Center",
    availableSeats: 20
  },
  {
    language: "Spanish",
    date: new Date("2025-06-11"),
    time: "14:00",
    location: "Lyon",
    availableSeats: 15
  },
  {
    language: "French",
    date: new Date("2025-06-12"),
    time: "09:00",
    location: "Marseille",
    availableSeats: 25
  },
  {
    language: "German",
    date: new Date("2025-06-13"),
    time: "11:00",
    location: "Paris Center",
    availableSeats: 10
  },
  {
    language: "Italian",
    date: new Date("2025-06-14"),
    time: "15:00",
    location: "Bordeaux",
    availableSeats: 30
  }
])
Test Accounts

Create a user via the frontend registration form or through the API:

curl -X POST http://localhost:8000/api/register \
-H "Content-Type: application/json" \
-d '{
  "name":"Admin User",
  "email":"test@gmail.com",
  "password":"123456"
}'

Grant administrator privileges:

use language_test_db

db.users.updateOne(
  { email: "test@gmail.com" },
  {
    $set: {
      roles: ["ROLE_USER", "ROLE_ADMIN"]
    }
  }
)