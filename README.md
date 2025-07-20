 Healthcare CRM System – Laravel 11

A role-based Healthcare CRM built with Laravel 11, AdminLTE, Laravel Breeze, and Spatie Permissions. This app includes user authentication, role-based dashboards, patient management, appointment scheduling, and audit logging.

---

// Features

- Role-based dashboards for Admin, CRM Agent, Doctor, Patient, Lab Manager
- Patient CRUD management with audit trail
- Appointment scheduling for CRM role
- Auth with Laravel Breeze + AdminLTE UI
- Password reset via Mailtrap email
- API endpoints (protected with Laravel Sanctum)
- Azure deployment ready (with guide in README) -- Incomplete

---

 Local Setup Instructions

 Clone the Repository


git clone https://github.com/ashokejoym/healthcare-crm.git
cd healthcare-crm

 Install Dependencies

composer install
npm install && npm run dev

Set Up Environment

cp .env.example .env
php artisan key:generate



APP_NAME="HealthCare CRM"
APP_URL=http://127.0.0.1:8000
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_db
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_user
MAIL_PASSWORD=your_mailtrap_pass
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=admin@healthcare.com
MAIL_FROM_NAME="HealthCare CRM"



php artisan migrate --seed

This will seed default roles and an admin user:
Email: admin@test.com
Password: password


Folder Structure (Key Parts)

resources/views/         # Blade views
app/Http/Controllers/    # Controllers (e.g., PatientController)
routes/web.php           # Web routes
routes/api.php           # API endpoints

Role-Based Dashboards

| Role        | Route              |
| ----------- | ------------------ |
| Admin       | /admin/dashboard   |
| CRM Agent   | /crm/dashboard     |
| Doctor      | /doctor/dashboard  |
| Patient     | /patient/dashboard |
| Lab Manager | /lab/dashboard     |


API Endpoints
Use tools Postman with bearer token (Laravel Sanctum):

/api/login

/api/patients

/api/appointments

/api/patients/{id}/audits

 Test Login Users

 | Role        | Email             | Password |
| ----------- | ----------------   | -------- | 
| Admin       | admin@test.com     | password |
| CRM Agent   | crmagent@test.com  | password |
| Doctor      | doctor@test.com    | password |
| Patient     | patient@test.com   | password |
| Lab Manager | lab@test           | password |

Commands for Development

php artisan serve
npm run dev
php artisan migrate:fresh --seed

API Endpoints (Sanctum Auth)

Auth Endpoints

| Method | Endpoint      | Description |
| ------ | ------------- | ----------- |
| POST   | `/api/login`  | Login user  |
| POST   | `/api/logout` | Logout user |



#### Example

**Login:**


POST /api/login
Content-Type: application/json

{
  "email": "admin@test.com",
  "password": "password"
}

**Logout:**

POST /api/logout


Patients

| Method | Endpoint             | Role      | Description        |
| ------ | -------------------- | --------- | ------------------ |
| GET    | `/api/patients`      | Admin/CRM | List all patients  |
| POST   | `/api/patients`      | Admin/CRM | Create new patient |
| GET    | `/api/patients/{id}` | Admin/CRM | View patient       |
| PUT    | `/api/patients/{id}` | Admin/CRM | Update patient     |
| DELETE | `/api/patients/{id}` | Admin     | Delete patient     |


//List Patients//

GET /api/patients
Authorization: Bearer your_token


//create Patients

POST /api/appointments
Authorization: Bearer your_token
Content-Type: application/json


{
  "first_name": "Johnbfd",
  "last_name": "Doe",
  "date_of_birth": "1990-01-01",
  "gender": "Male",
  "phone_number": "98765432888",
  "email": "john.doe@example.cmjgsgn",
  "address": "123 Main Street",
  "emergency_contact_name": "Jane Doe",
  "emergency_contact_phone": "9876543211",
  "insurance_details": {
    "policy_number": "POL123456",
    "provider_name": "XYZ Health"
  }
}




## Appointments

| Method | Endpoint                 | Role | Description       |
| ------ | ------------------------ | ---- | ----------------- |
| GET    | `/api/appointments`      | CRM  | List appointments |
| POST   | `/api/appointments`      | CRM  | Create            |
| GET    | `/api/appointments/{id}` | CRM  | View              |


**Create Appointment:**

http
POST /api/appointments
Authorization: Bearer your_token
Content-Type: application/json

{
  "patient_id": 1,
  "doctor_id": 1,
  "appointment_date": "2025-07-25",
  "appointment_time": "10:30:00",
  "notes": "Routine check-up"
}



Audit Trail

| Method | Endpoint                    | Role  | Description        |
| ------ | --------------------------- | ----- | ------------------ |
| GET    | `/api/patients/{id}/audits` | Admin | View audit history |


Example

http
GET /api/patients/1/audits
Authorization: Bearer your_token


 .env Configuration for Azure

APP_URL=https://app-name.azurewebsites.net
APP_ENV=production
APP_DEBUG=false
APP_URL=https://appname.azurewebsites.net

DB_CONNECTION=mysql
DB_HOST=mysql-server.mysql.database.azure.com
DB_PORT=3306
DB_DATABASE=db_name
DB_USERNAME=username@your-mysql-server
DB_PASSWORD=password
