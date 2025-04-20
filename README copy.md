# Medicare - Healthcare Management System

A comprehensive healthcare management system built with React.js frontend and PHP backend.

## Prerequisites

Before running this project, make sure you have the following installed:

- [XAMPP](https://www.apachefriends.org/) (for PHP and MySQL)
- [Node.js](https://nodejs.org/) (version 14 or higher)
- [npm](https://www.npmjs.com/) (comes with Node.js)

## Project Structure

```
Medicare/
├── frontend/          # React frontend
│   └── website/      # Main frontend application
└── backend/          # PHP backend
    └── api/         # API endpoints
```

## Setup Instructions

### 1. XAMPP Installation

#### For Windows:

1. Download XAMPP:

   - Go to [https://www.apachefriends.org/](https://www.apachefriends.org/)
   - Click "Download" for the latest version
   - Choose Windows version

2. Install XAMPP:

   - Run the downloaded installer
   - Click "Next" through the installation wizard
   - Choose your installation directory (default is `C:\xampp`)
   - Select components (make sure Apache and MySQL are selected)
   - Complete the installation

3. Start XAMPP Control Panel:
   - Find XAMPP Control Panel in your Start menu or desktop
   - Run it as administrator
   - You should see Apache and MySQL services listed
   - Click "Start" next to both Apache and MySQL
   - Both services should show green background when running

#### For Linux:

1. Download XAMPP:

   - Go to [https://www.apachefriends.org/](https://www.apachefriends.org/)
   - Click "Download" for the latest version
   - Choose Linux version

2. Install XAMPP:

   ```bash
   # Make the installer executable
   chmod +x xampp-installer.run

   # Run the installer
   sudo ./xampp-installer.run
   ```

   - Follow the installation wizard
   - Choose your installation directory (default is `/opt/lampp`)
   - Select components (make sure Apache and MySQL are selected)
   - Complete the installation

3. Start XAMPP:

   ```bash
   # Start XAMPP
   sudo /opt/lampp/lampp start

   # Check status
   sudo /opt/lampp/lampp status
   ```

#### For macOS:

1. Download XAMPP:

   - Go to [https://www.apachefriends.org/](https://www.apachefriends.org/)
   - Click "Download" for the latest version
   - Choose macOS version

2. Install XAMPP:
   - Open the downloaded .dmg file
   - Drag the XAMPP folder to Applications
   - Open XAMPP from Applications folder
   - Click "Start" next to Apache and MySQL

### 2. Database Setup

1. Open phpMyAdmin:

   - Open your web browser
   - Go to `http://localhost/phpmyadmin`
   - You should see the phpMyAdmin interface

2. Create Database:

   - Click "New" on the left sidebar
   - Enter "medicare" as the database name
   - Click "Create"

3. Import Database:
   - Select the "medicare" database from the left sidebar
   - Click "Import" tab at the top
   - Click "Choose File" and select `my_table.sql` from the project
   - Scroll down and click "Go" to import

### 3. Backend Setup

1. Copy the `backend` folder to your XAMPP's `htdocs` directory:

#### For Windows:

- Navigate to `C:\xampp\htdocs`
- Copy the `backend` folder there

#### For Linux:

```bash
sudo cp -r backend /opt/lampp/htdocs/
```

#### For macOS:

- Navigate to `/Applications/XAMPP/htdocs`
- Copy the `backend` folder there

### 4. Frontend Setup

1. Navigate to the frontend directory:

   ```bash
   cd frontend/website
   ```

2. Install dependencies:

   ```bash
   npm install
   ```

3. Start the development server:
   ```bash
   npm start
   ```

The application will open automatically in your default browser at `http://localhost:3000`

## Troubleshooting

1. If you see a CORS error:

   - Make sure XAMPP's Apache service is running
   - Check if the backend API is accessible at `http://localhost/medicare/backend/api`
